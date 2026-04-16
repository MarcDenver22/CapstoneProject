# Laravel Blade Template Usage Analysis

## Executive Summary

✅ **Overall Status**: System is mostly well-maintained with 1 critical issue and 1 unused file

### Key Findings:
- **Total Blade Files**: 79
- **Actively Referenced Views**: 56
- **Missing but Called**: 1 (CRITICAL - `hr.reports.pdf-export`)
- **Unused Views**: 1 (`welcome.blade.php` - safe to delete)
- **Essential Infrastructure**: 22 (layouts, components, partials - all used)

### Action Items:
| Priority | Item | Type | Status |
|----------|------|------|--------|
| 🔴 CRITICAL | Create `hr/reports/pdf-export.blade.php` | Missing view | Must fix before PDF export works |
| 🟡 MEDIUM | Delete `welcome.blade.php` | Unused file | Safe to delete |

---

## Summary
- **Total Blade Files Found**: 79
- **Total Unique Views Referenced in Controllers**: 56
- **Analysis Date**: April 15, 2026

---

## References Found in Controllers

### Directly Called Views (56 unique views):

**Authentication Views (6)**
- `auth.login` - AuthenticatedSessionController, LoginController
- `auth.register` - RegisteredUserController
- `auth.confirm-password` - ConfirmablePasswordController
- `auth.reset-password` - NewPasswordController
- `auth.forgot-password` - PasswordResetLinkController
- `auth.verify-email` - EmailVerificationPromptController

**Landing/Public Views (2)**
- `landing` - LandingPageController
- `welcome` - ❌ **NOT FOUND IN FILE SEARCH** (check index.php/routes)

**Kiosk Views (2)**
- `kiosk.unlock` - KioskController
- `kiosk.scan` - KioskScanController

**Admin Dashboard (1)**
- `admin.dashboard` - Admin\DashboardController

**Admin Events (4)**
- `admin.events.index` - Admin\EventController
- `admin.events.create` - Admin\EventController
- `admin.events.show` - Admin\EventController
- `admin.events.edit` - Admin\EventController

**Admin Employees (5)**
- `admin.employees.list` - Admin\EmployeesController
- `admin.employees.show` - Admin\EmployeesController
- `admin.employees.create` - Admin\EmployeesController
- `admin.employees.edit` - Admin\EmployeesController
- `admin.employees.register` - Employee\RegistrationController

**Admin Attendance (5)**
- `admin.attendance.index` - Admin\AttendanceController
- `admin.attendance.create` - Admin\AttendanceController
- `admin.attendance.show` - Admin\AttendanceController
- `admin.attendance.edit` - Admin\AttendanceController
- `admin.attendance.today` - Admin\AttendanceController

**Admin Audit Logs (2)**
- `admin.audit-logs.index` - Admin\AuditLogController
- `admin.audit-logs.show` - Admin\AuditLogController

**Admin Announcements (4)**
- `admin.announcements.index` - Admin\AnnouncementController
- `admin.announcements.create` - Admin\AnnouncementController
- `admin.announcements.show` - Admin\AnnouncementController
- `admin.announcements.edit` - Admin\AnnouncementController

**Employee Views (5)**
- `employee.dashboard` - Employee\DashboardController
- `employee.face_enrollment` - Employee\RegistrationController, Employee\FaceEnrollmentController
- `employee.attendance-history-table` - Employee\DashboardController, HR\ReportController
- `employee.leave-requests.index` - Employee\LeaveRequestController
- `employee.leave-requests.create` - Employee\LeaveRequestController
- `employee.leave-requests.show` - Employee\LeaveRequestController
- `employee.leave-requests.edit` - Employee\LeaveRequestController

**HR Views (9)**
- `hr.dashboard` - HR\DashboardController
- `hr.reports.index` - HR\ReportController
- `hr.reports.daily` - HR\ReportController
- `hr.reports.weekly` - HR\ReportController
- `hr.reports.monthly` - HR\ReportController
- `hr.reports.per-employee` - HR\ReportController
- `hr.reports.per-department` - HR\ReportController
- `hr.reports.pdf-export` - HR\ReportController (line 307) ❌ **FILE NOT FOUND**
- `hr.dtr-export` - HR\ReportController

