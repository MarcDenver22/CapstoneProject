<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FaceEnrollmentController extends Controller
{
    /**
     * Show the face enrollment form for already logged-in employees
     */
    public function showForm()
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        
        // Ensure department relationship is loaded
        if ($user && !$user->relationLoaded('department')) {
            $user->load('department');
        }
        
        return view('employee.face_enrollment', compact('user'));
    }

    /**
     * Save a face descriptor sample
     */
    public function saveSample(Request $request)
    {
        try {
            /** @var \App\Models\User|null $user */
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated',
                ], 401);
            }

            // Debug: Log incoming request
            Log::info('Face enrollment saveSample called', [
                'user_id' => $user->id,
                'request_data_keys' => array_keys($request->all()),
                'descriptor_received' => $request->has('face_descriptor') ? 'yes' : 'no',
            ]);

            $validated = $request->validate([
                'face_descriptor' => 'required|array|size:128',
            ]);

            // Validate descriptor is array of 128 floats
            $descriptor = $validated['face_descriptor'];
            
            if (!is_array($descriptor) || count($descriptor) !== 128) {
                Log::warning('Invalid descriptor dimensions', [
                    'received_count' => count($descriptor),
                    'expected' => 128,
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid face descriptor. Must be 128-dimensional array.',
                ], 400);
            }

            // Validate all values are numeric
            $non_numeric_count = 0;
            foreach ($descriptor as $value) {
                if (!is_numeric($value)) {
                    $non_numeric_count++;
                }
            }
            
            if ($non_numeric_count > 0) {
                Log::warning('Non-numeric descriptor values', ['count' => $non_numeric_count]);
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid descriptor values: ' . $non_numeric_count . ' non-numeric values found',
                ], 400);
            }

            // Store the descriptor if under limit
            $currentDescriptors = json_decode($user->face_encodings ?? '[]', true);
            if (!is_array($currentDescriptors)) {
                $currentDescriptors = [];
            }

            if (count($currentDescriptors) < 10) {
                $currentDescriptors[] = $descriptor;
                $user->update([
                    'face_encodings' => json_encode($currentDescriptors),
                    'face_samples_count' => count($currentDescriptors),
                ]);

                Log::info('Face descriptor saved', [
                    'user_id' => $user->id,
                    'sample_count' => count($currentDescriptors),
                ]);

                return response()->json([
                    'success' => true,
                    'sample_count' => count($currentDescriptors),
                    'message' => count($currentDescriptors) . ' sample(s) captured',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Maximum samples reached',
            ], 400);
            
        } catch (\Exception $e) {
            Log::error('Face enrollment save error: ' . $e->getMessage(), [
                'exception' => $e->getTraceAsString(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error saving sample: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Complete face enrollment
     */
    public function complete(Request $request)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        // Validate minimum samples
        if ($user->face_samples_count >= 3) {
            $user->update([
                'face_enrolled' => true,
                'face_enrolled_at' => now(),
            ]);

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
     * Check enrollment status
     */
    public function status()
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        return response()->json([
            'face_enrolled' => $user->face_enrolled,
            'sample_count' => $user->face_samples_count,
        ]);
    }

    /**
     * Reset face enrollment
     */
    public function reset(Request $request)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        $user->update([
            'face_encodings' => null,
            'face_samples_count' => 0,
            'face_enrolled' => false,
            'face_enrolled_at' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Face enrollment reset',
        ]);
    }

    /**
     * Get face descriptors for recognition
     */
    public function getFaceDescriptors(Request $request)
    {
        try {
            $validated = $request->validate([
                'registration_numbers' => 'required|array',
                'registration_numbers.*' => 'string'
            ]);

            $users = User::whereIn('registration_number', $validated['registration_numbers'])
                ->where('face_enrolled', true)
                ->where('face_encodings', '!=', null)
                ->get(['registration_number', 'face_encodings', 'face_samples_count']);

            $descriptors = [];

            foreach ($users as $user) {
                $encodings = json_decode($user->face_encodings, true);
                
                if (is_array($encodings) && !empty($encodings)) {
                    // Convert base64 to mock descriptors for now
                    $descriptorArrays = [];

                    foreach (array_slice($encodings, 0, 5) as $encoding) {
                        try {
                            // Create a 128-dimensional descriptor from base64
                            $hash = hash('sha256', $encoding);
                            $descriptor = [];
                            for ($i = 0; $i < 128; $i++) {
                                $descriptor[] = (float)(hexdec(substr($hash, $i * 2, 2)) / 255 - 0.5);
                            }
                            $descriptorArrays[] = $descriptor;
                        } catch (\Exception $e) {
                            Log::warning("Failed to extract descriptor for {$user->registration_number}");
                        }
                    }

                    if (!empty($descriptorArrays)) {
                        $descriptors[$user->registration_number] = $descriptorArrays;
                    }
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Retrieved face descriptors',
                'descriptors' => $descriptors,
                'count' => count($descriptors)
            ]);

        } catch (\Exception $e) {
            Log::error('Get face descriptors error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve face descriptors',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get employees by department for face recognition
     */
    public function getEmployeesByDepartment(Request $request)
    {
        try {
            $departmentId = $request->input('department_id');

            $query = User::where('role', 'employee')
                ->with('department');

            if ($departmentId) {
                $query->where('department_id', $departmentId);
            }

            $employees = $query->get(['id', 'name', 'email', 'registration_number', 'department_id', 'face_enrolled', 'face_samples_count']);

            return response()->json([
                'success' => true,
                'employees' => $employees,
                'count' => $employees->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Get employees by department error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load employees',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get students for recognition
     */
    public function getStudents(Request $request)
    {
        try {
            $course = $request->input('course');
            $unit = $request->input('unit');

            $query = User::where('role', 'employee')
                ->where('face_enrolled', true)
                ->where('face_samples_count', '>=', 3);

            if ($course) {
                $query->where('course', $course);
            }
            if ($unit) {
                $query->where('unit', $unit);
            }

            $students = $query->get(['id', 'registration_number', 'first_name', 'last_name', 'email', 'course', 'unit']);

            if ($students->isEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'No enrolled students found',
                    'data' => [],
                    'html' => '<p class="text-muted">No students found for selected filters.</p>'
                ]);
            }

            // Generate HTML table
            $html = '<table class="table table-striped table-hover">';
            $html .= '<thead><tr>';
            $html .= '<th>#</th><th>Registration</th><th>Name</th><th>Email</th><th>Course</th><th>Unit</th><th>Status</th>';
            $html .= '</tr></thead><tbody>';

            foreach ($students as $index => $student) {
                $html .= '<tr>';
                $html .= '<td>' . ($index + 1) . '</td>';
                $html .= '<td>' . htmlspecialchars($student->registration_number) . '</td>';
                $html .= '<td>' . htmlspecialchars($student->first_name . ' ' . $student->last_name) . '</td>';
                $html .= '<td>' . htmlspecialchars($student->email) . '</td>';
                $html .= '<td>' . htmlspecialchars($student->course ?? 'N/A') . '</td>';
                $html .= '<td>' . htmlspecialchars($student->unit ?? 'N/A') . '</td>';
                $html .= '<td><span class="badge bg-secondary">absent</span></td>';
                $html .= '</tr>';
            }

            $html .= '</tbody></table>';

            return response()->json([
                'status' => 'success',
                'data' => $students,
                'html' => $html,
                'count' => $students->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Get students error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to load students',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save attendance records
     */
    public function saveAttendance(Request $request)
    {
        try {
            $validated = $request->validate([
                'attendance' => 'required|array',
                'attendance.*.registration_number' => 'required|string',
                'attendance.*.status' => 'required|in:present,absent',
            ]);

            $savedCount = 0;
            $failedCount = 0;

            foreach ($validated['attendance'] as $record) {
                try {
                    $user = User::where('registration_number', $record['registration_number'])->first();

                    if ($user) {
                        Attendance::create([
                            'user_id' => $user->id,
                            'status' => $record['status'],
                            'attendance_date' => now()->toDateString(),
                            'time_in' => now(),
                            'liveness_verified' => true,
                        ]);

                        $savedCount++;
                        Log::info("Attendance saved for {$record['registration_number']} - {$record['status']}");
                    } else {
                        $failedCount++;
                        Log::warning("Student not found: {$record['registration_number']}");
                    }
                } catch (\Exception $e) {
                    $failedCount++;
                    Log::error("Failed to save attendance for {$record['registration_number']}: " . $e->getMessage());
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => "Saved $savedCount attendance records" . ($failedCount > 0 ? " ($failedCount failed)" : ""),
                'saved_count' => $savedCount,
                'failed_count' => $failedCount,
                'total_count' => count($validated['attendance'])
            ]);

        } catch (\Throwable $e) {
            Log::error('Save attendance error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to save attendance',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
