<?php
require 'vendor/autoload.php';

use App\Models\Attendance;
use App\Models\AttendanceLog;

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== DTR RECORD CLEANUP ===\n\n";

// Show current records
$attendanceRecords = Attendance::all();
$logsCount = AttendanceLog::count();

echo "Current Records:\n";
echo "  Attendance (DTR): " . $attendanceRecords->count() . " records\n";
echo "  Attendance Logs: " . $logsCount . " records\n\n";

if ($attendanceRecords->count() > 0) {
    echo "Attendance Records:\n";
    foreach ($attendanceRecords as $rec) {
        echo "  - User ID {$rec->user_id}: {$rec->attendance_date} (IN: {$rec->time_in}, OUT: {$rec->time_out})\n";
    }
}

echo "\n";
$input = readline("Delete ALL attendance records? (yes/no): ");

if (strtolower($input) === 'yes') {
    // Delete all records
    Attendance::truncate();
    AttendanceLog::truncate();
    
    echo "\n✅ All records deleted successfully!\n";
    echo "   - Attendance table cleared\n";
    echo "   - Attendance Logs table cleared\n";
} else {
    echo "\n❌ Operation cancelled. Records remain unchanged.\n";
}

echo "\n=== CLEANUP COMPLETE ===\n";
