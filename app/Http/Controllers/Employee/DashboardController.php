<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        
        // Ensure department relationship is loaded
        if ($user && !$user->relationLoaded('department')) {
            $user->load('department');
        }
        
        // Get the department name with null safety
        $departmentName = 'N/A';
        if ($user && $user->department_id && $user->department) {
            $departmentName = $user->department->name;
        }

        // Employee profile info - from authenticated user
        $profile = [
            'name' => $user->name,
            'position' => $user->position ?? 'N/A',
            'department' => $departmentName,
            'faculty_id' => $user->faculty_id ?? 'N/A',
        ];

        // Monthly attendance stats (current month)
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        
        $monthlyAttendance = Attendance::where('user_id', $user->id)
            ->whereYear('attendance_date', $currentYear)
            ->whereMonth('attendance_date', $currentMonth)
            ->get();

        $daysPresent = $monthlyAttendance->whereIn('status', ['present', 'late'])->count();
        $absences = $monthlyAttendance->where('status', 'absent')->count();
        $lateArrivals = $monthlyAttendance->where('status', 'late')->count();

        // Today's attendance
        $todayAttendance = Attendance::where('user_id', $user->id)
            ->where('attendance_date', Carbon::now()->toDateString())
            ->first();

        $todayTimeIn = $todayAttendance?->time_in;
        $todayTimeOut = $todayAttendance?->time_out;
        $todayStatus = $todayAttendance?->status ?? 'No status';

        // Attendance Records - last 15 records ordered by date descending
        $attendanceRecords = Attendance::where('user_id', $user->id)
            ->orderBy('attendance_date', 'desc')
            ->limit(15)
            ->get();

        // Leave Request Stats
        $leaveRequests = LeaveRequest::where('user_id', $user->id)->get();
        $pendingLeaves = $leaveRequests->where('status', 'pending')->count();
        $approvedLeaves = $leaveRequests->where('status', 'approved')->count();
        $rejectedLeaves = $leaveRequests->where('status', 'rejected')->count();
        
        // All leave requests ordered by most recent
        $allLeaveRequests = LeaveRequest::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('employee.dashboard', compact(
            'user',
            'profile',
            'daysPresent',
            'absences',
            'lateArrivals',
            'todayTimeIn',
            'todayTimeOut',
            'todayStatus',
            'attendanceRecords',
            'pendingLeaves',
            'approvedLeaves',
            'rejectedLeaves',
            'allLeaveRequests'
        ));
    }

    public function attendanceHistory()
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        // Monthly attendance stats (current month)
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        
        $monthlyAttendance = Attendance::where('user_id', $user->id)
            ->whereYear('attendance_date', $currentYear)
            ->whereMonth('attendance_date', $currentMonth)
            ->get();

        $daysPresent = $monthlyAttendance->whereIn('status', ['present', 'late'])->count();
        $absences = $monthlyAttendance->where('status', 'absent')->count();
        $lateArrivals = $monthlyAttendance->where('status', 'late')->count();

        // All attendance records ordered by date descending
        $attendanceRecords = Attendance::where('user_id', $user->id)
            ->orderBy('attendance_date', 'desc')
            ->get();

        return view('employee.attendance-history', compact(
            'daysPresent',
            'absences',
            'lateArrivals',
            'attendanceRecords'
        ));
    }
}
