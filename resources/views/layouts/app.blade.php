<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard - NU Registrar</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        /* Global Styles */
        * {
            box-sizing: border-box;
        }
        
        body { 
            padding-top: 0; 
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            line-height: 1.5;
        }
        
        .layout-container {
            display: flex;
            min-height: 100vh;
            position: relative;
        }
        
        /* Enhanced Sidebar */
        .sidebar {
            width: 280px;
            min-height: 100vh;
            background: linear-gradient(145deg, #2c2f92 0%, #1e2461 50%, #002f5f 100%);
            color: white;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 0 16px 16px 0;
            box-shadow: 
                2px 0 20px rgba(0, 0, 0, 0.15),
                inset -1px 0 0 rgba(255, 255, 255, 0.1);
            flex-shrink: 0;
            position: relative;
            overflow: hidden;
        }
        
        .sidebar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 20% 20%, rgba(255, 255, 255, 0.05) 0%, transparent 50%);
            pointer-events: none;
        }
        
        .sidebar.collapsed {
            width: 0;
            overflow: hidden;
        }
        
        .main-content-wrapper {
            flex: 1;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            overflow-x: hidden;
            background: #f8f9fa;
            min-height: 100vh;
        }
        
        /* Enhanced Sidebar Toggle */
        .sidebar-toggle {
            background: rgba(255, 255, 255, 0.12);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            margin-right: 16px;
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        .sidebar-toggle:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.4);
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }
        
        .sidebar-toggle:active {
            transform: scale(0.95);
        }
        
        /* Enhanced Navigation Links */
        .sidebar .px-2 {
            padding: 1rem 0.75rem;
        }
        
        .sidebar a {
            color: rgba(255, 255, 255, 0.9);
            padding: 16px 24px;
            display: flex;
            align-items: center;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 0 30px 30px 0;
            margin: 4px 0;
            position: relative;
            font-weight: 500;
            font-size: 0.95rem;
            letter-spacing: 0.3px;
            overflow: hidden;
        }
        
        .sidebar a::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.05);
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }
        
        .sidebar a:hover::before {
            transform: translateX(0);
        }
        
        .sidebar a i {
            width: 24px;
            text-align: center;
            margin-right: 16px;
            font-size: 18px;
            transition: all 0.3s ease;
            position: relative;
            z-index: 1;
        }
        
        .sidebar.collapsed a i {
            margin-right: 0;
        }
        
        .sidebar a .nav-text {
            transition: all 0.3s ease;
            position: relative;
            z-index: 1;
        }
        
        .sidebar.collapsed a .nav-text {
            opacity: 0;
            width: 0;
            overflow: hidden;
        }
        
        .sidebar a.active {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.2) 0%, rgba(255, 255, 255, 0.1) 100%);
            color: white;
            font-weight: 600;
            box-shadow: 
                0 4px 16px rgba(0, 0, 0, 0.2),
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .sidebar a:hover:not(.active) {
            background: rgba(255, 255, 255, 0.12);
            color: white;
            transform: translateX(8px);
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.15);
        }
        
        .sidebar a.active::after {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 24px;
            background: linear-gradient(180deg, #ffd600 0%, #ffed4a 100%);
            border-radius: 0 2px 2px 0;
            box-shadow: 0 0 8px rgba(255, 214, 0, 0.5);
        }
        
        /* Enhanced Header */
        .nu-header {
            background: linear-gradient(135deg, #2c2f92 0%, #1e2461 100%);
            color: #fff;
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-family: 'Segoe UI', Arial, sans-serif;
            font-weight: 600;
            font-size: 1.3rem;
            position: relative;
            z-index: 1100; /* ensure header is above page content */
            box-shadow: 0 2px 20px rgba(44, 47, 146, 0.3);
            backdrop-filter: blur(10px);
        }
        
        .nu-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
            pointer-events: none;
        }
        
        .nu-header .nu-title {
            font-size: 1.6rem;
            font-weight: 700;
            letter-spacing: 1.2px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
            position: relative;
            z-index: 1;
        }
        
        .nu-header .nu-welcome {
            font-size: 1.1rem;
            font-weight: 400;
            opacity: 0.95;
            position: relative;
            z-index: 1;
        }
        
        .nu-header .nu-welcome b {
            font-weight: 700;
        }
        
        /* Enhanced User Dropdown */
        .user-dropdown {
            position: relative;
            z-index: 1200; /* keep dropdown container above header elements */
        }
        
        .user-dropdown-toggle {
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            color: white;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-size: 0.95rem;
            font-weight: 500;
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1);
        }
        
        .user-dropdown-toggle:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.4);
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }
        
        .user-dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border-radius: 12px;
            box-shadow: 
                0 8px 32px rgba(0, 0, 0, 0.15),
                0 0 0 1px rgba(0, 0, 0, 0.05);
            min-width: 220px;
            z-index: 1210; /* ensure dropdown menu is above header and page */
            opacity: 0;
            visibility: hidden;
            transform: translateY(-15px) scale(0.95);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            margin-top: 12px;
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            pointer-events: none; /* disabled until visible */
        }
        
        .user-dropdown.show .user-dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0) scale(1);
            pointer-events: auto; /* allow clicks when shown */
        }
        
        .user-dropdown-menu::before {
            content: '';
            position: absolute;
            top: -8px;
            right: 24px;
            width: 16px;
            height: 16px;
            background: white;
            transform: rotate(45deg);
            border-radius: 2px;
            box-shadow: -2px -2px 8px rgba(0, 0, 0, 0.1);
            z-index: 1220; /* ensure caret is above other elements */
        }
        
        .user-dropdown-item {
            display: flex;
            align-items: center;
            padding: 16px 20px;
            color: #000000;
            text-decoration: none;
            transition: all 0.2s ease;
            font-size: 0.95rem;
            font-weight: 500;
            gap: 12px;
            position: relative;
            background: none;
            border: none;
            width: 100%;
            text-align: left;
            cursor: pointer;
        }
        
        .user-dropdown-item.logout-item {
            color: #000000 !important;
            font-weight: 600;
        }
        
        .user-dropdown-item.logout-item span {
            color: #000000 !important;
        }
        
        .user-dropdown-item.logout-item:hover {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            color: #000000 !important;
            transform: translateX(4px);
        }
        
        .user-dropdown-item.logout-item:hover span {
            color: #000000 !important;
        }
        
        .user-dropdown-item:first-child {
            border-radius: 12px 12px 0 0;
        }
        
        .user-dropdown-item:last-child {
            border-radius: 0 0 12px 12px;
        }
        
        .user-dropdown-item:hover {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            color: #2c2f92;
            transform: translateX(4px);
        }
        
        .user-dropdown-item i {
            width: 18px;
            text-align: center;
            font-size: 16px;
        }
        
        .dropdown-divider {
            height: 1px;
            background: linear-gradient(90deg, transparent 0%, #e9ecef 50%, transparent 100%);
            margin: 8px 0;
        }
        
        /* Enhanced Header Bar */
        .nu-header-bar {
            height: 5px;
            background: linear-gradient(90deg, #ffd600 0%, #ffed4a 50%, #ffd600 100%);
            width: 100%;
            box-shadow: 0 2px 8px rgba(255, 214, 0, 0.3);
            position: relative;
            z-index: 1099; /* below header but below dropdown menu */
        }
        
        .nu-logo-container {
            display: flex;
            align-items: center;
            gap: 12px;
            position: relative;
            z-index: 1;
        }
        
        .nu-shield {
            height: 2rem;
            width: auto;
            vertical-align: middle;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
            transition: transform 0.3s ease;
        }
        
        .nu-shield:hover {
            transform: scale(1.05);
        }
        
        /* Enhanced Content Area */
        .container-fluid {
            background: #f8f9fa;
            min-height: calc(100vh - 100px);
            padding: 2rem;
        }
        
        /* Enhanced Responsive Design */
        @media (max-width: 768px) {
            .nu-header {
                padding: 1rem 1.5rem;
                font-size: 1.1rem;
            }
            
            .nu-header .nu-title {
                font-size: 1.4rem;
            }
            
            .sidebar {
                width: 260px;
            }
            
            .container-fluid {
                padding: 1.5rem 1rem;
            }
        }
        
        @media (max-width: 576px) {
            .nu-header {
                padding: 0.75rem 1rem;
            }
            
            .nu-header .nu-title {
                font-size: 1.2rem;
                letter-spacing: 0.8px;
            }
            
            .sidebar-toggle {
                width: 36px;
                height: 36px;
                font-size: 16px;
                margin-right: 12px;
            }
            
            .user-dropdown-toggle {
                padding: 10px 16px;
                font-size: 0.9rem;
            }
            
            .container-fluid {
                padding: 1rem 0.75rem;
            }
        }
        
        /* Smooth Scrollbar */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }
        
        .sidebar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 3px;
        }
        
        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 3px;
        }
        
        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.5);
        }
        
        /* Loading and Animation States */
        .sidebar a,
        .user-dropdown-toggle,
        .sidebar-toggle {
            backface-visibility: hidden;
            transform: translateZ(0);
        }
        
        /* Focus States for Accessibility */
        .sidebar a:focus,
        .user-dropdown-toggle:focus,
        .sidebar-toggle:focus {
            outline: 2px solid rgba(255, 214, 0, 0.8);
            outline-offset: 2px;
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

<!-- NU Header -->
<div class="nu-header">
    <div class="nu-logo-container">
        <button class="sidebar-toggle" onclick="toggleSidebar()">
            <span id="toggle-icon">☰</span>
        </button>
        <img src="{{ asset('images/NU_shield.svg.png') }}" alt="NU Shield" class="nu-shield">
        <span class="nu-title">NU LIPA - STUDENT</span>
    </div>
    @auth
        <div class="user-dropdown" id="userDropdown">
            <button class="user-dropdown-toggle" onclick="toggleUserDropdown(event)" aria-expanded="false">
                <i class="bi bi-person-circle"></i>
                <span>{{ Auth::user()->first_name }}</span>
                <i class="bi bi-chevron-down" style="font-size: 12px;"></i>
            </button>
            <div class="user-dropdown-menu">
                @if(Auth::user()->role && Auth::user()->role->name === 'student')
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
                @endif
                <form action="{{ route('logout.post') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="user-dropdown-item logout-item">
                        <i class="bi bi-box-arrow-right"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </div>
    @else
        <span class="nu-welcome">Welcome to <b>NU Lipa Student</b></span>
    @endauth
</div>
<div class="nu-header-bar"></div>

<!-- Layout with Sidebar -->
<div class="layout-container">
    <!-- Sidebar -->
    <div class="sidebar pt-0" id="sidebar">
        <div class="px-2">
            <a href="{{ route('student.dashboard') }}" class="{{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i>
                <span class="nav-text">Dashboard</span>
            </a>
            <a href="{{ route('student.request.document') }}" class="{{ request()->routeIs('student.request.document') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-text"></i>
                <span class="nav-text">Request Documents</span>
            </a>
            <a href="{{ route('student.my-requests') }}" class="{{ request()->routeIs('student.my-requests', 'student.track') ? 'active' : '' }}">
                <i class="bi bi-search"></i>
                <span class="nav-text">Track Requests</span>
            </a>
            <a href="{{ route('student.profile') }}" class="{{ request()->routeIs('student.profile') ? 'active' : '' }}">
                <i class="bi bi-person-circle"></i>
                <span class="nav-text">Profile</span>
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content-wrapper">
        <div class="container-fluid py-4">
            @yield('content')
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Enhanced sidebar toggle with smooth animations
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const toggleIcon = document.getElementById('toggle-icon');
    
    sidebar.classList.toggle('collapsed');
    
    // Animate the toggle icon
    if (sidebar.classList.contains('collapsed')) {
        toggleIcon.style.transform = 'rotate(180deg)';
    } else {
        toggleIcon.style.transform = 'rotate(0deg)';
    }
    
    // Add a subtle animation effect
    toggleIcon.style.transition = 'transform 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
}

