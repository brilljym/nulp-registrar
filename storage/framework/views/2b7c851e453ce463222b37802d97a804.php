<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $__env->yieldContent('title', 'Accounting Dashboard - NU Lipa'); ?></title>
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
            --nu-gray: #6c757d3c;
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
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .nu-logo-container {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

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
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .sidebar-toggle:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.4);
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
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

        .admin-badge {
            background: var(--nu-yellow);
            color: var(--nu-blue);
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .user-dropdown {
            position: relative;
        }

        .user-dropdown-toggle {
            background: rgba(255, 255, 255, 0.12);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            padding: 0.5rem 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .user-dropdown-toggle:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.4);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .user-dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            background: var(--nu-white);
            border-radius: 10px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
            min-width: 200px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1000;
            margin-top: 0.5rem;
        }

        .user-dropdown-menu.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .user-dropdown-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            color: var(--nu-gray);
            text-decoration: none;
            transition: all 0.3s ease;
            border-radius: 8px;
            margin: 0.25rem;
        }

        .user-dropdown-item:hover {
            background: var(--nu-light-gray);
            color: var(--nu-blue);
            transform: translateX(2px);
        }

        .user-dropdown-item.logout-item {
            color: var(--nu-danger);
        }

        .user-dropdown-item.logout-item:hover {
            background: rgba(220, 53, 69, 0.1);
            color: var(--nu-danger);
        }

        .header-accent {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--nu-yellow) 0%, transparent 50%, var(--nu-yellow) 100%);
        }

        /* Sidebar Backdrop */
        .sidebar-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 1040;
        }

        .sidebar-backdrop.show {
            opacity: 1;
            visibility: visible;
        }

        /* Layout Container */
        .layout-container {
            display: flex;
            min-height: 100vh;
            position: relative;
        }

        /* Enhanced Sidebar */
        .sidebar {
            width: var(--sidebar-width);
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
            margin-top: var(--header-height);
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

        .sidebar-content {
            padding: 2rem 0;
            height: 100%;
            overflow-y: auto;
            overflow-x: hidden;
        }

        /* Custom Scrollbar for Sidebar */
        .sidebar-content::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar-content::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }

        .sidebar-content::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 3px;
        }

        .sidebar-content::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.5);
        }

        /* Navigation Sections */
        .nav-section {
            margin-bottom: 2rem;
        }

        .nav-section-title {
            padding: 0.75rem 2rem;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255, 255, 255, 0.6);
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.875rem 2rem;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            border-radius: 0 25px 25px 0;
            margin: 0.125rem 0.5rem 0.125rem 0;
            font-weight: 500;
        }

        .sidebar a::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 0;
            background: var(--nu-yellow);
            border-radius: 0 25px 25px 0;
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar a:hover {
            color: white;
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(4px);
        }

        .sidebar a:hover::before {
            width: 4px;
        }

        .sidebar a.active {
            color: white;
            background: rgba(255, 255, 255, 0.15);
            box-shadow: inset 0 0 20px rgba(255, 215, 0, 0.2);
        }

        .sidebar a.active::before {
            width: 4px;
            background: var(--nu-yellow);
        }

        .sidebar a i {
            font-size: 1.25rem;
            width: 20px;
            text-align: center;
            flex-shrink: 0;
        }

        .nav-text {
            flex: 1;
            font-size: 0.95rem;
        }

        /* Main Content */
        .main-content-wrapper {
            flex: 1;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            overflow-x: hidden;
            background: var(--nu-light-gray);
            min-height: 100vh;
            margin-top: var(--header-height);
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
            border: none;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                left: -100%;
                top: var(--header-height);
                height: calc(100vh - var(--header-height));
                z-index: 1045;
                border-radius: 0;
            }

            .sidebar.show {
                left: 0;
            }

            .sidebar-toggle {
                display: block !important;
            }

            .main-content-wrapper {
                margin-left: 0;
            }

            .main-content {
                padding: 1rem;
            }

            .nu-header {
                padding: 0.5rem 1rem;
            }

            .nu-title {
                font-size: 1rem;
            }

            .user-dropdown-toggle {
                padding: 0.375rem 0.75rem;
                font-size: 0.875rem;
            }
        }

        @media (max-width: 480px) {
            .sidebar {
                width: 280px;
            }

            .main-content {
                padding: 0.75rem;
            }

            .card-body {
                padding: 1.5rem;
            }
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
            <span class="nu-title">NU LIPA - ACCOUNTING</span>
            <span class="admin-badge">Accounting Panel</span>
        </div>
    </div>
    <div class="d-flex align-items-center gap-3">
        <?php if(auth()->guard()->check()): ?>
            <div class="user-dropdown" id="userDropdown">
                <button class="user-dropdown-toggle" onclick="toggleUserDropdown()">
                    <i class="bi bi-cash-coin"></i>
                    <span><?php echo e(Auth::user()->first_name); ?></span>
                    <i class="bi bi-chevron-down" style="font-size: 12px;"></i>
                </button>
                <div class="user-dropdown-menu">
                    <a href="<?php echo e(route('logout')); ?>" class="user-dropdown-item logout-item">
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
    <div class="header-accent"></div>
</div>

<!-- Sidebar Backdrop for Mobile -->
<div class="sidebar-backdrop" id="sidebarBackdrop"></div>

<!-- Layout with Sidebar -->
<div class="layout-container">
    <!-- Enhanced Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-content">
            <!-- Payment Verification Section -->
            <div class="nav-section">
                <div class="nav-section-title">Payment Management</div>
                <a href="<?php echo e(route('accounting.dashboard')); ?>" class="<?php echo e(request()->is('accounting/dashboard*') ? 'active' : ''); ?>">
                    <i class="bi bi-receipt"></i>
                    <span class="nav-text">Payment Verification & Approval</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content-wrapper">
        <div class="main-content">
            <?php echo $__env->yieldContent('content'); ?>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Sidebar Toggle Functionality
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('sidebarBackdrop');
        const toggleIcon = document.getElementById('toggle-icon');

        if (window.innerWidth <= 768) {
            // Mobile behavior
            sidebar.classList.toggle('show');
            backdrop.classList.toggle('show');
        } else {
            // Desktop behavior
            sidebar.classList.toggle('collapsed');
            if (sidebar.classList.contains('collapsed')) {
                toggleIcon.textContent = '☰';
            } else {
                toggleIcon.textContent = '✕';
            }
        }
    }

    // User Dropdown Functionality
    function toggleUserDropdown() {
        const dropdown = document.getElementById('userDropdown');
        const menu = dropdown.querySelector('.user-dropdown-menu');
        menu.classList.toggle('show');
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('userDropdown');
        const menu = dropdown ? dropdown.querySelector('.user-dropdown-menu') : null;

        if (dropdown && menu && !dropdown.contains(event.target)) {
            menu.classList.remove('show');
        }
    });

    // Close sidebar when clicking backdrop on mobile
    document.getElementById('sidebarBackdrop').addEventListener('click', function() {
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('sidebarBackdrop');

        sidebar.classList.remove('show');
        backdrop.classList.remove('show');
    });

    // Handle window resize
    window.addEventListener('resize', function() {
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('sidebarBackdrop');

        if (window.innerWidth > 768) {
            sidebar.classList.remove('show');
            backdrop.classList.remove('show');
        }
    });

    // Auto-hide sidebar on mobile when clicking a link
    document.querySelectorAll('.sidebar a').forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                const sidebar = document.getElementById('sidebar');
                const backdrop = document.getElementById('sidebarBackdrop');

                sidebar.classList.remove('show');
                backdrop.classList.remove('show');
            }
        });
    });
