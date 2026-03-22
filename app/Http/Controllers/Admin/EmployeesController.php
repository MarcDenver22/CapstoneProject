<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Services\AuditLogger;

class EmployeesController extends Controller
{
    /**
     * Display a listing of all employees
     */
    public function index(Request $request)
    {
        $query = User::whereIn('role', ['employee', 'hr'])->with('department');

        // Search functionality
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('faculty_id', 'like', "%{$search}%")
                  ->orWhere('position', 'like', "%{$search}%");
            });
        }

        $employees = $query->paginate(10);

        // Calculate statistics (includes both employee and hr roles)
        $totalEmployees = User::whereIn('role', ['employee', 'hr'])->count();
        $faceEnrolledCount = User::whereIn('role', ['employee', 'hr'])->where('face_enrolled', true)->count();
        $pendingCount = $totalEmployees - $faceEnrolledCount;

        return view('admin.employees.list', compact(
            'employees',
            'totalEmployees',
            'faceEnrolledCount',
            'pendingCount'
        ));
    }

    /**
     * Display a specific employee details
     */
    public function show($id)
    {
        $employee = User::with('department')->findOrFail($id);

        // Verify it's an employee
        if ($employee->role !== 'employee') {
            return redirect()->route('admin.employees.list')->with('error', 'User is not an employee');
        }

        return view('admin.employees.show', compact('employee'));
    }

    /**
     * Delete an employee
     */
    public function destroy($id)
    {
        $employee = User::findOrFail($id);
        
        if ($employee->role === 'employee') {
            $employeeData = $employee->toArray();
            $employee->delete();

            // Log employee deletion
            AuditLogger::logDelete('Employee', $id, $employeeData);

            return redirect()->route('admin.employees.list')->with('success', 'Employee deleted successfully');
        }

        return redirect()->route('admin.employees.list')->with('error', 'Cannot delete non-employee user');
    }

    /**
     * Reset face enrollment for an employee
     */
    public function resetFaceEnrollment($id)
    {
        $employee = User::findOrFail($id);

        if ($employee->role === 'employee') {
            $employee->update([
                'face_enrolled' => false,
                'face_samples_count' => 0,
                'face_encodings' => null,
                'face_enrolled_at' => null,
            ]);

            // Log face enrollment reset
            AuditLogger::log('reset_face_enrollment', 'Employee', $id, [
                'employee_name' => $employee->name,
                'reset_at' => now(),
            ]);

            return redirect()->back()->with('success', 'Face enrollment reset successfully');
        }

        return redirect()->back()->with('error', 'Operation failed');
    }

    /**
     * Show form to create new employee
     */
    public function create()
    {
        $departments = Department::active()->get();
        return view('admin.employees.create', compact('departments'));
    }

    /**
     * Store a newly created employee
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'position' => 'nullable|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'faculty_id' => 'nullable|string|unique:users,faculty_id',
        ]);

        $validated['password'] = bcrypt($validated['password']);
        $validated['role'] = 'employee';

        $employee = User::create($validated);

        // Log employee creation
        AuditLogger::logCreate('Employee', $employee->id, [
            'name' => $employee->name,
            'email' => $employee->email,
            'position' => $employee->position,
            'faculty_id' => $employee->faculty_id,
        ]);

        return redirect()->route('admin.employees.list')->with('success', 'Employee created successfully');
    }

    /**
     * Show form to edit employee
     */
    public function edit($id)
    {
        $employee = User::with('department')->findOrFail($id);
        $departments = Department::active()->get();

        if ($employee->role !== 'employee') {
            return redirect()->route('admin.employees.list')->with('error', 'User is not an employee');
        }

        return view('admin.employees.edit', compact('employee', 'departments'));
    }

    /**
     * Update an existing employee
     */
    public function update(Request $request, $id)
    {
        $employee = User::findOrFail($id);

        if ($employee->role !== 'employee') {
            return redirect()->route('admin.employees.list')->with('error', 'User is not an employee');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'position' => 'nullable|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'faculty_id' => 'nullable|string|unique:users,faculty_id,' . $id,
        ]);

        // Track changes
        $changes = [];
        if ($employee->name !== $validated['name']) {
            $changes['name'] = ['from' => $employee->name, 'to' => $validated['name']];
        }
        if ($employee->email !== $validated['email']) {
            $changes['email'] = ['from' => $employee->email, 'to' => $validated['email']];
        }
        if ($employee->position !== $validated['position']) {
            $changes['position'] = ['from' => $employee->position, 'to' => $validated['position']];
        }
        if ($employee->department_id !== $validated['department_id']) {
            $changes['department_id'] = ['from' => $employee->department_id, 'to' => $validated['department_id']];
        }
        if ($employee->faculty_id !== $validated['faculty_id']) {
            $changes['faculty_id'] = ['from' => $employee->faculty_id, 'to' => $validated['faculty_id']];
        }

        $employee->update($validated);

        // Log employee update if there are changes
        if (!empty($changes)) {
            AuditLogger::logUpdate('Employee', $id, $changes);
        }

        return redirect()->route('admin.employees.show', $id)->with('success', 'Employee updated successfully');
    }
}
