<?php $__env->startSection('content'); ?>
    <h2>Transaction Details</h2>
    <p><strong>Reference:</strong> <?php echo e($transaction->request_id); ?></p>
    <p><strong>Status:</strong> <?php echo e(ucfirst($transaction->status)); ?></p>
    <!-- other fields -->
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Nu-Regisv2\resources\views\student\transactions\show.blade.php ENDPATH**/ ?>