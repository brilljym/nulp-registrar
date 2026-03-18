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

    /* Tab styling */
    .nav-tabs .nav-link {
        color: #2c3192;
        font-weight: 600;
        border: none;
        border-bottom: 3px solid transparent;
        padding: 0.75rem 1.5rem;
        transition: all 0.2s;
    }

    .nav-tabs .nav-link:hover {
        border-bottom-color: #2c3192;
        background: transparent;
    }

    .nav-tabs .nav-link.active {
        color: #2c3192;
        border-bottom: 3px solid #2c3192;
        background: transparent;
    }

    .nav-tabs {
        border-bottom: 2px solid #dee2e6;
    }

    .tab-count {
        background: #2c3192;
        color: #fff;
        font-size: 0.7rem;
        padding: 0.15rem 0.5rem;
        border-radius: 10px;
        margin-left: 0.4rem;
        font-weight: 700;
    }

    .tab-count.onsite { background: #17a2b8; }
    .tab-count.student { background: #6f42c1; }
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-gray-800"><i class="bi bi-cash-coin me-2"></i>Accounting Dashboard</h1>
                    <p class="text-muted mb-0">Payment Verification & Approval</p>
                </div>
                <div class="d-flex gap-2">
                    <span class="badge" style="background:linear-gradient(135deg,#17a2b8,#138496)">
                        <?php echo e($pendingOnsiteRequests->count()); ?> Onsite Pending
                    </span>
                    <span class="badge" style="background:linear-gradient(135deg,#6f42c1,#5a32a3)">
                        <?php echo e($pendingStudentRequests->count()); ?> Student Pending
                    </span>
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
                <div class="card-body pb-0">
                    <ul class="nav nav-tabs" id="pendingTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="onsite-tab" data-bs-toggle="tab" data-bs-target="#onsite-pane" type="button" role="tab">
                                <i class="bi bi-building me-1"></i> Onsite Requests
                                <span class="tab-count onsite"><?php echo e($pendingOnsiteRequests->count()); ?></span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="student-tab" data-bs-toggle="tab" data-bs-target="#student-pane" type="button" role="tab">
                                <i class="bi bi-person-badge me-1"></i> Student Requests
                                <span class="tab-count student"><?php echo e($pendingStudentRequests->count()); ?></span>
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="tab-content" id="pendingTabContent">

                    
                    <div class="tab-pane fade show active" id="onsite-pane" role="tabpanel">
                        <div class="card-body">
                            <?php if($pendingOnsiteRequests->count() > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead>
                                            <tr>
                                                <th>Reference Code</th>
                                                <th>Full Name</th>
                                                <th>Documents</th>
                                                <th>Total Amount</th>
                                                <th>Receipt</th>
                                                <th>Uploaded</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__currentLoopData = $pendingOnsiteRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr class="table-row">
                                                    <td>
                                                        <strong><?php echo e($request->ref_code ?? $request->id); ?></strong>
                                                        <br><small class="text-muted"><?php echo e($request->queue_number ?? ''); ?></small>
                                                    </td>
                                                    <td>
                                                        <?php echo e($request->full_name); ?>

                                                        <br><small class="text-muted"><?php echo e($request->course); ?> - <?php echo e($request->year_level); ?></small>
                                                    </td>
                                                    <td>
                                                        <?php $__currentLoopData = $request->requestItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <div>• <?php echo e($item->document->type_document); ?> (x<?php echo e($item->quantity); ?>)</div>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </td>
                                                    <td>
                                                        <strong class="text-success">
                                                            ₱<?php echo e(number_format($request->requestItems->sum(fn($item) => $item->document->price * $item->quantity), 2)); ?>

                                                        </strong>
                                                    </td>
                                                    <td>
                                                        <img src="<?php echo e(route('accounting.receipt.onsite', $request)); ?>"
                                                             alt="Payment Receipt"
                                                             class="img-thumbnail"
                                                             style="max-width: 80px; max-height: 80px; cursor: pointer;"
                                                             onclick="openReceiptModal('<?php echo e(route('accounting.receipt.onsite', $request)); ?>', '<?php echo e($request->ref_code ?? $request->id); ?>')">
                                                    </td>
                                                    <td><small class="text-muted"><?php echo e($request->updated_at->diffForHumans()); ?></small></td>
                                                    <td>
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
                                                    </td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-5">
                                    <i class="bi bi-check-circle-fill text-success" style="font-size: 3rem;"></i>
                                    <h4 class="mt-3">No Pending Onsite Approvals</h4>
                                    <p class="text-muted">All onsite payment receipts have been processed.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    
                    <div class="tab-pane fade" id="student-pane" role="tabpanel">
                        <div class="card-body">
                            <?php if($pendingStudentRequests->count() > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead>
                                            <tr>
                                                <th>Reference No.</th>
                                                <th>Student</th>
                                                <th>Documents</th>
                                                <th>Total Amount</th>
                                                <th>Receipt</th>
                                                <th>Uploaded</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__currentLoopData = $pendingStudentRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr class="table-row">
                                                    <td>
                                                        <strong><?php echo e($request->reference_no ?? 'N/A'); ?></strong>
                                                    </td>
                                                    <td>
                                                        <?php echo e($request->student->user->first_name); ?> <?php echo e($request->student->user->last_name); ?>

                                                        <br><small class="text-muted"><?php echo e($request->student->student_id); ?></small>
                                                    </td>
                                                    <td>
                                                        <?php $__currentLoopData = $request->requestItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <div>• <?php echo e($item->document->type_document); ?> (x<?php echo e($item->quantity); ?>)</div>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </td>
                                                    <td>
                                                        <strong class="text-success">
                                                            ₱<?php echo e(number_format($request->requestItems->sum(fn($item) => $item->document->price * $item->quantity), 2)); ?>

                                                        </strong>
                                                    </td>
                                                    <td>
                                                        <img src="<?php echo e(route('accounting.receipt.student', $request)); ?>"
                                                             alt="Payment Receipt"
                                                             class="img-thumbnail"
                                                             style="max-width: 80px; max-height: 80px; cursor: pointer;"
                                                             onclick="openReceiptModal('<?php echo e(route('accounting.receipt.student', $request)); ?>', '<?php echo e($request->reference_no); ?>')">
                                                    </td>
                                                    <td><small class="text-muted"><?php echo e($request->updated_at->diffForHumans()); ?></small></td>
                                                    <td>
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
                                                    </td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-5">
                                    <i class="bi bi-check-circle-fill text-success" style="font-size: 3rem;"></i>
                                    <h4 class="mt-3">No Pending Student Approvals</h4>
                                    <p class="text-muted">All student payment receipts have been processed.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

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
<?php echo $__env->make('layouts.accounting', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Brill\nu-regis\resources\views/accounting/dashboard.blade.php ENDPATH**/ ?>