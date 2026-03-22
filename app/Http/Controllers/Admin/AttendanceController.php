<?php

namespace App\Http\Controllers\Admin;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AttendanceController extends Controller
{
    /**
     * Display a listing of attendance records.
     */
    public function index()
    {
        $attendances = Attendance::with('user')
            ->latest('attendance_date')
            ->paginate(20);
        
        return view('admin.attendance.index', compact('attendances'));
    }

    /**
     * Show the form for creating a new attendance record.
     */
    public function create()
    {
        $employees = User::where('role', 'employee')->get();
        return view('admin.attendance.create', compact('employees'));
    }

    /**
     * Store a newly created attendance record in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'attendance_date' => 'required|date',
            'time_in' => 'nullable|date_format:H:i',
            'time_out' => 'nullable|date_format:H:i',
            'status' => 'required|in:present,absent,late,half_day,leave',
            'notes' => 'nullable|string|max:500',
            'liveness_verified' => 'boolean',
        ]);

        Attendance::create($validated);

        return redirect()->route('admin.attendance.index')->with('success', 'Attendance record created successfully!');
    }

    /**
     * Display the specified attendance record.
     */
    public function show(Attendance $attendance)
    {
        return view('admin.attendance.show', compact('attendance'));
    }

    /**
     * Show the form for editing the specified attendance record.
     */
    public function edit(Attendance $attendance)
    {
        $employees = User::where('role', 'employee')->get();
        return view('admin.attendance.edit', compact('attendance', 'employees'));
    }

    /**
     * Update the specified attendance record in storage.
     */
    public function update(Request $request, Attendance $attendance)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'attendance_date' => 'required|date',
            'time_in' => 'nullable|date_format:H:i',
            'time_out' => 'nullable|date_format:H:i',
            'status' => 'required|in:present,absent,late,half_day,leave',
            'notes' => 'nullable|string|max:500',
            'liveness_verified' => 'boolean',
        ]);

        $attendance->update($validated);

        return redirect()->route('admin.attendance.index')->with('success', 'Attendance record updated successfully!');
    }

    /**
     * Remove the specified attendance record from storage.
     */
    public function destroy(Attendance $attendance)
    {
        $attendance->delete();
        return redirect()->route('admin.attendance.index')->with('success', 'Attendance record deleted successfully!');
    }

    /**
     * Display today's attendance records.
     */
    public function today()
    {
        $today = now()->toDateString();
        
        $attendanceRecords = Attendance::with('user')
            ->where('attendance_date', $today)
            ->latest('time_in')
            ->get();
        
        $todayStats = [
            'present' => Attendance::where('attendance_date', $today)->where('status', 'present')->count(),
            'late' => Attendance::where('attendance_date', $today)->where('status', 'late')->count(),
            'absent' => Attendance::where('attendance_date', $today)->where('status', 'absent')->count(),
            'verified' => Attendance::where('attendance_date', $today)->where('liveness_verified', true)->count(),
        ];
        
        return view('admin.attendance.today', compact('attendanceRecords', 'todayStats'));
    }
}
