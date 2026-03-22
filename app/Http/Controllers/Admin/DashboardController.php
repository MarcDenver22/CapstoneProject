<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AuditLog;
use App\Models\Event;
use App\Models\Announcement;
use App\Models\Attendance;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();
        
        // Stat Cards - Dynamic data from database
        $totalEmployees = User::whereIn('role', ['employee', 'hr'])->count();
        $presentToday = Attendance::where('attendance_date', $today)->where('status', 'present')->count();
        $absentToday = Attendance::where('attendance_date', $today)->where('status', 'absent')->count();
        $lateArrivals = Attendance::where('attendance_date', $today)->where('status', 'late')->count();

        // Events & Announcements
        $upcomingEvents = Event::orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $activeAnnouncements = Announcement::orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Recent Audit Logs
        $recentLogs = AuditLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Today's Attendance Records
        $todayAttendance = Attendance::with('user')
            ->where('attendance_date', $today)
            ->latest('time_in')
            ->get();

        return view('admin.dashboard', compact(
            'totalEmployees',
            'presentToday',
            'absentToday',
            'lateArrivals',
            'upcomingEvents',
            'activeAnnouncements',
            'recentLogs',
            'todayAttendance'
        ));
    }
}
