<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Department;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
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
        
        if (!$user) {
            return Redirect::back()->withErrors(['error' => 'User not found.']);
        }
        
        // Build validation rules
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'position' => 'nullable|string|max:255',
            'department_id' => 'nullable|exists:supabase.departments,id',
        ];
        
        // Add password validation if user is trying to change password
        if ($request->filled('password')) {
            $rules['current_password'] = 'required|string';
            $rules['password'] = 'required|string|min:8|confirmed';
        }
        
        $validated = $request->validate($rules);
        
        // Track if password changed
        $passwordChanged = false;
        
        // Verify current password if changing password
        if ($request->filled('password')) {
            if (!Hash::check($request->input('current_password'), $user->password)) {
                return Redirect::back()
                    ->withInput($request->except('password', 'password_confirmation', 'current_password'))
                    ->withErrors(['current_password' => 'The password you entered is incorrect. Please verify and try again.']);
            }
            $passwordChanged = true;
        }
        
        // Prepare update data (only include non-null values and changed fields)
        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];
        
        if (isset($validated['position']) && $validated['position'] !== null) {
            $updateData['position'] = $validated['position'];
        }
        
        if (isset($validated['department_id']) && $validated['department_id'] !== null) {
            $updateData['department_id'] = $validated['department_id'];
        }
        
        // Update profile fields
        $user->update($updateData);
        
        // If password changed, update it separately and explicitly
        if ($passwordChanged && $request->filled('password')) {
            $newHashedPassword = Hash::make($validated['password']);
            
            // Force refresh from database first
            $user->refresh();
            
            // Update password directly
            DB::connection('supabase')
                ->table('users')
                ->where('id', $user->id)
                ->update(['password' => $newHashedPassword]);
            
            // Refresh user to get latest password from database
            $user->refresh();
        }
        
        // Mark email as unverified if it changed
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
            $user->save();
        }

        if ($user->role === 'hr') {
            $redirectRoute = 'hr.profile.edit';
        } elseif ($user->role === 'admin') {
            $redirectRoute = 'admin.profile.edit';
        } else {
            $redirectRoute = 'profile.edit';
        }
        
        // Return appropriate message based on what was updated
        if ($passwordChanged) {
            return Redirect::route($redirectRoute)
                ->with('success', '✓ Your password has been successfully changed! Your new password is now active.');
        } else {
            return Redirect::route($redirectRoute)
                ->with('status', 'profile-updated');
        }
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
