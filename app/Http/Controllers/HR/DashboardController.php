<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\Department;
use App\Models\Event;
use App\Models\Announcement;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        // HR Profile
        $profile = $user->load('department');

        // Current month attendance stats
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // All employees count (includes both employee and hr roles)
        $totalEmployees = User::whereIn('role', ['employee', 'hr'])->count();
        
        // Monthly attendance stats (all employees - current month)
        $monthlyAttendance = Attendance::whereYear('attendance_date', $currentYear)
            ->whereMonth('attendance_date', $currentMonth)
            ->get();

        $daysPresent = $monthlyAttendance->whereIn('status', ['present', 'late'])->count();
        $absences = $monthlyAttendance->where('status', 'absent')->count();
        $lateArrivals = $monthlyAttendance->where('status', 'late')->count();

        // Today's attendance overview
        $todayAttendance = Attendance::where('attendance_date', Carbon::now()->toDateString())->get();
        $todayPresent = $todayAttendance->whereIn('status', ['present', 'late'])->count();
        $todayAbsent = $todayAttendance->where('status', 'absent')->count();

        // Attendance Records - last 15 records ordered by date descending
        $attendanceRecords = Attendance::orderBy('attendance_date', 'desc')
            ->limit(15)
            ->get();

        // Leave Requests Stats
        $allLeaveRequests = LeaveRequest::orderBy('created_at', 'desc')->get();
        $pendingLeaves = $allLeaveRequests->where('status', 'pending')->count();
        $approvedLeaves = $allLeaveRequests->where('status', 'approved')->count();
        $rejectedLeaves = $allLeaveRequests->where('status', 'rejected')->count();

        // Get latest leave request for display
        $latestRequest = $allLeaveRequests->first();

        // Department stats
        $departments = Department::withCount('users')->get();

        // Events and Announcements for dashboard
        // Get all events (regardless of status for debugging)
        $upcomingEvents = Event::orderBy('start_date', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($event) {
                $event->type = 'event';
                $event->display_date = $event->start_date->format('M d');
                return $event;
            });
        
        // Get all announcements (regardless of status for debugging)
        $activeAnnouncements = Announcement::orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($announcement) {
                $announcement->type = 'announcement';
                $announcement->display_date = $announcement->published_at ? $announcement->published_at->format('M d') : now()->format('M d');
                return $announcement;
            });
        
        // Combine and sort by date
        $eventsAndAnnouncements = $upcomingEvents->merge($activeAnnouncements)
            ->sortByDesc(function ($item) {
                return $item->type === 'event' ? $item->start_date : ($item->published_at ?? now());
            })
            ->values();

        return view('hr.dashboard', compact(
            'user',
            'profile',
            'totalEmployees',
            'daysPresent',
            'absences',
            'lateArrivals',
            'todayPresent',
            'todayAbsent',
            'attendanceRecords',
            'pendingLeaves',
            'approvedLeaves',
            'rejectedLeaves',
            'latestRequest',
            'allLeaveRequests',
            'departments',
            'eventsAndAnnouncements'
        ));
    }
}
