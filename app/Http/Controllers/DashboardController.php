<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Stat Cards
        $totalEmployees = 156;
        $presentToday = 132;
        $absentToday = 18;
        $lateArrivals = 6;

        // Announcements & Events
        $announcements = [
            [
                'type' => 'event',
                'color' => 'blue',
                'icon' => 'fa-calendar-alt',
                'title' => 'Faculty Meeting',
                'description' => 'General assembly for all faculty members.',
                'date' => 'March 15, 2026',
            ],
            [
                'type' => 'holiday',
                'color' => 'green',
                'icon' => 'fa-umbrella-beach',
                'title' => 'Holiday - Araw ng Kagitingan',
                'description' => 'No classes and work.',
                'date' => 'April 9, 2026',
            ],
            [
                'type' => 'event',
                'color' => 'purple',
                'icon' => 'fa-chalkboard-teacher',
                'title' => 'Teacher Training Seminar',
                'description' => 'Professional development workshop.',
                'date' => 'March 22, 2026',
            ],
        ];

        // Your 6 Campus Departments
        $departments = [
            ['name' => 'Bachelor of Elementary Education', 'total' => 32, 'present' => 28, 'absent' => 3, 'late' => 1],
            ['name' => 'Bachelor of Industrial Technology', 'total' => 25, 'present' => 22, 'absent' => 2, 'late' => 1],
            ['name' => 'Bachelor of Secondary Education', 'total' => 28, 'present' => 24, 'absent' => 3, 'late' => 1],
            ['name' => 'Bachelor of Technology and Livelihood Education', 'total' => 35, 'present' => 30, 'absent' => 4, 'late' => 1],
            ['name' => 'Bachelor of Business Administration', 'total' => 20, 'present' => 18, 'absent' => 1, 'late' => 1],
            ['name' => 'Bachelor Information Technology', 'total' => 16, 'present' => 10, 'absent' => 5, 'late' => 1],
        ];

        // Recent System Activities
        $activities = [
            ['action' => 'New employee added', 'detail' => 'Juan Dela Cruz - Bachelor Information Technology', 'time' => '2 minutes ago', 'icon' => 'fa-user-plus', 'color' => 'green'],
            ['action' => 'Attendance updated', 'detail' => 'Maria Santos marked as Late', 'time' => '15 minutes ago', 'icon' => 'fa-clock', 'color' => 'yellow'],
            ['action' => 'Leave request approved', 'detail' => 'Pedro Reyes - Sick Leave (3 days)', 'time' => '1 hour ago', 'icon' => 'fa-check-circle', 'color' => 'blue'],
            ['action' => 'Report generated', 'detail' => 'Monthly Attendance Report - February 2026', 'time' => '3 hours ago', 'icon' => 'fa-file-alt', 'color' => 'purple'],
            ['action' => 'Employee record updated', 'detail' => 'Ana Reyes - Contact info changed', 'time' => '5 hours ago', 'icon' => 'fa-edit', 'color' => 'orange'],
        ];

        return view('dashboard', compact(
            'totalEmployees',
            'presentToday',
            'absentToday',
            'lateArrivals',
            'announcements',
            'departments',
            'activities'
        ));
    }
}