<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NU Document Request - NU Lipa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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

        /* Centered card */
        .document-request-card {
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

        .document-request-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-blue), var(--accent-color));
            border-radius: var(--border-radius-xl) var(--border-radius-xl) 0 0;
        }

        .card-title {
            color: var(--neutral-800);
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            letter-spacing: -0.025em;
            line-height: 1.2;
        }

        .card-subtitle {
            color: var(--neutral-500);
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
            font-weight: 400;
        }

        /* Enhanced Buttons */
        .btn-login {
            background: var(--primary-blue);
            color: var(--nu-white);
            border: 2px solid var(--primary-blue);
            border-radius: var(--border-radius-lg);
            padding: 0.875rem 1.5rem;
            font-size: 1rem;
            font-weight: 600;
            width: 100%;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            cursor: pointer;
            text-decoration: none;
            outline: none;
            position: relative;
            overflow: hidden;
        }

        .btn-login:hover {
            background: var(--primary-blue-hover);
            border-color: var(--primary-blue-hover);
            transform: translateY(-1px);
            box-shadow: var(--shadow-lg);
            color: var(--nu-white);
        }

        .btn-login:focus {
            outline: 2px solid var(--primary-blue);
            outline-offset: 2px;
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .btn-walkin {
            background: var(--neutral-50);
            color: var(--neutral-700);
            border: 2px solid var(--neutral-200);
            border-radius: var(--border-radius-lg);
            padding: 0.875rem 1.5rem;
            font-size: 1rem;
            font-weight: 600;
            width: 100%;
            margin-bottom: 0.75rem;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .btn-walkin:hover {
            background: var(--neutral-100);
            color: var(--neutral-800);
            border-color: var(--neutral-300);
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
            text-decoration: none;
        }

        .btn-walkin:focus {
            outline: 2px solid var(--neutral-400);
            outline-offset: 2px;
        }

        .no-login-text {
            color: var(--neutral-400);
            font-size: 0.825rem;
            margin-bottom: 0;
            font-weight: 400;
        }

        .divider {
            margin: 1.25rem 0;
            position: relative;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: var(--neutral-200);
        }

        .divider-text {
            background: var(--nu-white);
            color: var(--neutral-400);
            padding: 0 1rem;
            font-size: 0.875rem;
            position: relative;
            font-weight: 500;
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

        /* Enhanced Form Styles */
        .form-label {
            color: var(--neutral-700);
            font-weight: 600;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-control {
            border-radius: var(--border-radius-lg);
            border: 2px solid var(--neutral-200);
            padding: 0.875rem 1rem;
            font-size: 1rem;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            background: var(--neutral-50);
            color: var(--neutral-800);
        }

        .form-control::placeholder {
            color: var(--neutral-400);
            font-weight: 400;
        }

        .form-control:focus {
            border-color: var(--primary-blue);
            background: var(--nu-white);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            outline: none;
        }

        .form-control:hover:not(:focus) {
            border-color: var(--neutral-300);
        }

        /* Enhanced Alert Styles */
        .alert-danger {
            border-radius: var(--border-radius-lg);
            border: none;
            background: rgba(239, 68, 68, 0.1);
            color: var(--error-color);
            margin-bottom: 1.5rem;
            padding: 1rem;
            font-weight: 500;
            border-left: 4px solid var(--error-color);
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
                overflow: hidden;
                display: flex;
                align-items: center;
                justify-content: center;
                position: relative;
                width: 100%;
            }

            .document-request-card {
                padding: 1.75rem 1.25rem;
                margin: 0 auto;
                max-width: min(340px, 90vw);
                width: 100%;
                position: relative;
            }

            .card-title {
                font-size: 1.5rem;
                margin-bottom: 0.375rem;
            }

            .card-subtitle {
                font-size: 0.825rem;
                margin-bottom: 1.25rem;
            }

            .btn-login,
            .btn-walkin {
                padding: 0.75rem 1rem;
                font-size: 0.9rem;
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
                display: flex;
                align-items: center;
                justify-content: center;
                position: relative;
                width: 100%;
            }

            .document-request-card {
                margin: 0 auto;
                padding: 1.5rem 1rem;
                max-width: min(300px, 90vw);
                width: 100%;
                position: relative;
            }

            .card-title {
                font-size: 1.375rem;
            }

            .card-subtitle {
                font-size: 0.775rem;
                margin-bottom: 1rem;
            }

            .btn-walkin,
            .btn-login {
                padding: 0.65rem 0.875rem;
                font-size: 0.875rem;
            }

            .divider {
                margin: 1.25rem 0;
            }

            .form-control {
                padding: 0.75rem 0.875rem;
                font-size: 0.9rem;
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
                display: flex;
                align-items: center;
                justify-content: center;
                position: relative;
                width: 100%;
            }

            .document-request-card {
                margin: 0 auto;
                padding: 1.25rem 0.875rem;
                max-width: min(280px, 90vw);
                position: relative;
            }

            .card-title {
                font-size: 1.25rem;
            }

            .card-subtitle {
                font-size: 0.75rem;
            }

            .btn-walkin,
            .btn-login {
                padding: 0.625rem 0.75rem;
                font-size: 0.825rem;
            }
        }

        /* Enhanced animations and interactions */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .document-request-card {
            animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .modal.fade .modal-dialog {
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Focus and accessibility improvements */
        .btn-login:focus-visible,
        .btn-walkin:focus-visible {
            outline: 2px solid var(--primary-blue);
            outline-offset: 2px;
        }

        .form-control:focus-visible {
            outline: none;
        }

        /* Smooth hover states */
        .document-request-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        /* Loading state for buttons */
        .btn-loading {
            position: relative;
            color: transparent;
        }

        .btn-loading::after {
            content: '';
            position: absolute;
            width: 16px;
            height: 16px;
            top: 50%;
            left: 50%;
            margin-left: -8px;
            margin-top: -8px;
            border: 2px solid transparent;
            border-top-color: currentColor;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<<body>
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
            <div class="document-request-card">
                <h1 class="card-title">NU Document Request</h1>
                <p class="card-subtitle">Secure access to your academic documents</p>
                
                <!-- Login Button -->
                <a href="<?php echo e(route('auth.login')); ?>" class="btn-login" aria-label="Go to login page">
                    <i class="bi bi-box-arrow-in-right" aria-hidden="true"></i>
                    Login
                </a>

                <!-- Divider -->
                <div class="divider" role="separator" aria-label="or">
                    <span class="divider-text">For NU Students</span>
                </div>

                <!-- Walk-In Button -->
                <a href="<?php echo e(route('onsite.index')); ?>" class="btn-walkin" aria-label="Request documents without login">
                    <i class="bi bi-person-walking" aria-hidden="true"></i>
                    Walk-In Request
                </a>
                <p class="no-login-text">No login required</p>
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

    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript for enhanced interactions -->
    <script>
        // Enhanced button interactions and accessibility
        document.addEventListener('DOMContentLoaded', function() {
            // Add click animation to buttons with improved ripple effect
            function createRippleEffect(e, element) {
                const ripple = document.createElement('span');
                const rect = element.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;
                
                ripple.style.cssText = `
                    position: absolute;
                    width: ${size}px;
                    height: ${size}px;
                    left: ${x}px;
                    top: ${y}px;
                    border-radius: 50%;
                    background: rgba(255, 255, 255, 0.4);
                    transform: scale(0);
                    animation: ripple 0.6s cubic-bezier(0.4, 0, 0.2, 1);
                    pointer-events: none;
                    z-index: 1;
                `;
                
                element.style.position = 'relative';
                element.style.overflow = 'hidden';
                element.appendChild(ripple);
                
                setTimeout(() => {
                    if (ripple.parentNode) {
                        ripple.remove();
                    }
                }, 600);
            }

            // Enhanced ripple effect for buttons
            document.querySelectorAll('.btn-walkin, .btn-login').forEach(button => {
                button.addEventListener('click', function(e) {
                    createRippleEffect(e, this);
                });

                // Add keyboard support
                button.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        this.click();
                    }
                });
            });

            // Form validation and loading states
            const loginForm = document.querySelector('form[action*="login"]');
            if (loginForm) {
                loginForm.addEventListener('submit', function(e) {
                    const submitBtn = this.querySelector('button[type="submit"]');
                    
                    // Add loading state
                    submitBtn.classList.add('btn-loading');
                    submitBtn.disabled = true;
                    
                    // Basic validation
                    const email = this.querySelector('#school_email');
                    const password = this.querySelector('#password');
                    
                    if (!email.value || !password.value) {
                        e.preventDefault();
                        submitBtn.classList.remove('btn-loading');
                        submitBtn.disabled = false;
                        
                        // Focus first empty field
                        const firstEmpty = email.value ? password : email;
                        firstEmpty.focus();
                        firstEmpty.style.borderColor = 'var(--error-color)';
                        
                        setTimeout(() => {
                            firstEmpty.style.borderColor = '';
                        }, 3000);
                    }
                });
            }

            // Add CSS for enhanced interactions
            const style = document.createElement('style');
            style.textContent = `
                @keyframes ripple {
                    to {
                        transform: scale(4);
                        opacity: 0;
                    }
                }

                .focused .form-label {
                    color: var(--primary-blue);
                    transform: translateY(-2px);
                    transition: all 0.2s ease;
                }

                .form-control[aria-invalid="true"] {
                    border-color: var(--error-color) !important;
                    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1) !important;
                }

                .btn-loading {
                    position: relative;
                    color: transparent !important;
                    cursor: not-allowed;
                }

                .btn-loading::after {
                    content: '';
                    position: absolute;
                    width: 16px;
                    height: 16px;
                    top: 50%;
                    left: 50%;
                    margin-left: -8px;
                    margin-top: -8px;
                    border: 2px solid transparent;
                    border-top-color: currentColor;
                    border-radius: 50%;
                    animation: spin 1s linear infinite;
                }

                @keyframes spin {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }

                @keyframes shake {
                    0%, 100% { transform: translateX(0); }
                    10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
                    20%, 40%, 60%, 80% { transform: translateX(5px); }
                }

                /* Password toggle button styling */
                .btn-link {
                    color: var(--neutral-400) !important;
                    text-decoration: none !important;
                    border: none !important;
                    background: none !important;
                    padding: 0 !important;
                    font-size: 1.1rem;
                    transition: color 0.2s ease;
                }

                .btn-link:hover {
                    color: var(--primary-blue) !important;
                }

                .btn-link:focus {
                    outline: 2px solid var(--primary-blue);
                    outline-offset: 2px;
                    border-radius: var(--border-radius-sm);
                }

                /* Enhanced focus indicators for accessibility */
                .btn-walkin:focus-visible,
                .btn-login:focus-visible,
                .register-link:focus-visible {
                    outline: 2px solid var(--primary-blue);
                    outline-offset: 2px;
                    border-radius: var(--border-radius-md);
                }

                /* Modal animations and improvements */
                .modal.fade .modal-dialog {
                    transform: scale(0.9) translate(0, -50px);
                    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                    opacity: 0;
                }

                .modal.show .modal-dialog {
                    transform: scale(1) translate(0, 0);
                    opacity: 1;
                }

                .modal-backdrop {
                    background-color: rgba(0, 0, 0, 0.6);
                    backdrop-filter: blur(4px);
                    -webkit-backdrop-filter: blur(4px);
                }

                /* Error state styling */
                .modal .alert-danger {
                    margin-bottom: 1rem;
                    animation: slideIn 0.3s ease-out;
                }

                /* Better mobile modal positioning */
                @media (max-width: 576px) {
                    .modal-dialog {
                        margin: 0.5rem auto;
                        max-width: calc(100vw - 1rem);
                        width: auto;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        min-height: calc(100vh - 1rem);
                    }
                    
                    .modal-dialog.modal-dialog-centered {
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        min-height: calc(100vh - 1rem);
                    }
                }

                /* Perfect centering for all screen sizes */
                .main-content {
                    display: flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                }

                .document-request-card {
                    margin: 0 auto !important;
                }

                .modal-dialog {
                    display: flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                }

                .modal-content {
                    margin: auto !important;
                }

                /* Reduce motion for users who prefer it */
                @media (prefers-reduced-motion: reduce) {
                    * {
                        animation-duration: 0.01ms !important;
                        animation-iteration-count: 1 !important;
                        transition-duration: 0.01ms !important;
                    }
                }
            `;
            document.head.appendChild(style);
        });
    </script>
</body>
</html><?php /**PATH D:\Nu-Regisv2\resources\views/auth/login.blade.php ENDPATH**/ ?>