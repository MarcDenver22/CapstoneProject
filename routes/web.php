<?php

use App\Http\Controllers\KioskController;
use App\Http\Controllers\KioskScanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Employee\DashboardController as EmployeeDashboardController;
use App\Http\Controllers\Employee\LeaveRequestController;
use App\Http\Controllers\Employee\FaceEnrollmentController;
use App\Http\Controllers\Employee\RegistrationController as EmployeeRegistrationController;
use App\Http\Controllers\HR\DashboardController as HRDashboardController;
use App\Http\Controllers\HR\ReportController as HRReportController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\EmployeesController;
use App\Http\Controllers\Admin\CampusUpdateController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Landing page
Route::get('/', function () {
    return view('landing');
})->name('landing');

Route::get('/dashboard', function () {
    // Redirect to role-based dashboard
    /** @var \App\Models\User|null $user */
    $user = Auth::user();
    if (!$user) {
        return redirect()->route('landing');
    }
    $role = $user->role;
    
    return match($role) {
        'employee' => redirect()->route('employee.dashboard'),
        'hr' => redirect()->route('hr.dashboard'),
        'admin' => redirect()->route('admin.dashboard'),
        'super_admin' => redirect()->route('super_admin.dashboard'),
        default => redirect()->route('landing'),
    };
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Kiosk routes (public)
Route::get('/kiosk', [KioskController::class, 'index'])->name('kiosk');
Route::get('/kiosk/unlock', [KioskController::class, 'showUnlock'])->name('kiosk.unlock');
Route::post('/kiosk/verify-pin', [KioskController::class, 'verifyPin'])->name('kiosk.verify-pin');
Route::post('/kiosk/logout', [KioskController::class, 'logout'])->name('kiosk.logout');

// Kiosk Scan routes
Route::get('/kiosk/scan', [KioskScanController::class, 'index'])->name('kiosk.scan');
Route::post('/kiosk/scan', [KioskScanController::class, 'scan'])->name('kiosk.scan.submit');
Route::post('/kiosk/find-user', [KioskScanController::class, 'findUser'])->name('kiosk.find-user');
Route::get('/kiosk/get-user-descriptor', [KioskScanController::class, 'getUserDescriptor'])->name('kiosk.get-descriptor');
Route::get('/kiosk/test-descriptor', [KioskScanController::class, 'testDescriptor'])->name('kiosk.test-descriptor');
Route::get('/kiosk/view-descriptors', [KioskScanController::class, 'viewDescriptors'])->name('kiosk.view-descriptors');

// Employee routes
Route::middleware('auth')->prefix('employee')->name('employee.')->group(function () {
    Route::get('/dashboard', [EmployeeDashboardController::class, 'index'])->name('dashboard');
    Route::get('/attendance-history', [EmployeeDashboardController::class, 'attendanceHistory'])->name('attendance-history');
    Route::get('/attendance-history/export-pdf', [EmployeeDashboardController::class, 'exportHistoryPdf'])->name('attendance-export-pdf');
    Route::get('/attendance-history/print-pdf', [EmployeeDashboardController::class, 'printHistoryPdf'])->name('attendance-print-pdf');
    Route::get('/profile/edit', [EmployeeDashboardController::class, 'editProfile'])->name('profile.edit');
    // Allow both POST and PUT for profile updates to avoid MethodNotAllowed errors
    Route::match(['post', 'put'], '/profile/update', [EmployeeDashboardController::class, 'updateProfile'])->name('profile.update');
    
    // Leave Requests
    Route::prefix('leave-requests')->name('leave-requests.')->group(function () {
        Route::get('/', [LeaveRequestController::class, 'index'])->name('index');
        Route::get('/create', [LeaveRequestController::class, 'create'])->name('create');
        Route::post('/', [LeaveRequestController::class, 'store'])->name('store');
        Route::get('/{id}', [LeaveRequestController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [LeaveRequestController::class, 'edit'])->name('edit');
        Route::patch('/{id}', [LeaveRequestController::class, 'update'])->name('update');
        // Allow both POST and DELETE for cancel to avoid MethodNotAllowed errors from old forms/tools
        Route::match(['post', 'delete'], '/{id}/cancel', [LeaveRequestController::class, 'cancel'])->name('cancel');
    });
    
    // Face Enrollment
    Route::prefix('face-enrollment')->name('face_enrollment.')->group(function () {
        Route::get('/', [FaceEnrollmentController::class, 'showForm'])->name('show');
        Route::post('/save-sample', [FaceEnrollmentController::class, 'saveSample'])->name('save_sample');
        Route::post('/complete', [FaceEnrollmentController::class, 'complete'])->name('complete');
        Route::get('/status', [FaceEnrollmentController::class, 'status'])->name('status');
        Route::post('/reset', [FaceEnrollmentController::class, 'reset'])->name('reset');
        Route::get('/descriptors', [FaceEnrollmentController::class, 'getFaceDescriptors'])->name('descriptors');
    });
    
    // Employee Registration
    Route::get('/registration', [EmployeeRegistrationController::class, 'showForm'])->name('registration.show');
    Route::post('/registration', [EmployeeRegistrationController::class, 'store'])->name('registration.store');
});

// HR routes
Route::middleware('auth')->prefix('hr')->name('hr.')->group(function () {
    Route::get('/dashboard', [HRDashboardController::class, 'index'])->name('dashboard');
    
    // Profile (HR) - reuse shared ProfileController
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Attendance History (HR user can view their own history like employees)
    Route::get('/attendance-history', [EmployeeDashboardController::class, 'attendanceHistory'])->name('attendance-history');
    Route::get('/attendance-history/export-pdf', [EmployeeDashboardController::class, 'exportHistoryPdf'])->name('attendance-export-pdf');
    Route::get('/attendance-history/print-pdf', [EmployeeDashboardController::class, 'printHistoryPdf'])->name('attendance-print-pdf');

    // Announcements (HR) - use unified CampusUpdateController
    Route::prefix('announcements')->name('announcements.')->group(function () {
        Route::get('/', [CampusUpdateController::class, 'index'])->name('index');
        Route::get('/create', [CampusUpdateController::class, 'create'])->name('create')->defaults('type', 'announcement');
        Route::post('/', [CampusUpdateController::class, 'store'])->name('store')->defaults('type', 'announcement');
        Route::get('/{id}', [CampusUpdateController::class, 'show'])->name('show')->defaults('type', 'announcement');
        Route::get('/{id}/edit', [CampusUpdateController::class, 'edit'])->name('edit')->defaults('type', 'announcement');
        Route::patch('/{id}', [CampusUpdateController::class, 'update'])->name('update')->defaults('type', 'announcement');
        Route::delete('/{id}', [CampusUpdateController::class, 'destroy'])->name('destroy')->defaults('type', 'announcement');
    });

    // HR Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [HRReportController::class, 'index'])->name('index');
        Route::get('/per-employee', [HRReportController::class, 'perEmployee'])->name('per-employee');
        Route::get('/monthly', [HRReportController::class, 'monthly'])->name('monthly');
        Route::match(['get', 'post'], '/export-pdf', [HRReportController::class, 'exportPdf'])->name('export-pdf');
    });

    // DTR Export
    Route::prefix('dtr')->name('dtr.')->group(function () {
        Route::post('/export-pdf', [HRReportController::class, 'exportDtrPdf'])->name('export-pdf');
        Route::post('/print', [HRReportController::class, 'printDtrPdf'])->name('print');
        Route::post('/export-excel', [HRReportController::class, 'exportDtrPdf'])->name('export-excel');
    });
    Route::get('/dtr-template-upload', [HRReportController::class, 'dtrExportPage'])->name('dtr-template-upload');
});

// Admin routes
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    
    // Employees
    Route::prefix('employees')->name('employees.')->group(function () {
        Route::get('/', [EmployeesController::class, 'index'])->name('list');
        Route::get('/create', [EmployeesController::class, 'create'])->name('create');
        Route::post('/', [EmployeesController::class, 'store'])->name('store');
        Route::get('/{id}', [EmployeesController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [EmployeesController::class, 'edit'])->name('edit');
        Route::match(['put', 'patch'], '/{id}', [EmployeesController::class, 'update'])->name('update');
        Route::delete('/{id}', [EmployeesController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/reset-face', [EmployeesController::class, 'resetFaceEnrollment'])->name('reset_face');
    });
    

    // Announcements (Admin) - use unified CampusUpdateController
    Route::prefix('announcements')->name('announcements.')->group(function () {
        Route::get('/', [CampusUpdateController::class, 'index'])->name('index');
        Route::get('/create', [CampusUpdateController::class, 'create'])->name('create')->defaults('type', 'announcement');
        Route::post('/', [CampusUpdateController::class, 'store'])->name('store')->defaults('type', 'announcement');
        Route::get('/{id}', [CampusUpdateController::class, 'show'])->name('show')->defaults('type', 'announcement');
        Route::get('/{id}/edit', [CampusUpdateController::class, 'edit'])->name('edit')->defaults('type', 'announcement');
        Route::patch('/{id}', [CampusUpdateController::class, 'update'])->name('update')->defaults('type', 'announcement');
        Route::delete('/{id}', [CampusUpdateController::class, 'destroy'])->name('destroy')->defaults('type', 'announcement');
    });
    
    // Audit Logs
    Route::prefix('audit-logs')->name('audit_logs.')->group(function () {
        Route::get('/', [AuditLogController::class, 'index'])->name('index');
        Route::get('/{id}', [AuditLogController::class, 'show'])->name('show');
    });
    
    // Attendance
    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AttendanceController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\AttendanceController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\AttendanceController::class, 'store'])->name('store');
        Route::get('/{attendance}', [\App\Http\Controllers\Admin\AttendanceController::class, 'show'])->name('show');
        Route::get('/{attendance}/edit', [\App\Http\Controllers\Admin\AttendanceController::class, 'edit'])->name('edit');
        Route::match(['put', 'patch'], '/{attendance}', [\App\Http\Controllers\Admin\AttendanceController::class, 'update'])->name('update');
        Route::delete('/{attendance}', [\App\Http\Controllers\Admin\AttendanceController::class, 'destroy'])->name('destroy');
        Route::get('/today', [\App\Http\Controllers\Admin\AttendanceController::class, 'today'])->name('today');
    });
});

