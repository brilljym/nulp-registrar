<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Your Document Request - NU Lipa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --nu-blue: #003399;
            --nu-yellow: #FFD700;
            --nu-white: #ffffff;
            --nu-dark-overlay: rgba(0, 0, 0, 0.4);
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: url('<?php echo e(asset('images/login-bg.jpg')); ?>') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            position: relative;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

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

        .site-content {
            position: relative;
            z-index: 10;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .nu-header {
            background: var(--nu-blue);
            color: var(--nu-white);
            padding: 0.75rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1050;
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

        .login-button {
            background: var(--nu-yellow);
            color: var(--nu-blue);
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 0.85rem;
        }

        .login-button:hover {
            background: #e6b800;
            color: var(--nu-blue);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(255, 215, 0, 0.4);
        }

        .main-content {
            flex: 1;
            padding: 6rem 1rem 3rem;
            min-height: calc(100vh - 7rem);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .search-container {
            background: var(--nu-white);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 3rem 2rem;
            margin: 0 auto;
            max-width: 500px;
            width: 100%;
            text-align: center;
        }

        .search-header {
            margin-bottom: 2rem;
        }

        .search-header h2 {
            color: var(--nu-blue);
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .search-header p {
            color: #6c757d;
            margin: 0;
        }

        .search-form {
            margin-bottom: 2rem;
        }

        .form-control {
            border-radius: 12px;
            border: 2px solid #e9ecef;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--nu-blue);
            box-shadow: 0 0 0 0.2rem rgba(0, 51, 153, 0.1);
        }

        .btn-search {
            background: linear-gradient(135deg, var(--nu-blue) 0%, #001f5f 100%);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 1rem;
        }

        .btn-search:hover {
            background: linear-gradient(135deg, #001f5f 0%, var(--nu-blue) 100%);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 51, 153, 0.3);
        }

        .help-links {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 2rem;
        }

        .help-link {
            color: var(--nu-blue);
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }

        .help-link:hover {
            color: #001f5f;
            text-decoration: underline;
        }

        .nu-footer {
            background: var(--nu-blue);
            color: var(--nu-white);
            padding: 0.75rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.8rem;
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 1050;
        }

        .footer-left {
            font-weight: 600;
        }

        .footer-right {
            text-align: right;
            font-weight: 400;
        }

        @media (max-width: 768px) {
            .nu-header {
                padding: 0.75rem 1rem;
            }

            .nu-logo-container {
                gap: 0.5rem;
            }

            .nu-shield {
                height: 1.5rem;
            }

            .nu-title {
                font-size: 0.95rem;
            }

            .login-button {
                font-size: 0.75rem;
                padding: 0.4rem 0.8rem;
            }

            .main-content {
                padding: 4rem 0.5rem 4rem;
            }

            .search-container {
                max-width: min(92vw, 360px);
                padding: 2rem 1.5rem;
                border-radius: 12px;
            }

            .search-header h2 {
                font-size: 1.3rem;
            }

            .help-links {
                flex-direction: column;
                gap: 0.5rem;
            }

            .nu-footer {
                padding: 0.5rem 1rem;
                flex-direction: column;
                align-items: flex-start;
                gap: 0.25rem;
                font-size: 0.7rem;
            }

            .footer-right {
                text-align: left;
            }
        }

        .alert {
            border-radius: 12px;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="bg-overlay"></div>
    
    <div class="site-content">
        <!-- Header -->
        <header class="nu-header">
            <div class="nu-logo-container">
                <img src="<?php echo e(asset('images/NU_shield.svg.png')); ?>" alt="NU Shield" class="nu-shield">
                <span class="nu-title">NU LIPA</span>
            </div>
            <a href="<?php echo e(route('login')); ?>" class="login-button">
                <i class="bi bi-box-arrow-in-right me-1"></i>Login
            </a>
        </header>

        <!-- Main Content -->
        <main class="main-content">
            <div class="search-container">
                <div class="search-header">
                    <h2><i class="bi bi-search me-2"></i>Track Your Request</h2>
                    <p>Enter your reference number to check the status of your document request</p>
                </div>

                <?php if(session('error')): ?>
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-circle me-2"></i><?php echo e(session('error')); ?>

                    </div>
                <?php endif; ?>

                <form class="search-form" action="<?php echo e(route('public.track.search')); ?>" method="GET">
                    <div class="mb-3">
                        <label for="reference" class="form-label text-start d-block">Reference Number</label>
                        <input type="text" 
                               class="form-control" 
                               id="reference" 
                               name="reference" 
                               placeholder="e.g., SR-20241002-0001" 
                               value="<?php echo e(request('reference')); ?>"
                               required>
                        <small class="form-text text-muted text-start d-block mt-1">
                            Reference numbers start with "SR-" followed by date and sequence
                        </small>
                    </div>
                    <button type="submit" class="btn-search">
                        <i class="bi bi-search me-2"></i>Search Request
                    </button>
                </form>

                <div class="help-links">
                    <a href="<?php echo e(route('student.request.document')); ?>" class="help-link">
                        <i class="bi bi-plus-circle me-1"></i>Submit New Request
                    </a>
                    <a href="<?php echo e(route('onsite.index')); ?>" class="help-link">
                        <i class="bi bi-building me-1"></i>Walk-in Service
                    </a>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="nu-footer">
            <div class="footer-left">
                <div class="fw-bold">NU DOCUMENT TRACKING</div>
                <div>National University - Lipa Campus</div>
            </div>
            <div class="footer-right">
                CONTACT US<br>
                <span class="fw-normal">NU Bldg, SM City Lipa, JP Laurel Highway, Lipa City, Batangas</span>
            </div>
        </footer>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-format reference number input
            const referenceInput = document.getElementById('reference');
            
            referenceInput.addEventListener('input', function() {
                let value = this.value.toUpperCase();
                
                // Remove any non-alphanumeric characters except hyphens
                value = value.replace(/[^A-Z0-9-]/g, '');
                
                // Auto-add SR- prefix if not present
                if (value && !value.startsWith('SR-')) {
                    value = 'SR-' + value.replace(/^SR-?/, '');
                }
                
                this.value = value;
            });

            // Form validation
            document.querySelector('.search-form').addEventListener('submit', function(e) {
                const reference = referenceInput.value.trim();
                
                if (!reference) {
                    e.preventDefault();
                    alert('Please enter a reference number.');
                    referenceInput.focus();
                    return;
                }
                
                if (!reference.match(/^SR-\d{8}-\d{4}$/)) {
                    e.preventDefault();
                    alert('Please enter a valid reference number format (SR-YYYYMMDD-XXXX).');
                    referenceInput.focus();
                    return;
                }
            });
        });
    </script>
</body>
</html><?php /**PATH D:\Nu-Regisv2\resources\views\public\track.blade.php ENDPATH**/ ?>