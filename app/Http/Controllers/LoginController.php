<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\AuditLogger;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Log the login action
            AuditLogger::log('login', 'User', Auth::id(), [
                'email' => Auth::user()->email,
                'role' => Auth::user()->role,
            ]);

            if (Auth::user()->role === 'employee') {
                return redirect()->route('employee.dashboard');
            }

            if (Auth::user()->role === 'hr') {
                return redirect()->route('hr.dashboard');
            }

            if (Auth::user()->role === 'super_admin') {
                return redirect()->route('super_admin.dashboard');
            }

            return redirect()->route('dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        // Log the logout action before destroying the session
        if (Auth::check()) {
            AuditLogger::log('logout', 'User', Auth::id(), [
                'email' => Auth::user()->email,
                'role' => Auth::user()->role,
            ]);
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