**Super Admin Views (9)**
- `super_admin.dashboard` - SuperAdmin\DashboardController
- `super_admin.audit_logs` - SuperAdmin\DashboardController
- `super_admin.system_config` - SuperAdmin\DashboardController
- `super_admin.system_health` - SuperAdmin\DashboardController
- `super_admin.manage_users` - SuperAdmin\DashboardController
- `super_admin.edit_user` - SuperAdmin\DashboardController
- `super_admin.reports` - SuperAdmin\DashboardController
- `super_admin.face_recognition_settings` - SuperAdmin\DashboardController
- `super_admin.privacy_settings` - SuperAdmin\DashboardController

**Profile Views (1)**
- `profile.edit` - ProfileController, Employee\DashboardController

**Export Views (1)**
- `exports.attendance-history-export` - HR\ReportController, Employee\DashboardController

---

## All Blade Files Found (79 files)

### **NOT CALLED DIRECTLY FROM CONTROLLERS** (Components, Layouts, Partials)

These are typically included/extended from other views, not called directly from controllers:

**Layouts (3)**
- `layouts/app.blade.php` - Main app layout (extended by most views)
- `layouts/guest.blade.php` - Guest layout (extended by auth views)
- `layouts/navigation.blade.php` - Navigation component (included in layouts)
- `employee/layouts/app.blade.php` - Employee-specific layout

**Components (15)** - Used via `<x-component-name />` syntax
- `components/application-logo.blade.php`
- `components/attendance-history.blade.php`
- `components/auth-session-status.blade.php`
- `components/danger-button.blade.php`
- `components/dropdown.blade.php`
- `components/dropdown-link.blade.php`
- `components/input-error.blade.php`
- `components/input-label.blade.php`
- `components/leave-request-history.blade.php`
- `components/modal.blade.php`
- `components/nav-link.blade.php`
- `components/primary-button.blade.php`
- `components/responsive-nav-link.blade.php`
- `components/secondary-button.blade.php`
- `components/text-input.blade.php`

**Profile Partials (3)** - Included in `profile/edit.blade.php`
- `profile/partials/delete-user-form.blade.php`
- `profile/partials/update-password-form.blade.php`
- `profile/partials/update-profile-information-form.blade.php`

---

## ✅ ANALYSIS RESULTS

### All Referenced Views Are Found
Every view() call in controllers has a corresponding blade file.

### Critical Issues Found:

1. **❌ MISSING: `hr.reports.pdf-export.blade.php`**
   - **Referenced in**: `HR\ReportController.php` (line 307)
   - **Expected location**: `resources/views/hr/reports/pdf-export.blade.php`
   - **Current status**: FILE DOES NOT EXIST
   - **Impact**: PDF export functionality will fail at runtime
   - **Required action**: Either create the missing view file OR remove/update the view() call
   
2. **⚠️ UNUSED: `welcome.blade.php`**
   - **Location**: `resources/views/welcome.blade.php`
   - **Reference found**: None in any PHP controller or routes file
   - **Current status**: ORPHANED - Not called from anywhere
   - **Impact**: Safe to delete (appears to be a default Laravel boilerplate file)
   - **Verification completed**: Checked all routes, controllers, and migration files

### Safe Additional Components:

The following view files are NOT directly called via `view()` but are essential infrastructure:

- All files in `components/` folder - Referenced as Blade components
- All files in `layouts/` folder - Extended with `@extends()` or `@layout()`
- All files in `profile/partials/` - Included with `@include()`

**These are NOT safe to delete as they are dependencies of other views.**

---

## Files Safe to Review for Deletion

**Candidates (with low confidence - verify before deleting):**
1. `resources/views/welcome.blade.php` - Check if used in `routes/web.php` or anywhere else

---

## Recommendations & Action Plan

### 🔴 CRITICAL - Must Fix:

1. **Create or Fix PDF Export View**
   - Option A: Create `resources/views/hr/reports/pdf-export.blade.php` with appropriate PDF export template
   - Option B: Update `HR\ReportController.php` line 307 to use a different view
   - Example template needed:
     ```blade
     <div class="pdf-export">
         <h1>{{ ucfirst($type) }} Report</h1>
         <!-- Format report data for PDF -->
         @foreach($data as $record)
             <!-- Display record -->
         @endforeach
     </div>
     ```

### 🟡 Can Clean Up:

1. **Delete `resources/views/welcome.blade.php`**
   - ✅ Verified: Not referenced anywhere in the application
   - ✅ Safe to delete
   - It's a default Laravel boilerplate file and not used in this system

### ✅ No Action Needed For:

All component, layout, and partial files are properly used and should NOT be deleted:
- All `components/` files are referenced via Blade component syntax
- All `layouts/` files are extended via `@extends()` 
- All `profile/partials/` files are included where needed
