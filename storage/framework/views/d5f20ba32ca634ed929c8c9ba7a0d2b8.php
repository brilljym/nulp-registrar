<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Confirm Pickup - NU Regis</title>
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

        /* Confirm specific styles */
        .confirm-container {
            background: var(--nu-white);
            border-radius: var(--border-radius-xl);
            box-shadow: var(--shadow-xl);
            padding: 1.5rem;
            max-width: 380px;
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

        .confirm-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-blue), var(--accent-color));
            border-radius: var(--border-radius-xl) var(--border-radius-xl) 0 0;
        }

        .confirm-header {
            text-align: center;
            margin-bottom: 1rem;
        }
        .confirm-header h2 {
            color: var(--neutral-800);
            font-weight: 700;
            margin-bottom: 0.2rem;
            font-size: 1.6rem;
            letter-spacing: -0.025em;
        }
        .queue-number {
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
        .request-details {
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
        .btn-confirm-pickup {
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
        .btn-confirm-pickup:hover:not(:disabled) {
            background: var(--primary-blue-hover);
            border-color: var(--primary-blue-hover);
            transform: translateY(-1px);
            box-shadow: var(--shadow-lg);
            color: var(--nu-white);
        }
        .btn-confirm-pickup:disabled {
            background: var(--neutral-400);
            border-color: var(--neutral-400);
            cursor: not-allowed;
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
        .status-ready {
            background: linear-gradient(135deg, var(--accent-color) 0%, #059669 100%);
            color: white;
        }
        .queue-status {
            font-size: 0.85rem;
        }
        .queue-status .alert {
            margin-bottom: 0;
            border-radius: var(--border-radius-lg);
        }
        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            color: var(--accent-color);
            border: 1px solid rgba(16, 185, 129, 0.2);
        }
        .alert-info {
            background: rgba(37, 99, 235, 0.1);
            color: var(--primary-blue);
            border: 1px solid rgba(37, 99, 235, 0.2);
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
            margin-right: 0.75rem;
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
            .confirm-container {
                padding: 1.2rem;
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
            .confirm-container {
                padding: 1rem;
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
            <span class="nu-welcome">Queue Confirmation</span>
        </header>

        <!-- Main content area -->
        <main class="main-content">
            <div class="confirm-container">
                <div class="confirm-header">
                    <h2><i class="fas fa-check-circle text-success me-2"></i>Queue Confirmed</h2>
                    <p class="text-muted" style="font-size: 0.85rem;">Your documents are ready for pickup</p>
                </div>

                <?php if(isset($request->queue_number) && $request->queue_number): ?>
                    <div class="queue-number">
                        <i class="fas fa-ticket-alt me-2"></i>
                        Queue #<?php echo e($request->queue_number); ?>

                    </div>
                    
                    <!-- Queue Position and Status -->
                    <div class="queue-status mb-3">
                        <?php if(isset($isReady) && $isReady): ?>
                            <div class="alert alert-success p-2" style="border-radius: 8px;">
                                <i class="fas fa-bell me-2"></i>
                                <strong>Please proceed to Window <?php echo e($windowAssignment ?? 'TBD'); ?></strong>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info p-2" style="border-radius: 8px;">
                                <div style="display: flex; align-items: center; margin-bottom: 0.5rem;">
                                    <i class="fas fa-clock me-2"></i>
                                    <?php if($queuePosition === 'Ready' || $queuePosition === 1): ?>
                                        <div class="position-number me-2">1</div>
                                        <strong>You are next in line!</strong>
                                    <?php elseif(is_numeric($queuePosition)): ?>
                                        <div class="position-number me-2"><?php echo e($queuePosition); ?></div>
                                        <strong class="queue-position">Position in Queue: <?php echo e($queuePosition); ?></strong>
                                    <?php else: ?>
                                        <div class="position-number me-2">?</div>
                                        <strong class="queue-position">Position in Queue: Calculating...</strong>
                                    <?php endif; ?>
                                </div>
                                <small>Please wait to be called</small>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <div class="request-details">
                    <h5 class="mb-2" style="font-size: 0.9rem;"><i class="fas fa-file-alt me-2"></i>Request Details</h5>

                    <div class="detail-row">
                        <span class="detail-label">Reference Code:</span>
                        <span class="detail-value"><?php echo e($type === 'student' ? $request->reference_no : $request->ref_code); ?></span>
                    </div>

                    <?php if($type === 'student'): ?>
                        <div class="detail-row">
                            <span class="detail-label">Student:</span>
                            <span class="detail-value"><?php echo e($request->student->user->first_name); ?> <?php echo e($request->student->user->last_name); ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Course:</span>
                            <span class="detail-value"><?php echo e($request->student->course); ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Year Level:</span>
                            <span class="detail-value"><?php echo e($request->student->year_level); ?></span>
                        </div>
                    <?php else: ?>
                        <div class="detail-row">
                            <span class="detail-label">Name:</span>
                            <span class="detail-value"><?php echo e($request->full_name); ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Course:</span>
                            <span class="detail-value"><?php echo e($request->course); ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Year Level:</span>
                            <span class="detail-value"><?php echo e($request->year_level); ?></span>
                        </div>
                    <?php endif; ?>

                    <div class="detail-row">
                        <span class="detail-label">Status:</span>
                        <span class="status-badge status-ready">
                            <i class="fas fa-check-circle me-1"></i>
                            <?php if($request->status === 'ready_for_pickup'): ?>
                                Ready for Pickup
                            <?php elseif($request->status === 'in_queue'): ?>
                                In Queue
                            <?php elseif($request->status === 'waiting'): ?>
                                Waiting in Queue
                            <?php elseif($request->status === 'released'): ?>
                                Ready for Release  
                            <?php else: ?>
                                <?php echo e($type === 'student' ? 'Ready for Release' : 'Released'); ?>

                            <?php endif; ?>
                        </span>
                    </div>

                    <div class="detail-row">
                        <span class="detail-label">Documents:</span>
                        <span class="detail-value">
                            <?php $__currentLoopData = $request->requestItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php echo e($item->document->type_document); ?>

                                <?php if($item->quantity > 1): ?>
                                    (<?php echo e($item->quantity); ?>)
                                <?php endif; ?>
                                <?php if(!$loop->last): ?>, <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </span>
                    </div>
                </div>

                <form action="<?php echo e(route('kiosk.confirm-pickup', ['type' => $type, 'id' => $request->id])); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php if(isset($isReady) && $isReady): ?>
                        <?php if($request->status === 'ready_for_release' || $request->status === 'released'): ?>
                            <button type="submit" class="btn btn-success btn-confirm-pickup" onclick="printThenSubmit(event, this)">
                                <i class="fas fa-check me-2"></i>Confirm I'm Here for Pickup
                            </button>
                        <?php elseif($request->status === 'completed'): ?>
                            <button type="submit" class="btn btn-success btn-confirm-pickup" onclick="printThenSubmit(event, this)">
                                <i class="fas fa-check me-2"></i>Confirm Queue Placement
                            </button>
                        <?php elseif($request->status === 'in_queue'): ?>
                            <button type="submit" class="btn btn-success btn-confirm-pickup" onclick="printThenSubmit(event, this)">
                                <i class="fas fa-check me-2"></i>Confirm Ready for Pickup
                            </button>
                        <?php elseif($request->status === 'ready_for_pickup'): ?>
                            <button type="submit" class="btn btn-success btn-confirm-pickup" onclick="printThenSubmit(event, this)">
                                <i class="fas fa-hand-paper me-2"></i>Confirm Document Collection
                            </button>
                        <?php elseif($request->status === 'waiting'): ?>
                            <button type="button" class="btn btn-warning btn-confirm-pickup" disabled>
                                <i class="fas fa-hourglass-half me-2"></i>You are in Waiting Queue
                            </button>
                        <?php endif; ?>
                    <?php else: ?>
                        <?php if($request->status === 'completed'): ?>
                            <button type="submit" class="btn btn-success btn-confirm-pickup" onclick="printThenSubmit(event, this)">
                                <i class="fas fa-check me-2"></i>Confirm Queue Placement
                            </button>
                        <?php elseif($request->status === 'waiting'): ?>
                            <button type="submit" class="btn btn-warning btn-confirm-pickup" onclick="printThenSubmit(event, this)">
                                <i class="fas fa-hourglass-half me-2"></i>Confirm Queue Placement
                            </button>
                        <?php else: ?>
                            <button type="button" class="btn btn-secondary btn-confirm-pickup" disabled>
                                <i class="fas fa-clock me-2"></i>Wait for Your Turn
                            </button>
                        <?php endif; ?>
                    <?php endif; ?>
                </form>

                <a href="<?php echo e(route('kiosk.index')); ?>" class="btn btn-secondary btn-back d-block">
                    <i class="fas fa-arrow-left me-2"></i>Back to Kiosk
                </a>
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
        let queueNumber = '<?php echo e($request->queue_number); ?>';
        let isCurrentlyReady = <?php echo e(isset($isReady) && $isReady ? 'true' : 'false'); ?>;
        let requestType = '<?php echo e($type); ?>';
        let requestId = '<?php echo e($request->id); ?>';
        
        // Initialize Pusher for real-time queue updates
        const pusher = new Pusher('<?php echo e(config('broadcasting.connections.pusher.key')); ?>', {
            cluster: '<?php echo e(config('broadcasting.connections.pusher.options.cluster')); ?>',
            encrypted: true
        });
        
        // Debug Pusher connection
        pusher.connection.bind('connected', function() {
            console.log('✅ Confirm Page: Pusher connected successfully');
        });
        
        pusher.connection.bind('error', function(err) {
            console.error('❌ Confirm Page: Pusher connection error:', err);
        });

        // Subscribe to queue update channels
        const queueUpdatesChannel = pusher.subscribe('queue-updates');
        const realTimeUpdatesChannel = pusher.subscribe('real-time-updates');
        
        // Listen for queue updates that affect this specific request
        queueUpdatesChannel.bind('realtime.notification', function(data) {
            console.log('🔄 Confirm Page: Received queue update:', data);
            
            // Check if this update is for our request
            if (data.data && (
                data.data.queue_number === queueNumber || 
                data.data.id === parseInt(requestId) ||
                data.data.reference_code === '<?php echo e($type === 'student' ? $request->reference_no : $request->ref_code); ?>'
            )) {
                handleQueueUpdate(data);
            }
        });

        realTimeUpdatesChannel.bind('realtime.notification', function(data) {
            console.log('🔄 Confirm Page: Received real-time update:', data);
            
            // Check if this update affects our request
            if (data.data && (
                data.data.queue_number === queueNumber || 
                data.data.id === parseInt(requestId)
            )) {
                handleQueueUpdate(data);
            }
        });

        // Function to handle queue updates
        function handleQueueUpdate(data) {
            console.log('📋 Processing queue update for our request:', data);
            
            // Show notification about the update
            if (data.message) {
                showQueueNotification(data.message, data.type || 'info');
            }
            
            // If status changed significantly, reload the page
            if (data.data && data.data.status && ['in_queue', 'ready_for_pickup', 'waiting'].includes(data.data.status)) {
                setTimeout(() => {
                    console.log('🔄 Reloading page due to status change...');
                    window.location.reload();
                }, 1500);
            }
        }

        // Function to show queue update notifications
        function showQueueNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = 'queue-notification';
            
            let bgColor = '--nu-blue';
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
                        <div style="font-weight: 700; margin-bottom: 0.25rem;">Queue Update</div>
                        <div style="font-weight: 400; font-size: 0.85rem; line-height: 1.3;">${message}</div>
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

        // Simple print then submit function
        function printThenSubmit(event, button) {
            // Prevent form submission initially
            event.preventDefault();
            
            // Check if already processed
            if (button.dataset.printed === 'true') {
                console.log('Already printed, submitting form...');
                button.closest('form').submit();
                return;
            }
            
            const originalText = button.innerHTML;
            
            // Disable button and show printing state
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-print me-2"></i>Printing Receipt...';
            
            // Show notification
            showQueueNotification('Printing receipt...', 'queue_update');
            
            // Print receipt
            printReceiptAfterConfirm()
                .then((result) => {
                    console.log('✅ Print completed successfully');
                    button.innerHTML = '<i class="fas fa-check me-2"></i>Receipt Printed - Confirming...';
                    showQueueNotification('Receipt printed successfully! Confirming placement...', 'queue_placement_confirmed');
                    
                    // Mark as printed and submit after delay
                    button.dataset.printed = 'true';
                    setTimeout(() => {
                        button.closest('form').submit();
                    }, 1500);
                })
                .catch(error => {
                    console.error('❌ Print failed:', error);
                    button.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>Print Failed - Continuing...';
                    showQueueNotification('Print failed, but continuing with confirmation...', 'error');
                    
                    // Still submit even if print failed
                    button.dataset.printed = 'true';
                    setTimeout(() => {
                        button.closest('form').submit();
                    }, 2000);
                });
        }

        // Auto-refresh queue status every 10 seconds
        function checkQueueStatus() {
            fetch(`<?php echo e(route('kiosk.queue-status')); ?>?queue_number=${queueNumber}`)
                .then(response => response.json())
                .then(data => {
                    if (data.is_ready && !isCurrentlyReady) {
                        // Status changed to ready - reload page to show window assignment
                        window.location.reload();
                    } else if (!data.is_ready && isCurrentlyReady) {
                        // Status changed back to waiting - reload page
                        window.location.reload();
                    }
                    
                    // Update position if it changed
                    const positionElement = document.querySelector('.queue-position');
                    if (positionElement && !data.is_ready) {
                        if (data.position === 'Ready' || data.position === 1) {
                            positionElement.textContent = 'You are next in line!';
                        } else if (typeof data.position === 'number') {
                            positionElement.textContent = `Position in Queue: ${data.position}`;
                        } else {
                            positionElement.textContent = 'Position in Queue: Calculating...';
                        }
                    }
                })
                .catch(error => {
                    console.log('Queue status check failed:', error);
                });
        }
        
        // Check status every 10 seconds
        setInterval(checkQueueStatus, 10000);
        
        // Show notification when ready
        <?php if(isset($isReady) && $isReady): ?>
            // Play notification sound (if supported)
            if ('Notification' in window) {
                Notification.requestPermission().then(function (permission) {
                    if (permission === 'granted') {
                        new Notification('NU Regis Queue', {
                            body: 'Please proceed to Window <?php echo e($windowAssignment ?? "TBD"); ?>',
                            icon: '/favicon.ico'
                        });
                    }
                });
            }
            
            // Flash the window assignment
            const windowAlert = document.querySelector('.alert-success');
            if (windowAlert) {
                setInterval(() => {
                    windowAlert.style.backgroundColor = windowAlert.style.backgroundColor === 'rgb(220, 53, 69)' ? '' : '#dc3545';
                }, 1000);
            }
        <?php endif; ?>

        // Clean up on page unload
        window.addEventListener('beforeunload', function() {
            pusher.disconnect();
        });

        // Print Receipt Functionality (called after form confirmation)
        function printReceiptAfterConfirm() {
            console.log('🖨️ Starting receipt print process...');
            return new Promise((resolve, reject) => {
                // Make AJAX request to print receipt
                fetch(`<?php echo e(route('kiosk.print-receipt', ['type' => $type, 'id' => $request->id])); ?>`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({})
                })
                .then(response => {
                    console.log('🖨️ Print request response:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('🖨️ Print response data:', data);
                    if (data.success) {
                        showPrintNotification(data.message, 'success');
                        console.log('✅ Print successful');
                        resolve(data);
                    } else {
                        showPrintNotification(data.message, 'error');
                        console.log('❌ Print failed:', data.message);
                        resolve(data); // Still resolve to continue with redirect
                    }
                })
                .catch(error => {
                    console.error('❌ Print error:', error);
                    showPrintNotification('Failed to print receipt, but confirmation will proceed.', 'error');
                    resolve(); // Still resolve to continue with redirect
                });
            });
        }

        function showPrintNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = 'print-notification';
            
            let bgColor = '--nu-blue';
            let icon = 'fas fa-info-circle';
            
            if (type === 'success') {
                bgColor = '--accent-color';
                icon = 'fas fa-check-circle';
            } else if (type === 'error') {
                bgColor = '--error-color';
                icon = 'fas fa-exclamation-circle';
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
                        <div style="font-weight: 700; margin-bottom: 0.25rem;">${type === 'success' ? 'Receipt Printed' : type === 'error' ? 'Print Error' : 'Print Status'}</div>
                        <div style="font-weight: 400; font-size: 0.85rem; line-height: 1.3;">${message}</div>
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

        // Test printer functionality (for debugging)
        function testPrinter() {
            fetch(`<?php echo e(route('kiosk.test-printer')); ?>`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                showPrintNotification(data.message, data.success ? 'success' : 'error');
            })
            .catch(error => {
                console.error('Printer test error:', error);
                showPrintNotification('Printer test failed', 'error');
            });
        }
    </script>
</body>
</html><?php /**PATH D:\Nu-Regisv2\resources\views/kiosk/confirm.blade.php ENDPATH**/ ?>