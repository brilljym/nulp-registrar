<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Release Kiosk - NU Regis</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
            background: url('{{ asset('images/login-bg.jpg') }}') no-repeat center center fixed;
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

        /* Kiosk specific styles */
        .kiosk-container {
            background: var(--nu-white);
            border-radius: var(--border-radius-xl);
            box-shadow: var(--shadow-xl);
            padding: 2rem;
            max-width: 380px;
            width: 100%;
            text-align: center;
            backdrop-filter: blur(10px);
            border: 1px solid var(--neutral-200);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            margin: 0 auto;
        }

        .kiosk-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-blue), var(--accent-color));
            border-radius: var(--border-radius-xl) var(--border-radius-xl) 0 0;
        }

        .kiosk-header {
            margin-bottom: 1rem;
        }
        .kiosk-header h1 {
            color: var(--neutral-800);
            font-weight: 700;
            margin-bottom: 0.2rem;
            font-size: 1.6rem;
            letter-spacing: -0.025em;
        }
        .kiosk-header p {
            color: var(--neutral-500);
            font-size: 0.9rem;
            margin-bottom: 0;
        }
        .form-control {
            height: 45px;
            font-size: 1.1rem;
            text-align: center;
            border-radius: var(--border-radius-lg);
            border: 2px solid var(--neutral-200);
            transition: all 0.3s ease;
            background: var(--neutral-50);
            color: var(--neutral-800);
        }
        .form-control:focus {
            border-color: var(--primary-blue);
            background: var(--nu-white);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            outline: none;
        }
        .form-control::placeholder {
            color: var(--neutral-400);
        }
        .btn-confirm {
            background: var(--primary-blue);
            border: 2px solid var(--primary-blue);
            height: 45px;
            font-size: 0.95rem;
            font-weight: 600;
            border-radius: var(--border-radius-lg);
            width: 100%;
            transition: all 0.3s ease;
            color: var(--nu-white);
        }
        .btn-confirm:hover {
            background: var(--primary-blue-hover);
            border-color: var(--primary-blue-hover);
            transform: translateY(-1px);
            box-shadow: var(--shadow-lg);
            color: var(--nu-white);
        }
        .logo {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--accent-color) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.6rem;
            color: white;
            font-size: 1.3rem;
        }
        .instruction {
            background: var(--neutral-50);
            border-radius: var(--border-radius-lg);
            padding: 0.7rem;
            margin-bottom: 1rem;
            border: 1px solid var(--neutral-200);
        }
        .instruction h5 {
            color: var(--neutral-700);
            margin-bottom: 0.2rem;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .instruction p {
            color: var(--neutral-500);
            margin: 0;
            font-size: 0.8rem;
            line-height: 1.3;
        }
        .form-label {
            color: var(--neutral-700);
            font-weight: 600;
            font-size: 0.9rem;
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

        /* Responsive design */
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
                padding: 4rem 1rem 2rem;
            }
            .kiosk-container {
                padding: 1.5rem;
                max-width: 90vw;
            }
            .nu-footer {
                padding: 0.5rem 1rem;
                font-size: 0.75rem;
            }
        }

        @media (max-width: 480px) {
            .nu-header {
                padding: 0.5rem;
            }
            .nu-title {
                font-size: 0.9rem;
            }
            .nu-welcome {
                font-size: 0.75rem;
            }
            .main-content {
                padding: 3.5rem 0.5rem 2rem;
            }
            .kiosk-container {
                padding: 1.2rem;
                margin: 0 0.5rem;
            }
            .nu-footer {
                padding: 0.5rem;
                flex-direction: column;
                gap: 0.25rem;
                text-align: center;
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
                <img src="{{ asset('images/NU_shield.svg.png') }}" alt="NU Shield" class="nu-shield">
                <span class="nu-title">NU LIPA - REGISTRAR</span>
            </div>
            <span class="nu-welcome">Document Release Kiosk</span>
        </header>

        <!-- Main content area -->
        <main class="main-content">
            <div class="kiosk-container">
                <div class="kiosk-header">
                    <div class="logo">
                        <i class="fas fa-university"></i>
                    </div>
                    <h1>NU Regis</h1>
                    <p>Document Release Kiosk</p>
                </div>

                <div class="instruction">
                    <h5><i class="fas fa-info-circle me-2"></i>How to Use</h5>
                    <p>Enter your queue number to confirm your queue placement and pick up your documents.</p>
                </div>

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('kiosk.confirm') }}" method="POST">
                    @csrf
                    <div class="mb-2">
                        <label for="queue_number" class="form-label fw-bold" style="font-size: 0.9rem;">Queue Number</label>
                        <input type="text"
                               class="form-control"
                               id="queue_number"
                               name="queue_number"
                               placeholder="Enter your queue number (e.g., A001)"
                               required
                               autofocus
                               autocomplete="off">
                    </div>
                    <button type="submit" class="btn btn-primary btn-confirm">
                        <i class="fas fa-search me-2"></i>Confirm Queue Placement
                    </button>
                </form>

                <div class="mt-2 text-muted">
                    <small style="font-size: 0.75rem;">Need help? Contact the registrar's office.</small>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="nu-footer">
            <div class="footer-left">
                © 2025 NU Lipa Document Request System
            </div>
            <div class="footer-right">
                Powered by NU LIPA | Registrar Office
            </div>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-focus and handle keyboard input
        document.getElementById('queue_number').addEventListener('input', function(e) {
            // Convert to uppercase
            this.value = this.value.toUpperCase();
        });

        // Auto-submit on Enter (optional)
        document.getElementById('queue_number').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                this.form.submit();
            }
        });
    </script>
</body>
</html>