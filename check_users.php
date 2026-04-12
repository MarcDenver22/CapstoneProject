<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

echo "=== All Faculty/Employees ===\n";
echo "ID | Faculty ID | Name | Email | Face Enrolled | Samples\n";
echo str_repeat("-", 100) . "\n";

$users = User::all();
foreach($users as $u) {
    $enrolled = $u->face_enrolled ? 'YES' : 'NO';
    echo "{$u->id} | {$u->faculty_id} | {$u->name} | {$u->email} | {$enrolled} | {$u->face_samples_count}\n";
}


