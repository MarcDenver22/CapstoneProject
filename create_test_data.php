<?php
require 'bootstrap/app.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Event;
use App\Models\Announcement;

// Create test events
Event::create([
    'title' => 'Team Meeting',
    'description' => 'Monthly team sync-up',
    'start_date' => now()->addDays(1),
    'end_date' => now()->addDays(1)->addHours(1),
    'location' => 'Conference Room A',
    'status' => 'upcoming',
    'created_by' => 1
]);

Event::create([
    'title' => 'Company Training',
    'description' => 'New system training for all employees',
    'start_date' => now()->addDays(3),
    'end_date' => now()->addDays(3)->addHours(3),
    'location' => 'Main Hall',
    'status' => 'upcoming',
    'created_by' => 1
]);

// Create test announcements
Announcement::create([
    'title' => 'System Maintenance',
    'content' => 'The system will be down for maintenance on Friday evening',
    'priority' => 'high',
    'status' => 'active',
    'published_at' => now(),
    'expires_at' => now()->addDays(7),
    'created_by' => 1
]);

Announcement::create([
    'title' => 'Company Policy Update',
    'content' => 'New remote work policy is now in effect',
    'priority' => 'medium',
    'status' => 'active',
    'published_at' => now(),
    'expires_at' => now()->addDays(30),
    'created_by' => 1
]);

echo "Test data created successfully!\n";
echo "Events: " . Event::count() . "\n";
echo "Announcements: " . Announcement::count() . "\n";
?>
