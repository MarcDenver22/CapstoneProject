<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Attendance;
use App\Models\Event;
use App\Models\LeaveRequest;
use App\Models\Department;
use App\Models\User;
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
            'employee_id' => $user->faculty_id ?? 'N/A',
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
        $todayAttendance = Attendance::on('supabase')
            ->where('user_id', $user->id)
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
        $leaveRequests = LeaveRequest::on('supabase')
            ->where('user_id', $user->id)->get();
        $pendingLeaves = $leaveRequests->where('status', 'pending')->count();
        $approvedLeaves = $leaveRequests->where('status', 'approved')->count();
        $rejectedLeaves = $leaveRequests->where('status', 'rejected')->count();
        
        // All leave requests ordered by most recent
        $allLeaveRequests = LeaveRequest::on('supabase')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Process attendance records into structured daily data for DTR display
        $daysData = [];
        $totalHours = 0;
        $totalMinutes = 0;

        $allAttendanceRecords = Attendance::on('supabase')
            ->where('user_id', $user->id)
            ->whereYear('attendance_date', $currentYear)
            ->whereMonth('attendance_date', $currentMonth)
            ->orderBy('attendance_date', 'asc')
            ->get();

        // Group records by day
        $recordsByDay = $allAttendanceRecords->groupBy(function($record) {
            return Carbon::parse($record->attendance_date)->day;
        });

        // Process each day (1-31)
        for ($day = 1; $day <= 31; $day++) {
            $dayRecords = $recordsByDay->get($day);
            $daysData[$day] = [];

            if ($dayRecords && $dayRecords->count() > 0) {
                // Get first record for the day
                $record = $dayRecords->first();

                // A.M. Arrival
                $daysData[$day]['am_arrival'] = $record->am_arrival 
                    ? Carbon::parse($record->am_arrival)->setTimezone('Asia/Manila')->format('H:i') 
                    : '—';

                // A.M. Departure
                $daysData[$day]['am_depart'] = $record->am_departure 
                    ? Carbon::parse($record->am_departure)->setTimezone('Asia/Manila')->format('H:i') 
                    : '—';

                // P.M. Arrival
                $daysData[$day]['pm_arrival'] = $record->pm_arrival 
                    ? Carbon::parse($record->pm_arrival)->setTimezone('Asia/Manila')->format('H:i') 
                    : '—';

                // P.M. Departure
                $daysData[$day]['pm_depart'] = $record->pm_departure 
                    ? Carbon::parse($record->pm_departure)->setTimezone('Asia/Manila')->format('H:i') 
                    : '—';

                // Calculate undertime based on actual punch times
                if ($record->am_arrival && $record->pm_departure) {
                    $timeIn = Carbon::parse($record->am_arrival)->setTimezone('Asia/Manila');
                    $timeOut = Carbon::parse($record->pm_departure)->setTimezone('Asia/Manila');
                    $expected_minutes = 480; // 8 hours
                    $actual_minutes = $timeIn->diffInMinutes($timeOut);
                    $actual_work_minutes = $actual_minutes - 60; // Subtract 1 hour lunch break

                    if ($actual_work_minutes < $expected_minutes) {
                        $undertime_minutes = $expected_minutes - $actual_work_minutes;
                        $daysData[$day]['undertime_hours'] = intdiv($undertime_minutes, 60);
                        $daysData[$day]['undertime_minutes'] = $undertime_minutes % 60;
                        $totalHours += $daysData[$day]['undertime_hours'];
                        $totalMinutes += $daysData[$day]['undertime_minutes'];
                        // Handle minute overflow
                        if ($totalMinutes >= 60) {
                            $totalHours += intdiv($totalMinutes, 60);
                            $totalMinutes = $totalMinutes % 60;
                        }
                    } else {
                        $daysData[$day]['undertime_hours'] = 0;
                        $daysData[$day]['undertime_minutes'] = 0;
                    }
                } else {
                    // If no complete punch times recorded, set undertime to 0
                    $daysData[$day]['undertime_hours'] = 0;
                    $daysData[$day]['undertime_minutes'] = 0;
                }
            }
        }

        // If no real data, all daysData entries will remain empty
        // No sample data generation needed

        // Fetch active announcements and events for employee dashboard
        $announcements = Announcement::on('supabase')
            ->active()
            ->byPriority()
            ->limit(5)
            ->get();

        $events = Event::on('supabase')
            ->orderBy('start_date', 'desc')
            ->limit(5)
            ->get();

        // No sample event creation - use only real events from database

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
            'daysData',
            'totalHours',
            'totalMinutes',
            'pendingLeaves',
            'approvedLeaves',
            'rejectedLeaves',
            'allLeaveRequests',
            'announcements',
            'events'
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

        // All attendance records ordered by date ascending (1-31)
        $attendanceRecords = Attendance::where('user_id', $user->id)
            ->orderBy('attendance_date', 'asc')
            ->get();

        // Process attendance records into structured daily data (same as PDF export)
        $daysData = [];
        $totalHours = 0;
        $totalMinutes = 0;

        // Group records by day
        $recordsByDay = $attendanceRecords->groupBy(function($record) {
            return Carbon::parse($record->attendance_date)->day;
        });

        // Process each day (1-31)
        for ($day = 1; $day <= 31; $day++) {
            $dayRecords = $recordsByDay->get($day);
            $daysData[$day] = [];

            if ($dayRecords && $dayRecords->count() > 0) {
                // Get first record for the day
                $record = $dayRecords->first();
                $firstRecord = $dayRecords->first();
                $lastRecord = $dayRecords->last();

                // A.M. Arrival
                $daysData[$day]['am_arrival'] = $record->am_arrival 
                    ? Carbon::parse($record->am_arrival)->setTimezone('Asia/Manila')->format('H:i') 
                    : '—';

                // A.M. Departure
                $daysData[$day]['am_depart'] = $record->am_departure 
                    ? Carbon::parse($record->am_departure)->setTimezone('Asia/Manila')->format('H:i') 
                    : '—';

                // P.M. Arrival
                $daysData[$day]['pm_arrival'] = $record->pm_arrival 
                    ? Carbon::parse($record->pm_arrival)->setTimezone('Asia/Manila')->format('H:i') 
                    : '—';

                // P.M. Departure
                $daysData[$day]['pm_depart'] = $record->pm_departure 
                    ? Carbon::parse($record->pm_departure)->setTimezone('Asia/Manila')->format('H:i') 
                    : '—';

                // Calculate undertime
                if ($record->am_arrival && $record->pm_departure) {
                    $timeIn = Carbon::parse($record->am_arrival)->setTimezone('Asia/Manila');
                    $timeOut = Carbon::parse($record->pm_departure)->setTimezone('Asia/Manila');
                    $expected_minutes = 480; // 8 hours
                    $actual_minutes = $timeIn->diffInMinutes($timeOut);
                    $actual_work_minutes = $actual_minutes - 60; // Subtract 1 hour lunch break

                    if ($actual_work_minutes < $expected_minutes) {
                        $undertime_minutes = $expected_minutes - $actual_work_minutes;
                        $daysData[$day]['undertime_hours'] = intdiv($undertime_minutes, 60);
                        $daysData[$day]['undertime_minutes'] = $undertime_minutes % 60;
                        $totalHours += $daysData[$day]['undertime_hours'];
                        $totalMinutes += $daysData[$day]['undertime_minutes'];
                        // Handle minute overflow
                        if ($totalMinutes >= 60) {
                            $totalHours += intdiv($totalMinutes, 60);
                            $totalMinutes = $totalMinutes % 60;
                        }
                    } else {
                        $daysData[$day]['undertime_hours'] = 0;
                        $daysData[$day]['undertime_minutes'] = 0;
                    }
                } else {
                    $daysData[$day]['undertime_hours'] = 0;
                    $daysData[$day]['undertime_minutes'] = 0;
                }
            }
        }

        return view('employee.attendance-history-table', compact(
            'user',
            'daysPresent',
            'absences',
            'lateArrivals',
            'attendanceRecords',
            'daysData',
            'totalHours',
            'totalMinutes'
        ));
    }

    public function exportHistoryPdf()
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        // Get all attendance records for the current month
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        
        $attendanceRecords = Attendance::where('user_id', $user->id)
            ->whereYear('attendance_date', $currentYear)
            ->whereMonth('attendance_date', $currentMonth)
            ->orderBy('attendance_date', 'asc')
            ->get();

        // Process attendance records into structured daily data
        $daysData = [];
        $totalHours = 0;
        $totalMinutes = 0;

        // Group records by day
        $recordsByDay = $attendanceRecords->groupBy(function($record) {
            return Carbon::parse($record->attendance_date)->day;
        });

        // Process each day (1-31)
        for ($day = 1; $day <= 31; $day++) {
            $dayRecords = $recordsByDay->get($day);
            $daysData[$day] = [];

            if ($dayRecords && $dayRecords->count() > 0) {
                // Get the first record for the day
                $record = $dayRecords->first();

                // A.M. Arrival
                $daysData[$day]['am_arrival'] = $record->am_arrival
                    ? Carbon::parse($record->am_arrival)->setTimezone('Asia/Manila')->format('H:i')
                    : '—';

                // A.M. Departure
                $daysData[$day]['am_depart'] = $record->am_departure
                    ? Carbon::parse($record->am_departure)->setTimezone('Asia/Manila')->format('H:i')
                    : '—';

                // P.M. Arrival
                $daysData[$day]['pm_arrival'] = $record->pm_arrival
                    ? Carbon::parse($record->pm_arrival)->setTimezone('Asia/Manila')->format('H:i')
                    : '—';

                // P.M. Departure
                $daysData[$day]['pm_depart'] = $record->pm_departure
                    ? Carbon::parse($record->pm_departure)->setTimezone('Asia/Manila')->format('H:i')
                    : '—';

                // Calculate undertime based on actual punch times
                if ($record->am_arrival && $record->pm_departure) {
                    $timeIn = Carbon::parse($record->am_arrival)->setTimezone('Asia/Manila');
                    $timeOut = Carbon::parse($record->pm_departure)->setTimezone('Asia/Manila');
                    $expected_minutes = 480; // 8 hours
                    $actual_minutes = $timeIn->diffInMinutes($timeOut);
                    $actual_work_minutes = $actual_minutes - 60; // Subtract 1 hour lunch break

                    if ($actual_work_minutes < $expected_minutes) {
                        $undertime_minutes = $expected_minutes - $actual_work_minutes;
                        $daysData[$day]['undertime_hours'] = intdiv($undertime_minutes, 60);
                        $daysData[$day]['undertime_minutes'] = $undertime_minutes % 60;
                        $totalHours += $daysData[$day]['undertime_hours'];
                        $totalMinutes += $daysData[$day]['undertime_minutes'];
                        // Handle minute overflow
                        if ($totalMinutes >= 60) {
                            $totalHours += intdiv($totalMinutes, 60);
                            $totalMinutes = $totalMinutes % 60;
                        }
                    } else {
                        $daysData[$day]['undertime_hours'] = 0;
                        $daysData[$day]['undertime_minutes'] = 0;
                    }
                } else {
                    $daysData[$day]['undertime_hours'] = 0;
                    $daysData[$day]['undertime_minutes'] = 0;
                }
            }
        }

        $fileName = "Attendance_History_{$user->name}_" . now()->format('Y-m-d_H-i-s') . '.pdf';

        // Generate the HTML from the view
        $html = view('exports.dtr-export', [
            'user' => $user,
            'attendanceRecords' => $attendanceRecords,
            'daysData' => $daysData,
            'month' => $currentMonth,
            'year' => $currentYear,
        ])->render();

        // Generate and download PDF
        return \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)
            ->setPaper('a4')
            ->setOption('margin-top', 0.5)
            ->setOption('margin-bottom', 0.5)
            ->setOption('margin-left', 0.5)
            ->setOption('margin-right', 0.5)
            ->download($fileName);
    }

    /**
     * Print the attendance history in DTR format
     */
    public function printHistoryPdf()
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        // Get all attendance records for the current month
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        
        $attendanceRecords = Attendance::where('user_id', $user->id)
            ->whereYear('attendance_date', $currentYear)
            ->whereMonth('attendance_date', $currentMonth)
            ->orderBy('attendance_date', 'asc')
            ->get();

        // Process attendance records into structured daily data
        $daysData = [];
        $totalHours = 0;
        $totalMinutes = 0;

        // Group records by day
        $recordsByDay = $attendanceRecords->groupBy(function($record) {
            return Carbon::parse($record->attendance_date)->day;
        });

        // Process each day (1-31)
        for ($day = 1; $day <= 31; $day++) {
            $dayRecords = $recordsByDay->get($day);
            $daysData[$day] = [];

            if ($dayRecords && $dayRecords->count() > 0) {
                // Get the first record for the day
                $record = $dayRecords->first();

                // A.M. Arrival
                $daysData[$day]['am_arrival'] = $record->am_arrival
                    ? Carbon::parse($record->am_arrival)->setTimezone('Asia/Manila')->format('H:i')
                    : '—';

                // A.M. Departure
                $daysData[$day]['am_depart'] = $record->am_departure
                    ? Carbon::parse($record->am_departure)->setTimezone('Asia/Manila')->format('H:i')
                    : '—';

                // P.M. Arrival
                $daysData[$day]['pm_arrival'] = $record->pm_arrival
                    ? Carbon::parse($record->pm_arrival)->setTimezone('Asia/Manila')->format('H:i')
                    : '—';

                // P.M. Departure
                $daysData[$day]['pm_depart'] = $record->pm_departure
                    ? Carbon::parse($record->pm_departure)->setTimezone('Asia/Manila')->format('H:i')
                    : '—';

                // Calculate undertime based on actual punch times
                if ($record->am_arrival && $record->pm_departure) {
                    $timeIn = Carbon::parse($record->am_arrival)->setTimezone('Asia/Manila');
                    $timeOut = Carbon::parse($record->pm_departure)->setTimezone('Asia/Manila');
                    $expected_minutes = 480; // 8 hours
                    $actual_minutes = $timeIn->diffInMinutes($timeOut);
                    $actual_work_minutes = $actual_minutes - 60; // Subtract 1 hour lunch break

                    if ($actual_work_minutes < $expected_minutes) {
                        $undertime_minutes = $expected_minutes - $actual_work_minutes;
                        $daysData[$day]['undertime_hours'] = intdiv($undertime_minutes, 60);
                        $daysData[$day]['undertime_minutes'] = $undertime_minutes % 60;
                        $totalHours += $daysData[$day]['undertime_hours'];
                        $totalMinutes += $daysData[$day]['undertime_minutes'];
                        // Handle minute overflow
                        if ($totalMinutes >= 60) {
                            $totalHours += intdiv($totalMinutes, 60);
                            $totalMinutes = $totalMinutes % 60;
                        }
                    } else {
                        $daysData[$day]['undertime_hours'] = 0;
                        $daysData[$day]['undertime_minutes'] = 0;
                    }
                } else {
                    $daysData[$day]['undertime_hours'] = 0;
                    $daysData[$day]['undertime_minutes'] = 0;
                }
            }
        }

        // Return the view for printing
        return view('exports.dtr-export', [
            'user' => $user,
            'attendanceRecords' => $attendanceRecords,
            'daysData' => $daysData,
            'month' => $currentMonth,
            'year' => $currentYear,
            'redirect_route' => route('employee.attendance-history'),
        ]);
    }

    /**
     * Show the profile edit form for the authenticated employee
     */
    public function editProfile()
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        $user->load('department');
        
        $departments = Department::where('is_active', true)->get();

        return view('profile.edit', compact('user', 'departments'));
    }

    /**
     * Update the authenticated employee's profile
     */
    public function updateProfile(Request $request)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        // Validate the input
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'position' => 'nullable|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        // Update the user
        $user->update($validated);

        return redirect()->route('employee.dashboard')->with('success', 'Profile updated successfully!');
    }
}
