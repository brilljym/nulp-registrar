 

<?php $__env->startSection('content'); ?>
<style>
    .section-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #0d6efd;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #ffc107;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .document-item {
        background: #f8f9fa;
        border: 1px solid #dee2e6 !important;
        transition: all 0.2s ease;
    }
    
    .document-item:hover {
        background: #fff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .total-cost-section {
        border: 1px solid #dee2e6;
    }
    
    .btn-outline-primary:hover {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
    
    .btn-outline-danger:hover {
        background-color: #dc3545;
        border-color: #dc3545;
    }
</style>
<div class="container mt-5">
    <h3 class="mb-4">Request a Document</h3>

    <?php if(session('success')): ?>
        <div class="alert alert-success">
            <i class="bi bi-check-circle me-2"></i><?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-circle me-2"></i><?php echo e(session('error')); ?>

        </div>
    <?php endif; ?>

    
    <?php if($pendingRequest && ($pendingRequest->payment_confirmed || ($pendingRequest->total_cost == 0 && $pendingRequest->status !== 'pending'))): ?>
        <?php $request = $pendingRequest; ?>
        <div class="card mb-4 shadow-sm border-0" style="border-left: 4px solid 
            <?php echo e($request->status === 'pending' ? '#ffc107' : 
               ($request->status === 'processing' ? '#0dcaf0' : 
               ($request->status === 'ready_for_release' ? '#0d6efd' : '#28a745'))); ?> !important;">
            <div class="card-body p-4">
                <!-- Header -->
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3" 
                         style="width: 40px; height: 40px; background-color: 
                         <?php echo e($request->status === 'pending' ? '#ffc107' : 
                            ($request->status === 'processing' ? '#0dcaf0' : 
                            ($request->status === 'ready_for_release' ? '#0d6efd' : '#28a745'))); ?>;">
                        <i class="bi bi-<?php echo e($request->status === 'pending' ? 'clock-fill' : 
                                          ($request->status === 'processing' ? 'gear-fill' : 
                                          ($request->status === 'ready_for_release' ? 'box-seam' : 'check-circle-fill'))); ?> text-white"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold" style="color: 
                            <?php echo e($request->status === 'pending' ? '#ffc107' : 
                               ($request->status === 'processing' ? '#0dcaf0' : 
                               ($request->status === 'ready_for_release' ? '#0d6efd' : '#28a745'))); ?>;">
                            <?php echo e($request->status === 'pending' ? 'Document Request Submitted' : 
                               ($request->status === 'processing' ? 'Document Being Processed' : 
                               ($request->status === 'ready_for_release' ? 'Document Ready' : 'Document Request Submitted'))); ?>

                        </h6>
                        <small class="text-muted"><?php echo e($request->created_at->format('M d, Y \a\t h:i A')); ?></small>
                    </div>
                </div>
                
                <!-- Details Grid -->
                <div class="row g-3">
                    <div class="col-md-6 col-lg-3">
                        <div class="d-flex align-items-center">
                            <div class="text-muted me-2" style="min-width: 20px;">
                                <i class="bi bi-person-badge"></i>
                            </div>
                            <div class="flex-grow-1">
                                <small class="text-muted text-uppercase fw-medium">Student</small>
                                <div class="fw-medium"><?php echo e($request->student->student_id); ?></div>
                                <small class="text-muted"><?php echo e($request->student->user->first_name); ?> <?php echo e($request->student->user->last_name); ?></small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 col-lg-3">
                        <div class="d-flex align-items-center">
                            <div class="text-muted me-2" style="min-width: 20px;">
                                <i class="bi bi-file-earmark-text"></i>
                            </div>
                            <div class="flex-grow-1">
                                <small class="text-muted text-uppercase fw-medium">Documents</small>
                                <div class="fw-medium"><?php echo e($request->requestItems->count()); ?> item(s)</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 col-lg-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center flex-grow-1">
                                <div class="text-muted me-2" style="min-width: 20px;">
                                    <i class="bi bi-clock-history"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <small class="text-muted text-uppercase fw-medium">Status</small>
                                    <div>
                                        <span class="badge px-2 py-1 
                                            <?php echo e($request->status === 'pending' ? 'bg-warning text-dark' : 
                                               ($request->status === 'processing' ? 'bg-info text-white' : 
                                               ($request->status === 'ready_for_release' ? 'bg-primary text-white' : 'bg-success text-white'))); ?>">
                                            <?php echo e(ucfirst(str_replace('_', ' ', $request->status))); ?>

                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-end ms-2">
                                <small class="text-muted text-uppercase fw-medium d-block">Total Price</small>
                                <div class="fw-bold text-success">
                                    <?php if($request->total_cost > 0): ?>
                                        ₱<?php echo e(number_format($request->total_cost, 2)); ?>

                                    <?php else: ?>
                                        Free
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 col-lg-3">
                        <div class="d-flex align-items-center">
                            <div class="text-muted me-2" style="min-width: 20px;">
                                <i class="bi bi-calendar-check"></i>
                            </div>
                            <div class="flex-grow-1">
                                <small class="text-muted text-uppercase fw-medium">Expected Release Date</small>
                                <div class="fw-medium">
                                    <?php if($request->expected_release_date): ?>
                                        <?php
                                            $releaseDate = is_string($request->expected_release_date) 
                                                ? \Carbon\Carbon::parse($request->expected_release_date) 
                                                : $request->expected_release_date;
                                        ?>
                                        <?php echo e($releaseDate->format('M d, Y')); ?>

                                        <br><small class="text-muted"><?php echo e($releaseDate->diffForHumans()); ?></small>
                                    <?php else: ?>
                                        <span class="text-muted">Not set</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Documents List -->
                <div class="border-top pt-3 mt-3">
                    <h6 class="mb-3 fw-bold">Requested Documents</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th class="fw-semibold text-muted" style="font-size: 0.85rem;">Document Type</th>
                                    <th class="text-center fw-semibold text-muted" style="font-size: 0.85rem;">Quantity</th>
                                    <th class="text-end fw-semibold text-muted" style="font-size: 0.85rem;">Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $request->requestItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td class="fw-medium"><?php echo e($item->document->type_document); ?></td>
                                        <td class="text-center"><?php echo e($item->quantity); ?></td>
                                        <td class="text-end fw-bold">
                                            <?php if($item->document->price > 0): ?>
                                                ₱<?php echo e(number_format($item->document->price * $item->quantity, 2)); ?>

                                            <?php else: ?>
                                                <span class="text-success fw-medium">Free</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <tr class="table-light">
                                    <td colspan="2" class="text-end fw-bold">Total Amount:</td>
                                    <td class="text-end fw-bold text-primary">₱<?php echo e(number_format($request->total_cost, 2)); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                
                <div class="border-top pt-3 mt-3">
                    <h6 class="mb-3 fw-bold">Request Information</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <tbody>
                                <tr>
                                    <td class="fw-semibold text-muted" style="width: 30%; border: none; padding: 0.5rem 0;">Requester:</td>
                                    <td class="fw-medium" style="border: none; padding: 0.5rem 0;"><?php echo e($request->student->user->first_name); ?> <?php echo e($request->student->user->last_name); ?></td>
                                </tr>
                                <?php if($request->student->course): ?>
                                <tr>
                                    <td class="fw-semibold text-muted" style="border: none; padding: 0.5rem 0;">Course:</td>
                                    <td class="fw-medium" style="border: none; padding: 0.5rem 0;"><?php echo e($request->student->course); ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if($request->reason): ?>
                                <tr>
                                    <td class="fw-semibold text-muted" style="border: none; padding: 0.5rem 0;">Reason:</td>
                                    <td class="fw-medium" style="border: none; padding: 0.5rem 0;"><?php echo e($request->reason); ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if($request->remarks): ?>
                                <tr>
                                    <td class="fw-semibold text-muted" style="border: none; padding: 0.5rem 0;">Remarks:</td>
                                    <td class="fw-medium text-muted" style="border: none; padding: 0.5rem 0;"><?php echo e($request->remarks); ?></td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- APK Files Section -->
                <div class="border-top pt-3 mt-3">
                    <div class="d-flex align-items-start">
                        <div class="text-primary me-3 mt-1">
                            <i class="bi bi-phone" style="font-size: 1.2rem;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-2 fw-bold">Mobile App Access</h6>
                            <div class="bg-light rounded p-3 mb-3">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <small class="text-muted fw-medium">Quick Setup:</small>
                                        <p class="mb-1 small">
                                            1. Download → 2. Install → 3. Login to your account
                                        </p>
                                        <small class="text-muted">
                                            <i class="bi bi-info-circle me-1"></i>
                                            Enable "Unknown sources" in Android settings for installation
                                        </small>
                                    </div>
                                    <div class="col-md-4 text-md-end mt-2 mt-md-0">
                                        <?php
                                            $apkPath = public_path('apk');
                                            $apkFiles = [];
                                            if (is_dir($apkPath)) {
                                                $apkFiles = array_filter(scandir($apkPath), function($file) {
                                                    return pathinfo($file, PATHINFO_EXTENSION) === 'apk';
                                                });
                                            }
                                        ?>
                                        
                                        <?php if(!empty($apkFiles)): ?>
                                            <?php $firstApk = reset($apkFiles); ?>
                                            <form action="<?php echo e(route('student.download.apk')); ?>" method="POST" class="d-inline">
                                                <?php echo csrf_field(); ?>
                                                <input type="hidden" name="reference_no" value="<?php echo e($request->reference_no); ?>">
                                                <input type="hidden" name="apk_file" value="<?php echo e($firstApk); ?>">
                                                <button type="submit" class="btn btn-success btn-sm">
                                                    <i class="bi bi-download me-1"></i>Download App
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <small class="text-muted">No apps available</small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    
    <?php if($pendingRequest && $pendingRequest->status === 'pending'): ?>
        <div class="row justify-content-center mt-4">
            <div class="col-md-8">
                <div class="card shadow-sm border-info">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="bi bi-clock me-2"></i>Awaiting Registrar Approval</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="fw-semibold text-muted" style="font-size: 0.85rem;">Document Type</th>
                                                <th class="text-center fw-semibold text-muted" style="font-size: 0.85rem;">Quantity</th>
                                                <th class="text-end fw-semibold text-muted" style="font-size: 0.85rem;">Price</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__currentLoopData = $pendingRequest->requestItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr>
                                                    <td class="fw-medium"><?php echo e($item->document->type_document); ?></td>
                                                    <td class="text-center"><?php echo e($item->quantity); ?></td>
                                                    <td class="text-end fw-bold">
                                                        <?php if($item->document->price > 0): ?>
                                                            ₱<?php echo e(number_format($item->document->price * $item->quantity, 2)); ?>

                                                        <?php else: ?>
                                                            <span class="text-success fw-medium">Free</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                </div>
                                
                                <div class="mt-3">
                                    <strong>Requester:</strong> <?php echo e($pendingRequest->student->user->first_name); ?> <?php echo e($pendingRequest->student->user->last_name); ?><br>
                                    <strong>Student ID:</strong> <?php echo e($pendingRequest->student->student_id); ?><br>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i>
                                    <strong>Your request is being reviewed by the Registrar.</strong><br>
                                    You will receive payment instructions once your request is approved.
                                </div>
                                <small class="text-muted">
                                    Please check back later or monitor your email for updates.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    
    <?php if($pendingRequest && $pendingRequest->status === 'registrar_approved' && $pendingRequest->total_cost > 0 && !$pendingRequest->payment_confirmed): ?>
        <div class="row justify-content-center mt-4">
            <div class="col-md-8">
                <div class="card shadow-sm border-warning">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0"><i class="bi bi-cash-coin me-2"></i>Awaiting Payment Confirmation</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="fw-semibold text-muted" style="font-size: 0.85rem;">Document Type</th>
                                                <th class="text-center fw-semibold text-muted" style="font-size: 0.85rem;">Quantity</th>
                                                <th class="text-end fw-semibold text-muted" style="font-size: 0.85rem;">Price</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__currentLoopData = $pendingRequest->requestItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr>
                                                    <td class="fw-medium"><?php echo e($item->document->type_document); ?></td>
                                                    <td class="text-center"><?php echo e($item->quantity); ?></td>
                                                    <td class="text-end fw-bold">
                                                        <?php if($item->document->price > 0): ?>
                                                            ₱<?php echo e(number_format($item->document->price * $item->quantity, 2)); ?>

                                                        <?php else: ?>
                                                            <span class="text-success fw-medium">Free</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                </div>
                                
                                <div class="mt-3">
                                    <strong>Requester:</strong> <?php echo e($pendingRequest->student->user->first_name); ?> <?php echo e($pendingRequest->student->user->last_name); ?><br>
                                    <strong>Student ID:</strong> <?php echo e($pendingRequest->student->student_id); ?><br>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="payment-info">
                                    <strong>Payment Breakdown:</strong>
                                    <div class="mt-2">
                                        <?php $__currentLoopData = $pendingRequest->requestItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="d-flex justify-content-between mb-1">
                                                <small><?php echo e($item->document->type_document); ?> (x<?php echo e($item->quantity); ?>)</small>
                                                <small>₱<?php echo e(number_format($item->document->price * $item->quantity, 2)); ?></small>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <hr class="my-2">
                                        <div class="d-flex justify-content-between fw-bold">
                                            <span>Total Amount:</span>
                                            <span class="text-success">₱<?php echo e(number_format($pendingRequest->total_cost, 2)); ?></span>
                                        </div>
                                    </div>
                                    <br>
                                    <small class="text-muted">
                                        Payment must be made at the Accounting Office before your documents can be processed.
                                    </small>
                                </div>
                                <br><strong>Total Quantity:</strong> <?php echo e($pendingRequest->requestItems->sum('quantity')); ?>

                                <br><br>
                                <div class="text-center">
                                    <img src="<?php echo e(asset('images/qr-display.jpg')); ?>" alt="Payment QR Code" class="img-fluid" style="max-width: 200px; max-height: 200px;">
                                    <p class="text-muted mt-2 small">Scan QR code for payment</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-3 p-3 bg-light rounded">
                            <h6 class="text-warning mb-2"><i class="bi bi-info-circle me-2"></i>Payment Instructions:</h6>
                            <ol class="mb-3 small">
                                <li>Scan the QR code above or proceed to the Accounting Office to make payment</li>
                                <li>Make payment for the total amount of <strong>₱<?php echo e(number_format($pendingRequest->total_cost, 2)); ?></strong></li>
                                <li>Upload your payment receipt below for verification</li>
                            </ol>
                            
                            <?php if($pendingRequest->payment_receipt_path): ?>
                                <div class="alert alert-info">
                                    <i class="bi bi-clock me-2"></i>
                                    Receipt uploaded and awaiting approval from accounting.
                                </div>
                            <?php else: ?>
                                <form method="POST" action="<?php echo e(route('student.upload.receipt', $pendingRequest)); ?>" enctype="multipart/form-data">
                                    <?php echo csrf_field(); ?>
                                    <div class="mb-3">
                                        <label for="payment_receipt" class="form-label">Upload Payment Receipt</label>
                                        <input type="file" class="form-control" id="payment_receipt" name="payment_receipt"
                                               accept="image/*" required>
                                        <div class="form-text">Accepted formats: JPG, PNG, GIF. Max size: 2MB</div>
                                    </div>
                                    <div class="text-center">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-upload me-2"></i>Upload Receipt
                                        </button>
                                    </div>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    
    <?php if(!$pendingRequest): ?>
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-file-earmark-plus me-2"></i>Request Documents</h5>
            </div>
            <div class="card-body">
                <form id="document-request-form" action="<?php echo e(route('student.request.document.submit')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    
                    <!-- Documents Section -->
                    <div class="form-section">
                        <h5 class="section-title">
                            <i class="bi bi-file-earmark-text me-2"></i>
                            Documents to Request
                        </h5>
                        
                        <div id="documents-container">
                            <!-- Initial document item -->
                            <div class="document-item mb-3 p-3 border rounded">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <strong>Document 1</strong>
                                    <button type="button" class="btn btn-sm btn-danger remove-document" style="display: none;">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                                <div class="row g-2">
                                    <div class="col-md-8">
                                        <select class="form-select document-select" name="documents[0][document_id]" required>
                                            <option value="" disabled selected>-- Choose document --</option>
                                            <?php $__currentLoopData = $documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($doc->id); ?>" data-price="<?php echo e($doc->price); ?>" data-name="<?php echo e($doc->type_document); ?>">
                                                    <?php echo e($doc->type_document); ?> - <?php echo e($doc->price > 0 ? '₱' . number_format($doc->price, 2) : 'Free'); ?>

                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="number" class="form-control quantity-input" name="documents[0][quantity]" placeholder="Qty" min="1" max="10" value="1" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex gap-2 mb-3">
                            <button type="button" class="btn btn-outline-primary" id="add-document">
                                <i class="bi bi-plus-circle me-1"></i>Add Another Document
                            </button>
                        </div>
                        
                        <div class="total-cost-section bg-light p-3 rounded">
                            <h5 class="mb-0">Total Cost: <span id="total-cost" class="text-success fw-bold">₱0.00</span></h5>
                            <div id="cost-breakdown" class="mt-2 small text-muted"></div>
                        </div>
                    </div>

                    <!-- Reason for Request Section -->
                    <div class="form-section mt-4">
                        <h5 class="section-title">
                            <i class="bi bi-clipboard-check me-2"></i>
                            Reason for Request
                        </h5>
                        
                        <div class="mb-3">
                            <label for="reason_select" class="form-label">Reason for Request *</label>
                            <select name="reason_select" id="reason_select" class="form-select" required>
                                <option value="" disabled selected>-- Select Reason --</option>
                                <option value="Graduate studies (local or abroad)">Graduate studies (local or abroad)</option>
                                <option value="Transfer to another school">Transfer to another school</option>
                                <option value="Credential evaluation (e.g., for PR, visa)">Credential evaluation (e.g., for PR, visa)</option>
                                <option value="Scholarship applications">Scholarship applications</option>
                                <option value="Academic standing verification">Academic standing verification</option>
                                <option value="Personal monitoring">Personal monitoring</option>
                                <option value="Internship or OJT requirements">Internship or OJT requirements</option>
                                <option value="Credit evaluation when transferring or studying abroad">Credit evaluation when transferring or studying abroad</option>
                                <option value="CHED or international credential assessments">CHED or international credential assessments</option>
                                <option value="Scholarship/grant validation">Scholarship/grant validation</option>
                                <option value="Visa applications (student visa proof)">Visa applications (student visa proof)</option>
                                <option value="SSS or PhilHealth requirements">SSS or PhilHealth requirements</option>
                                <option value="OJT/internship verification">OJT/internship verification</option>
                                <option value="Job applications">Job applications</option>
                                <option value="Graduate studies">Graduate studies</option>
                                <option value="Visa or immigration processes">Visa or immigration processes</option>
                                <option value="Lost/damaged original diploma">Lost/damaged original diploma</option>
                                <option value="Employment or PR applications">Employment or PR applications</option>
                                <option value="Study abroad or licensure exams (e.g., PRC)">Study abroad or licensure exams (e.g., PRC)</option>
                                <option value="Other">Other (please specify below)</option>
                            </select>
                        </div>

                        <!-- Show textarea if Other is selected -->
                        <div class="mb-3" id="other_reason_container" style="display:none;">
                            <label for="other_reason" class="form-label">Please specify your reason</label>
                            <textarea name="other_reason" id="other_reason" class="form-control" rows="3" 
                                      placeholder="Type your reason here..." disabled></textarea>
                        </div>

                        <!-- Hidden field for final reason -->
                        <input type="hidden" id="reason" name="reason" value="">
                    </div>
                    
                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-send me-2"></i>Submit Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            <strong>You have a pending document request.</strong> Please wait for your current request to be completed before submitting a new one.
        </div>
    <?php endif; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let documentIndex = 1;

        function updateTotalCost() {
            let total = 0;
            let breakdownHtml = '';

            document.querySelectorAll('.document-item').forEach((item, index) => {
                const select = item.querySelector('.document-select');
                const quantity = item.querySelector('.quantity-input');
                const selectedOption = select.options[select.selectedIndex];
                
                if (selectedOption && selectedOption.value) {
                    const price = parseFloat(selectedOption.dataset.price) || 0;
                    const qty = parseInt(quantity.value) || 1;
                    const itemTotal = price * qty;
                    total += itemTotal;
                    
                    if (itemTotal > 0) {
                        breakdownHtml += `<div>${selectedOption.dataset.name} x${qty}: ₱${itemTotal.toFixed(2)}</div>`;
                    }
                }
            });

            document.getElementById('total-cost').textContent = '₱' + total.toFixed(2);

            const breakdownElement = document.getElementById('cost-breakdown');
            if (breakdownHtml) {
                breakdownElement.innerHTML = breakdownHtml;
            } else {
                breakdownElement.innerHTML = '';
            }
        }

        function updateRemoveButtons() {
            const items = document.querySelectorAll('.document-item');
            items.forEach((item, index) => {
                const removeBtn = item.querySelector('.remove-document');
                if (items.length > 1) {
                    removeBtn.style.display = 'inline-block';
                } else {
                    removeBtn.style.display = 'none';
                }
            });
        }

        function attachDocumentEvents(item) {
            const select = item.querySelector('.document-select');
            const quantity = item.querySelector('.quantity-input');
            const removeBtn = item.querySelector('.remove-document');

            select.addEventListener('change', updateTotalCost);
            quantity.addEventListener('input', updateTotalCost);

            removeBtn.addEventListener('click', function() {
                item.remove();
                updateTotalCost();
                updateRemoveButtons();
                // Re-index the remaining items
                document.querySelectorAll('.document-item').forEach((item, index) => {
                    item.querySelector('.document-select').name = `documents[${index}][document_id]`;
                    item.querySelector('.quantity-input').name = `documents[${index}][quantity]`;
                    item.querySelector('strong').textContent = `Document ${index + 1}`;
                });
                documentIndex = document.querySelectorAll('.document-item').length;
            });
        }

        document.getElementById('add-document').addEventListener('click', function() {
            const container = document.getElementById('documents-container');
            const newItem = document.createElement('div');
            newItem.className = 'document-item mb-3 p-3 border rounded';
            newItem.innerHTML = `
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <strong>Document ${documentIndex + 1}</strong>
                    <button type="button" class="btn btn-sm btn-danger remove-document">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
                <div class="row g-2">
                    <div class="col-md-8">
                        <select class="form-select document-select" name="documents[${documentIndex}][document_id]" required>
                            <option value="">Choose document...</option>
                            <?php $__currentLoopData = $documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($doc->id); ?>" data-price="<?php echo e($doc->price); ?>" data-name="<?php echo e($doc->type_document); ?>">
                                    <?php echo e($doc->type_document); ?> - <?php echo e($doc->price > 0 ? '₱' . number_format($doc->price, 2) : 'Free'); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="number" class="form-control quantity-input" name="documents[${documentIndex}][quantity]" placeholder="Qty" min="1" max="10" value="1" required>
                    </div>
                </div>
            `;
            container.appendChild(newItem);
            documentIndex++;
            updateRemoveButtons();
            attachDocumentEvents(newItem);
        });

        // Attach events to initial document item
        document.querySelectorAll('.document-item').forEach(item => {
            attachDocumentEvents(item);
        });

        // Initial total cost calculation
        updateTotalCost();
        updateRemoveButtons();

        // Reason field functionality
        const reasonSelect = document.getElementById('reason_select');
        const otherReasonContainer = document.getElementById('other_reason_container');
        const otherReasonTextarea = document.getElementById('other_reason');
        const hiddenReasonField = document.getElementById('reason');

        if (reasonSelect) {
            reasonSelect.addEventListener('change', function() {
                const selectedReason = this.value;
                
                if (selectedReason === 'Other') {
                    otherReasonContainer.style.display = 'block';
                    otherReasonTextarea.disabled = false;
                    otherReasonTextarea.required = true;
                    hiddenReasonField.value = '';
                } else {
                    otherReasonContainer.style.display = 'none';
                    otherReasonTextarea.disabled = true;
                    otherReasonTextarea.required = false;
                    otherReasonTextarea.value = '';
                    hiddenReasonField.value = selectedReason;
                }
            });

            // Handle other reason textarea
            otherReasonTextarea.addEventListener('input', function() {
                if (reasonSelect.value === 'Other' && this.value.trim() !== '') {
                    hiddenReasonField.value = this.value.trim();
                } else if (reasonSelect.value === 'Other') {
                    hiddenReasonField.value = '';
                }
            });
        }

        // Form validation and UX before submit
        document.getElementById('document-request-form').addEventListener('submit', function(e) {
            // Check if at least one document is selected
            const selectedDocuments = document.querySelectorAll('.document-select');
            let hasValidDocument = false;
            
            selectedDocuments.forEach(select => {
                if (select.value) {
                    hasValidDocument = true;
                }
            });
            
            if (!hasValidDocument) {
                e.preventDefault();
                alert('Please select at least one document.');
                return;
            }

            // Check if reason is selected
            const reasonSelect = document.getElementById('reason_select');
            const hiddenReasonField = document.getElementById('reason');
            
            if (!reasonSelect.value) {
                e.preventDefault();
                alert('Please select a reason for your document request.');
                reasonSelect.focus();
                return;
            }

            // If "Other" is selected, check if other reason is provided
            if (reasonSelect.value === 'Other') {
                const otherReasonTextarea = document.getElementById('other_reason');
                if (!otherReasonTextarea.value.trim()) {
                    e.preventDefault();
                    alert('Please specify your reason for the document request.');
                    otherReasonTextarea.focus();
                    return;
                }
                // Update hidden field with the other reason
                hiddenReasonField.value = otherReasonTextarea.value.trim();
            }

            // Submit button loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalHtml = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Processing Request...';
            submitBtn.disabled = true;
            
            // Re-enable if there's an error (in case of validation failure)
            setTimeout(() => {
                submitBtn.innerHTML = originalHtml;
                submitBtn.disabled = false;
            }, 5000);
        });
    });

    // Real-time updates for request status
    <?php if($pendingRequest): ?>
        // Load Pusher
        const pusherScript = document.createElement('script');
        pusherScript.src = 'https://js.pusher.com/8.2.0/pusher.min.js';
        document.head.appendChild(pusherScript);

        pusherScript.onload = function() {
            // Initialize Pusher
            const pusher = new Pusher('<?php echo e(config('broadcasting.connections.pusher.key')); ?>', {
                cluster: '<?php echo e(config('broadcasting.connections.pusher.options.cluster')); ?>',
                encrypted: true
            });

            // Subscribe to the specific request channel
            const requestChannel = pusher.subscribe('request-<?php echo e($pendingRequest->reference_no); ?>');

            // Listen for status updates
            requestChannel.bind('realtime.notification', function(data) {
                console.log('Received request update:', data);

                if (data.data && data.data.status_update) {
                    // Show notification
                    showStatusUpdateNotification(data.message, data.data.status);

                    // Update status display immediately
                    updateStatusDisplay(data.data.status);

                    // Auto-refresh the page after a delay to ensure all data is updated
                    setTimeout(() => {
                        window.location.reload();
                    }, 5000);
                }
            });

            // Function to show status update notifications
            function showStatusUpdateNotification(message, status) {
                // Create notification element
                const notification = document.createElement('div');
                notification.className = 'alert alert-info alert-dismissible fade show position-fixed';
                notification.style.cssText = `
                    top: 20px;
                    right: 20px;
                    z-index: 9999;
                    max-width: 400px;
                    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                `;
                notification.innerHTML = `
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Status Update:</strong> ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;

                document.body.appendChild(notification);

                // Auto-remove after 5 seconds
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.remove();
                    }
                }, 5000);
            }

            // Function to update status display immediately
            function updateStatusDisplay(newStatus) {
                // Find the status badge element
                const statusBadge = document.querySelector('.badge');
                if (statusBadge) {
                    // Update the badge text
                    statusBadge.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1).replace('_', ' ');
                    
                    // Remove existing bg-* classes
                    statusBadge.classList.remove('bg-warning', 'bg-info', 'bg-primary', 'bg-success', 'text-dark', 'text-white');
                    
                    // Add new classes based on status
                    const statusClasses = getStatusClasses(newStatus);
                    statusClasses.forEach(cls => statusBadge.classList.add(cls));
                }
            }

            // Helper function to get status classes
            function getStatusClasses(status) {
                switch(status) {
                    case 'pending': return ['bg-warning', 'text-dark'];
                    case 'processing': return ['bg-info', 'text-white'];
                    case 'ready_for_release': return ['bg-primary', 'text-white'];
                    case 'completed': return ['bg-success', 'text-white'];
                    default: return ['bg-secondary', 'text-white'];
                }
            }
        };
    <?php endif; ?>
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Nu-Regisv2\resources\views/student/request_document.blade.php ENDPATH**/ ?>