<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hostel Portal Access</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #4a90e2; color: white; padding: 20px; text-align: center; }
        .content { background: #f9f9f9; padding: 20px; border-radius: 0 0 5px 5px; }
        .credentials { background: #fff; padding: 15px; border: 1px solid #ddd; border-radius: 5px; margin: 15px 0; }
        .button { background: #4a90e2; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; }
        .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Welcome to Your Hostel Portal</h1>
        </div>
        
        <div class="content">
            <p>Hello {{ $hostelOccupant->first_name }},</p>
            
            <p>Your hostel portal account has been created successfully. You can now access your account using the credentials below:</p>
            
            <div class="credentials">
                <p><strong>Email:</strong> {{ $email }}</p>
                <p><strong>Password:</strong> {{ $password }}</p>
                <p><strong>Portal URL:</strong> {{ $loginUrl }}</p>
            </div>
            
            <p>For security reasons, we recommend changing your password after your first login.</p>
            
            <p style="text-align: center;">
                <a href="{{ $loginUrl }}" class="button">Login to Portal</a>
            </p>
            
            <p><strong>What you can do in the portal:</strong></p>
            <ul>
                <li>View and manage your bookings</li>
                <li>Submit maintenance requests</li>
                <li>Update your profile information</li>
                <li>Communicate with hostel management</li>
                <li>View billing and payment history</li>
            </ul>
            
            <p>If you have any questions or need assistance, please contact the hostel administration.</p>
        </div>
        
        <div class="footer">
            <p>This is an automated message. Please do not reply to this email.</p>
            <p>&copy; {{ date('Y') }} Hostel Management System. All rights reserved.</p>
        </div>
    </div>
</body>
</html>