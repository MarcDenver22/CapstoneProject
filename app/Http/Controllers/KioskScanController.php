<?php

namespace App\Http\Controllers;

use App\Models\AttendanceLog;
use App\Models\AuditLog;
use App\Models\User;
use App\Services\FaceRecognitionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class KioskScanController extends Controller
{
    protected FaceRecognitionService $faceRecognition;

    public function __construct(FaceRecognitionService $faceRecognition)
    {
        $this->faceRecognition = $faceRecognition;
    }

    /**
     * Show the kiosk scan page
     */
    public function index()
    {
        return view('kiosk.scan');
    }

    /**
     * Handle face scan and attendance recording
     */
    public function scan(Request $request): JsonResponse
    {
        try {
            // Validate request
            $validated = $request->validate([
                'image' => 'required|string',
            ]);

            // Decode and save temporary image
            $imageBase64 = $validated['image'];
            if (strpos($imageBase64, 'data:image') === 0) {
                $imageBase64 = substr($imageBase64, strpos($imageBase64, ',') + 1);
            }

            $imageData = base64_decode($imageBase64, true);
            if (!$imageData) {
                return response()->json([
                    'status' => 'fail',
                    'message' => 'Invalid image data',
                ], 400);
            }

            // Save temporary image for processing
            $tempPath = storage_path('temp/scans/' . uniqid() . '.jpg');
            @mkdir(dirname($tempPath), 0755, true);
            file_put_contents($tempPath, $imageData);

            // Call face recognition service
            $recognition = $this->faceRecognition->recognize($tempPath);

            // Clean up temp file
            @unlink($tempPath);

            // If recognition failed or not recognized
            if (!$recognition['recognized']) {
                $this->logAuditTrail(null, 'scan_failed', 'Face not recognized', $request->ip());

                return response()->json([
                    'status' => 'fail',
                    'message' => 'Face not recognized. Please try again.',
                    'action_recorded' => 'none',
                    'timestamp' => now()->toIso8601String(),
                ]);
            }

            // Get employee
            $employee = User::find($recognition['employee_id']);
            if (!$employee) {
                $this->logAuditTrail($recognition['employee_id'], 'scan_failed', 'Employee not found', $request->ip());

                return response()->json([
                    'status' => 'fail',
                    'message' => 'Employee record not found.',
                    'action_recorded' => 'none',
                    'timestamp' => now()->toIso8601String(),
                ]);
            }

            // Check liveness
            if (!$recognition['liveness_passed']) {
                $this->logAuditTrail($employee->id, 'scan_failed', 'Liveness check failed', $request->ip());

                return response()->json([
                    'status' => 'fail',
                    'message' => 'Liveness verification failed. Please look at the camera.',
                    'action_recorded' => 'none',
                    'timestamp' => now()->toIso8601String(),
                ]);
            }

            // Determine punch type based on current time
            $now = Carbon::now();
            $hour = $now->hour;

            // AM: before 12pm, PM: 12pm and after
            $period = $hour < 12 ? 'AM' : 'PM';
            
            // Check if employee already punched in/out for this period
            $lastLog = AttendanceLog::where('employee_id', $employee->id)
                ->where('log_date', $now->toDateString())
                ->where('period', $period)
                ->orderBy('punched_at', 'desc')
                ->first();

            // Determine punch type
            if ($lastLog) {
                // If last was IN, mark as OUT. If OUT or none, mark as IN
                $punchType = $lastLog->punch_type === 'IN' ? 'OUT' : 'IN';
            } else {
                $punchType = 'IN';
            }

            // Save attendance log
            $log = AttendanceLog::create([
                'employee_id' => $employee->id,
                'log_date' => $now->toDateString(),
                'period' => $period,
                'punch_type' => $punchType,
                'punched_at' => $now,
                'method' => 'face_recognition',
                'confidence' => $recognition['confidence'] ?? 0.95,
                'liveness_passed' => true,
                'photo_path' => null, // TODO: store actual photo if needed
                'notes' => 'Kiosk scan - ' . strtoupper($punchType),
            ]);

            // Log audit trail
            $this->logAuditTrail(
                $employee->id,
                'scan_success',
                "Attendance recorded: {$period} {$punchType}",
                $request->ip()
            );

            // Format employee name for privacy (first name only or name initials)
            $employeeName = $employee->name;
            if (strpos($employeeName, ' ') !== false) {
                $parts = explode(' ', $employeeName, 2);
                $employeeName = $parts[0] . ' ' . substr($parts[1], 0, 1) . '.';
            }

            return response()->json([
                'status' => 'success',
                'message' => "Attendance recorded for {$employeeName}",
                'action_recorded' => strtolower($period) . '_' . strtolower($punchType),
                'timestamp' => $now->toIso8601String(),
                'employee_id' => $employee->id,
                'period' => $period,
                'punch_type' => $punchType,
            ]);

        } catch (\Exception $e) {
            Log::error('Kiosk scan error: ' . $e->getMessage(), [
                'exception' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 'fail',
                'message' => 'An error occurred during scanning. Please try again.',
                'action_recorded' => 'none',
                'timestamp' => now()->toIso8601String(),
            ], 500);
        }
    }

    /**
     * Log an audit trail entry
     */
    private function logAuditTrail(?int $employeeId, string $action, string $details, string $ipAddress): void
    {
        try {
            AuditLog::create([
                'user_id' => $employeeId,
                'action' => $action,
                'ip_address' => $ipAddress,
                'changes' => ['details' => $details],
            ]);
        } catch (\Exception $e) {
            Log::warning('Failed to log audit trail: ' . $e->getMessage());
        }
    }
}
