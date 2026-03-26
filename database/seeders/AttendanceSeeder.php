<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all employees
        $employees = User::where('role', 'employee')->get();

        if ($employees->isEmpty()) {
            $this->command->info('No employees found. Create employees first.');
            return;
        }

        // Create sample attendance records for the current month
        $now = Carbon::now();
        $startDate = $now->copy()->startOfMonth();
        $endDate = $now->copy()->endOfMonth();

        $statuses = ['present', 'late', 'half_day', 'leave'];
        $recordCount = 0;

        foreach ($employees as $employee) {
            for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
                // Skip weekends
                if ($date->isWeekend()) {
                    continue;
                }

                // Skip if record already exists
                if (Attendance::where('user_id', $employee->id)
                    ->whereDate('attendance_date', $date->toDateString())
                    ->exists()) {
                    continue;
                }

                // 90% chance of present/late, 5% half day, 5% leave
                $rand = rand(1, 100);
                if ($rand <= 50) {
                    $status = 'present';
                    $timeIn = $date->copy()->setTime(8, rand(0, 15)); // 8:00-8:15
                    $timeOut = $date->copy()->setTime(17, rand(0, 30)); // 5:00-5:30
                } elseif ($rand <= 90) {
                    $status = 'late';
                    $timeIn = $date->copy()->setTime(8, rand(16, 45)); // 8:16-8:45
                    $timeOut = $date->copy()->setTime(17, rand(0, 30)); // 5:00-5:30
                } elseif ($rand <= 95) {
                    $status = 'half_day';
                    $timeIn = $date->copy()->setTime(8, rand(0, 15));
                    $timeOut = $date->copy()->setTime(12, 30); // Half day at 12:30
                } else {
                    $status = 'leave';
                    $timeIn = null;
                    $timeOut = null;
                }

                Attendance::create([
                    'user_id' => $employee->id,
                    'attendance_date' => $date->toDateString(),
                    'time_in' => $timeIn ? $timeIn->format('H:i:s') : null,
                    'time_out' => $timeOut ? $timeOut->format('H:i:s') : null,
                    'status' => $status,
                    'notes' => $status === 'leave' ? 'Annual Leave' : null,
                    'liveness_verified' => true,
                ]);

                $recordCount++;
            }
        }

        $this->command->info("Created {$recordCount} attendance records for {$employees->count()} employees in " . $now->format('F Y'));
    }
}
