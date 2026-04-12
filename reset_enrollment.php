<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Clear EMP003's corrupted enrollment data
$user = \App\Models\User::where('employee_id', 'EMP003')->first();
if ($user) {
    $user->update([
        'face_enrolled' => false,
        'face_encodings' => null,
        'face_samples_count' => 0,
        'face_enrolled_at' => null
    ]);
    echo "✓ EMP003 (Employee) enrollment cleared - ready for fresh enrollment\n";
} else {
    echo "✗ EMP003 not found\n";
}

// Show current enrollment status
echo "\nCurrent enrollment status:\n";
echo str_repeat("-", 60) . "\n";
$users = \App\Models\User::all();
foreach($users as $u) {
    $status = $u->face_enrolled ? "✓ ENROLLED ({$u->face_samples_count} samples)" : "✗ NOT enrolled";
    echo sprintf("%-12s | %-25s | %s\n", $u->employee_id, $u->name, $status);
}
