<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\ReportController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes (no authentication required)
// Rate limit: 10 requests per minute for security-sensitive endpoints
Route::prefix('v1')->middleware('throttle:auth-public')->group(function () {
    // Authentication routes - strict rate limiting
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/register', [AuthController::class, 'register']);
});

// Public kiosk endpoint for face recognition
// Rate limit: 30 requests per minute (higher due to kiosk multiple attempts)
Route::prefix('v1')->middleware('throttle:kiosk')->group(function () {
    Route::post('/attendance/recognize', [AttendanceController::class, 'recognize']);
});

// Protected routes (require authentication)
// Rate limit: 100 requests per minute for authenticated users
Route::prefix('v1')->middleware(['auth:sanctum', 'throttle:api'])->group(function () {
    
    // Authentication
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/refresh', [AuthController::class, 'refresh']);
    
    // Attendance Management
    Route::apiResource('attendances', AttendanceController::class);
    Route::get('/attendances/user/{userId}', [AttendanceController::class, 'userAttendance']);
    Route::get('/attendances/date/{date}', [AttendanceController::class, 'dateAttendance']);
    Route::post('/attendances/{id}/checkout', [AttendanceController::class, 'checkout']);
    
    // Employee Management
    Route::apiResource('employees', EmployeeController::class);
    Route::get('/employees/search/{query}', [EmployeeController::class, 'search']);
    Route::get('/employees/department/{deptId}', [EmployeeController::class, 'byDepartment']);
    
    // Reports
    Route::prefix('reports')->group(function () {
        Route::get('/daily', [ReportController::class, 'daily']);
        Route::get('/weekly', [ReportController::class, 'weekly']);
        Route::get('/monthly', [ReportController::class, 'monthly']);
        Route::get('/employee/{userId}', [ReportController::class, 'employeeReport']);
        Route::get('/department/{deptId}', [ReportController::class, 'departmentReport']);
    });
});
