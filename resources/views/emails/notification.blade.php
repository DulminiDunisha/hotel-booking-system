<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #1f2937;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f9fafb;
            padding: 30px;
            border-radius: 0 0 8px 8px;
            border: 1px solid #e5e7eb;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            color: #6b7280;
            font-size: 14px;
        }
        .button {
            display: inline-block;
            background-color: #3b82f6;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
        }
        .info-box {
            background-color: white;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Dumidu Hotel</h1>
        <p>{{ $title }}</p>
    </div>
    
    <div class="content">
        <p>Dear {{ $user->name }},</p>
        
        <div class="info-box">
            {!! nl2br(e($message)) !!}
        </div>
        
        <p>If you have any questions or need assistance, please don't hesitate to contact us:</p>
        <ul>
            <li>Phone: +94 11 234 5678</li>
            <li>Email: info@dumiduhotel.com</li>
            <li>Website: www.dumiduhotel.com</li>
        </ul>
        
        <p>Thank you for choosing Dumidu Hotel!</p>
        
        <p>Best regards,<br>
        Dumidu Hotel Team</p>
    </div>
    
    <div class="footer">
        <p>This is an automated message. Please do not reply to this email.</p>
        <p>&copy; {{ date('Y') }} Dumidu Hotel. All rights reserved.</p>
    </div>
</body>
</html>
