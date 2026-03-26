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
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    protected $auditLogger;

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
     * Generate daily attendance report
     */
    public function daily(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        $date = Carbon::parse($request->date);
        $departmentId = $request->department_id;

        $query = Attendance::where('attendance_date', $date->toDateString())
            ->with('user.department');

        if ($departmentId) {
            $query->whereHas('user', function ($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            });
        }

        $records = $query->orderBy('attendance_date', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();

        $department = $departmentId ? Department::find($departmentId) : null;

        return view('hr.reports.daily', compact('records', 'date', 'department'));
    }

    /**
     * Generate weekly attendance report
     */
    public function weekly(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfWeek();
        $endDate = $startDate->clone()->endOfWeek();
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

        // Pivot data for summary by employee
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
        });

        return view('hr.reports.weekly', compact('records', 'summary', 'startDate', 'endDate', 'department'));
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
     * Generate per-department/unit attendance report
     */
    public function perDepartment(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $department = Department::findOrFail($request->department_id);
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        $records = Attendance::whereBetween('attendance_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->whereHas('user', function ($q) use ($department) {
                $q->where('department_id', $department->id);
            })
            ->with('user.department')
            ->orderBy('attendance_date', 'asc')
            ->get();

        // Summary by employee
        $employeeSummary = $records->groupBy('user.id')->map(function ($userRecords) {
            return [
                'user' => $userRecords->first()->user,
                'present' => $userRecords->where('status', 'present')->count(),
                'absent' => $userRecords->where('status', 'absent')->count(),
                'late' => $userRecords->where('status', 'late')->count(),
                'half_day' => $userRecords->where('status', 'half_day')->count(),
                'leave' => $userRecords->where('status', 'leave')->count(),
                'attendance_rate' => $this->calculateAttendanceRate($userRecords),
            ];
        })->sortBy('user.name');

        // Department-wide summary
        $departmentSummary = [
            'total_present' => $records->where('status', 'present')->count(),
            'total_absent' => $records->where('status', 'absent')->count(),
            'total_late' => $records->where('status', 'late')->count(),
            'total_half_day' => $records->where('status', 'half_day')->count(),
            'total_leave' => $records->where('status', 'leave')->count(),
            'average_attendance_rate' => $this->calculateAverageDepartmentRate($employeeSummary),
        ];

        return view('hr.reports.per-department', compact(
            'department',
            'records',
            'employeeSummary',
            'departmentSummary',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Export report to CSV
     */
    public function exportCsv(Request $request)
    {
        $request->validate([
            'type' => 'required|in:daily,weekly,monthly,per-employee,per-department',
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
            'type' => 'required|in:daily,weekly,monthly,per-employee,per-department',
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
        
        // For now, we'll output as a formatted HTML that can be printed as PDF
        // In production, consider using a library like dompdf
        return view('hr.reports.pdf-export', compact('data', 'type'), ['fileName' => $fileName]);
    }

    /**
     * Helper: Get report data based on type
     */
    private function getReportData(Request $request, $type)
    {
        return match($type) {
            'daily' => $this->getDailyData($request),
            'weekly' => $this->getWeeklyData($request),
            'monthly' => $this->getMonthlyData($request),
            'per-employee' => $this->getPerEmployeeData($request),
            'per-department' => $this->getPerDepartmentData($request),
        };
    }

    private function getDailyData(Request $request)
    {
        $date = Carbon::parse($request->date);
        $query = Attendance::where('attendance_date', $date->toDateString())->with('user.department');
        
        if ($request->department_id) {
            $query->whereHas('user', fn($q) => $q->where('department_id', $request->department_id));
        }
        
        return $query->orderBy('created_at')->get();
    }

    private function getWeeklyData(Request $request)
    {
        $startDate = Carbon::parse($request->start_date)->startOfWeek();
        $endDate = $startDate->clone()->endOfWeek();
        
        $query = Attendance::whereBetween('attendance_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->with('user.department');
        
        if ($request->department_id) {
            $query->whereHas('user', fn($q) => $q->where('department_id', $request->department_id));
        }
        
        return $query->orderBy('attendance_date')->get();
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

    private function getPerDepartmentData(Request $request)
    {
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        
        return Attendance::whereBetween('attendance_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->whereHas('user', fn($q) => $q->where('department_id', $request->department_id))
            ->with('user.department')
            ->orderBy('attendance_date')
            ->get();
    }

    /**
     * Output CSV stream
     */
    private function outputCsvStream($records, $type)
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
    private function calculateHours($record)
    {
        if (!$record->time_in || !$record->time_out) {
            return 0;
        }

        return $record->time_out->diffInHours($record->time_in);
    }

    /**
     * Helper: Calculate average time
     */
    private function calculateAverageTime($records, $field)
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
    private function calculateAttendanceRate($records)
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
    private function calculateAverageDepartmentRate($employeeSummary)
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

        $records = Attendance::where('user_id', $user->id)
            ->whereBetween('attendance_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->get();

        $fileName = "DTR_{$user->name}_{$month}_{$year}.xlsx";

        // Log the export action
        AuditLogger::log('export', 'dtr_excel', Auth::id(), [
            'user_id' => $user->id,
            'month' => $month,
            'year' => $year,
            'file' => $fileName,
        ]);

        return (new \App\Exports\DtrExport($records, $user, $month, $year))->download($fileName);
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

        $records = Attendance::where('user_id', $user->id)
            ->whereBetween('attendance_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->get();

        $fileName = "DTR_{$user->name}_{$month}_{$year}.pdf";

        // Log the export action
        AuditLogger::log('export', 'dtr_pdf', Auth::id(), [
            'user_id' => $user->id,
            'month' => $month,
            'year' => $year,
            'file' => $fileName,
        ]);

        $html = (new \App\Exports\DtrExport($records, $user, $month, $year))->generate();

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

        $records = Attendance::where('user_id', $user->id)
            ->whereBetween('attendance_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->get();

        // Log the print action
        AuditLogger::log('print', 'dtr_pdf', Auth::id(), [
            'user_id' => $user->id,
            'month' => $month,
            'year' => $year,
        ]);

        $html = (new \App\Exports\DtrExport($records, $user, $month, $year))->generate();

        return view('exports.dtr-print', [
            'html' => $html,
        ]);
    }


}
