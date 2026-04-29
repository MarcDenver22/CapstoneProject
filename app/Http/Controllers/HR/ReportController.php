<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use App\Models\Department;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    protected AuditLogger $auditLogger;

    public function __construct(AuditLogger $auditLogger)
    {
        $this->auditLogger = $auditLogger;
    }

    /**
     * Show report generation form
     */
    public function index()
    {
        $employees = User::whereIn('role', ['employee', 'hr'])->orderBy('name')->get();
        $departments = Department::where('is_active', true)->orderBy('name')->get();

        return view('hr.reports.index', compact('employees', 'departments'));
    }



    /**
     * Generate monthly attendance report
     */
    public function monthly(Request $request)
    {
        $request->validate([
            'month' => 'required|date_format:Y-m',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        $month = Carbon::createFromFormat('Y-m', $request->month);
        $startDate = $month->clone()->startOfMonth();
        $endDate = $month->clone()->endOfMonth();
        $departmentId = $request->department_id;

        $query = Attendance::whereBetween('attendance_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->with('user.department')
            ->orderBy('attendance_date', 'asc');

        if ($departmentId) {
            $query->whereHas('user', function ($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            });
        }

        $records = $query->get();
        $department = $departmentId ? Department::find($departmentId) : null;

        // Generate summary by employee
        $summary = $records->groupBy('user.id')->map(function ($userRecords) {
            return [
                'user' => $userRecords->first()->user,
                'present' => $userRecords->where('status', 'present')->count(),
                'absent' => $userRecords->where('status', 'absent')->count(),
                'late' => $userRecords->where('status', 'late')->count(),
                'half_day' => $userRecords->where('status', 'half_day')->count(),
                'leave' => $userRecords->where('status', 'leave')->count(),
                'total_hours' => $userRecords->sum(function ($record) {
                    return $this->calculateHours($record);
                }),
                'records' => $userRecords,
            ];
        })->sortBy('user.name');

        return view('hr.reports.monthly', compact('summary', 'month', 'startDate', 'endDate', 'department'));
    }

    /**
     * Generate per-employee attendance report
     */
    public function perEmployee(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $employee = User::findOrFail($request->employee_id);
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        $records = Attendance::where('user_id', $employee->id)
            ->whereBetween('attendance_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->with('user.department')
            ->orderBy('attendance_date', 'asc')
            ->get();

        $summary = [
            'present' => $records->where('status', 'present')->count(),
            'absent' => $records->where('status', 'absent')->count(),
            'late' => $records->where('status', 'late')->count(),
            'half_day' => $records->where('status', 'half_day')->count(),
            'leave' => $records->where('status', 'leave')->count(),
            'total_hours' => $records->sum(function ($record) {
                return $this->calculateHours($record);
            }),
            'average_time_in' => $this->calculateAverageTime($records, 'time_in'),
            'average_time_out' => $this->calculateAverageTime($records, 'time_out'),
        ];

        return view('hr.reports.per-employee', compact('employee', 'records', 'summary', 'startDate', 'endDate'));
    }



    /**
     * Export report to CSV
     */
    public function exportCsv(Request $request)
    {
        $request->validate([
            'type' => 'required|in:monthly,per-employee',
        ]);

        $type = $request->type;
        $fileName = "attendance_report_{$type}_" . now()->format('Y-m-d_H-i-s') . '.csv';

        // Get the appropriate data based on type
        $data = $this->getReportData($request, $type);

        // Log the export action
        AuditLogger::log('export', 'attendance_report', Auth::id(), [
            'type' => $type,
            'filters' => $request->except(['type', 'export']),
            'file' => $fileName,
        ]);

        return response()->streamDownload(function () use ($data, $type) {
            $this->outputCsvStream($data, $type);
        }, $fileName, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$fileName}",
        ]);
    }

    /**
     * Export report to PDF
     */
    public function exportPdf(Request $request)
    {
        $request->validate([
            'type' => 'required|in:monthly,per-employee',
        ]);

        $type = $request->type;
        $fileName = "attendance_report_{$type}_" . now()->format('Y-m-d_H-i-s') . '.pdf';

        // Log the export action
        AuditLogger::log('export', 'attendance_report', Auth::id(), [
            'type' => $type,
            'filters' => $request->except(['type', 'export']),
            'file' => $fileName,
        ]);

        // Get appropriate view and data
        $data = $this->getReportData($request, $type);
        
        // Export as DTR format
        if ($type === 'per-employee') {
            // Export single employee's DTR
            $employee = User::findOrFail($request->employee_id);
            $month = Carbon::createFromFormat('Y-m', $request->month);
            $startDate = $month->clone()->startOfMonth();
            $endDate = $month->clone()->endOfMonth();

            // Get month and year from parsed date
            $monthNum = $month->month;
            $year = $month->year;

            $attendanceRecords = Attendance::where('user_id', $employee->id)
                ->whereBetween('attendance_date', [$startDate->toDateString(), $endDate->toDateString()])
                ->orderBy('attendance_date', 'asc')
                ->get();

            // Process attendance records into DTR format
            $daysData = $this->processDaysDataForDtr($attendanceRecords);

            return view('exports.dtr-export', [
                'user' => $employee,
                'attendanceRecords' => $attendanceRecords,
                'daysData' => $daysData,
                'month' => $monthNum,
                'year' => $year,
                'redirect_route' => route('hr.reports.index'),
            ], ['fileName' => $fileName]);

        } elseif ($type === 'monthly') {
            // Export monthly as multiple DTR forms (one per employee)
            $month = Carbon::createFromFormat('Y-m', $request->month);
            $startDate = $month->clone()->startOfMonth();
            $endDate = $month->clone()->endOfMonth();
            $departmentId = $request->department_id;

            $query = User::whereIn('role', ['employee', 'hr']);
            if ($departmentId) {
                $query->where('department_id', $departmentId);
            }
            $employees = $query->orderBy('name')->get();

            // Build data for all employees in DTR format
            $dtrExports = [];
            foreach ($employees as $employee) {
                $attendanceRecords = Attendance::where('user_id', $employee->id)
                    ->whereBetween('attendance_date', [$startDate->toDateString(), $endDate->toDateString()])
                    ->orderBy('attendance_date', 'asc')
                    ->get();

                $daysData = $this->processDaysDataForDtr($attendanceRecords);

                $dtrExports[] = [
                    'user' => $employee,
                    'attendanceRecords' => $attendanceRecords,
                    'daysData' => $daysData,
                    'month' => $month->month,
                    'year' => $month->year,
                ];
            }

            // Pass as collection to monthly DTR view for rendering multiple forms
            return view('exports.monthly-dtr-export', [
                'dtrExports' => $dtrExports,
                'month' => $month,
                'redirect_route' => route('hr.reports.index'),
            ], ['fileName' => $fileName]);
        }
        
        // Fallback
        return redirect()->route('hr.reports.index')->with('error', 'Invalid report type');
    }

    /**
     * Helper: Get report data based on type
     */
    private function getReportData(Request $request, string $type)
    {
        return match($type) {
            'monthly' => $this->getMonthlyData($request),
            'per-employee' => $this->getPerEmployeeData($request),
        };
    }



    private function getMonthlyData(Request $request)
    {
        $month = Carbon::createFromFormat('Y-m', $request->month);
        $startDate = $month->clone()->startOfMonth();
        $endDate = $month->clone()->endOfMonth();
        
        $query = Attendance::whereBetween('attendance_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->with('user.department');
        
        if ($request->department_id) {
            $query->whereHas('user', fn($q) => $q->where('department_id', $request->department_id));
        }
        
        return $query->orderBy('attendance_date')->get();
    }

    private function getPerEmployeeData(Request $request)
    {
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        
        return Attendance::where('user_id', $request->employee_id)
            ->whereBetween('attendance_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->with('user.department')
            ->orderBy('attendance_date')
            ->get();
    }



    /**
     * Output CSV stream
     */
    private function outputCsvStream(Collection $records, string $type)
    {
        $output = fopen('php://output', 'w');

        if ($type === 'daily') {
            fputcsv($output, ['Employee', 'Department', 'Time In', 'Time Out', 'Status', 'Liveness Verified', 'Notes']);
        } else {
            fputcsv($output, ['Date', 'Employee', 'Department', 'Time In', 'Time Out', 'Status', 'Liveness Verified']);
        }

        foreach ($records as $record) {
            if ($type === 'daily') {
                fputcsv($output, [
                    $record->user->name,
                    $record->user->department->name ?? 'N/A',
                    $record->time_in ? $record->time_in->format('H:i:s') : 'N/A',
                    $record->time_out ? $record->time_out->format('H:i:s') : 'N/A',
                    ucfirst(str_replace('_', ' ', $record->status)),
                    $record->liveness_verified ? 'Yes' : 'No',
                    $record->notes ?? '',
                ]);
            } else {
                fputcsv($output, [
                    $record->attendance_date->format('Y-m-d'),
                    $record->user->name,
                    $record->user->department->name ?? 'N/A',
                    $record->time_in ? $record->time_in->format('H:i:s') : 'N/A',
                    $record->time_out ? $record->time_out->format('H:i:s') : 'N/A',
                    ucfirst(str_replace('_', ' ', $record->status)),
                    $record->liveness_verified ? 'Yes' : 'No',
                ]);
            }
        }

        fclose($output);
    }

    /**
     * Helper: Calculate working hours
     */
    private function calculateHours(Attendance $record)
    {
        if (!$record->time_in || !$record->time_out) {
            return 0;
        }

        return $record->time_out->diffInHours($record->time_in);
    }

    /**
     * Helper: Calculate average time
     */
    private function calculateAverageTime(Collection $records, string $field)
    {
        $times = $records->whereNotNull($field)->pluck($field);
        
        if ($times->isEmpty()) {
            return 'N/A';
        }

        $sum = $times->reduce(function ($carry, $time) {
            return $carry + $time->secondsSinceMidnight();
        }, 0);

        $avgSeconds = $sum / count($times);
        $avgTime = Carbon::createFromTime(0, 0, 0)->addSeconds($avgSeconds);

        return $avgTime->format('H:i');
    }

    /**
     * Helper: Calculate attendance rate
     */
    /**
     * Process attendance records into DTR day format
     */
    private function processDaysDataForDtr(Collection $attendanceRecords)
    {
        $daysData = [];
        $recordsByDay = $attendanceRecords->groupBy(function($record) {
            return Carbon::parse($record->attendance_date)->day;
        });

        for ($day = 1; $day <= 31; $day++) {
            $dayRecords = $recordsByDay->get($day);
            $daysData[$day] = [];

            if ($dayRecords && $dayRecords->count() > 0) {
                $record = $dayRecords->first();

                $daysData[$day]['am_arrival'] = $record->am_arrival
                    ? Carbon::parse($record->am_arrival)->setTimezone('Asia/Manila')->format('H:i')
                    : '—';
                $daysData[$day]['am_depart'] = $record->am_departure
                    ? Carbon::parse($record->am_departure)->setTimezone('Asia/Manila')->format('H:i')
                    : '—';
                $daysData[$day]['pm_arrival'] = $record->pm_arrival
                    ? Carbon::parse($record->pm_arrival)->setTimezone('Asia/Manila')->format('H:i')
                    : '—';
                $daysData[$day]['pm_depart'] = $record->pm_departure
                    ? Carbon::parse($record->pm_departure)->setTimezone('Asia/Manila')->format('H:i')
                    : '—';

                if ($record->am_arrival && $record->pm_departure) {
                    $timeIn = Carbon::parse($record->am_arrival)->setTimezone('Asia/Manila');
                    $timeOut = Carbon::parse($record->pm_departure)->setTimezone('Asia/Manila');
                    $expected_minutes = 480; // 8 hours
                    $actual_minutes = $timeIn->diffInMinutes($timeOut);
                    $actual_work_minutes = $actual_minutes - 60; // Subtract 1 hour lunch

                    if ($actual_work_minutes < $expected_minutes) {
                        $undertime_minutes = $expected_minutes - $actual_work_minutes;
                        $daysData[$day]['undertime_hours'] = intdiv($undertime_minutes, 60);
                        $daysData[$day]['undertime_minutes'] = $undertime_minutes % 60;
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

        return $daysData;
    }

    private function calculateAttendanceRate(Collection $records)
    {
        if ($records->isEmpty()) {
            return 0;
        }

        $present = $records->whereIn('status', ['present', 'half_day'])->count();
        $rate = ($present / count($records)) * 100;

        return round($rate, 2);
    }

    /**
     * Helper: Calculate average department attendance rate
     */
    private function calculateAverageDepartmentRate(Collection $employeeSummary)
    {
        if (empty($employeeSummary)) {
            return 0;
        }

        $totalRate = $employeeSummary->sum('attendance_rate');
        return round($totalRate / count($employeeSummary), 2);
    }

    /**
     * Show DTR export page
     */
    public function dtrExportPage()
    {
        $employees = User::whereIn('role', ['employee', 'hr'])->orderBy('name')->get();
        return view('hr.dtr-export', compact('employees'));
    }

    /**
     * Export DTR as PDF (Civil Service Format)
     */
    public function exportDtrPdf(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2020',
        ]);

        $user = User::findOrFail($request->user_id);
        $month = $request->month;
        $year = $request->year;

        $startDate = Carbon::createFromFormat('Y-m-d', "{$year}-{$month}-01")->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $attendanceRecords = Attendance::where('user_id', $user->id)
            ->whereBetween('attendance_date', [$startDate->toDateString(), $endDate->toDateString()])
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

        $fileName = "DTR_{$user->name}_{$month}_{$year}.pdf";

        // Log the export action
        AuditLogger::log('export', 'dtr_pdf', Auth::id(), [
            'user_id' => $user->id,
            'month' => $month,
            'year' => $year,
            'file' => $fileName,
        ]);

        // Generate the HTML from the view
        $html = view('exports.dtr-export', [
            'user' => $user,
            'attendanceRecords' => $attendanceRecords,
            'daysData' => $daysData,
            'month' => $month,
            'year' => $year,
        ])->render();

        // Generate and download PDF
        return Pdf::loadHTML($html)
            ->setPaper('a4')
            ->setOption('margin-top', 0.5)
            ->setOption('margin-bottom', 0.5)
            ->setOption('margin-left', 0.5)
            ->setOption('margin-right', 0.5)
            ->download($fileName);
    }

    /**
     * Print DTR in Civil Service Format
     */
    public function printDtrPdf(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2020',
        ]);

        $user = User::findOrFail($request->user_id);
        $month = $request->month;
        $year = $request->year;

        $startDate = Carbon::createFromFormat('Y-m-d', "{$year}-{$month}-01")->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $attendanceRecords = Attendance::where('user_id', $user->id)
            ->whereBetween('attendance_date', [$startDate->toDateString(), $endDate->toDateString()])
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

        // Log the print action
        AuditLogger::log('print', 'dtr_pdf', Auth::id(), [
            'user_id' => $user->id,
            'month' => $month,
            'year' => $year,
        ]);

        // Return the view for printing
        return view('exports.dtr-export', [
            'user' => $user,
            'attendanceRecords' => $attendanceRecords,
            'daysData' => $daysData,
            'month' => $month,
            'year' => $year,
        ]);
    }

    /**
     * Show the HR user's attendance history with HR layout
     */
    public function attendanceHistory()
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (!$user || !in_array($user->role, ['hr', 'super_admin'])) {
            return redirect('/')->with('error', 'Unauthorized access');
        }

        // Get all attendance records for the authenticated HR user
        $attendanceRecords = Attendance::on('supabase')
            ->where('user_id', $user->id)
            ->orderBy('attendance_date', 'desc')
            ->paginate(20);

        return view('employee.attendance-history-table', compact('user', 'attendanceRecords'));
    }

}