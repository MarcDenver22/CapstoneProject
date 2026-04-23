<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use App\Http\Requests\Api\GetAttendanceRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    /**
     * Get all attendance records (with pagination)
     */
    public function index(GetAttendanceRequest $request)
    {
        $query = Attendance::query();

        // Filter by date range
        if ($request->has('from_date')) {
            $query->whereDate('date', '>=', $request->from_date);
        }
        if ($request->has('to_date')) {
            $query->whereDate('date', '<=', $request->to_date);
        }

        // Filter by user
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $attendances = $query->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => $attendances
        ]);
    }

    /**
     * Get single attendance record
     */
    public function show($id)
    {
        $attendance = Attendance::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $attendance
        ]);
    }

    /**
     * Create attendance record (check-in)
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
        ]);

        $attendance = Attendance::create([
            'user_id' => $request->user_id,
            'date' => $request->date,
            'check_in' => $request->check_in ?? now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Attendance check-in recorded',
            'data' => $attendance
        ], 201);
    }

    /**
     * Update attendance record
     */
    public function update(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);

        $request->validate([
            'check_in' => 'nullable|date_format:Y-m-d H:i:s',
            'check_out' => 'nullable|date_format:Y-m-d H:i:s',
        ]);

        $attendance->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Attendance updated',
            'data' => $attendance
        ]);
    }

    /**
     * Delete attendance record
     */
    public function destroy($id)
    {
        $attendance = Attendance::findOrFail($id);
        $attendance->delete();

        return response()->json([
            'success' => true,
            'message' => 'Attendance deleted'
        ]);
    }

    /**
     * Get attendance for specific user
     */
    public function userAttendance(Request $request, $userId)
    {
        $user = User::findOrFail($userId);

        $query = Attendance::where('user_id', $userId);

        if ($request->has('from_date')) {
            $query->whereDate('date', '>=', $request->from_date);
        }
        if ($request->has('to_date')) {
            $query->whereDate('date', '<=', $request->to_date);
        }

        $attendances = $query->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'user' => $user,
            'data' => $attendances
        ]);
    }

    /**
     * Get attendance for specific date
     */
    public function dateAttendance($date)
    {
        $attendances = Attendance::whereDate('date', $date)->paginate(15);

        return response()->json([
            'success' => true,
            'date' => $date,
            'data' => $attendances
        ]);
    }

    /**
     * Check-out from attendance
     */
    public function checkout(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);

        $attendance->update([
            'check_out' => $request->check_out ?? now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Check-out recorded',
            'data' => $attendance
        ]);
    }

    /**
     * Face recognition endpoint (public)
     */
    public function recognize(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|max:5120',
        ]);

        try {
            $photo = $request->file('photo');
            $photoPath = $photo->store('attendance-photos', 'public');

            // TODO: Integrate with actual face recognition service
            // For now, uses first user as placeholder
            
            $user = User::first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'No employees found in the system.'
                ], 404);
            }

            $existingAttendance = Attendance::where('user_id', $user->id)
                ->whereDate('date', Carbon::today())
                ->first();

            if ($existingAttendance) {
                $existingAttendance->update([
                    'check_out' => now(),
                ]);

                return response()->json([
                    'success' => true,
                    'name' => $user->name,
                    'message' => 'Check-out recorded successfully!',
                    'type' => 'checkout',
                    'data' => $existingAttendance
                ]);
            } else {
                $attendance = Attendance::create([
                    'user_id' => $user->id,
                    'date' => Carbon::today(),
                    'check_in' => now(),
                    'photo_path' => $photoPath,
                ]);

                return response()->json([
                    'success' => true,
                    'name' => $user->name,
                    'message' => 'Check-in recorded successfully!',
                    'type' => 'checkin',
                    'data' => $attendance
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing face recognition: ' . $e->getMessage()
            ], 500);
        }
    }
}
