<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification - NU Lipa</title>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        :root {
            --nu-blue: #003399;
            --nu-yellow: #FFD700;
            --nu-white: #ffffff;
            --nu-gray: #6c757d;
            --nu-light-gray: #f8f9fa;
            --nu-dark-overlay: rgba(0, 0, 0, 0.4);
            
            /* Enhanced professional color palette */
            --primary-blue: #2563eb;
            --primary-blue-hover: #1d4ed8;
            --neutral-50: #f9fafb;
            --neutral-100: #f3f4f6;
            --neutral-200: #e5e7eb;
            --neutral-300: #d1d5db;
            --neutral-400: #9ca3af;
            --neutral-500: #6b7280;
            --neutral-600: #4b5563;
            --neutral-700: #374151;
            --neutral-800: #1f2937;
            --neutral-900: #111827;
            --accent-color: #10b981;
            --error-color: #ef4444;
            --warning-color: #f59e0b;
            
            /* Spacing and sizing */
            --border-radius-sm: 0.375rem;
            --border-radius-md: 0.5rem;
            --border-radius-lg: 0.75rem;
            --border-radius-xl: 1rem;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: url('<?php echo e(asset('images/login-bg.jpg')); ?>') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            height: 100vh;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
        }

        /* Background overlay with blur */
        .bg-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--nu-dark-overlay);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            z-index: 1;
        }

        /* Main content wrapper */
        .content-wrapper {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            height: 100vh;
            overflow: hidden;
        }

        /* Header */
        .nu-header {
            background: var(--nu-blue);
            color: var(--nu-white);
            padding: 0.5rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 10;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .nu-logo-container {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .nu-shield {
            height: 2rem;
            width: auto;
        }

        .nu-title {
            font-size: 1.25rem;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .nu-welcome {
            font-size: 0.95rem;
            font-weight: 400;
        }

        /* Main content area */
        .main-content {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 5rem 1rem 3rem;
            min-height: calc(100vh - 7rem);
            overflow: hidden;
            position: relative;
            width: 100%;
        }

        /* Footer */
        .nu-footer {
            background: var(--nu-blue);
            color: var(--nu-white);
            padding: 0.75rem 1.5rem;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 10;
            font-size: 0.8rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .footer-left {
            font-weight: 600;
        }

        .footer-right {
            text-align: right;
            font-weight: 400;
        }
        
        .otp-container {
            background: var(--nu-white);
            border-radius: var(--border-radius-xl);
            box-shadow: var(--shadow-xl);
            padding: 2rem;
            max-width: 380px;
            width: 100%;
            max-width: min(380px, 90vw);
            text-align: center;
            backdrop-filter: blur(10px);
            border: 1px solid var(--neutral-200);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            margin: 0 auto;
        }
        
        .otp-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-blue), var(--accent-color));
            border-radius: var(--border-radius-xl) var(--border-radius-xl) 0 0;
        }
        
        .nu-logo {
            width: 60px;
            height: 60px;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, #2c2f92 0%, #1e2461 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 6px 15px rgba(44, 47, 146, 0.3);
            position: relative;
            z-index: 1;
        }
        
        .nu-logo i {
            font-size: 26px;
            color: white;
        }
        
        .otp-title {
            font-size: 22px;
            font-weight: 700;
            color: #2c2f92;
            margin-bottom: 6px;
            position: relative;
            z-index: 1;
        }
        
        .otp-subtitle {
            font-size: 14px;
            color: #666;
            margin-bottom: 20px;
            line-height: 1.4;
            position: relative;
            z-index: 1;
        }
        
        .user-info {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid rgba(44, 47, 146, 0.1);
            position: relative;
            z-index: 1;
        }
        
        .user-info h5 {
            color: #2c2f92;
            margin-bottom: 4px;
            font-weight: 600;
            font-size: 16px;
        }
        
        .user-info p {
            margin: 0;
            color: #555;
            font-size: 14px;
        }
        
        .otp-form {
            position: relative;
            z-index: 1;
        }
        
        .otp-input-container {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-bottom: 20px;
        }
        
        .otp-digit {
            width: 40px;
            height: 48px;
            border: 2px solid #ddd;
            border-radius: 10px;
            text-align: center;
            font-size: 20px;
            font-weight: 700;
            color: #2c2f92;
            background: white;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            outline: none;
        }
        
        .otp-digit:focus {
            border-color: #2c2f92;
            box-shadow: 0 0 0 3px rgba(44, 47, 146, 0.1);
            transform: scale(1.05);
        }
        
        .otp-digit.filled {
            border-color: #28a745;
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        }
        
        .verify-btn {
            background: linear-gradient(135deg, #2c2f92 0%, #1e2461 100%);
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            width: 100%;
            margin-bottom: 20px;
            box-shadow: 0 3px 12px rgba(44, 47, 146, 0.3);
        }
        
        .verify-btn:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(44, 47, 146, 0.4);
        }
        
        .verify-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        
        .resend-section {
            border-top: 1px solid rgba(0, 0, 0, 0.1);
            padding-top: 18px;
            position: relative;
            z-index: 1;
        }
        
        .resend-text {
            color: #666;
            font-size: 13px;
            margin-bottom: 10px;
        }
        
        .resend-btn {
            background: none;
            border: 2px solid #6c757d;
            color: #6c757d;
            padding: 8px 20px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .resend-btn:hover:not(:disabled) {
            border-color: #2c2f92;
            color: #2c2f92;
            background: rgba(44, 47, 146, 0.05);
        }
        
        .resend-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .timer {
            font-size: 14px;
            color: #dc3545;
            margin-top: 10px;
            font-weight: 600;
        }
        
        .alert {
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
            position: relative;
            z-index: 1;
        }
        
        .loading-spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 2px solid transparent;
            border-top: 2px solid currentColor;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 8px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .btn-loading .loading-spinner {
            display: inline-block;
        }
        
        /* Enhanced Responsive Design */
        @media (max-width: 768px) {
            .nu-header {
                padding: 0.75rem 1rem;
                position: fixed;
                height: auto;
            }

            .nu-logo-container {
                gap: 0.5rem;
            }

            .nu-shield {
                height: 1.75rem;
            }

            .nu-title {
                font-size: 1rem;
            }

            .nu-welcome {
                font-size: 0.8rem;
            }

            .main-content {
                padding: 4rem 0.75rem 4rem;
                min-height: calc(100vh - 8rem);
            }

            .otp-container {
                padding: 1.75rem 1.25rem;
                margin: 0 auto;
                max-width: min(340px, 90vw);
                width: 100%;
                position: relative;
            }

            .otp-title {
                font-size: 1.5rem;
                margin-bottom: 0.375rem;
            }

            .otp-subtitle {
                font-size: 0.825rem;
                margin-bottom: 1.25rem;
            }

            .nu-footer {
                padding: 0.5rem 1rem;
                font-size: 0.7rem;
                height: auto;
            }

            .footer-left {
                font-size: 0.65rem;
            }

            .footer-right {
                font-size: 0.6rem;
                margin-top: 0.25rem;
            }
        }

        @media (max-width: 480px) {
            .nu-header {
                padding: 0.5rem 0.75rem;
            }

            .nu-title {
                font-size: 0.95rem;
            }

            .nu-welcome {
                font-size: 0.75rem;
            }

            .main-content {
                padding: 4rem 0.5rem 4rem;
                min-height: calc(100vh - 8rem);
            }

            .otp-container {
                margin: 0 auto;
                padding: 1.5rem 1rem;
                max-width: min(300px, 90vw);
                width: 100%;
                position: relative;
            }

            .otp-title {
                font-size: 1.375rem;
            }

            .otp-subtitle {
                font-size: 0.775rem;
                margin-bottom: 1rem;
            }
            
            .otp-digit {
                width: 36px;
                height: 44px;
                font-size: 18px;
            }
            
            .otp-input-container {
                gap: 6px;
            }

            .nu-footer {
                padding: 0.375rem 0.5rem;
                font-size: 0.65rem;
                line-height: 1.2;
            }

            .footer-left {
                font-size: 0.6rem;
            }

            .footer-right {
                font-size: 0.55rem;
            }
        }

        @media (max-width: 360px) {
            .main-content {
                padding: 3.5rem 0.25rem 3.5rem;
            }

            .otp-container {
                margin: 0 auto;
                padding: 1.25rem 0.875rem;
                max-width: min(280px, 90vw);
                position: relative;
            }

            .otp-title {
                font-size: 1.25rem;
            }

            .otp-subtitle {
                font-size: 0.75rem;
            }
        }
    </style>
</head>
<body>
    <!-- Background overlay with blur -->
    <div class="bg-overlay"></div>
    
    <!-- Main content wrapper -->
    <div class="content-wrapper">
        <!-- Header -->
        <header class="nu-header">
            <div class="nu-logo-container">
                <img src="<?php echo e(asset('images/NU_shield.svg.png')); ?>" alt="NU Shield" class="nu-shield">
                <span class="nu-title">NU LIPA</span>
            </div>
            <span class="nu-welcome">Welcome to NU Lipa</span>
        </header>

        <!-- Main content area -->
        <main class="main-content">
            <div class="otp-container">
        <div class="nu-logo">
            <i class="bi bi-shield-lock"></i>
        </div>
        
        <h1 class="otp-title">Two-Factor Authentication</h1>
        <p class="otp-subtitle">We've sent a 6-digit verification code to your email address to ensure account security.</p>
        
        <div class="user-info">
            <h5><i class="bi bi-person-circle me-2"></i><?php echo e(Auth::user()->full_name); ?></h5>
            <p><i class="bi bi-envelope me-2"></i><?php echo e(Auth::user()->personal_email); ?></p>
        </div>
        
        <div id="alertContainer"></div>
        
        <form id="otpForm" class="otp-form">
            <div class="otp-input-container">
                <input type="text" class="otp-digit" maxlength="1" data-index="0" autocomplete="off">
                <input type="text" class="otp-digit" maxlength="1" data-index="1" autocomplete="off">
                <input type="text" class="otp-digit" maxlength="1" data-index="2" autocomplete="off">
                <input type="text" class="otp-digit" maxlength="1" data-index="3" autocomplete="off">
                <input type="text" class="otp-digit" maxlength="1" data-index="4" autocomplete="off">
                <input type="text" class="otp-digit" maxlength="1" data-index="5" autocomplete="off">
            </div>
            
            <button type="submit" class="verify-btn" id="verifyBtn">
                <span class="loading-spinner"></span>
                Verify & Continue
            </button>
        </form>
        
        <div class="resend-section">
            <p class="resend-text">Didn't receive the code?</p>
            <button type="button" class="resend-btn" id="resendBtn">
                <span class="loading-spinner"></span>
                Resend Code
            </button>
            <div class="timer" id="resendTimer" style="display: none;"></div>
        </div>
    </div>
        </main>

        <!-- Footer -->
        <footer class="nu-footer">
            <div class="footer-left">
                NU ONLINE SERVICES • All Rights Reserved • National University
            </div>
            <div class="footer-right">
                NU Bldg, SM City Lipa, JP Laurel Highway, Lipa City, Batangas
            </div>
        </footer>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const otpInputs = document.querySelectorAll('.otp-digit');
            const verifyBtn = document.getElementById('verifyBtn');
            const resendBtn = document.getElementById('resendBtn');
            const resendTimer = document.getElementById('resendTimer');
            const otpForm = document.getElementById('otpForm');
            const alertContainer = document.getElementById('alertContainer');
            
            let resendCooldown = 0;
            let timerInterval;
            
            // Auto-send OTP when page loads
            sendOTP();
            
            // OTP Input Handling
            otpInputs.forEach((input, index) => {
                input.addEventListener('input', (e) => {
                    const value = e.target.value;
                    
                    // Only allow digits
                    if (!/^\d*$/.test(value)) {
                        e.target.value = '';
                        return;
                    }
                    
                    // Update visual state
                    if (value) {
                        e.target.classList.add('filled');
                        // Move to next input
                        if (index < otpInputs.length - 1) {
                            otpInputs[index + 1].focus();
                        }
                    } else {
                        e.target.classList.remove('filled');
                    }
                    
                    // Auto-submit when all digits are filled
                    const allFilled = Array.from(otpInputs).every(input => input.value.length === 1);
                    if (allFilled) {
                        setTimeout(() => {
                            otpForm.dispatchEvent(new Event('submit'));
                        }, 300);
                    }
                });
                
                input.addEventListener('keydown', (e) => {
                    // Handle backspace
                    if (e.key === 'Backspace' && !e.target.value && index > 0) {
                        otpInputs[index - 1].focus();
                        otpInputs[index - 1].value = '';
                        otpInputs[index - 1].classList.remove('filled');
                    }
                    
                    // Handle paste
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        otpForm.dispatchEvent(new Event('submit'));
                    }
                });
                
                // Handle paste event
                input.addEventListener('paste', (e) => {
                    e.preventDefault();
                    const pastedData = e.clipboardData.getData('text').replace(/\D/g, '');
                    
                    if (pastedData.length === 6) {
                        otpInputs.forEach((inp, idx) => {
                            inp.value = pastedData[idx] || '';
                            if (inp.value) {
                                inp.classList.add('filled');
                            }
                        });
                        
                        setTimeout(() => {
                            otpForm.dispatchEvent(new Event('submit'));
                        }, 300);
                    }
                });
            });
            
            // Form submission
            otpForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const otp = Array.from(otpInputs).map(input => input.value).join('');
                
                if (otp.length !== 6) {
                    showAlert('Please enter all 6 digits of the verification code.', 'warning');
                    return;
                }
                
                verifyOTP(otp);
            });
            
            // Resend button
            resendBtn.addEventListener('click', function() {
                if (resendCooldown === 0) {
                    sendOTP();
                }
            });
            
            function verifyOTP(otp) {
                setButtonLoading(verifyBtn, true);
                
                fetch('/student/2fa/verify-otp', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ otp: otp })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('Verification successful! Redirecting...', 'success');
                        setTimeout(() => {
                            window.location.href = data.redirect || '/student/dashboard';
                        }, 1500);
                    } else {
                        showAlert(data.message, 'error');
                        clearOTPInputs();
                        otpInputs[0].focus();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('Verification failed. Please try again.', 'error');
                    clearOTPInputs();
                })
                .finally(() => {
                    setButtonLoading(verifyBtn, false);
                });
            }
            
            function sendOTP() {
                setButtonLoading(resendBtn, true);
                
                fetch('/student/2fa/send-otp', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('Verification code sent to your email.', 'success');
                        startResendTimer();
                    } else {
                        showAlert(data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('Failed to send verification code. Please try again.', 'error');
                })
                .finally(() => {
                    setButtonLoading(resendBtn, false);
                });
            }
            
            function startResendTimer() {
                resendCooldown = 60; // 60 seconds cooldown
                resendBtn.disabled = true;
                resendTimer.style.display = 'block';
                
                timerInterval = setInterval(() => {
                    resendTimer.textContent = `You can resend code in ${resendCooldown} seconds`;
                    resendCooldown--;
                    
                    if (resendCooldown < 0) {
                        clearInterval(timerInterval);
                        resendBtn.disabled = false;
                        resendTimer.style.display = 'none';
                        resendCooldown = 0;
                    }
                }, 1000);
            }
            
            function clearOTPInputs() {
                otpInputs.forEach(input => {
                    input.value = '';
                    input.classList.remove('filled');
                });
            }
            
            function setButtonLoading(button, loading) {
                if (loading) {
                    button.classList.add('btn-loading');
                    button.disabled = true;
                } else {
                    button.classList.remove('btn-loading');
                    button.disabled = false;
                }
            }
            
            function showAlert(message, type = 'info') {
                // Remove existing alerts
                alertContainer.innerHTML = '';
                
                const alertClass = {
                    'success': 'alert-success',
                    'error': 'alert-danger',
                    'warning': 'alert-warning',
                    'info': 'alert-info'
                }[type] || 'alert-info';
                
                const alertDiv = document.createElement('div');
                alertDiv.className = `alert ${alertClass}`;
                alertDiv.textContent = message;
                
                alertContainer.appendChild(alertDiv);
                
                // Auto-remove success and info alerts after 5 seconds
                if (type === 'success' || type === 'info') {
                    setTimeout(() => {
                        if (alertDiv.parentElement) {
                            alertDiv.remove();
                        }
                    }, 5000);
                }
            }
            
            // Focus first input on load
            otpInputs[0].focus();
        });
    </script>
</body>
</html><?php /**PATH D:\Nu-Regisv2\resources\views/auth/verify-otp.blade.php ENDPATH**/ ?>