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

<div class="container mt-5">
    <!-- Window Status Alert -->
    <?php if(isset($isWindowOccupied) && isset($windowNumber)): ?>
        <div class="alert <?php echo e($isWindowOccupied ? 'alert-warning' : 'alert-info'); ?> mb-4">
            <div class="d-flex align-items-center">
                <i class="bi <?php echo e($isWindowOccupied ? 'bi-exclamation-triangle-fill' : 'bi-check-circle-fill'); ?> me-2"></i>
                <div>
                    <strong>Window <?php echo e($windowNumber); ?> Status:</strong>
                    <?php if($isWindowOccupied && isset($currentRequest)): ?>
                        <span class="text-warning">Currently Occupied</span> - Processing request from 
                        <strong><?php echo e($currentRequest->student->user->first_name); ?> <?php echo e($currentRequest->student->user->last_name); ?></strong> 
                        (<?php echo e($currentRequest->created_at->format('M d, Y')); ?>)
                        <br><small class="text-muted">Complete this request to receive new ones.</small>
                    <?php else: ?>
                        <span class="text-success">Available</span> - Ready to receive new requests
                        <?php
                            $pendingCount = \App\Models\StudentRequest::where('status', 'pending')
                                ->whereNull('assigned_registrar_id')
                                ->whereNull('window_id')
                                ->count();
                        ?>
                        <?php if($pendingCount > 0): ?>
                            <br><small class="text-info">
                                <i class="bi bi-clock-fill"></i> <?php echo e($pendingCount); ?> pending request(s) available for approval
                            </small>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <h2 class="mb-4">
        <i class="bi bi-list-check text-primary"></i>
        <?php echo e(request('search') ? 'Search Results' : 'All Document Requests'); ?>

        <?php if(isset($windowNumber)): ?>
            <small class="text-muted">(Window <?php echo e($windowNumber); ?>)</small>
        <?php endif; ?>
    </h2>

    <?php if(request('search')): ?>
        <div class="alert alert-info">
            Showing <?php echo e($all->total()); ?> result<?php echo e($all->total() === 1 ? '' : 's'); ?> for:
            <strong>"<?php echo e(request('search')); ?>"</strong>
            <a href="<?php echo e(route('registrar.dashboard')); ?>" class="btn btn-sm btn-outline-secondary ms-2">Clear</a>
        </div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th class="text-center" style="width: 5%;">#</th>
                    <th>Student Details</th>
                    <th>Document Type</th>
                    <th>Reason</th>
                    <th class="text-center">Status</th>
                    <th>Window / Registrar</th>
                    <th>Request Date</th>
                    <th>Expected Release Date</th>
                    <th class="text-center" style="width: 15%;">Actions</th>
                </tr>
            </thead>
            <tbody>

                <?php $__currentLoopData = $all; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="table-row">
                    <td class="text-center fw-bold text-muted"><?php echo e($all->firstItem() + $index); ?></td>
                    <td>
                        <div class="fw-semibold"><?php echo e($request->student->user->first_name); ?> <?php echo e($request->student->user->last_name); ?></div>
                        <small class="text-muted"><?php echo e($request->student->student_id); ?></small><br>
                        <small class="text-primary">REF: <?php echo e($request->reference_no); ?></small>
                    </td>
                    <td>
                        <?php if($request->requestItems->count() > 0): ?>
                            <?php $__currentLoopData = $request->requestItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="fw-semibold"><?php echo e($item->document->type_document); ?> (x<?php echo e($item->quantity); ?>)</div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php else: ?>
                            <span class="text-muted">No documents</span>
                        <?php endif; ?>
                        <small class="text-muted">Total: ₱<?php echo e(number_format($request->total_cost, 2)); ?></small>
                    </td>
                    <td>
                        <?php echo e($request->reason ?? 'Not specified'); ?>

                    </td>
                    <td class="text-center">
                        <span class="badge bg-<?php echo e($request->status === 'completed' ? 'success' : ($request->status === 'pending' ? 'warning' : ($request->status === 'registrar_approved' ? 'info' : ($request->status === 'processing' ? 'info' : ($request->status === 'ready_for_release' ? 'primary' : ($request->status === 'ready_for_pickup' ? 'warning' : ($request->status === 'in_queue' ? 'primary' : ($request->status === 'waiting' ? 'secondary' : 'secondary')))))))); ?> rounded-pill px-3">
                            <?php echo e(ucfirst(str_replace('_', ' ', $request->status))); ?>

                        </span><br>
                        <small class="text-muted">
                            <?php if($request->status === 'in_queue'): ?>
                                In Progress
                            <?php elseif($request->status === 'ready_for_pickup'): ?>
                                In Progress
                            <?php elseif($request->status === 'waiting'): ?>
                                Waiting
                            <?php elseif($request->status === 'completed'): ?>
                                Completed
                            <?php else: ?>
                                In Progress
                            <?php endif; ?>
                        </small>
                    </td>
                    <td>
                        <?php if($request->window && $request->window->window_number): ?>
                            <div class="fw-semibold"><i class="bi bi-grid-1x2"></i> Window <?php echo e($request->window->window_number); ?></div>
                            <small class="text-muted"><?php echo e($request->registrar ? $request->registrar->first_name . ' ' . $request->registrar->last_name : 'Unassigned'); ?></small>
                        <?php elseif($request->registrar): ?>
                            <div><i class="bi bi-person-fill"></i> <?php echo e($request->registrar->first_name); ?> <?php echo e($request->registrar->last_name); ?></div>
                        <?php else: ?>
                            <span class="text-muted"><i class="bi bi-dash-circle"></i> Not Assigned</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div><i class="bi bi-clock"></i> <?php echo e($request->created_at->format('M j, Y')); ?></div>
                        <small class="text-muted"><?php echo e($request->created_at->format('g:i A')); ?></small>
                    </td>
                    <td>
                        <?php if($request->expected_release_date): ?>
                            <div><i class="bi bi-calendar-check"></i> <?php echo e(\Carbon\Carbon::parse($request->expected_release_date)->format('M j, Y')); ?></div>
                            <small class="text-muted"><?php echo e(\Carbon\Carbon::parse($request->expected_release_date)->format('l')); ?></small>
                            <?php if(in_array($request->status, ['completed', 'processing'])): ?>
                                <br>
                                <button class="btn btn-sm btn-outline-primary mt-1" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editReleaseDateModal" 
                                        data-request-id="<?php echo e($request->id); ?>"
                                        data-current-date="<?php echo e($request->expected_release_date->format('Y-m-d\TH:i')); ?>">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="text-muted"><i class="bi bi-dash-circle"></i> Not set</span>
                            <?php if(in_array($request->status, ['completed', 'processing'])): ?>
                                <br>
                                <button class="btn btn-sm btn-outline-primary mt-1" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editReleaseDateModal" 
                                        data-request-id="<?php echo e($request->id); ?>"
                                        data-current-date="">
                                    <i class="bi bi-plus-circle"></i> Set Date
                                </button>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <?php if($request->status === 'pending'): ?>
                            <?php if(isset($hasActiveOnsiteRequest) && $hasActiveOnsiteRequest): ?>
                                <div class="text-center">
                                    <small class="text-muted">Onsite Processing</small><br>
                                    <span class="badge bg-secondary rounded-pill px-3 mb-1">Waiting</span><br>
                                    <small class="text-muted">
                                        <i class="bi bi-hourglass-split"></i> You have an active onsite request - complete it first to approve new ones
                                    </small>
                                </div>
                            <?php else: ?>
                                <small class="text-muted">Onsite Processing</small><br>
                                <button type="button" class="btn btn-success action-btn" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#approveRequestModal" 
                                        data-request-id="<?php echo e($request->id); ?>"
                                        data-student-name="<?php echo e($request->student->user->first_name); ?> <?php echo e($request->student->user->last_name); ?>">
                                    Approve & Take Request
                                </button>
                            <?php endif; ?>
                        <?php elseif($request->status === 'registrar_approved'): ?>
                            <small class="text-muted">Onsite Processing</small><br>
                            <span class="text-info"><i class="bi bi-check-circle"></i> Approved - Awaiting Payment</span>
                        <?php elseif($request->status === 'processing'): ?>
                            <small class="text-muted">Onsite Processing</small><br>
                            <form action="<?php echo e(route('registrar.release', $request->id)); ?>" method="POST" class="d-inline">
                                <?php echo csrf_field(); ?>
                                <button class="btn btn-warning action-btn">Mark as Ready</button>
                            </form>
                        <?php elseif($request->status === 'ready_for_release'): ?>
                            <form action="<?php echo e(route('registrar.close', $request->id)); ?>" method="POST" class="d-inline">
                                <?php echo csrf_field(); ?>
                                <button class="btn btn-success action-btn">Mark as Completed</button>
                            </form>
                        <?php elseif($request->status === 'in_queue'): ?>
                            <small class="text-muted">Kiosk Processing</small><br>
                            <form action="<?php echo e(route('registrar.ready-pickup', $request->id)); ?>" method="POST" class="d-inline">
                                <?php echo csrf_field(); ?>
                                <button class="btn btn-warning action-btn">Ready for Pickup</button>
                            </form>
                        <?php elseif($request->status === 'ready_for_pickup'): ?>
                            <form action="<?php echo e(route('registrar.complete', $request->id)); ?>" method="POST" class="d-inline">
                                <?php echo csrf_field(); ?>
                                <button class="btn btn-success action-btn">Mark as Completed</button>
                            </form>
                        <?php elseif($request->status === 'waiting'): ?>
                            <small class="text-muted">Kiosk Processing</small><br>
                            <small class="text-muted">
                                <i class="bi bi-hourglass-split"></i> Waiting in queue - will move automatically when available
                            </small>
                        <?php elseif($request->status === 'completed'): ?>
                            <span class="text-success"><i class="bi bi-check-circle-fill"></i> Completed</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>

    <?php if($all->isEmpty()): ?>
        <div class="alert alert-info text-center mt-4">
            No document requests found<?php echo e(request('search') ? ' matching "' . request('search') . '"' : '.'); ?>

        </div>
    <?php endif; ?>

    <!-- Laravel Pagination (clean & compact) -->
    <div class="d-flex justify-content-between align-items-center mt-4">
        <div class="pagination-info">
            <small class="text-muted">
                Showing <?php echo e($all->firstItem()); ?> to <?php echo e($all->lastItem()); ?> of <?php echo e($all->total()); ?> requests
            </small>
        </div>
        <div class="pagination-wrapper">
            <?php echo e($all->appends(['search' => request('search')])->links('vendor.pagination.bootstrap-5')); ?>

        </div>
    </div>
