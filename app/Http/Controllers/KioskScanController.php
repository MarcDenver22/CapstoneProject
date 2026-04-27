<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceLog;
use App\Models\AuditLog;
use App\Models\User;
use App\Services\FaceRecognitionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class KioskScanController extends Controller
{
    protected FaceRecognitionService $faceRecognition;

    public function __construct(FaceRecognitionService $faceRecognition)
    {
        $this->faceRecognition = $faceRecognition;
    }

    /**
     * Show the kiosk scan page
     */
    public function index()
    {
        return view('kiosk.scan');
    }

    /**
     * Find and set user by employee ID (faculty_id)
     * This allows the kiosk to identify which employee is scanning
     */
    public function findUser(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'employee_id' => 'required|string',
            ]);

            $user = User::where('faculty_id', $validated['employee_id'])->first();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Employee ID not found. Check your ID.',
                ], 404);
            }

            if (!$user->face_enrolled) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You have not completed face enrollment. Please enroll first.',
                ], 400);
            }

            // Set the user in session for the scan process
            session(['kiosk_user' => $user]);

            // Return user info with faculty_id
            return response()->json([
                'status' => 'success',
                'user_id' => $user->id,
                'name' => $user->name,
                'message' => 'User found. Please look at the camera.',
            ]);

        } catch (\Exception $e) {
            Log::error('Find user error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Error finding user: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get current user's face descriptors for client-side matching
     */
    public function getUserDescriptor(Request $request): JsonResponse
    {
        try {
            // Get user from session or auth
            $user = session('kiosk_user');
            
            if (!$user || !$user instanceof User) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No user authenticated'
                ], 401);
            }

            // Check if user has face enrollment
            if (!$user->face_enrolled || !$user->face_encodings) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User has not completed face enrollment'
                ], 400);
            }

            // Get face descriptors (already 128-dimensional arrays)
            $descriptors = json_decode($user->face_encodings, true);
            if (!is_array($descriptors) || empty($descriptors)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No face data found'
                ], 400);
            }

            // Average all descriptors for more robust matching
            // This improves accuracy across different lighting/angles
            $averageDescriptor = $this->averageDescriptors($descriptors);

            // Return face descriptors with faculty_id
            return response()->json([
                'status' => 'success',
                'employee_id' => $user->faculty_id,
                'name' => $user->name,
                'descriptor' => $averageDescriptor,
                'sample_count' => count($descriptors)
            ]);

        } catch (\Exception $e) {
            Log::error('Get descriptor error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to load descriptor'
            ], 500);
        }
    }

    /**
     * Average multiple face descriptors for more robust matching
     */
    private function averageDescriptors(array $descriptors): array
    {
        if (empty($descriptors)) {
            return [];
        }

        // Initialize sum array with 128 zeros
        $sum = array_fill(0, 128, 0);

        // Sum all descriptors
        foreach ($descriptors as $descriptor) {
            if (is_array($descriptor) && count($descriptor) === 128) {
                for ($i = 0; $i < 128; $i++) {
                    $sum[$i] += (float)$descriptor[$i];
                }
            }
        }

        // Calculate average
        $count = count($descriptors);
        for ($i = 0; $i < 128; $i++) {
            $sum[$i] = $sum[$i] / $count;
        }

        return $sum;
    }

    /**
     * Extract descriptor from base64 encoded face image (DEPRECATED)
     */
    private function extractDescriptorFromBase64($base64Data): array
    {
        // This method is deprecated. Descriptors are now extracted client-side.
        // Keeping for backward compatibility only.
        // Generate descriptor from base64 hash
        $hash = hash('sha256', $base64Data);
        $descriptor = [];
        
        for ($i = 0; $i < 128; $i++) {
            $hexPair = substr($hash, $i * 2, 2);
            $value = (hexdec($hexPair) / 255) * 2 - 1; // Convert to -1 to 1 range
            $descriptor[] = (float)$value;
        }
        
        return $descriptor;
    }

    /**
     * Record attendance based on face descriptor match
     */
    public function scan(Request $request): JsonResponse
    {
        try {
            // Validate face descriptor from client
            $validated = $request->validate([
                'face_descriptor' => 'required|array|size:128',
                'user_id' => 'required|integer',
            ]);

            // Get the user from session (they already logged in with employee ID)
            $user = User::find($validated['user_id']);
            if (!$user) {
                Log::error('Kiosk scan: User not found', ['user_id' => $validated['user_id']]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not found',
                ], 404);
            }

            // Verify user is enrolled
            if (!$user->face_enrolled) {
                Log::warning('Kiosk scan: User not enrolled', ['user_id' => $user->id, 'name' => $user->name]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not enrolled for face recognition',
                ], 400);
            }

            // Get enrolled descriptors
            $enrolledDescriptors = json_decode($user->face_encodings, true);
            if (!is_array($enrolledDescriptors) || empty($enrolledDescriptors)) {
                Log::warning('Kiosk scan: No face data found', ['user_id' => $user->id]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'No face data found for user',
                ], 400);
            }

            // Convert descriptor array to proper format
            $liveDescriptor = array_map('floatval', $validated['face_descriptor']);
            
            // Log the descriptor info for debugging
            Log::info('Kiosk scan: Received descriptor', [
                'user_id' => $user->id,
                'descriptor_length' => count($liveDescriptor),
                'first_5_values' => array_slice($liveDescriptor, 0, 5),
                'descriptor_type' => gettype($liveDescriptor[0]),
            ]);

            // Calculate distance between live descriptor and enrolled descriptors
            $minDistance = PHP_FLOAT_MAX;
            $distances = [];
            foreach ($enrolledDescriptors as $idx => $enrolled) {
                if (!is_array($enrolled)) {
                    continue;
                }
                $enrolled = array_map('floatval', $enrolled);
                $distance = $this->euclideanDistance($liveDescriptor, $enrolled);
                $distances[] = $distance;
                if ($distance < $minDistance) {
                    $minDistance = $distance;
                }
            }

            // Euclidean distance threshold: < 0.6 is typically a good match
            $threshold = 0.6;
            $confidence = 1 - min($minDistance / 1.0, 1.0); // Convert distance to confidence score

            Log::info('Face matching result', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'min_distance' => number_format($minDistance, 4),
                'avg_distance' => number_format(array_sum($distances) / count($distances), 4),
                'all_distances' => array_map(fn($d) => number_format($d, 4), $distances),
                'confidence' => number_format($confidence, 4),
                'threshold' => $threshold,
                'matched' => $minDistance < $threshold,
            ]);

            if ($minDistance >= $threshold) {
                $this->logAuditTrail($user->id, 'scan_failed', 'Face not matched (distance: ' . round($minDistance, 4) . ')', $request->ip());
                Log::warning('Kiosk scan: Face not matched', [
                    'user_id' => $user->id,
                    'min_distance' => $minDistance,
                    'threshold' => $threshold,
                ]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Face not recognized. Please try again.',
                ], 400);
            }

            // Face matched! Record attendance
            $now = Carbon::now();
            $hour = $now->hour;

            // Determine punch type based on attendance state
            $punchType = $this->determinePunchType($user->id, $now);

            // Get today's attendance record for state-based period detection
            $dtr = Attendance::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'attendance_date' => $now->toDateString(),
                ],
                [
                    'status' => 'present',
                    'liveness_verified' => true,
                ]
            );

            // Determine period based on attendance state (state-based detection)
            // 1st IN → AM, 1st OUT → AM, 2nd IN → PM, 2nd OUT → PM
            // EXCEPTION: If it's past 12 PM and both AM fields are null, they skipped AM → use PM
            if ($hour >= 12 && !$dtr->am_arrival && !$dtr->am_departure) {
                // Past 12 PM with no AM records = employee is absent in AM, coming for PM
                $period = 'PM';
            } elseif (!$dtr->am_arrival) {
                // AM arrival not set = first punch = AM period
                $period = 'AM';
            } elseif (!$dtr->am_departure) {
                // AM departure not set = second punch = AM period
                $period = 'AM';
            } elseif (!$dtr->pm_arrival) {
                // PM arrival not set = third punch = PM period
                $period = 'PM';
            } else {
                // PM period (either arriving or departing)
                $period = 'PM';
            }

            // Check if employee already punched in/out for this period
            $lastLog = AttendanceLog::where('employee_id', $user->id)
                ->where('log_date', $now->toDateString())
                ->where('period', $period)
                ->orderBy('punched_at', 'desc')
                ->first();

            // Save attendance log
            $log = AttendanceLog::create([
                'employee_id' => $user->id,
                'log_date' => $now->toDateString(),
                'period' => $period,
                'punch_type' => $punchType,
                'punched_at' => $now,
                'method' => 'face_recognition',
                'confidence' => round($confidence, 4),
                'liveness_passed' => true,
                'notes' => 'Kiosk face scan - ' . strtoupper($punchType),
            ]);

            // Update period-specific times
            if ($period === 'AM') {
                if ($punchType === 'IN') {
                    // Morning arrival
                    if (!$dtr->am_arrival) {
                        $dtr->am_arrival = $now;
                    }
                } else {
                    // Lunch departure (AM departure) - even if it's 12:10 PM
                    $dtr->am_departure = $now;
                }
            } else {
                // PM period
                if ($punchType === 'IN') {
                    // Afternoon arrival
                    if (!$dtr->pm_arrival) {
                        $dtr->pm_arrival = $now;
                    }
                } else {
                    // End of day departure (PM departure)
                    $dtr->pm_departure = $now;
                }
            }

            // Keep time_in and time_out for backward compatibility
            // time_in = earliest arrival (am_arrival)
            if (!$dtr->time_in && $dtr->am_arrival) {
                $dtr->time_in = $dtr->am_arrival;
            }
            
            // time_out = latest departure (pm_departure, or am_departure if no pm_departure)
            if ($dtr->pm_departure) {
                $dtr->time_out = $dtr->pm_departure;
            } elseif ($dtr->am_departure) {
                $dtr->time_out = $dtr->am_departure;
            }

            $dtr->save();

            // Log audit trail
            $this->logAuditTrail(
                $user->id,
                'scan_success',
                "Face scan: {$period} {$punchType} (distance: {$minDistance}, confidence: {$confidence})",
                $request->ip()
            );

            return response()->json([
                'status' => 'success',
                'message' => "Attendance recorded for {$user->name}",
                'name' => $user->name,
                'user_id' => $user->id,
                'period' => $period,
                'punch_type' => $punchType,
                'confidence' => round($confidence, 4),
                'distance' => round($minDistance, 4),
                'time_in' => $dtr->time_in ? optional($dtr->time_in)->format('h:i A') : null,
                'time_out' => $dtr->time_out ? optional($dtr->time_out)->format('h:i A') : null,
                'attendance_date' => $dtr->attendance_date,
            ]);

        } catch (\Exception $e) {
            Log::error('Kiosk scan error: ' . $e->getMessage(), [
                'exception' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Error during face matching: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Calculate Euclidean distance between two 128-d descriptors
     */
    private function euclideanDistance(array $descriptor1, array $descriptor2): float
    {
        if (count($descriptor1) !== count($descriptor2)) {
            return PHP_FLOAT_MAX;
        }

        $sum = 0.0;
        for ($i = 0; $i < count($descriptor1); $i++) {
            $diff = $descriptor1[$i] - $descriptor2[$i];
            $sum += $diff * $diff;
        }

        return sqrt($sum);
    }

    /**
     * Log an audit trail entry
     */
    private function logAuditTrail(?int $employeeId, string $action, string $details, string $ipAddress): void
    {
        try {
            AuditLog::create([
                'user_id' => $employeeId,
                'action' => $action,
                'ip_address' => $ipAddress,
                'changes' => ['details' => $details],
            ]);
        } catch (\Exception $e) {
            Log::warning('Failed to log audit trail: ' . $e->getMessage());
        }
    }

    /**
     * DEBUG: Test a descriptor against enrolled samples
     */
    public function testDescriptor(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'employee_id' => 'required|string',
                'face_descriptor' => 'required|array|size:128',
            ]);

            $user = User::where('employee_id', $validated['employee_id'])->first();
            if (!$user || !$user->face_enrolled) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not found or not enrolled',
                ]);
            }

            $enrolledDescriptors = json_decode($user->face_encodings, true);
            $liveDescriptor = array_map('floatval', $validated['face_descriptor']);
            
            $minDistance = PHP_FLOAT_MAX;
            $distances = [];
            
            foreach ($enrolledDescriptors as $enrolled) {
                $enrolled = array_map('floatval', $enrolled);
                $distance = $this->euclideanDistance($liveDescriptor, $enrolled);
                $distances[] = round($distance, 4);
                if ($distance < $minDistance) {
                    $minDistance = $distance;
                }
            }

            $threshold = 0.6;
            $matched = $minDistance < $threshold;
            
            return response()->json([
                'status' => 'success',
                'employee_name' => $user->name,
                'matched' => $matched,
                'min_distance' => round($minDistance, 4),
                'avg_distance' => round(array_sum($distances) / count($distances), 4),
                'distances' => $distances,
                'threshold' => $threshold,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * DEBUG: View stored descriptors for an employee
     */
    public function viewDescriptors(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'employee_id' => 'required|string',
            ]);

            $user = User::where('employee_id', $validated['employee_id'])->first();
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not found',
                ]);
            }

            $descriptors = [];
            if ($user->face_enrolled) {
                $encodings = json_decode($user->face_encodings, true);
                foreach ($encodings as $encoding) {
                    $descriptors[] = array_map('floatval', $encoding);
                }
            }

            return response()->json([
                'status' => 'success',
                'employee_name' => $user->name,
                'face_enrolled' => $user->face_enrolled,
                'count' => count($descriptors),
                'descriptors' => $descriptors,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Determine the punch type (IN or OUT) based on attendance state
     * State-based: Checks which period fields are populated in today's record
     */
    private function determinePunchType(int $userId, Carbon $now): string
    {
        $today = $now->toDateString();
        
        // Get today's attendance record
        $attendance = Attendance::where('user_id', $userId)
            ->where('attendance_date', $today)
            ->first();
        
        if (!$attendance) {
            // No record today = first punch = IN
            return 'IN';
        }
        
        // Check state: which period fields are populated?
        if (!$attendance->am_arrival) {
            // AM arrival not set = first punch = IN
            return 'IN';
        } elseif (!$attendance->am_departure) {
            // AM arrival exists but no departure = next punch = OUT
            return 'OUT';
        } elseif (!$attendance->pm_arrival) {
            // AM complete, PM arrival not set = next punch = IN
            return 'IN';
        } elseif (!$attendance->pm_departure) {
            // PM arrival exists but no departure = next punch = OUT
            return 'OUT';
        }
        
        // All periods filled = no more punches today
        return 'OUT';
    }

    /**
     * Determine the period (AM or PM) based on attendance state
     * State-based: Looks at which fields are already populated
     * 
     * Logic:
     * - If punch_type is IN: Determine which period to start
     * - If punch_type is OUT: Determine which period to complete
     */
    private function determinePeriod(int $hour, int $minute, string $punchType): string
    {
        // For state-based detection, we need the Attendance record
        // This is a helper, actual period determination happens in scan() method
        // Default to AM for first punches, PM for later ones
        return $hour < 12 ? 'AM' : 'PM';
    }
}
