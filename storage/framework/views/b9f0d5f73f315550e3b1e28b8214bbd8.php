<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - NU Document Request - NU Lipa</title>
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
            background: rgba(0, 0, 0, 0.4);
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
            padding: 3rem 2rem 2rem;
            max-width: 420px;
            width: 100%;
            max-width: min(420px, 90vw);
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
        .btn-primary-custom {
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

        .btn-primary-custom:hover {
            background: var(--primary-blue-hover);
            border-color: var(--primary-blue-hover);
            transform: translateY(-1px);
            box-shadow: var(--shadow-lg);
            color: var(--nu-white);
        }

        .btn-primary-custom:focus {
            outline: 2px solid var(--primary-blue);
            outline-offset: 2px;
        }

        .btn-primary-custom:active {
            transform: translateY(0);
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
        .alert-success {
            border-radius: var(--border-radius-lg);
            border: none;
            background: rgba(16, 185, 129, 0.1);
            color: var(--accent-color);
            margin-bottom: 1.5rem;
            padding: 1rem;
            font-weight: 500;
            border-left: 4px solid var(--accent-color);
        }

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

        /* Back link */
        .back-link {
            color: var(--primary-blue);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            position: absolute;
            top: 1rem;
            left: 1rem;
            margin-bottom: 0;
            padding: 0.5rem 0.75rem;
            background: rgba(37, 99, 235, 0.05);
            border-radius: var(--border-radius-lg);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
        }

        .back-link:hover {
            color: var(--primary-blue-hover);
            background: rgba(37, 99, 235, 0.1);
            border-color: rgba(37, 99, 235, 0.2);
            transform: translateY(-1px);
            box-shadow: var(--shadow-sm);
            text-decoration: none;
        }

        .back-link:active {
            transform: translateY(0);
        }

        .back-link:focus {
            outline: 2px solid var(--primary-blue);
            outline-offset: 2px;
            background: rgba(37, 99, 235, 0.15);
        }

        .back-link i {
            font-size: 0.9rem;
            transition: transform 0.2s ease;
        }

        .back-link:hover i {
            transform: translateX(-2px);
        }

        /* Reset password link */
        .document-request-card .reset-link {
            text-align: left !important;
        }

        /* Password toggle */
        .password-toggle {
            position: relative;
        }

        .password-toggle .form-control {
            padding-right: 3rem;
        }

        .password-toggle .btn-link {
            position: absolute;
            right: 0.5rem;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: none;
            color: var(--neutral-400);
            padding: 0.5rem;
            cursor: pointer;
            font-size: 0.875rem;
        }

        .password-toggle .btn-link:hover {
            color: var(--primary-blue);
        }

        /* Enhanced Responsive Design */
        @media (max-width: 768px) {
            .nu-header {
                padding: 0.75rem 1rem;
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

            .document-request-card {
                padding: 2.75rem 1.25rem 1.75rem;
                max-width: min(380px, 90vw);
            }

            .card-title {
                font-size: 1.5rem;
                margin-bottom: 0.375rem;
                margin-top: 0.75rem; /* Add space from back-link */
            }

            .card-subtitle {
                font-size: 0.825rem;
                margin-bottom: 1.25rem;
            }

            .btn-primary-custom {
                padding: 0.75rem 1rem;
                font-size: 0.9rem;
            }

            .reset-link {
                font-size: 0.8rem !important;
                margin-top: 0.75rem !important;
            }

            .back-link {
                top: 0.75rem !important;
                left: 0.75rem !important;
                padding: 0.375rem 0.625rem !important;
                font-size: 0.8rem !important;
                min-height: 44px !important; /* Better touch target for mobile */
                display: inline-flex !important;
                align-items: center !important;
            }

            .nu-footer {
                padding: 0.5rem 1rem;
                font-size: 0.7rem;
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

            .document-request-card {
                padding: 2.5rem 1rem 1.5rem;
                max-width: min(320px, 90vw);
            }

            .card-title {
                font-size: 1.375rem;
                margin-top: 1rem; /* Add space from back-link */
            }

            .card-subtitle {
                font-size: 0.775rem;
                margin-bottom: 1rem;
            }

            .btn-primary-custom {
                padding: 0.65rem 0.875rem;
                font-size: 0.875rem;
            }

            .reset-link {
                font-size: 0.75rem !important;
                margin-top: 0.5rem !important;
            }

            .back-link {
                top: 0.5rem !important;
                left: 0.5rem !important;
                padding: 0.25rem 0.5rem !important;
                font-size: 0.75rem !important;
                min-height: 40px !important; /* Better touch target for small mobile */
                display: inline-flex !important;
                align-items: center !important;
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

        .document-request-card {
            animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Focus and accessibility improvements */
        .btn-primary-custom:focus-visible {
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
            <span class="nu-welcome">Secure Login</span>
        </header>

        <!-- Main content area -->
        <main class="main-content">
            <div class="document-request-card">
                <a href="<?php echo e(route('login')); ?>" class="back-link">
                    <i class="bi bi-arrow-left"></i>
                    Back to Options
                </a>

                <h1 class="card-title">Login</h1>
                <p class="card-subtitle">Enter your credentials to access your account</p>

                <?php if(session('status')): ?>
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle me-2"></i>
                        <?php echo e(session('status')); ?>

                    </div>
                <?php endif; ?>

                <?php if($errors->any()): ?>
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <?php echo e($errors->first()); ?>

                    </div>
                <?php endif; ?>

                <form method="POST" action="<?php echo e(route('login.post')); ?>" novalidate>
                    <?php echo csrf_field(); ?>
                    <div class="mb-3">
                        <label for="school_email" class="form-label">
                            School Email
                        </label>
                        <input type="email"
                               class="form-control"
                               id="school_email"
                               name="school_email"
                               placeholder="your.email@nulipa.edu.ph"
                               value="<?php echo e(old('school_email')); ?>"
                               required
                               autofocus
                               aria-describedby="email-help">
                        <small id="email-help" class="form-text text-muted visually-hidden">
                            Enter your school email address
                        </small>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label">
                            Password
                        </label>
                        <div class="password-toggle">
                            <input type="password"
                                   class="form-control"
                                   id="password"
                                   name="password"
                                   placeholder="Enter your password"
                                   required
                                   aria-describedby="password-help">
                            <button type="button" class="btn-link" id="togglePassword" aria-label="Toggle password visibility">
                                <i class="bi bi-eye" id="passwordIcon"></i>
                            </button>
                        </div>
                        <small id="password-help" class="form-text text-muted visually-hidden">
                            Enter your account password
                        </small>
                    </div>

                    <button type="submit" class="btn-primary-custom mb-3" aria-label="Sign in to your account">
                        <i class="bi bi-box-arrow-in-right"></i>
                        <span>Sign In</span>
                    </button>
                </form>

                <p class="text-muted reset-link" style="font-size: 0.875rem; margin-top: 1rem;">
                    Forgot your password?
                    <a href="<?php echo e(route('password.request')); ?>" class="text-decoration-none" style="color: var(--primary-blue); font-weight: 500;">
                        Reset Password
                    </a>
                </p>
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
        // Enhanced form validation and loading states
        document.addEventListener('DOMContentLoaded', function() {
            const loginForm = document.querySelector('form[action*="login"]');
            if (loginForm) {
                loginForm.addEventListener('submit', function(e) {
                    const submitBtn = this.querySelector('button[type="submit"]');

                    // Add loading state
                    submitBtn.innerHTML = '<i class="bi bi-arrow-repeat spinning"></i><span>Signing In...</span>';
                    submitBtn.disabled = true;

                    // Basic validation
                    const email = this.querySelector('#school_email');
                    const password = this.querySelector('#password');

                    if (!email.value || !password.value) {
                        e.preventDefault();
                        submitBtn.innerHTML = '<i class="bi bi-box-arrow-in-right"></i><span>Sign In</span>';
                        submitBtn.disabled = false;

                        if (!email.value) email.focus();
                        else password.focus();

                        return;
                    }
                });
            }

            // Password visibility toggle
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const passwordIcon = document.getElementById('passwordIcon');

            if (togglePassword && passwordInput && passwordIcon) {
                togglePassword.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);

                    passwordIcon.className = type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
                });
            }

            // Add CSS for enhanced interactions
            const style = document.createElement('style');
            style.textContent = `
                .spinning {
                    animation: spin 1s linear infinite;
                }

                @keyframes spin {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }

                /* Focus indicators for accessibility */
                .btn-primary-custom:focus-visible,
                .back-link:focus-visible {
                    outline: 2px solid var(--primary-color);
                    outline-offset: 2px;
                }

                /* Error state styling */
                .form-control[aria-invalid="true"] {
                    border-color: var(--error-color) !important;
                }
            `;
            document.head.appendChild(style);
        });
    </script>
</body>
</html><?php /**PATH D:\Nu-Regisv2\resources\views/auth/login-form.blade.php ENDPATH**/ ?>