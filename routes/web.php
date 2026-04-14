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
use App\Http\Controllers\HR\EventController as HREventController;
use App\Http\Controllers\HR\AnnouncementController as HRAnnouncementController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\EmployeesController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\AnnouncementController as AdminAnnouncementController;
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
    Route::post('/profile/update', [EmployeeDashboardController::class, 'updateProfile'])->name('profile.update');
    
    // Leave Requests
    Route::prefix('leave-requests')->name('leave-requests.')->group(function () {
        Route::get('/', [LeaveRequestController::class, 'index'])->name('index');
        Route::get('/create', [LeaveRequestController::class, 'create'])->name('create');
        Route::post('/', [LeaveRequestController::class, 'store'])->name('store');
        Route::get('/{id}', [LeaveRequestController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [LeaveRequestController::class, 'edit'])->name('edit');
        Route::patch('/{id}', [LeaveRequestController::class, 'update'])->name('update');
        Route::post('/{id}/cancel', [LeaveRequestController::class, 'cancel'])->name('cancel');
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
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    
    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [HRReportController::class, 'index'])->name('index');
        Route::get('/daily', [HRReportController::class, 'daily'])->name('daily');
        Route::get('/weekly', [HRReportController::class, 'weekly'])->name('weekly');
        Route::get('/monthly', [HRReportController::class, 'monthly'])->name('monthly');
        Route::get('/per-employee', [HRReportController::class, 'perEmployee'])->name('per-employee');
        Route::get('/per-department', [HRReportController::class, 'perDepartment'])->name('per-department');
        Route::get('/export-pdf', [HRReportController::class, 'exportPdf'])->name('export-pdf');
        Route::get('/export-csv', [HRReportController::class, 'exportCsv'])->name('export-csv');
    });
    
    // DTR Export
    Route::get('/dtr-export', [HRReportController::class, 'dtrExportPage'])->name('dtr-export');
    Route::post('/dtr-export-pdf', [HRReportController::class, 'exportDtrPdf'])->name('dtr.export-pdf');
    Route::post('/dtr-print-pdf', [HRReportController::class, 'printDtrPdf'])->name('dtr.export-pdf-print');
    Route::get('/dtr-template-upload', [HRReportController::class, 'dtrExportPage'])->name('dtr-template-upload');
    Route::post('/dtr-export-excel', [HRReportController::class, 'exportDtrPdf'])->name('dtr.export-excel');
    
    // Events
    Route::prefix('events')->name('events.')->group(function () {
        Route::get('/', [HREventController::class, 'index'])->name('index');
        Route::get('/create', [HREventController::class, 'create'])->name('create');
        Route::post('/', [HREventController::class, 'store'])->name('store');
        Route::get('/{id}', [HREventController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [HREventController::class, 'edit'])->name('edit');
        Route::patch('/{id}', [HREventController::class, 'update'])->name('update');
        Route::delete('/{id}', [HREventController::class, 'destroy'])->name('destroy');
    });
    
    // Announcements
    Route::prefix('announcements')->name('announcements.')->group(function () {
        Route::get('/', [HRAnnouncementController::class, 'index'])->name('index');
        Route::get('/create', [HRAnnouncementController::class, 'create'])->name('create');
        Route::post('/', [HRAnnouncementController::class, 'store'])->name('store');
        Route::get('/{id}', [HRAnnouncementController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [HRAnnouncementController::class, 'edit'])->name('edit');
        Route::patch('/{id}', [HRAnnouncementController::class, 'update'])->name('update');
        Route::delete('/{id}', [HRAnnouncementController::class, 'destroy'])->name('destroy');
    });
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
        Route::patch('/{id}', [EmployeesController::class, 'update'])->name('update');
        Route::delete('/{id}', [EmployeesController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/reset-face', [EmployeesController::class, 'resetFaceEnrollment'])->name('reset_face');
    });
    
    // Events
    Route::prefix('events')->name('events.')->group(function () {
        Route::get('/', [AdminEventController::class, 'index'])->name('index');
        Route::get('/create', [AdminEventController::class, 'create'])->name('create');
        Route::post('/', [AdminEventController::class, 'store'])->name('store');
        Route::get('/{id}', [AdminEventController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [AdminEventController::class, 'edit'])->name('edit');
        Route::patch('/{id}', [AdminEventController::class, 'update'])->name('update');
        Route::delete('/{id}', [AdminEventController::class, 'destroy'])->name('destroy');
    });
    
    // Announcements
    Route::prefix('announcements')->name('announcements.')->group(function () {
        Route::get('/', [AdminAnnouncementController::class, 'index'])->name('index');
        Route::get('/create', [AdminAnnouncementController::class, 'create'])->name('create');
        Route::post('/', [AdminAnnouncementController::class, 'store'])->name('store');
        Route::get('/{id}', [AdminAnnouncementController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [AdminAnnouncementController::class, 'edit'])->name('edit');
        Route::patch('/{id}', [AdminAnnouncementController::class, 'update'])->name('update');
        Route::delete('/{id}', [AdminAnnouncementController::class, 'destroy'])->name('destroy');
    });
    
    // Audit Logs
    Route::prefix('audit-logs')->name('audit_logs.')->group(function () {
        Route::get('/', [AuditLogController::class, 'index'])->name('index');
        Route::get('/{id}', [AuditLogController::class, 'show'])->name('show');
    });
    
    // Attendance
    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('index');
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
        Route::patch('/{user}', [SuperAdminDashboardController::class, 'updateUser'])->name('update');
        Route::delete('/{user}', [SuperAdminDashboardController::class, 'deleteUser'])->name('delete');
    });
    
    // Audit Logs
    Route::get('/audit-logs', [SuperAdminDashboardController::class, 'auditLogs'])->middleware('role:super_admin')->name('audit_logs');
    
    // System Settings
    Route::get('/system-config', [SuperAdminDashboardController::class, 'systemConfig'])->middleware('role:super_admin')->name('system_config');
    Route::get('/system-health', [SuperAdminDashboardController::class, 'systemHealth'])->middleware('role:super_admin')->name('system_health');
});

require __DIR__.'/auth.php';
