

<?php $__env->startSection('content'); ?>
<style>
    .document-card {
        background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
        border-radius: 15px;
        padding: 2rem;
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        border: none;
        transition: all 0.3s ease;
        height: 100%;
    }
    
    .document-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.15);
    }
    
    .stats-number {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    
    .stats-label {
        font-size: 1rem;
        font-weight: 500;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .stats-icon {
        font-size: 3rem;
        opacity: 0.1;
        position: absolute;
        top: 1rem;
        right: 1rem;
    }
    
    .primary-stat { color: #2c3192; }
    .success-stat { color: #28a745; }
    .warning-stat { color: #ffc107; }
    .danger-stat { color: #dc3545; }
    .info-stat { color: #17a2b8; }
    
    .table-documents {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }
    
    .table-documents thead th {
        background: linear-gradient(135deg, #2c3192 0%, #1e2570 100%);
        color: #fff;
        font-weight: 600;
        border: none;
        padding: 1rem;
    }
    
    .table-documents tbody td {
        padding: 0.75rem 1rem;
        border-top: 1px solid #e9ecef;
        vertical-align: middle;
    }

    .action-btn {
        background: linear-gradient(135deg, #2c3192 0%, #1e2570 100%);
        color: white;
        border: none;
        border-radius: 8px;
        padding: 0.5rem 1rem;
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
    }

    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(44, 49, 146, 0.3);
        color: white;
    }

    .action-btn.success {
        background: linear-gradient(135deg, #28a745 0%, #20873a 100%);
    }

    .action-btn.success:hover {
        box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
    }

    .price-badge {
        background: linear-gradient(135deg, #2c3192 0%, #1e2570 100%);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 25px;
        font-weight: 600;
        font-size: 1.1rem;
    }

    .document-row {
        transition: all 0.3s ease;
    }

    .document-row:hover {
        background-color: rgba(44, 49, 146, 0.05);
        transform: scale(1.01);
    }

    .search-box {
        border-radius: 25px;
        border: 2px solid #e9ecef;
        padding: 0.75rem 1.5rem;
        transition: all 0.3s ease;
    }

    .search-box:focus {
        border-color: #2c3192;
        box-shadow: 0 0 0 0.2rem rgba(44, 49, 146, 0.25);
    }

    .filter-badge {
        background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
        color: white;
        padding: 0.3rem 0.8rem;
        border-radius: 15px;
        font-size: 0.85rem;
        font-weight: 500;
    }

    @media print {
        .no-print {
            display: none !important;
        }
        
        .document-card, .table-documents {
            box-shadow: none !important;
            border: 1px solid #ddd !important;
        }
    }
</style>

<div class="container-fluid mt-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-0" style="color: #2c3192; font-weight: 700;">
                        <i class="fas fa-file-alt me-2"></i>Document Management
                    </h2>
                    <p class="text-muted mt-2">Manage available document types and pricing</p>
                </div>
                <div class="no-print">
                    <button class="btn action-btn success me-2" data-bs-toggle="modal" data-bs-target="#addDocumentModal">
                        <i class="fas fa-plus me-2"></i>Add Document Type
                    </button>
                    <button class="btn action-btn" onclick="window.print()">
                        <i class="fas fa-print me-2"></i>Print List
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Document Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="document-card position-relative">
                <i class="fas fa-file-alt stats-icon primary-stat"></i>
                <div class="stats-number primary-stat"><?php echo e($documents->total()); ?></div>
                <div class="stats-label">Total Documents</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="document-card position-relative">
                <i class="fas fa-peso-sign stats-icon success-stat"></i>
                <div class="stats-number success-stat">₱<?php echo e(number_format(\App\Models\Document::avg('price') ?? 0, 2)); ?></div>
                <div class="stats-label">Average Price</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="document-card position-relative">
                <i class="fas fa-arrow-up stats-icon info-stat"></i>
                <div class="stats-number info-stat">₱<?php echo e(number_format(\App\Models\Document::max('price') ?? 0, 2)); ?></div>
                <div class="stats-label">Highest Price</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="document-card position-relative">
                <i class="fas fa-arrow-down stats-icon warning-stat"></i>
                <div class="stats-number warning-stat">₱<?php echo e(number_format(\App\Models\Document::min('price') ?? 0, 2)); ?></div>
                <div class="stats-label">Lowest Price</div>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="row mb-4 no-print">
        <div class="col-md-8">
            <div class="input-group">
                <input type="text" class="form-control search-box" id="documentSearch" 
                       placeholder="Search documents by name or type...">
                <button class="btn action-btn" type="button" onclick="searchDocuments()">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
        <div class="col-md-4">
            <select class="form-select" id="priceFilter" onchange="filterByPrice()">
                <option value="">All Price Ranges</option>
                <option value="0-50">₱0 - ₱50</option>
                <option value="51-100">₱51 - ₱100</option>
                <option value="101-200">₱101 - ₱200</option>
                <option value="201-500">₱201 - ₱500</option>
                <option value="501+">₱501+</option>
            </select>
        </div>
    </div>

    <!-- Documents Table -->
    <div class="row">
        <div class="col-12">
            <div class="document-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0">
                        <i class="fas fa-list me-2" style="color: #2c3192;"></i>Document Types
                    </h4>
                    <span class="filter-badge">
                        <?php echo e($documents->total()); ?> Total Documents
                    </span>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-documents" id="documentsTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Document Type</th>
                                <th>Price</th>
                                <th>Requests Count</th>
                                <th>Revenue Generated</th>
                                <th>Status</th>
                                <th class="no-print">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="document-row" data-price="<?php echo e($document->price); ?>">
                                <td><strong><?php echo e($document->id); ?></strong></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-file-alt me-2" style="color: #2c3192;"></i>
                                        <div>
                                            <strong><?php echo e($document->type_document); ?></strong>
                                            <?php if($document->description): ?>
                                            <br><small class="text-muted"><?php echo e(Str::limit($document->description, 50)); ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="price-badge">₱<?php echo e(number_format($document->price, 2)); ?></span>
                                </td>
                                <td>
                                    <?php
                                        $requestCount = \App\Models\StudentRequestItem::where('document_id', $document->id)->count();
                                    ?>
                                    <span class="badge bg-info"><?php echo e(number_format($requestCount)); ?> requests</span>
                                </td>
                                <td>
                                    <?php
                                        $revenue = \App\Models\StudentRequestItem::where('document_id', $document->id)
                                                    ->join('student_requests', 'student_request_items.student_request_id', '=', 'student_requests.id')
                                                    ->where('student_requests.payment_confirmed', true)
                                                    ->sum(\DB::raw('student_request_items.price * student_request_items.quantity'));
                                    ?>
                                    <strong style="color: #28a745;">₱<?php echo e(number_format($revenue, 2)); ?></strong>
                                </td>
                                <td>
                                    <?php if($document->is_active ?? true): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td class="no-print">
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                                onclick="editDocument(<?php echo e($document->id); ?>)" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-info" 
                                                onclick="viewDocumentStats(<?php echo e($document->id); ?>)" title="View Stats">
                                            <i class="fas fa-chart-bar"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-<?php echo e($document->is_active ?? true ? 'warning' : 'success'); ?>" 
                                                onclick="toggleDocumentStatus(<?php echo e($document->id); ?>)" 
                                                title="<?php echo e($document->is_active ?? true ? 'Deactivate' : 'Activate'); ?>">
                                            <i class="fas fa-<?php echo e($document->is_active ?? true ? 'pause' : 'play'); ?>"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <i class="fas fa-inbox fa-3x mb-3 d-block" style="opacity: 0.3;"></i>
                                    <h5>No documents found</h5>
                                    <p>Start by adding your first document type.</p>
                                    <button class="btn action-btn success" data-bs-toggle="modal" data-bs-target="#addDocumentModal">
                                        <i class="fas fa-plus me-2"></i>Add Document Type
                                    </button>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if($documents->hasPages()): ?>
                <div class="d-flex justify-content-center mt-4 no-print">
                    <?php echo e($documents->links()); ?>

                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Quick Statistics -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="document-card">
                <h5 class="mb-3">
                    <i class="fas fa-chart-pie me-2" style="color: #2c3192;"></i>Quick Statistics
                </h5>
                <div class="row">
                    <div class="col-md-3 text-center">
                        <div class="mb-3">
                            <div class="stats-number text-primary"><?php echo e(\App\Models\Document::count()); ?></div>
                            <div class="stats-label">Total Document Types</div>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="mb-3">
                            <div class="stats-number text-success"><?php echo e(\App\Models\StudentRequestItem::distinct('document_id')->count()); ?></div>
                            <div class="stats-label">Requested Types</div>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="mb-3">
                            <?php
                                $totalRevenue = \App\Models\StudentRequestItem::join('student_requests', 'student_request_items.student_request_id', '=', 'student_requests.id')
                                              ->where('student_requests.payment_confirmed', true)
                                              ->sum(\DB::raw('student_request_items.price * student_request_items.quantity'));
                            ?>
                            <div class="stats-number text-info">₱<?php echo e(number_format($totalRevenue, 2)); ?></div>
                            <div class="stats-label">Total Revenue</div>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="mb-3">
                            <div class="stats-number text-warning"><?php echo e(\App\Models\StudentRequestItem::sum('quantity')); ?></div>
                            <div class="stats-label">Documents Issued</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Document Modal -->
<div class="modal fade" id="addDocumentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Document Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addDocumentForm">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="documentType" class="form-label">Document Type Name</label>
                        <input type="text" class="form-control" id="documentType" name="type_document" required>
                    </div>
                    <div class="mb-3">
                        <label for="documentPrice" class="form-label">Price (₱)</label>
                        <input type="number" class="form-control" id="documentPrice" name="price" step="0.01" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label for="documentDescription" class="form-label">Description (Optional)</label>
                        <textarea class="form-control" id="documentDescription" name="description" rows="3"></textarea>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="isActive" name="is_active" checked>
                        <label class="form-check-label" for="isActive">
                            Active (Available for requests)
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn action-btn">
                        <i class="fas fa-save me-2"></i>Save Document
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Document Modal -->
<div class="modal fade" id="editDocumentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Document Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editDocumentForm">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <input type="hidden" id="editDocumentId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editDocumentType" class="form-label">Document Type Name</label>
                        <input type="text" class="form-control" id="editDocumentType" name="type_document" required>
                    </div>
                    <div class="mb-3">
                        <label for="editDocumentPrice" class="form-label">Price (₱)</label>
                        <input type="number" class="form-control" id="editDocumentPrice" name="price" step="0.01" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label for="editDocumentDescription" class="form-label">Description (Optional)</label>
                        <textarea class="form-control" id="editDocumentDescription" name="description" rows="3"></textarea>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="editIsActive" name="is_active">
                        <label class="form-check-label" for="editIsActive">
                            Active (Available for requests)
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn action-btn">
                        <i class="fas fa-save me-2"></i>Update Document
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Document Statistics Modal -->
<div class="modal fade" id="documentStatsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Document Statistics</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="documentStatsContent">
                <!-- Dynamic content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
// Search functionality
function searchDocuments() {
    const searchTerm = document.getElementById('documentSearch').value.toLowerCase();
    const rows = document.querySelectorAll('#documentsTable tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        if (text.includes(searchTerm) || searchTerm === '') {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// Filter by price range
function filterByPrice() {
    const filterValue = document.getElementById('priceFilter').value;
    const rows = document.querySelectorAll('#documentsTable tbody tr');
    
    rows.forEach(row => {
        const price = parseFloat(row.getAttribute('data-price') || 0);
        let showRow = true;
        
        if (filterValue) {
            switch(filterValue) {
                case '0-50':
                    showRow = price >= 0 && price <= 50;
                    break;
                case '51-100':
                    showRow = price >= 51 && price <= 100;
                    break;
                case '101-200':
                    showRow = price >= 101 && price <= 200;
                    break;
                case '201-500':
                    showRow = price >= 201 && price <= 500;
                    break;
                case '501+':
                    showRow = price >= 501;
                    break;
            }
        }
        
        row.style.display = showRow ? '' : 'none';
    });
}

// Real-time search
document.getElementById('documentSearch').addEventListener('input', searchDocuments);

// Add document functionality
document.getElementById('addDocumentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('<?php echo e(route("admin.documents.store")); ?>', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while saving the document.');
    });
});

// Edit document functionality
function editDocument(id) {
    fetch(`/admin/documents/${id}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('editDocumentId').value = data.id;
            document.getElementById('editDocumentType').value = data.type_document;
            document.getElementById('editDocumentPrice').value = data.price;
            document.getElementById('editDocumentDescription').value = data.description || '';
            document.getElementById('editIsActive').checked = data.is_active;
            
            const modal = new bootstrap.Modal(document.getElementById('editDocumentModal'));
            modal.show();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading document data.');
        });
}

// Update document functionality
document.getElementById('editDocumentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const id = document.getElementById('editDocumentId').value;
    const formData = new FormData(this);
    
    fetch(`/admin/documents/${id}`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the document.');
    });
});

// Toggle document status
function toggleDocumentStatus(id) {
    if (confirm('Are you sure you want to change the document status?')) {
        fetch(`/admin/documents/${id}/toggle-status`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the document status.');
        });
    }
}

// View document statistics
function viewDocumentStats(id) {
    fetch(`/admin/documents/${id}/stats`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('documentStatsContent').innerHTML = data.html;
            const modal = new bootstrap.Modal(document.getElementById('documentStatsModal'));
            modal.show();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading document statistics.');
        });
}

// Auto-refresh data every 5 minutes
setTimeout(function() {
    location.reload();
}, 300000);
</script>

<!-- FontAwesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Nu-Regisv2\resources\views/admin/documents/index.blade.php ENDPATH**/ ?>