// Enhanced user dropdown with better UX
function toggleUserDropdown(event) {
    // prevent the document click handler from immediately closing the menu
    if (event && event.stopPropagation) event.stopPropagation();

    const dropdown = document.getElementById('userDropdown');
    const toggleBtn = dropdown ? dropdown.querySelector('.user-dropdown-toggle') : null;
    const isOpen = dropdown.classList.contains('show');

    if (isOpen) {
        dropdown.classList.remove('show');
        if (toggleBtn) toggleBtn.setAttribute('aria-expanded', 'false');
    } else {
        // Close any other open dropdowns first
        document.querySelectorAll('.user-dropdown.show').forEach(dd => {
            if (dd !== dropdown) {
                dd.classList.remove('show');
                const btn = dd.querySelector('.user-dropdown-toggle');
                if (btn) btn.setAttribute('aria-expanded', 'false');
            }
        });
        dropdown.classList.add('show');
        if (toggleBtn) toggleBtn.setAttribute('aria-expanded', 'true');
        // focus the first interactive element inside the menu
        const firstFocusable = dropdown.querySelector('button, a, input, [tabindex]');
        if (firstFocusable) firstFocusable.focus();
    }
}

// Enhanced click outside handler
document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('userDropdown');
    if (dropdown && !dropdown.contains(event.target)) {
        dropdown.classList.remove('show');
        const btn = dropdown.querySelector('.user-dropdown-toggle');
        if (btn) btn.setAttribute('aria-expanded', 'false');
    }
});

