<?php $__env->startSection('content'); ?>
<style>
    /* Header bar styling to match screenshot */
    .navbar, .header-bar, .admin-header {
        background-color: #2c3192 !important;
        color: #ffd600 !important;
    }
    .navbar .navbar-brand, .header-bar .navbar-brand, .admin-header .navbar-brand {
        color: #ffd600 !important;
    }
    .navbar .nav-link, .header-bar .nav-link, .admin-header .nav-link {
        color: #fff !important;
    }
    .navbar .nav-link.logout, .header-bar .nav-link.logout, .admin-header .nav-link.logout {
        border: 1px solid #ffd600;
        color: #ffd600 !important;
        background: transparent;
    }
    .navbar .nav-link.logout:hover, .header-bar .nav-link.logout:hover, .admin-header .nav-link.logout:hover {
        background: #ffd600;
        color: #2c3192 !important;
    }

    /* Professional table styling */
    .table {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border-radius: 0.375rem;
        overflow: hidden;
        border: none;
    }
    
    .table thead th {
        background: linear-gradient(135deg, #2c3192 0%, #1e2570 100%);
        color: #fff;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        border: none;
        padding: 1rem 0.75rem;
    }
    
    .table-row {
        transition: all 0.2s ease-in-out;
    }
    
    .table-row:hover {
        background-color: #f8f9fa;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .table tbody td {
        padding: 1rem 0.75rem;
        vertical-align: middle;
        border-top: 1px solid #e9ecef;
    }
    
    .action-btn {
        border-radius: 50%;
        width: 35px;
        height: 35px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease-in-out;
        border: 1.5px solid;
    }
    
    .action-btn:hover {
        transform: scale(1.1);
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }
    
    .btn-outline-warning.action-btn {
        color: #f57c00;
        border-color: #f57c00;
    }
    
    .btn-outline-warning.action-btn:hover {
        background-color: #f57c00;
        border-color: #f57c00;
        color: #fff;
    }
    
    .btn-outline-danger.action-btn {
        color: #dc3545;
        border-color: #dc3545;
    }
    
    .btn-outline-danger.action-btn:hover {
        background-color: #dc3545;
        border-color: #dc3545;
        color: #fff;
    }
    
    .badge.bg-primary {
        background: linear-gradient(135deg, #2c3192 0%, #1e2570 100%) !important;
        font-weight: 500;
        font-size: 0.75rem;
    }
</style>
<div class="container mt-5">
    <h2 class="mb-4">User Management</h2>

    <!-- Add User Button -->
    <button type="button" class="btn btn-primary mb-3 add-user-btn" data-bs-toggle="modal" data-bs-target="#createUserModal">
        + Add New User
    </button>
    <style>
        .add-user-btn {
            background-color: #2c3192;
            border-color:rgb(0, 0, 0);
            color:rgb(255, 255, 255);
        }
        .add-user-btn:hover, .add-user-btn:focus {
            background-color:rgb(0, 0, 0);
            color: #2c3192;
            border-color: #2c3192;
        }
    </style>

    <!-- Flash Success Message -->
    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <!-- Validation Errors -->
    <?php if($errors->any()): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- User Table -->
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th class="text-center" style="width: 5%;">#</th>
                    <th>Full Name</th>
                    <th>School Email</th>
                    <th>Personal Email</th>
                    <th class="text-center" style="width: 10%;">Role</th>
                    <th class="text-center" style="width: 10%;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="table-row">
                    <td class="text-center fw-bold text-muted"><?php echo e($users->firstItem() + $index); ?></td>
                    <td class="fw-semibold"><?php echo e($user->last_name); ?>, <?php echo e($user->first_name); ?> <?php echo e($user->middle_name); ?></td>
                    <td class="text-primary"><?php echo e($user->school_email); ?></td>
                    <td class="text-muted"><?php echo e($user->personal_email); ?></td>
                    <td class="text-center">
                        <span class="badge bg-primary rounded-pill px-3"><?php echo e(strtoupper($user->role->name ?? 'N/A')); ?></span>
                    </td>
                    <td class="text-center">
                        <!-- Edit Icon Button -->
                        <button type="button" class="btn btn-outline-warning btn-sm me-2 action-btn edit-btn"
                            data-bs-toggle="modal"
                            data-bs-target="#editUserModal<?php echo e($user->id); ?>"
                            data-tippy-content="<span class='tooltip-icon'>✏️</span> Edit User">
                            <i class="fas fa-edit"></i>
                        </button>

                        <!-- Delete Form with Icon -->
                        <form method="POST" action="<?php echo e(route('admin.users.destroy', $user->id)); ?>" style="display:inline-block;" class="delete-form">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" 
                                class="btn btn-outline-danger btn-sm action-btn delete-btn"
                                data-tippy-content="<span class='tooltip-icon'>🗑️</span> Delete User">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>

                <!-- Edit Modal for each user -->
                <?php echo $__env->make('admin.users.modals.edit', ['user' => $user], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>

    <!-- Laravel Pagination (clean & compact) -->
    <div class="d-flex justify-content-between align-items-center mt-4">
        <div class="pagination-info">
            <small class="text-muted">
                Showing <?php echo e($users->firstItem()); ?> to <?php echo e($users->lastItem()); ?> of <?php echo e($users->total()); ?> users
            </small>
        </div>
        <div class="pagination-wrapper">
            <?php echo e($users->links('vendor.pagination.bootstrap-5')); ?>

        </div>
    </div>
</div>

<!-- Create User Modal -->
<?php echo $__env->make('admin.users.modals.create', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<!-- Success Modal -->
<?php echo $__env->make('admin.users.modals.success', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<!-- Delete Confirmation Modal -->
<?php echo $__env->make('admin.users.modals.delete', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<!-- FontAwesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<!-- Tippy.js CDN -->
<script src="https://unpkg.com/@popperjs/core@2"></script>
<script src="https://unpkg.com/tippy.js@6"></script>

<!-- User Management JavaScript -->
<script src="<?php echo e(asset('js/user-management.js')); ?>"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Tippy.js tooltips for edit buttons
    tippy('.edit-btn', {
        allowHTML: true,
        theme: 'light-border',
        placement: 'top',
        arrow: true,
        animation: 'fade',
        duration: [200, 150]
    });

    // Initialize Tippy.js tooltips for delete buttons
    tippy('.delete-btn', {
        allowHTML: true,
        theme: 'light-border',
        placement: 'top',
        arrow: true,
        animation: 'fade',
        duration: [200, 150]
    });
});
</script>

<style>
/* Custom Tippy.js theme styling */
.tippy-box[data-theme~='light-border'] {
    background-color: #fff;
    border: 1px solid #e9ecef;
    color: #333;
    box-shadow: 0 4px 14px 0 rgba(0,0,0,.1);
    font-size: 0.875rem;
    font-weight: 500;
}

.tippy-box[data-theme~='light-border'][data-placement^='top'] > .tippy-arrow::before {
    border-top-color: #e9ecef;
}

.tippy-box[data-theme~='light-border'] .tippy-content {
    padding: 8px 12px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.tippy-box[data-theme~='light-border'] i {
    color: #6c757d;
}

.tooltip-icon {
    font-size: 1rem;
    margin-right: 4px;
}

/* Enhanced pagination styling */
.pagination-wrapper .pagination {
    margin: 0;
    gap: 2px;
}

.pagination-wrapper .page-link {
    border: 1px solid #dee2e6;
    color: #2c3192;
    padding: 0.5rem 0.75rem;
    border-radius: 0.375rem !important;
    font-size: 0.875rem;
    font-weight: 500;
    transition: all 0.2s ease-in-out;
    margin: 0 1px;
}

.pagination-wrapper .page-link:hover {
    background-color: #2c3192;
    border-color: #2c3192;
    color: #fff;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(44, 49, 146, 0.2);
}

.pagination-wrapper .page-item.active .page-link {
    background: linear-gradient(135deg, #2c3192 0%, #1e2570 100%);
    border-color: #2c3192;
    color: #fff;
    box-shadow: 0 2px 4px rgba(44, 49, 146, 0.3);
}

.pagination-wrapper .page-item.disabled .page-link {
    color: #6c757d;
    background-color: #f8f9fa;
    border-color: #dee2e6;
}

.pagination-info {
    color: #6c757d;
    font-size: 0.875rem;
    font-weight: 500;
}

@media (max-width: 768px) {
    .d-flex.justify-content-between {
        flex-direction: column;
        gap: 1rem;
        align-items: center !important;
    }
    
    .pagination-info {
        order: 2;
    }
    
    .pagination-wrapper {
        order: 1;
    }
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Nu-Regisv2\resources\views/admin/users/index.blade.php ENDPATH**/ ?>