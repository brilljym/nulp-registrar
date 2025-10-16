<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Status - <?php echo e($studentRequest->reference_no); ?></title>
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

        .back-button {
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

        .back-button:hover {
            background: #e6b800;
            color: var(--nu-blue);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(255, 215, 0, 0.4);
        }

        .main-content {
            flex: 1;
            padding: 5rem 1rem 3rem;
            min-height: calc(100vh - 7rem);
        }

        .result-container {
            background: var(--nu-white);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 2rem;
            margin: 2rem auto;
            max-width: 900px;
        }

        .result-header {
            background: linear-gradient(135deg, var(--nu-blue) 0%, #001f5f 100%);
            color: var(--nu-white);
            padding: 1.5rem;
            text-align: center;
            border-radius: 15px;
            margin-bottom: 2rem;
        }

        .status-badge {
            font-size: 0.875rem;
            font-weight: 600;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-block;
            margin-top: 1rem;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .status-processing {
            background: #cff4fc;
            color: #055160;
            border: 1px solid #a6e9f7;
        }

        .status-ready_for_release {
            background: #cfe2ff;
            color: #084298;
            border: 1px solid #9ec5fe;
        }

        .status-completed {
            background: #d1e7dd;
            color: #0f5132;
            border: 1px solid #a3cfbb;
        }

        .info-card {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .timeline-steps-container {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .timeline-steps-container::-webkit-scrollbar {
            display: none;
        }

        .timeline-step {
            min-width: 80px;
            text-align: center;
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

            .back-button {
                font-size: 0.75rem;
                padding: 0.4rem 0.8rem;
            }

            .main-content {
                padding: 4rem 0.5rem 4rem;
            }

            .result-container {
                max-width: min(92vw, 360px);
                padding: 1.5rem;
                border-radius: 12px;
                margin: 1rem auto;
            }

            .result-header {
                padding: 1rem;
            }

            .info-card {
                padding: 1rem;
            }

            .timeline-step .rounded-circle {
                width: 45px !important;
                height: 45px !important;
                font-size: 20px;
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
            <a href="<?php echo e(route('public.track')); ?>" class="back-button">
                <i class="bi bi-arrow-left me-1"></i>Search Again
            </a>
        </header>

        <!-- Main Content -->
        <main class="main-content">
            <div class="result-container">
                <div class="result-header">
                    <h3><i class="bi bi-file-earmark-check me-2"></i>Request Found!</h3>
                    <p style="margin: 0; font-size: 0.9rem; opacity: 0.9;">
                        Reference: <strong><?php echo e($studentRequest->reference_no); ?></strong>
                    </p>
                    <span class="status-badge status-<?php echo e($studentRequest->status); ?>">
                        <?php echo e(ucfirst(str_replace('_', ' ', $studentRequest->status))); ?>

                    </span>
                </div>

                
                <div class="info-card">
                    <h6 class="mb-3"><i class="bi bi-info-circle me-2"></i>Request Summary</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <small class="text-muted text-uppercase fw-medium">Student</small>
                            <div class="fw-semibold"><?php echo e($studentRequest->student->user->first_name); ?> <?php echo e($studentRequest->student->user->last_name); ?></div>
                            <small class="text-muted"><?php echo e($studentRequest->student->student_id); ?></small>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted text-uppercase fw-medium">Submitted</small>
                            <div class="fw-semibold"><?php echo e($studentRequest->created_at->format('M d, Y')); ?></div>
                            <small class="text-muted"><?php echo e($studentRequest->created_at->format('h:i A')); ?></small>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted text-uppercase fw-medium">Documents</small>
                            <div class="fw-semibold"><?php echo e($studentRequest->requestItems->count()); ?> item(s)</div>
                            <small class="text-muted">₱<?php echo e(number_format($studentRequest->total_cost, 2)); ?></small>
                        </div>
                        <?php if($studentRequest->expected_release_date): ?>
                        <div class="col-md-6">
                            <small class="text-muted text-uppercase fw-medium">Expected Release</small>
                            <div class="fw-semibold"><?php echo e($studentRequest->expected_release_date->format('M d, Y')); ?></div>
                            <small class="text-muted"><?php echo e($studentRequest->expected_release_date->diffForHumans()); ?></small>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                
                <div class="info-card">
                    <h6 class="mb-3"><i class="bi bi-file-earmark-text me-2"></i>Requested Documents</h6>
                    <?php $__currentLoopData = $studentRequest->requestItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="d-flex justify-content-between align-items-center p-2 border rounded mb-2">
                            <div>
                                <strong><?php echo e($item->document->type_document); ?></strong>
                                <small class="text-muted d-block">Quantity: <?php echo e($item->quantity); ?></small>
                            </div>
                            <div class="text-end">
                                <?php if($item->price > 0): ?>
                                    <strong class="text-success">₱<?php echo e(number_format($item->price * $item->quantity, 2)); ?></strong>
                                <?php else: ?>
                                    <span class="text-muted">Free</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                
                <?php if($studentRequest->total_cost > 0): ?>
                <div class="info-card">
                    <h6 class="mb-3"><i class="bi bi-credit-card me-2"></i>Payment Status</h6>
                    <?php if($studentRequest->payment_approved): ?>
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            <strong>Payment Approved</strong> - Your payment has been verified and approved.
                        </div>
                    <?php elseif($studentRequest->payment_receipt_path): ?>
                        <div class="alert alert-info">
                            <i class="bi bi-clock-fill me-2"></i>
                            <strong>Payment Under Review</strong> - Your payment receipt is being reviewed by our accounting team.
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <strong>Payment Required</strong> - Please upload your payment receipt to continue processing.
                        </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                
                <div class="d-flex justify-content-center mb-4">
                    <div class="d-flex align-items-center justify-content-between w-100 timeline-steps-container" style="max-width: 700px; padding: 0 30px;">
                        <?php $__currentLoopData = $steps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($index > 0): ?>
                                <div style="height: 4px; flex-grow: 1; background-color: <?php echo e($index <= $currentStepIndex ? '#28a745' : '#ced4da'); ?>; margin: 0 -10px; z-index: 0;"></div>
                            <?php endif; ?>
                            <div class="timeline-step position-relative" style="z-index: 1;">
                                <div class="mb-2 d-flex justify-content-center">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                                         style="width: 50px; height: 50px;
                                                background-color: <?php echo e($index < $currentStepIndex ? '#28a745' : ($index === $currentStepIndex ? '#0d6efd' : '#dee2e6')); ?>;
                                                color: #fff; font-size: 20px;">
                                        <?php echo e($step['icon']); ?>

                                    </div>
                                </div>
                                <div class="fw-semibold small <?php echo e($index < $currentStepIndex ? 'text-success' : ($index === $currentStepIndex ? 'text-primary' : 'text-muted')); ?>">
                                    <?php echo e($step['label']); ?>

                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>

                
                <div class="text-center">
                    <?php if($studentRequest->status === 'pending'): ?>
                        <?php if($studentRequest->total_cost > 0 && !$studentRequest->payment_receipt_path): ?>
                            <div class="alert alert-warning">
                                <h6 class="alert-heading"><i class="bi bi-credit-card me-2"></i>Payment Required</h6>
                                <p>Please upload your payment receipt to proceed with your document request.</p>
                            </div>
                        <?php elseif($studentRequest->payment_receipt_path && !$studentRequest->payment_approved): ?>
                            <div class="alert alert-info">
                                <h6 class="alert-heading"><i class="bi bi-clock me-2"></i>Payment Under Review</h6>
                                <p>Your payment receipt is being reviewed by our accounting team.</p>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-success">
                                <h6 class="alert-heading"><i class="bi bi-check-circle me-2"></i>Request Submitted</h6>
                                <p>Your document request has been submitted and is ready for processing.</p>
                            </div>
                        <?php endif; ?>

                    <?php elseif($studentRequest->status === 'processing'): ?>
                        <div class="alert alert-info">
                            <h6 class="alert-heading"><i class="bi bi-gear me-2"></i>Being Processed</h6>
                            <p>Your document request is currently being processed by our registrar team.</p>
                        </div>

                    <?php elseif($studentRequest->status === 'ready_for_release'): ?>
                        <div class="alert alert-primary">
                            <h6 class="alert-heading"><i class="bi bi-box-seam me-2"></i>Ready</h6>
                            <p>Your document is ready for pickup or download.</p>
                        </div>

                    <?php elseif($studentRequest->status === 'completed'): ?>
                        <div class="alert alert-success">
                            <h6 class="alert-heading"><i class="bi bi-check-circle-fill me-2"></i>Completed</h6>
                            <p>Your document request has been completed successfully!</p>
                        </div>
                    <?php endif; ?>
                </div>

                
                <div class="text-center mt-4">
                    <a href="<?php echo e(route('public.track')); ?>" class="btn btn-primary me-2">
                        <i class="bi bi-search me-2"></i>Track Another Request
                    </a>
                    <a href="<?php echo e(route('login')); ?>" class="btn btn-outline-primary">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Student Login
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
</body>
</html><?php /**PATH D:\Nu-Regisv2\resources\views\public\track-result.blade.php ENDPATH**/ ?>