@extends('layouts.registrar')

@section('content')
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
    
    .badge.bg-success {
        background: linear-gradient(135deg, #198754 0%, #146c43 100%) !important;
        font-weight: 500;
        font-size: 0.75rem;
    }
</style>

<div class="container mt-5">
    <h2 class="mb-4">
        <i class="bi bi-check-circle-fill text-success"></i> Completed Document Requests
    </h2>

    <!-- Window Status Alert -->
    @if(isset($isWindowOccupied) && isset($windowNumber))
        <div class="alert {{ $isWindowOccupied ? 'alert-warning' : 'alert-info' }} mb-4">
            <div class="d-flex align-items-center">
                <i class="bi {{ $isWindowOccupied ? 'bi-exclamation-triangle-fill' : 'bi-check-circle-fill' }} me-2"></i>
                <div>
                    <strong>Window {{ $windowNumber }} Status:</strong>
                    @if($isWindowOccupied && isset($currentRequest))
                        <span class="text-warning">Currently Occupied</span> - Processing request from 
                        <strong>{{ $currentRequest->student->user->first_name }} {{ $currentRequest->student->user->last_name }}</strong> 
                        ({{ $currentRequest->created_at->format('M d, Y') }})
                        <br><small class="text-muted">Complete this request to receive new ones.</small>
                    @else
                        <span class="text-success">Available</span> - Ready to receive new requests
                        @php
                            $pendingCount = \App\Models\StudentRequest::where('status', 'pending')
                                ->whereNull('assigned_registrar_id')
                                ->whereNull('window_id')
                                ->count();
                        @endphp
                        @if($pendingCount > 0)
                            <br><small class="text-info">
                                <i class="bi bi-clock-fill"></i> {{ $pendingCount }} pending request(s) available for approval
                            </small>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th class="text-center" style="width: 5%;">#</th>
                    <th>Student Details</th>
                    <th>Document Type</th>
                    <th>Reason</th>
                    <th class="text-center">Status</th>
                    <th>Assigned Registrar</th>
                    <th>Request Date</th>
                    <th>Expected Release Date</th>
                    <th class="text-center" style="width: 15%;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($completed as $index => $request)
                <tr class="table-row">
                    <td class="text-center fw-bold text-muted">{{ $index + 1 }}</td>
                    <td>
                        <div class="fw-semibold">{{ $request->student->user->first_name }} {{ $request->student->user->last_name }}</div>
                        <small class="text-muted">{{ $request->student->student_id }}</small><br>
                        <small class="text-primary">REF: {{ $request->reference_no }}</small>
                    </td>
                    <td>
                        @if($request->requestItems->count() > 0)
                            @foreach($request->requestItems as $item)
                                <div class="fw-semibold">{{ $item->document->type_document }} (x{{ $item->quantity }})</div>
                            @endforeach
                        @else
                            <span class="text-muted">No documents</span>
                        @endif
                        <small class="text-muted">Total: ₱{{ number_format($request->total_cost, 2) }}</small>
                    </td>
                    <td>
                        {{ $request->reason ?? 'Not specified' }}
                    </td>
                    <td class="text-center">
                        <span class="badge bg-success rounded-pill px-3">{{ ucfirst($request->status) }}</span><br>
                        <small class="text-muted">Verified</small>
                    </td>
                    <td>
                        <div><i class="bi bi-person-fill"></i> {{ $request->assignedRegistrar->user->first_name ?? 'System' }} {{ $request->assignedRegistrar->user->last_name ?? '' }}</div>
                    </td>
                    <td>
                        <div><i class="bi bi-clock"></i> {{ $request->created_at->format('M j, Y') }}</div>
                        <small class="text-muted">{{ $request->created_at->format('g:i A') }}</small>
                    </td>
                    <td>
                        @if($request->expected_release_date)
                            <div><i class="bi bi-calendar-check"></i> {{ \Carbon\Carbon::parse($request->expected_release_date)->format('M j, Y') }}</div>
                            <small class="text-muted">{{ \Carbon\Carbon::parse($request->expected_release_date)->format('l') }}</small>
                            <br>
                            <button class="btn btn-sm btn-outline-primary mt-1" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editReleaseDateModal" 
                                    data-request-id="{{ $request->id }}"
                                    data-current-date="{{ $request->expected_release_date->format('Y-m-d\TH:i') }}">
                                <i class="bi bi-pencil"></i> Edit
                            </button>
                        @else
                            <span class="text-muted"><i class="bi bi-dash-circle"></i> Not set</span>
                            <br>
                            <button class="btn btn-sm btn-outline-primary mt-1" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editReleaseDateModal" 
                                    data-request-id="{{ $request->id }}"
                                    data-current-date="">
                                <i class="bi bi-plus-circle"></i> Set Date
                            </button>
                        @endif
                    </td>
                    <td class="text-center">
                        <span class="text-success"><i class="bi bi-check-circle-fill"></i> Completed</span>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($completed->isEmpty())
        <div class="alert alert-info text-center mt-4">No completed document requests found.</div>
    @endif
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
                @csrf
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

@endsection
