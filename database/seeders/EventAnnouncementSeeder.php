<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Announcement;
use App\Models\User;
use Illuminate\Database\Seeder;

class EventAnnouncementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hrUser = User::where('role', 'hr')->first();
        
        if (!$hrUser) {
            echo "No HR user found. Please run UserSeeder first.\n";
            return;
        }

        $userId = $hrUser->id;

        // Create test events
        Event::create([
            'title' => 'Team Meeting',
            'description' => 'Monthly team sync-up meeting',
            'start_date' => now()->addDays(1),
            'end_date' => now()->addDays(1)->addHours(1),
            'location' => 'Conference Room A',
            'status' => 'upcoming',
            'created_by' => $userId
        ]);

        Event::create([
            'title' => 'Company Training',
            'description' => 'New system training for all employees',
            'start_date' => now()->addDays(3),
            'end_date' => now()->addDays(3)->addHours(3),
            'location' => 'Main Hall',
            'status' => 'upcoming',
            'created_by' => $userId
        ]);

        Event::create([
            'title' => 'Project Kickoff',
            'description' => 'Kickoff meeting for Q2 projects',
            'start_date' => now()->addDays(5),
            'end_date' => now()->addDays(5)->addHours(2),
            'location' => 'Virtual',
            'status' => 'upcoming',
            'created_by' => $userId
        ]);

        // Create test announcements
        Announcement::create([
            'title' => 'System Maintenance',
            'content' => 'The system will be down for maintenance on Friday evening from 6 PM to 10 PM.',
            'priority' => 'high',
            'status' => 'active',
            'published_at' => now(),
            'expires_at' => now()->addDays(7),
            'created_by' => $userId
        ]);

        Announcement::create([
            'title' => 'Company Policy Update',
            'content' => 'Please review the updated remote work policy that is now in effect.',
            'priority' => 'medium',
            'status' => 'active',
            'published_at' => now(),
            'expires_at' => now()->addDays(30),
            'created_by' => $userId
        ]);

        Announcement::create([
            'title' => 'Welcome to our Dashboard',
            'content' => 'We are excited to introduce the new attendance management system.',
            'priority' => 'low',
            'status' => 'active',
            'published_at' => now(),
            'expires_at' => now()->addDays(60),
            'created_by' => $userId
        ]);

        echo "Test events and announcements created successfully!\n";
        echo "Events created: 3\n";
        echo "Announcements created: 3\n";
    }
}
