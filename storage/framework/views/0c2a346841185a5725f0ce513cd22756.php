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
    
    .btn-outline-info.action-btn {
        color: #17a2b8;
        border-color: #17a2b8;
    }
    
    .btn-outline-info.action-btn:hover {
        background-color: #17a2b8;
        border-color: #17a2b8;
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
    
    .badge.bg-success {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
        font-weight: 500;
        font-size: 0.75rem;
    }
    
    .badge.bg-info {
        background: linear-gradient(135deg, #17a2b8 0%, #138496 100%) !important;
        font-weight: 500;
        font-size: 0.75rem;
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

<div class="container mt-5">
    <h2 class="mb-4">Student Management</h2>

    <!-- Student Table -->
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th class="text-center" style="width: 5%;">#</th>
                    <th>Student ID</th>
                    <th>Full Name</th>
                    <th>Department</th>
                    <th>Course</th>
                    <th class="text-center" style="width: 10%;">Year Level</th>
                    <th class="text-center" style="width: 10%;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="table-row">
                    <td class="text-center fw-bold text-muted"><?php echo e($students->firstItem() + $index); ?></td>
                    <td class="text-primary fw-semibold"><?php echo e($student->student_id); ?></td>
                    <td class="fw-semibold">
                        <?php echo e(optional($student->user)->last_name); ?>, <?php echo e(optional($student->user)->first_name); ?> <?php echo e(optional($student->user)->middle_name); ?>

                    </td>
                    <td class="text-secondary">
                        <span class="badge bg-info rounded-pill px-2"><?php echo e(strtoupper($student->department ?? 'N/A')); ?></span>
                    </td>
                    <td class="text-muted fw-medium"><?php echo e($student->course ?? 'N/A'); ?></td>
                    <td class="text-center">
                        <span class="badge bg-success rounded-pill px-3"><?php echo e(strtoupper($student->year_level ?? 'N/A')); ?></span>
                    </td>
                    <td class="text-center">
                        <!-- View Icon Button -->
                        <a href="<?php echo e(route('admin.students.show', $student)); ?>" class="btn btn-outline-info btn-sm me-2 action-btn view-btn"
                            data-tippy-content="<span class='tooltip-icon'>👁️</span> View Student">
                            <i class="fas fa-eye"></i>
                        </a>

                        <!-- Delete Form with Icon -->
                        <form method="POST" action="#" style="display:inline-block;">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" 
                                onclick="return confirm('Are you sure you want to delete this student?')" 
                                class="btn btn-outline-danger btn-sm action-btn delete-btn"
                                data-tippy-content="<span class='tooltip-icon'>🗑️</span> Delete Student">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>

    <!-- Laravel Pagination (clean & compact) -->
    <div class="d-flex justify-content-between align-items-center mt-4">
        <div class="pagination-info">
            <small class="text-muted">
                Showing <?php echo e($students->firstItem()); ?> to <?php echo e($students->lastItem()); ?> of <?php echo e($students->total()); ?> students
            </small>
        </div>
        <div class="pagination-wrapper">
            <?php echo e($students->links('vendor.pagination.bootstrap-5')); ?>

        </div>
    </div>
</div>

<!-- FontAwesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<!-- Tippy.js CDN -->
<script src="https://unpkg.com/@popperjs/core@2"></script>
<script src="https://unpkg.com/tippy.js@6"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Tippy.js tooltips for view buttons
    tippy('.view-btn', {
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
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Nu-Regisv2\resources\views/admin/students/index.blade.php ENDPATH**/ ?>