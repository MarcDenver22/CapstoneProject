<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\AuditLogger;

class DashboardController extends Controller
{
    public function index()
    {
        // Get system statistics
        $totalUsers = User::count();
        $activeTodayCount = User::whereDate('created_at', today())->count();
        $users = User::orderBy('created_at', 'desc')->limit(10)->get();
        
        // Get recent audit logs (10 most recent activities)
        $recentActivities = AuditLog::with('user')
            ->latest('created_at')
            ->limit(10)
            ->get();
        
        return view('super_admin.dashboard', compact(
            'totalUsers',
            'activeTodayCount',
            'users',
            'recentActivities'
        ));
    }

    /**
     * Show all audit logs
     */
    public function auditLogs()
    {
        $logs = AuditLog::with('user')
            ->latest('created_at')
            ->paginate(20);
        
        return view('super_admin.audit_logs', compact('logs'));
    }

    /**
     * Manage system configuration
     */
    public function systemConfig()
    {
        return view('super_admin.system_config');
    }

    /**
     * View system health and monitoring
     */
    public function systemHealth()
    {
        return view('super_admin.system_health');
    }

    /**
     * Manage users (create, edit, delete)
     */
    public function manageUsers()
    {
        // Only super_admin can access user management
        if (Auth::user()->role !== 'super_admin') {
            return redirect()->back()->with('error', 'Unauthorized access');
        }

        $users = User::paginate(15);
        return view('super_admin.manage_users', compact('users'));
    }

    /**
     * Create new user
     */
    public function createUser(Request $request)
    {
        // Only super_admin can create users
        if (Auth::user()->role !== 'super_admin') {
            return redirect()->back()->with('error', 'Unauthorized access');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,employee,super_admin,hr',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role' => $validated['role'],
            'email_verified_at' => now(),
        ]);

        // Log user creation
        AuditLogger::logCreate('User', $user->id, [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ]);

        return redirect()->route('super_admin.users.index')->with('success', 'User created successfully');
    }

    /**
     * Edit user - Show form to assign/change role for existing user
     */
    public function editUser(User $user)
    {
        // Only super_admin can edit users
        if (Auth::user()->role !== 'super_admin') {
            return redirect()->back()->with('error', 'Unauthorized access');
        }

        return view('super_admin.edit_user', compact('user'));
    }

    /**
     * Update user
     */
    public function updateUser(Request $request, User $user)
    {
        // Only super_admin can update users
        if (Auth::user()->role !== 'super_admin') {
            return redirect()->back()->with('error', 'Unauthorized access');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,employee,super_admin,hr',
        ]);

        // Track changes before update
        $changes = [];
        if ($user->name !== $validated['name']) {
            $changes['name'] = ['from' => $user->name, 'to' => $validated['name']];
        }
        if ($user->email !== $validated['email']) {
            $changes['email'] = ['from' => $user->email, 'to' => $validated['email']];
        }
        if ($user->role !== $validated['role']) {
            $changes['role'] = ['from' => $user->role, 'to' => $validated['role']];
        }

        $user->update($validated);

        // Log user update if there are changes
        if (!empty($changes)) {
            AuditLogger::logUpdate('User', $user->id, $changes);
        }

        return redirect()->route('super_admin.users.index')->with('success', 'User updated successfully');
    }

    /**
     * Delete user
     */
    public function deleteUser(User $user)
    {
        // Only super_admin can delete users
        if (Auth::user()->role !== 'super_admin') {
            return redirect()->back()->with('error', 'Unauthorized access');
        }

        // Prevent deleting the current user
        if (Auth::id() === $user->id) {
            return redirect()->route('super_admin.users.index')->with('error', 'Cannot delete your own account');
        }

        $userName = $user->name;
        $userData = $user->toArray();
        $user->delete();

        // Log user deletion
        AuditLogger::logDelete('User', $user->id, $userData);

        return redirect()->route('super_admin.users.index')->with('success', "User '{$userName}' deleted successfully");
    }

    /**
     * Generate reports
     */
    public function generateReports(Request $request)
    {
        $period = $request->input('period', 'month');
        
        return view('super_admin.reports', [
            'period' => $period,
        ]);
    }

    /**
     * Backup database
     */
    public function backupDatabase()
    {
        // Implement database backup logic
        return response()->json(['status' => 'success', 'message' => 'Backup started']);
    }

    /**
     * Manage face recognition settings
     */
    public function faceRecognitionSettings()
    {
        return view('super_admin.face_recognition_settings');
    }

    /**
     * Manage privacy compliance settings
     */
    public function privacySettings()
    {
        return view('super_admin.privacy_settings');
    }
}