// Prevent clicks inside the dropdown menu from bubbling up to document
document.addEventListener('DOMContentLoaded', function() {
    const dropdownMenu = document.querySelector('.user-dropdown-menu');
    if (dropdownMenu) {
        dropdownMenu.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }
});

// Keyboard navigation support
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        // Close dropdown on Escape key
        const dropdown = document.getElementById('userDropdown');
        if (dropdown) {
            dropdown.classList.remove('show');
        }
    }
    
    // Toggle sidebar with Ctrl+B
    if (event.ctrlKey && event.key === 'b') {
        event.preventDefault();
        toggleSidebar();
    }
});

// Enhanced responsive behavior
window.addEventListener('resize', function() {
    const sidebar = document.getElementById('sidebar');
    const dropdown = document.getElementById('userDropdown');
    
    // Auto-collapse sidebar on small screens
    if (window.innerWidth <= 768) {
        sidebar.classList.add('collapsed');
    }
    
    // Close dropdown on resize
    if (dropdown) {
        dropdown.classList.remove('show');
    }
});

// Smooth scroll to top functionality
function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

// Add loading states for better UX
document.addEventListener('DOMContentLoaded', function() {
    // Add fade-in animation to main content
    const mainContent = document.querySelector('.main-content-wrapper');
    if (mainContent) {
        mainContent.style.opacity = '0';
        mainContent.style.transform = 'translateY(20px)';
        mainContent.style.transition = 'all 0.4s ease';
        
        setTimeout(() => {
            mainContent.style.opacity = '1';
            mainContent.style.transform = 'translateY(0)';
        }, 100);
    }
    
    // Add stagger animation to sidebar links
    const sidebarLinks = document.querySelectorAll('.sidebar a');
    sidebarLinks.forEach((link, index) => {
        link.style.opacity = '0';
        link.style.transform = 'translateX(-20px)';
        link.style.transition = 'all 0.3s ease';
        
        setTimeout(() => {
            link.style.opacity = '1';
            link.style.transform = 'translateX(0)';
        }, 200 + (index * 100));
    });
});

