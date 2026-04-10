# Attendance Management System - REST API Documentation

## Overview
Complete REST API for the attendance management system with token-based authentication using Laravel Sanctum.

**Base URL:** `http://localhost:8000/api/v1`

---

## Authentication

### 1. Login
Get API token for authentication.

```
POST /auth/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": { /* user object */ },
    "token": "1|abc123xyz..."
  }
}
```

### 2. Register
Create new user account.

```
POST /auth/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

### 3. Get Current User
Get authenticated user details.

```
GET /auth/me
Authorization: Bearer {token}
```

### 4. Logout
Revoke API token.

```
POST /auth/logout
Authorization: Bearer {token}
```

### 5. Refresh Token
Get new API token.

```
POST /auth/refresh
Authorization: Bearer {token}
```

---

## Attendance Endpoints

### 1. Get All Attendances
List all attendance records with pagination.

```
GET /attendances?per_page=15&from_date=2026-01-01&to_date=2026-12-31&user_id=1
Authorization: Bearer {token}
```

**Query Parameters:**
- `per_page`: Items per page (default: 15)
- `from_date`: Filter from date (YYYY-MM-DD)
- `to_date`: Filter to date (YYYY-MM-DD)
- `user_id`: Filter by user ID

**Response:**
```json
{
  "success": true,
  "data": {
    "data": [ /* attendance records */ ],
    "current_page": 1,
    "total": 50
  }
}
```

### 2. Get Single Attendance
Get specific attendance record.

```
GET /attendances/{id}
Authorization: Bearer {token}
```

### 3. Create Attendance (Check-in)
Record check-in.

```
POST /attendances
Authorization: Bearer {token}
Content-Type: application/json

{
  "user_id": 1,
  "date": "2026-04-09",
  "check_in": "2026-04-09 09:00:00"
}
```

### 4. Update Attendance
Update check-in/check-out times.

```
PUT /attendances/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "check_out": "2026-04-09 17:00:00"
}
```

### 5. Delete Attendance
Delete attendance record.

```
DELETE /attendances/{id}
Authorization: Bearer {token}
```

### 6. Get User Attendance
Get attendance records for specific user.

```
GET /attendances/user/{userId}?from_date=2026-01-01&to_date=2026-12-31
Authorization: Bearer {token}
```

### 7. Get Date Attendance
Get all attendance for specific date.

```
GET /attendances/date/{date}
Authorization: Bearer {token}
```

### 8. Check-out
Record check-out time.

```
POST /attendances/{id}/checkout
Authorization: Bearer {token}
Content-Type: application/json

{
  "check_out": "2026-04-09 17:30:00"
}
```

### 9. Face Recognition (Public)
Upload photo for automatic face recognition and attendance.

```
POST /attendance/recognize
Content-Type: multipart/form-data

photo: [binary image file]
```

**Response:**
```json
{
  "success": true,
  "name": "John Doe",
  "message": "Check-in recorded successfully!",
  "type": "checkin",
  "data": { /* attendance record */ }
}
```

---

## Employee Endpoints

### 1. Get All Employees
List all employees.

```
GET /employees?per_page=15&role=employee&department_id=1
Authorization: Bearer {token}
```

**Query Parameters:**
- `per_page`: Items per page (default: 15)
- `role`: Filter by role (admin, employee, hr, super_admin)
- `department_id`: Filter by department

### 2. Get Single Employee
Get employee details.

```
GET /employees/{id}
Authorization: Bearer {token}
```

### 3. Create Employee
Add new employee.

```
POST /employees
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Jane Smith",
  "email": "jane@example.com",
  "password": "password123",
  "role": "employee",
  "department_id": 1
}
```

### 4. Update Employee
Update employee information.

```
PUT /employees/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Jane Smith",
  "department_id": 2
}
```

### 5. Delete Employee
Remove employee.

```
DELETE /employees/{id}
Authorization: Bearer {token}
```

### 6. Search Employees
Search employees by name or email.

```
GET /employees/search/{query}
Authorization: Bearer {token}
```

Example: `GET /employees/search/john`

### 7. Get Employees by Department
Get all employees in specific department.

```
GET /employees/department/{deptId}
Authorization: Bearer {token}
```

---

## Reports Endpoints

### 1. Daily Report
Get daily attendance report.

```
GET /reports/daily?date=2026-04-09
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "date": "2026-04-09",
  "statistics": {
    "total_checked_in": 45,
    "total_checked_out": 42,
    "total_absent": 5
  },
  "data": [ /* attendance records */ ]
}
```

### 2. Weekly Report
Get weekly attendance report.

```
GET /reports/weekly?start_date=2026-04-01&end_date=2026-04-07
Authorization: Bearer {token}
```

### 3. Monthly Report
Get monthly attendance report.

```
GET /reports/monthly?year=2026&month=4
Authorization: Bearer {token}
```

### 4. Employee Report
Get attendance report for specific employee.

```
GET /reports/employee/{userId}?start_date=2026-04-01&end_date=2026-04-30
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "user": { /* user object */ },
  "start_date": "2026-04-01",
  "end_date": "2026-04-30",
  "statistics": {
    "total_present": 20,
    "total_absent": 2,
    "on_time": 18
  },
  "data": [ /* attendance records */ ]
}
```

### 5. Department Report
Get attendance report for specific department.

```
GET /reports/department/{deptId}?start_date=2026-04-01&end_date=2026-04-30
Authorization: Bearer {token}
```

---

## Error Handling

All error responses follow this format:

```json
{
  "success": false,
  "message": "Error description"
}
```

### Common Status Codes:
- `200 OK` - Successful GET/PUT/DELETE
- `201 Created` - Successful POST
- `400 Bad Request` - Validation error
- `401 Unauthorized` - Missing or invalid token
- `404 Not Found` - Resource not found
- `500 Server Error` - Server error

---

## Usage Examples

### Example: Login and Get User Attendance

```bash
# 1. Login
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "user@example.com",
    "password": "password"
  }'

# Response contains token: "1|abc123xyz..."

# 2. Use token to get attendance
curl -X GET "http://localhost:8000/api/v1/attendances/user/1?from_date=2026-04-01&to_date=2026-04-30" \
  -H "Authorization: Bearer 1|abc123xyz..."
```

---

## Rate Limiting
Currently no rate limiting. Can be added with middleware if needed.

---

## CORS
Update `config/cors.php` to allow your frontend domain for CORS requests.

---

## Documentation Updates
This API documentation is complete. For changes or additions, update the `routes/api.php` and corresponding controller files.
