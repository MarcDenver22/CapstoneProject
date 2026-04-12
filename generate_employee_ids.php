<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

echo "=== Generating Employee IDs ===\n";

$users = User::orderBy('id')->get();
$counter = 1;

foreach($users as $user) {
    $employeeId = 'EMP' . str_pad($counter, 3, '0', STR_PAD_LEFT);
    $user->update(['employee_id' => $employeeId]);
    echo "User {$user->id} ({$user->name}) -> {$employeeId}\n";
    $counter++;
}

echo "\n✅ Employee IDs generated!\n\n";

echo "=== Updated Employees ===\n";
echo "ID | Employee ID | Name | Email | Face Enrolled\n";
echo str_repeat("-", 80) . "\n";

$users = User::all();
foreach($users as $u) {
    $enrolled = $u->face_enrolled ? 'YES' : 'NO';
    echo "{$u->id} | {$u->employee_id} | {$u->name} | {$u->email} | {$enrolled}\n";
}
