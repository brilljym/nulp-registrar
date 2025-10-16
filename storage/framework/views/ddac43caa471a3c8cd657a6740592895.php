<?php $__env->startSection('content'); ?>
<div class="bg-white p-4 rounded shadow-sm">
    <h5 class="mb-4"><i class="bi bi-windows text-primary"></i> Window Queue Management</h5>

    <div class="table-responsive">
        <table class="table table-bordered text-center align-middle">
            <thead class="table-primary">
                <tr>
                    <th>Window Name</th>
                    <th>Status</th>
                    <th>Assigned To</th>
                    <th>Student ID</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $windows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $window): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($window->name); ?></td>
                        <td>
                            <?php if($window->is_occupied): ?>
                                <span class="badge bg-danger">Occupied</span>
                            <?php else: ?>
                                <span class="badge bg-success">Available</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo e($window->assignedRequest->full_name ?? '—'); ?></td>
                        <td><?php echo e($window->assignedRequest->student_id ?? '—'); ?></td>
                        <td>
                            <?php if($window->is_occupied): ?>
                                <form action="<?php echo e(route('window.release', $window->id)); ?>" method="POST">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('PATCH'); ?>
                                    <button class="btn btn-sm btn-outline-danger">Release</button>
                                </form>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="5" class="text-muted">No windows found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.registrar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Nu-Regisv2\resources\views\registrar\windows.blade.php ENDPATH**/ ?>