<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>On-site Document Request - NU Lipa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* ...styles omitted for brevity in patch (kept same as provided) ... */
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
            position: relative;
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
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: calc(100vh - 7rem);
            overflow: hidden;
            position: relative;
            width: 100%;
        }

        .request-container {
            width: 100%;
            max-width: 750px;
            background: var(--nu-white);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            animation: slideUp 0.8s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .request-header {
            background: linear-gradient(135deg, var(--nu-blue) 0%, #001f5f 100%);
            color: var(--nu-white);
            padding: 1.2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .request-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 215, 0, 0.1) 0%, transparent 50%);
            animation: rotate 20s linear infinite;
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .request-header h1 {
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 0.2rem;
            position: relative;
            z-index: 2;
        }

        .request-header p {
            font-size: 0.9rem;
            opacity: 0.9;
            margin: 0;
            position: relative;
            z-index: 2;
        }

        .request-body {
            padding: 2rem;
        }

        .form-section {
            margin-bottom: 2rem;
        }

        .section-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--nu-blue);
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--nu-yellow);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }

        .form-control, .form-select {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 0.875rem 1rem;
            font-size: 1rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background-color: #f8f9fa;
            color: var(--nu-gray);
        }

        .form-control::placeholder {
            color: #9ca3af;
            font-weight: 400;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--nu-blue);
            background-color: var(--nu-white);
            box-shadow: 0 0 0 3px rgba(0, 51, 153, 0.1);
            outline: none;
        }

        .form-control:hover:not(:focus) {
            border-color: #d1d5db;
        }

        /* Enhanced form validation styles */
        .form-control.is-invalid {
            border-color: #ef4444;
            background-color: rgba(239, 68, 68, 0.05);
        }

        .form-control.is-valid {
            border-color: #10b981;
            background-color: rgba(16, 185, 129, 0.05);
        }

        /* Hide check icons for academic fields, student identification fields, Document Type, Reason for Request, and Quantity when valid */
        #course.is-valid,
        #year_level.is-valid,
        #department.is-valid,
        #student-search.is-valid,
        #full_name_display.is-valid,
        .document-select.is-valid,
        #reason_select.is-valid,
        #documents-container .quantity-input.is-valid {
            background-image: none !important;
        }

        .btn-submit {
            background: linear-gradient(135deg, var(--nu-blue) 0%, #001f5f 100%);
            border: none;
            color: var(--nu-white);
            padding: 1rem 2rem;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 12px;
            width: 100%;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 30px rgba(0, 51, 153, 0.3);
            color: var(--nu-white);
            background: linear-gradient(135deg, #001f5f 0%, var(--nu-blue) 100%);
        }

        .btn-submit:focus {
            outline: 2px solid var(--nu-blue);
            outline-offset: 2px;
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .login-button:focus {
            outline: 2px solid var(--nu-yellow);
            outline-offset: 2px;
        }

        .nu-welcome {
            font-size: 0.95rem;
            font-weight: 400;
        }

        .suggestions-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 2px solid var(--nu-blue);
            border-top: none;
            border-radius: 0 0 12px 12px;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .suggestion-item {
            padding: 0.75rem 1rem;
            cursor: pointer;
            border-bottom: 1px solid #f1f3f4;
            transition: all 0.2s ease;
        }

        .suggestion-item:hover {
            background-color: #f8f9fa;
            color: var(--nu-blue);
        }

        .suggestion-item:last-child {
            border-bottom: none;
        }

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

        /* Privacy Notice Styling */
        .form-check-input {
            width: 1.2rem;
            height: 1.2rem;
            border-radius: 4px;
            border: 2px solid var(--nu-blue);
            background-color: #f8f9fa;
            transition: all 0.3s ease;
        }

        .form-check-input:checked {
            background-color: var(--nu-blue);
            border-color: var(--nu-blue);
        }

        .form-check-input:focus {
            border-color: var(--nu-blue);
            box-shadow: 0 0 0 3px rgba(0, 51, 153, 0.1);
        }

        .form-check-label {
            margin-left: 0.75rem;
            cursor: pointer;
        }

        .form-check-label a {
            color: var(--nu-blue) !important;
            text-decoration: underline;
        }

        .form-check-label a:hover {
            color: #001f5f !important;
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

            .request-container {
                max-width: min(95vw, 500px);
                margin: 0 auto;
            }

            .request-header h1 {
                font-size: 1.2rem;
            }

            .request-header p {
                font-size: 0.825rem;
            }

            .request-body {
                padding: 2rem 1.5rem;
            }

            .section-title {
                font-size: 1.1rem;
            }

            .form-label {
                font-size: 0.9rem;
            }

            .form-control, .form-select {
                padding: 0.75rem 0.875rem;
                font-size: 0.9rem;
            }

            /* Stack columns on mobile */
            .row .col-md-6,
            .row .col-md-4,
            .row .col-md-8 {
                margin-bottom: 1rem;
            }

            /* Improve form spacing on mobile */
            .mb-3 {
                margin-bottom: 1rem !important;
            }

            .btn-submit {
                padding: 0.875rem 1.5rem;
                font-size: 1rem;
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

            .request-container {
                max-width: min(92vw, 360px);
                margin: 0 auto;
                border-radius: 12px;
            }

            .request-header {
                padding: 1rem;
            }

            .request-header h1 {
                font-size: 1.1rem;
                margin-bottom: 0.1rem;
            }

            .request-header p {
                font-size: 0.775rem;
            }

            .request-body {
                padding: 1.75rem 1.25rem;
            }

            .section-title {
                font-size: 1rem;
                margin-bottom: 0.875rem;
            }

            .form-label {
                font-size: 0.875rem;
                margin-bottom: 0.375rem;
            }

            .form-control, .form-select {
                padding: 0.65rem 0.75rem;
                font-size: 0.875rem;
            }

            .btn-submit {
                padding: 0.75rem 1.25rem;
                font-size: 0.95rem;
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

            .suggestions-dropdown {
                max-height: 150px;
            }

            .suggestion-item {
                padding: 0.625rem 0.75rem;
                font-size: 0.875rem;
            }

        @media (max-width: 360px) {
            .main-content {
                padding: 3.5rem 0.25rem 3.5rem;
            }

            .request-container {
                max-width: min(95vw, 300px);
                margin: 0 auto;
            }

            .request-header {
                padding: 0.875rem;
            }

            .request-header h1 {
                font-size: 1rem;
            }

            .request-header p {
                font-size: 0.75rem;
            }

            .request-body {
                padding: 1.5rem 1rem;
            }

            .section-title {
                font-size: 0.95rem;
                margin-bottom: 0.75rem;
            }

            .form-section {
                margin-bottom: 1.5rem;
            }

            .form-label {
                font-size: 0.825rem;
            }

            .form-control, .form-select {
                padding: 0.625rem 0.75rem;
                font-size: 0.825rem;
            }

            .btn-submit {
                padding: 0.625rem 1rem;
                font-size: 0.9rem;
            }

            .suggestions-dropdown {
                max-height: 120px;
            }

            .suggestion-item {
                padding: 0.5rem 0.625rem;
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

        .request-container {
            animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Focus and accessibility improvements */
        .btn-submit:focus-visible,
        .login-button:focus-visible {
            outline: 2px solid var(--nu-blue);
            outline-offset: 2px;
        }

        .form-control:focus-visible,
        .form-select:focus-visible {
            outline: none;
        }

        /* Smooth hover states */
        .request-container:hover {
            transform: translateY(-2px);
            box-shadow: 0 25px 70px rgba(0, 0, 0, 0.4);
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

        /* Perfect centering for all screen sizes */
        .main-content {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        .request-container {
            margin: 0 auto !important;
        }

        /* Reduce motion for users who prefer it */
        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
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
            <span class="nu-welcome">Walk-In Document Service</span>
        </header>

        <!-- Main Content -->
        <main class="main-content">
            <div class="request-container">
                <div class="request-header">
                    <h1><i class="bi bi-file-earmark-text-fill me-2"></i>On-Site Document Request</h1>
                    <p>Quick and easy document processing for walk-in requests</p>
                </div>
                
                <div class="request-body">
                    <?php if(session('success')): ?>
                        <div class="alert alert-success">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                                <div>
                                    <strong>Request Submitted Successfully!</strong><br>
                                    <?php echo e(session('success')); ?>

                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                <strong>Please correct the following errors:</strong>
                            </div>
                            <ul class="mb-0 ps-3">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?php echo e(route('onsite.store')); ?>" id="onsiteForm">
                        <?php echo csrf_field(); ?>

                        <div class="form-section">
                            <div class="section-title">
                                <i class="bi bi-search"></i>
                                Student Identification
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="student-search" class="form-label">Student ID *</label>
                                    <div class="position-relative">
                                        <input type="text" id="student-search" name="student_id" class="form-control" 
                                               placeholder="Type your student ID" autocomplete="off" required 
                                               pattern="[0-9\-]+" inputmode="text" 
                                               oninput="this.value=this.value.replace(/[^0-9\-]/g,'')">
                                        <div id="suggestions" class="suggestions-dropdown" style="display:none;"></div>
                                    </div>
                                </div>
                                <div class="col-md-8 mb-3">
                                    <label for="full_name_display" class="form-label">Full Name</label>
                                    <div class="position-relative">
                                        <input type="text" id="full_name_display" class="form-control" 
                                               placeholder="Type your full name" autocomplete="off">
                                        <div id="name-suggestions" class="suggestions-dropdown" style="display:none;"></div>
                                    </div>
                                    <!-- Hidden field for form submission -->
                                    <input type="hidden" id="full_name" name="full_name" value="">
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <div class="section-title">
                                <i class="bi bi-mortarboard-fill"></i>
                                Academic Information
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="course" class="form-label">Course *</label>
                                    <input type="text" id="course" name="course" class="form-control" 
                                           readonly style="background-color: #f8f9fa;"
                                           placeholder="Auto-filled from student data" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="year_level" class="form-label">Year Level *</label>
                                    <input type="text" id="year_level" name="year_level" class="form-control" 
                                           readonly style="background-color: #f8f9fa;"
                                           placeholder="Auto-filled from student data" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="department" class="form-label">Department *</label>
                                    <input type="text" id="department" name="department" class="form-control"
                                           readonly style="background-color: #f8f9fa;"
                                           placeholder="Auto-filled from student data" required>
                                </div>
                            </div>
                        </div>                        <div class="form-section">
                            <div class="section-title">
                                <i class="bi bi-file-text-fill"></i>
                                Document Request
                            </div>

                            <div id="documents-container">
                                <div class="document-item mb-3 p-3 border rounded">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Document Type *</label>
                                            <select class="form-select document-select" name="documents[0][document_id]" required>
                                                <option value="" disabled selected>-- Select Document Type --</option>
                                                <?php $__currentLoopData = $documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($document->id); ?>" data-price="<?php echo e($document->price); ?>" data-name="<?php echo e($document->type_document); ?>">
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
                                        <div class="col-12">
                                            <label class="form-label">Reason for this document *</label>
                                            <select class="form-select reason-select" name="documents[0][reason_select]" required disabled>
                                                <option value="" disabled selected>-- Please select a document first --</option>
                                            </select>
                                            <small class="text-muted">Select a document type to load available reasons</small>
                                        </div>
                                        <div class="col-12 other-reason-container" style="display:none;">
                                            <label class="form-label">Please specify your reason</label>
                                            <textarea class="form-control other-reason" name="documents[0][other_reason]" rows="3"
                                                      placeholder="Type your reason here..." disabled></textarea>
                                        </div>
                                        <input type="hidden" class="reason-hidden" name="documents[0][reason]" value="">
                                    </div>
                                </div>
                            </div>

                            <button type="button" class="btn btn-outline-primary mb-3" id="add-document">
                                <i class="bi bi-plus-circle me-2"></i>Add Another Document
                            </button>

                            <div class="mb-3">
                                <div class="alert alert-info">
                                    <strong>Document Summary:</strong>
                                    <div id="cost-breakdown" class="mt-2">
                                        <small class="text-muted">No documents selected</small>
                                    </div>
                                    <hr class="my-2">
                                    <strong>Total Cost: <span id="total-cost">₱0.00</span></strong>
                                </div>
                            </div>
                        </div>

                        <!-- Privacy Notice and Terms Agreement -->
                        <div class="form-section">
                            <div class="alert alert-info border-0" style="background: rgba(0, 51, 153, 0.05); border-left: 4px solid var(--nu-blue);">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="privacy_agreement" name="privacy_agreement" value="1" required style="border-color: var(--nu-blue);">
                                    <label class="form-check-label" for="privacy_agreement" style="font-size: 0.9rem; color: var(--nu-gray); line-height: 1.5;">
                                        <strong>Privacy Notice and Terms of Agreement *</strong><br>
                                        I hereby acknowledge and agree to the following:<br>
                                        • I understand that the personal information I provide will be used solely for processing my document request.<br>
                                        • My data will be handled in accordance with the National University Data Privacy Policy and Republic Act No. 10173 (Data Privacy Act of 2012).<br>
                                        • I consent to the collection, processing, and storage of my personal information for document processing purposes.<br>
                                        • I have read and understood the <a href="#" data-bs-toggle="modal" data-bs-target="#privacyModal" style="color: var(--nu-blue); text-decoration: underline;">Privacy Policy</a> and <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal" style="color: var(--nu-blue); text-decoration: underline;">Terms of Service</a>.
                                    </label>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn-submit">
                            <i class="bi bi-send-fill me-2"></i>
                            Submit Document Request
                        </button>
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

    <!-- Privacy Policy Modal -->
    <div class="modal fade" id="privacyModal" tabindex="-1" aria-labelledby="privacyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background: var(--nu-blue); color: white;">
                    <h5 class="modal-title" id="privacyModalLabel">
                        <i class="bi bi-shield-lock-fill me-2"></i>Privacy Policy
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h6 class="fw-bold text-primary">National University Data Privacy Policy</h6>
                    <p class="text-muted small">Effective Date: January 1, 2024</p>
                    
                    <h6 class="fw-bold mt-4">1. Introduction</h6>
                    <p>National University ("we," "us," or "our") is committed to protecting your privacy and ensuring the security of your personal information. This Privacy Policy explains how we collect, use, disclose, and safeguard your data in compliance with Republic Act No. 10173 (Data Privacy Act of 2012) and its implementing rules and regulations.</p>
                    
                    <h6 class="fw-bold mt-4">2. Information We Collect</h6>
                    <p>We collect the following types of personal information:</p>
                    <ul>
                        <li><strong>Student Identification:</strong> Student ID, full name, course, year level, department</li>
                        <li><strong>Document Requests:</strong> Type of documents requested, quantity, reason for request</li>
                        <li><strong>Transaction Data:</strong> Queue number, reference number, request date and time</li>
                        <li><strong>Technical Information:</strong> IP address, device information, browser type (for security purposes)</li>
                    </ul>
                    
                    <h6 class="fw-bold mt-4">3. How We Use Your Information</h6>
                    <p>Your personal information is used for the following purposes:</p>
                    <ul>
                        <li>Processing and fulfilling your document requests</li>
                        <li>Maintaining accurate academic and transaction records</li>
                        <li>Sending notifications about your request status via push notifications</li>
                        <li>Improving our services and queue management system</li>
                        <li>Complying with legal and regulatory requirements</li>
                    </ul>
                    
                    <h6 class="fw-bold mt-4">4. Data Sharing and Disclosure</h6>
                    <p>We do not sell, rent, or trade your personal information. We may share your data only in the following circumstances:</p>
                    <ul>
                        <li>With authorized university personnel for document processing</li>
                        <li>When required by law or legal process</li>
                        <li>With third-party service providers who assist us in operating our systems (under strict confidentiality agreements)</li>
                    </ul>
                    
                    <h6 class="fw-bold mt-4">5. Data Security</h6>
                    <p>We implement appropriate technical and organizational security measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction. These measures include:</p>
                    <ul>
                        <li>Secure encrypted connections (HTTPS/SSL)</li>
                        <li>Access controls and authentication systems</li>
                        <li>Regular security audits and monitoring</li>
                        <li>Staff training on data protection</li>
                    </ul>
                    
                    <h6 class="fw-bold mt-4">6. Data Retention</h6>
                    <p>We retain your personal information for as long as necessary to fulfill the purposes outlined in this policy, comply with legal obligations, and resolve disputes. Student records are maintained in accordance with NU record retention policies and applicable laws.</p>
                    
                    <h6 class="fw-bold mt-4">7. Your Rights</h6>
                    <p>Under the Data Privacy Act, you have the right to:</p>
                    <ul>
                        <li><strong>Access:</strong> Request access to your personal information</li>
                        <li><strong>Correction:</strong> Request correction of inaccurate or incomplete data</li>
                        <li><strong>Erasure:</strong> Request deletion of your data (subject to legal requirements)</li>
                        <li><strong>Object:</strong> Object to the processing of your data</li>
                        <li><strong>Data Portability:</strong> Request a copy of your data in a structured format</li>
                        <li><strong>Withdraw Consent:</strong> Withdraw your consent at any time (where processing is based on consent)</li>
                    </ul>
                    
                    <h6 class="fw-bold mt-4">8. Contact Information</h6>
                    <p>For questions, concerns, or requests regarding this Privacy Policy or your personal information, please contact:</p>
                    <p class="ms-3">
                        <strong>Data Protection Officer</strong><br>
                        National University - Lipa<br>
                        NU Bldg, SM City Lipa, JP Laurel Highway, Lipa City, Batangas<br>
                        Email: dpo@nu-lipa.edu.ph<br>
                        Phone: (043) 756-5555
                    </p>
                    
                    <h6 class="fw-bold mt-4">9. Updates to This Policy</h6>
                    <p>We may update this Privacy Policy from time to time. We will notify you of any material changes by posting the updated policy on our website and updating the "Effective Date" above.</p>
                    
                    <p class="mt-4"><small class="text-muted">Last updated: January 1, 2024</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Terms of Service Modal -->
    <div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background: var(--nu-blue); color: white;">
                    <h5 class="modal-title" id="termsModalLabel">
                        <i class="bi bi-file-text-fill me-2"></i>Terms of Service
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h6 class="fw-bold text-primary">NU Lipa Document Request Service - Terms of Service</h6>
                    <p class="text-muted small">Effective Date: January 1, 2024</p>
                    
                    <h6 class="fw-bold mt-4">1. Acceptance of Terms</h6>
                    <p>By accessing and using the National University Lipa Document Request Service ("the Service"), you acknowledge that you have read, understood, and agree to be bound by these Terms of Service. If you do not agree to these terms, please do not use the Service.</p>
                    
                    <h6 class="fw-bold mt-4">2. Service Description</h6>
                    <p>The Service provides a digital platform for students and alumni to:</p>
                    <ul>
                        <li>Submit on-site document requests</li>
                        <li>Track the status of document requests</li>
                        <li>Receive notifications about request progress</li>
                        <li>Manage queue positions for document pickup</li>
                    </ul>
                    
                    <h6 class="fw-bold mt-4">3. Eligibility</h6>
                    <p>To use the Service, you must:</p>
                    <ul>
                        <li>Be a current or former student of National University - Lipa</li>
                        <li>Provide accurate and complete information</li>
                        <li>Have a valid student ID or reference number</li>
                        <li>Agree to these Terms of Service and our Privacy Policy</li>
                    </ul>
                    
                    <h6 class="fw-bold mt-4">4. User Responsibilities</h6>
                    <p>When using the Service, you agree to:</p>
                    <ul>
                        <li>Provide accurate, current, and complete information about yourself</li>
                        <li>Maintain the accuracy of your information</li>
                        <li>Use the Service only for lawful purposes</li>
                        <li>Not impersonate any person or entity</li>
                        <li>Not interfere with or disrupt the Service or servers</li>
                        <li>Comply with all applicable local, state, national, and international laws</li>
                    </ul>
                    
                    <h6 class="fw-bold mt-4">5. Document Request Process</h6>
                    <p><strong>5.1 Request Submission:</strong> Document requests must be submitted with complete and accurate information. Incomplete or inaccurate requests may be rejected or delayed.</p>
                    
                    <p><strong>5.2 Processing Time:</strong> Processing times vary depending on the type of document requested. Estimated processing times are provided for reference only and are not guaranteed.</p>
                    
                    <p><strong>5.3 Fees and Payment:</strong> Applicable fees must be paid before document processing. Fee information is displayed at the time of request submission. All payments are non-refundable except in cases of service error.</p>
                    
                    <p><strong>5.4 Document Pickup:</strong> Documents must be picked up within the specified timeframe. Unclaimed documents may be subject to additional storage fees or disposal after a reasonable period.</p>
                    
                    <h6 class="fw-bold mt-4">6. Queue Management</h6>
                    <p>Queue positions are assigned automatically and are subject to change based on:</p>
                    <ul>
                        <li>Document processing status</li>
                        <li>Registrar availability</li>
                        <li>Priority cases (as determined by university policy)</li>
                    </ul>
                    <p>The university reserves the right to adjust queue positions as necessary to ensure efficient service delivery.</p>
                    
                    <h6 class="fw-bold mt-4">7. Notifications</h6>
                    <p>By using the Service, you consent to receive notifications via:</p>
                    <ul>
                        <li>Push notifications through the mobile application</li>
                        <li>Email to your registered email address</li>
                        <li>SMS to your registered mobile number (if applicable)</li>
                    </ul>
                    <p>You may opt out of non-essential notifications through your account settings.</p>
                    
                    <h6 class="fw-bold mt-4">8. Intellectual Property</h6>
                    <p>All content, features, and functionality of the Service, including but not limited to text, graphics, logos, icons, images, and software, are the exclusive property of National University and are protected by copyright, trademark, and other intellectual property laws.</p>
                    
                    <h6 class="fw-bold mt-4">9. Limitation of Liability</h6>
                    <p>To the fullest extent permitted by law, National University shall not be liable for:</p>
                    <ul>
                        <li>Any indirect, incidental, special, consequential, or punitive damages</li>
                        <li>Loss of profits, revenues, data, or use</li>
                        <li>Service interruptions or delays</li>
                        <li>Errors or inaccuracies in content</li>
                        <li>Unauthorized access to or alteration of your transmissions or data</li>
                    </ul>
                    
                    <h6 class="fw-bold mt-4">10. Service Availability</h6>
                    <p>We strive to provide continuous access to the Service. However, we do not guarantee that the Service will be uninterrupted, timely, secure, or error-free. We reserve the right to:</p>
                    <ul>
                        <li>Modify or discontinue the Service at any time</li>
                        <li>Perform scheduled maintenance</li>
                        <li>Suspend access for technical or security reasons</li>
                    </ul>
                    
                    <h6 class="fw-bold mt-4">11. Prohibited Activities</h6>
                    <p>You may not:</p>
                    <ul>
                        <li>Use the Service for fraudulent purposes</li>
                        <li>Submit false or misleading information</li>
                        <li>Attempt to gain unauthorized access to the Service</li>
                        <li>Use automated systems or software to extract data</li>
                        <li>Reverse engineer, decompile, or disassemble any part of the Service</li>
                        <li>Transmit viruses, malware, or other harmful code</li>
                    </ul>
                    
                    <h6 class="fw-bold mt-4">12. Termination</h6>
                    <p>We reserve the right to terminate or suspend your access to the Service immediately, without prior notice, for any reason, including but not limited to:</p>
                    <ul>
                        <li>Violation of these Terms of Service</li>
                        <li>Fraudulent or illegal activity</li>
                        <li>Provision of false information</li>
                        <li>Abuse of the Service or other users</li>
                    </ul>
                    
                    <h6 class="fw-bold mt-4">13. Governing Law</h6>
                    <p>These Terms of Service shall be governed by and construed in accordance with the laws of the Republic of the Philippines. Any disputes arising from these terms shall be subject to the exclusive jurisdiction of the courts of Lipa City, Batangas.</p>
                    
                    <h6 class="fw-bold mt-4">14. Changes to Terms</h6>
                    <p>We reserve the right to modify these Terms of Service at any time. We will notify you of any material changes by posting the updated terms on our website and updating the "Effective Date" above. Your continued use of the Service after such changes constitutes your acceptance of the new terms.</p>
                    
                    <h6 class="fw-bold mt-4">15. Contact Information</h6>
                    <p>For questions or concerns about these Terms of Service, please contact:</p>
                    <p class="ms-3">
                        <strong>Registrar's Office</strong><br>
                        National University - Lipa<br>
                        NU Bldg, SM City Lipa, JP Laurel Highway, Lipa City, Batangas<br>
                        Email: registrar@nu-lipa.edu.ph<br>
                        Phone: (043) 756-5555
                    </p>
                    
                    <h6 class="fw-bold mt-4">16. Severability</h6>
                    <p>If any provision of these Terms of Service is found to be invalid or unenforceable, the remaining provisions shall continue in full force and effect.</p>
                    
                    <h6 class="fw-bold mt-4">17. Entire Agreement</h6>
                    <p>These Terms of Service, together with our Privacy Policy, constitute the entire agreement between you and National University regarding the use of the Service.</p>
                    
                    <p class="mt-4 text-center fw-bold">By using the Service, you acknowledge that you have read, understood, and agree to be bound by these Terms of Service.</p>
                    
                    <p class="mt-4"><small class="text-muted">Last updated: January 1, 2024</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // Student search functionality with fallback
            document.getElementById('student-search').addEventListener('input', function() {
                const query = this.value;
                if (query.length >= 2) {
                    // Try primary endpoint first, then fallback
                    searchStudents(query, '/api/students/search', 'student_id')
                        .catch(() => {
                            console.warn('Primary search failed, trying fallback...');
                            return searchStudents(query, '/api/students/search-simple', 'student_id');
                        })
                        .catch(error => {
                            console.error('Both search endpoints failed:', error);
                            document.getElementById('suggestions').style.display = 'none';
                            document.getElementById('full_name_display').value = '';
                        });
                } else {
                    document.getElementById('suggestions').style.display = 'none';
                    document.getElementById('full_name_display').value = '';
                }
            });

            // Validate academic fields when student ID input loses focus
            document.getElementById('student-search').addEventListener('blur', function() {
                setTimeout(() => {
                    // Only validate if suggestions are not visible (user finished input)
                    const suggestions = document.getElementById('suggestions');
                    if (suggestions.style.display === 'none' || suggestions.style.display === '') {
                        const courseField = document.getElementById('course');
                        const yearLevelField = document.getElementById('year_level');
                        const departmentField = document.getElementById('department');
                        
                        // Validate academic fields only if they are still empty
                        if (courseField.value.trim() === '') {
                            validateField(courseField);
                        }
                        if (yearLevelField.value.trim() === '') {
                            validateField(yearLevelField);
                        }
                        if (departmentField.value.trim() === '') {
                            validateField(departmentField);
                        }
                    }
                }, 200); // Small delay to allow for suggestion clicks
            });

            // Full name search functionality
            document.getElementById('full_name_display').addEventListener('input', function() {
                const query = this.value;
                if (query.length >= 2) {
                    // Try primary endpoint first, then fallback
                    searchStudents(query, '/api/students/search', 'full_name')
                        .catch(() => {
                            console.warn('Primary name search failed, trying fallback...');
                            return searchStudents(query, '/api/students/search-simple', 'full_name');
                        })
                        .catch(error => {
                            console.error('Both name search endpoints failed:', error);
                            document.getElementById('name-suggestions').style.display = 'none';
                            document.getElementById('student-search').value = '';
                        });
                } else {
                    document.getElementById('name-suggestions').style.display = 'none';
                    document.getElementById('student-search').value = '';
                }
            });

            // Validate academic fields when full name input loses focus
            document.getElementById('full_name_display').addEventListener('blur', function() {
                setTimeout(() => {
                    // Only validate if suggestions are not visible (user finished input)
                    const suggestions = document.getElementById('name-suggestions');
                    if (suggestions.style.display === 'none' || suggestions.style.display === '') {
                        const courseField = document.getElementById('course');
                        const yearLevelField = document.getElementById('year_level');
                        const departmentField = document.getElementById('department');
                        
                        // Validate academic fields only if they are still empty
                        if (courseField.value.trim() === '') {
                            validateField(courseField);
                        }
                        if (yearLevelField.value.trim() === '') {
                            validateField(yearLevelField);
                        }
                        if (departmentField.value.trim() === '') {
                            validateField(departmentField);
                        }
                    }
                }, 200); // Small delay to allow for suggestion clicks
            });

            // Reusable search function with proper error handling
            function searchStudents(query, endpoint, searchBy) {
                const params = new URLSearchParams();
                params.append('query', encodeURIComponent(query));
                params.append('search_by', searchBy);
                
                return fetch(endpoint + '?' + params.toString())
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        const contentType = response.headers.get('content-type');
                        if (!contentType || !contentType.includes('application/json')) {
                            throw new Error('Response is not JSON');
                        }
                        return response.json();
                    })
                    .then(data => {
                        const suggestionsContainer = searchBy === 'student_id' ? 
                            document.getElementById('suggestions') : 
                            document.getElementById('name-suggestions');
                        const otherField = searchBy === 'student_id' ? 
                            document.getElementById('full_name_display') : 
                            document.getElementById('student-search');
                        
                        suggestionsContainer.innerHTML = '';
                        
                        if (!data || !Array.isArray(data) || data.length === 0) {
                            suggestionsContainer.style.display = 'none';
                            otherField.value = '';
                            
                            // Clear and mark academic fields as invalid when no student found
                            document.getElementById('course').value = '';
                            document.getElementById('year_level').value = '';
                            document.getElementById('department').value = '';
                            validateField(document.getElementById('course'));
                            validateField(document.getElementById('year_level'));
                            validateField(document.getElementById('department'));
                            
                            return;
                        }
                        
                        data.forEach(student => {
                            if (student && student.student_id && student.full_name) {
                                const div = document.createElement('div');
                                div.className = 'suggestion-item';
                                div.setAttribute('data-student-id', student.student_id || '');
                                div.setAttribute('data-full-name', student.full_name || '');
                                div.setAttribute('data-course', student.course || '');
                                div.setAttribute('data-year-level', student.year_level || '');
                                div.setAttribute('data-department', student.department || '');
                                div.innerHTML = `
                                    <div class="suggestion-id"><strong>${student.student_id}</strong></div>
                                    <div class="suggestion-name">${student.full_name}</div>
                                    <div class="suggestion-course text-muted">${student.course} - ${student.year_level}</div>
                                `;
                                suggestionsContainer.appendChild(div);
                            }
                        });
                        suggestionsContainer.style.display = 'block';
                        return data;
                    });
            }

            // Handle student suggestion click
            document.addEventListener('click', function(e) {
                if (e.target.closest('.suggestion-item')) {
                    const suggestionItem = e.target.closest('.suggestion-item');
                    const studentId = suggestionItem.getAttribute('data-student-id');
                    const fullName = suggestionItem.getAttribute('data-full-name');
                    const course = suggestionItem.getAttribute('data-course');
                    const yearLevel = suggestionItem.getAttribute('data-year-level');
                    const department = suggestionItem.getAttribute('data-department');

                    // Fill student information
                    document.getElementById('student-search').value = studentId;
                    document.getElementById('full_name_display').value = fullName;
                    document.getElementById('full_name').value = fullName;

                    // Auto-fill academic information (now as text inputs)
                    document.getElementById('course').value = course || '';
                    document.getElementById('year_level').value = yearLevel || '';
                    document.getElementById('department').value = department || '';

                    // Validate the auto-filled fields
                    validateField(document.getElementById('course'));
                    validateField(document.getElementById('year_level'));
                    validateField(document.getElementById('department'));

                    // Hide both suggestion containers
                    document.getElementById('suggestions').style.display = 'none';
                    document.getElementById('name-suggestions').style.display = 'none';
                }
            });

            // Hide suggestions when clicking outside
            document.addEventListener('click', function(event) {
                if (!event.target.closest('#student-search') && !event.target.closest('#suggestions') &&
                    !event.target.closest('#full_name_display') && !event.target.closest('#name-suggestions')) {
                    document.getElementById('suggestions').style.display = 'none';
                    document.getElementById('name-suggestions').style.display = 'none';
                }
            });

            // Clear auto-filled data when student ID is manually changed
            document.getElementById('student-search').addEventListener('keydown', function() {
                setTimeout(() => {
                    const currentVal = this.value;
                    if (currentVal.length < 2) {
                        document.getElementById('full_name_display').value = '';
                        document.getElementById('full_name').value = '';
                        document.getElementById('course').value = '';
                        document.getElementById('year_level').value = '';
                        document.getElementById('department').value = '';
                        
                        // Remove validation states when clearing
                        document.getElementById('course').classList.remove('is-invalid', 'is-valid');
                        document.getElementById('year_level').classList.remove('is-invalid', 'is-valid');
                        document.getElementById('department').classList.remove('is-invalid', 'is-valid');
                    }
                }, 10);
            });

            // Clear auto-filled data when full name is manually changed
            document.getElementById('full_name_display').addEventListener('keydown', function() {
                setTimeout(() => {
                    const currentVal = this.value;
                    if (currentVal.length < 2) {
                        document.getElementById('student-search').value = '';
                        document.getElementById('full_name').value = '';
                        document.getElementById('course').value = '';
                        document.getElementById('year_level').value = '';
                        document.getElementById('department').value = '';
                        
                        // Remove validation states when clearing
                        document.getElementById('course').classList.remove('is-invalid', 'is-valid');
                        document.getElementById('year_level').classList.remove('is-invalid', 'is-valid');
                        document.getElementById('department').classList.remove('is-invalid', 'is-valid');
                    }
                }, 10);
            });

            // Document-specific reasons mapping
            const documentReasons = {
                'Transcript of Records with Documentary Stamp': [
                    'Required for transfer to another school or university',
                    'Needed for employment application or job requirement',
                    'For evaluation or credential verification abroad',
                    'Requirement for licensure examination or board exam application',
                    'Other'
                ],
                'Certificates (Any) with Documentary Stamp': [
                    'For authentication or submission to CHED, DFA, or POEA',
                    'Requirement for overseas employment or study',
                    'Needed for official endorsement or verification purposes',
                    'Other'
                ],
                'Certificates (Any) without Documentary Stamp': [
                    'For personal record or local employment application',
                    'Needed for scholarship or internship requirements',
                    'Requirement for organization or club membership verification',
                    'Other'
                ],
                'Form 137': [
                    'Required for transfer to another academic institution',
                    'Needed for student record completion or enrollment verification',
                    'For evaluation of previous academic performance',
                    'Other'
                ],
                'CTC of Grades Per Term (Certified True Copy)': [
                    'Needed for scholarship renewal or application',
                    'Requirement for internship or OJT documentation',
                    'For employment or promotion qualification verification',
                    'Other'
                ],
                'CTC of Diploma (Per Set)': [
                    'Requirement for employment application or promotion',
                    'Needed for foreign credential evaluation or visa processing',
                    'For personal record or framing purposes',
                    'Other'
                ],
                'CTC of TOR (Per Set)': [
                    'Requirement for graduate school admission',
                    'For professional licensing or board examination application',
                    'Needed for job application, especially abroad',
                    'Other'
                ],
                'Copy of Diploma with Documentary Stamp': [
                    'Requirement for DFA authentication or Apostille',
                    'Needed for employment abroad or immigration purposes',
                    'For official submission to an employer or institution',
                    'Other'
                ],
                'Honorable Dismissal (HD / Transfer Credentials with Documentary Stamp)': [
                    'Required when transferring to another college or university',
                    'Needed for clearance or exit documentation from previous school',
                    'For authentication or verification by another institution',
                    'Other'
                ],
                'Reprinting of COR – Stamp Enrolled / CTC / Copy of Grades': [
                    'Lost or damaged original Certificate of Registration',
                    'Needed as proof of enrollment for scholarship or internship',
                    'Requirement for employment or verification of student status',
                    'Other'
                ],
                'Certificate of Good Moral': [
                    'Requirement for transfer, scholarship, or graduation',
                    'Needed for employment or internship application',
                    'Requirement for licensure exam or government application',
                    'Other'
                ],
                'Course Descriptions': [
                    'Needed for subject evaluation or credit transfer to another institution',
                    'Requirement for graduate school or foreign credential assessment',
                    'For employment verification or curriculum equivalency evaluation',
                    'Other'
                ],
                'Documentary Stamp': [
                    'Required for official authentication or notarization of documents',
                    'Needed for submission to government agencies or embassies',
                    'To comply with official certification or verification requirements',
                    'Other'
                ]
            };

            // Function to normalize document name for matching (removes spaces, dashes, etc.)
            function normalizeDocumentName(name) {
                return name.toLowerCase()
                    .replace(/\s+/g, '')
                    .replace(/-/g, '')
                    .replace(/–/g, '')
                    .replace(/\//g, '')
                    .replace(/\(/g, '')
                    .replace(/\)/g, '')
                    .replace(/\./g, '')
                    .replace(/₱/g, '')
                    .replace(/[0-9]/g, '');
            }

            // Function to find matching document reasons with fuzzy matching
            function findDocumentReasons(documentName) {
                // First try exact match
                if (documentReasons[documentName]) {
                    return documentReasons[documentName];
                }

                // Try fuzzy match
                const normalizedSearch = normalizeDocumentName(documentName);
                
                for (const [key, reasons] of Object.entries(documentReasons)) {
                    if (normalizeDocumentName(key) === normalizedSearch) {
                        return reasons;
                    }
                }

                // If still no match, check if document name contains key words
                const lowerDocName = documentName.toLowerCase();
                
                if (lowerDocName.includes('transcript') && lowerDocName.includes('records')) {
                    return documentReasons['Transcript of Records with Documentary Stamp'];
                }
                if (lowerDocName.includes('certificate') && lowerDocName.includes('with') && lowerDocName.includes('stamp')) {
                    return documentReasons['Certificates (Any) with Documentary Stamp'];
                }
                if (lowerDocName.includes('certificate') && lowerDocName.includes('without')) {
                    return documentReasons['Certificates (Any) without Documentary Stamp'];
                }
                if (lowerDocName.includes('form') && lowerDocName.includes('137')) {
                    return documentReasons['Form 137'];
                }
                if (lowerDocName.includes('ctc') && lowerDocName.includes('grades')) {
                    return documentReasons['CTC of Grades Per Term (Certified True Copy)'];
                }
                if (lowerDocName.includes('ctc') && lowerDocName.includes('diploma')) {
                    return documentReasons['CTC of Diploma (Per Set)'];
                }
                if (lowerDocName.includes('ctc') && lowerDocName.includes('tor')) {
                    return documentReasons['CTC of TOR (Per Set)'];
                }
                if (lowerDocName.includes('copy') && lowerDocName.includes('diploma')) {
                    return documentReasons['Copy of Diploma with Documentary Stamp'];
                }
                if (lowerDocName.includes('honorable') && lowerDocName.includes('dismissal')) {
                    return documentReasons['Honorable Dismissal (HD / Transfer Credentials with Documentary Stamp)'];
                }
                if (lowerDocName.includes('reprinting') && lowerDocName.includes('cor')) {
                    return documentReasons['Reprinting of COR – Stamp Enrolled / CTC / Copy of Grades'];
                }
                if (lowerDocName.includes('good') && lowerDocName.includes('moral')) {
                    return documentReasons['Certificate of Good Moral'];
                }
                if (lowerDocName.includes('course') && lowerDocName.includes('description')) {
                    return documentReasons['Course Descriptions'];
                }
                if (lowerDocName.includes('documentary') && lowerDocName.includes('stamp') && !lowerDocName.includes('transcript')) {
                    return documentReasons['Documentary Stamp'];
                }

                return null;
            }

            // Function to update reason dropdown based on selected document
            function updateReasonOptionsForItem(item, documentName) {
                const reasonSelect = item.querySelector('.reason-select');
                const otherReasonContainer = item.querySelector('.other-reason-container');
                const otherReasonTextarea = item.querySelector('.other-reason');
                const reasonHidden = item.querySelector('.reason-hidden');
                const reasons = findDocumentReasons(documentName);
                
                // Clear existing options
                reasonSelect.innerHTML = '';
                
                if (reasons && reasons.length > 0) {
                    // Enable the select and add new options
                    reasonSelect.disabled = false;
                    reasonSelect.innerHTML = '<option value="" disabled selected>-- Select Reason --</option>';
                    
                    reasons.forEach(reason => {
                        const option = document.createElement('option');
                        option.value = reason;
                        option.textContent = reason;
                        reasonSelect.appendChild(option);
                    });
                } else {
                    // No specific reasons, disable the select
                    reasonSelect.disabled = true;
                    reasonSelect.innerHTML = '<option value="" disabled selected>-- Please select a document first --</option>';
                }
                
                reasonSelect.required = !!documentName;
                reasonHidden.value = '';

                otherReasonContainer.style.display = 'none';
                otherReasonTextarea.disabled = true;
                otherReasonTextarea.required = false;
                otherReasonTextarea.value = '';
            }

            function syncReasonForItem(item) {
                const reasonSelect = item.querySelector('.reason-select');
                const otherReasonContainer = item.querySelector('.other-reason-container');
                const otherReasonTextarea = item.querySelector('.other-reason');
                const reasonHidden = item.querySelector('.reason-hidden');

                if (reasonSelect.value === 'Other') {
                    otherReasonContainer.style.display = 'block';
                    otherReasonTextarea.disabled = false;
                    otherReasonTextarea.required = true;
                    reasonHidden.value = otherReasonTextarea.value.trim();
                } else {
                    otherReasonContainer.style.display = 'none';
                    otherReasonTextarea.disabled = true;
                    otherReasonTextarea.required = false;
                    otherReasonTextarea.value = '';
                    reasonHidden.value = reasonSelect.value || '';
                }
            }

            // Document management functionality
            let documentIndex = 1;

            function updateTotalCost() {
                let total = 0;
                let breakdownHtml = '';

                document.querySelectorAll('.document-item').forEach((item, index) => {
                    const select = item.querySelector('.document-select');
                    const quantity = item.querySelector('.quantity-input');
                    const selectedOption = select.options[select.selectedIndex];
                    const documentName = selectedOption ? selectedOption.text.split(' (₱')[0] : 'Unknown Document';
                    const price = selectedOption ? parseFloat(selectedOption.getAttribute('data-price') || 0) : 0;
                    const qty = parseInt(quantity.value) || 0;
                    const itemTotal = price * qty;

                    total += itemTotal;

                    if (qty > 0 && selectedOption && selectedOption.value) {
                        breakdownHtml += `<div class="d-flex justify-content-between">
                            <small>${documentName} (x${qty})</small>
                            <small>₱${itemTotal.toFixed(2)}</small>
                        </div>`;
                    }
                });

                document.getElementById('total-cost').textContent = '₱' + total.toFixed(2);

                const breakdownElement = document.getElementById('cost-breakdown');
                if (breakdownHtml) {
                    breakdownElement.innerHTML = breakdownHtml;
                } else {
                    breakdownElement.innerHTML = '<small class="text-muted">No documents selected</small>';
                }
            }

            function updateRemoveButtons() {
                const items = document.querySelectorAll('.document-item');
                items.forEach((item, index) => {
                    const removeBtn = item.querySelector('.remove-document');
                    if (items.length > 1) {
                        removeBtn.style.display = 'block';
                    } else {
                        removeBtn.style.display = 'none';
                    }
                });
            }

            document.getElementById('add-document').addEventListener('click', function() {
                const container = document.getElementById('documents-container');
                const newItem = document.createElement('div');
                newItem.className = 'document-item mb-3 p-3 border rounded';
                newItem.innerHTML = `
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Document Type *</label>
                            <select class="form-select document-select" name="documents[${documentIndex}][document_id]" required>
                                <option value="" disabled selected>-- Select Document Type --</option>
                                <?php $__currentLoopData = $documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($document->id); ?>" data-price="<?php echo e($document->price); ?>" data-name="<?php echo e($document->type_document); ?>">
                                        <?php echo e($document->type_document); ?> <?php if($document->price > 0): ?> (₱<?php echo e($document->price); ?>) <?php endif; ?>
                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Quantity *</label>
                            <input type="number" class="form-control quantity-input" name="documents[${documentIndex}][quantity]"
                                   value="1" min="1" max="150" required>
                        </div>
                        <div class="col-md-2 mb-3 d-flex align-items-end">
                            <button type="button" class="btn btn-danger remove-document">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Reason for this document *</label>
                            <select class="form-select reason-select" name="documents[${documentIndex}][reason_select]" required disabled>
                                <option value="" disabled selected>-- Please select a document first --</option>
                            </select>
                            <small class="text-muted">Select a document type to load available reasons</small>
                        </div>
                        <div class="col-12 other-reason-container" style="display:none;">
                            <label class="form-label">Please specify your reason</label>
                            <textarea class="form-control other-reason" name="documents[${documentIndex}][other_reason]" rows="3"
                                      placeholder="Type your reason here..." disabled></textarea>
                        </div>
                        <input type="hidden" class="reason-hidden" name="documents[${documentIndex}][reason]" value="">
                    </div>
                `;
                container.appendChild(newItem);
                documentIndex++;
                updateRemoveButtons();
                attachDocumentEvents(newItem);
            });

            function reindexDocumentItems() {
                const items = document.querySelectorAll('.document-item');
                items.forEach((item, index) => {
                    item.querySelector('.document-select').name = `documents[${index}][document_id]`;
                    item.querySelector('.quantity-input').name = `documents[${index}][quantity]`;
                    item.querySelector('.reason-select').name = `documents[${index}][reason_select]`;
                    item.querySelector('.other-reason').name = `documents[${index}][other_reason]`;
                    item.querySelector('.reason-hidden').name = `documents[${index}][reason]`;
                });
                documentIndex = items.length;
            }

            function attachDocumentEvents(item) {
                const select = item.querySelector('.document-select');
                const quantity = item.querySelector('.quantity-input');
                const removeBtn = item.querySelector('.remove-document');
                const reasonSelect = item.querySelector('.reason-select');
                const otherReasonTextarea = item.querySelector('.other-reason');

                select.addEventListener('change', function() {
                    updateTotalCost();

                    const selectedOption = this.options[this.selectedIndex];
                    if (selectedOption && selectedOption.value) {
                        updateReasonOptionsForItem(item, selectedOption.dataset.name || selectedOption.text.split(' (₱')[0].trim());
                    } else {
                        updateReasonOptionsForItem(item, '');
                    }
                });
                
                quantity.addEventListener('input', updateTotalCost);

                reasonSelect.addEventListener('change', function() {
                    syncReasonForItem(item);
                });

                otherReasonTextarea.addEventListener('input', function() {
                    syncReasonForItem(item);
                });

                removeBtn.addEventListener('click', function() {
                    item.remove();
                    updateTotalCost();
                    updateRemoveButtons();
                    reindexDocumentItems();
                });
            }

            // Attach events to initial document item
            document.querySelectorAll('.document-item').forEach(item => {
                attachDocumentEvents(item);
            });

            // Initial total cost calculation
            updateTotalCost();
            updateRemoveButtons();

            // Form validation and UX before submit
            document.getElementById('onsiteForm').addEventListener('submit', function(e) {
                // Sync and validate reasons per selected document
                const documentItems = document.querySelectorAll('.document-item');
                for (const item of documentItems) {
                    const docSelect = item.querySelector('.document-select');
                    if (!docSelect.value) {
                        continue;
                    }

                    const reasonSelect = item.querySelector('.reason-select');
                    const otherReasonTextarea = item.querySelector('.other-reason');
                    const reasonHidden = item.querySelector('.reason-hidden');

                    if (!reasonSelect.value) {
                        e.preventDefault();
                        alert('Please select a reason for each document.');
                        reasonSelect.focus();
                        return false;
                    }

                    if (reasonSelect.value === 'Other') {
                        if (!otherReasonTextarea.value.trim()) {
                            e.preventDefault();
                            alert('Please specify your reason when selecting Other.');
                            otherReasonTextarea.focus();
                            return false;
                        }
                        reasonHidden.value = otherReasonTextarea.value.trim();
                    } else {
                        reasonHidden.value = reasonSelect.value;
                    }
                }

                const studentId = document.getElementById('student-search').value.trim();
                const fullNameDisplay = document.getElementById('full_name_display').value.trim();

                if (!studentId && !fullNameDisplay) {
                    e.preventDefault();
                    alert('Please enter either a student ID or full name.');
                    document.getElementById('student-search').focus();
                    return false;
                }

                // Auto-generate full name if empty
                let fullName = document.getElementById('full_name').value.trim();
                if (!fullName) {
                    // Use display name if available, or generate placeholder
                    fullName = fullNameDisplay || `Student ${studentId || 'Unknown'}`;
                    document.getElementById('full_name').value = fullName;
                }

                // Validate privacy agreement checkbox
                const privacyCheckbox = document.getElementById('privacy_agreement');
                if (!privacyCheckbox.checked) {
                    e.preventDefault();
                    alert('Please read and agree to the Privacy Notice and Terms of Agreement before submitting your request.');
                    privacyCheckbox.focus();
                    return false;
                }

                // Submit button loading state
                const submitBtn = this.querySelector('.btn-submit');
                submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Processing Request...';
                submitBtn.disabled = true;
            });

            // Enhanced button interactions and accessibility
            function createRippleEffect(e, element) {
                const ripple = document.createElement('span');
                const rect = element.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;
                
                ripple.style.cssText = `
                    position: absolute;
                    border-radius: 50%;
                    transform: scale(0);
                    animation: ripple 600ms linear;
                    background-color: rgba(255, 255, 255, 0.7);
                    width: ${size}px;
                    height: ${size}px;
                    left: ${x}px;
                    top: ${y}px;
                    pointer-events: none;
                    z-index: 1;
                `;
                
                element.style.position = 'relative';
                element.style.overflow = 'hidden';
                element.appendChild(ripple);
                
                setTimeout(() => {
                    ripple.remove();
                }, 600);
            }

            // Enhanced ripple effect for buttons
            document.querySelectorAll('.btn-submit, .login-button').forEach(button => {
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

            // Form validation feedback with enhanced styling
            // Exclude academic fields from initial validation - they get validated after student search
            const inputs = document.querySelectorAll('input[required]:not(#course):not(#year_level):not(#department), select[required]');
            inputs.forEach(input => {
                input.addEventListener('blur', function() {
                    validateField(this);
                });
                
                input.addEventListener('focus', function() {
                    this.classList.remove('is-invalid', 'is-valid');
                });

                input.addEventListener('input', function() {
                    if (this.classList.contains('is-invalid')) {
                        validateField(this);
                    }
                });
            });

            function validateField(field) {
                if (field.value.trim() === '') {
                    field.classList.add('is-invalid');
                    field.classList.remove('is-valid');
                } else {
                    field.classList.remove('is-invalid');
                    field.classList.add('is-valid');
                }
            }

            // Enhanced form field interactions
            document.querySelectorAll('.form-control').forEach(input => {
                // Add floating label effect
                input.addEventListener('focus', function() {
                    this.style.transform = 'scale(1.02)';
                    this.style.transition = 'transform 0.2s ease';
                });
                
                input.addEventListener('blur', function() {
                    this.style.transform = 'scale(1)';
                });
            });

            // Add CSS for enhanced interactions
            const style = document.createElement('style');
            style.textContent = `
                @keyframes ripple {
                    0% { transform: scale(0); opacity: 1; }
                    100% { transform: scale(4); opacity: 0; }
                }
                
                .form-control:focus {
                    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1) !important;
                }
            `;
            document.head.appendChild(style);
        });
    </script>
</body>
</html>
<?php /**PATH C:\Users\Brill\nu-regis\resources\views/onsite/index.blade.php ENDPATH**/ ?>