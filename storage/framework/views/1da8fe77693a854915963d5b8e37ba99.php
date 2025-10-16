<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Pickup Confirmed - NU Regis</title>
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

        /* Success specific styles */
        .success-container {
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
            max-height: 85vh;
            overflow-y: auto;
        }

        .success-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--accent-color), #059669);
            border-radius: var(--border-radius-xl) var(--border-radius-xl) 0 0;
        }

        .success-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, var(--accent-color) 0%, #059669 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.2rem;
            color: white;
            font-size: 2rem;
            box-shadow: var(--shadow-lg);
        }
        .success-header h2 {
            color: var(--neutral-800);
            font-weight: 700;
            margin-bottom: 0.2rem;
            font-size: 1.6rem;
            letter-spacing: -0.025em;
        }
        .success-message {
            background: var(--neutral-50);
            border-radius: var(--border-radius-lg);
            padding: 1rem;
            margin-bottom: 1rem;
            border: 1px solid var(--neutral-200);
        }
        .success-message p {
            color: var(--neutral-700);
            font-size: 0.9rem;
            margin: 0;
        }
        .badge {
            border-radius: 15px;
            padding: 0.4rem 0.8rem;
            font-weight: 600;
            font-size: 0.75rem;
        }
        .thank-you {
            color: var(--neutral-500);
            font-style: italic;
            margin-top: 0.8rem;
            font-size: 0.85rem;
        }
        .bg-warning {
            background: linear-gradient(135deg, var(--warning-color) 0%, #d97706 100%) !important;
            color: white !important;
        }
        .bg-success {
            background: linear-gradient(135deg, var(--accent-color) 0%, #059669 100%) !important;
            color: white !important;
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
            font-weight: 700;
            font-size: 0.9rem;
            margin-right: 0.5rem;
        }
        .queue-position-display {
            background: var(--neutral-50);
            border: 1px solid var(--neutral-200);
            border-radius: var(--border-radius-lg);
            padding: 0.75rem;
            margin: 0.75rem 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: var(--neutral-700);
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
            .success-container {
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
            .success-container {
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
                <img src="<?php echo e(asset('images/NU_shield.svg.png')); ?>" alt="NU Shield" class="nu-shield">
                <span class="nu-title">NU LIPA - REGISTRAR</span>
            </div>
            <span class="nu-welcome">Success Confirmation</span>
        </header>

        <!-- Main content area -->
        <main class="main-content">
            <div class="success-container">
                <div class="success-icon">
                    <i class="fas fa-check-circle"></i>
                </div>

                <div class="success-header">
                    <?php if($request->status === 'ready_for_pickup'): ?>
                        <h2>Ready for Pickup!</h2>
                        <p class="text-muted" style="font-size: 0.85rem;">Please proceed to your assigned window</p>
                    <?php elseif($request->status === 'waiting'): ?>
                        <h2>Queue Confirmed!</h2>
                        <p class="text-muted" style="font-size: 0.85rem;">You are in the waiting queue</p>
                    <?php else: ?>
                        <h2>Pickup Completed!</h2>
                        <p class="text-muted" style="font-size: 0.85rem;">Your documents have been successfully collected</p>
                    <?php endif; ?>
                </div>

                <div class="success-message">
                    <?php if(isset($message)): ?>
                        <p style="color: var(--accent-color); font-weight: 600; margin-bottom: 1rem;"><?php echo e($message); ?></p>
                    <?php endif; ?>
                    <p>
                        <strong>Reference Code:</strong> <?php echo e($type === 'student' ? $request->reference_no : $request->ref_code); ?>

                    </p>
                    <?php if(isset($request->queue_number) && $request->queue_number): ?>
                        <p>
                            <strong>Queue Number:</strong> <?php echo e($request->queue_number); ?>

                        </p>
                    <?php endif; ?>
                    
                    <?php if(isset($queuePosition)): ?>
                        <div class="queue-position-display">
                            <?php if($queuePosition === 'Ready' || $queuePosition === 1): ?>
                                <div class="position-number">1</div>
                                <span>You are next in line!</span>
                            <?php elseif(is_numeric($queuePosition)): ?>
                                <div class="position-number"><?php echo e($queuePosition); ?></div>
                                <span>Position in Queue: <?php echo e($queuePosition); ?></span>
                            <?php else: ?>
                                <div class="position-number">?</div>
                                <span>Position in Queue: Calculating...</span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    
                    <p>
                        <strong>Status:</strong> 
                        <?php if($request->status === 'ready_for_pickup'): ?>
                            <span class="badge bg-warning">Ready for Pickup</span>
                        <?php elseif($request->status === 'waiting'): ?>
                            <span class="badge bg-info">Waiting in Queue</span>
                        <?php elseif($request->status === 'in_queue'): ?>
                            <span class="badge bg-primary">In Queue</span>
                        <?php else: ?>
                            <span class="badge bg-success">Completed</span>
                        <?php endif; ?>
                    </p>
                    <?php if(isset($request->queue_number) && $request->queue_number): ?>
                        <p id="wait-time">
                            <strong>Estimated Wait Time:</strong> <span id="wait-time-value">Calculating...</span>
                        </p>
                    <?php endif; ?>
                </div>

                <div class="thank-you">
                    <p>Thank you for using NU Regis!</p>
                    <p>We hope to serve you again soon.</p>
                    <p id="redirect-message" style="margin-top: 1rem; font-weight: 600; color: var(--primary-blue);">
                        <i class="fas fa-clock me-2"></i>Redirecting to kiosk in <span id="countdown">3</span> seconds...
                    </p>
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
        // Enhanced wait time calculation with real-time updates
        <?php if(isset($request->queue_number) && $request->queue_number): ?>
            console.log('Enhanced wait time script running for queue:', '<?php echo e($request->queue_number); ?>');
            var queuePosition = <?php echo json_encode($queuePosition ?? 2, 15, 512) ?>;
            var requestId = '<?php echo e($type === 'student' ? $request->reference_no : $request->ref_code); ?>';
            var serviceType = '<?php echo e($type === 'student' ? 'student_documents' : 'alumni_documents'); ?>';
            console.log('Queue position:', queuePosition, 'Service type:', serviceType);

            // Function to update wait time
            function updateWaitTime() {
                var queueLength = 0;
                if (queuePosition === 'Ready' || queuePosition == 1) {
                    document.getElementById('wait-time-value').textContent = 'You are next!';
                    return;
                } else if (typeof queuePosition === 'number' && queuePosition > 1) {
                    queueLength = queuePosition - 1;
                } else {
                    document.getElementById('wait-time').style.display = 'none';
                    return;
                }

                console.log('Fetching enhanced wait time from API with time-based adjustments...');
                
                // Get current time for time-based multiplier awareness
                var currentHour = new Date().getHours();
                var timeMultiplier = getTimeMultiplier(currentHour);
                console.log('Current hour:', currentHour, 'Time multiplier:', timeMultiplier);
                
                // Use the enhanced estimate endpoint with service type and time consideration
                var baseServiceTime = getServiceTime(serviceType);
                var adjustedServiceTime = Math.round(baseServiceTime * timeMultiplier);
                
                var apiUrl = 'https://smart-queueing-waiting-time-ai-ylac.vercel.app/estimate?' + 
                           'queue_length=' + queueLength + 
                           '&avg_service_time=' + adjustedServiceTime + 
                           '&counters=3'; // Assuming 3 service counters

                console.log('API call with adjusted service time:', adjustedServiceTime, 'minutes (base:', baseServiceTime, ', multiplier:', timeMultiplier, ')');

                fetch(apiUrl)
                    .then(response => {
                        console.log('API response status:', response.status);
                        return response.json();
                    })
                    .then(data => {
                        console.log('API response data:', data);
                        var waitTime = Math.round(data.estimated_turnaround_time_minutes);
                        
                        // Format wait time display with time-of-day context
                        var timeContext = getTimeContext(currentHour);
                        if (waitTime < 1) {
                            document.getElementById('wait-time-value').textContent = 'Less than 1 minute';
                        } else if (waitTime < 60) {
                            document.getElementById('wait-time-value').textContent = waitTime + ' minutes' + timeContext;
                        } else {
                            var hours = Math.floor(waitTime / 60);
                            var minutes = waitTime % 60;
                            document.getElementById('wait-time-value').textContent = 
                                hours + 'h ' + (minutes > 0 ? minutes + 'm' : '') + timeContext;
                        }
                        
                        // Add visual indicator based on wait time and time of day
                        var waitTimeElement = document.getElementById('wait-time-value');
                        waitTimeElement.parentElement.className = 'text-muted';
                        if (waitTime <= 5) {
                            waitTimeElement.style.color = '#10b981'; // Green
                        } else if (waitTime <= 15) {
                            waitTimeElement.style.color = '#f59e0b'; // Yellow
                        } else {
                            waitTimeElement.style.color = '#ef4444'; // Red
                        }
                        
                        // Add time-based warning if applicable (only once)
                        if (timeMultiplier > 1.0) {
                            // Remove any existing warning first
                            var existingWarning = waitTimeElement.parentElement.querySelector('.time-warning');
                            if (existingWarning) {
                                existingWarning.remove();
                            }
                            
                            var warning = document.createElement('small');
                            warning.className = 'time-warning';
                            warning.style.display = 'block';
                            warning.style.color = '#f59e0b';
                            warning.style.fontSize = '0.75rem';
                            warning.style.marginTop = '0.25rem';
                            warning.textContent = getTimeWarning(currentHour);
                            waitTimeElement.parentElement.appendChild(warning);
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching wait time:', error);
                        document.getElementById('wait-time-value').textContent = 'Unable to calculate';
                    });
            }

            // Function to get time-based multiplier (matching API logic)
            function getTimeMultiplier(hour) {
                if ((hour >= 9 && hour <= 11) || (hour >= 14 && hour <= 16)) {
                    return 1.2; // 20% longer during rush hours
                } else if (hour >= 12 && hour <= 13) {
                    return 1.3; // 30% longer during lunch rush
                } else {
                    return 1.0; // No adjustment for other hours
                }
            }

            // Function to get time context message
            function getTimeContext(hour) {
                if ((hour >= 9 && hour <= 11) || (hour >= 14 && hour <= 16)) {
                    return ' (peak hours)';
                } else if (hour >= 12 && hour <= 13) {
                    return ' (lunch rush)';
                } else {
                    return '';
                }
            }

            // Function to get time-based warning message
            function getTimeWarning(hour) {
                if ((hour >= 9 && hour <= 11) || (hour >= 14 && hour <= 16)) {
                    return '⚠️ Peak hours - wait times may be longer';
                } else if (hour >= 12 && hour <= 13) {
                    return '⚠️ Lunch rush - expect delays';
                } else {
                    return '';
                }
            }

            // Function to get average service time based on document type
            function getServiceTime(serviceType) {
                var serviceTimes = {
                    'student_documents': 8,   // Student documents typically faster
                    'alumni_documents': 12,   // Alumni might need more verification
                    'transcript': 15,         // Transcripts require more processing
                    'certification': 10,      // Standard certifications
                    'general': 10            // Default fallback
                };
                return serviceTimes[serviceType] || serviceTimes['general'];
            }

            // Initial wait time calculation
            updateWaitTime();

            // Periodically update wait time (every 30 seconds)
            setInterval(function() {
                // Only update if still waiting
                var statusBadge = document.querySelector('.badge');
                if (statusBadge && (statusBadge.textContent.includes('Waiting') || statusBadge.textContent.includes('Queue'))) {
                    updateWaitTime();
                }
            }, 30000);

            // Optional: Check queue status for real-time updates
            function checkQueueStatus() {
                fetch('https://smart-queueing-waiting-time-ai-ylac.vercel.app/queue/waiting')
                    .then(response => response.json())
                    .then(data => {
                        console.log('Current queue status:', data);
                        // You can implement real-time position updates here
                    })
                    .catch(error => {
                        console.log('Queue status check failed:', error);
                    });
            }

            // Check queue status every minute
            setInterval(checkQueueStatus, 60000);

        <?php else: ?>
            console.log('Queue number not set, skipping wait time calculation');
        <?php endif; ?>

        // Auto-redirect to kiosk after 3 seconds
        let countdown = 3;
        const countdownElement = document.getElementById('countdown');
        
        const countdownTimer = setInterval(() => {
            countdown--;
            if (countdownElement) {
                countdownElement.textContent = countdown;
            }
            
            if (countdown <= 0) {
                clearInterval(countdownTimer);
                window.location.href = '<?php echo e(route('kiosk.index')); ?>';
            }
        }, 1000);

        // Allow user to click anywhere to go back immediately
        document.addEventListener('click', function(e) {
            // Don't redirect if clicking on specific interactive elements
            if (!e.target.closest('a, button, input, select, textarea')) {
                clearInterval(countdownTimer);
                window.location.href = '<?php echo e(route('kiosk.index')); ?>';
            }
        });

        // Add keyboard shortcut to go back (Escape key)
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                clearInterval(countdownTimer);
                window.location.href = '<?php echo e(route('kiosk.index')); ?>';
            }
        });
    </script>
</body>
</html><?php /**PATH D:\Nu-Regisv2\resources\views/kiosk/success.blade.php ENDPATH**/ ?>