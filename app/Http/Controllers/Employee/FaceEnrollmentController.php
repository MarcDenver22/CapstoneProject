<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
     * Save a face sample
     */
    public function saveSample(Request $request)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        $validated = $request->validate([
            'face_sample' => 'required|string',
        ]);

        // Decode base64 image
        $imageData = $validated['face_sample'];
        if (strpos($imageData, 'data:image') === 0) {
            $imageData = substr($imageData, strpos($imageData, ',') + 1);
        }

        // Store the sample if under limit
        $currentEncodings = json_decode($user->face_encodings ?? '[]', true);

        if (count($currentEncodings) < 5) {
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
}
