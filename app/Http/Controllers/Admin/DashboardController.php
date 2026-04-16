<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AuditLog;
use App\Models\Announcement;
use App\Models\Attendance;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();
        
        // Stat Cards - Dynamic data from database
        $totalEmployees = User::on('supabase')
            ->whereIn('role', ['employee', 'hr'])->count();
        $presentToday = Attendance::on('supabase')
            ->where('attendance_date', $today)->where('status', 'present')->count();
        $absentToday = Attendance::on('supabase')
            ->where('attendance_date', $today)->where('status', 'absent')->count();
        $lateArrivals = Attendance::on('supabase')
            ->where('attendance_date', $today)->where('status', 'late')->count();

        // Announcements
        $activeAnnouncements = Announcement::on('supabase')
            ->where('status', '!=', 'archived')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Recent Audit Logs
        $recentLogs = AuditLog::on('supabase')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Today's Attendance Records
        $todayAttendance = Attendance::on('supabase')
            ->with('user')
            ->where('attendance_date', $today)
            ->orderBy('time_in', 'desc')
            ->get();

        return view('admin.dashboard', compact(
            'totalEmployees',
            'presentToday',
            'absentToday',
            'lateArrivals',
            'activeAnnouncements',
            'recentLogs',
            'todayAttendance'
        ));
    }
}
