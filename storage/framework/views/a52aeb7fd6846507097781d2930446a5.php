<?php $__env->startSection('content'); ?>
<div class="container mt-5">
    <h3 class="mb-4">
        <i class="bi bi-check-circle-fill text-success me-2"></i>
        Your Completed Documents
    </h3>
    
    <div class="alert alert-light border-start border-4 border-info">
        <div class="d-flex align-items-center">
            <i class="bi bi-info-circle text-info me-2"></i>
            <div>
                <strong>Note:</strong> This section shows only your completed document requests. 
                To track pending or active requests, please visit <a href="<?php echo e(route('student.my-requests')); ?>" class="text-decoration-none fw-bold">My Requests</a> page, 
                or <a href="<?php echo e(route('student.request.document')); ?>" class="text-decoration-none">submit a new request</a>.
            </div>
        </div>
    </div>

    <?php if($completedRequests->isEmpty()): ?>
        <div class="alert alert-info">
            <i class="bi bi-inbox me-2"></i>
            No completed documents yet. Your completed document requests will appear here.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered align-middle text-center">
                <thead class="table-success">
                    <tr>
                        <th>#</th>
                        <th>Reference #</th>
                        <th>Document Type</th>
                        <th>Total Cost</th>
                        <th>Status</th>
                        <th>Requested On</th>
                        <th>Release Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $completedRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="text-center fw-bold text-muted"><?php echo e($index + 1); ?></td>
                            <td class="fw-bold text-primary"><?php echo e($request->reference_no); ?></td>
                            <td class="text-start">
                                <?php if($request->requestItems->count() > 0): ?>
                                    <?php $__currentLoopData = $request->requestItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="mb-1"><?php echo e($item->document->type_document ?? 'Unknown'); ?> (x<?php echo e($item->quantity); ?>)</div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <div class="text-muted small mt-2">Total: ₱<?php echo e(number_format($request->total_cost, 2)); ?></div>
                                <?php endif; ?>
                            </td>
                            <td>₱<?php echo e(number_format($request->total_cost, 2)); ?></td>
                            <td>
                                <span class="badge bg-success">
                                    <?php echo e(ucfirst($request->status)); ?>

                                </span>
                            </td>
                            <td><?php echo e(\Carbon\Carbon::parse($request->created_at)->format('M j, Y')); ?></td>
                            <td><?php echo e($request->expected_release_date ? \Carbon\Carbon::parse($request->expected_release_date)->format('M j, Y') : 'N/A'); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Nu-Regisv2\resources\views/dashboard/student.blade.php ENDPATH**/ ?>