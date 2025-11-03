<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kiosk Queue Status - NU Regis</title>
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

        /* Kiosk status specific styles */
        .kiosk-container {
            background: var(--nu-white);
            border-radius: var(--border-radius-xl);
            box-shadow: var(--shadow-xl);
            padding: 1.5rem;
            max-width: 420px;
            width: 100%;
            backdrop-filter: blur(10px);
            border: 1px solid var(--neutral-200);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            margin: 0 auto;
            max-height: 85vh;
            overflow-y: auto;
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
            text-align: center;
            margin-bottom: 1rem;
        }
        .kiosk-header h2 {
            color: var(--neutral-800);
            font-weight: 700;
            margin-bottom: 0.2rem;
            font-size: 1.6rem;
            letter-spacing: -0.025em;
        }
        .kiosk-number {
            background: linear-gradient(135deg, var(--accent-color) 0%, #059669 100%);
            color: white;
            border-radius: var(--border-radius-lg);
            padding: 0.8rem 1.5rem;
            font-size: 1.4rem;
            font-weight: bold;
            text-align: center;
            margin: 0.8rem 0;
            box-shadow: var(--shadow-lg);
        }
        .kiosk-details {
            background: var(--neutral-50);
            border-radius: var(--border-radius-lg);
            padding: 0.8rem;
            margin-bottom: 1rem;
            border: 1px solid var(--neutral-200);
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.3rem;
            font-size: 0.85rem;
        }
        .detail-label {
            font-weight: 600;
            color: var(--neutral-700);
        }
        .detail-value {
            color: var(--neutral-600);
        }
        .btn-refresh {
            background: var(--primary-blue);
            border: 2px solid var(--primary-blue);
            height: 45px;
            font-size: 0.95rem;
            font-weight: 600;
            border-radius: var(--border-radius-lg);
            width: 100%;
            transition: all 0.3s ease;
            color: var(--nu-white);
            margin-bottom: 0.8rem;
        }
        .btn-refresh:hover:not(:disabled) {
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
            margin-top: 0.8rem;
            transition: all 0.3s ease;
            color: var(--nu-white);
        }
        .btn-back:hover {
            background: var(--neutral-600);
            border-color: var(--neutral-600);
            transform: translateY(-1px);
            color: var(--nu-white);
        }
        .status-badge {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            border-radius: 15px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-processing {
            background: linear-gradient(135deg, var(--warning-color) 0%, #d97706 100%);
            color: white;
        }
        .status-ready {
            background: linear-gradient(135deg, var(--accent-color) 0%, #059669 100%);
            color: white;
        }
        .status-completed {
            background: linear-gradient(135deg, var(--primary-blue) 0%, #1e40af 100%);
            color: white;
        }
        .queue-status {
            font-size: 0.85rem;
        }
        .queue-status .alert {
            margin-bottom: 0;
            border-radius: var(--border-radius-lg);
        }
        .alert-info {
            background: rgba(37, 99, 235, 0.1);
            color: var(--primary-blue);
            border: 1px solid rgba(37, 99, 235, 0.2);
        }
        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            color: var(--accent-color);
            border: 1px solid rgba(16, 185, 129, 0.2);
        }
        .position-number {
            background: var(--primary-blue);
            color: var(--nu-white);
            width: 2rem;
            height: 2rem;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: bold;
            margin-left: 0.5rem;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .kiosk-container {
                margin: 1rem;
                padding: 1rem;
            }
            .kiosk-number {
                font-size: 1.2rem;
                padding: 0.6rem 1.2rem;
            }
        }

        @media (max-width: 480px) {
            .nu-header {
                padding: 0.5rem 1rem;
            }
            .nu-title {
                font-size: 1.1rem;
            }
            .main-content {
                padding: 4rem 0.5rem 2rem;
            }
            .kiosk-container {
                margin: 0.5rem;
                padding: 1rem;
            }
            .kiosk-header h2 {
                font-size: 1.4rem;
            }
            .kiosk-number {
                font-size: 1.1rem;
                padding: 0.5rem 1rem;
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
                <img src="{{ asset('images/nu-shield.png') }}" alt="NU Shield" class="nu-shield" onerror="this.style.display='none'">
                <div>
                    <div class="nu-title">KIOSK TRACKING</div>
                    <div class="nu-welcome">National University Lipa</div>
                </div>
            </div>
            <div class="nu-time" id="current-time"></div>
        </header>

        <!-- Main content area -->
        <main class="main-content">
            <div class="kiosk-container">
                <div class="kiosk-header">
                    <h2><i class="fas fa-desktop me-2"></i>KIOSK STATUS</h2>
                    <div class="kiosk-number" id="kiosk-number">
                        {{ $kioskData['ref_code'] ?? $kioskData['kiosk_number'] ?? 'K001' }}
                    </div>
                </div>

                <div class="kiosk-details">
                    <div class="detail-row">
                        <span class="detail-label">Full Name:</span>
                        <span class="detail-value">{{ $kioskData['full_name'] ?? 'N/A' }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Student ID:</span>
                        <span class="detail-value">{{ $kioskData['student_id'] ?? 'N/A' }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Document:</span>
                        <span class="detail-value">{{ $kioskData['document_name'] ?? 'N/A' }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Quantity:</span>
                        <span class="detail-value">{{ $kioskData['quantity'] ?? 'N/A' }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Window:</span>
                        <span class="detail-value">{{ $kioskData['window_name'] ?? 'Not Assigned' }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Queue Number:</span>
                        <span class="detail-value">
                            {{ $kioskData['queue_number'] ?? 'N/A' }}
                            @if($kioskData['queue_number'] ?? false)
                                <span class="position-number">{{ $kioskData['queue_number'] }}</span>
                            @endif
                        </span>
                    </div>
                </div>

                <div class="queue-status">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Status:</strong>
                        <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $kioskData['status'] ?? 'processing')) }}">
                            {{ ucwords(str_replace('_', ' ', $kioskData['status'] ?? 'processing')) }}
                        </span>
                    </div>

                    @if(($kioskData['status'] ?? 'processing') === 'ready_for_release' || ($kioskData['status'] ?? 'processing') === 'completed')
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Ready for Pickup!</strong> Your document is ready for release.
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-clock me-2"></i>
                            <strong>Processing:</strong> Your request is being processed. Please wait for your turn.
                        </div>
                    @endif
                </div>

                <button class="btn btn-refresh" onclick="refreshStatus()">
                    <i class="fas fa-sync-alt me-2"></i>REFRESH STATUS
                </button>

                <button class="btn btn-back" onclick="goBack()">
                    <i class="fas fa-arrow-left me-2"></i>BACK TO KIOSK
                </button>
            </div>
        </main>

        <!-- Footer -->
        <footer class="nu-footer">
            <div class="footer-left">
                <i class="fas fa-university me-1"></i>NU Lipa Registrar
            </div>
            <div class="footer-right">
                <i class="fas fa-map-marker-alt me-1"></i>Muralla St., Lipa City, Batangas
            </div>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Pusher JS for Real-time Updates -->
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script>
        let kioskNumber = '{{ $kioskData['ref_code'] ?? $kioskData['kiosk_number'] ?? 'K001' }}';
        let kioskId = '{{ $kioskData['id'] ?? '' }}';

        // Initialize Pusher for real-time queue updates
        const pusher = new Pusher('{{ config('broadcasting.connections.pusher.key') }}', {
            cluster: '{{ config('broadcasting.connections.pusher.options.cluster') }}',
            encrypted: true
        });

        // Debug Pusher connection
        pusher.connection.bind('connected', function() {
            console.log('✅ Kiosk Status: Pusher connected successfully');
        });

        pusher.connection.bind('error', function(err) {
            console.error('❌ Kiosk Status: Pusher connection error:', err);
        });

        // Subscribe to queue update channels
        const queueUpdatesChannel = pusher.subscribe('queue-updates');
        const realTimeUpdatesChannel = pusher.subscribe('real-time-updates');

        // Listen for queue updates that affect this kiosk request
        queueUpdatesChannel.bind('realtime.notification', function(data) {
            console.log('Queue update received:', data);
            if (data && (data.request_id == kioskId || data.kiosk_number == kioskNumber)) {
                handleQueueUpdate(data);
            }
        });

        realTimeUpdatesChannel.bind('realtime.notification', function(data) {
            console.log('Real-time update received:', data);
            if (data && (data.request_id == kioskId || data.kiosk_number == kioskNumber)) {
                handleQueueUpdate(data);
            }
        });

        // Function to handle queue updates
        function handleQueueUpdate(data) {
            console.log('Handling queue update:', data);

            // Update status if provided
            if (data.status) {
                const statusElement = document.querySelector('.status-badge');
                if (statusElement) {
                    statusElement.textContent = data.status.toUpperCase();
                    statusElement.className = 'status-badge status-' + data.status.toLowerCase().replace(' ', '-');
                }
            }

            // Update queue number if provided
            if (data.queue_number) {
                const queueElement = document.getElementById('kiosk-number');
                if (queueElement) {
                    queueElement.textContent = data.queue_number;
                }
            }

            // Show notification
            showQueueNotification('Status updated: ' + (data.message || 'Your request status has changed'), 'info');
        }

        // Function to show queue update notifications
        function showQueueNotification(message, type = 'info') {
            // Remove existing notifications
            const existingNotifications = document.querySelectorAll('.toast-notification');
            existingNotifications.forEach(notification => notification.remove());

            // Create new notification
            const notification = document.createElement('div');
            notification.className = `toast-notification alert alert-${type}`;
            notification.style.cssText = `
                position: fixed;
                top: 80px;
                right: 20px;
                z-index: 1000;
                max-width: 300px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.3);
                border-radius: 8px;
                padding: 12px 16px;
                font-size: 14px;
                animation: slideInRight 0.3s ease-out;
            `;

            notification.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} me-2"></i>
                ${message}
            `;

            document.body.appendChild(notification);

            // Add slide in animation
            const style = document.createElement('style');
            style.textContent = `
                @keyframes slideInRight {
                    from { transform: translateX(100%); opacity: 0; }
                    to { transform: translateX(0); opacity: 1; }
                }
            `;
            document.head.appendChild(style);

            // Auto remove after 5 seconds
            setTimeout(() => {
                notification.style.animation = 'slideOutRight 0.3s ease-in';
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 300);
            }, 5000);
        }

        // Function to refresh status
        function refreshStatus() {
            const refreshBtn = document.querySelector('.btn-refresh');
            const originalText = refreshBtn.innerHTML;
            refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>REFRESHING...';
            refreshBtn.disabled = true;

            // Reload the page to get updated status
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        }

        // Function to go back
        function goBack() {
            if (window.history.length > 1) {
                window.history.back();
            } else {
                window.location.href = '/'; // Go to home or kiosk entry page
            }
        }

        // Update current time
        function updateTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('en-US', {
                hour12: true,
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            const timeElement = document.getElementById('current-time');
            if (timeElement) {
                timeElement.textContent = timeString;
            }
        }

        // Update time every second
        updateTime();
        setInterval(updateTime, 1000);

        // Auto-refresh status every 30 seconds
        setInterval(() => {
            console.log('Auto-refreshing kiosk status...');
            // You can implement silent refresh here if needed
        }, 30000);

        // Clean up on page unload
        window.addEventListener('beforeunload', function() {
            pusher.disconnect();
        });
    </script>
</body>
</html>