// Super Admin routes
Route::middleware('auth')->prefix('super-admin')->name('super_admin.')->group(function () {
    Route::get('/dashboard', [SuperAdminDashboardController::class, 'index'])->middleware('role:super_admin')->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    
    // Users Management
    Route::prefix('users')->name('users.')->middleware('role:super_admin')->group(function () {
        Route::get('/', [SuperAdminDashboardController::class, 'manageUsers'])->name('index');
        Route::get('/create', [SuperAdminDashboardController::class, 'manageUsers'])->name('create');
        Route::post('/', [SuperAdminDashboardController::class, 'createUser'])->name('store');
        Route::get('/{user}/edit', [SuperAdminDashboardController::class, 'editUser'])->name('edit');
        Route::match(['put', 'patch'], '/{user}', [SuperAdminDashboardController::class, 'updateUser'])->name('update');
        Route::delete('/{user}', [SuperAdminDashboardController::class, 'deleteUser'])->name('delete');
    });
    
    // Audit Logs
    Route::get('/audit-logs', [SuperAdminDashboardController::class, 'auditLogs'])->middleware('role:super_admin')->name('audit_logs');
    
    // System Settings
    Route::get('/system-config', [SuperAdminDashboardController::class, 'systemConfig'])->middleware('role:super_admin')->name('system_config');
    Route::get('/system-health', [SuperAdminDashboardController::class, 'systemHealth'])->middleware('role:super_admin')->name('system_health');
});

require __DIR__.'/auth.php';
