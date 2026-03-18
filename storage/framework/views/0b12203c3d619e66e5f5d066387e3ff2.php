

<?php $__env->startSection('title', 'QR Code Management - NU Lipa'); ?>

<?php $__env->startSection('content'); ?>
<style>
    .qr-table thead th {
        background: linear-gradient(135deg, #2c3192 0%, #1e2570 100%);
        color: #fff;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        border: none;
        padding: 1rem 0.75rem;
    }

    .qr-table tbody td {
        padding: 0.85rem 0.75rem;
        vertical-align: middle;
        border: none;
    }

    .qr-table-row:hover {
        background-color: rgba(44, 49, 146, 0.05);
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

    .qr-status-badge {
        font-size: 0.7rem;
        padding: 0.25rem 0.6rem;
        border-radius: 0.25rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .qr-uploaded {
        background: #d4edda;
        color: #155724;
    }

    .qr-pending {
        background: #fff3cd;
        color: #856404;
    }

    .qr-thumbnail {
        max-width: 60px;
        max-height: 60px;
        border-radius: 4px;
        cursor: pointer;
        border: 1px solid #dee2e6;
    }

    .upload-btn {
        border-radius: 6px;
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.2s ease-in-out;
        border: 1.5px solid;
        margin: 0 0.25rem;
    }

    .upload-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }

    .btn-primary.upload-btn {
        background-color: #0d6efd;
        border-color: #0d6efd;
        color: #fff;
    }

    .btn-primary.upload-btn:hover {
        background-color: #0b5ed7;
        border-color: #0a58ca;
    }

    .btn-info.upload-btn {
        background-color: #0dcaf0;
        border-color: #0dcaf0;
        color: #000;
    }

    .btn-info.upload-btn:hover {
        background-color: #31d5f8;
        border-color: #25cff2;
    }

    .dropzone-box {
        border: 2px dashed #b8c2d9;
        border-radius: 12px;
        background: #f8fbff;
        padding: 1.5rem;
        text-align: center;
        transition: all 0.2s ease-in-out;
        cursor: pointer;
    }

    .dropzone-box:hover {
        border-color: #2c3192;
        background: #f1f5ff;
    }

    .default-qr-preview {
        max-width: 220px;
        max-height: 220px;
        border-radius: 8px;
        border: 1px solid #dee2e6;
        background: #fff;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-gray-800"><i class="bi bi-qr-code me-2"></i>QR Code Management</h1>
                    <p class="text-muted mb-0">Upload or Replace Payment QR Codes</p>
                </div>
                <div class="d-flex gap-2">
                    <span class="badge" style="background:linear-gradient(135deg,#17a2b8,#138496)">
                        <?php echo e($pendingOnsiteRequests->count()); ?> Onsite Requests
                    </span>
                    <span class="badge" style="background:linear-gradient(135deg,#6f42c1,#5a32a3)">
                        <?php echo e($pendingStudentRequests->count()); ?> Student Requests
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
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-image me-2"></i>Default Payment QR Code</h6>
                    <small>This appears on Student and Onsite payment pages when no request-specific QR is set.</small>
                </div>
                <div class="card-body">
                    <div class="row g-4 align-items-center">
                        <div class="col-md-5 text-center">
                            <img src="<?php echo e($defaultQrUrl); ?>" alt="Default Payment QR" class="default-qr-preview mb-2" id="defaultQrPreviewImage">
                            <div class="text-muted small">Current default QR code</div>
                        </div>
                        <div class="col-md-7">
                            <form method="POST" action="<?php echo e(route('accounting.qr.default.upload')); ?>" enctype="multipart/form-data">
                                <?php echo csrf_field(); ?>
                                <label for="default_qr_code" class="dropzone-box w-100 mb-3">
                                    <i class="bi bi-cloud-arrow-up" style="font-size: 1.8rem; color: #2c3192;"></i>
                                    <div class="fw-semibold mt-2">Click or drag & drop a new QR image here</div>
                                    <div class="text-muted small">JPEG, PNG, GIF, WEBP - max 5 MB</div>
                                </label>
                                <input type="file" class="form-control d-none" id="default_qr_code" name="default_qr_code" accept="image/*" required>
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary upload-btn">
                                        <i class="bi bi-arrow-repeat me-1"></i>Replace Default QR
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-body pb-0">
                    <ul class="nav nav-tabs" id="qrTabs" role="tablist">
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

                <div class="tab-content" id="qrTabContent">

                    
                    <div class="tab-pane fade show active" id="onsite-pane" role="tabpanel">
                        <div class="card-body">
                            <?php if($pendingOnsiteRequests->count() > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle qr-table">
                                        <thead>
                                            <tr>
                                                <th>Reference Code</th>
                                                <th>Full Name</th>
                                                <th>Course</th>
                                                <th>QR Code Status</th>
                                                <th>QR Code Preview</th>
                                                <th>Action</th>
                                                <th>Updated</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__currentLoopData = $pendingOnsiteRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr class="qr-table-row">
                                                    <td>
                                                        <strong><?php echo e($request->ref_code ?? $request->id); ?></strong>
                                                        <br><small class="text-muted"><?php echo e($request->queue_number ?? ''); ?></small>
                                                    </td>
                                                    <td>
                                                        <?php echo e($request->full_name); ?>

                                                    </td>
                                                    <td>
                                                        <small class="text-muted"><?php echo e($request->course); ?> - <?php echo e($request->year_level); ?></small>
                                                    </td>
                                                    <td>
                                                        <?php if($request->qr_code_path): ?>
                                                            <span class="qr-status-badge qr-uploaded">✓ Uploaded</span>
                                                        <?php else: ?>
                                                            <span class="qr-status-badge qr-pending">⚠ Pending</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if($request->qr_code_path): ?>
                                                            <img src="<?php echo e($request->qr_code_path); ?>"
                                                                 alt="QR Code"
                                                                 class="qr-thumbnail"
                                                                 onclick="openQRPreview('<?php echo e($request->qr_code_path); ?>', '<?php echo e($request->ref_code ?? $request->id); ?>')">
                                                        <?php else: ?>
                                                            <span class="text-muted">No QR Code</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if($request->qr_code_path): ?>
                                                            <button type="button" class="btn btn-info btn-sm upload-btn" onclick="openQRUploadModal('onsite', <?php echo e($request->id); ?>, '<?php echo e($request->ref_code ?? $request->id); ?>')">
                                                                <i class="bi bi-pencil-square me-1"></i>Replace
                                                            </button>
                                                        <?php else: ?>
                                                            <button type="button" class="btn btn-primary btn-sm upload-btn" onclick="openQRUploadModal('onsite', <?php echo e($request->id); ?>, '<?php echo e($request->ref_code ?? $request->id); ?>')">
                                                                <i class="bi bi-cloud-upload me-1"></i>Upload
                                                            </button>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <small class="text-muted"><?php echo e($request->updated_at->diffForHumans()); ?></small>
                                                    </td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-5">
                                    <i class="bi bi-building" style="font-size:3rem;color:#dee2e6;"></i>
                                    <h5 class="mt-3 text-muted">No Onsite Requests</h5>
                                    <p class="text-muted">There are no pending onsite requests waiting for QR codes.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    
                    <div class="tab-pane fade" id="student-pane" role="tabpanel">
                        <div class="card-body">
                            <?php if($pendingStudentRequests->count() > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle qr-table">
                                        <thead>
                                            <tr>
                                                <th>Reference No.</th>
                                                <th>Student Name</th>
                                                <th>Student ID</th>
                                                <th>QR Code Status</th>
                                                <th>QR Code Preview</th>
                                                <th>Action</th>
                                                <th>Updated</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__currentLoopData = $pendingStudentRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr class="qr-table-row">
                                                    <td>
                                                        <strong><?php echo e($request->reference_no ?? 'N/A'); ?></strong>
                                                    </td>
                                                    <td>
                                                        <?php echo e($request->student->user->first_name); ?> <?php echo e($request->student->user->last_name); ?>

                                                    </td>
                                                    <td>
                                                        <small class="text-muted"><?php echo e($request->student->student_id); ?></small>
                                                    </td>
                                                    <td>
                                                        <?php if($request->qr_code_path): ?>
                                                            <span class="qr-status-badge qr-uploaded">✓ Uploaded</span>
                                                        <?php else: ?>
                                                            <span class="qr-status-badge qr-pending">⚠ Pending</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if($request->qr_code_path): ?>
                                                            <img src="<?php echo e($request->qr_code_path); ?>"
                                                                 alt="QR Code"
                                                                 class="qr-thumbnail"
                                                                 onclick="openQRPreview('<?php echo e($request->qr_code_path); ?>', '<?php echo e($request->reference_no); ?>')">
                                                        <?php else: ?>
                                                            <span class="text-muted">No QR Code</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if($request->qr_code_path): ?>
                                                            <button type="button" class="btn btn-info btn-sm upload-btn" onclick="openQRUploadModal('student', <?php echo e($request->id); ?>, '<?php echo e($request->reference_no); ?>')">
                                                                <i class="bi bi-pencil-square me-1"></i>Replace
                                                            </button>
                                                        <?php else: ?>
                                                            <button type="button" class="btn btn-primary btn-sm upload-btn" onclick="openQRUploadModal('student', <?php echo e($request->id); ?>, '<?php echo e($request->reference_no); ?>')">
                                                                <i class="bi bi-cloud-upload me-1"></i>Upload
                                                            </button>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <small class="text-muted"><?php echo e($request->updated_at->diffForHumans()); ?></small>
                                                    </td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-5">
                                    <i class="bi bi-person-badge" style="font-size:3rem;color:#dee2e6;"></i>
                                    <h5 class="mt-3 text-muted">No Student Requests</h5>
                                    <p class="text-muted">There are no pending student requests waiting for QR codes.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- QR Code Preview Modal -->
<div class="modal fade" id="qrPreviewModal" tabindex="-1" aria-labelledby="qrPreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="qrPreviewModalLabel">QR Code Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="qrPreviewImage" src="" alt="QR Code" class="img-fluid" style="max-height: 70vh;">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- QR Code Upload Modal -->
<div class="modal fade" id="qrUploadModal" tabindex="-1" aria-labelledby="qrUploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="qrUploadModalLabel">Upload QR Code</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="qrUploadForm" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <input type="hidden" id="requestType" name="request_type">
                    <input type="hidden" id="requestId" name="request_id">
                    
                    <div class="mb-3">
                        <label for="qrUploadInput" class="form-label">Select QR Code Image</label>
                        <input class="form-control" type="file" id="qrUploadInput" name="qr_code" accept="image/*" required>
                        <small class="form-text text-muted">Accepted formats: JPG, PNG, GIF. Max size: 2MB</small>
                    </div>
                    <div id="previewContainer" class="text-center mb-3" style="display: none;">
                        <label class="form-label">Preview</label>
                        <img id="qrPreview" src="" alt="QR Code Preview" style="max-width: 200px; max-height: 200px; border-radius: 4px;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-cloud-upload me-1"></i>Upload QR Code
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Open QR preview modal
    function openQRPreview(imageSrc, reference) {
        document.getElementById('qrPreviewImage').src = imageSrc;
        document.getElementById('qrPreviewModalLabel').textContent = 'QR Code - ' + reference;
        new bootstrap.Modal(document.getElementById('qrPreviewModal')).show();
    }

    // Open QR upload modal
    function openQRUploadModal(type, requestId, reference) {
        document.getElementById('requestType').value = type;
        document.getElementById('requestId').value = requestId;
        document.getElementById('qrUploadModalLabel').textContent = 'Upload QR Code - ' + reference;
        document.getElementById('qrUploadForm').reset();
        document.getElementById('previewContainer').style.display = 'none';
        new bootstrap.Modal(document.getElementById('qrUploadModal')).show();
    }

    // Preview image before upload
    document.getElementById('qrUploadInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                document.getElementById('qrPreview').src = event.target.result;
                document.getElementById('previewContainer').style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });

    // Handle QR code upload
    document.getElementById('qrUploadForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const requestType = document.getElementById('requestType').value;
        const requestId = document.getElementById('requestId').value;
        
        const url = `/accounting/qr-code/upload/${requestType}/${requestId}`;
        
        fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('qrUploadModal')).hide();
                alert('QR code uploaded successfully!');
                location.reload();
            } else {
                alert('Error uploading QR code: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            alert('Error uploading QR code: ' + error);
        });
    });

    // Preview for default QR replacement
    document.getElementById('default_qr_code').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) {
            return;
        }

        const reader = new FileReader();
        reader.onload = function(event) {
            document.getElementById('defaultQrPreviewImage').src = event.target.result;
        };
        reader.readAsDataURL(file);
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.accounting', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Brill\nu-regis\resources\views/accounting/qr-manage.blade.php ENDPATH**/ ?>