// Add hover effects for better interactivity
document.addEventListener('DOMContentLoaded', function() {
    // Enhance sidebar link interactions
    const sidebarLinks = document.querySelectorAll('.sidebar a:not(.active)');
    sidebarLinks.forEach(link => {
        link.addEventListener('mouseenter', function() {
            this.style.transition = 'all 0.2s ease';
        });
        
        link.addEventListener('mouseleave', function() {
            this.style.transition = 'all 0.3s ease';
        });
    });
    
    // Add ripple effect to buttons
    const buttons = document.querySelectorAll('.sidebar-toggle, .user-dropdown-toggle');
    buttons.forEach(button => {
        button.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.cssText = `
                position: absolute;
                width: ${size}px;
                height: ${size}px;
                left: ${x}px;
                top: ${y}px;
                background: rgba(255, 255, 255, 0.3);
                border-radius: 50%;
                transform: scale(0);
                animation: ripple 0.6s ease-out;
                pointer-events: none;
            `;
            
            this.style.position = 'relative';
            this.style.overflow = 'hidden';
            this.appendChild(ripple);
            
            setTimeout(() => ripple.remove(), 600);
        });
    });
});

// Add CSS for ripple animation
const style = document.createElement('style');
style.textContent = `
    @keyframes ripple {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .fade-in-up {
        animation: fadeInUp 0.6s ease-out forwards;
    }
`;
document.head.appendChild(style);

// 2FA Management Functions
function load2FAStatus() {
    console.log('Loading 2FA status...');
    // Get current 2FA status from user data
    fetch('/student/2fa/status', {
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
    
    fetch('/student/2fa/toggle', {
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

// Load 2FA status when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Add CSRF token meta tag if not exists
    if (!document.querySelector('meta[name="csrf-token"]')) {
        const csrfMeta = document.createElement('meta');
        csrfMeta.name = 'csrf-token';
        csrfMeta.content = '{{ csrf_token() }}';
        document.head.appendChild(csrfMeta);
    }
    
    load2FAStatus();
});
</script>
</body>
</html>
