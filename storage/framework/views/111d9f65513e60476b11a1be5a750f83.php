<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NU Lipa - OTP Verification</title>
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
            background-color: #f8f9fa;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        
        .header {
            background: linear-gradient(135deg, #2c2f92 0%, #1e2461 100%);
            padding: 30px 40px;
            text-align: center;
            color: white;
            position: relative;
        }
        
        .header h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        .header p {
            font-size: 16px;
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
        
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
            color: #2c2f92;
            font-weight: 600;
        }
        
        .message {
            font-size: 16px;
            margin-bottom: 30px;
            color: #555;
            line-height: 1.7;
        }
        
        .otp-container {
            text-align: center;
            margin: 40px 0;
            padding: 30px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 12px;
            border: 2px dashed #2c2f92;
        }
        
        .otp-label {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 1px;
        }
        
        .otp-code {
            font-size: 36px;
            font-weight: 800;
            color: #2c2f92;
            letter-spacing: 8px;
            font-family: 'Courier New', monospace;
            text-shadow: 0 2px 4px rgba(44, 47, 146, 0.2);
            margin-bottom: 15px;
        }
        
        .otp-note {
            font-size: 13px;
            color: #777;
            font-style: italic;
        }
        
        .security-notice {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 20px;
            margin: 30px 0;
        }
        
        .security-notice h3 {
            color: #856404;
            font-size: 16px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }
        
        .security-notice h3::before {
            content: "⚠️";
            margin-right: 8px;
        }
        
        .security-notice p {
            color: #856404;
            font-size: 14px;
            margin: 5px 0;
        }
        
        .footer {
            background-color: #f8f9fa;
            padding: 30px 40px;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }
        
        .footer p {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
        }
        
        .footer .signature {
            font-weight: 600;
            color: #2c2f92;
            margin-top: 20px;
        }
        
        .expiry-info {
            background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
            border-radius: 8px;
            padding: 15px;
            margin: 25px 0;
            text-align: center;
        }
        
        .expiry-info .timer-icon {
            font-size: 20px;
            margin-right: 8px;
        }
        
        .expiry-info p {
            color: #444;
            font-size: 14px;
            margin: 0;
        }
        
        @media (max-width: 600px) {
            .email-container {
                margin: 10px;
                border-radius: 8px;
            }
            
            .header, .content, .footer {
                padding: 20px;
            }
            
            .otp-code {
                font-size: 28px;
                letter-spacing: 4px;
            }
            
            .header h1 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <img src="<?php echo e(asset('images/NU_shield.svg.png')); ?>" alt="NU Logo" class="logo">
            <h1>NU LIPA</h1>
            <p>Student Portal - Two Factor Authentication</p>
        </div>
        
        <!-- Content -->
        <div class="content">
            <div class="greeting">
                Hello <?php echo e($userName); ?>,
            </div>
            
            <div class="message">
                You have requested to log in to your NU Lipa Student Portal account with two-factor authentication enabled. 
                Please use the verification code below to complete your login process.
            </div>
            
            <div class="otp-container">
                <div class="otp-label">Your Verification Code</div>
                <div class="otp-code"><?php echo e($otpCode); ?></div>
                <div class="otp-note">Enter this code in your browser to continue</div>
            </div>
            
            <div class="expiry-info">
                <p>
                    <span class="timer-icon">⏰</span>
                    This code will expire in <strong>10 minutes</strong> for your security.
                </p>
            </div>
            
            <div class="security-notice">
                <h3>Security Notice</h3>
                <p>• Do not share this code with anyone</p>
                <p>• NU Lipa staff will never ask for your verification code</p>
                <p>• If you did not request this code, please ignore this email</p>
                <p>• For security concerns, contact the registrar office immediately</p>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p>This is an automated message from NU Lipa Student Portal.</p>
            <p>For technical support, please contact your system administrator.</p>
            
            <div class="signature">
                Best regards,<br>
                NU Lipa IT Services
            </div>
        </div>
    </div>
</body>
</html><?php /**PATH D:\Nu-Regisv2\resources\views\emails\otp.blade.php ENDPATH**/ ?>