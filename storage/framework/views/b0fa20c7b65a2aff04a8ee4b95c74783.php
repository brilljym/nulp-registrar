<?php $__env->startSection('content'); ?>
<style>
    /* Professional table styling to match user management */
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

    .window-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.875rem;
        box-shadow: 0 2px 4px rgba(40, 167, 69, 0.3);
    }

    .registrar-id {
        font-weight: 700;
        color: #2c3192;
        font-size: 1.1rem;
    }

    .registrar-name {
        font-weight: 600;
        color: #495057;
    }

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
</style>

<div class="container mt-5">
    <h2 class="mb-4">Registrar Management</h2>

    <!-- Flash Success Message -->
    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <!-- Registrars Table -->
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th class="text-center" style="width: 10%;">ID</th>
                    <th>Registrar Name</th>
                    <th>School Email</th>
                    <th class="text-center" style="width: 15%;">Window Number</th>
                    <th class="text-center" style="width: 15%;">Created</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $registrars; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $registrar): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="table-row">
                    <td class="text-center">
                        <span class="registrar-id">#<?php echo e($registrar->id); ?></span>
                    </td>
                    <td>
                        <?php if($registrar->user): ?>
                            <span class="registrar-name">
                                <?php echo e($registrar->user->last_name); ?>, <?php echo e($registrar->user->first_name); ?> 
                                <?php if($registrar->user->middle_name): ?>
                                    <?php echo e($registrar->user->middle_name); ?>

                                <?php endif; ?>
                            </span>
                        <?php else: ?>
                            <span class="text-muted">No user assigned</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-primary">
                        <?php echo e($registrar->user->school_email ?? 'N/A'); ?>

                    </td>
                    <td class="text-center">
                        <span class="window-badge">
                            <i class="fas fa-window-maximize"></i>
                            Window <?php echo e($registrar->window_number); ?>

                        </span>
                    </td>
                    <td class="text-center text-muted">
                        <?php echo e($registrar->created_at->format('M d, Y')); ?>

                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="5" class="text-center py-4">
                        <div class="text-muted">
                            <i class="fas fa-users fa-2x mb-2"></i>
                            <p>No registrars found.</p>
                        </div>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Laravel Pagination -->
    <div class="d-flex justify-content-between align-items-center mt-4">
        <div class="pagination-info">
            <small class="text-muted">
                <?php if($registrars->total() > 0): ?>
                    Showing <?php echo e($registrars->firstItem()); ?> to <?php echo e($registrars->lastItem()); ?> of <?php echo e($registrars->total()); ?> registrars
                <?php else: ?>
                    No registrars to display
                <?php endif; ?>
            </small>
        </div>
        <div class="pagination-wrapper">
            <?php echo e($registrars->links('vendor.pagination.bootstrap-5')); ?>

        </div>
    </div>
</div>

<!-- FontAwesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Nu-Regisv2\resources\views/admin/registrars/index.blade.php ENDPATH**/ ?>