</script>

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
        console.log('✅ Accounting Pusher connected successfully');
    });

    pusher.connection.bind('error', function(err) {
        console.error('❌ Accounting Pusher connection error:', err);
    });

    pusher.connection.bind('disconnected', function() {
        console.log('⚠️ Accounting Pusher disconnected');
    });

    // Subscribe to accounting notifications channel
    const accountingChannel = pusher.subscribe('accounting-notifications');

    // Subscribe to new onsite requests channel
    const newOnsiteRequestsChannel = pusher.subscribe('new-onsite-requests');

    // Listen for new onsite requests
    accountingChannel.bind('realtime.notification', function(data) {
        console.log('🔄 Accounting received notification:', data);

        // Show notification
        showAccountingNotification(data.message, data.type, data.data);

        // Auto-refresh dashboard to show new request
        if (data.type === 'new-request' || (data.data && data.data.request_type === 'onsite')) {
            setTimeout(() => {
                const currentPage = window.location.pathname;
                if (currentPage.includes('/accounting')) {
                    console.log('🔄 Reloading accounting page:', currentPage);
                    window.location.reload();
                }
            }, 1000);
        }
    });

    // Listen for new onsite requests
    newOnsiteRequestsChannel.bind('realtime.notification', function(data) {
        console.log('📋 Accounting received new onsite request:', data);

        // Show notification with special styling for new requests
        showAccountingNotification(data.message, 'new-onsite-request', data.data);

        // Auto-refresh dashboard to show new request
        setTimeout(() => {
            const currentPage = window.location.pathname;
            if (currentPage.includes('/accounting')) {
                window.location.reload();
            }
        }, 1000);
    });

    // Function to show accounting notifications
    function showAccountingNotification(message, type = 'info', data = {}) {
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

        // Add icon based on type
        let icon = 'bi-info-circle';
        if (type === 'success') icon = 'bi-check-circle';
        if (type === 'warning') icon = 'bi-exclamation-triangle';
        if (type === 'error') icon = 'bi-x-circle';
        if (type === 'new-request' || type === 'new-onsite-request') icon = 'bi-plus-circle';

        notification.innerHTML = `
            <i class="bi ${icon} me-2"></i>
            <strong>${message}</strong>
            ${data.ref_code ? `<br><small>Ref: ${data.ref_code}</small>` : ''}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
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

    // Helper function to get Bootstrap alert class
    function getBootstrapClass(type) {
        const classes = {
            'success': 'success',
            'error': 'danger',
            'warning': 'warning',
            'info': 'info',
            'new-request': 'primary',
            'new-onsite-request': 'primary',
            'payment-approved': 'success'
        };
        return classes[type] || 'info';
    }
</script>

</body>
</html>
<?php /**PATH D:\Nu-Regisv2\resources\views/layouts/accounting.blade.php ENDPATH**/ ?>