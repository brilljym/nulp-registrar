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
                        <span class="text-warning">Currently Occupied</span> - Processing 
                        <?php if(isset($currentRequestType) && $currentRequestType === 'student'): ?>
                            <strong>student request</strong> from 
                            <strong><?php echo e($currentRequest->student->user->first_name); ?> <?php echo e($currentRequest->student->user->last_name); ?></strong>
                        <?php else: ?>
                            <strong>onsite request</strong> from 
                            <strong><?php echo e($currentRequest->full_name); ?></strong>
                        <?php endif; ?>
                        (<?php echo e($currentRequest->created_at->format('M d, Y')); ?>)
                        <br><small class="text-muted">Complete this request to receive new ones.</small>
                    <?php else: ?>
                        <span class="text-success">Available</span> - Ready to receive new requests
                        <?php
                            $pendingCount = \App\Models\OnsiteRequest::where('status', 'pending')
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
        <?php if(request()->routeIs('registrar.onsite.pending')): ?>
            <i class="bi bi-clock-fill text-warning"></i> My Pending Requests 
            <?php if(isset($windowNumber)): ?>
                <small class="text-muted">(Window <?php echo e($windowNumber); ?>)</small>
            <?php endif; ?>
        <?php elseif(request()->routeIs('registrar.onsite.completed')): ?>
            <i class="bi bi-check-circle-fill text-success"></i> My Completed Requests
            <?php if(isset($windowNumber)): ?>
                <small class="text-muted">(Window <?php echo e($windowNumber); ?>)</small>
            <?php endif; ?>
        <?php else: ?>
            <i class="bi bi-building-check text-info"></i> My Window Queue
            <?php if(isset($windowNumber)): ?>
                <small class="text-muted">(Window <?php echo e($windowNumber); ?>)</small>
            <?php endif; ?>
        <?php endif; ?>
    </h2>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th class="text-center" style="width: 5%;">#</th>
                    <th>Name & Ticket</th>
                    <th>Course Details</th>
                    <th>Document Type</th>
                    <th>Reason</th>
                    <th class="text-center">Status</th>
                    <th>Expected Release Date</th>
                    <th>Window / Registrar</th>
                    <th class="text-center" style="width: 15%;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if($requests->isEmpty()): ?>
                    <tr>
                        <td colspan="9" class="text-center py-4">
                            <div class="alert alert-info mb-0">
                                <?php if(isset($isWindowOccupied) && $isWindowOccupied): ?>
                                    Your window is currently occupied. Complete the current request to receive new ones.
                                <?php elseif(request()->routeIs('registrar.onsite.pending')): ?>
                                    <?php
                                        $totalPending = \App\Models\OnsiteRequest::where('status', 'pending')
                                            ->whereNull('assigned_registrar_id')
                                            ->whereNull('window_id')
                                            ->count();
                                    ?>
                                    <?php if($totalPending > 0): ?>
                                        <div class="text-warning mb-2">
                                            <i class="bi bi-exclamation-circle"></i> There are <?php echo e($totalPending); ?> pending request(s) waiting for approval, but none are currently assigned to your window.
                                        </div>
                                        <small class="text-muted">
                                            Pending requests should appear here automatically. If you don't see them, please refresh the page or contact support.
                                        </small>
                                    <?php else: ?>
                                        No pending requests assigned to your window at this time.
                                    <?php endif; ?>
                                <?php elseif(request()->routeIs('registrar.onsite.completed')): ?>
                                    No completed requests found for your window.
                                <?php else: ?>
                                    No requests assigned to your window at this time.
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php $__currentLoopData = $requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $req): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="table-row">
                    <td class="text-center fw-bold text-muted"><?php echo e($requests->firstItem() + $index); ?></td>
                    <td>
                        <div class="fw-semibold"><?php echo e($req->full_name); ?></div>
                        <small class="text-muted"><?php echo e($req->created_at->format('M d, Y')); ?></small><br>
                        <small class="text-monospace text-secondary">
                            ticket-no:<?php echo e($req->created_at->format('Ymd')); ?>-i<?php echo e($req->id); ?>

                        </small>
                    </td>
                    <td>
                        <div><strong>Course:</strong> <?php echo e($req->course); ?></div>
                        <div><strong>Year:</strong> <?php echo e($req->year_level); ?></div>
                        <div><strong>Dept:</strong> <?php echo e($req->department); ?></div>
                    </td>
                    <td>
                        <?php if($req->requestItems->count() > 0): ?>
                            <?php $__currentLoopData = $req->requestItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div><?php echo e($item->document->type_document); ?> (x<?php echo e($item->quantity); ?>)</div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php else: ?>
                            <span class="text-muted">Not specified</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php echo e($req->reason ?? 'Not specified'); ?>

                    </td>
                    <td class="text-center">
                        <span class="badge bg-<?php echo e($req->display_status === 'completed' ? 'success' : ($req->display_status === 'pending' ? 'warning' : ($req->display_status === 'registrar_approved' ? 'info' : ($req->display_status === 'processing' ? 'primary' : ($req->display_status === 'released' ? 'success' : ($req->display_status === 'in_queue' ? 'primary' : ($req->display_status === 'ready_for_pickup' ? 'warning' : ($req->display_status === 'waiting' ? 'secondary' : 'secondary')))))))); ?> rounded-pill px-3">
                            <?php echo e($req->display_status_label); ?>

                        </span><br>
                        <small class="text-muted">
                            <?php if($req->display_status === 'in_queue'): ?>
                                In Progress
                            <?php elseif($req->display_status === 'ready_for_pickup'): ?>
                                In Progress
                            <?php elseif($req->status === 'waiting'): ?>
                                Waiting
                            <?php elseif($req->current_step === 'start'): ?>
                                In Progress
                            <?php else: ?>
                                <?php echo e(ucfirst($req->current_step)); ?>

                            <?php endif; ?>
                        </small>
                    </td>
                    <td>
                        <?php if($req->expected_release_date): ?>
                            <div><i class="bi bi-calendar-check"></i> <?php echo e(\Carbon\Carbon::parse($req->expected_release_date)->format('M j, Y')); ?></div>
                            <small class="text-muted"><?php echo e(\Carbon\Carbon::parse($req->expected_release_date)->format('l g:i A')); ?></small>
                            <?php if($req->status === 'completed'): ?>
                                <br>
                                <button class="btn btn-sm btn-outline-primary mt-1" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editReleaseDateModal" 
                                        data-request-id="<?php echo e($req->id); ?>"
                                        data-current-date="<?php echo e($req->expected_release_date->format('Y-m-d\TH:i')); ?>">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="text-muted"><i class="bi bi-dash-circle"></i> Not set</span>
                            <?php if($req->status === 'completed'): ?>
                                <br>
                                <button class="btn btn-sm btn-outline-primary mt-1" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editReleaseDateModal" 
                                        data-request-id="<?php echo e($req->id); ?>"
                                        data-current-date="">
                                    <i class="bi bi-plus-circle"></i> Set Date
                                </button>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div><strong>Window:</strong>
                            <?php if($req->window): ?>
                                <?php echo e($req->window->name); ?>

                            <?php else: ?>
                                <span class="text-muted">Unassigned</span>
                            <?php endif; ?>
                        </div>
                        <div><strong>Registrar:</strong>
                            <?php if($req->registrar): ?>
                                <?php echo e($req->registrar->first_name); ?> <?php echo e($req->registrar->last_name); ?>

                            <?php else: ?>
                                <span class="text-muted">Not assigned</span>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="text-center">
                        <?php if($req->status === 'completed'): ?>
                            <?php if($req->feedback): ?>
                                <div class="text-center">
                                    <div class="text-warning mb-1">
                                        <?php for($i = 1; $i <= $req->feedback->rating; $i++): ?>
                                            ⭐
                                        <?php endfor; ?>
                                    </div>
                                    <small class="text-success">
                                        <i class="bi bi-chat-heart"></i> Feedback received
                                    </small>
                                    <?php if($req->feedback->comment): ?>
                                        <br><small class="text-muted" title="<?php echo e($req->feedback->comment); ?>">
                                            "<?php echo e(Str::limit($req->feedback->comment, 30)); ?>"
                                        </small>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <small class="text-muted">
                                    <i class="bi bi-chat"></i> No feedback yet
                                </small>
                            <?php endif; ?>
                        <?php elseif($req->status === 'pending'): ?>
                            <small class="text-muted">Onsite Processing</small><br>
                            <?php if(isset($hasActiveOnsiteRequest) && $hasActiveOnsiteRequest): ?>
                                <div class="text-center">
                                    <span class="badge bg-secondary rounded-pill px-3 mb-1">Waiting</span><br>
                                    <small class="text-muted">
                                        <i class="bi bi-hourglass-split"></i> You have an active onsite request - complete it first to approve new ones
                                    </small>
                                </div>
                            <?php else: ?>
                                <button type="button" class="btn btn-success action-btn" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#approveRequestModal" 
                                        data-request-id="<?php echo e($req->id); ?>"
                                        data-student-name="<?php echo e($req->full_name); ?>">
                                    Approve & Take Request
                                </button>
                            <?php endif; ?>
                        <?php elseif($req->status === 'registrar_approved'): ?>
                            <small class="text-muted">Onsite Processing</small><br>
                            <span class="text-info"><i class="bi bi-check-circle"></i> Approved - Awaiting Payment</span>
                        <?php elseif($req->status === 'processing' && $req->assigned_registrar_id === Auth::id()): ?>
                            <small class="text-muted">Onsite Processing</small><br>
                            <form method="POST" action="<?php echo e(route('registrar.onsite.release', $req->id)); ?>" class="d-inline">
                                <?php echo csrf_field(); ?>
                                <button class="btn btn-warning action-btn">Mark as Ready</button>
                            </form>
                        <?php elseif($req->current_step === 'release' && $req->assigned_registrar_id === Auth::id()): ?>
                            <form method="POST" action="<?php echo e(route('registrar.onsite.close', $req->id)); ?>" class="d-inline">
                                <?php echo csrf_field(); ?>
                                <button class="btn btn-primary action-btn">Close</button>
                            </form>
                        <?php elseif($req->status === 'released' && $req->assigned_registrar_id === Auth::id()): ?>
                            <small class="text-muted">Onsite Processing</small><br>
                            <form method="POST" action="<?php echo e(route('registrar.onsite.complete', $req->id)); ?>" class="d-inline">
                                <?php echo csrf_field(); ?>
                                <button class="btn btn-success action-btn">Mark as Completed</button>
                            </form>
                        <?php elseif($req->status === 'in_queue' && (!$req->assigned_registrar_id || $req->assigned_registrar_id === Auth::id())): ?>
                            <small class="text-muted">Kiosk Processing</small><br>
                            <?php if(!isset($isWindowOccupied) || !$isWindowOccupied || (isset($currentRequest) && $currentRequest->id === $req->id)): ?>
                                <?php if($req->assigned_registrar_id === Auth::id()): ?>
                                    
                                    <form method="POST" action="<?php echo e(route('registrar.onsite.ready-pickup', $req->id)); ?>" class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <button class="btn btn-warning action-btn">Ready for Pickup</button>
                                    </form>
                                <?php else: ?>
                                    
                                    <button type="button" class="btn btn-primary action-btn" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#takeRequestModal" 
                                            data-request-id="<?php echo e($req->id); ?>"
                                            data-student-name="<?php echo e($req->full_name); ?>">
                                        Take Request
                                    </button>
                                <?php endif; ?>
                            <?php else: ?>
                                <small class="text-muted">
                                    <i class="bi bi-lock-fill"></i> Window occupied
                                </small>
                            <?php endif; ?>
                        <?php elseif($req->status === 'ready_for_pickup' && $req->assigned_registrar_id === Auth::id()): ?>
                            <form method="POST" action="<?php echo e(route('registrar.onsite.complete-request', $req->id)); ?>" class="d-inline">
                                <?php echo csrf_field(); ?>
                                <button class="btn btn-success action-btn">Complete Request</button>
                            </form>
                        <?php elseif($req->status === 'ready_for_pickup' && !$req->assigned_registrar_id): ?>
                            
                            <?php if(!isset($isWindowOccupied) || !$isWindowOccupied): ?>
                                <div class="d-flex flex-column gap-1">
                                    <button type="button" class="btn btn-success action-btn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#approveRequestModal"
                                            data-request-id="<?php echo e($req->id); ?>"
                                            data-student-name="<?php echo e($req->full_name); ?>">
                                        Approve & Take Request
                                    </button>
                                    <button type="button" class="btn btn-primary action-btn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#takeRequestModal"
                                            data-request-id="<?php echo e($req->id); ?>"
                                            data-student-name="<?php echo e($req->full_name); ?>">
                                        Take Request
                                    </button>
                                </div>
                            <?php else: ?>
                                <small class="text-muted">
                                    <i class="bi bi-hourglass-split"></i> Window occupied - will be available when current request is completed
                                </small>
                            <?php endif; ?>
                        <?php elseif($req->status === 'waiting' || $req->display_status === 'waiting'): ?>
                            <small class="text-muted">Kiosk Processing</small><br>
                            <?php if($req->assigned_registrar_id === Auth::id()): ?>
                                
                                <?php if(!isset($isWindowOccupied) || !$isWindowOccupied): ?>
                                    <div class="d-flex flex-column gap-1">
                                        <form method="POST" action="<?php echo e(route('registrar.onsite.take', $req->id)); ?>" class="d-inline">
                                            <?php echo csrf_field(); ?>
                                            <button class="btn btn-primary action-btn">Start Processing</button>
                                        </form>
                                    </div>
                                <?php else: ?>
                                    <small class="text-muted">
                                        <i class="bi bi-hourglass-split"></i> Window occupied - will start when current request is completed
                                    </small>
                                <?php endif; ?>
                            <?php elseif(!$req->assigned_registrar_id): ?>
                                
                                <?php if(!isset($isWindowOccupied) || !$isWindowOccupied): ?>
                                    <div class="d-flex flex-column gap-1">
                                        <button type="button" class="btn btn-success action-btn" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#approveRequestModal" 
                                                data-request-id="<?php echo e($req->id); ?>"
                                                data-student-name="<?php echo e($req->full_name); ?>">
                                            Approve & Take Request
                                        </button>
                                        <button type="button" class="btn btn-primary action-btn" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#takeRequestModal" 
                                                data-request-id="<?php echo e($req->id); ?>"
                                                data-student-name="<?php echo e($req->full_name); ?>">
                                            Take Request
                                        </button>
                                    </div>
                                <?php else: ?>
                                    <small class="text-muted">
                                        <i class="bi bi-hourglass-split"></i> Waiting in queue - will be available when your window is free
                                    </small>
                                <?php endif; ?>
                            <?php else: ?>
                                
                                <small class="text-muted">
                                    <i class="bi bi-person-fill"></i> Assigned to another registrar - waiting for them to process
                                </small>
                            <?php endif; ?>
                        <?php elseif($req->assigned_registrar_id && $req->assigned_registrar_id !== Auth::id()): ?>
                            <small class="text-muted">
                                <i class="bi bi-person-fill"></i> Assigned to another registrar
                            </small>
                        <?php else: ?>
                            <small class="text-muted">
                                <i class="bi bi-clock"></i> Pending assignment
                            </small>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if($requests->isEmpty()): ?>
        <div class="text-center py-4">
            <small class="text-muted">
                <?php if(isset($isWindowOccupied) && $isWindowOccupied): ?>
                    Complete your current request to receive new ones.
                <?php else: ?>
                    Refresh the page if you expect to see requests here.
                <?php endif; ?>
            </small>
        </div>
    <?php endif; ?>

    <!-- Laravel Pagination (clean & compact) -->
    <div class="d-flex justify-content-between align-items-center mt-4">
        <div class="pagination-info">
            <small class="text-muted">
                Showing <?php echo e($requests->firstItem()); ?> to <?php echo e($requests->lastItem()); ?> of <?php echo e($requests->total()); ?> requests
            </small>
        </div>
        <div class="pagination-wrapper">
            <?php echo e($requests->links('vendor.pagination.bootstrap-5')); ?>

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
        editReleaseDateForm.action = `/registrar/onsite/update-release-date/${requestId}`;
        
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
        approveRequestForm.action = `/registrar/onsite/approve/${requestId}`;
        
        // Set student name
        studentNameInput.value = studentName;
        
        // Clear remarks
        remarksTextarea.value = '';
        
        // Set up rejection button
        rejectRequestBtn.onclick = function() {
            if (confirm('Are you sure you want to reject this onsite request? The student will be able to re-approve it from their timeline.')) {
                // Create a rejection form
                const rejectForm = document.createElement('form');
                rejectForm.method = 'POST';
                rejectForm.action = `/registrar/onsite/reject/${requestId}`;
                
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
                
                document.body.appendChild(rejectForm);
                rejectForm.submit();
            }
        };
    });
});
</script>

