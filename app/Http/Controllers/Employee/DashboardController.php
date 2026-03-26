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

        // Process attendance records into structured daily data for DTR display
        $daysData = [];
        $totalHours = 0;
        $totalMinutes = 0;

        $allAttendanceRecords = Attendance::where('user_id', $user->id)
            ->whereYear('attendance_date', $currentYear)
            ->whereMonth('attendance_date', $currentMonth)
            ->orderBy('attendance_date', 'asc')
            ->get();

        // Group records by day
        $recordsByDay = $allAttendanceRecords->groupBy(function($record) {
            return \Carbon\Carbon::parse($record->attendance_date)->day;
        });

        // Process each day (1-31)
        for ($day = 1; $day <= 31; $day++) {
            $dayRecords = $recordsByDay->get($day);
            $daysData[$day] = [];

            if ($dayRecords && $dayRecords->count() > 0) {
                // Get the first and last record for the day
                $firstRecord = $dayRecords->first();
                $lastRecord = $dayRecords->last();

                // A.M. Arrival (first time_in of the day)
                if ($firstRecord->time_in) {
                    $timeIn = \Carbon\Carbon::parse($firstRecord->time_in);
                    $daysData[$day]['am_arrival'] = $timeIn->format('H:i');
                }

                // A.M. Departure (noon - 12:00)
                $daysData[$day]['am_depart'] = '12:00';

                // P.M. Arrival (afternoon - 13:00)
                $daysData[$day]['pm_arrival'] = '13:00';

                // P.M. Departure (last time_out of the day)
                if ($lastRecord->time_out) {
                    $timeOut = \Carbon\Carbon::parse($lastRecord->time_out);
                    $daysData[$day]['pm_depart'] = $timeOut->format('H:i');
                }

                // Calculate undertime
                if ($firstRecord->time_in && $lastRecord->time_out) {
                    $timeIn = \Carbon\Carbon::parse($firstRecord->time_in);
                    $timeOut = \Carbon\Carbon::parse($lastRecord->time_out);
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
                }
            }
        }

        // If no real data, add sample data for demonstration
        if ($allAttendanceRecords->count() === 0) {
            $sampleDays = [1, 3, 4, 5, 8, 9, 11, 12, 15, 16, 18, 19, 22, 23, 25];
            foreach ($sampleDays as $day) {
                $daysData[$day] = [
                    'am_arrival' => '08:' . str_pad(rand(0, 30), 2, '0', STR_PAD_LEFT),
                    'am_depart' => '12:00',
                    'pm_arrival' => '13:00',
                    'pm_depart' => '17:' . str_pad(rand(0, 59), 2, '0', STR_PAD_LEFT),
                    'undertime_hours' => 0,
                    'undertime_minutes' => 0
                ];
            }
        }

        // Fetch active announcements and events for employee dashboard
        $announcements = Announcement::active()
            ->byPriority()
            ->limit(5)
            ->get();

        $events = Event::orderBy('start_date', 'desc')
            ->limit(5)
            ->get();

        // If no events exist, create sample events for demonstration
        if ($events->count() === 0) {
            $sampleEvents = [
                [
                    'title' => 'Team Meeting',
                    'description' => 'Monthly team sync-up meeting',
                    'start_date' => now()->addDays(3),
                    'end_date' => now()->addDays(3)->addHours(1),
                    'location' => 'Conference Room A',
                    'status' => 'upcoming'
                ],
                [
                    'title' => 'Training Workshop',
                    'description' => 'Professional development workshop',
                    'start_date' => now()->addDays(7),
                    'end_date' => now()->addDays(7)->addHours(3),
                    'location' => 'Building B - Hall 101',
                    'status' => 'upcoming'
                ],
                [
                    'title' => 'Company Outing',
                    'description' => 'Year-end company gathering',
                    'start_date' => now()->addDays(14),
                    'end_date' => now()->addDays(14)->addHours(4),
                    'location' => 'Beach Resort',
                    'status' => 'upcoming'
                ]
            ];
            
            $events = collect($sampleEvents);
        }

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
            return \Carbon\Carbon::parse($record->attendance_date)->day;
        });

        // Process each day (1-31)
        for ($day = 1; $day <= 31; $day++) {
            $dayRecords = $recordsByDay->get($day);
            $daysData[$day] = [];

            if ($dayRecords && $dayRecords->count() > 0) {
                // Get the first and last record for the day
                $firstRecord = $dayRecords->first();
                $lastRecord = $dayRecords->last();

                // A.M. Arrival (first time_in of the day, or default 08:00)
                if ($firstRecord->time_in) {
                    $timeIn = \Carbon\Carbon::parse($firstRecord->time_in);
                    $daysData[$day]['am_arrival'] = $timeIn->format('H:i');
                } else {
                    $daysData[$day]['am_arrival'] = '08:00';
                }

                // A.M. Departure (noon - 12:00)
                $daysData[$day]['am_depart'] = '12:00';

                // P.M. Arrival (afternoon - 13:00)
                $daysData[$day]['pm_arrival'] = '13:00';

                // P.M. Departure (last time_out of the day, or default 17:00)
                if ($lastRecord->time_out) {
                    $timeOut = \Carbon\Carbon::parse($lastRecord->time_out);
                    $daysData[$day]['pm_depart'] = $timeOut->format('H:i');
                } else {
                    $daysData[$day]['pm_depart'] = '17:00';
                }

                // Calculate undertime
                if ($firstRecord->time_in && $lastRecord->time_out) {
                    $timeIn = \Carbon\Carbon::parse($firstRecord->time_in);
                    $timeOut = \Carbon\Carbon::parse($lastRecord->time_out);
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
            return \Carbon\Carbon::parse($record->attendance_date)->day;
        });

        // Process each day (1-31)
        for ($day = 1; $day <= 31; $day++) {
            $dayRecords = $recordsByDay->get($day);
            $daysData[$day] = [];

            if ($dayRecords && $dayRecords->count() > 0) {
                // Get the first and last record for the day
                $firstRecord = $dayRecords->first();
                $lastRecord = $dayRecords->last();

                // A.M. Arrival (first time_in of the day, or default 08:00)
                if ($firstRecord->time_in) {
                    $timeIn = \Carbon\Carbon::parse($firstRecord->time_in);
                    $daysData[$day]['am_arrival'] = $timeIn->format('H:i');
                } else {
                    $daysData[$day]['am_arrival'] = '08:00'; // Sample default time
                }

                // A.M. Departure (noon - 12:00)
                $daysData[$day]['am_depart'] = '12:00';

                // P.M. Arrival (afternoon - 13:00)
                $daysData[$day]['pm_arrival'] = '13:00';

                // P.M. Departure (last time_out of the day, or default 17:00)
                if ($lastRecord->time_out) {
                    $timeOut = \Carbon\Carbon::parse($lastRecord->time_out);
                    $daysData[$day]['pm_depart'] = $timeOut->format('H:i');
                } else {
                    $daysData[$day]['pm_depart'] = '17:00'; // Sample default time
                }

                // Calculate undertime
                if ($firstRecord->time_in && $lastRecord->time_out) {
                    $timeIn = \Carbon\Carbon::parse($firstRecord->time_in);
                    $timeOut = \Carbon\Carbon::parse($lastRecord->time_out);
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
                }
            }
        }

        $fileName = "Attendance_History_{$user->name}_" . now()->format('Y-m-d_H-i-s') . '.pdf';

        // Generate the HTML from the view
        $html = view('exports.attendance-history-export', [
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
            return \Carbon\Carbon::parse($record->attendance_date)->day;
        });

        // Process each day (1-31)
        for ($day = 1; $day <= 31; $day++) {
            $dayRecords = $recordsByDay->get($day);
            $daysData[$day] = [];

            if ($dayRecords && $dayRecords->count() > 0) {
                // Get the first and last record for the day
                $firstRecord = $dayRecords->first();
                $lastRecord = $dayRecords->last();

                // A.M. Arrival (first time_in of the day, or default 08:00)
                if ($firstRecord->time_in) {
                    $timeIn = \Carbon\Carbon::parse($firstRecord->time_in);
                    $daysData[$day]['am_arrival'] = $timeIn->format('H:i');
                } else {
                    $daysData[$day]['am_arrival'] = '08:00';
                }

                // A.M. Departure (noon - 12:00)
                $daysData[$day]['am_depart'] = '12:00';

                // P.M. Arrival (afternoon - 13:00)
                $daysData[$day]['pm_arrival'] = '13:00';

                // P.M. Departure (last time_out of the day, or default 17:00)
                if ($lastRecord->time_out) {
                    $timeOut = \Carbon\Carbon::parse($lastRecord->time_out);
                    $daysData[$day]['pm_depart'] = $timeOut->format('H:i');
                } else {
                    $daysData[$day]['pm_depart'] = '17:00';
                }

                // Calculate undertime
                if ($firstRecord->time_in && $lastRecord->time_out) {
                    $timeIn = \Carbon\Carbon::parse($firstRecord->time_in);
                    $timeOut = \Carbon\Carbon::parse($lastRecord->time_out);
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
                }
            }
        }

        // Return the view for printing
        return view('exports.attendance-history-export', [
            'user' => $user,
            'attendanceRecords' => $attendanceRecords,
            'daysData' => $daysData,
            'month' => $currentMonth,
            'year' => $currentYear,
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

        return view('employee.profile.edit', compact('user', 'departments'));
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
            'faculty_id' => 'nullable|string|max:50',
            'position' => 'nullable|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        // Update the user
        $user->update($validated);

        return redirect()->route('employee.dashboard')->with('success', 'Profile updated successfully!');
    }
}
