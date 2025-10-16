

<?php $__env->startSection('title', 'Accounting Dashboard - NU Lipa'); ?>

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
        border-radius: 6px;
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.2s ease-in-out;
        border: 1.5px solid;
        margin: 0 0.25rem;
    }
    
    .action-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }
    
    .btn-success.action-btn {
        background-color: #198754;
        border-color: #198754;
        color: #fff;
    }
    
    .btn-success.action-btn:hover {
        background-color: #157347;
        border-color: #146c43;
    }
    
    .btn-warning.action-btn {
        background-color: #ffc107;
        border-color: #ffc107;
        color: #000;
    }
    
    .btn-warning.action-btn:hover {
        background-color: #ffca2c;
        border-color: #ffc720;
    }
    
    .btn-primary.action-btn {
        background-color: #0d6efd;
        border-color: #0d6efd;
        color: #fff;
    }
    
    .btn-primary.action-btn:hover {
        background-color: #0b5ed7;
        border-color: #0a58ca;
    }
    
    .badge.bg-primary {
        background: linear-gradient(135deg, #2c3192 0%, #1e2570 100%) !important;
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
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-gray-800"><i class="bi bi-cash-coin me-2"></i>Accounting Dashboard</h1>
                    <p class="text-muted mb-0">Payment Verification & Approval</p>
                </div>
            </div>

            <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo e(session('success')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if(session('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo e(session('error')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="bi bi-receipt me-2"></i>Pending Payment Approvals</h6>
                </div>
                <div class="card-body">
                    <?php if($pendingRequests->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Reference Code</th>
                                        <th>Student</th>
                                        <th>Documents</th>
                                        <th>Total Amount</th>
                                        <th>Receipt</th>
                                        <th>Uploaded</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $pendingRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr class="table-row">
                                            <td>
                                                <strong><?php echo e($request->ref_code ?? $request->reference_no); ?></strong>
                                                <br><small class="text-muted"><?php echo e($request->queue_number ?? ''); ?></small>
                                                <?php if($request instanceof \App\Models\StudentRequest): ?>
                                                    <br><small class="badge bg-info">📄 Document Request</small>
                                                <?php else: ?>
                                                    <br><small class="badge bg-primary">🏢 Onsite Request</small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if($request instanceof \App\Models\StudentRequest): ?>
                                                    <?php echo e($request->student->user->first_name); ?> <?php echo e($request->student->user->last_name); ?>

                                                    <br><small class="text-muted"><?php echo e($request->student->student_id); ?></small>
                                                    <br><small class="text-info">📋 Student Document Service</small>
                                                <?php else: ?>
                                                    <?php echo e($request->full_name); ?>

                                                    <br><small class="text-muted"><?php echo e($request->course); ?> - <?php echo e($request->year_level); ?></small>
                                                    <br><small class="text-primary">🏪 Onsite Service Counter</small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php $__currentLoopData = $request->requestItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <div>• <?php echo e($item->document->type_document); ?> (x<?php echo e($item->quantity); ?>)</div>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </td>
                                            <td>
                                                ₱<?php echo e(number_format($request->requestItems->sum(function($item) {
                                                    return $item->document->price * $item->quantity;
                                                }), 2)); ?>

                                            </td>
                                            <td>
                                                <?php if($request instanceof \App\Models\StudentRequest): ?>
                                                    <img src="<?php echo e(route('accounting.receipt.student', $request)); ?>"
                                                         alt="Payment Receipt"
                                                         class="img-thumbnail"
                                                         style="max-width: 80px; max-height: 80px; cursor: pointer;"
                                                         onclick="openReceiptModal('<?php echo e(route('accounting.receipt.student', $request)); ?>', '<?php echo e($request->reference_no); ?>')">
                                                <?php else: ?>
                                                    <img src="<?php echo e(route('accounting.receipt.onsite', $request)); ?>"
                                                         alt="Payment Receipt"
                                                         class="img-thumbnail"
                                                         style="max-width: 80px; max-height: 80px; cursor: pointer;"
                                                         onclick="openReceiptModal('<?php echo e(route('accounting.receipt.onsite', $request)); ?>', '<?php echo e($request->reference_no ?? $request->id); ?>')">
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo e($request->updated_at->diffForHumans()); ?></td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <?php if($request instanceof \App\Models\StudentRequest): ?>
                                                        <form method="POST" action="<?php echo e(route('accounting.approve.student', $request)); ?>" class="d-inline">
                                                            <?php echo csrf_field(); ?>
                                                            <button type="submit" class="btn btn-success btn-sm action-btn" onclick="return confirm('Approve this payment?')">
                                                                <i class="bi bi-check-circle"></i> Approve
                                                            </button>
                                                        </form>
                                                        <form method="POST" action="<?php echo e(route('accounting.reject.student', $request)); ?>" class="d-inline">
                                                            <?php echo csrf_field(); ?>
                                                            <button type="submit" class="btn btn-danger btn-sm action-btn" onclick="return confirm('Reject this payment?')">
                                                                <i class="bi bi-x-circle"></i> Reject
                                                            </button>
                                                        </form>
                                                    <?php else: ?>
                                                        <form method="POST" action="<?php echo e(route('accounting.approve.onsite', $request)); ?>" class="d-inline">
                                                            <?php echo csrf_field(); ?>
                                                            <button type="submit" class="btn btn-success btn-sm action-btn" onclick="return confirm('Approve this payment?')">
                                                                <i class="bi bi-check-circle"></i> Approve
                                                            </button>
                                                        </form>
                                                        <form method="POST" action="<?php echo e(route('accounting.reject.onsite', $request)); ?>" class="d-inline">
                                                            <?php echo csrf_field(); ?>
                                                            <button type="submit" class="btn btn-danger btn-sm action-btn" onclick="return confirm('Reject this payment?')">
                                                                <i class="bi bi-x-circle"></i> Reject
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-check-circle-fill text-success" style="font-size: 3rem;"></i>
                            <h4 class="mt-3">No Pending Approvals</h4>
                            <p class="text-muted">All payment receipts have been processed.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Receipt Modal -->
<div class="modal fade" id="receiptModal" tabindex="-1" aria-labelledby="receiptModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="receiptModalLabel">Payment Receipt</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="receiptImage" src="" alt="Payment Receipt" class="img-fluid" style="max-height: 70vh;">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Auto-refresh the page every 30 seconds to check for new requests
    setTimeout(function() {
        location.reload();
    }, 30000);

    // Function to open receipt modal
    function openReceiptModal(imageSrc, reference) {
        document.getElementById('receiptImage').src = imageSrc;
        document.getElementById('receiptModalLabel').textContent = 'Payment Receipt - ' + reference;
        new bootstrap.Modal(document.getElementById('receiptModal')).show();
    }
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.accounting', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Nu-Regisv2\resources\views\accounting\dashboard.blade.php ENDPATH**/ ?>