<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset - NU Lipa Document Request</title>
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f9fafb;
            margin: 0;
            padding: 0;
            color: #374151;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #003399 0%, #2563eb 100%);
            color: #ffffff;
            padding: 30px 40px;
            text-align: center;
            position: relative;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }
        .header p {
            margin: 10px 0 0 0;
            font-size: 14px;
            opacity: 0.9;
        }
        .logo {
            position: absolute;
            top: 20px;
            left: 20px;
            width: 60px;
            height: 60px;
            z-index: 2;
        }
        .content {
            padding: 40px;
        }
        .content h2 {
            color: #1f2937;
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 20px;
        }
        .content p {
            line-height: 1.6;
            margin-bottom: 20px;
        }
        .reset-button {
            display: inline-block;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #ffffff;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 16px;
            margin: 20px 0;
            transition: all 0.2s ease;
        }
        .reset-button:hover {
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
            transform: translateY(-1px);
        }
        .warning {
            background-color: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
        }
        .warning p {
            margin: 0;
            color: #92400e;
            font-size: 14px;
        }
        .footer {
            background-color: #f9fafb;
            padding: 20px 40px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        .footer p {
            margin: 0;
            font-size: 12px;
            color: #6b7280;
        }
        .footer a {
            color: #2563eb;
            text-decoration: none;
        }
        .footer a:hover {
            text-decoration: underline;
        }
        @media (max-width: 600px) {
            .container {
                margin: 10px;
            }
            .header, .content, .footer {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="<?php echo e(asset('images/NU_shield.svg.png')); ?>" alt="NU Logo" class="logo">
            <h1>NU Lipa</h1>
            <p>Document Request System</p>
        </div>

        <div class="content">
            <h2>Password Reset Request</h2>

            <p>Hello,</p>

            <p>You have requested to reset your password for your NU Lipa Document Request account. If you did not make this request, please ignore this email.</p>

            <p>To reset your password, click the button below:</p>

            <a href="<?php echo e($resetUrl); ?>" class="reset-button">Reset Password</a>

            <div class="warning">
                <p><strong>Security Notice:</strong> This password reset link will expire in 24 hours for your security. If you did not request this reset, please contact our support team immediately.</p>
            </div>

            <p>If the button above doesn't work, you can request a new password reset link from the login page.</p>

            <p>Thank you for using NU Lipa Document Request System.</p>

            <p>Best regards,<br>NU Lipa IT Support Team</p>
        </div>

        <div class="footer">
            <p>If you have any questions, please contact piquizon@nu-lipa.edu.ph.</p>
            <p>&copy; 2025 National University - Lipa. All rights reserved.</p>
        </div>
    </div>
</body>
</html><?php /**PATH D:\Nu-Regisv2\resources\views\emails\reset-password.blade.php ENDPATH**/ ?>