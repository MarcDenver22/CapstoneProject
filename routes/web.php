<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\KioskController;
use App\Http\Controllers\KioskScanController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EmployeesController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Employee\DashboardController as EmployeeDashboardController;
use App\Http\Controllers\Employee\RegistrationController;
use App\Http\Controllers\Employee\FaceEnrollmentController;
use App\Http\Controllers\Employee\LeaveRequestController;
use App\Http\Controllers\HR\DashboardController as HRDashboardController;
use App\Http\Controllers\HR\EventController as HREventController;
use App\Http\Controllers\HR\AnnouncementController as HRAnnouncementController;
use App\Http\Controllers\HR\ReportController as HRReportController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use Illuminate\Support\Facades\Route;

// Landing page route
Route::get('/', [LandingPageController::class, 'index'])->name('landing');

// Auth routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Employee Registration routes (public)
Route::get('/register', [RegistrationController::class, 'showForm'])->name('employee.registration.show');
Route::post('/register', [RegistrationController::class, 'store'])->name('employee.registration.store');

// Kiosk routes (public but IP-restricted)
Route::middleware('kiosk.ip.allowlist')->group(function () {
    // Kiosk unlock page (IP check only)
    Route::get('/kiosk/unlock', [KioskController::class, 'showUnlock'])->name('kiosk.unlock');
    Route::post('/kiosk/verify-pin', [KioskController::class, 'verifyPin'])->name('kiosk.verify-pin');
    
    // Kiosk main page (IP check only, PIN check handled in controller)
    Route::get('/kiosk', [KioskController::class, 'index'])->name('kiosk');
    Route::post('/kiosk/logout', [KioskController::class, 'logout'])->name('kiosk.logout');
    
    // Kiosk scan page (camera-based face scan)
    Route::get('/kiosk/scan', [KioskScanController::class, 'index'])->name('kiosk.scan');
    Route::post('/kiosk/scan', [KioskScanController::class, 'scan'])->name('kiosk.scan');
    Route::post('/kiosk/find-user', [KioskScanController::class, 'findUser'])->name('kiosk.find-user');
    Route::get('/kiosk/get-user-descriptor', [KioskScanController::class, 'getUserDescriptor'])->name('kiosk.get-descriptor');
    
    // Debug endpoints (for troubleshooting)
    Route::post('/kiosk/test-descriptor', [KioskScanController::class, 'testDescriptor'])->name('kiosk.test-descriptor');
    Route::get('/kiosk/view-descriptors', [KioskScanController::class, 'viewDescriptors'])->name('kiosk.view-descriptors');
});

