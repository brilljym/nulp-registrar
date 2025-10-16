<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Your Request - NU Lipa</title>
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
            position: relative;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* Enhanced mobile touch interactions */
        .btn, .form-control, .card {
            -webkit-tap-highlight-color: transparent;
        }

        /* Timeline container improvements */
        .timeline-steps-container {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .timeline-steps-container::-webkit-scrollbar {
            display: none;
        }

        /* Smooth animations and interactions */
        .card, .btn, .alert {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        /* Better focus indicators for accessibility */
        .btn:focus-visible,
        .form-control:focus-visible {
            outline: 2px solid var(--primary-blue);
            outline-offset: 2px;
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

        /* Enhanced Header */
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

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 5rem 1rem 3rem;
            min-height: calc(100vh - 7rem);
            position: relative;
            width: 100%;
        }

        /* Footer */
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

        /* Timeline Styles */
        .timeline-container {
            background: var(--nu-white);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 2rem;
            margin: 2rem auto;
            max-width: 1200px;
        }

        .timeline-header {
            background: linear-gradient(135deg, var(--nu-blue) 0%, #001f5f 100%);
            color: var(--nu-white);
            padding: 1.5rem;
            text-align: center;
            border-radius: 15px;
            margin-bottom: 2rem;
        }

        .timeline-header h3 {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
        }

        /* Window Cards Styling */
        .window-card {
            transition: all 0.2s ease;
            cursor: default;
        }

        .window-card:hover {
            transform: translateY(-2px);
        }

        .window-card.current-window {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }

        .processing-card {
            border: 2px solid var(--nu-blue);
            background: linear-gradient(135deg, rgba(0, 51, 153, 0.05) 0%, rgba(0, 51, 153, 0.1) 100%);
        }

        .release-card {
            background: linear-gradient(135deg, rgba(255, 215, 0, 0.1) 0%, rgba(255, 215, 0, 0.2) 100%);
        }

        /* Alert Enhancements */
        .alert {
            border-radius: 12px;
            border: none;
            margin-bottom: 1.5rem;
            font-weight: 500;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            color: #059669;
            border-left: 4px solid #10b981;
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
            border-left: 4px solid #ef4444;
        }

        .alert-info {
            background: rgba(59, 130, 246, 0.1);
            color: #2563eb;
            border-left: 4px solid #3b82f6;
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

            .timeline-container {
                margin: 1rem;
                padding: 1.5rem;
                border-radius: 15px;
            }

            .timeline-header {
                padding: 1.25rem;
                border-radius: 12px;
                margin-bottom: 1.5rem;
            }

            .timeline-header h3 {
                font-size: 1.25rem;
            }

            /* Timeline responsiveness */
            .timeline-steps-container {
                padding: 0 15px !important;
                max-width: 100% !important;
                overflow-x: auto;
                scrollbar-width: none;
            }

            .timeline-steps-container::-webkit-scrollbar {
                display: none;
            }

            /* Timeline steps */
            .position-relative {
                width: 60px !important;
                margin: 0 5px;
            }

            .rounded-circle {
                width: 45px !important;
                height: 45px !important;
                font-size: 18px !important;
            }

            .fw-semibold.small {
                font-size: 0.7rem !important;
                line-height: 1.2;
                margin-top: 0.25rem;
            }

            /* Cards and alerts */
            .card {
                margin-bottom: 1rem;
                border-radius: 12px;
            }

            .card-header {
                padding: 1rem;
                border-radius: 12px 12px 0 0;
            }

            .card-body {
                padding: 1.25rem;
            }

            .alert {
                margin-bottom: 1rem;
                padding: 1rem;
                border-radius: 10px;
            }

            /* Content spacing improvements */
            .row {
                margin-left: -0.5rem;
                margin-right: -0.5rem;
            }

            .row > * {
                padding-left: 0.5rem;
                padding-right: 0.5rem;
            }

            /* Better text sizing for mobile */
            h3 {
                font-size: 1.5rem;
            }

            h4 {
                font-size: 1.35rem;
            }

            h5 {
                font-size: 1.2rem;
            }

            h6 {
                font-size: 1rem;
            }

            /* Button responsiveness */
            .btn {
                padding: 0.75rem 1rem;
                font-size: 0.9rem;
            }

            .btn-lg {
                padding: 0.875rem 1.25rem;
                font-size: 1rem;
            }

            /* Form controls */
            .form-control {
                padding: 0.75rem;
                font-size: 0.9rem;
                border-radius: 8px;
            }

            .modal-dialog {
                margin: 0.75rem;
                max-width: calc(100vw - 1.5rem);
            }

            .modal-content {
                border-radius: 15px;
            }

            .modal-header {
                padding: 1rem 1.25rem;
            }

            .modal-body {
                padding: 1.25rem;
            }

            .nu-footer {
                padding: 0.5rem 1rem;
                font-size: 0.7rem;
                flex-direction: column;
                gap: 0.25rem;
                text-align: center;
                height: auto;
            }

            .footer-right {
                text-align: center;
                font-size: 0.65rem;
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

            .timeline-container {
                margin: 0.5rem;
                padding: 1.25rem;
                border-radius: 12px;
            }

            .timeline-header {
                padding: 1rem;
                margin-bottom: 1.25rem;
            }

            .timeline-header h3 {
                font-size: 1.125rem;
            }

            /* Timeline steps for smaller screens */
            .timeline-steps-container {
                padding: 0 10px !important;
                flex-wrap: nowrap;
                overflow-x: auto;
                scrollbar-width: none;
            }

            .position-relative {
                width: 50px !important;
                flex-shrink: 0;
                margin: 0 2px;
            }

            .rounded-circle {
                width: 40px !important;
                height: 40px !important;
                font-size: 16px !important;
            }

            .fw-semibold.small {
                font-size: 0.65rem !important;
                white-space: nowrap;
            }

            /* Cards */
            .card-header {
                padding: 0.875rem;
            }

            .card-body {
                padding: 1rem;
            }

            .card-header h6 {
                font-size: 0.95rem;
            }

            .alert {
                padding: 0.875rem;
                font-size: 0.9rem;
            }

            /* Better text sizing for smaller screens */
            h3 {
                font-size: 1.25rem;
            }

            h4 {
                font-size: 1.15rem;
            }

            h5 {
                font-size: 1.1rem;
            }

            .text-center p {
                font-size: 0.9rem;
            }

            .small, small {
                font-size: 0.8rem;
            }

            /* Buttons */
            .btn {
                padding: 0.65rem 0.875rem;
                font-size: 0.875rem;
            }

            .btn-lg {
                padding: 0.75rem 1rem;
                font-size: 0.95rem;
            }

            /* Form controls */
            .form-control {
                padding: 0.65rem;
                font-size: 0.875rem;
            }

            .modal-dialog {
                margin: 0.5rem;
                max-width: calc(100vw - 1rem);
            }

            .modal-header {
                padding: 0.875rem 1rem;
            }

            .modal-title {
                font-size: 0.95rem;
            }

            .modal-body {
                padding: 1rem;
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

            .timeline-container {
                margin: 0.25rem;
                padding: 1rem;
                border-radius: 10px;
            }

            .timeline-header {
                padding: 0.875rem;
                margin-bottom: 1rem;
            }

            .timeline-header h3 {
                font-size: 1rem;
            }

            /* Compact timeline for very small screens */
            .timeline-steps-container {
                padding: 0 5px !important;
                gap: 5px;
                overflow-x: auto;
            }

            .position-relative {
                width: 45px !important;
            }

            .rounded-circle {
                width: 35px !important;
                height: 35px !important;
                font-size: 14px !important;
            }

            .fw-semibold.small {
                font-size: 0.6rem !important;
                margin-top: 0.125rem;
            }

            /* Cards */
            .card-header {
                padding: 0.75rem;
            }

            .card-body {
                padding: 0.875rem;
            }

            .alert {
                padding: 0.75rem;
                font-size: 0.825rem;
            }

            /* Buttons */
            .btn {
                padding: 0.625rem 0.75rem;
                font-size: 0.825rem;
            }

            .btn-lg {
                padding: 0.7rem 0.875rem;
                font-size: 0.9rem;
            }

            .modal-dialog {
                margin: 0.25rem;
                max-width: calc(100vw - 0.5rem);
            }

            .modal-header {
                padding: 0.75rem 0.875rem;
            }

            .modal-body {
                padding: 0.875rem;
            }

            /* Compact text for very small screens */
            h3 {
                font-size: 1.1rem;
                line-height: 1.3;
            }

            h4 {
                font-size: 1.05rem;
            }

            h5 {
                font-size: 1rem;
            }

            .text-center p {
                font-size: 0.85rem;
                line-height: 1.4;
            }

            .small, small {
                font-size: 0.75rem;
            }

            /* Better spacing for content */
            .container {
                padding-left: 0.5rem;
                padding-right: 0.5rem;
            }

            .mt-5 {
                margin-top: 2rem !important;
            }

            .mb-4 {
                margin-bottom: 1.5rem !important;
            }
        }
    </style>
</head>
<body>
    <div class="bg-overlay"></div>
    
    <div class="site-content">
        <!-- Enhanced Header -->
        <header class="nu-header">
            <div class="nu-logo-container">
                <img src="<?php echo e(asset('images/NU_shield.svg.png')); ?>" alt="NU Shield" class="nu-shield">
                <span class="nu-title">NU LIPA</span>
            </div>
            <span class="nu-welcome">Request Tracking System</span>
        </header>

        <!-- Main Content -->
        <main class="main-content">
            <?php
                $steps = [
                    ['label' => 'Start', 'icon' => '📝', 'step' => 'start'],
                    ['label' => 'Registrar Approval', 'icon' => '✅', 'step' => 'registrar_approved'],
                    ['label' => 'Payment', 'icon' => '💸', 'step' => 'payment'],
                    ['label' => 'Processing', 'icon' => '⚙️', 'step' => 'processing'],
                    ['label' => 'Ready', 'icon' => '📦', 'step' => 'release'],
                    ['label' => 'Completed', 'icon' => '✅', 'step' => 'completed'],
                ];
                $currentIndex = 0;
                if ($onsiteRequest->status === 'registrar_approved' && $onsiteRequest->current_step !== 'payment') {
                    $currentIndex = 1;
                } elseif ($onsiteRequest->current_step === 'payment') {
                    // If payment is approved, move to next step, otherwise stay in payment step
                    if ($onsiteRequest->payment_approved) {
                        $currentIndex = 3; // Skip to processing since window step is removed
                    } else {
                        $currentIndex = 2; // Stay in payment step if waiting for approval
                    }
                } elseif ($onsiteRequest->current_step === 'processing') {
                    $currentIndex = 3;
                } elseif ($onsiteRequest->current_step === 'release' && $onsiteRequest->status === 'released') {
                    $currentIndex = 4; // Stay at "Ready" when registrar marks as ready
                } elseif ($onsiteRequest->status === 'completed') {
                    $currentIndex = 5; // Final step - completed
                }

                // Calculate ticket number once for all applicable steps
                $ticketNumber = $onsiteRequest->queue_number ?? 'ticket-no:' . $onsiteRequest->created_at->format('Ymd') . '-i' . $onsiteRequest->id;
            ?>

            <div class="timeline-container">
                <div class="timeline-header">
                    <h3><i class="bi bi-geo-alt-fill me-2"></i>Track Your Request</h3>
                    <p style="margin: 0; font-size: 0.9rem; opacity: 0.9;">Requested Document: <?php echo e($onsiteRequest->requestItems->pluck('document.type_document')->join(', ')); ?></p>
                </div>
                
                <div class="container py-3">
    <h3 class="text-center mb-4 text-primary">Track Your Request</h3>

    
    <?php $__currentLoopData = ['success', 'error', 'info']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if(session($msg)): ?>
            <div class="alert alert-<?php echo e($msg === 'error' ? 'danger' : ($msg === 'info' ? 'info' : 'success')); ?> text-center">
                <?php echo e(session($msg)); ?>

            </div>
        <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    
    <div class="d-flex justify-content-center">
        <div class="d-flex align-items-center justify-content-between w-100 timeline-steps-container" style="max-width: 1100px; padding: 0 30px;">
            <?php $__currentLoopData = $steps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if($index > 0): ?>
                    <div style="height: 4px; flex-grow: 1; background-color: #ced4da; margin: 0 -10px; z-index: 0;"></div>
                <?php endif; ?>

                <div class="text-center position-relative" style="width: 90px; z-index: 1;">
                    <div class="mb-2 d-flex justify-content-center">
                        <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                             style="width: 55px; height: 55px;
                                    background-color:
                                        <?php echo e($index < $currentIndex ? '#198754' : ($index === $currentIndex ? '#0d6efd' : '#dee2e6')); ?>;
                                    color: white; font-size: 24px;">
                            <?php echo e($step['icon']); ?>

                        </div>
                    </div>
                    <div class="fw-semibold small
                        <?php echo e($index < $currentIndex ? 'text-success' : ($index === $currentIndex ? 'text-primary' : 'text-muted')); ?>">
                        <?php echo e($step['label']); ?>

                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    
    
    <?php if($onsiteRequest->status === 'pending'): ?>
        <div class="row justify-content-center mt-5">
            <div class="col-md-8">
                <div class="card shadow-sm border-info">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="bi bi-clock me-2"></i>Awaiting Registrar Approval</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Documents Requested:</strong>
                                <ul class="list-unstyled mb-2">
                                    <?php $__currentLoopData = $onsiteRequest->requestItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li>• <?php echo e($item->document->type_document); ?> (x<?php echo e($item->quantity); ?>)</li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                                <strong>Requester:</strong> <?php echo e($onsiteRequest->full_name ?? 'N/A'); ?><br>
                                <strong>Reference Code:</strong> <span class="text-primary fw-bold"><?php echo e($onsiteRequest->ref_code); ?></span><br>
                                <?php if($onsiteRequest->course): ?>
                                    <strong>Course:</strong> <?php echo e($onsiteRequest->course); ?><br>
                                <?php endif; ?>
                                <?php if($onsiteRequest->reason): ?>
                                    <strong>Reason:</strong> <?php echo e($onsiteRequest->reason); ?><br>
                                <?php endif; ?>
                                <?php if($onsiteRequest->remarks): ?>
                                    <strong>Remarks:</strong> <span class="text-muted"><?php echo e($onsiteRequest->remarks); ?></span><br>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i>
                                    <strong>Your request is being reviewed by the Registrar.</strong><br>
                                    Once approved, you will be able to proceed with payment.
                                </div>
                                <small class="text-muted">
                                    Please wait at the counter. You will be notified when your request is approved.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php elseif($onsiteRequest->current_step === 'payment'): ?>
        <div class="row justify-content-center mt-5">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Document Payment Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Documents Requested:</strong>
                                <ul class="list-unstyled mb-2">
                                    <?php $__currentLoopData = $onsiteRequest->requestItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li>• <?php echo e($item->document->type_document); ?> (x<?php echo e($item->quantity); ?>)</li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                                <strong>Requester:</strong> <?php echo e($onsiteRequest->full_name ?? 'N/A'); ?><br>
                                <strong>Reference Code:</strong> <span class="text-primary fw-bold"><?php echo e($onsiteRequest->ref_code); ?></span><br>
                                <?php if($onsiteRequest->course): ?>
                                    <strong>Course:</strong> <?php echo e($onsiteRequest->course); ?><br>
                                <?php endif; ?>
                                <?php if($onsiteRequest->reason): ?>
                                    <strong>Reason:</strong> <?php echo e($onsiteRequest->reason); ?><br>
                                <?php endif; ?>
                                <?php if($onsiteRequest->remarks): ?>
                                    <strong>Remarks:</strong> <span class="text-muted"><?php echo e($onsiteRequest->remarks); ?></span><br>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <?php
                                    $totalPrice = $onsiteRequest->requestItems->sum(function($item) {
                                        return $item->document->price * $item->quantity;
                                    });
                                    $isFree = $totalPrice == 0;
                                ?>
                                <div class="payment-info">
                                    <strong>Payment Breakdown:</strong>
                                    <?php if($isFree): ?>
                                        <div class="mt-2">
                                            <span class="badge bg-success fs-6">FREE</span>
                                        </div>
                                    <?php else: ?>
                                        <div class="mt-2">
                                            <?php $__currentLoopData = $onsiteRequest->requestItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <div class="d-flex justify-content-between mb-1">
                                                    <small><?php echo e($item->document->type_document); ?> (x<?php echo e($item->quantity); ?>)</small>
                                                    <small>₱<?php echo e(number_format($item->document->price * $item->quantity, 2)); ?></small>
                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <hr class="my-2">
                                            <div class="d-flex justify-content-between fw-bold">
                                                <span>Total:</span>
                                                <span>₱<?php echo e(number_format($totalPrice, 2)); ?></span>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <br>
                                    <small class="text-muted">
                                        <?php if($isFree): ?>
                                            No payment required for these documents
                                        <?php else: ?>
                                            Payment must be made at the Onsite Service Counter
                                        <?php endif; ?>
                                    </small>
                                </div>
                                <br><strong>Total Quantity:</strong> <?php echo e($onsiteRequest->requestItems->sum('quantity')); ?>

                                <br><br>
                                <div class="text-center">
                                    <img src="<?php echo e(asset('images/qr-display.jpg')); ?>" alt="Payment QR Code" class="img-fluid" style="max-width: 200px; max-height: 200px;">
                                    <p class="text-muted mt-2 small">Scan QR code for payment</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    
    <div class="text-center mt-5">

        <?php if($onsiteRequest->current_step === 'payment'): ?>
            <?php if($onsiteRequest->payment_approved): ?>
                <h5 class="mb-3 text-success">✅ Payment Approved - Ready for Processing</h5>
                <div class="alert alert-success mx-auto" style="max-width: 500px;">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    Your payment has been verified by accounting. Your request will now proceed to processing.
                </div>
                <form method="POST" action="<?php echo e(route('onsite.reference.submit')); ?>" class="d-flex justify-content-center align-items-center gap-2">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="onsite_id" value="<?php echo e($onsiteRequest->id); ?>">
                    <input type="hidden" name="ref_code" value="">
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="bi bi-arrow-right me-2"></i>Proceed to Processing
                    </button>
                </form>
            <?php elseif($onsiteRequest->payment_receipt_path): ?>
                <h5 class="mb-3 text-warning">⏳ Payment Receipt Submitted - Awaiting Approval</h5>
                <div class="alert alert-warning mx-auto" style="max-width: 500px;">
                    <i class="bi bi-clock me-2"></i>
                    Your payment receipt has been submitted and is being reviewed by accounting.
                    You will be notified once approved.
                </div>
                <div class="text-center">
                    <p class="text-muted">Receipt submitted: <?php echo e($onsiteRequest->updated_at->diffForHumans()); ?></p>
                </div>
            <?php else: ?>
                <h5 class="mb-3">Upload Onsite Service Payment Receipt</h5>
                <div class="alert alert-info mx-auto" style="max-width: 500px;">
                    <i class="bi bi-upload me-2"></i>
                    Please upload your payment receipt for onsite document services to proceed with processing.
                </div>
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <form method="POST" action="<?php echo e(route('onsite.upload.receipt', $onsiteRequest)); ?>" enctype="multipart/form-data">
                                    <?php echo csrf_field(); ?>
                                    <div class="mb-3">
                                        <label for="payment_receipt" class="form-label">Onsite Service Payment Receipt</label>
                                        <input type="file" class="form-control" id="payment_receipt" name="payment_receipt"
                                               accept="image/*" required>
                                        <div class="form-text">Accepted formats: JPG, PNG, GIF. Max size: 2MB</div>
                                    </div>
                                    <div class="text-center">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-upload me-2"></i>Upload Receipt
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>





        <?php elseif($onsiteRequest->current_step === 'processing'): ?>
            <h5 class="mb-3">📄 Your document is now being processed</h5>
            
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="bi bi-person-badge-fill text-primary" style="font-size: 2rem;"></i>
                            </div>
                            <h6 class="text-muted mb-1">Processing by</h6>
                            <?php if($onsiteRequest->registrar): ?>
                                <h5 class="text-primary mb-2">
                                    <?php echo e($onsiteRequest->registrar->first_name ?? 'Registrar'); ?> 
                                    <?php echo e($onsiteRequest->registrar->last_name ?? ''); ?>

                                </h5>
                            <?php else: ?>
                                <p class="text-muted mb-0"><em>Registrar not yet assigned</em></p>
                            <?php endif; ?>
                            <hr class="my-2">
                            <p class="text-monospace text-secondary mb-0" style="font-size: 0.8rem;"><?php echo e($ticketNumber); ?></p>
                        </div>
                    </div>
                </div>
            </div>

        <?php elseif($onsiteRequest->current_step === 'release' && $onsiteRequest->status === 'released'): ?>
            <div class="alert alert-info mt-3">
                <i class="bi bi-envelope-check me-2"></i>
                Please monitor your email for updates regarding your document request. Your requested document will be ready to pick up in 3 to 5 business days. Thank you for your patience!
            </div>

        <?php elseif($onsiteRequest->current_step === 'completed'): ?>
            <h4 class="text-success">✅ Your document request has been completed. Thank you!</h4>
            
            <div class="row justify-content-center mt-4">
                <div class="col-md-8">
                    <div class="card border-success shadow-sm">
                        <div class="card-header bg-success text-white text-center">
                            <h6 class="mb-0"><i class="bi bi-check-circle-fill me-2"></i>Request Completed Successfully</h6>
                        </div>
                        <div class="card-body text-center">
                            <p class="mb-3">Your request has been completed successfully. We hope you had a great experience with our service.</p>
                            
                            
                            <?php if($onsiteRequest->feedback): ?>
                                <div class="alert alert-info">
                                    <i class="bi bi-heart-fill me-2"></i>Thank you for your feedback! 
                                    Your rating: 
                                    <?php for($i = 1; $i <= $onsiteRequest->feedback->rating; $i++): ?>
                                        <span class="text-warning">⭐</span>
                                    <?php endfor; ?>
                                </div>
                            <?php else: ?>
                                <div class="mb-3">
                                    <h6 class="text-muted">How was your experience?</h6>
                                    <p class="small text-muted">Your feedback helps us improve our services</p>
                                    <a href="<?php echo e(route('onsite.feedback.show', $onsiteRequest->id)); ?>" class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-chat-heart me-2"></i>Provide Feedback (Optional)
                                    </a>
                                </div>
                            <?php endif; ?>
                            
                            <hr class="my-3">
                            <p class="text-monospace text-secondary mb-2"><?php echo e($ticketNumber); ?></p>
                            <p id="countdown" class="fs-6 mt-3 text-muted">Redirecting to login in <span id="timer">10</span> seconds...</p>
                            <button onclick="skipRedirect()" class="btn btn-link btn-sm text-decoration-none">Skip and stay on this page</button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>


<?php if(!$onsiteRequest->full_name || !$onsiteRequest->course || !$onsiteRequest->year_level || !$onsiteRequest->department): ?>
    <script>
        window.addEventListener('load', function () {
            const modal = new bootstrap.Modal(document.getElementById('infoModal'));
            modal.show();
        });
    </script>
<?php endif; ?>


<div class="modal fade" id="infoModal" tabindex="-1" aria-labelledby="infoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form method="POST" action="<?php echo e(route('onsite.update', $onsiteRequest->id)); ?>" class="modal-content">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <div class="modal-header">
                <h5 class="modal-title" id="infoModalLabel">Enter Your Information</h5>
            </div>
            <div class="modal-body row g-3">
                <div class="col-md-6">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="full_name" class="form-control" value="<?php echo e($onsiteRequest->full_name); ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Student ID (if any)</label>
                    <input type="text" name="student_id" class="form-control" value="<?php echo e($onsiteRequest->student_id); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Course</label>
                    <input type="text" name="course" class="form-control" value="<?php echo e($onsiteRequest->course); ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Year Level</label>
                    <input type="text" name="year_level" class="form-control" value="<?php echo e($onsiteRequest->year_level); ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Department</label>
                    <input type="text" name="department" class="form-control" value="<?php echo e($onsiteRequest->department); ?>" required>
                </div>

                <div class="col-12">
                    <h6>Document Request</h6>
                    <div id="modal-documents-container">
                        <?php if($onsiteRequest->requestItems->count() > 0): ?>
                            <?php $__currentLoopData = $onsiteRequest->requestItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="document-item mb-3 p-3 border rounded">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Document Type *</label>
                                            <select class="form-select document-select" name="documents[<?php echo e($index); ?>][document_id]" required>
                                                <option value="" disabled>-- Select Document Type --</option>
                                                <?php $__currentLoopData = $documents ?? collect(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($document->id); ?>" data-price="<?php echo e($document->price); ?>" <?php echo e($item->document_id == $document->id ? 'selected' : ''); ?>>
                                                        <?php echo e($document->type_document); ?> <?php if($document->price > 0): ?> (₱<?php echo e($document->price); ?>) <?php endif; ?>
                                                    </option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Quantity *</label>
                                            <input type="number" class="form-control quantity-input" name="documents[<?php echo e($index); ?>][quantity]"
                                                   value="<?php echo e($item->quantity); ?>" min="1" max="150" required>
                                        </div>
                                        <div class="col-md-2 mb-3 d-flex align-items-end">
                                            <button type="button" class="btn btn-danger remove-document" style="<?php echo e($onsiteRequest->requestItems->count() > 1 ? '' : 'display: none;'); ?>">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php else: ?>
                            <div class="document-item mb-3 p-3 border rounded">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Document Type *</label>
                                        <select class="form-select document-select" name="documents[0][document_id]" required>
                                            <option value="" disabled selected>-- Select Document Type --</option>
                                            <?php $__currentLoopData = $documents ?? collect(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($document->id); ?>" data-price="<?php echo e($document->price); ?>">
                                                    <?php echo e($document->type_document); ?> <?php if($document->price > 0): ?> (₱<?php echo e($document->price); ?>) <?php endif; ?>
                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Quantity *</label>
                                        <input type="number" class="form-control quantity-input" name="documents[0][quantity]"
                                               value="1" min="1" max="150" required>
                                    </div>
                                    <div class="col-md-2 mb-3 d-flex align-items-end">
                                        <button type="button" class="btn btn-danger remove-document" style="display: none;">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <button type="button" class="btn btn-outline-primary mb-3" id="modal-add-document">
                        <i class="bi bi-plus-circle me-2"></i>Add Another Document
                    </button>

                    <div class="mb-3">
                        <div class="alert alert-info">
                            <strong>Total Cost:</strong> <span id="modal-total-cost">₱0.00</span>
                            <div id="modal-cost-breakdown" class="mt-2 small text-muted"></div>
                        </div>
                    </div>
                </div>

                <!-- Include reason if it exists -->
                <?php if($onsiteRequest->reason): ?>
                <input type="hidden" name="reason" value="<?php echo e($onsiteRequest->reason); ?>">
                <?php endif; ?>
                <!-- Include document_id -->
                <input type="hidden" name="document_id" value="<?php echo e($onsiteRequest->document_id); ?>">
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Proceed to Payment</button>
            </div>
        </form>
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
    
    <!-- Pusher JS for Real-time Updates -->
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script>
    // Initialize Pusher
    const pusher = new Pusher('<?php echo e(config('broadcasting.connections.pusher.key')); ?>', {
        cluster: '<?php echo e(config('broadcasting.connections.pusher.options.cluster')); ?>',
        encrypted: true
    });

    // Debug Pusher connection
    pusher.connection.bind('connected', function() {
        console.log('✅ Timeline Pusher connected successfully');
    });

    pusher.connection.bind('error', function(err) {
        console.error('❌ Timeline Pusher connection error:', err);
    });

    pusher.connection.bind('disconnected', function() {
        console.log('⚠️ Timeline Pusher disconnected');
    });

    // Subscribe to request-specific channel for this request
    const channelName = 'request-<?php echo e($onsiteRequest->ref_code); ?>';
    console.log('📡 Timeline subscribing to channel:', channelName);
    const requestChannel = pusher.subscribe(channelName);
    
    // Debug channel subscription
    requestChannel.bind('pusher:subscription_succeeded', function() {
        console.log('✅ Timeline successfully subscribed to channel:', channelName);
    });
    
    requestChannel.bind('pusher:subscription_error', function(err) {
        console.error('❌ Timeline subscription error:', err);
    });        // Listen for status updates
        requestChannel.bind('realtime.notification', function(data) {
            console.log('🔄 Timeline received real-time update:', data);
            
            // Show notification
            showNotification(data.message || 'Status updated', data.type);
            
            // Refresh the page to show updated status
            setTimeout(() => {
                console.log('🔄 Refreshing timeline page...');
                window.location.reload();
            }, 2000);
        });

        // Fallback: Poll for updates every 30 seconds
        setInterval(function() {
            console.log('🔄 Polling for timeline updates...');
            fetch(window.location.href, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html'
                }
            })
            .then(response => response.text())
            .then(html => {
                // Check if the status has changed by looking for specific indicators
                const currentStep = '<?php echo e($onsiteRequest->current_step); ?>';
                const paymentApproved = '<?php echo e($onsiteRequest->payment_approved ? 'true' : 'false'); ?>';
                
                // Simple check: if the HTML contains different status indicators
                if ((currentStep === 'payment' && html.includes('document is now being processed')) ||
                    (currentStep === 'payment' && !html.includes('Payment Receipt Submitted') && paymentApproved === 'false')) {
                    console.log('🔄 Status change detected via polling, refreshing...');
                    window.location.reload();
                }
            })
            .catch(error => {
                console.log('🔄 Polling error:', error);
            });
        }, 30000); // Check every 30 seconds

        // Function to show notifications
        function showNotification(message, type = 'info') {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `alert alert-${getBootstrapClass(type)} alert-dismissible fade show position-fixed`;
            notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; max-width: 400px;';
            notification.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            // Add to page
            document.body.appendChild(notification);
            
            // Auto-remove after 5 seconds
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 5000);
        }

        // Convert notification type to Bootstrap class
        function getBootstrapClass(type) {
            switch(type) {
                case 'success': return 'success';
                case 'error': return 'danger';
                case 'warning': return 'warning';
                case 'status-update': return 'info';
                default: return 'info';
            }
        }

        // Clean up on page unload
        window.addEventListener('beforeunload', function() {
            pusher.disconnect();
        });
    </script>

    <!-- Enhanced Mobile Interactions -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Enhanced mobile touch interactions
            const timeline = document.querySelector('.timeline-steps-container');
            
            // Add smooth scrolling for timeline on mobile
            if (timeline) {
                timeline.style.scrollBehavior = 'smooth';
                
                // Add swipe detection for timeline navigation
                let isDown = false;
                let startX;
                let scrollLeft;

                timeline.addEventListener('mousedown', (e) => {
                    isDown = true;
                    timeline.classList.add('active');
                    startX = e.pageX - timeline.offsetLeft;
                    scrollLeft = timeline.scrollLeft;
                });

                timeline.addEventListener('mouseleave', () => {
                    isDown = false;
                    timeline.classList.remove('active');
                });

                timeline.addEventListener('mouseup', () => {
                    isDown = false;
                    timeline.classList.remove('active');
                });

                timeline.addEventListener('mousemove', (e) => {
                    if (!isDown) return;
                    e.preventDefault();
                    const x = e.pageX - timeline.offsetLeft;
                    const walk = (x - startX) * 2;
                    timeline.scrollLeft = scrollLeft - walk;
                });
            }

            // Add ripple effects to buttons
            function createRipple(e) {
                const button = e.currentTarget;
                const circle = document.createElement("span");
                const diameter = Math.max(button.clientWidth, button.clientHeight);
                const radius = diameter / 2;

                circle.style.width = circle.style.height = `${diameter}px`;
                circle.style.left = `${e.clientX - button.offsetLeft - radius}px`;
                circle.style.top = `${e.clientY - button.offsetTop - radius}px`;
                circle.classList.add("ripple");

                const ripple = button.getElementsByClassName("ripple")[0];
                if (ripple) {
                    ripple.remove();
                }

                button.appendChild(circle);
            }

            // Apply ripple effect to buttons
            const buttons = document.querySelectorAll('.btn');
            buttons.forEach(btn => {
                btn.addEventListener('click', createRipple);
                btn.style.position = 'relative';
                btn.style.overflow = 'hidden';
            });

            // Add CSS for ripple effect
            const style = document.createElement('style');
            style.textContent = `
                .ripple {
                    position: absolute;
                    border-radius: 50%;
                    background-color: rgba(255, 255, 255, 0.4);
                    transform: scale(0);
                    animation: ripple-animation 0.6s linear;
                    pointer-events: none;
                }
                
                @keyframes ripple-animation {
                    to {
                        transform: scale(4);
                        opacity: 0;
                    }
                }
                
                .timeline-steps-container.active {
                    cursor: grabbing;
                    cursor: -webkit-grabbing;
                }
                
                .timeline-steps-container {
                    cursor: grab;
                    cursor: -webkit-grab;
                }
                
                /* Smooth transitions for mobile */
                .card, .alert, .btn {
                    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
                }
                
                /* Better touch targets for mobile */
                @media (max-width: 768px) {
                    .btn {
                        min-height: 44px;
                        padding: 0.75rem 1rem;
                    }
                    
                    .form-control {
                        min-height: 44px;
                    }
                    
                    .timeline-steps-container {
                        -webkit-overflow-scrolling: touch;
                    }
                }
            `;
            document.head.appendChild(style);

            // Prevent zoom on double tap for iOS
            let lastTouchEnd = 0;
            document.addEventListener('touchend', function (event) {
                const now = (new Date()).getTime();
                if (now - lastTouchEnd <= 300) {
                    event.preventDefault();
                }
                lastTouchEnd = now;
            }, false);

            // Improve form validation feedback
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function() {
                    const submitBtn = this.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = submitBtn.innerHTML.replace(/^/, '<i class="bi bi-hourglass-split me-2"></i>');
                        setTimeout(() => {
                            if (submitBtn) {
                                submitBtn.disabled = false;
                            }
                        }, 3000);
                    }
                });
            });

            // Modal document management functionality
            let modalDocumentIndex = <?php echo e($onsiteRequest->requestItems->count() ?: 1); ?>;

            function updateModalTotalCost() {
                let total = 0;
                let breakdownHtml = '';

                document.querySelectorAll('#modal-documents-container .document-item').forEach(item => {
                    const select = item.querySelector('.document-select');
                    const quantity = item.querySelector('.quantity-input');
                    const selectedOption = select.options[select.selectedIndex];
                    const price = selectedOption ? parseFloat(selectedOption.getAttribute('data-price') || 0) : 0;
                    const qty = parseInt(quantity.value) || 0;
                    const itemTotal = price * qty;
                    total += itemTotal;

                    if (selectedOption && selectedOption.value && qty > 0) {
                        const documentName = selectedOption.text.split(' (₱')[0]; // Remove price from display name
                        breakdownHtml += `${documentName} (x${qty}) - ₱${itemTotal.toFixed(2)}<br>`;
                    }
                });

                document.getElementById('modal-total-cost').textContent = '₱' + total.toFixed(2);
                document.getElementById('modal-cost-breakdown').innerHTML = breakdownHtml;
            }

            function updateModalRemoveButtons() {
                const items = document.querySelectorAll('#modal-documents-container .document-item');
                items.forEach((item, index) => {
                    const removeBtn = item.querySelector('.remove-document');
                    if (items.length > 1) {
                        removeBtn.style.display = 'block';
                    } else {
                        removeBtn.style.display = 'none';
                    }
                });
            }

            document.getElementById('modal-add-document').addEventListener('click', function() {
                const container = document.getElementById('modal-documents-container');
                const newItem = document.createElement('div');
                newItem.className = 'document-item mb-3 p-3 border rounded';
                newItem.innerHTML = `
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Document Type *</label>
                            <select class="form-select document-select" name="documents[${modalDocumentIndex}][document_id]" required>
                                <option value="" disabled selected>-- Select Document Type --</option>
                                <?php $__currentLoopData = $documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($document->id); ?>" data-price="<?php echo e($document->price); ?>">
                                        <?php echo e($document->type_document); ?> <?php if($document->price > 0): ?> (₱<?php echo e($document->price); ?>) <?php endif; ?>
                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Quantity *</label>
                            <input type="number" class="form-control quantity-input" name="documents[${modalDocumentIndex}][quantity]"
                                   value="1" min="1" max="150" required>
                        </div>
                        <div class="col-md-2 mb-3 d-flex align-items-end">
                            <button type="button" class="btn btn-danger remove-document">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
                container.appendChild(newItem);
                modalDocumentIndex++;
                updateModalRemoveButtons();
                attachModalDocumentEvents(newItem);
            });

            function attachModalDocumentEvents(item) {
                const select = item.querySelector('.document-select');
                const quantity = item.querySelector('.quantity-input');
                const removeBtn = item.querySelector('.remove-document');

                select.addEventListener('change', updateModalTotalCost);
                quantity.addEventListener('input', updateModalTotalCost);

                removeBtn.addEventListener('click', function() {
                    item.remove();
                    updateModalTotalCost();
                    updateModalRemoveButtons();
                });
            }

            // Attach events to existing modal document items
            document.querySelectorAll('#modal-documents-container .document-item').forEach(item => {
                attachModalDocumentEvents(item);
            });

            // Initial modal total cost calculation
            updateModalTotalCost();
            updateModalRemoveButtons();
        });
    </script>

    <?php if($onsiteRequest->current_step === 'completed'): ?>
    <script>
        let count = 10;
        let countdownInterval;
        const timerEl = document.getElementById('timer');

        function startCountdown() {
            countdownInterval = setInterval(() => {
                count--;
                if (timerEl) {
                    timerEl.textContent = count;
                }
                if (count <= 0) {
                    clearInterval(countdownInterval);
                    window.location.href = "<?php echo e(route('login')); ?>";
                }
            }, 1000);
        }

        function skipRedirect() {
            if (countdownInterval) {
                clearInterval(countdownInterval);
            }
            const countdownEl = document.getElementById('countdown');
            if (countdownEl) {
                countdownEl.innerHTML = '<span class="text-success">You can stay on this page. <a href="<?php echo e(route('login')); ?>">Click here to go to login</a></span>';
            }
        }

        // Start the countdown automatically
        startCountdown();
    </script>
    <?php endif; ?>
</body>
</html>
<?php /**PATH D:\Nu-Regisv2\resources\views\onsite\timeline.blade.php ENDPATH**/ ?>