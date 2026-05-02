<?php

namespace App\Jobs;

use App\Models\Attendance;
use App\Models\AttendanceLog;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * RecordAttendanceJob
 *
 * Queued job that writes a single attendance punch (IN or OUT) to
 * Supabase.  Dispatched by the kiosk scan endpoint when Supabase is
 * unreachable so that no attendance record is lost during an outage.
 *
 * The job payload carries the timestamp from the moment the employee
 * scanned so that the DTR reflects the real check-in / check-out time.
 * Period (AM/PM) and punch type (IN/OUT) are computed at execution time
 * from whatever state is already in Supabase, which means jobs that are
 * queued consecutively offline will still produce the correct sequence
 * once they process in FIFO order.
 *
 * Retry strategy – exponential back-off (Laravel backoff() method):
 *   Attempt 1 → immediate
 *   Attempt 2 → wait  10 s
 *   Attempt 3 → wait  30 s
 *   Attempt 4 → wait  60 s
 *   Attempt 5 → wait 120 s
 * After 5 failures the job is moved to the failed_jobs table and
 * failed() is called so the incident is logged for review.
 */
class RecordAttendanceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** Maximum number of attempts before giving up. */
    public int $tries = 5;

    /** Per-job execution time limit in seconds. */
    public int $timeout = 30;

    /**
     * @param int         $userId         users.id of the employee
     * @param string      $punchedAt      Full datetime string (Y-m-d H:i:s) of the punch
     * @param float|null  $confidence     Face-match confidence (0–1), null when not measurable offline
     * @param string|null $photoPath      Optional stored photo path
     */
    public function __construct(
        public readonly int $userId,
        public readonly string $punchedAt,
        public readonly ?float $confidence,
        public readonly ?string $photoPath = null,
    ) {}

    /**
     * Delays (in seconds) between each retry attempt.
     * With $tries = 5 there are 4 retries, so 4 delays:
     *   retry 1 →  10 s, retry 2 →  30 s, retry 3 → 60 s, retry 4 → 120 s
     */
    public function backoff(): array
    {
        return [10, 30, 60, 120];
    }

    /**
     * Execute the job: compute punch context, guard against duplicates,
     * then write the attendance log and DTR row to Supabase.
     */
    public function handle(): void
    {
        $attempt  = $this->attempts();
        $now      = Carbon::parse($this->punchedAt);
        $dateStr  = $now->toDateString();
        $hour     = $now->hour;

        Log::info('RecordAttendanceJob: Syncing offline attendance', [
            'user_id'    => $this->userId,
            'date'       => $dateStr,
            'punched_at' => $this->punchedAt,
            'attempt'    => $attempt,
        ]);

        // ── Fetch / create today's DTR row ───────────────────────────────
        $dtr = Attendance::firstOrCreate(
            [
                'user_id'         => $this->userId,
                'attendance_date' => $dateStr,
            ],
            [
                'status'           => 'present',
                'liveness_verified' => true,
                'synced'           => true,
            ]
        );

        // ── Compute period and punch type from current DTR state ─────────
        [$period, $punchType] = $this->computePeriodAndType($dtr, $hour);

        // ── Duplicate guard ──────────────────────────────────────────────
        // Prevent the same punch being recorded twice when a job retries
        // after a partial success or a network hiccup.
        $duplicate = AttendanceLog::where('employee_id', $this->userId)
            ->where('log_date', $dateStr)
            ->where('period', $period)
            ->where('punch_type', $punchType)
            ->whereBetween('punched_at', [
                $now->copy()->subMinutes(5),
                $now->copy()->addMinutes(5),
            ])
            ->exists();

        if ($duplicate) {
            Log::info('RecordAttendanceJob: Duplicate punch detected — skipping', [
                'user_id' => $this->userId,
                'date'    => $dateStr,
                'period'  => $period,
            ]);
            return;
        }

        // ── Write the attendance log entry ───────────────────────────────
        AttendanceLog::create([
            'employee_id'    => $this->userId,
            'log_date'       => $dateStr,
            'period'         => $period,
            'punch_type'     => $punchType,
            'punched_at'     => $now,
            'method'         => 'face_recognition',
            'confidence'     => $this->confidence !== null ? round($this->confidence, 4) : null,
            'liveness_passed' => true,
            'photo_path'     => $this->photoPath,
            'notes'          => 'Offline sync — kiosk face scan ' . strtoupper($punchType),
        ]);

        // ── Update period-specific time columns on the DTR ───────────────
        if ($period === 'AM') {
            if ($punchType === 'IN' && !$dtr->am_arrival) {
                $dtr->am_arrival = $now;
            } elseif ($punchType === 'OUT') {
                $dtr->am_departure = $now;
            }
        } else {
            if ($punchType === 'IN' && !$dtr->pm_arrival) {
                $dtr->pm_arrival = $now;
            } elseif ($punchType === 'OUT') {
                $dtr->pm_departure = $now;
            }
        }

        // Keep legacy time_in / time_out columns in sync
        if (!$dtr->time_in && $dtr->am_arrival) {
            $dtr->time_in = $dtr->am_arrival;
        }
        if ($dtr->pm_departure) {
            $dtr->time_out = $dtr->pm_departure;
        } elseif ($dtr->am_departure) {
            $dtr->time_out = $dtr->am_departure;
        }

        $dtr->synced = true;
        $dtr->save();

        Log::info('RecordAttendanceJob: Attendance synced successfully', [
            'user_id'       => $this->userId,
            'attendance_id' => $dtr->id,
            'date'          => $dateStr,
            'period'        => $period,
            'punch_type'    => $punchType,
        ]);
    }

    /**
     * Compute which period (AM/PM) and punch type (IN/OUT) apply for
     * this scan, mirroring the state-based logic in KioskScanController.
     *
     * @return array{0: string, 1: string}  [period, punchType]
     */
    private function computePeriodAndType(Attendance $dtr, int $hour): array
    {
        // If it's past noon and there are no AM records the employee
        // skipped the morning shift — start them in PM.
        if ($hour >= 12 && !$dtr->am_arrival && !$dtr->am_departure) {
            $period = 'PM';
        } elseif (!$dtr->am_arrival) {
            $period = 'AM';   // first punch of the day
        } elseif (!$dtr->am_departure) {
            $period = 'AM';   // second punch (leaving for lunch)
        } elseif (!$dtr->pm_arrival) {
            $period = 'PM';   // third punch (returning from lunch)
        } else {
            $period = 'PM';   // fourth punch (end of day)
        }

        // Punch type follows the same state machine
        if (!$dtr->am_arrival) {
            $punchType = 'IN';
        } elseif (!$dtr->am_departure) {
            $punchType = 'OUT';
        } elseif (!$dtr->pm_arrival) {
            $punchType = 'IN';
        } else {
            $punchType = 'OUT';
        }

        return [$period, $punchType];
    }

    /**
     * Called when all retry attempts have been exhausted.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('RecordAttendanceJob: All retries exhausted — attendance may be lost', [
            'user_id'    => $this->userId,
            'punched_at' => $this->punchedAt,
            'error'      => $exception->getMessage(),
        ]);
    }
}