</div>

<!-- Edit Release Date Modal -->
<div class="modal fade" id="editReleaseDateModal" tabindex="-1" aria-labelledby="editReleaseDateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editReleaseDateModalLabel">Edit Expected Release Date</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editReleaseDateForm" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="expected_release_date" class="form-label">Expected Release Date & Time</label>
                        <input type="datetime-local" class="form-control" id="expected_release_date" name="expected_release_date" required>
                        <div class="form-text">Select the date and time when the document will be released.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Date</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const editReleaseDateModal = document.getElementById('editReleaseDateModal');
    const editReleaseDateForm = document.getElementById('editReleaseDateForm');
    const expectedReleaseDateInput = document.getElementById('expected_release_date');

    editReleaseDateModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const requestId = button.getAttribute('data-request-id');
        const currentDate = button.getAttribute('data-current-date');
        
        // Update form action
        editReleaseDateForm.action = `/registrar/update-release-date/${requestId}`;
        
        // Set current date if exists
        if (currentDate) {
            expectedReleaseDateInput.value = currentDate;
        } else {
            // Set to current date + 1 day as default
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            expectedReleaseDateInput.value = tomorrow.toISOString().slice(0, 16);
        }
    });
});
</script>

<!-- Approve Request Modal -->
<div class="modal fade" id="approveRequestModal" tabindex="-1" aria-labelledby="approveRequestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approveRequestModalLabel">Approve & Take Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="approveRequestForm" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="student_name" class="form-label">Student</label>
                        <input type="text" class="form-control" id="student_name" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="remarks" class="form-label">Remarks/Comments</label>
                        <textarea class="form-control" id="remarks" name="remarks" rows="3" placeholder="Add any remarks or comments about this request..."></textarea>
                        <div class="form-text">Optional: Add notes that will be visible to the student in their timeline.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger me-auto" id="rejectRequestBtn">
                        <i class="bi bi-x-circle"></i> Reject Request
                    </button>
                    <button type="submit" class="btn btn-success">Approve & Take Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const approveRequestModal = document.getElementById('approveRequestModal');
    const approveRequestForm = document.getElementById('approveRequestForm');
    const studentNameInput = document.getElementById('student_name');
    const remarksTextarea = document.getElementById('remarks');
    const rejectRequestBtn = document.getElementById('rejectRequestBtn');

    approveRequestModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const requestId = button.getAttribute('data-request-id');
        const studentName = button.getAttribute('data-student-name');
        
        // Update form action
        approveRequestForm.action = `/registrar/approve/${requestId}`;
        
        // Set student name
        studentNameInput.value = studentName;
        
        // Clear remarks
        remarksTextarea.value = '';
        
        // Set up rejection button
        rejectRequestBtn.onclick = function() {
            console.log('Reject button clicked for student request:', requestId);
            if (confirm('Are you sure you want to reject this request? The student will be able to re-approve it from their timeline.')) {
                // Create a rejection form
                const rejectForm = document.createElement('form');
                rejectForm.method = 'POST';
                rejectForm.action = `/registrar/reject/${requestId}`;
                
                // Add CSRF token
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content') || '<?php echo e(csrf_token()); ?>';
                rejectForm.appendChild(csrfInput);
                
                // Add remarks if any
                if (remarksTextarea.value.trim()) {
                    const remarksInput = document.createElement('input');
                    remarksInput.type = 'hidden';
                    remarksInput.name = 'remarks';
                    remarksInput.value = remarksTextarea.value.trim();
                    rejectForm.appendChild(remarksInput);
                }
                
                console.log('Submitting rejection form with data:', {
                    requestId: requestId,
                    remarks: remarksTextarea.value.trim(),
                    csrfToken: csrfInput.value ? 'present' : 'missing'
                });
                document.body.appendChild(rejectForm);
                rejectForm.submit();
            }
        };
    });
});
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.registrar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Nu-Regisv2\resources\views/registrar/dashboard_all.blade.php ENDPATH**/ ?>