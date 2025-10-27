<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - NU Lipa</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

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
            height: 2.5rem;
            width: auto;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3));
        }

        .nu-title {
            font-size: 1.3rem;
            font-weight: 700;
            letter-spacing: 1px;
            background: linear-gradient(45deg, var(--nu-white), var(--nu-yellow));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .admin-badge {
            background: linear-gradient(135deg, var(--nu-yellow) 0%, #e6b800 100%);
            color: var(--nu-blue);
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .user-dropdown {
            position: relative;
        }

        .user-dropdown-toggle {
            background: rgba(255, 215, 0, 0.2);
            border: 2px solid rgba(255, 215, 0, 0.3);
            color: var(--nu-white);
            padding: 0.5rem 1rem;
            border-radius: 25px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .user-dropdown-toggle:hover {
            background: rgba(255, 215, 0, 0.3);
            border-color: var(--nu-yellow);
            color: var(--nu-white);
        }

        .user-dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            background: var(--nu-white);
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            min-width: 200px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            z-index: 1000;
            margin-top: 0.5rem;
        }

        .user-dropdown.show .user-dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .user-dropdown-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.875rem 1.25rem;
            color: var(--nu-gray);
            text-decoration: none;
            transition: all 0.3s ease;
            border-bottom: 1px solid #f1f3f4;
        }

        .user-dropdown-item:first-child {
            border-radius: 12px 12px 0 0;
        }

        .user-dropdown-item:last-child {
            border-radius: 0 0 12px 12px;
            border-bottom: none;
        }

        .user-dropdown-item:hover {
            background: var(--nu-light-gray);
            color: var(--nu-blue);
        }

        .header-accent {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--nu-yellow) 0%, var(--nu-blue) 50%, var(--nu-yellow) 100%);
        }

        /* Layout Container */
        .layout-container {
            display: flex;
            min-height: 100vh;
            padding-top: var(--header-height);
        }

        /* Enhanced Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            min-height: calc(100vh - var(--header-height));
            background: linear-gradient(180deg, var(--nu-blue) 0%, #001f5f 100%);
            color: var(--nu-white);
            transition: all 0.3s ease;
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.15);
            flex-shrink: 0;
            position: fixed;
            top: var(--header-height);
            left: 0;
            z-index: 1000;
            overflow-y: auto;
        }

        .sidebar.collapsed {
            width: 80px;
        }

        .sidebar-content {
            padding: 1.5rem 0;
        }

        .nav-section {
            margin-bottom: 2rem;
        }

        .nav-section-title {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.8rem;
            font-weight: 600;
            padding: 0 1.5rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 1rem;
            transition: opacity 0.3s ease;
        }

        .sidebar.collapsed .nav-section-title {
            opacity: 0;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.5rem;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
            font-weight: 500;
            position: relative;
        }

        .sidebar a:hover {
            background: rgba(255, 215, 0, 0.1);
            color: var(--nu-white);
            border-left-color: var(--nu-yellow);
            transform: translateX(5px);
        }

        .sidebar a.active {
            background: rgba(255, 215, 0, 0.2);
            color: var(--nu-white);
            border-left-color: var(--nu-yellow);
        }

        .sidebar a i {
            font-size: 1.2rem;
            width: 24px;
            text-align: center;
            flex-shrink: 0;
        }

        .nav-text {
            transition: opacity 0.3s ease;
            white-space: nowrap;
            overflow: hidden;
        }

        .sidebar.collapsed .nav-text {
            opacity: 0;
            width: 0;
        }

        /* Main Content */
        .main-content-wrapper {
            flex: 1;
            margin-left: var(--sidebar-width);
            transition: all 0.3s ease;
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

        /* Enhanced Cards */
        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            background: var(--nu-white);
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }

        .card-header {
            background: linear-gradient(135deg, var(--nu-blue) 0%, #001f5f 100%);
            color: var(--nu-white);
            border: none;
            padding: 1.5rem;
            font-weight: 600;
        }

        .card-body {
            padding: 2rem;
        }

        /* Admin Stats Cards */
        .admin-stats-card {
            background: linear-gradient(135deg, var(--nu-white) 0%, #f8f9fa 100%);
            border-left: 2px solid var(--nu-blue);
            transition: all 0.3s ease;
        }

        .admin-stats-card:hover {
            border-left-color: var(--nu-yellow);
            transform: translateY(-2px);
        }

        .admin-stats-card .card-body {
            padding: 0.75rem;
        }

        .stats-number {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--nu-blue);
            margin: 0;
            line-height: 1.2;
        }

        .stats-label {
            font-size: 0.75rem;
            color: var(--nu-gray);
            margin: 0;
            font-weight: 500;
            line-height: 1.3;
        }

        .stats-icon {
            font-size: 1.5rem;
            color: var(--nu-blue);
            opacity: 0.6;
        }

        /* Enhanced Tables */
        .table-container {
            background: var(--nu-white);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .table thead th {
            background: linear-gradient(135deg, var(--nu-blue) 0%, #001f5f 100%);
            color: var(--nu-white);
            border: none;
            font-weight: 600;
            padding: 1rem;
        }

        .table tbody tr {
            transition: all 0.3s ease;
        }

        .table tbody tr:hover {
            background-color: rgba(0, 51, 153, 0.05);
            transform: scale(1.001);
        }

        .table tbody td {
            padding: 1rem;
            border-color: #f1f3f4;
        }

        /* Action Buttons */
        .btn-admin {
            background: linear-gradient(135deg, var(--nu-blue) 0%, #001f5f 100%);
            border: none;
            color: var(--nu-white);
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-admin:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 51, 153, 0.3);
            color: var(--nu-white);
        }

        /* Mobile Sidebar Backdrop */
        .sidebar-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .sidebar-backdrop.show {
            opacity: 1;
            visibility: visible;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                width: 280px;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                position: fixed;
                top: var(--header-height);
                left: 0;
                height: calc(100vh - var(--header-height));
                z-index: 1000;
            }

            .sidebar.mobile-open {
                transform: translateX(0);
            }

            .main-content-wrapper {
                margin-left: 0;
            }

            .nu-header {
                padding: 1rem;
            }

            .nu-title {
                font-size: 1.2rem;
            }

            .main-content {
                padding: 1rem;
            }

            /* Mobile sidebar content adjustments */
            .sidebar .nav-section-title {
                font-size: 0.75rem;
                padding: 0 1rem;
                margin-bottom: 0.75rem;
            }
            
            .sidebar a {
                padding: 0.875rem 1rem;
                font-size: 0.9rem;
            }
            
            .sidebar a i {
                font-size: 1.1rem;
                width: 20px;
            }
        }

        /* Custom Scrollbar */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 3px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.5);
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
        <img src="{{ asset('images/NU_shield.svg.png') }}" alt="NU Shield" class="nu-shield">
        <div class="d-flex align-items-center gap-2">
            <span class="nu-title">NU LIPA - ADMIN</span>
            <span class="admin-badge">Admin Panel</span>
        </div>
    </div>
    <div class="d-flex align-items-center gap-3">
        @auth
            <div class="user-dropdown" id="userDropdown">
                <button class="user-dropdown-toggle" onclick="toggleUserDropdown()">
                    <i class="bi bi-shield-fill-check"></i>
                    <span>{{ Auth::user()->first_name }}</span>
                    <i class="bi bi-chevron-down" style="font-size: 12px;"></i>
                </button>
                <div class="user-dropdown-menu">
                    <a href="{{ route('logout') }}" class="user-dropdown-item">
                        <i class="bi bi-box-arrow-right"></i>
                        <span>Logout</span>
                    </a>
                </div>
            </div>
        @else
            <a href="{{ route('logout') }}" class="user-dropdown-toggle">
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
            </a>
        @endauth
    </div>
    <div class="header-accent"></div>
</div>

<!-- Sidebar Backdrop for Mobile -->
<div class="sidebar-backdrop" id="sidebarBackdrop"></div>

<!-- Layout with Sidebar -->
<div class="layout-container">
    <!-- Enhanced Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-content">
            

            <!-- User Management Section -->
            <div class="nav-section">
                <div class="nav-section-title">User Management</div>
                <a href="{{ route('admin.users.index') }}" class="{{ request()->is('admin/users*') && !request()->is('admin/students*') && !request()->is('admin/registrars*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i>
                    <span class="nav-text">All Users</span>
                </a>
                <a href="{{ route('admin.students.index') }}" class="{{ request()->is('admin/students*') ? 'active' : '' }}">
                    <i class="bi bi-mortarboard"></i>
                    <span class="nav-text">Students</span>
                </a>
                <a href="{{ route('admin.registrars.index') }}" class="{{ request()->is('admin/registrars*') ? 'active' : '' }}">
                    <i class="bi bi-person-badge"></i>
                    <span class="nav-text">Registrars</span>
                </a>
            </div>

            <!-- Document Management Section -->
            <div class="nav-section">
                <div class="nav-section-title">Document Management</div>
                <a href="{{ route('admin.documents.index') }}" class="{{ request()->is('admin/documents*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-text"></i>
                    <span class="nav-text">Document Requests</span>
                </a>
            </div>

            <!-- Reports Section -->
            <div class="nav-section">
                <div class="nav-section-title">Reports</div>
                <a href="{{ route('admin.reports') }}" class="{{ request()->is('admin/reports') ? 'active' : '' }}">
                    <i class="bi bi-bar-chart-line"></i>
                    <span class="nav-text">Reports & Analytics</span>
                </a>
            </div>

           
        </div>
    </div>

    <!-- Main Content Wrapper -->
    <div class="main-content-wrapper">
        <!-- Main Content Area -->
        <main class="main-content">
            @yield('content')
        </main>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const icon = document.getElementById('toggle-icon');
    
    sidebar.classList.toggle('collapsed');
    
    if (sidebar.classList.contains('collapsed')) {
        icon.innerHTML = '→';
    } else {
        icon.innerHTML = '☰';
    }
}

