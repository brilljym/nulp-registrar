<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Queue Status - NU Regis</title>
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

        /* Status specific styles */
        .status-container {
            background: var(--nu-white);
            border-radius: var(--border-radius-xl);
            box-shadow: var(--shadow-xl);
            padding: 2rem;
            max-width: 500px;
            width: 100%;
            backdrop-filter: blur(10px);
            border: 1px solid var(--neutral-200);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            margin: 0 auto;
            max-height: 90vh;
            overflow-y: auto;
        }

        .status-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-blue), var(--accent-color));
            border-radius: var(--border-radius-xl) var(--border-radius-xl) 0 0;
        }

        .status-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .status-header h1 {
            color: var(--neutral-800);
            font-weight: 700;
            margin-bottom: 0.5rem;
            font-size: 2rem;
            letter-spacing: -0.025em;
        }

        .queue-number-display {
            background: linear-gradient(135deg, var(--primary-blue) 0%, #1e40af 100%);
            color: white;
            border-radius: var(--border-radius-xl);
            padding: 1.5rem 2rem;
            font-size: 2rem;
            font-weight: bold;
            text-align: center;
            margin: 1rem 0;
            box-shadow: var(--shadow-lg);
            letter-spacing: 0.1em;
        }

        .status-card {
            background: var(--neutral-50);
            border-radius: var(--border-radius-lg);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid var(--neutral-200);
            text-align: center;
        }

        .status-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            display: block;
        }

        .status-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .status-description {
            color: var(--neutral-600);
            font-size: 1rem;
            margin-bottom: 1rem;
        }

        .position-info {
            background: var(--neutral-100);
            border-radius: var(--border-radius-md);
            padding: 1rem;
            margin-top: 1rem;
        }

        .position-number {
            font-size: 2rem;
            font-weight: bold;
            color: var(--primary-blue);
            display: inline-block;
            margin-right: 0.5rem;
        }

        .estimated-time {
            background: var(--accent-color);
            color: white;
            border-radius: var(--border-radius-md);
            padding: 0.5rem 1rem;
            font-weight: 600;
            display: inline-block;
            margin-top: 0.5rem;
        }

        .progress-container {
            margin-top: 1.5rem;
        }

        .progress {
            height: 8px;
            border-radius: 4px;
            background: var(--neutral-200);
            overflow: hidden;
        }

        .progress-bar {
            background: linear-gradient(90deg, var(--accent-color), var(--primary-blue));
            transition: width 0.5s ease;
        }

        .error-container {
            background: var(--error-color);
            color: white;
            border-radius: var(--border-radius-lg);
            padding: 2rem;
            text-align: center;
        }

        .error-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.8;
        }

        .error-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .error-description {
            opacity: 0.9;
        }

        .btn-refresh {
            background: var(--primary-blue);
            border: 2px solid var(--primary-blue);
            height: 45px;
            font-size: 0.95rem;
            font-weight: 600;
            border-radius: var(--border-radius-lg);
            padding: 0 2rem;
            transition: all 0.3s ease;
            color: var(--nu-white);
            margin-top: 1rem;
        }

        .btn-refresh:hover {
            background: var(--primary-blue-hover);
            border-color: var(--primary-blue-hover);
            transform: translateY(-1px);
            box-shadow: var(--shadow-lg);
            color: var(--nu-white);
        }

        .btn-back {
            background: var(--neutral-500);
            border: 2px solid var(--neutral-500);
            height: 40px;
            font-size: 0.85rem;
            border-radius: var(--border-radius-lg);
            margin-top: 1rem;
            transition: all 0.3s ease;
            color: var(--nu-white);
        }

        .btn-back:hover {
            background: var(--neutral-600);
            border-color: var(--neutral-600);
            transform: translateY(-1px);
            color: var(--nu-white);
        }

        /* Status-specific colors */
        .status-waiting { --status-color: var(--warning-color); }
        .status-in_queue { --status-color: var(--primary-blue); }
        .status-ready_for_pickup,
        .status-ready_for_release { --status-color: var(--accent-color); }
        .status-processing { --status-color: var(--primary-blue); }
        .status-completed { --status-color: var(--accent-color); }

        .status-waiting .status-icon { color: var(--warning-color); }
        .status-in_queue .status-icon { color: var(--primary-blue); }
        .status-ready_for_pickup .status-icon,
        .status-ready_for_release .status-icon { color: var(--accent-color); }
        .status-processing .status-icon { color: var(--primary-blue); }
        .status-completed .status-icon { color: var(--accent-color); }

        /* Pulse animation for active status */
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .status-active .status-card {
            animation: pulse 2s infinite;
            box-shadow: 0 0 20px rgba(37, 99, 235, 0.3);
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
            .status-container {
                padding: 1.5rem;
                max-width: 95vw;
            }
            .queue-number-display {
                font-size: 1.5rem;
                padding: 1rem 1.5rem;
            }
            .status-title {
                font-size: 1.25rem;
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
            .status-container {
                padding: 1rem;
                margin: 0 0.5rem;
            }
            .queue-number-display {
                font-size: 1.2rem;
                padding: 0.8rem 1rem;
            }
            .status-icon {
                font-size: 2.5rem;
            }
            .status-title {
                font-size: 1.1rem;
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
                <span class="nu-title">NU LIPA - REGISTRAR</span>
            </div>
            <span class="nu-welcome">Queue Status</span>
        </header>

        <!-- Main content area -->
        <main class="main-content">
            <div class="status-container">
                <?php if(isset($error)): ?>
                    <!-- Error State -->
                    <div class="error-container">
                        <div class="error-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="error-title"><?php echo e($error); ?></div>
                        <div class="error-description">
                            The queue number <strong><?php echo e($queueNumber); ?></strong> was not found in our system.
                        </div>
                        <a href="<?php echo e(route('kiosk.index')); ?>" class="btn btn-light btn-back">
                            <i class="fas fa-arrow-left me-2"></i>Back to Kiosk
                        </a>
                    </div>
                <?php else: ?>
                    <!-- Status Display -->
                    <div class="status-header">
                        <h1>Queue Status</h1>
                        <p class="text-muted">Track your request progress</p>
                    </div>

                    <!-- Queue Number -->
                    <div class="queue-number-display">
                        <?php echo e($queueNumber); ?>

                    </div>

                    <!-- Status Card -->
                    <div class="status-card status-<?php echo e($statusInfo['status']); ?> <?php echo e(in_array($statusInfo['status'], ['in_queue', 'processing']) ? 'status-active' : ''); ?>">
                        <div class="status-icon">
                            <i class="<?php echo e($statusInfo['statusIcon']); ?>"></i>
                        </div>
                        <div class="status-title"><?php echo e($statusInfo['statusText']); ?></div>
                        <div class="status-description"><?php echo e($statusInfo['description']); ?></div>

                        <?php if($statusInfo['status'] === 'waiting'): ?>
                            <div class="position-info">
                                <div class="position-number"><?php echo e($statusInfo['position']); ?></div>
                                <span>in queue</span>
                            </div>
                            <div class="estimated-time">
                                <i class="fas fa-clock me-1"></i>
                                Estimated wait: <?php echo e($statusInfo['estimatedTime']); ?>

                            </div>
                        <?php elseif(in_array($statusInfo['status'], ['ready_for_pickup', 'ready_for_release'])): ?>
                            <div class="estimated-time">
                                <i class="fas fa-check-circle me-1"></i>
                                <?php echo e($statusInfo['estimatedTime']); ?>

                            </div>
                        <?php else: ?>
                            <div class="estimated-time">
                                <i class="fas fa-info-circle me-1"></i>
                                <?php echo e($statusInfo['estimatedTime']); ?>

                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Progress Bar for waiting status -->
                    <?php if($statusInfo['status'] === 'waiting' && $statusInfo['position'] > 0): ?>
                        <div class="progress-container">
                            <div class="progress">
                                <div class="progress-bar" style="width: <?php echo e(min(100, (1 / $statusInfo['position']) * 100)); ?>%"></div>
                            </div>
                            <small class="text-muted mt-1 d-block">Progress to front of queue</small>
                        </div>
                    <?php endif; ?>

                    <!-- Action Buttons -->
                    <div class="text-center">
                        <button onclick="window.location.reload()" class="btn btn-primary btn-refresh">
                            <i class="fas fa-sync-alt me-2"></i>Refresh Status
                        </button>
                        <br>
                        <a href="<?php echo e(route('kiosk.index')); ?>" class="btn btn-secondary btn-back">
                            <i class="fas fa-arrow-left me-2"></i>Back to Kiosk
                        </a>
                    </div>
                <?php endif; ?>
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

    <!-- Pusher JS for Real-time Updates -->
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script>
        <?php if(!isset($error)): ?>
        let queueNumber = '<?php echo e($queueNumber); ?>';
        let currentStatus = '<?php echo e($statusInfo['status']); ?>';

        // Initialize Pusher for real-time queue updates
        const pusher = new Pusher('<?php echo e(config('broadcasting.connections.pusher.key')); ?>', {
            cluster: '<?php echo e(config('broadcasting.connections.pusher.options.cluster')); ?>',
            encrypted: true
        });

        // Debug Pusher connection
        pusher.connection.bind('connected', function() {
            console.log('✅ Status Page: Pusher connected successfully');
        });

        pusher.connection.bind('error', function(err) {
            console.error('❌ Status Page: Pusher connection error:', err);
        });

        // Subscribe to queue update channels
        const queueUpdatesChannel = pusher.subscribe('queue-updates');
        const realTimeUpdatesChannel = pusher.subscribe('real-time-updates');

        // Listen for queue updates
        queueUpdatesChannel.bind('realtime.notification', function(data) {
            console.log('🔄 Status Page: Received queue update:', data);

            // Check if this update affects our queue number
            if (data.data && (
                data.data.queue_number === queueNumber ||
                (data.data.id && data.data.queue_number)
            )) {
                handleQueueUpdate(data);
            }
        });

        realTimeUpdatesChannel.bind('realtime.notification', function(data) {
            console.log('🔄 Status Page: Received real-time update:', data);

            // Check if this update affects our request
            if (data.data && (
                data.data.queue_number === queueNumber ||
                data.data.id === parseInt('<?php echo e($request->id); ?>')
            )) {
                handleQueueUpdate(data);
            }
        });

        // Function to handle queue updates
        function handleQueueUpdate(data) {
            console.log('📋 Processing queue update for our request:', data);

            // Show notification about the update
            if (data.message) {
                showStatusNotification(data.message, data.type || 'info');
            }

            // Reload the page to show updated status
            setTimeout(() => {
                console.log('🔄 Reloading status page due to update...');
                window.location.reload();
            }, 2000);
        }

        // Function to show status update notifications
        function showStatusNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = 'status-notification';

            let bgColor = '--primary-blue';
            let icon = 'fas fa-info-circle';

            if (type === 'queue_placement_confirmed') {
                bgColor = '--accent-color';
                icon = 'fas fa-check-circle';
            } else if (type === 'queue_update') {
                bgColor = '--warning-color';
                icon = 'fas fa-bell';
            }

            notification.style.cssText = `
                position: fixed;
                top: 80px;
                right: 20px;
                background: var(${bgColor});
                color: var(--nu-white);
                padding: 1rem 1.5rem;
                border-radius: var(--border-radius-md);
                box-shadow: var(--shadow-lg);
                z-index: 9999;
                font-weight: 600;
                font-size: 0.9rem;
                max-width: 350px;
                transform: translateX(370px);
                transition: transform 0.3s ease;
                border-left: 4px solid var(--nu-yellow);
            `;

            notification.innerHTML = `
                <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                    <i class="${icon}" style="margin-top: 0.125rem; flex-shrink: 0;"></i>
                    <div>
                        <div style="font-weight: 700; margin-bottom: 0.25rem;">Status Update</div>
                        <div>${message}</div>
                    </div>
                </div>
            `;

            document.body.appendChild(notification);

            // Animate in
            setTimeout(() => {
                notification.style.transform = 'translateX(0)';
            }, 100);

            // Auto-remove after 4 seconds
            setTimeout(() => {
                notification.style.transform = 'translateX(370px)';
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 300);
            }, 4000);
        }

        // Auto-refresh status every 30 seconds
        setInterval(() => {
            console.log('🔄 Auto-refreshing status...');
            window.location.reload();
        }, 30000);

        // Clean up on page unload
        window.addEventListener('beforeunload', function() {
            pusher.disconnect();
        });
        <?php endif; ?>
    </script>
</body>
</html><?php /**PATH D:\Nu-Regisv2\resources\views/kiosk/status.blade.php ENDPATH**/ ?>