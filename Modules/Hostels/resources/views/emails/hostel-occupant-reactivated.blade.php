<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Reactivated - Hostel Portal</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .content {
            padding: 30px;
        }
        .welcome-back {
            background-color: #e8f5e8;
            border-left: 4px solid #4caf50;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .credentials {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 20px 0;
        }
        .features {
            background-color: #f0f4f8;
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
        }
        .features ul {
            margin: 0;
            padding-left: 20px;
        }
        .features li {
            margin-bottom: 8px;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
            border-top: 1px solid #e9ecef;
        }
        .security-note {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Welcome Back to Your Hostel Portal!</h1>
        </div>
        
        <div class="content">
            <div class="welcome-back">
                <strong>🎉 Your account has been reactivated successfully!</strong>
                <p>We're excited to have you back for another stay.</p>
            </div>
            
            <p>Hello {{ $hostelOccupant->first_name }},</p>
            
            <p>Your hostel portal account has been reactivated for your new booking. You can continue to use your existing credentials to access your account.</p>
            
            <div class="credentials">
                <p><strong>📧 Email:</strong> {{ $email }}</p>
                <p><strong>🔗 Portal URL:</strong> {{ $loginUrl }}</p>
            </div>
            
            <p style="text-align: center;">
                <a href="{{ $loginUrl }}" class="button">Login to Your Portal</a>
            </p>
            
            <div class="security-note">
                <strong>🔒 Security Reminder:</strong>
                <p>If you don't remember your password, you can use the "Forgot Password" feature on the login page to reset it.</p>
            </div>
            
            <div class="features">
                <p><strong>📋 What you can access in your portal:</strong></p>
                <ul>
                    <li>View all your current and past bookings</li>
                    <li>Access your complete booking history</li>
                    <li>Submit maintenance requests</li>
                    <li>Update your profile information</li>
                    <li>View billing and payment history across all stays</li>
                    <li>Communicate with hostel management</li>
                </ul>
            </div>
            
            <p>Having all your stays in one account makes it easier to manage your hostel experience and access your complete history.</p>
            
            <p>If you have any questions or need assistance, please contact the hostel administration.</p>
        </div>
        
        <div class="footer">
            <p>This is an automated message. Please do not reply to this email.</p>
            <p>&copy; {{ date('Y') }} Hostel Management System. All rights reserved.</p>
        </div>
    </div>
</body>
</html>