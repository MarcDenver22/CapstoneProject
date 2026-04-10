<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;

class KioskController extends Controller
{
    /**
     * Show the kiosk main page (redirect to attendance).
     */
    public function index()
    {
        // Check if kiosk has been unlocked
        if (request()->cookie('kiosk_unlocked') !== 'true') {
            return redirect()->route('kiosk.unlock');
        }

        // Redirect to the new camera-based face scan system
        return redirect()->route('kiosk.scan');
    }

    /**
     * Show the kiosk PIN unlock page.
     */
    public function showUnlock()
    {
        return view('kiosk.unlock');
    }

    /**
     * Verify the kiosk PIN and unlock the session.
     */
    public function verifyPin(Request $request)
    {
        $validated = $request->validate([
            'pin' => 'required|string|min:1',
        ]);

        $pin = trim($validated['pin']);
        $correct_pin = env('KIOSK_PIN', null);

        // If no PIN is configured, unlock automatically
        if ($correct_pin === null) {
            return redirect()->route('kiosk')
                ->cookie(Cookie::make('kiosk_unlocked', 'true', 60));
        }

        // Verify the PIN
        if ($pin === $correct_pin) {
            Log::info('Kiosk PIN verified successfully');
            return redirect()->route('kiosk')
                ->cookie(Cookie::make('kiosk_unlocked', 'true', 60));
        }

        Log::warning('Invalid Kiosk PIN attempt', [
            'provided' => $pin,
            'expected' => $correct_pin,
        ]);
        return back()->with('error', 'Invalid PIN. Please try again.');
    }

    /**
     * Lock the kiosk (for testing purposes).
     */
    public function logout()
    {
        return redirect()->route('landing')
            ->cookie(Cookie::forget('kiosk_unlocked'));
    }

    /**
     * Debug: Show current PIN configuration (remove this route in production)
     */
    public function debugInfo()
    {
        return response()->json([
            'configured_pin' => env('KIOSK_PIN'),
            'allowed_ips' => env('KIOSK_ALLOWED_IPS'),
            'visitor_ip' => request()->ip(),
        ]);
    }
}

