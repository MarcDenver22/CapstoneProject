<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Department;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        /** @var \App\Models\User|null $user */
        $user = $request->user();
        
        // Load department and fetch all active departments for dropdown (all roles)
        if ($user) {
            $user->load('department');
            $departments = Department::on('supabase')->where('is_active', true)->get();
            return view('profile.edit', [
                'user' => $user,
                'departments' => $departments,
            ]);
        }
        
        return view('profile.edit', [
            'user' => $user,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        /** @var \App\Models\User|null $user */
        $user = $request->user();
        
        // Build validation rules - allow all roles to update position and department
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . ($user?->id ?? 'NULL'),
            'position' => 'nullable|string|max:255',
            'department_id' => 'nullable|exists:supabase.departments,id',
        ];
        
        $validated = $request->validate($rules);
        
        // Update user profile
        if ($user) {
            $user->fill($validated);

            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }

            $user->save();
        }

        if ($user && $user->role === 'hr') {
            $redirectRoute = 'hr.profile.edit';
        } elseif ($user && $user->role === 'admin') {
            $redirectRoute = 'admin.profile.edit';
        } else {
            $redirectRoute = 'profile.edit';
        }
        return Redirect::route($redirectRoute)->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
