<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Get daily attendance report
     */
    public function daily(Request $request)
    {
        $date = $request->date ?? Carbon::today();

        $attendance = Attendance::whereDate('date', $date)
            ->with('user')
            ->paginate(15);

        $stats = [
            'total_checked_in' => Attendance::whereDate('date', $date)->whereNotNull('check_in')->count(),
            'total_checked_out' => Attendance::whereDate('date', $date)->whereNotNull('check_out')->count(),
            'total_absent' => User::count() - Attendance::whereDate('date', $date)->count(),
        ];

        return response()->json([
            'success' => true,
            'date' => $date,
            'statistics' => $stats,
            'data' => $attendance
        ]);
    }

    /**
     * Get weekly attendance report
     */
    public function weekly(Request $request)
    {
        $startDate = $request->start_date ?? Carbon::now()->startOfWeek();
        $endDate = $request->end_date ?? Carbon::now()->endOfWeek();

        $attendance = Attendance::whereBetween('date', [$startDate, $endDate])
            ->with('user')
            ->paginate(15);

        $stats = [
            'total_checked_in' => Attendance::whereBetween('date', [$startDate, $endDate])
                ->whereNotNull('check_in')->count(),
            'total_checked_out' => Attendance::whereBetween('date', [$startDate, $endDate])
                ->whereNotNull('check_out')->count(),
        ];

        return response()->json([
            'success' => true,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'statistics' => $stats,
            'data' => $attendance
        ]);
    }

    /**
     * Get monthly attendance report
     */
    public function monthly(Request $request)
    {
        $year = $request->year ?? Carbon::now()->year;
        $month = $request->month ?? Carbon::now()->month;

        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();

        $attendance = Attendance::whereBetween('date', [$startDate, $endDate])
            ->with('user')
            ->paginate(15);

        $stats = [
            'total_days' => $endDate->diffInDays($startDate),
            'total_checked_in' => Attendance::whereBetween('date', [$startDate, $endDate])
                ->whereNotNull('check_in')->count(),
            'total_checked_out' => Attendance::whereBetween('date', [$startDate, $endDate])
                ->whereNotNull('check_out')->count(),
        ];

        return response()->json([
            'success' => true,
            'year' => $year,
            'month' => $month,
            'statistics' => $stats,
            'data' => $attendance
        ]);
    }

    /**
     * Get employee attendance report
     */
    public function employeeReport(Request $request, $userId)
    {
        $user = User::findOrFail($userId);

        $startDate = $request->start_date ?? Carbon::now()->startOfMonth();
        $endDate = $request->end_date ?? Carbon::now()->endOfMonth();

        $attendance = Attendance::where('user_id', $userId)
            ->whereBetween('date', [$startDate, $endDate])
            ->paginate(15);

        $stats = [
            'total_present' => $attendance->count(),
            'total_absent' => Carbon::create($startDate)->diffInDays($endDate) - $attendance->count(),
            'on_time' => 0, // Implement based on your business logic
        ];

        return response()->json([
            'success' => true,
            'user' => $user,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'statistics' => $stats,
            'data' => $attendance
        ]);
    }

    /**
     * Get department attendance report
     */
    public function departmentReport(Request $request, $deptId)
    {
        $startDate = $request->start_date ?? Carbon::now()->startOfMonth();
        $endDate = $request->end_date ?? Carbon::now()->endOfMonth();

        $employees = User::where('department_id', $deptId)->get();
        $employeeIds = $employees->pluck('id');

        $attendance = Attendance::whereIn('user_id', $employeeIds)
            ->whereBetween('date', [$startDate, $endDate])
            ->with('user')
            ->paginate(15);

        $stats = [
            'total_employees' => $employees->count(),
            'total_present' => $attendance->count(),
            'total_absent' => ($employees->count() * $startDate->diffInDays($endDate)) - $attendance->count(),
        ];

        return response()->json([
            'success' => true,
            'department_id' => $deptId,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'statistics' => $stats,
            'data' => $attendance
        ]);
    }
}
