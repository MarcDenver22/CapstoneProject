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

        // Attendance Records for HR user - last 15 records
        $attendanceRecords = Attendance::where('user_id', $user->id)
            ->orderBy('attendance_date', 'desc')
            ->limit(15)
            ->get();

        // Process HR user's attendance data into DTR format
        $daysData = [];
        $totalHours = 0;
        $totalMinutes = 0;

        $hrAttendanceRecords = Attendance::where('user_id', $user->id)
            ->whereYear('attendance_date', $currentYear)
            ->whereMonth('attendance_date', $currentMonth)
            ->orderBy('attendance_date', 'asc')
            ->get();

        // Group records by day
        $recordsByDay = $hrAttendanceRecords->groupBy(function($record) {
            return \Carbon\Carbon::parse($record->attendance_date)->day;
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
                if ($record->am_arrival) {
                    $timeIn = \Carbon\Carbon::parse($record->am_arrival);
                    $daysData[$day]['am_arrival'] = $timeIn->format('H:i');
                }

                // A.M. Departure
                $daysData[$day]['am_depart'] = $record->am_departure 
                    ? \Carbon\Carbon::parse($record->am_departure)->format('H:i') 
                    : '—';

                // P.M. Arrival
                $daysData[$day]['pm_arrival'] = $record->pm_arrival 
                    ? \Carbon\Carbon::parse($record->pm_arrival)->format('H:i') 
                    : '—';

                // P.M. Departure
                $daysData[$day]['pm_depart'] = $record->pm_departure 
                    ? \Carbon\Carbon::parse($record->pm_departure)->format('H:i') 
                    : '—';

                // Calculate undertime
                if ($record->am_arrival && $record->pm_departure) {
                    $timeIn = \Carbon\Carbon::parse($record->am_arrival);
                    $timeOut = \Carbon\Carbon::parse($record->pm_departure);
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
                    // If no time_in or time_out recorded yet, set undertime to 0
                    $daysData[$day]['undertime_hours'] = 0;
                    $daysData[$day]['undertime_minutes'] = 0;
                }
            }
        }

        // If no real data, add sample data for demonstration
        if ($hrAttendanceRecords->count() === 0) {
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

        // If no events exist, create sample events
        if ($upcomingEvents->count() === 0) {
            $upcomingEvents = collect([
                (object)[
                    'id' => 1,
                    'title' => 'Team Meeting',
                    'type' => 'event',
                    'display_date' => now()->addDays(3)->format('M d'),
                    'location' => 'Conference Room A',
                    'start_date' => now()->addDays(3),
                    'status' => 'upcoming'
                ],
                (object)[
                    'id' => 2,
                    'title' => 'Training Workshop',
                    'type' => 'event',
                    'display_date' => now()->addDays(7)->format('M d'),
                    'location' => 'Building B - Hall 101',
                    'start_date' => now()->addDays(7),
                    'status' => 'upcoming'
                ],
                (object)[
                    'id' => 3,
                    'title' => 'Company Outing',
                    'type' => 'event',
                    'display_date' => now()->addDays(14)->format('M d'),
                    'location' => 'Beach Resort',
                    'start_date' => now()->addDays(14),
                    'status' => 'upcoming'
                ]
            ]);
        }
        
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
        $eventsAndAnnouncements = $upcomingEvents->concat($activeAnnouncements)
            ->sortByDesc(function ($item) {
                if ($item->type === 'event') {
                    $date = $item->start_date;
                    return is_object($date) && method_exists($date, 'timestamp') ? $date->timestamp : strtotime($date);
                } else {
                    $date = $item->published_at ?? now();
                    return is_object($date) && method_exists($date, 'timestamp') ? $date->timestamp : strtotime($date);
                }
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
            'daysData',
            'totalHours',
            'totalMinutes',
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