<!-- Take Request Modal -->
<div class="modal fade" id="takeRequestModal" tabindex="-1" aria-labelledby="takeRequestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="takeRequestModalLabel">Take Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="takeRequestForm" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="take_student_name" class="form-label">Student</label>
                        <input type="text" class="form-control" id="take_student_name" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="take_remarks" class="form-label">Remarks/Comments</label>
                        <textarea class="form-control" id="take_remarks" name="remarks" rows="3" placeholder="Add any remarks or comments about this request..."></textarea>
                        <div class="form-text">Optional: Add notes that will be visible to the student in their timeline.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Take Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const takeRequestModal = document.getElementById('takeRequestModal');
    const takeRequestForm = document.getElementById('takeRequestForm');
    const takeStudentNameInput = document.getElementById('take_student_name');
    const takeRemarksTextarea = document.getElementById('take_remarks');

    takeRequestModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const requestId = button.getAttribute('data-request-id');
        const studentName = button.getAttribute('data-student-name');
        
        // Update form action
        takeRequestForm.action = `/registrar/onsite/take/${requestId}`;
        
        // Set student name
        takeStudentNameInput.value = studentName;
        
        // Clear remarks
        takeRemarksTextarea.value = '';
    });
});
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.registrar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Nu-Regisv2\resources\views/registrar/onsite/index.blade.php ENDPATH**/ ?>