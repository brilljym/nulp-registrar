<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NU Lipa - Your Account Credentials</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .email-container {
            max-width: 650px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .header {
            background: linear-gradient(135deg, #2c2f92 0%, #1e2461 100%);
            padding: 40px 30px;
            text-align: center;
            color: white;
            position: relative;
        }

        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.05"/><circle cx="10" cy="50" r="0.5" fill="white" opacity="0.05"/><circle cx="90" cy="30" r="0.5" fill="white" opacity="0.05"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }

        .header-content {
            position: relative;
            z-index: 1;
        }

        .header h1 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 8px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
            letter-spacing: -0.5px;
        }

        .header p {
            font-size: 18px;
            opacity: 0.95;
            font-weight: 300;
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
            padding: 50px 40px;
        }

        .welcome-section {
            text-align: center;
            margin-bottom: 40px;
        }

        .welcome-message {
            font-size: 24px;
            font-weight: 600;
            color: #2c2f92;
            margin-bottom: 10px;
        }

        .welcome-text {
            font-size: 16px;
            color: #6c757d;
            line-height: 1.7;
        }

        .credentials-card {
            background: linear-gradient(135deg, #f8f9ff 0%, #e8f2ff 100%);
            border: 2px solid #e3f2fd;
            border-radius: 16px;
            padding: 35px;
            margin: 35px 0;
            position: relative;
            box-shadow: 0 8px 25px rgba(44, 47, 146, 0.1);
        }

        .credentials-card::before {
            content: '🔐';
            position: absolute;
            top: -15px;
            left: 30px;
            background: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .credentials-title {
            font-size: 20px;
            font-weight: 600;
            color: #2c2f92;
            margin-bottom: 25px;
            margin-left: 50px;
        }

        .credential-item {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            padding: 15px 20px;
            background: white;
            border-radius: 10px;
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .credential-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .credential-item:last-child {
            margin-bottom: 0;
        }

        .credential-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            font-size: 18px;
        }

        .email-icon {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .password-icon {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }

        .role-icon {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
        }

        .credential-content {
            flex: 1;
        }

        .credential-label {
            font-weight: 600;
            color: #495057;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }

        .credential-value {
            font-family: 'SF Mono', 'Monaco', 'Inconsolata', 'Roboto Mono', monospace;
            font-size: 16px;
            font-weight: 500;
            color: #2c2f92;
            background: #f8f9fa;
            padding: 8px 12px;
            border-radius: 6px;
            border: 1px solid #e9ecef;
            word-break: break-all;
        }

        .action-section {
            text-align: center;
            margin: 40px 0;
        }

        .login-button {
            display: inline-block;
            background: linear-gradient(135deg, #2c2f92 0%, #1e2461 100%);
            color: white;
            text-decoration: none;
            padding: 16px 32px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 16px;
            box-shadow: 0 8px 25px rgba(44, 47, 146, 0.3);
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }

        .login-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 35px rgba(44, 47, 146, 0.4);
        }

        .security-alert {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
            border: 2px solid #ffc107;
            border-radius: 12px;
            padding: 25px;
            margin: 30px 0;
            position: relative;
        }

        .security-alert::before {
            content: '⚠️';
            position: absolute;
            top: -12px;
            left: 25px;
            background: white;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            border: 2px solid #ffc107;
        }

        .security-alert-title {
            color: #856404;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 12px;
            margin-left: 45px;
        }

        .security-alert-text {
            color: #856404;
            margin: 0;
            line-height: 1.6;
        }

        .security-tips {
            background: linear-gradient(135deg, #e7f3ff 0%, #b3d9ff 100%);
            border: 2px solid #4dabf7;
            border-radius: 12px;
            padding: 25px;
            margin: 30px 0;
        }

        .security-tips-title {
            color: #0056b3;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }

        .security-tips-title::before {
            content: '🛡️';
            margin-right: 10px;
        }

        .security-tips ul {
            color: #0056b3;
            padding-left: 20px;
            margin: 0;
        }

        .security-tips li {
            margin-bottom: 8px;
            line-height: 1.5;
        }

        .support-section {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 25px;
            margin: 30px 0;
            text-align: center;
        }

        .support-title {
            color: #2c2f92;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .support-text {
            color: #6c757d;
            margin-bottom: 15px;
        }

        .contact-info {
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
        }

        .contact-item {
            display: flex;
            align-items: center;
            color: #495057;
            font-size: 14px;
        }

        .contact-item::before {
            content: '📧';
            margin-right: 8px;
            font-size: 16px;
        }

        .contact-item.email::before {
            content: '📧';
        }

        .contact-item.phone::before {
            content: '📞';
        }

        .footer {
            background: linear-gradient(135deg, #2c2f92 0%, #1e2461 100%);
            padding: 30px 40px;
            text-align: center;
            color: white;
        }

        .footer-content {
            max-width: 500px;
            margin: 0 auto;
        }

        .footer p {
            font-size: 14px;
            opacity: 0.9;
            margin-bottom: 10px;
        }

        .footer .signature {
            font-weight: 600;
            opacity: 1;
            font-size: 16px;
        }

        .footer .automated {
            font-style: italic;
            opacity: 0.8;
        }

        @media (max-width: 768px) {
            body {
                padding: 10px;
            }

            .email-container {
                margin: 0;
            }

            .header {
                padding: 30px 20px;
            }

            .header h1 {
                font-size: 28px;
            }

            .content {
                padding: 30px 20px;
            }

            .welcome-message {
                font-size: 20px;
            }

            .credentials-card {
                padding: 25px;
                margin: 25px 0;
            }

            .credential-item {
                flex-direction: column;
                align-items: flex-start;
                padding: 15px;
            }

            .credential-icon {
                margin-bottom: 10px;
                margin-right: 0;
            }

            .credential-content {
                width: 100%;
            }

            .contact-info {
                flex-direction: column;
                gap: 10px;
            }

            .login-button {
                padding: 14px 28px;
                font-size: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <img src="{{ asset('images/NU_shield.svg.png') }}" alt="NU Logo" class="logo">
            <div class="header-content">
                <h1>NU Lipa</h1>
                <p>Your Account Has Been Created</p>
            </div>
        </div>

        <div class="content">
            <div class="welcome-section">
                <div class="welcome-message">
                    Welcome to NU Lipa, {{ $user->first_name }} {{ $user->last_name }}!
                </div>
                <div class="welcome-text">
                    Your account has been successfully created by an administrator. Below are your secure login credentials.
                </div>
            </div>

            <div class="credentials-card">
                <div class="credentials-title">Your Login Credentials</div>

                <div class="credential-item">
                    <div class="credential-icon email-icon">📧</div>
                    <div class="credential-content">
                        <div class="credential-label">School Email</div>
                        <div class="credential-value">{{ $user->school_email }}</div>
                    </div>
                </div>

                <div class="credential-item">
                    <div class="credential-icon password-icon">🔑</div>
                    <div class="credential-content">
                        <div class="credential-label">Password</div>
                        <div class="credential-value">{{ $password }}</div>
                    </div>
                </div>

                <div class="credential-item">
                    <div class="credential-icon role-icon">👤</div>
                    <div class="credential-content">
                        <div class="credential-label">Role</div>
                        <div class="credential-value">{{ ucfirst($user->role->name ?? 'User') }}</div>
                    </div>
                </div>
            </div>

            <div class="action-section">
                <a href="#" class="login-button">Access Your Account</a>
                <p style="color: #6c757d; font-size: 14px; margin: 0;">
                    Please bookmark the login page for easy access
                </p>
            </div>

            <div class="security-alert">
                <div class="security-alert-title">Important Security Notice</div>
                <div class="security-alert-text">
                    <strong>Please change your password immediately after your first login</strong> for security purposes. Keep your credentials secure and do not share them with anyone.
                </div>
            </div>

            <div class="security-tips">
                <div class="security-tips-title">Security Best Practices</div>
                <ul>
                    <li>Use a strong, unique password for your account</li>
                    <li>Enable two-factor authentication when available</li>
                    <li>Never share your login credentials with others</li>
                    <li>Log out when using public or shared computers</li>
                    <li>Report any suspicious activity to IT Support immediately</li>
                </ul>
            </div>

            <div class="support-section">
                <div class="support-title">Need Help?</div>
                <div class="support-text">
                    If you have any questions or need assistance with your account, please don't hesitate to contact our support team.
                </div>
                <div class="contact-info">
                    <div class="contact-item email">it.support@nulipa.edu.ph</div>
                    <div class="contact-item phone">(043) 123-4567</div>
                </div>
            </div>

            <div style="text-align: center; color: #6c757d; font-size: 16px; margin: 30px 0;">
                Best regards,<br>
                <strong style="color: #2c2f92;">NU Lipa Administration Team</strong>
            </div>
        </div>

        <div class="footer">
            <div class="footer-content">
                <p class="automated">This is an automated message. Please do not reply to this email.</p>
                <p class="signature">NU Lipa - Office of the Registrar</p>
            </div>
        </div>
    </div>
</body>
</html>
        .welcome-message {
            font-size: 18px;
            margin-bottom: 30px;
            color: #2c2f92;
            font-weight: 600;
        }

        .credentials-box {
            background-color: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 25px;
            margin: 25px 0;
        }

        .credential-item {
            margin-bottom: 15px;
            padding: 10px 0;
            border-bottom: 1px solid #dee2e6;
        }

        .credential-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }

        .credential-label {
            font-weight: 600;
            color: #495057;
            display: inline-block;
            min-width: 120px;
        }

        .credential-value {
            font-family: 'Courier New', monospace;
            background-color: #ffffff;
            padding: 5px 10px;
            border-radius: 4px;
            border: 1px solid #ced4da;
            font-weight: 500;
            color: #2c2f92;
        }

        .important-note {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
        }

        .important-note h3 {
            color: #856404;
            font-size: 16px;
            margin-bottom: 10px;
        }

        .important-note p {
            color: #856404;
            margin: 0;
        }

        .footer {
            background-color: #f8f9fa;
            padding: 30px 40px;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }

        .footer p {
            color: #6c757d;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .footer .signature {
            font-weight: 600;
            opacity: 1;
            font-size: 16px;
        }

        .footer .automated {
            font-style: italic;
            opacity: 0.8;
        }

        @media (max-width: 768px) {
            body {
                padding: 10px;
            }

            .email-container {
                margin: 0;
            }

            .header {
                padding: 30px 20px;
            }

            .header h1 {
                font-size: 28px;
            }

            .content {
                padding: 30px 20px;
            }

            .welcome-message {
                font-size: 20px;
            }

            .credentials-card {
                padding: 25px;
                margin: 25px 0;
            }

            .credential-item {
                flex-direction: column;
                align-items: flex-start;
                padding: 15px;
            }

            .credential-icon {
                margin-bottom: 10px;
                margin-right: 0;
            }

            .credential-content {
                width: 100%;
            }

            .contact-info {
                flex-direction: column;
                gap: 10px;
            }

            .login-button {
                padding: 14px 28px;
                font-size: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <img src="{{ asset('images/NU_shield.svg.png') }}" alt="NU Logo" class="logo">
            <div class="header-content">
                <h1>NU Lipa</h1>
                <p>Your Account Has Been Created</p>
            </div>
        </div>

        <div class="content">
            <div class="welcome-section">
                <div class="welcome-message">
                    Welcome to NU Lipa, {{ $user->first_name }} {{ $user->last_name }}!
                </div>
                <div class="welcome-text">
                    Your account has been successfully created by an administrator. Below are your secure login credentials.
                </div>
            </div>

            <div class="credentials-card">
                <div class="credentials-title">Your Login Credentials</div>

                <div class="credential-item">
                    <div class="credential-icon email-icon">📧</div>
                    <div class="credential-content">
                        <div class="credential-label">School Email</div>
                        <div class="credential-value">{{ $user->school_email }}</div>
                    </div>
                </div>

                <div class="credential-item">
                    <div class="credential-icon password-icon">🔑</div>
                    <div class="credential-content">
                        <div class="credential-label">Password</div>
                        <div class="credential-value">{{ $password }}</div>
                    </div>
                </div>

                <div class="credential-item">
                    <div class="credential-icon role-icon">👤</div>
                    <div class="credential-content">
                        <div class="credential-label">Role</div>
                        <div class="credential-value">{{ ucfirst($user->role->name ?? 'User') }}</div>
                    </div>
                </div>
            </div>

            <div class="action-section">
                <a href="#" class="login-button">Access Your Account</a>
                <p style="color: #6c757d; font-size: 14px; margin: 0;">
                    Please bookmark the login page for easy access
                </p>
            </div>

            <div class="security-alert">
                <div class="security-alert-title">Important Security Notice</div>
                <div class="security-alert-text">
                    <strong>Please change your password immediately after your first login</strong> for security purposes. Keep your credentials secure and do not share them with anyone.
                </div>
            </div>

            <div class="security-tips">
                <div class="security-tips-title">Security Best Practices</div>
                <ul>
                    <li>Use a strong, unique password for your account</li>
                    <li>Enable two-factor authentication when available</li>
                    <li>Never share your login credentials with others</li>
                    <li>Log out when using public or shared computers</li>
                    <li>Report any suspicious activity to IT Support immediately</li>
                </ul>
            </div>

            <div class="support-section">
                <div class="support-title">Need Help?</div>
                <div class="support-text">
                    If you have any questions or need assistance with your account, please don't hesitate to contact our support team.
                </div>
                <div class="contact-info">
                    <div class="contact-item email">it.support@nulipa.edu.ph</div>
                    <div class="contact-item phone">(043) 123-4567</div>
                </div>
            </div>

            <div style="text-align: center; color: #6c757d; font-size: 16px; margin: 30px 0;">
                Best regards,<br>
                <strong style="color: #2c2f92;">NU Lipa Administration Team</strong>
            </div>
        </div>

        <div class="footer">
            <div class="footer-content">
                <p class="automated">This is an automated message. Please do not reply to this email.</p>
                <p class="signature">NU Lipa - Office of the Registrar</p>
            </div>
        </div>
    </div>
</body>
</html>

