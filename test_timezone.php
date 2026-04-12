<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

echo "=== TIMEZONE TEST ===\n\n";

// Check Laravel config
$timezone = config('app.timezone');
echo "Laravel Configured Timezone: " . $timezone . "\n";

// Check current time
$now = Carbon::now();
echo "\nCurrent Time (Laravel):\n";
echo "  Local: " . $now->format('Y-m-d H:i:s') . "\n";
echo "  UTC: " . $now->setTimezone('UTC')->format('Y-m-d H:i:s') . "\n";
echo "  Timezone: " . $now->timezoneName . "\n";

// Check database time
echo "\nDatabase Current Time:\n";
$dbTime = DB::selectOne("SELECT NOW() as current_time, UTC_TIMESTAMP() as utc_time");
echo "  MySQL NOW(): " . $dbTime->current_time . "\n";
echo "  MySQL UTC: " . $dbTime->utc_time . "\n";

// Test with a recent attendance record
echo "\n=== RECENT ATTENDANCE RECORDS ===\n";
$records = DB::select("
    SELECT 
        user_id,
        attendance_date,
        time_in,
        time_out,
        DATE_ADD(time_in, INTERVAL 8 HOUR) as time_in_adjusted,
        DATE_ADD(time_out, INTERVAL 8 HOUR) as time_out_adjusted
    FROM attendance
    WHERE attendance_date >= DATE_SUB(CURDATE(), INTERVAL 1 DAY)
    ORDER BY id DESC
    LIMIT 5
");

if (empty($records)) {
    echo "No recent records found.\n";
} else {
    foreach ($records as $rec) {
        echo "\nUser ID: {$rec->user_id}, Date: {$rec->attendance_date}\n";
        echo "  Time IN (DB): {$rec->time_in}\n";
        if ($rec->time_in_adjusted) {
            echo "  Time IN (Adjusted +8h): {$rec->time_in_adjusted}\n";
        }
        echo "  Time OUT (DB): {$rec->time_out}\n";
        if ($rec->time_out_adjusted) {
            echo "  Time OUT (Adjusted +8h): {$rec->time_out_adjusted}\n";
        }
    }
}

echo "\n=== TEST COMPLETE ===\n";