// Protected routes
Route::middleware('auth')->group(function () {
    // Admin Dashboard (Admin + Super Admin)
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])
        ->middleware('can.access.admin')
        ->name('admin.dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('can.access.admin')
        ->name('dashboard');
    
    // Employee Dashboard
    Route::get('/employee/dashboard', [EmployeeDashboardController::class, 'index'])->name('employee.dashboard');
    Route::get('/employee/attendance-history', [EmployeeDashboardController::class, 'attendanceHistory'])->name('employee.attendance-history');
    Route::get('/employee/attendance-history/export-pdf', [EmployeeDashboardController::class, 'exportHistoryPdf'])->name('employee.attendance-history.export-pdf');
    Route::get('/employee/attendance-history/print', [EmployeeDashboardController::class, 'printHistoryPdf'])->name('employee.attendance-history.print');
    
    // Employee Profile
    Route::get('/employee/profile/edit', [EmployeeDashboardController::class, 'editProfile'])->name('employee.profile.edit');
    Route::put('/employee/profile', [EmployeeDashboardController::class, 'updateProfile'])->name('employee.profile.update');
    
    // Employee Leave Requests
    Route::prefix('/employee/leave-requests')->group(function () {
        Route::get('/', [LeaveRequestController::class, 'index'])->name('employee.leave-requests.index');
        Route::get('/create', [LeaveRequestController::class, 'create'])->name('employee.leave-requests.create');
        Route::post('/', [LeaveRequestController::class, 'store'])->name('employee.leave-requests.store');
        Route::get('/{id}', [LeaveRequestController::class, 'show'])->name('employee.leave-requests.show');
        Route::get('/{id}/edit', [LeaveRequestController::class, 'edit'])->name('employee.leave-requests.edit');
        Route::put('/{id}', [LeaveRequestController::class, 'update'])->name('employee.leave-requests.update');
        Route::delete('/{id}', [LeaveRequestController::class, 'cancel'])->name('employee.leave-requests.cancel');
    });
    
    // Admin - Employees Management (Admin + Super Admin)
    Route::middleware('can.access.admin')->group(function () {
        Route::get('/employees', [EmployeesController::class, 'index'])->name('admin.employees.list');
        Route::get('/employees/create', [EmployeesController::class, 'create'])->name('admin.employees.create');
        Route::post('/employees', [EmployeesController::class, 'store'])->name('admin.employees.store');
        Route::get('/employees/{id}', [EmployeesController::class, 'show'])->name('admin.employees.show');
        Route::get('/employees/{id}/edit', [EmployeesController::class, 'edit'])->name('admin.employees.edit');
        Route::put('/employees/{id}', [EmployeesController::class, 'update'])->name('admin.employees.update');
        Route::delete('/employees/{id}', [EmployeesController::class, 'destroy'])->name('admin.employees.destroy');
        Route::post('/employees/{id}/reset-face', [EmployeesController::class, 'resetFaceEnrollment'])->name('admin.employees.reset_face');
        
        // Events Management
        Route::resource('events', EventController::class)->names([
            'index' => 'admin.events.index',
            'create' => 'admin.events.create',
            'store' => 'admin.events.store',
            'show' => 'admin.events.show',
            'edit' => 'admin.events.edit',
            'update' => 'admin.events.update',
            'destroy' => 'admin.events.destroy',
        ]);
        
        // Announcements Management
        Route::resource('announcements', AnnouncementController::class)->names([
            'index' => 'admin.announcements.index',
            'create' => 'admin.announcements.create',
            'store' => 'admin.announcements.store',
            'show' => 'admin.announcements.show',
            'edit' => 'admin.announcements.edit',
            'update' => 'admin.announcements.update',
            'destroy' => 'admin.announcements.destroy',
        ]);
        
        // Audit Logs
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('admin.audit_logs.index');
        Route::get('/audit-logs/{auditLog}', [AuditLogController::class, 'show'])->name('admin.audit_logs.show');
        
        // Attendance Management
        Route::get('/attendance/today', [AttendanceController::class, 'today'])->name('admin.attendance.today');
        Route::resource('attendance', AttendanceController::class)->names([
            'index' => 'admin.attendance.index',
            'create' => 'admin.attendance.create',
            'store' => 'admin.attendance.store',
            'show' => 'admin.attendance.show',
            'edit' => 'admin.attendance.edit',
            'update' => 'admin.attendance.update',
            'destroy' => 'admin.attendance.destroy',
        ]);
    });
    
    // Employee Face Enrollment (after registration)
    Route::get('/face-enrollment', [FaceEnrollmentController::class, 'showForm'])->name('employee.face_enrollment.show');
    Route::post('/face-enrollment/save-sample', [FaceEnrollmentController::class, 'saveSample'])->name('employee.face.save_sample');
    Route::post('/face-enrollment/complete', [FaceEnrollmentController::class, 'complete'])->name('employee.face.complete');
    Route::get('/face-enrollment/status', [FaceEnrollmentController::class, 'status'])->name('employee.face.status');
    Route::post('/face-enrollment/reset', [FaceEnrollmentController::class, 'reset'])->name('employee.face.reset');



    // HR Dashboard
    Route::middleware('role:hr')->group(function () {
        Route::get('/hr/dashboard', [HRDashboardController::class, 'index'])->name('hr.dashboard');
        Route::resource('hr/events', HREventController::class)->names([
            'index' => 'hr.events.index',
            'create' => 'hr.events.create',
            'store' => 'hr.events.store',
            'show' => 'hr.events.show',
            'edit' => 'hr.events.edit',
            'update' => 'hr.events.update',
            'destroy' => 'hr.events.destroy',
        ]);
        Route::resource('hr/announcements', HRAnnouncementController::class)->names([
            'index' => 'hr.announcements.index',
            'create' => 'hr.announcements.create',
            'store' => 'hr.announcements.store',
            'show' => 'hr.announcements.show',
            'edit' => 'hr.announcements.edit',
            'update' => 'hr.announcements.update',
            'destroy' => 'hr.announcements.destroy',
        ]);
        
        // HR Reports
        Route::get('/hr/reports', [HRReportController::class, 'index'])->name('hr.reports.index');
        Route::get('/hr/reports/daily', [HRReportController::class, 'daily'])->name('hr.reports.daily');
        Route::get('/hr/reports/weekly', [HRReportController::class, 'weekly'])->name('hr.reports.weekly');
        Route::get('/hr/reports/monthly', [HRReportController::class, 'monthly'])->name('hr.reports.monthly');
        Route::get('/hr/reports/per-employee', [HRReportController::class, 'perEmployee'])->name('hr.reports.per-employee');
        Route::get('/hr/reports/per-department', [HRReportController::class, 'perDepartment'])->name('hr.reports.per-department');
        Route::get('/hr/reports/export-csv', [HRReportController::class, 'exportCsv'])->name('hr.reports.export-csv');
        Route::get('/hr/reports/export-pdf', [HRReportController::class, 'exportPdf'])->name('hr.reports.export-pdf');
        Route::get('/hr/dtr', [HRReportController::class, 'dtrExportPage'])->name('hr.dtr.page');
        Route::get('/hr/dtr/export-pdf', [HRReportController::class, 'exportDtrPdf'])->name('hr.dtr.export-pdf');
        Route::get('/hr/dtr/print', [HRReportController::class, 'printDtrPdf'])->name('hr.dtr.print');
    });
    
    // Super Admin routes
    Route::middleware('role:super_admin')->group(function () {
        Route::get('/super-admin/dashboard', [SuperAdminDashboardController::class, 'index'])->name('super_admin.dashboard');
        Route::get('/super-admin/users', [SuperAdminDashboardController::class, 'manageUsers'])->name('super_admin.users');
        Route::post('/super-admin/users', [SuperAdminDashboardController::class, 'createUser'])->name('super_admin.users.create');
        Route::get('/super-admin/users/{user}/edit', [SuperAdminDashboardController::class, 'editUser'])->name('super_admin.users.edit');
        Route::put('/super-admin/users/{user}', [SuperAdminDashboardController::class, 'updateUser'])->name('super_admin.users.update');
        Route::delete('/super-admin/users/{user}', [SuperAdminDashboardController::class, 'deleteUser'])->name('super_admin.users.delete');
        Route::get('/super-admin/audit-logs', [SuperAdminDashboardController::class, 'auditLogs'])->name('super_admin.audit_logs');
        Route::get('/super-admin/system-config', [SuperAdminDashboardController::class, 'systemConfig'])->name('super_admin.system_config');
        Route::get('/super-admin/system-health', [SuperAdminDashboardController::class, 'systemHealth'])->name('super_admin.system_health');
        Route::get('/super-admin/reports', [SuperAdminDashboardController::class, 'generateReports'])->name('super_admin.reports');
        Route::post('/super-admin/backup', [SuperAdminDashboardController::class, 'backupDatabase'])->name('super_admin.backup');
        Route::get('/super-admin/face-recognition', [SuperAdminDashboardController::class, 'faceRecognitionSettings'])->name('super_admin.face_recognition');
        Route::get('/super-admin/privacy', [SuperAdminDashboardController::class, 'privacySettings'])->name('super_admin.privacy');
    });
});