function toggleUserDropdown() {
    const dropdown = document.getElementById('userDropdown');
    dropdown.classList.toggle('show');
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('userDropdown');
    if (dropdown && !dropdown.contains(event.target)) {
        dropdown.classList.remove('show');
    }
});

// Handle mobile sidebar
function toggleMobileSidebar() {
    const sidebar = document.getElementById('sidebar');
    const backdrop = document.getElementById('sidebarBackdrop');
    
    if (sidebar && backdrop) {
        sidebar.classList.toggle('mobile-open');
        backdrop.classList.toggle('show');
        
        // Prevent body scroll when sidebar is open
        if (sidebar.classList.contains('mobile-open')) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = '';
        }
    }
}

// Enhanced sidebar toggle function
function toggleSidebar() {
    if (window.innerWidth <= 768) {
        toggleMobileSidebar();
        return;
    }
    
    const sidebar = document.getElementById('sidebar');
    const icon = document.getElementById('toggle-icon');
    
    sidebar.classList.toggle('collapsed');
    
    if (sidebar.classList.contains('collapsed')) {
        icon.innerHTML = '→';
    } else {
        icon.innerHTML = '☰';
    }
}

// Close mobile sidebar when clicking outside or backdrop
document.addEventListener('click', function(event) {
    const sidebar = document.getElementById('sidebar');
    const toggle = document.querySelector('.sidebar-toggle');
    const backdrop = document.getElementById('sidebarBackdrop');
    
    if (window.innerWidth <= 768) {
        if (event.target === backdrop) {
            sidebar.classList.remove('mobile-open');
            backdrop.classList.remove('show');
            document.body.style.overflow = '';
        }
    }
});

// Handle escape key to close mobile sidebar
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape' && window.innerWidth <= 768) {
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('sidebarBackdrop');
        
        if (sidebar.classList.contains('mobile-open')) {
            sidebar.classList.remove('mobile-open');
            backdrop.classList.remove('show');
            document.body.style.overflow = '';
        }
    }
});

// Handle window resize
window.addEventListener('resize', function() {
    const sidebar = document.getElementById('sidebar');
    const backdrop = document.getElementById('sidebarBackdrop');
    
    if (window.innerWidth > 768) {
        sidebar.classList.remove('mobile-open');
        backdrop.classList.remove('show');
        document.body.style.overflow = '';
    }
});
</script>

</body>
</html>
