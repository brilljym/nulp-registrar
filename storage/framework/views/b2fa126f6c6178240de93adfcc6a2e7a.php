<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registrar Dashboard - NU Lipa</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --nu-blue: #003399;
            --nu-blue-light: #0066cc;
            --nu-yellow: #FFD700;
            --nu-white: #ffffff;
            --nu-gray: #6c757d;
            --nu-light-gray: #f8f9fa;
            --nu-dark-overlay: rgba(0, 0, 0, 0.4);
            --nu-danger: #dc3545;
            --nu-success: #28a745;
            --sidebar-width: 300px;
            --header-height: 60px;
            --footer-height: 45px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body { 
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--nu-white);
            min-height: 100vh;
        }

        /* Enhanced Header */
        .nu-header {
            background: linear-gradient(135deg, var(--nu-blue) 0%, #001f5f 100%);
            color: var(--nu-white);
            padding: 0.75rem 2rem;
            height: var(--header-height);
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1050;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }

        .nu-logo-container {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .sidebar-toggle {
            background: rgba(255, 215, 0, 0.2);
            border: 2px solid rgba(255, 215, 0, 0.3);
            color: var(--nu-white);
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1.2rem;
        }

        .sidebar-toggle:hover {
            background: rgba(255, 215, 0, 0.3);
            border-color: var(--nu-yellow);
            transform: scale(1.05);
        }

        .nu-shield {
            height: 2.2rem;
            width: auto;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3));
        }

        .nu-title {
            font-size: 1.2rem;
            font-weight: 700;
            letter-spacing: 1px;
            background: linear-gradient(45deg, var(--nu-white), var(--nu-yellow));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .user-dropdown {
            position: relative;
            margin-left: auto;
        }

        .user-dropdown-toggle {
            background: rgba(255, 215, 0, 0.15);
            border: 2px solid rgba(255, 215, 0, 0.4);
            color: var(--nu-white);
            padding: 0.6rem 1.2rem;
            border-radius: 30px;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
            font-size: 0.95rem;
            min-width: 140px;
            justify-content: center;
        }

        .user-dropdown-toggle:hover {
            background: rgba(255, 215, 0, 0.25);
            border-color: var(--nu-yellow);
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(255, 215, 0, 0.2);
        }

        .user-dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            background: var(--nu-white);
            border-radius: 12px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.25);
            min-width: 220px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-15px) scale(0.95);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1000;
            margin-top: 0.75rem;
            border: 1px solid rgba(0, 51, 153, 0.1);
            overflow: hidden;
        }

        .user-dropdown.show .user-dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0) scale(1);
        }

        .user-dropdown-item {
            display: flex;
            align-items: center;
            gap: 0.875rem;
            padding: 1rem 1.25rem;
            color: var(--nu-gray);
            text-decoration: none;
            transition: all 0.2s ease;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            font-weight: 500;
            font-size: 0.95rem;
        }

        .user-dropdown-item:hover {
            background: linear-gradient(135deg, rgba(0, 51, 153, 0.05), rgba(0, 51, 153, 0.08));
            color: var(--nu-blue);
            transform: translateX(5px);
        }

        .user-dropdown-item:last-child {
            border-bottom: none;
        }

        .user-dropdown-item i {
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
        }

        /* Layout Container */
        .layout-container {
            display: flex;
            min-height: 100vh;
            padding-top: var(--header-height);
        }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            min-height: calc(100vh - var(--header-height));
            max-height: calc(100vh - var(--header-height));
            background: linear-gradient(180deg, var(--nu-blue) 0%, #001f5f 100%);
            color: var(--nu-white);
            /* Use specific transitions for smoother animation */
            transition: transform 350ms cubic-bezier(0.2, 0.8, 0.2, 1),
                        opacity 250ms ease-in-out,
                        box-shadow 250ms ease-in-out;
            will-change: transform, opacity;
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.15);
            flex-shrink: 0;
            position: fixed;
            top: var(--header-height);
            left: 0;
            z-index: 1000;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 1.5rem 0;
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 215, 0, 0.5) rgba(255, 255, 255, 0.1);
        }

        /* Custom Scrollbar for Webkit browsers */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 3px;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 215, 0, 0.6);
            border-radius: 3px;
            transition: background 0.3s ease;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 215, 0, 0.8);
        }

        .sidebar::-webkit-scrollbar-corner {
            background: transparent;
        }

        .sidebar.collapsed {
            width: 80px;
        }

        /* Fully hide the sidebar when toggled on desktop */
        .sidebar.hidden {
            /* Move off-screen using transform for smooth animation; avoid changing width/padding abruptly */
            transform: translateX(-100%);
            opacity: 0.0;
            pointer-events: none;
            box-shadow: none !important;
        }

        /* When sidebar is hidden, allow main content to take full width (animate margin-left)
           main-content-wrapper transition declared below. */
        .sidebar.hidden + .main-content-wrapper {
            margin-left: 0;
        }

        .sidebar.collapsed .nav-text {
            opacity: 0;
            transform: translateX(-10px);
        }

        .sidebar.collapsed .nav-section-title {
            opacity: 0;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.5rem;
            color: rgba(255, 255, 255, 0.85);
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-left: 4px solid transparent;
            font-weight: 500;
            position: relative;
            margin: 0.2rem 0;
            font-size: 0.95rem;
        }

        .sidebar a:hover {
            background: linear-gradient(90deg, rgba(255, 215, 0, 0.15), rgba(255, 215, 0, 0.05));
            color: var(--nu-white);
            border-left-color: var(--nu-yellow);
            transform: translateX(8px);
            box-shadow: inset 0 0 20px rgba(255, 215, 0, 0.1);
        }

        .sidebar a.active {
            background: linear-gradient(90deg, rgba(255, 215, 0, 0.25), rgba(255, 215, 0, 0.1));
            color: var(--nu-white);
            border-left-color: var(--nu-yellow);
            font-weight: 600;
            box-shadow: inset 0 0 25px rgba(255, 215, 0, 0.15);
        }

        .sidebar a i {
            font-size: 1.1rem;
            width: 24px;
            text-align: center;
            flex-shrink: 0;
        }

        .nav-text { 
            white-space: nowrap; 
            transition: all 0.3s ease;
        }

        .nav-section {
            margin-bottom: 2rem;
        }

        .nav-section:last-child {
            margin-bottom: 1rem;
        }

        .nav-section-title {
            padding: 0 1.5rem 0.75rem 1.5rem;
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
            position: relative;
        }

        .nav-section-title::after {
            content: '';
            position: absolute;
            bottom: 0.25rem;
            left: 1.5rem;
            right: 1.5rem;
            height: 1px;
            background: linear-gradient(90deg, var(--nu-yellow), transparent);
            opacity: 0.3;
        }

        /* Queue In Progress Styles */
        .queue-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1.5rem;
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
            margin: 0.1rem 0;
            background: rgba(255, 215, 0, 0.1);
            border-radius: 0 8px 8px 0;
            font-size: 0.85rem;
        }

        .queue-item:hover {
            background: linear-gradient(90deg, rgba(255, 215, 0, 0.2), rgba(255, 215, 0, 0.1));
            color: var(--nu-white);
            border-left-color: var(--nu-yellow);
            transform: translateX(5px);
        }

        .queue-item.priority {
            background: linear-gradient(90deg, rgba(255, 99, 132, 0.2), rgba(255, 99, 132, 0.1));
            border-left-color: #ff6384;
        }
        
        .queue-item.waiting {
            background: rgba(108, 117, 125, 0.1);
            border-left-color: #6c757d;
            opacity: 0.7;
        }
        
        .queue-item.waiting:hover {
            background: rgba(108, 117, 125, 0.2);
            border-left-color: #6c757d;
        }

        .queue-item .queue-number {
            background: rgba(255, 215, 0, 0.8);
            color: var(--nu-blue);
            padding: 0.2rem 0.5rem;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.75rem;
            min-width: 45px;
            text-align: center;
        }
        
        .queue-item.waiting .queue-number {
            background: rgba(108, 117, 125, 0.8);
            color: white;
        }

        .queue-item .queue-info {
            flex: 1;
            min-width: 0;
        }

        .queue-item .queue-name {
            font-weight: 600;
            font-size: 0.8rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 0.1rem;
        }

        .queue-item .queue-status {
            font-size: 0.7rem;
            opacity: 0.8;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .queue-item .queue-icon {
            font-size: 1rem;
            opacity: 0.7;
        }

        .no-queue-items {
            padding: 1rem 1.5rem;
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.8rem;
            text-align: center;
            font-style: italic;
        }

        .loading-indicator {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.85rem;
            padding: 1rem 1.5rem;
            text-align: center;
        }

        .loading-indicator i {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* Responsive adjustments for queue items */
        .sidebar.collapsed .queue-item .queue-info {
            display: none;
        }

        .sidebar.collapsed .queue-item .queue-number {
            margin: 0 auto;
        }

        /* Main Content */
        .main-content-wrapper {
            flex: 1;
            margin-left: var(--sidebar-width);
            /* Animate only margin-left for performance and smoothness */
            transition: margin-left 350ms cubic-bezier(0.2, 0.8, 0.2, 1);
            will-change: margin-left;
            display: flex;
            flex-direction: column;
            min-height: calc(100vh - var(--header-height));
        }

        .sidebar.collapsed + .main-content-wrapper {
            margin-left: 80px;
        }

        .main-content {
            flex: 1;
            padding: 2rem;
            background: transparent;
        }

        .content-search-form {
            min-width: 320px;
        }

        .content-search-form .input-group {
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .content-search-form .input-group:focus-within {
            box-shadow: 0 6px 25px rgba(0, 51, 153, 0.15);
            transform: translateY(-2px);
        }

        .content-search-form .form-control {
            border: 2px solid rgba(0, 51, 153, 0.1);
            border-right: none;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .content-search-form .form-control:focus {
            border-color: var(--nu-blue);
            box-shadow: none;
            background: rgba(0, 51, 153, 0.02);
        }

        .content-search-form .btn {
            border: 2px solid rgba(0, 51, 153, 0.1);
            border-left: none;
            background: var(--nu-blue);
            color: white;
            padding: 0.75rem 1.25rem;
            transition: all 0.3s ease;
        }

        .content-search-form .btn:hover {
            background: var(--nu-blue-light);
            transform: scale(1.05);
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 280px;
                max-height: calc(100vh - var(--header-height));
                transform: translateX(-100%);
                transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                z-index: 1100;
                overflow-y: auto;
                -webkit-overflow-scrolling: touch; /* Smooth scrolling on iOS */
            }

            .sidebar.mobile-open {
                transform: translateX(0);
                box-shadow: 8px 0 30px rgba(0, 0, 0, 0.3);
            }

            .main-content-wrapper {
                margin-left: 0;
            }

            .nu-header {
                padding: 0.75rem 1rem;
            }

            .nu-title {
                font-size: 1rem;
                display: none;
            }

            .nu-logo-container {
                gap: 0.75rem;
            }

            .user-dropdown-toggle {
                padding: 0.5rem 0.75rem;
                font-size: 0.9rem;
                min-width: auto;
            }

            .user-dropdown-toggle span {
                display: none;
            }

            .user-dropdown-menu {
                right: -10px;
                min-width: 180px;
            }

            .main-content {
                padding: 1rem;
            }

            .sidebar a {
                padding: 1.2rem 1.5rem;
                font-size: 1rem;
            }

            .sidebar a i {
                font-size: 1.2rem;
            }

            .nav-section-title {
                font-size: 0.7rem;
                padding: 0 1.5rem 0.5rem 1.5rem;
            }

            /* Enhanced scrollbar for mobile */
            .sidebar::-webkit-scrollbar {
                width: 4px;
            }
        }

        @media (max-width: 480px) {
            .nu-header {
                padding: 0.5rem 0.75rem;
            }

            .sidebar {
                width: 100%;
                max-height: calc(100vh - var(--header-height));
            }

            .main-content {
                padding: 0.75rem;
            }

            .user-dropdown-menu {
                right: 0;
                left: auto;
                min-width: 160px;
            }
        }
        
        /* 2FA Switch Styles */
        .twofa-switch-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 20px;
            color: #000000;
            font-size: 0.95rem;
            font-weight: 500;
            gap: 12px;
            position: relative;
            background: none;
            border: none;
            width: 100%;
            text-align: left;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .twofa-switch-container:hover {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            color: #2c2f92;
        }
        
        .twofa-switch-info {
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 1;
        }
        
        .twofa-switch-info i {
            width: 18px;
            text-align: center;
            font-size: 16px;
        }
        
        .switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
            flex-shrink: 0;
        }
        
        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 24px;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 50%;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
        }
        
        .switch input:checked + .slider {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            box-shadow: 
                inset 0 2px 4px rgba(0, 0, 0, 0.1),
                0 0 0 2px rgba(40, 167, 69, 0.2);
        }
        
        .switch input:focus + .slider {
            box-shadow: 
                inset 0 2px 4px rgba(0, 0, 0, 0.1),
                0 0 0 2px rgba(44, 47, 146, 0.3);
        }
        
        .switch input:checked + .slider:before {
            transform: translateX(26px);
        }
        
        .switch:hover .slider {
            box-shadow: 
                inset 0 2px 4px rgba(0, 0, 0, 0.1),
                0 2px 8px rgba(0, 0, 0, 0.15);
        }
        
        .switch input:checked:hover + .slider {
            background: linear-gradient(135deg, #218838 0%, #1c9c8a 100%);
        }
        
        .twofa-switch-container .switch-status {
            font-size: 0.85rem;
            opacity: 0.8;
            margin-left: 4px;
            transition: all 0.2s ease;
        }
        
        .twofa-switch-container.enabled .switch-status {
            color: #28a745;
            font-weight: 600;
        }
        
        .twofa-switch-container.disabled .switch-status {
            color: #6c757d;
        }
        
        /* Disabled switch styles */
        .switch input:disabled + .slider {
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        .switch input:disabled + .slider:before {
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        }
        
        .twofa-switch-container:has(input:disabled) {
            cursor: not-allowed;
            opacity: 0.7;
        }
        
        .twofa-switch-container:has(input:disabled):hover {
            background: none;
            transform: none;
        }
    </style>
</head>
<body>

<!-- Enhanced Header -->
<div class="nu-header">
    <div class="nu-logo-container">
        <button class="sidebar-toggle" onclick="toggleSidebar()">
            <span id="toggle-icon">☰</span>
        </button>
        <img src="<?php echo e(asset('images/NU_shield.svg.png')); ?>" alt="NU Shield" class="nu-shield">
        <div class="d-flex align-items-center gap-2">
            <span class="nu-title">NU LIPA - REGISTRAR</span>
        </div>
    </div>
    
    <div class="d-flex align-items-center gap-3">
        <?php if(auth()->guard()->check()): ?>
            <div class="user-dropdown" id="userDropdown">
                <button class="user-dropdown-toggle" onclick="toggleUserDropdown()">
                    <i class="bi bi-person-circle"></i>
                    <span><?php echo e(Auth::user()->first_name); ?></span>
                    <i class="bi bi-chevron-down" style="font-size: 12px;"></i>
                </button>
                <div class="user-dropdown-menu">
                    <?php if(Auth::user()->role && Auth::user()->role->name === 'registrar'): ?>
                    <div class="twofa-switch-container" id="twofa-container">
                        <div class="twofa-switch-info">
                            <i class="bi bi-shield-lock" id="2fa-icon"></i>
                            <div>
                                <span>Two-Factor Authentication</span>
                                <div class="switch-status" id="2fa-status">Loading...</div>
                            </div>
                        </div>
                        <label class="switch">
                            <input type="checkbox" id="2fa-switch" onchange="toggle2FA()">
                            <span class="slider"></span>
                        </label>
                    </div>
                    <div class="dropdown-divider"></div>
                    <?php endif; ?>
                    <a href="<?php echo e(route('logout')); ?>" class="user-dropdown-item">
                        <i class="bi bi-box-arrow-right"></i>
                        <span>Logout</span>
                    </a>
                </div>
            </div>
        <?php else: ?>
            <a href="<?php echo e(route('logout')); ?>" class="user-dropdown-toggle">
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- Layout with Sidebar -->
<div class="layout-container">
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-content">
            <!-- Queue In Progress Section -->
            <div class="nav-section">
                <div class="nav-section-title">Queue In Progress</div>
                <div id="queue-in-progress-container">
                    <div class="loading-indicator" style="padding: 1rem 1.5rem; color: rgba(255, 255, 255, 0.7); font-size: 0.85rem;">
                        <i class="bi bi-clock-history me-2"></i>Loading queue status...
                    </div>
                </div>
            </div>

            <!-- Student Requests Section -->
            <div class="nav-section">
                <div class="nav-section-title">Student Requests</div>
                <a href="<?php echo e(route('registrar.all-requests')); ?>" class="<?php echo e(request()->routeIs('registrar.all-requests') ? 'active' : ''); ?>">
                    <i class="bi bi-grid-3x3-gap"></i>
                    <span class="nav-text">ALL</span>
                </a>
                <a href="<?php echo e(route('registrar.completed')); ?>" class="<?php echo e(request()->routeIs('registrar.completed') ? 'active' : ''); ?>">
                    <i class="bi bi-check-circle"></i>
                    <span class="nav-text">COMPLETED</span>
                </a>
                <a href="<?php echo e(route('registrar.pending')); ?>" class="<?php echo e(request()->routeIs('registrar.pending') ? 'active' : ''); ?>">
                    <i class="bi bi-clock"></i>
                    <span class="nav-text">PENDING</span>
                </a>
            </div>

            <!-- Onsite Requests Section -->
            <div class="nav-section">
                <div class="nav-section-title">Onsite Requests</div>
                <a href="<?php echo e(route('registrar.onsite.management')); ?>" class="<?php echo e(request()->routeIs('registrar.onsite.management') ? 'active' : ''); ?>">
                    <i class="bi bi-grid-3x3-gap"></i>
                    <span class="nav-text">ALL</span>
                </a>
                <a href="<?php echo e(route('registrar.onsite.completed')); ?>" class="<?php echo e(request()->routeIs('registrar.onsite.completed') ? 'active' : ''); ?>">
                    <i class="bi bi-check-circle"></i>
                    <span class="nav-text">COMPLETED</span>
                </a>
                <a href="<?php echo e(route('registrar.onsite.pending')); ?>" class="<?php echo e(request()->routeIs('registrar.onsite.pending') ? 'active' : ''); ?>">
                    <i class="bi bi-clock"></i>
                    <span class="nav-text">PENDING</span>
                </a>
            </div>

            <!-- Management Section -->
            <div class="nav-section">
                <div class="nav-section-title">Management</div>
                <a href="<?php echo e(route('registrar.analytics')); ?>" class="<?php echo e(request()->routeIs('registrar.analytics') ? 'active' : ''); ?>" title="View system analytics and reports">
                    <i class="bi bi-graph-up"></i>
                    <span class="nav-text"> Report & Analytics</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content Wrapper -->
    <div class="main-content-wrapper">
        <main class="main-content">
            <div class="container-fluid py-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div></div>
                    <?php if(!request()->routeIs('registrar.analytics')): ?>
                    <form method="GET" action="<?php echo e(route('registrar.dashboard')); ?>" class="content-search-form">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Search by name, ID, or doc" value="<?php echo e(request('search')); ?>" autocomplete="off">
                            <button class="btn btn-admin" type="submit">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </form>
                    <?php endif; ?>
                </div>

                <?php echo $__env->yieldContent('content'); ?>
            </div>
        </main>
    </div>
</div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Pusher JS for Real-time Updates -->
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script><!-- Pusher JS for Real-time Updates -->
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
    // Initialize Pusher
    const pusher = new Pusher('<?php echo e(config('broadcasting.connections.pusher.key')); ?>', {
        cluster: '<?php echo e(config('broadcasting.connections.pusher.options.cluster')); ?>',
        encrypted: true
    });
    
    // Debug Pusher connection
    pusher.connection.bind('connected', function() {
        console.log('✅ Pusher connected successfully');
    });
    
    pusher.connection.bind('error', function(err) {
        console.error('❌ Pusher connection error:', err);
    });

    pusher.connection.bind('disconnected', function() {
        console.log('⚠️ Pusher disconnected');
    });

    // Subscribe to registrar notifications channel
    const registrarChannel = pusher.subscribe('registrar-notifications');
    
    // Subscribe to new onsite requests channel
    const newOnsiteRequestsChannel = pusher.subscribe('new-onsite-requests');
    
    // Subscribe to onsite request updates channel
    const onsiteRequestUpdatesChannel = pusher.subscribe('onsite-request-updates');
    
    // Subscribe to new student requests channel
    const newStudentRequestsChannel = pusher.subscribe('new-student-requests');
    
    // Listen for all status updates on main registrar channel
    registrarChannel.bind('realtime.notification', function(data) {
        console.log('🔄 Received registrar notification:', data);
        
        // Show notification
        showRegistrarNotification(data.message, data.type, data.data);
        
        // Try to update the status in the DOM first
        if (data.data && data.data.status_update && data.data.request_type === 'student') {
            console.log('🔄 Attempting DOM update for reference:', data.data.reference_no);
            updateRequestStatus(data.data.reference_no, data.data.status);
        }
        
        // Also refresh the page as backup
        if (data.data && data.data.status_update) {
            console.log('🔄 Status update detected, reloading page in 500ms...');
            setTimeout(() => {
                // Refresh any registrar page when status updates occur
                const currentPage = window.location.pathname;
                if (currentPage.includes('/registrar')) {
                    console.log('🔄 Reloading registrar page:', currentPage);
                    window.location.reload();
                } else {
                    console.log('🔄 Not reloading - not on registrar page:', currentPage);
                }
            }, 500);
        }
    });

    // Listen for new onsite requests
    newOnsiteRequestsChannel.bind('realtime.notification', function(data) {
        console.log('Received new onsite request:', data);
        
        // Show notification with special styling for new requests
        showRegistrarNotification(data.message, 'new-request', data.data);
        
        // Auto-refresh dashboard to show new request
        setTimeout(() => {
            const currentPage = window.location.pathname;
            if (currentPage.includes('/registrar')) {
                window.location.reload();
            }
        }, 1000);
    });

    // Listen for onsite request updates
    onsiteRequestUpdatesChannel.bind('realtime.notification', function(data) {
        console.log('Received onsite request update:', data);
        
        // Show notification
        showRegistrarNotification(data.message, 'request-updated', data.data);
        
        // Auto-refresh to show updated information
        setTimeout(() => {
            const currentPage = window.location.pathname;
            if (currentPage.includes('/registrar')) {
                window.location.reload();
            }
        }, 500);
    });

    // Listen for new student requests
    newStudentRequestsChannel.bind('realtime.notification', function(data) {
        console.log('Received new student request:', data);
        
        // Show notification
        showRegistrarNotification(data.message, 'new-request', data.data);
        
        // Auto-refresh to show new request
        setTimeout(() => {
            const currentPage = window.location.pathname;
            if (currentPage.includes('/registrar')) {
                window.location.reload();
            }
        }, 1000);
    });

    // Function to show registrar notifications
    function showRegistrarNotification(message, type = 'info', data = {}) {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `alert alert-${getBootstrapClass(type)} alert-dismissible fade show position-fixed`;
        notification.style.cssText = `
            top: 80px; 
            right: 20px; 
            z-index: 9999; 
            max-width: 400px;
            min-width: 350px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        `;
        
        // Create enhanced notification content
        let notificationContent = `
            <div class="d-flex align-items-start">
                <i class="bi ${getNotificationIcon(type)} me-2 mt-1"></i>
                <div class="flex-grow-1">
                    <div class="fw-semibold">${message}</div>
        `;
        
        // Add additional info for status updates
        if (data.status_update && data.request_id) {
            notificationContent += `
                <small class="text-muted d-block mt-1">
                    Request: ${data.request_id}
                    ${data.student_name ? ` | Student: ${data.student_name}` : ''}
                    ${data.request_type ? ` | Type: ${data.request_type}` : ''}
                </small>
            `;
        }
        
        notificationContent += `
                </div>
                <button type="button" class="btn-close ms-2" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        notification.innerHTML = notificationContent;
        
        // Add to page
        document.body.appendChild(notification);
        
        // Auto-remove after 8 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 8000);
        
        // Play notification sound (optional)
        if (data.status_update) {
            playNotificationSound();
        }
    }

    // Convert notification type to Bootstrap class
    function getBootstrapClass(type) {
        switch(type) {
            case 'success': return 'success';
            case 'error': return 'danger';
            case 'warning': return 'warning';
            case 'status-update': return 'info';
            case 'new-request': return 'primary';
            case 'request-updated': return 'warning';
            case 'payment-verified': return 'success';
            default: return 'info';
        }
    }

    // Get appropriate icon for notification type
    function getNotificationIcon(type) {
        switch(type) {
            case 'success': return 'bi-check-circle';
            case 'error': return 'bi-exclamation-triangle';
            case 'warning': return 'bi-exclamation-circle';
            case 'status-update': return 'bi-arrow-repeat';
            case 'new-request': return 'bi-plus-circle';
            case 'request-updated': return 'bi-pencil-square';
            case 'payment-verified': return 'bi-credit-card-2-front';
            default: return 'bi-info-circle';
        }
    }

    // Simple notification sound
    function playNotificationSound() {
        try {
            // Create a simple beep sound using Web Audio API
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();
            
            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);
            
            oscillator.frequency.value = 800;
            oscillator.type = 'sine';
            
            gainNode.gain.setValueAtTime(0.1, audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.1);
            
            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + 0.1);
        } catch (error) {
            // Ignore audio errors
            console.log('Audio notification not available');
        }
    }

    // Clean up on page unload
    window.addEventListener('beforeunload', function() {
        pusher.disconnect();
    });

    // Queue In Progress Management
    function loadQueueInProgress() {
        console.log('🎯 Loading queue in progress...');
        
        fetch('/registrar/queue/in-progress', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('🎯 Queue data received:', data);
            updateQueueDisplay(data);
        })
        .catch(error => {
            console.error('🎯 Error loading queue:', error);
            updateQueueDisplay(null, error.message);
        });
    }

    function updateQueueDisplay(queueData, errorMessage = null) {
        const container = document.getElementById('queue-in-progress-container');
        if (!container) return;

        if (errorMessage) {
            container.innerHTML = `
                <div class="no-queue-items">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Unable to load queue
                </div>
            `;
            return;
        }

        if (!queueData || (!queueData.student_requests?.length && !queueData.onsite_requests?.length)) {
            container.innerHTML = `
                <div class="no-queue-items">
                    <i class="bi bi-check-circle me-2"></i>
                    No active queue
                </div>
            `;
            return;
        }

        let html = '';
        let waitingCount = 0;
        const maxWaitingDisplay = 1;
        let hasValidRequests = false; // Track if we have any valid requests to display

        // Add student requests in queue
        if (queueData.student_requests?.length) {
            queueData.student_requests.forEach(request => {
                // Only show specific statuses: in_queue, waiting, ready_for_pickup
                if (!['in_queue', 'waiting', 'ready_for_pickup'].includes(request.status)) {
                    return; // Skip this request
                }
                
                hasValidRequests = true; // Mark that we have valid requests
                
                const isPriority = request.status === 'ready_for_pickup';
                const isWaiting = request.status === 'waiting';
                
                // If it's waiting and we've already shown the max waiting, skip
                if (isWaiting) {
                    if (waitingCount >= maxWaitingDisplay) {
                        return; // Skip this waiting request
                    }
                    waitingCount++;
                }
                
                html += `
                    <div class="queue-item ${isPriority ? 'priority' : ''} ${isWaiting ? 'waiting' : ''}" title="${request.student?.user?.first_name || 'Unknown'} ${request.student?.user?.last_name || ''} - ${request.status.replace('_', ' ').toUpperCase()} (Student Request)">
                        <div class="queue-number">${request.queue_number || 'N/A'}</div>
                        <div class="queue-info">
                            <div class="queue-name">${(request.student?.user?.first_name || 'Unknown')} ${(request.student?.user?.last_name || '').charAt(0)}.</div>
                            <div class="queue-status">${request.status.replace('_', ' ')} • <span style="color: #17a2b8; font-weight: 600;">Student</span></div>
                        </div>
                        <i class="bi ${isPriority ? 'bi-exclamation-circle' : (isWaiting ? 'bi-hourglass-split' : 'bi-person-circle')} queue-icon"></i>
                    </div>
                `;
            });
        }

        // Add onsite requests in queue
        if (queueData.onsite_requests?.length) {
            queueData.onsite_requests.forEach(request => {
                // Only show specific statuses: in_queue, waiting, ready_for_pickup
                if (!['in_queue', 'waiting', 'ready_for_pickup'].includes(request.status)) {
                    return; // Skip this request
                }
                
                hasValidRequests = true; // Mark that we have valid requests
                
                const isPriority = request.status === 'ready_for_pickup';
                const isWaiting = request.status === 'waiting';
                
                // If it's waiting and we've already shown the max waiting, skip
                if (isWaiting) {
                    if (waitingCount >= maxWaitingDisplay) {
                        return; // Skip this waiting request
                    }
                    waitingCount++;
                }
                
                const name = request.full_name || 'Unknown';
                const nameParts = name.split(' ');
                const displayName = nameParts.length > 1 ? `${nameParts[0]} ${nameParts[nameParts.length-1].charAt(0)}.` : name;
                
                html += `
                    <div class="queue-item ${isPriority ? 'priority' : ''} ${isWaiting ? 'waiting' : ''}" title="${name} - ${request.status.replace('_', ' ').toUpperCase()} (Onsite Request)">
                        <div class="queue-number">${request.queue_number || 'N/A'}</div>
                        <div class="queue-info">
                            <div class="queue-name">${displayName}</div>
                            <div class="queue-status">${request.status.replace('_', ' ')} • <span style="color: #28a745; font-weight: 600;">Onsite</span></div>
                        </div>
                        <i class="bi ${isPriority ? 'bi-exclamation-circle' : (isWaiting ? 'bi-hourglass-split' : 'bi-building')} queue-icon"></i>
                    </div>
                `;
            });
        }

        // Check if we have any valid requests after filtering
        if (!hasValidRequests) {
            container.innerHTML = `
                <div class="no-queue-items">
                    <i class="bi bi-check-circle me-2"></i>
                    No queue in progress
                </div>
            `;
            return;
        }

        // Add indicator if there are more waiting requests
        const totalWaiting = (queueData.student_requests?.filter(r => r.status === 'waiting').length || 0) + 
                            (queueData.onsite_requests?.filter(r => r.status === 'waiting').length || 0);
        
        if (totalWaiting > maxWaitingDisplay) {
            html += `
                <div class="queue-item waiting" style="opacity: 0.6; font-style: italic;">
                    <div class="queue-number">...</div>
                    <div class="queue-info">
                        <div class="queue-name">+${totalWaiting - maxWaitingDisplay} more</div>
                        <div class="queue-status">waiting in queue</div>
                    </div>
                    <i class="bi bi-three-dots queue-icon"></i>
                </div>
            `;
        }

        container.innerHTML = html;
    }

    // Auto-refresh queue every 10 seconds
    setInterval(loadQueueInProgress, 10000);
</script>

<script>
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const icon = document.getElementById('toggle-icon');
    
    // Handle mobile vs desktop differently
    if (window.innerWidth <= 768) {
        sidebar.classList.toggle('mobile-open');
    } else {
        // For desktop, fully hide/show the sidebar instead of only collapsing it
        sidebar.classList.toggle('hidden');

        // Keep collapsed state as an optional compact mode; remove it when hidden
        // Always show the hamburger icon; we don't use the arrow anymore
        if (sidebar.classList.contains('hidden')) {
            sidebar.classList.remove('collapsed');
        }
        icon.innerHTML = '☰';
    }
}

function toggleUserDropdown() {
    const dropdown = document.getElementById('userDropdown');
    dropdown.classList.toggle('show');
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const userDropdown = document.getElementById('userDropdown');
    
    if (userDropdown && !userDropdown.contains(event.target)) {
        userDropdown.classList.remove('show');
    }
});

// Handle mobile sidebar
document.addEventListener('click', function(event) {
    const sidebar = document.getElementById('sidebar');
    const toggle = document.querySelector('.sidebar-toggle');
    
    if (window.innerWidth <= 768) {
        if (!sidebar.contains(event.target) && !toggle.contains(event.target)) {
            sidebar.classList.remove('mobile-open');
        }
    }
});

// Add click handler to sidebar for toggling
document.getElementById('sidebar').addEventListener('click', function(event) {
    // Only toggle if clicking on sidebar background, not on links or interactive elements
    if (event.target === this || event.target.classList.contains('sidebar-content') || 
        event.target.classList.contains('nav-section') || 
        event.target.classList.contains('nav-section-title')) {
        toggleSidebar();
    }
});

// Handle window resize
window.addEventListener('resize', function() {
    const sidebar = document.getElementById('sidebar');
    const icon = document.getElementById('toggle-icon');
    
    if (window.innerWidth > 768) {
        sidebar.classList.remove('mobile-open');
        // If the sidebar was hidden on mobile, keep it hidden; otherwise show collapse affordance
        if (sidebar.classList.contains('hidden')) {
            icon.innerHTML = '☰';
        } else if (!sidebar.classList.contains('collapsed')) {
            icon.innerHTML = '☰';
        }
    } else {
        // On small screens we prefer the slide-in mobile sidebar
        sidebar.classList.remove('collapsed');
        // Ensure the hidden state is removed on small screens so mobile-open can function
        sidebar.classList.remove('hidden');
        icon.innerHTML = '☰';
    }
});

// 2FA Management Functions
function load2FAStatus() {
    console.log('Loading 2FA status...');
    // Get current 2FA status from user data
    fetch('/registrar/2fa/status', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        credentials: 'same-origin'
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('2FA Status Response:', data);
        const icon = document.getElementById('2fa-icon');
        const status = document.getElementById('2fa-status');
        const switchInput = document.getElementById('2fa-switch');
        const container = document.getElementById('twofa-container');
        
        if (data.two_factor_enabled) {
            icon.className = 'bi bi-shield-check';
            status.textContent = 'Enabled';
            switchInput.checked = true;
            container.classList.remove('disabled');
            container.classList.add('enabled');
        } else {
            icon.className = 'bi bi-shield-lock';
            status.textContent = 'Disabled';
            switchInput.checked = false;
            container.classList.remove('enabled');
            container.classList.add('disabled');
        }
    })
    .catch(error => {
        console.error('Detailed error loading 2FA status:', error);
        console.error('Error message:', error.message);
        const status = document.getElementById('2fa-status');
        const container = document.getElementById('twofa-container');
        if (status) status.textContent = 'Error: ' + error.message;
        if (container) container.classList.add('disabled');
    });
}

function toggle2FA() {
    const status = document.getElementById('2fa-status');
    const switchInput = document.getElementById('2fa-switch');
    const container = document.getElementById('twofa-container');
    const icon = document.getElementById('2fa-icon');
    
    // Disable the switch temporarily to prevent multiple clicks
    switchInput.disabled = true;
    const originalStatus = status.textContent;
    status.textContent = 'Processing...';
    
    fetch('/registrar/2fa/toggle', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            showNotification(data.message, 'success');
            
            // Update UI based on the new state
            if (data.two_factor_enabled) {
                icon.className = 'bi bi-shield-check';
                status.textContent = 'Enabled';
                switchInput.checked = true;
                container.classList.remove('disabled');
                container.classList.add('enabled');
            } else {
                icon.className = 'bi bi-shield-lock';
                status.textContent = 'Disabled';
                switchInput.checked = false;
                container.classList.remove('enabled');
                container.classList.add('disabled');
            }
        } else {
            // Revert the switch state on error
            switchInput.checked = !switchInput.checked;
            status.textContent = originalStatus;
            showNotification(data.message || 'Failed to update 2FA setting', 'error');
        }
    })
    .catch(error => {
        console.error('Error toggling 2FA:', error);
        // Revert the switch state on error
        switchInput.checked = !switchInput.checked;
        status.textContent = originalStatus;
        showNotification('Failed to update 2FA setting. Please try again.', 'error');
    })
    .finally(() => {
        // Re-enable the switch
        switchInput.disabled = false;
    });
}

function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 8px;
        color: white;
        font-weight: 500;
        z-index: 9999;
        min-width: 300px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        transform: translateX(350px);
        transition: transform 0.3s ease;
    `;
    
    // Set background color based on type
    switch(type) {
        case 'success':
            notification.style.background = 'linear-gradient(135deg, #28a745, #20c997)';
            break;
        case 'error':
            notification.style.background = 'linear-gradient(135deg, #dc3545, #e74c3c)';
            break;
        default:
            notification.style.background = 'linear-gradient(135deg, #17a2b8, #20c997)';
    }
    
    notification.textContent = message;
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // Auto remove after 4 seconds
    setTimeout(() => {
        notification.style.transform = 'translateX(350px)';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 4000);
}

// Initialize proper state on page load
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const icon = document.getElementById('toggle-icon');
    
    if (window.innerWidth <= 768) {
        sidebar.classList.remove('collapsed');
        icon.innerHTML = '☰';
    }
    
    // Load 2FA status
    load2FAStatus();
    
    // Load queue in progress
    loadQueueInProgress();
    
    // Initialize Pusher for real-time updates
    initializeRealTimeUpdates();
});

// Real-time updates for registrar dashboard
function initializeRealTimeUpdates() {
    // Pusher is already initialized at the top of the page
    // This function is kept for future enhancements if needed
}

// Function to update request status in the table
function updateRequestStatus(referenceNo, newStatus) {
    console.log('🔄 updateRequestStatus called with:', referenceNo, newStatus);
    
    // Find the table row with this reference number
    const rows = document.querySelectorAll('tbody tr');
    console.log('🔄 Found', rows.length, 'table rows');
    
    rows.forEach((row, index) => {
        const studentCell = row.querySelector('td:nth-child(2)'); // Student details column
        if (studentCell) {
            console.log('🔄 Row', index, 'student cell text:', studentCell.textContent);
            if (studentCell.textContent.includes(referenceNo)) {
                console.log('🔄 Found matching row for reference:', referenceNo);
                
                // Update the status badge
                const statusCell = row.querySelector('td:nth-child(4)'); // Status column
                if (statusCell) {
                    const badge = statusCell.querySelector('.badge');
                    if (badge) {
                        console.log('🔄 Updating badge from', badge.textContent, 'to', newStatus);
                        
                        // Update badge text
                        badge.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1).replace('_', ' ');
                        
                        // Update badge classes
                        badge.className = 'badge rounded-pill px-3';
                        
                        switch(newStatus) {
                            case 'pending':
                                badge.classList.add('bg-warning', 'text-dark');
                                break;
                            case 'processing':
                                badge.classList.add('bg-info', 'text-white');
                                break;
                            case 'ready_for_release':
                                badge.classList.add('bg-primary', 'text-white');
                                break;
                            case 'completed':
                                badge.classList.add('bg-success', 'text-white');
                                break;
                            default:
                                badge.classList.add('bg-secondary', 'text-white');
                        }
                        
                        // Update the "In Progress" text below the badge
                        const smallText = statusCell.querySelector('small');
                        if (smallText) {
                            smallText.textContent = newStatus === 'completed' ? 'Completed' : 'In Progress';
                        }
                        
                        console.log('🔄 Status update completed');
                    } else {
                        console.log('🔄 No badge found in status cell');
                    }
                } else {
                    console.log('🔄 No status cell found');
                }
            }
        }
    });
}
</script>

</body>
</html>
<?php /**PATH D:\Nu-Regisv2\resources\views/layouts/registrar.blade.php ENDPATH**/ ?>