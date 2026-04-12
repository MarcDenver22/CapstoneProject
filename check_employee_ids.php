<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

$users = User::all();
foreach($users as $u) {
    echo $u->id . ' | ' . ($u->employee_id ?? 'NULL') . ' | ' . $u->name . "\n";
}
