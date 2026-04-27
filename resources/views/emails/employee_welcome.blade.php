<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #2563eb; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
        .content { background-color: #f9fafb; padding: 20px; border: 1px solid #e5e7eb; }
        .details-box { background-color: white; border: 1px solid #d1d5db; border-radius: 5px; padding: 15px; margin: 15px 0; }
        .details-box p { margin: 8px 0; }
        .label { font-weight: bold; color: #1f2937; }
        .button { display: inline-block; background-color: #2563eb; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 15px 0; }
        .footer { background-color: #f3f4f6; padding: 15px; text-align: center; font-size: 12px; color: #6b7280; border-radius: 0 0 5px 5px; border: 1px solid #e5e7eb; }
        .step { margin: 10px 0; padding-left: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Welcome to Attendance System</h1>
        </div>
        
        <div class="content">
            <p>Hello <strong>{{ $employee->name }}</strong>,</p>
            
            <p>Your employee account has been successfully created! Below are your login credentials:</p>
            
            <div class="details-box">
                <p><span class="label">Email:</span> {{ $employee->email }}</p>
                <p><span class="label">Temporary Password:</span> <code>{{ $temporaryPassword }}</code></p>
                <p><span class="label">Employee ID:</span> {{ $employee->faculty_id }}</p>
                <p><span class="label">Department:</span> {{ $employee->department?->name ?? 'N/A' }}</p>
            </div>
            
            <h3>Next Steps</h3>
            <div class="step">
                <strong>1. Log In:</strong> Use your email and temporary password to access the system
            </div>
            <div class="step">
                <strong>2. Change Password:</strong> After logging in, please change your password immediately for security
            </div>
            <div class="step">
                <strong>3. Enroll Face:</strong> Complete face biometric enrollment for attendance tracking
            </div>
            
            <center>
                <a href="{{ config('app.url') }}/login" class="button">Login to System</a>
            </center>
            
            <h3>Important Information</h3>
            <ul>
                <li>This is an automated message, please do not reply</li>
                <li>Keep your credentials secure</li>
                <li>If you did not create this account, please contact your administrator immediately</li>
            </ul>
        </div>
        
        <div class="footer">
            <p><strong>Attendance System Administration</strong></p>
            <p>{{ config('app.name') }}</p>
        </div>
    </div>
</body>
</html>
