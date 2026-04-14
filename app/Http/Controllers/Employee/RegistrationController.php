<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Services\AuditLogger;

class RegistrationController extends Controller
{
    /**
     * Show the combined registration form
     */
    public function showForm()
    {
        $departments = Department::active()->get();
        return view('admin.employees.register', compact('departments'));
    }

    /**
     * Store the new employee and auto-login, then redirect to face enrollment
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'faculty_id' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'password' => 'required|string|min:8|confirmed',
            'terms' => 'required|accepted',
        ]);

        // Create the user
        $user = User::create([
            'name' => $validated['first_name'] . ' ' . $validated['last_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'employee',
            'faculty_id' => $validated['faculty_id'],
            'position' => $validated['position'],
            'department_id' => $validated['department_id'],
            'email_verified_at' => now(),
        ]);

        // Load the department relationship
        $user->load('department');

        // Auto-login the user
        Auth::login($user);

        // Log the employee registration
        AuditLogger::logCreate('User', $user->id, [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'faculty_id' => $user->faculty_id,
            'position' => $user->position,
            'department_id' => $user->department_id,
        ]);

        // Redirect to face enrollment
        return redirect()->route('employee.face_enrollment.show')->with('success', 'Registration successful! Please enroll your face.');
    }

    /**
     * Show the face enrollment form
     */
    public function showFaceEnrollment()
    {
        /** @var User $user */
        $user = Auth::user();
        return view('employee.face_enrollment', compact('user'));
    }

    /**
     * Save a face sample
     */
    public function saveSample(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'face_sample' => 'required|string',
        ]);

        // Decode base64 image
        $imageData = $validated['face_sample'];
        if (strpos($imageData, 'data:image') === 0) {
            $imageData = substr($imageData, strpos($imageData, ',') + 1);
        }

        // Store the sample
        $currentEncodings = json_decode($user->face_encodings ?? '[]', true);

        if (count($currentEncodings) < 10) {
            $currentEncodings[] = $imageData;
            $user->update([
                'face_encodings' => json_encode($currentEncodings),
                'face_samples_count' => count($currentEncodings),
            ]);

            return response()->json([
                'success' => true,
                'sample_count' => count($currentEncodings),
                'message' => count($currentEncodings) . ' sample(s) captured',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Maximum samples reached',
        ], 400);
    }

    /**
     * Complete face enrollment
     */
    public function complete(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        // Check if user has minimum required samples
        if ($user->face_samples_count >= 3) {
            $user->update(['face_enrolled' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Face enrollment completed successfully',
                'redirect' => route('employee.dashboard'),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Minimum 3 samples required',
        ], 400);
    }

    /**
     * Reset face enrollment session
     */
    public function reset(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $user->update([
            'face_encodings' => null,
            'face_samples_count' => 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Face enrollment reset',
        ]);
    }
}
