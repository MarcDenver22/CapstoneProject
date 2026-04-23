<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    /**
     * Get all employees
     */
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        if ($request->has('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        $employees = $query->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => $employees
        ]);
    }

    /**
     * Get single employee
     */
    public function show($id)
    {
        $employee = User::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $employee
        ]);
    }

    /**
     * Create employee
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'role' => 'required|in:admin,employee,hr,super_admin',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        $employee = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
            'department_id' => $request->department_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Employee created successfully',
            'data' => $employee
        ], 201);
    }

    /**
     * Update employee
     */
    public function update(Request $request, $id)
    {
        $employee = User::findOrFail($id);

        $request->validate([
            'name' => 'nullable|string',
            'email' => 'nullable|email|unique:users,email,' . $id,
            'role' => 'nullable|in:admin,employee,hr,super_admin',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        $employee->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Employee updated successfully',
            'data' => $employee
        ]);
    }

    /**
     * Delete employee
     */
    public function destroy($id)
    {
        $employee = User::findOrFail($id);
        $employee->delete();

        return response()->json([
            'success' => true,
            'message' => 'Employee deleted successfully'
        ]);
    }

    /**
     * Search employees
     */
    public function search($query)
    {
        // Validate the search query parameter
        if (strlen($query) > 100) {
            return response()->json([
                'success' => false,
                'message' => 'Search query cannot exceed 100 characters'
            ], 422);
        }
        
        if (strlen($query) < 1) {
            return response()->json([
                'success' => false,
                'message' => 'Search query must be at least 1 character'
            ], 422);
        }

        $employees = User::where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->paginate(15);

        return response()->json([
            'success' => true,
            'query' => $query,
            'data' => $employees
        ]);
    }

    /**
     * Get employees by department
     */
    public function byDepartment($deptId)
    {
        $employees = User::where('department_id', $deptId)->paginate(15);

        return response()->json([
            'success' => true,
            'department_id' => $deptId,
            'data' => $employees
        ]);
    }
}
