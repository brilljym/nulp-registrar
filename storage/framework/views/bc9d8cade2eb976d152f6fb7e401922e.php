<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NU Document Request - NU Lipa</title>
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

        /* Centered card */
        .document-request-card {
            background: var(--nu-white);
            border-radius: var(--border-radius-xl);
            box-shadow: var(--shadow-xl);
            padding: 2rem;
            max-width: 380px;
            width: 100%;
            max-width: min(380px, 90vw);
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
        .btn-login {
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

        .btn-login:hover {
            background: var(--primary-blue-hover);
            border-color: var(--primary-blue-hover);
            transform: translateY(-1px);
            box-shadow: var(--shadow-lg);
            color: var(--nu-white);
        }

        .btn-login:focus {
            outline: 2px solid var(--primary-blue);
            outline-offset: 2px;
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .btn-walkin {
            background: var(--neutral-50);
            color: var(--neutral-700);
            border: 2px solid var(--neutral-200);
            border-radius: var(--border-radius-lg);
            padding: 0.875rem 1.5rem;
            font-size: 1rem;
            font-weight: 600;
            width: 100%;
            margin-bottom: 0.75rem;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .btn-walkin:hover {
            background: var(--neutral-100);
            color: var(--neutral-800);
            border-color: var(--neutral-300);
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
            text-decoration: none;
        }

        .btn-walkin:focus {
            outline: 2px solid var(--neutral-400);
            outline-offset: 2px;
        }

        .no-login-text {
            color: var(--neutral-400);
            font-size: 0.825rem;
            margin-bottom: 0;
            font-weight: 400;
        }

        .divider {
            margin: 1.25rem 0;
            position: relative;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: var(--neutral-200);
        }

        .divider-text {
            background: var(--nu-white);
            color: var(--neutral-400);
            padding: 0 1rem;
            font-size: 0.875rem;
            position: relative;
            font-weight: 500;
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
                overflow: hidden;
                display: flex;
                align-items: center;
                justify-content: center;
                position: relative;
                width: 100%;
            }

            .document-request-card {
                padding: 1.75rem 1.25rem;
                margin: 0 auto;
                max-width: min(340px, 90vw);
                width: 100%;
                position: relative;
            }

            .card-title {
                font-size: 1.5rem;
                margin-bottom: 0.375rem;
            }

            .card-subtitle {
                font-size: 0.825rem;
                margin-bottom: 1.25rem;
            }

            .btn-login,
            .btn-walkin {
                padding: 0.75rem 1rem;
                font-size: 0.9rem;
            }

            .nu-footer {
                padding: 0.5rem 1rem;
                font-size: 0.7rem;
                height: auto;
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
                display: flex;
                align-items: center;
                justify-content: center;
                position: relative;
                width: 100%;
            }

            .document-request-card {
                margin: 0 auto;
                padding: 1.5rem 1rem;
                max-width: min(300px, 90vw);
                width: 100%;
                position: relative;
            }

            .card-title {
                font-size: 1.375rem;
            }

            .card-subtitle {
                font-size: 0.775rem;
                margin-bottom: 1rem;
            }

            .btn-walkin,
            .btn-login {
                padding: 0.65rem 0.875rem;
                font-size: 0.875rem;
            }

            .divider {
                margin: 1.25rem 0;
            }

            .form-control {
                padding: 0.75rem 0.875rem;
                font-size: 0.9rem;
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
                display: flex;
                align-items: center;
                justify-content: center;
                position: relative;
                width: 100%;
            }

            .document-request-card {
                margin: 0 auto;
                padding: 1.25rem 0.875rem;
                max-width: min(280px, 90vw);
                position: relative;
            }

            .card-title {
                font-size: 1.25rem;
            }

            .card-subtitle {
                font-size: 0.75rem;
            }

            .btn-walkin,
            .btn-login {
                padding: 0.625rem 0.75rem;
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

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .document-request-card {
            animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .modal.fade .modal-dialog {
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Focus and accessibility improvements */
        .btn-login:focus-visible,
        .btn-walkin:focus-visible {
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

        /* Floating Help Button */
        .floating-help-btn {
            position: fixed;
            bottom: 5rem;
            right: 1.75rem;
            z-index: 100;
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-blue), var(--accent-color));
            color: #fff;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            box-shadow: 0 4px 16px rgba(37,99,235,0.35);
            transition: transform 0.25s cubic-bezier(0.4,0,0.2,1), box-shadow 0.25s;
            animation: helpPulse 2.5s ease-in-out infinite;
        }
        .floating-help-btn:hover {
            transform: scale(1.12) translateY(-2px);
            box-shadow: 0 8px 24px rgba(37,99,235,0.45);
        }
        .floating-help-btn:active { transform: scale(0.95); }
        .floating-help-tooltip {
            position: fixed;
            bottom: 9.5rem;
            right: 1.75rem;
            background: var(--neutral-800);
            color: #fff;
            font-size: 0.75rem;
            font-weight: 500;
            padding: 0.35rem 0.75rem;
            border-radius: 6px;
            white-space: nowrap;
            pointer-events: none;
            opacity: 0;
            transform: translateY(6px);
            transition: opacity 0.2s, transform 0.2s;
            z-index: 101;
        }
        .floating-help-tooltip::after {
            content: '';
            position: absolute;
            top: 100%;
            right: 1.25rem;
            border: 5px solid transparent;
            border-top-color: var(--neutral-800);
        }
        .floating-help-btn:hover + .floating-help-tooltip,
        .floating-help-btn:focus + .floating-help-tooltip {
            opacity: 1;
            transform: translateY(0);
        }
        @keyframes helpPulse {
            0%, 100% { box-shadow: 0 4px 16px rgba(37,99,235,0.35); }
            50%        { box-shadow: 0 4px 24px rgba(37,99,235,0.55), 0 0 0 8px rgba(37,99,235,0.12); }
        }

        /* Instructions Modal */
        .instr-section {
            padding: 1rem 1.25rem;
            border-radius: 0.625rem;
            background: var(--neutral-50);
            margin-bottom: 1.25rem;
        }
        .instr-section h6 {
            font-weight: 700;
            color: var(--neutral-800);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.75rem;
        }
        .instr-section ul, .instr-section ol {
            padding-left: 1.4rem;
            margin-bottom: 0;
        }
        .instr-section li { margin-bottom: 0.4rem; color: var(--neutral-700); font-size: 0.9rem; }
        .instr-bl-blue    { border-left: 4px solid var(--primary-blue); }
        .instr-bl-green   { border-left: 4px solid var(--accent-color); }
        .instr-bl-yellow  { border-left: 4px solid var(--warning-color); }
        .status-pill {
            display: inline-block;
            font-size: 0.72rem;
            font-weight: 600;
            padding: 0.2rem 0.55rem;
            border-radius: 999px;
            color: #fff;
        }
        @media (max-width: 576px) {
            .floating-help-btn { bottom: 4.5rem; right: 1rem; width: 50px; height: 50px; font-size: 1.25rem; }
            .floating-help-tooltip { display: none; }
        }

        /* =============================================
           INSTRUCTION SLIDESHOW MODAL
        ============================================= */
        .instr-modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.65);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            z-index: 99999;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            animation: fadeInOverlay 0.4s ease;
        }
        @keyframes fadeInOverlay {
            from { opacity: 0; }
            to   { opacity: 1; }
        }
        .instr-modal-overlay.hide {
            animation: fadeOutOverlay 0.35s ease forwards;
        }
        @keyframes fadeOutOverlay {
            from { opacity: 1; }
            to   { opacity: 0; pointer-events: none; }
        }

        .instr-modal-box {
            background: #fff;
            border-radius: 1.25rem;
            box-shadow: 0 24px 64px rgba(0,0,0,0.4);
            width: 100%;
            max-width: 780px;
            overflow: hidden;
            animation: slideUpModal 0.4s cubic-bezier(0.34,1.56,0.64,1);
            position: relative;
        }
        @keyframes slideUpModal {
            from { opacity:0; transform: translateY(40px) scale(0.95); }
            to   { opacity:1; transform: translateY(0) scale(1); }
        }

        /* Header strip */
        .instr-modal-header {
            background: var(--nu-blue);
            color: #fff;
            padding: 1rem 1.5rem 0.875rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .instr-modal-header .instr-modal-title {
            font-size: 0.95rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .instr-modal-close {
            background: rgba(255,255,255,0.15);
            border: none;
            color: #fff;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            transition: background 0.2s;
            flex-shrink: 0;
        }
        .instr-modal-close:hover { background: rgba(255,255,255,0.3); }

        /* Slide image area */
        .instr-slides {
            position: relative;
            overflow: hidden;
            background: var(--neutral-100);
        }
        .instr-slide {
            display: none;
            padding: 0;
        }
        .instr-slide.active {
            display: block;
            animation: slideIn 0.35s cubic-bezier(0.4,0,0.2,1);
        }
        .instr-slide.slide-out {
            animation: slideOut 0.35s cubic-bezier(0.4,0,0.2,1) forwards;
        }
        @keyframes slideIn {
            from { opacity:0; transform: translateX(40px); }
            to   { opacity:1; transform: translateX(0); }
        }
        @keyframes slideOut {
            from { opacity:1; transform: translateX(0); }
            to   { opacity:0; transform: translateX(-40px); }
        }
        .instr-slide img {
            width: 100%;
            height: auto;
            display: block;
            max-height: 72vh;
            object-fit: contain;
            background: #f3f4f6;
        }

        /* Dots */
        .instr-dots {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.875rem 0 0.25rem;
        }
        .instr-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--neutral-300);
            transition: background 0.25s, width 0.25s;
            cursor: pointer;
            border: none;
        }
        .instr-dot.active {
            background: var(--nu-blue);
            width: 22px;
            border-radius: 4px;
        }

        /* Footer nav */
        .instr-modal-footer {
            padding: 0.875rem 1.5rem 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
        }
        .instr-step-label {
            font-size: 0.78rem;
            color: var(--neutral-400);
            font-weight: 500;
        }
        .instr-nav-btns {
            display: flex;
            gap: 0.5rem;
        }
        .instr-btn-prev, .instr-btn-next {
            padding: 0.5rem 1.1rem;
            border-radius: var(--border-radius-lg);
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            border: 2px solid var(--neutral-200);
            background: #fff;
            color: var(--neutral-700);
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 0.35rem;
        }
        .instr-btn-prev:hover { background: var(--neutral-100); }
        .instr-btn-next {
            background: var(--nu-blue);
            border-color: var(--nu-blue);
            color: #fff;
        }
        .instr-btn-next:hover { background: #002277; border-color: #002277; }
        .instr-btn-prev:disabled { opacity: 0.35; cursor: not-allowed; }

        /* Don't show again */
        .instr-skip {
            font-size: 0.75rem;
            color: var(--neutral-400);
            text-decoration: underline;
            cursor: pointer;
            background: none;
            border: none;
            padding: 0;
        }
        .instr-skip:hover { color: var(--neutral-600); }

        @media (max-width: 480px) {
            .instr-modal-box { border-radius: 1rem; }
            .instr-slide img { max-height: 45vh; }
            .instr-modal-footer { padding: 0.75rem 1rem 1rem; }
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
            <span class="nu-welcome">Welcome to NU Lipa</span>
        </header>

        <!-- Main content area -->
        <main class="main-content">
            <div class="document-request-card">
                <h1 class="card-title">NU Document Request</h1>
                <p class="card-subtitle">Secure access to your academic documents</p>
                
                <!-- Login Button -->
                <a href="<?php echo e(route('auth.login')); ?>" class="btn-login" aria-label="Go to login page">
                    <i class="bi bi-box-arrow-in-right" aria-hidden="true"></i>
                    Login
                </a>

                <!-- Divider -->
                <div class="divider" role="separator" aria-label="or">
                    <span class="divider-text">For NU Students</span>
                </div>

                <!-- Walk-In Button -->
                <a href="<?php echo e(route('onsite.index')); ?>" class="btn-walkin" aria-label="Request documents without login">
                    <i class="bi bi-person-walking" aria-hidden="true"></i>
                    Walk-In Request
                </a>
                <p class="no-login-text">No login required</p>
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

        <!-- Floating Help Button -->
        <button type="button" class="floating-help-btn" data-bs-toggle="modal" data-bs-target="#instructionsModal" aria-label="How to use this system">
            <i class="bi bi-question-lg"></i>
        </button>
        <span class="floating-help-tooltip">How to use</span>
    </div>

    <!-- ====== Instructions Modal ====== -->
    <div class="modal fade" id="instructionsModal" tabindex="-1" aria-labelledby="instrModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">

                <!-- Header -->
                <div class="modal-header" style="background: var(--nu-blue); color: #fff;">
                    <h5 class="modal-title fw-bold d-flex align-items-center gap-2" id="instrModalLabel">
                        <i class="bi bi-book"></i> How to Use the NU Document Request System
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Body -->
                <div class="modal-body">

                    <!-- Intro -->
                    <p class="text-muted mb-3" style="font-size:0.9rem;">
                        Welcome to the <strong>NU Lipa Document Request System</strong>. Follow the guide below to request, track, and receive your academic documents.
                    </p>

                    <!-- 1. Who Can Use -->
                    <div class="instr-section instr-bl-blue">
                        <h6><i class="bi bi-people text-primary"></i> Who Can Use This System?</h6>
                        <ul>
                            <li><strong>NU Students</strong> — log in with your school email to request documents online</li>
                            <li><strong>Walk-in Visitors</strong> — use the <em>Walk-In Request</em> option; no account needed</li>
                            <li><strong>Alumni</strong> — visit the registrar in person or use the walk-in form</li>
                        </ul>
                    </div>

                    <!-- 2. Logging In -->
                    <div class="instr-section instr-bl-blue">
                        <h6><i class="bi bi-box-arrow-in-right text-primary"></i> Logging In (For Students)</h6>
                        <ol>
                            <li>Click the <strong>Login</strong> button on this page</li>
                            <li>Enter your <strong>school email</strong> (e.g., <code>yourname@nulipa.edu.ph</code>)</li>
                            <li>Enter your <strong>password</strong> and click <em>Sign In</em></li>
                            <li>If you forgot your password, click <strong>Reset Password</strong> on the login page</li>
                        </ol>
                    </div>

                    <!-- 3. Requesting Documents -->
                    <div class="instr-section instr-bl-green">
                        <h6><i class="bi bi-file-earmark-plus" style="color:var(--accent-color);"></i> Requesting a Document</h6>
                        <ol>
                            <li>After logging in, go to <strong>Request Document</strong> from your dashboard</li>
                            <li>Select the <strong>document type</strong> (Transcript of Records, Certification, Diploma, etc.)</li>
                            <li>Choose the <strong>number of copies</strong> needed</li>
                            <li>State the <strong>purpose</strong> of your request</li>
                            <li>Review the details and click <strong>Submit Request</strong></li>
                            <li>You will receive a <strong>Reference Number</strong> (e.g., <code>SR-20251104-0002</code>) — <em>save this!</em></li>
                        </ol>
                    </div>

                    <!-- 4. Walk-in Request -->
                    <div class="instr-section instr-bl-green">
                        <h6><i class="bi bi-person-walking" style="color:var(--accent-color);"></i> Walk-In Request (No Login Required)</h6>
                        <ol>
                            <li>Click <strong>Walk-In Request</strong> on this page</li>
                            <li>Fill in your <strong>name, student ID, and contact details</strong></li>
                            <li>Choose your document type and number of copies</li>
                            <li>Submit the form — a <strong>queue number</strong> will be issued</li>
                            <li>Wait for your number to be called at the registrar's window</li>
                        </ol>
                    </div>

                    <!-- 5. Tracking -->
                    <div class="instr-section instr-bl-blue">
                        <h6><i class="bi bi-geo-alt text-primary"></i> Tracking Your Request</h6>
                        <ul>
                            <li><strong>Web Portal:</strong> Log in and open <em>My Requests</em> to check the latest status</li>
                            <li><strong>Mobile App:</strong> Download the <em>NU Document Request</em> app to see your live queue position and get push notifications</li>
                            <li><strong>Reference Number:</strong> Use it any time to look up your request status</li>
                        </ul>
                    </div>

                    <!-- 6. Status Guide -->
                    <div class="instr-section">
                        <h6><i class="bi bi-list-check text-info"></i> Request Status Guide</h6>
                        <table class="table table-sm table-bordered mb-0" style="font-size:0.85rem;">
                            <thead class="table-light">
                                <tr><th>Status</th><th>Meaning</th></tr>
                            </thead>
                            <tbody>
                                <tr><td><span class="status-pill" style="background:#f59e0b;">Pending</span></td><td>Submitted — waiting for a registrar to pick it up</td></tr>
                                <tr><td><span class="status-pill" style="background:#0ea5e9;">In Queue</span></td><td>Assigned to a registrar, in processing line</td></tr>
                                <tr><td><span class="status-pill" style="background:#2563eb;">Processing</span></td><td>Registrar is actively working on your document</td></tr>
                                <tr><td><span class="status-pill" style="background:#10b981;">Ready for Pickup</span></td><td>Document is done — visit the office to collect it</td></tr>
                                <tr><td><span class="status-pill" style="background:#6b7280;">Completed</span></td><td>Document has been picked up and the request is closed</td></tr>
                                <tr><td><span class="status-pill" style="background:#ef4444;">Rejected</span></td><td>Request could not be fulfilled (reason will be provided)</td></tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- 7. Payment & Pickup -->
                    <div class="instr-section instr-bl-yellow">
                        <h6><i class="bi bi-cash-coin" style="color:var(--warning-color);"></i> Payment &amp; Document Pickup</h6>
                        <ul>
                            <li><strong>Fees:</strong> Applicable fees will be shown in your request details. Pay at the registrar's cashier window before claiming your document.</li>
                            <li><strong>Processing Time:</strong> Standard — 3 to 5 business days. Rush requests may be accommodated upon request.</li>
                            <li><strong>Pickup:</strong> Bring a <em>valid school ID</em> and your <em>reference number</em>. Unclaimed documents after 30 days require re-requesting.</li>
                        </ul>
                    </div>

                    <!-- 8. Reminders -->
                    <div class="instr-section instr-bl-yellow">
                        <h6><i class="bi bi-exclamation-triangle" style="color:var(--warning-color);"></i> Important Reminders</h6>
                        <ul>
                            <li>Never share your login credentials with anyone</li>
                            <li>Double-check all details before submitting — corrections may cause delays</li>
                            <li>Office hours: <strong>Monday – Friday, 8:00 AM – 5:00 PM</strong></li>
                            <li>Your data is protected under <strong>RA 10173 (Data Privacy Act of 2012)</strong></li>
                        </ul>
                    </div>

                    <!-- 9. Contact -->
                    <div class="instr-section" style="background: linear-gradient(135deg,rgba(37,99,235,0.06),rgba(16,185,129,0.06)); border: 1.5px solid var(--primary-blue);">
                        <h6><i class="bi bi-headset text-primary"></i> Need Help?</h6>
                        <div class="d-flex flex-column gap-1" style="font-size:0.9rem;">
                            <div><i class="bi bi-envelope me-2 text-primary"></i><strong>Email:</strong> piquizon@nu-lipa.edu.ph</div>
                            <div><i class="bi bi-geo-alt me-2 text-primary"></i><strong>Location:</strong> NU Building, SM City Lipa, JP Laurel Highway, Lipa City, Batangas</div>
                            <div><i class="bi bi-clock me-2 text-primary"></i><strong>Office Hours:</strong> Monday – Friday, 8:00 AM – 5:00 PM</div>
                        </div>
                    </div>

                </div><!-- /modal-body -->

                <!-- Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal" style="background:var(--nu-blue);border-color:var(--nu-blue);">
                        <i class="bi bi-check-circle me-1"></i> Got It!
                    </button>
                </div>

            </div>
        </div>
    </div>

    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- ====== INSTRUCTION SLIDESHOW MODAL (auto-show on first visit) ====== -->
    <div class="instr-modal-overlay" id="instrOverlay">
        <div class="instr-modal-box" role="dialog" aria-modal="true" aria-labelledby="instrModalTitle">

            <!-- Header -->
            <div class="instr-modal-header">
                <span class="instr-modal-title" id="instrModalTitle">
                    <i class="bi bi-book-fill"></i> How to Use the System
                </span>
                <button class="instr-modal-close" id="instrClose" aria-label="Close instructions">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <!-- Slides -->
            <div class="instr-slides">
                <div class="instr-slide active" data-slide="0">
                    <img src="<?php echo e(asset('images/instructions/INSTRUCTION1.png')); ?>" alt="Step 1 - Login Instructions">
                </div>
                <div class="instr-slide" data-slide="1">
                    <img src="<?php echo e(asset('images/instructions/INSTRUCTION2.png')); ?>" alt="Step 2 - Request Instructions">
                </div>
                <div class="instr-slide" data-slide="2">
                    <img src="<?php echo e(asset('images/instructions/INSTRUCTION3.png')); ?>" alt="Step 3 - Tracking Instructions">
                </div>
            </div>

            <!-- Dots -->
            <div class="instr-dots">
                <button class="instr-dot active" data-dot="0" aria-label="Slide 1"></button>
                <button class="instr-dot" data-dot="1" aria-label="Slide 2"></button>
                <button class="instr-dot" data-dot="2" aria-label="Slide 3"></button>
            </div>

            <!-- Footer nav -->
            <div class="instr-modal-footer">
                <button class="instr-skip" id="instrSkip">Don't show again</button>
                <div class="instr-nav-btns">
                    <button class="instr-btn-prev" id="instrPrev" disabled>
                        <i class="bi bi-chevron-left"></i> Back
                    </button>
                    <button class="instr-btn-next" id="instrNext">
                        Next <i class="bi bi-chevron-right"></i>
                    </button>
                </div>
            </div>

        </div>
    </div>
    
    <!-- Instruction Slideshow JS -->
    <script>
    (function () {
        var STORAGE_KEY = 'nu_instr_seen';
        var overlay   = document.getElementById('instrOverlay');
        var closeBtn  = document.getElementById('instrClose');
        var skipBtn   = document.getElementById('instrSkip');
        var prevBtn   = document.getElementById('instrPrev');
        var nextBtn   = document.getElementById('instrNext');
        var slides    = document.querySelectorAll('.instr-slide');
        var dots      = document.querySelectorAll('.instr-dot');
        var current   = 0;
        var total     = slides.length;

        // Hide if user clicked "Don't show again"
        if (localStorage.getItem(STORAGE_KEY)) {
            overlay.style.display = 'none';
            return;
        }

        function goTo(index) {
            slides[current].classList.remove('active');
            dots[current].classList.remove('active');
            current = index;
            slides[current].classList.add('active');
            dots[current].classList.add('active');
            prevBtn.disabled = current === 0;
            nextBtn.innerHTML = current === total - 1
                ? '<i class="bi bi-check2"></i> Got It!'
                : 'Next <i class="bi bi-chevron-right"></i>';
        }

        function closeModal() {
            overlay.classList.add('hide');
            overlay.addEventListener('animationend', function () {
                overlay.style.display = 'none';
            }, { once: true });
        }

        nextBtn.addEventListener('click', function () {
            if (current < total - 1) {
                goTo(current + 1);
            } else {
                closeModal();
            }
        });

        prevBtn.addEventListener('click', function () {
            if (current > 0) goTo(current - 1);
        });

        dots.forEach(function (dot) {
            dot.addEventListener('click', function () {
                goTo(parseInt(this.dataset.dot));
            });
        });

        closeBtn.addEventListener('click', closeModal);

        skipBtn.addEventListener('click', function () {
            localStorage.setItem(STORAGE_KEY, '1');
            closeModal();
        });

        // Close on overlay background click
        overlay.addEventListener('click', function (e) {
            if (e.target === overlay) closeModal();
        });

        // Keyboard nav
        document.addEventListener('keydown', function (e) {
            if (!overlay || overlay.style.display === 'none') return;
            if (e.key === 'ArrowRight') nextBtn.click();
            if (e.key === 'ArrowLeft' && !prevBtn.disabled) prevBtn.click();
            if (e.key === 'Escape') closeModal();
        });
    })();
    </script>

    <!-- Custom JavaScript for enhanced interactions -->
    <script>
        // Enhanced button interactions and accessibility
        document.addEventListener('DOMContentLoaded', function() {
            // Add click animation to buttons with improved ripple effect
            function createRippleEffect(e, element) {
                const ripple = document.createElement('span');
                const rect = element.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;
                
                ripple.style.cssText = `
                    position: absolute;
                    width: ${size}px;
                    height: ${size}px;
                    left: ${x}px;
                    top: ${y}px;
                    border-radius: 50%;
                    background: rgba(255, 255, 255, 0.4);
                    transform: scale(0);
                    animation: ripple 0.6s cubic-bezier(0.4, 0, 0.2, 1);
                    pointer-events: none;
                    z-index: 1;
                `;
                
                element.style.position = 'relative';
                element.style.overflow = 'hidden';
                element.appendChild(ripple);
                
                setTimeout(() => {
                    if (ripple.parentNode) {
                        ripple.remove();
                    }
                }, 600);
            }

            // Enhanced ripple effect for buttons
            document.querySelectorAll('.btn-walkin, .btn-login').forEach(button => {
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

            // Form validation and loading states
            const loginForm = document.querySelector('form[action*="login"]');
            if (loginForm) {
                loginForm.addEventListener('submit', function(e) {
                    const submitBtn = this.querySelector('button[type="submit"]');
                    
                    // Add loading state
                    submitBtn.classList.add('btn-loading');
                    submitBtn.disabled = true;
                    
                    // Basic validation
                    const email = this.querySelector('#school_email');
                    const password = this.querySelector('#password');
                    
                    if (!email.value || !password.value) {
                        e.preventDefault();
                        submitBtn.classList.remove('btn-loading');
                        submitBtn.disabled = false;
                        
                        // Focus first empty field
                        const firstEmpty = email.value ? password : email;
                        firstEmpty.focus();
                        firstEmpty.style.borderColor = 'var(--error-color)';
                        
                        setTimeout(() => {
                            firstEmpty.style.borderColor = '';
                        }, 3000);
                    }
                });
            }

            // Add CSS for enhanced interactions
            const style = document.createElement('style');
            style.textContent = `
                @keyframes ripple {
                    to {
                        transform: scale(4);
                        opacity: 0;
                    }
                }

                .focused .form-label {
                    color: var(--primary-blue);
                    transform: translateY(-2px);
                    transition: all 0.2s ease;
                }

                .form-control[aria-invalid="true"] {
                    border-color: var(--error-color) !important;
                    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1) !important;
                }

                .btn-loading {
                    position: relative;
                    color: transparent !important;
                    cursor: not-allowed;
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

                @keyframes shake {
                    0%, 100% { transform: translateX(0); }
                    10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
                    20%, 40%, 60%, 80% { transform: translateX(5px); }
                }

                /* Password toggle button styling */
                .btn-link {
                    color: var(--neutral-400) !important;
                    text-decoration: none !important;
                    border: none !important;
                    background: none !important;
                    padding: 0 !important;
                    font-size: 1.1rem;
                    transition: color 0.2s ease;
                }

                .btn-link:hover {
                    color: var(--primary-blue) !important;
                }

                .btn-link:focus {
                    outline: 2px solid var(--primary-blue);
                    outline-offset: 2px;
                    border-radius: var(--border-radius-sm);
                }

                /* Enhanced focus indicators for accessibility */
                .btn-walkin:focus-visible,
                .btn-login:focus-visible,
                .register-link:focus-visible {
                    outline: 2px solid var(--primary-blue);
                    outline-offset: 2px;
                    border-radius: var(--border-radius-md);
                }

                /* Modal animations and improvements */
                .modal.fade .modal-dialog {
                    transform: scale(0.9) translate(0, -50px);
                    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                    opacity: 0;
                }

                .modal.show .modal-dialog {
                    transform: scale(1) translate(0, 0);
                    opacity: 1;
                }

                .modal-backdrop {
                    background-color: rgba(0, 0, 0, 0.6);
                    backdrop-filter: blur(4px);
                    -webkit-backdrop-filter: blur(4px);
                }

                /* Error state styling */
                .modal .alert-danger {
                    margin-bottom: 1rem;
                    animation: slideIn 0.3s ease-out;
                }

                /* Better mobile modal positioning */
                @media (max-width: 576px) {
                    .modal-dialog {
                        margin: 0.5rem auto;
                        max-width: calc(100vw - 1rem);
                        width: auto;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        min-height: calc(100vh - 1rem);
                    }
                    
                    .modal-dialog.modal-dialog-centered {
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        min-height: calc(100vh - 1rem);
                    }
                }

                /* Perfect centering for all screen sizes */
                .main-content {
                    display: flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                }

                .document-request-card {
                    margin: 0 auto !important;
                }

                .modal-dialog {
                    display: flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                }

                .modal-content {
                    margin: auto !important;
                }

                /* Reduce motion for users who prefer it */
                @media (prefers-reduced-motion: reduce) {
                    * {
                        animation-duration: 0.01ms !important;
                        animation-iteration-count: 1 !important;
                        transition-duration: 0.01ms !important;
                    }
                }
            `;
            document.head.appendChild(style);
        });
    </script>
</body>
</html><?php /**PATH D:\Nu-Regisv2\resources\views/auth/login.blade.php ENDPATH**/ ?>