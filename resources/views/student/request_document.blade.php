@extends('layouts.app') {{-- or your actual layout --}}

@section('content')
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

    /* Privacy Notice Styling */
    .form-check-input {
        transition: all 0.3s ease;
    }

    .form-check-input:checked {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }

    .form-check-input:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
    }

    .form-check-label {
        cursor: pointer;
    }

    .form-check-label a {
        transition: color 0.2s ease;
    }

    .form-check-label a:hover {
        color: #0a58ca !important;
    }
</style>
<div class="container mt-5">
    <h3 class="mb-4">Request a Document</h3>

    @if(session('success'))
        <div class="alert alert-success">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
        </div>
    @endif

    {{-- Show pending request details if exists and payment is confirmed or no payment required --}}
    @if($pendingRequest && ($pendingRequest->payment_confirmed || ($pendingRequest->total_cost == 0 && $pendingRequest->status !== 'pending')))
        @php $request = $pendingRequest; @endphp
        <div class="card mb-4 shadow-sm border-0" style="border-left: 4px solid 
            {{ $request->status === 'pending' ? '#ffc107' : 
               ($request->status === 'processing' ? '#0dcaf0' : 
               ($request->status === 'ready_for_release' ? '#0d6efd' : '#28a745')) }} !important;">
            <div class="card-body p-4">
                <!-- Header -->
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3" 
                         style="width: 40px; height: 40px; background-color: 
                         {{ $request->status === 'pending' ? '#ffc107' : 
                            ($request->status === 'processing' ? '#0dcaf0' : 
                            ($request->status === 'ready_for_release' ? '#0d6efd' : '#28a745')) }};">
                        <i class="bi bi-{{ $request->status === 'pending' ? 'clock-fill' : 
                                          ($request->status === 'processing' ? 'gear-fill' : 
                                          ($request->status === 'ready_for_release' ? 'box-seam' : 'check-circle-fill')) }} text-white"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold" style="color: 
                            {{ $request->status === 'pending' ? '#ffc107' : 
                               ($request->status === 'processing' ? '#0dcaf0' : 
                               ($request->status === 'ready_for_release' ? '#0d6efd' : '#28a745')) }};">
                            {{ $request->status === 'pending' ? 'Document Request Submitted' : 
                               ($request->status === 'processing' ? 'Document Being Processed' : 
                               ($request->status === 'ready_for_release' ? 'Document Ready' : 'Document Request Submitted')) }}
                        </h6>
                        <small class="text-muted">{{ $request->created_at->format('M d, Y \a\t h:i A') }}</small>
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
                                <div class="fw-medium">{{ $request->student->student_id }}</div>
                                <small class="text-muted">{{ $request->student->user->first_name }} {{ $request->student->user->last_name }}</small>
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
                                <div class="fw-medium">{{ $request->requestItems->count() }} item(s)</div>
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
                                            {{ $request->status === 'pending' ? 'bg-warning text-dark' : 
                                               ($request->status === 'processing' ? 'bg-info text-white' : 
                                               ($request->status === 'ready_for_release' ? 'bg-primary text-white' : 'bg-success text-white')) }}">
                                            {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-end ms-2">
                                <small class="text-muted text-uppercase fw-medium d-block">Total Price</small>
                                <div class="fw-bold text-success">
                                    @if($request->total_cost > 0)
                                        ₱{{ number_format($request->total_cost, 2) }}
                                    @else
                                        Free
                                    @endif
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
                                    @if($request->expected_release_date)
                                        @php
                                            $releaseDate = is_string($request->expected_release_date) 
                                                ? \Carbon\Carbon::parse($request->expected_release_date) 
                                                : $request->expected_release_date;
                                        @endphp
                                        {{ $releaseDate->format('M d, Y') }}
                                        <br><small class="text-muted">{{ $releaseDate->diffForHumans() }}</small>
                                    @else
                                        <span class="text-muted">Not set</span>
                                    @endif
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
                                @foreach($request->requestItems as $item)
                                    <tr>
                                        <td class="fw-medium">{{ $item->document->type_document }}</td>
                                        <td class="text-center">{{ $item->quantity }}</td>
                                        <td class="text-end fw-bold">
                                            @if($item->document->price > 0)
                                                ₱{{ number_format($item->document->price * $item->quantity, 2) }}
                                            @else
                                                <span class="text-success fw-medium">Free</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                <tr class="table-light">
                                    <td colspan="2" class="text-end fw-bold">Total Amount:</td>
                                    <td class="text-end fw-bold text-primary">₱{{ number_format($request->total_cost, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                {{-- Request Information Table --}}
                <div class="border-top pt-3 mt-3">
                    <h6 class="mb-3 fw-bold">Request Information</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <tbody>
                                <tr>
                                    <td class="fw-semibold text-muted" style="width: 30%; border: none; padding: 0.5rem 0;">Requester:</td>
                                    <td class="fw-medium" style="border: none; padding: 0.5rem 0;">{{ $request->student->user->first_name }} {{ $request->student->user->last_name }}</td>
                                </tr>
                                @if($request->student->course)
                                <tr>
                                    <td class="fw-semibold text-muted" style="border: none; padding: 0.5rem 0;">Course:</td>
                                    <td class="fw-medium" style="border: none; padding: 0.5rem 0;">{{ $request->student->course }}</td>
                                </tr>
                                @endif
                                @if($request->reason)
                                <tr>
                                    <td class="fw-semibold text-muted" style="border: none; padding: 0.5rem 0;">Reason:</td>
                                    <td class="fw-medium" style="border: none; padding: 0.5rem 0;">{{ $request->reason }}</td>
                                </tr>
                                @endif
                                @if($request->remarks)
                                <tr>
                                    <td class="fw-semibold text-muted" style="border: none; padding: 0.5rem 0;">Remarks:</td>
                                    <td class="fw-medium text-muted" style="border: none; padding: 0.5rem 0;">{{ $request->remarks }}</td>
                                </tr>
                                @endif
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
                                        @php
                                            $apkPath = public_path('apk');
                                            $apkFiles = [];
                                            if (is_dir($apkPath)) {
                                                $apkFiles = array_filter(scandir($apkPath), function($file) {
                                                    return pathinfo($file, PATHINFO_EXTENSION) === 'apk';
                                                });
                                            }
                                        @endphp
                                        
                                        @if(!empty($apkFiles))
                                            @php $firstApk = reset($apkFiles); @endphp
                                            <form action="{{ route('student.download.apk') }}" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="reference_no" value="{{ $request->reference_no }}">
                                                <input type="hidden" name="apk_file" value="{{ $firstApk }}">
                                                <button type="submit" class="btn btn-success btn-sm">
                                                    <i class="bi bi-download me-1"></i>Download App
                                                </button>
                                            </form>
                                        @else
                                            <small class="text-muted">No apps available</small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Approval Section - Show for pending requests waiting for registrar approval --}}
    @if($pendingRequest && $pendingRequest->status === 'pending')
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
                                            @foreach($pendingRequest->requestItems as $item)
                                                <tr>
                                                    <td class="fw-medium">{{ $item->document->type_document }}</td>
                                                    <td class="text-center">{{ $item->quantity }}</td>
                                                    <td class="text-end fw-bold">
                                                        @if($item->document->price > 0)
                                                            ₱{{ number_format($item->document->price * $item->quantity, 2) }}
                                                        @else
                                                            <span class="text-success fw-medium">Free</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                
                                <div class="mt-3">
                                    <strong>Requester:</strong> {{ $pendingRequest->student->user->first_name }} {{ $pendingRequest->student->user->last_name }}<br>
                                    <strong>Student ID:</strong> {{ $pendingRequest->student->student_id }}<br>
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
    @endif

    {{-- Payment Section - Show for registrar approved requests that require payment and payment not confirmed --}}
    @if($pendingRequest && $pendingRequest->status === 'registrar_approved' && $pendingRequest->total_cost > 0 && !$pendingRequest->payment_confirmed)
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
                                            @foreach($pendingRequest->requestItems as $item)
                                                <tr>
                                                    <td class="fw-medium">{{ $item->document->type_document }}</td>
                                                    <td class="text-center">{{ $item->quantity }}</td>
                                                    <td class="text-end fw-bold">
                                                        @if($item->document->price > 0)
                                                            ₱{{ number_format($item->document->price * $item->quantity, 2) }}
                                                        @else
                                                            <span class="text-success fw-medium">Free</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                
                                <div class="mt-3">
                                    <strong>Requester:</strong> {{ $pendingRequest->student->user->first_name }} {{ $pendingRequest->student->user->last_name }}<br>
                                    <strong>Student ID:</strong> {{ $pendingRequest->student->student_id }}<br>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="payment-info">
                                    <strong>Payment Breakdown:</strong>
                                    <div class="mt-2">
                                        @foreach($pendingRequest->requestItems as $item)
                                            <div class="d-flex justify-content-between mb-1">
                                                <small>{{ $item->document->type_document }} (x{{ $item->quantity }})</small>
                                                <small>₱{{ number_format($item->document->price * $item->quantity, 2) }}</small>
                                            </div>
                                        @endforeach
                                        <hr class="my-2">
                                        <div class="d-flex justify-content-between fw-bold">
                                            <span>Total Amount:</span>
                                            <span class="text-success">₱{{ number_format($pendingRequest->total_cost, 2) }}</span>
                                        </div>
                                    </div>
                                    <br>
                                    <small class="text-muted">
                                        Payment must be made at the Accounting Office before your documents can be processed.
                                    </small>
                                </div>
                                <br><strong>Total Quantity:</strong> {{ $pendingRequest->requestItems->sum('quantity') }}
                                <br><br>
                                <div class="text-center">
                                    <img src="{{ asset('images/qr-display.jpg') }}" alt="Payment QR Code" class="img-fluid" style="max-width: 200px; max-height: 200px;">
                                    <p class="text-muted mt-2 small">Scan QR code for payment</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-3 p-3 bg-light rounded">
                            <h6 class="text-warning mb-2"><i class="bi bi-info-circle me-2"></i>Payment Instructions:</h6>
                            <ol class="mb-3 small">
                                <li>Scan the QR code above or proceed to the Accounting Office to make payment</li>
                                <li>Make payment for the total amount of <strong>₱{{ number_format($pendingRequest->total_cost, 2) }}</strong></li>
                                <li>Upload your payment receipt below for verification</li>
                            </ol>
                            
                            @if($pendingRequest->payment_receipt_path)
                                <div class="alert alert-info">
                                    <i class="bi bi-clock me-2"></i>
                                    Receipt uploaded and awaiting approval from accounting.
                                </div>
                            @else
                                <form method="POST" action="{{ route('student.upload.receipt', $pendingRequest) }}" enctype="multipart/form-data">
                                    @csrf
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
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Show form only if no pending request --}}
    @if(!$pendingRequest)
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-file-earmark-plus me-2"></i>Request Documents</h5>
            </div>
            <div class="card-body">
                <form id="document-request-form" action="{{ route('student.request.document.submit') }}" method="POST">
                    @csrf
                    
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
                                            @foreach($documents as $doc)
                                                <option value="{{ $doc->id }}" data-price="{{ $doc->price }}" data-name="{{ $doc->type_document }}">
                                                    {{ $doc->type_document }} - {{ $doc->price > 0 ? '₱' . number_format($doc->price, 2) : 'Free' }}
                                                </option>
                                            @endforeach
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
                            <select name="reason_select" id="reason_select" class="form-select" required disabled>
                                <option value="" disabled selected>-- Please select a document first --</option>
                            </select>
                            <small class="text-muted">Select a document type above to see available reasons</small>
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

                    <!-- Privacy Notice and Terms Agreement -->
                    <div class="form-section mt-4">
                        <div class="alert alert-info border-0" style="background: rgba(13, 110, 253, 0.05); border-left: 4px solid #0d6efd;">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="privacy_agreement" name="privacy_agreement" value="1" required style="width: 1.2rem; height: 1.2rem; border-radius: 4px; border: 2px solid #0d6efd;">
                                <label class="form-check-label ms-2" for="privacy_agreement" style="font-size: 0.9rem; color: #6c757d; line-height: 1.5; cursor: pointer;">
                                    <strong style="color: #0d6efd;">Privacy Notice and Terms of Agreement *</strong><br>
                                    I hereby acknowledge and agree to the following:<br>
                                    • I understand that the personal information I provide will be used solely for processing my document request.<br>
                                    • My data will be handled in accordance with the National University Data Privacy Policy and Republic Act No. 10173 (Data Privacy Act of 2012).<br>
                                    • I consent to the collection, processing, and storage of my personal information for document processing purposes.<br>
                                    • I have read and understood the <a href="#" data-bs-toggle="modal" data-bs-target="#privacyModal" style="color: #0d6efd; text-decoration: underline;">Privacy Policy</a> and <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal" style="color: #0d6efd; text-decoration: underline;">Terms of Service</a>.
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-send me-2"></i>Submit Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @else
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            <strong>You have a pending document request.</strong> Please wait for your current request to be completed before submitting a new one.
        </div>
    @endif
</div>

<!-- Privacy Policy Modal -->
<div class="modal fade" id="privacyModal" tabindex="-1" aria-labelledby="privacyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: #0d6efd; color: white;">
                <h5 class="modal-title" id="privacyModalLabel">
                    <i class="bi bi-shield-lock-fill me-2"></i>Privacy Policy
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6 class="fw-bold text-primary">National University Data Privacy Policy</h6>
                <p class="text-muted small">Effective Date: January 1, 2024</p>
                
                <h6 class="fw-bold mt-4">1. Introduction</h6>
                <p>National University ("we," "us," or "our") is committed to protecting your privacy and ensuring the security of your personal information. This Privacy Policy explains how we collect, use, disclose, and safeguard your data in compliance with Republic Act No. 10173 (Data Privacy Act of 2012) and its implementing rules and regulations.</p>
                
                <h6 class="fw-bold mt-4">2. Information We Collect</h6>
                <p>We collect the following types of personal information:</p>
                <ul>
                    <li><strong>Student Identification:</strong> Student ID, full name, course, year level, department</li>
                    <li><strong>Document Requests:</strong> Type of documents requested, quantity, reason for request</li>
                    <li><strong>Transaction Data:</strong> Queue number, reference number, request date and time</li>
                    <li><strong>Technical Information:</strong> IP address, device information, browser type (for security purposes)</li>
                </ul>
                
                <h6 class="fw-bold mt-4">3. How We Use Your Information</h6>
                <p>Your personal information is used for the following purposes:</p>
                <ul>
                    <li>Processing and fulfilling your document requests</li>
                    <li>Maintaining accurate academic and transaction records</li>
                    <li>Sending notifications about your request status via push notifications</li>
                    <li>Improving our services and queue management system</li>
                    <li>Complying with legal and regulatory requirements</li>
                </ul>
                
                <h6 class="fw-bold mt-4">4. Data Sharing and Disclosure</h6>
                <p>We do not sell, rent, or trade your personal information. We may share your data only in the following circumstances:</p>
                <ul>
                    <li>With authorized university personnel for document processing</li>
                    <li>When required by law or legal process</li>
                    <li>With third-party service providers who assist us in operating our systems (under strict confidentiality agreements)</li>
                </ul>
                
                <h6 class="fw-bold mt-4">5. Data Security</h6>
                <p>We implement appropriate technical and organizational security measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction. These measures include:</p>
                <ul>
                    <li>Secure encrypted connections (HTTPS/SSL)</li>
                    <li>Access controls and authentication systems</li>
                    <li>Regular security audits and monitoring</li>
                    <li>Staff training on data protection</li>
                </ul>
                
                <h6 class="fw-bold mt-4">6. Data Retention</h6>
                <p>We retain your personal information for as long as necessary to fulfill the purposes outlined in this policy, comply with legal obligations, and resolve disputes. Student records are maintained in accordance with NU record retention policies and applicable laws.</p>
                
                <h6 class="fw-bold mt-4">7. Your Rights</h6>
                <p>Under the Data Privacy Act, you have the right to:</p>
                <ul>
                    <li><strong>Access:</strong> Request access to your personal information</li>
                    <li><strong>Correction:</strong> Request correction of inaccurate or incomplete data</li>
                    <li><strong>Erasure:</strong> Request deletion of your data (subject to legal requirements)</li>
                    <li><strong>Object:</strong> Object to the processing of your data</li>
                    <li><strong>Data Portability:</strong> Request a copy of your data in a structured format</li>
                    <li><strong>Withdraw Consent:</strong> Withdraw your consent at any time (where processing is based on consent)</li>
                </ul>
                
                <h6 class="fw-bold mt-4">8. Contact Information</h6>
                <p>For questions, concerns, or requests regarding this Privacy Policy or your personal information, please contact:</p>
                <p class="ms-3">
                    <strong>Data Protection Officer</strong><br>
                    National University - Lipa<br>
                    NU Bldg, SM City Lipa, JP Laurel Highway, Lipa City, Batangas<br>
                    Email: dpo@nu-lipa.edu.ph<br>
                    Phone: (043) 756-5555
                </p>
                
                <h6 class="fw-bold mt-4">9. Updates to This Policy</h6>
                <p>We may update this Privacy Policy from time to time. We will notify you of any material changes by posting the updated policy on our website and updating the "Effective Date" above.</p>
                
                <p class="mt-4"><small class="text-muted">Last updated: January 1, 2024</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Terms of Service Modal -->
<div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: #0d6efd; color: white;">
                <h5 class="modal-title" id="termsModalLabel">
                    <i class="bi bi-file-text-fill me-2"></i>Terms of Service
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6 class="fw-bold text-primary">NU Lipa Document Request Service - Terms of Service</h6>
                <p class="text-muted small">Effective Date: January 1, 2024</p>
                
                <h6 class="fw-bold mt-4">1. Acceptance of Terms</h6>
                <p>By accessing and using the National University Lipa Document Request Service ("the Service"), you acknowledge that you have read, understood, and agree to be bound by these Terms of Service. If you do not agree to these terms, please do not use the Service.</p>
                
                <h6 class="fw-bold mt-4">2. Service Description</h6>
                <p>The Service provides a digital platform for students and alumni to:</p>
                <ul>
                    <li>Submit on-site document requests</li>
                    <li>Track the status of document requests</li>
                    <li>Receive notifications about request progress</li>
                    <li>Manage queue positions for document pickup</li>
                </ul>
                
                <h6 class="fw-bold mt-4">3. Eligibility</h6>
                <p>To use the Service, you must:</p>
                <ul>
                    <li>Be a current or former student of National University - Lipa</li>
                    <li>Provide accurate and complete information</li>
                    <li>Have a valid student ID or reference number</li>
                    <li>Agree to these Terms of Service and our Privacy Policy</li>
                </ul>
                
                <h6 class="fw-bold mt-4">4. User Responsibilities</h6>
                <p>When using the Service, you agree to:</p>
                <ul>
                    <li>Provide accurate, current, and complete information about yourself</li>
                    <li>Maintain the accuracy of your information</li>
                    <li>Use the Service only for lawful purposes</li>
                    <li>Not impersonate any person or entity</li>
                    <li>Not interfere with or disrupt the Service or servers</li>
                    <li>Comply with all applicable local, state, national, and international laws</li>
                </ul>
                
                <h6 class="fw-bold mt-4">5. Document Request Process</h6>
                <p><strong>5.1 Request Submission:</strong> Document requests must be submitted with complete and accurate information. Incomplete or inaccurate requests may be rejected or delayed.</p>
                
                <p><strong>5.2 Processing Time:</strong> Processing times vary depending on the type of document requested. Estimated processing times are provided for reference only and are not guaranteed.</p>
                
                <p><strong>5.3 Fees and Payment:</strong> Applicable fees must be paid before document processing. Fee information is displayed at the time of request submission. All payments are non-refundable except in cases of service error.</p>
                
                <p><strong>5.4 Document Pickup:</strong> Documents must be picked up within the specified timeframe. Unclaimed documents may be subject to additional storage fees or disposal after a reasonable period.</p>
                
                <h6 class="fw-bold mt-4">6. Queue Management</h6>
                <p>Queue positions are assigned automatically and are subject to change based on:</p>
                <ul>
                    <li>Document processing status</li>
                    <li>Registrar availability</li>
                    <li>Priority cases (as determined by university policy)</li>
                </ul>
                <p>The university reserves the right to adjust queue positions as necessary to ensure efficient service delivery.</p>
                
                <h6 class="fw-bold mt-4">7. Notifications</h6>
                <p>By using the Service, you consent to receive notifications via:</p>
                <ul>
                    <li>Push notifications through the mobile application</li>
                    <li>Email to your registered email address</li>
                    <li>SMS to your registered mobile number (if applicable)</li>
                </ul>
                <p>You may opt out of non-essential notifications through your account settings.</p>
                
                <h6 class="fw-bold mt-4">8. Intellectual Property</h6>
                <p>All content, features, and functionality of the Service, including but not limited to text, graphics, logos, icons, images, and software, are the exclusive property of National University and are protected by copyright, trademark, and other intellectual property laws.</p>
                
                <h6 class="fw-bold mt-4">9. Limitation of Liability</h6>
                <p>To the fullest extent permitted by law, National University shall not be liable for:</p>
                <ul>
                    <li>Any indirect, incidental, special, consequential, or punitive damages</li>
                    <li>Loss of profits, revenues, data, or use</li>
                    <li>Service interruptions or delays</li>
                    <li>Errors or inaccuracies in content</li>
                    <li>Unauthorized access to or alteration of your transmissions or data</li>
                </ul>
                
                <h6 class="fw-bold mt-4">10. Service Availability</h6>
                <p>We strive to provide continuous access to the Service. However, we do not guarantee that the Service will be uninterrupted, timely, secure, or error-free. We reserve the right to:</p>
                <ul>
                    <li>Modify or discontinue the Service at any time</li>
                    <li>Perform scheduled maintenance</li>
                    <li>Suspend access for technical or security reasons</li>
                </ul>
                
                <h6 class="fw-bold mt-4">11. Prohibited Activities</h6>
                <p>You may not:</p>
                <ul>
                    <li>Use the Service for fraudulent purposes</li>
                    <li>Submit false or misleading information</li>
                    <li>Attempt to gain unauthorized access to the Service</li>
                    <li>Use automated systems or software to extract data</li>
                    <li>Reverse engineer, decompile, or disassemble any part of the Service</li>
                    <li>Transmit viruses, malware, or other harmful code</li>
                </ul>
                
                <h6 class="fw-bold mt-4">12. Termination</h6>
                <p>We reserve the right to terminate or suspend your access to the Service immediately, without prior notice, for any reason, including but not limited to:</p>
                <ul>
                    <li>Violation of these Terms of Service</li>
                    <li>Fraudulent or illegal activity</li>
                    <li>Provision of false information</li>
                    <li>Abuse of the Service or other users</li>
                </ul>
                
                <h6 class="fw-bold mt-4">13. Governing Law</h6>
                <p>These Terms of Service shall be governed by and construed in accordance with the laws of the Republic of the Philippines. Any disputes arising from these terms shall be subject to the exclusive jurisdiction of the courts of Lipa City, Batangas.</p>
                
                <h6 class="fw-bold mt-4">14. Changes to Terms</h6>
                <p>We reserve the right to modify these Terms of Service at any time. We will notify you of any material changes by posting the updated terms on our website and updating the "Effective Date" above. Your continued use of the Service after such changes constitutes your acceptance of the new terms.</p>
                
                <h6 class="fw-bold mt-4">15. Contact Information</h6>
                <p>For questions or concerns about these Terms of Service, please contact:</p>
                <p class="ms-3">
                    <strong>Registrar's Office</strong><br>
                    National University - Lipa<br>
                    NU Bldg, SM City Lipa, JP Laurel Highway, Lipa City, Batangas<br>
                    Email: registrar@nu-lipa.edu.ph<br>
                    Phone: (043) 756-5555
                </p>
                
                <h6 class="fw-bold mt-4">16. Severability</h6>
                <p>If any provision of these Terms of Service is found to be invalid or unenforceable, the remaining provisions shall continue in full force and effect.</p>
                
                <h6 class="fw-bold mt-4">17. Entire Agreement</h6>
                <p>These Terms of Service, together with our Privacy Policy, constitute the entire agreement between you and National University regarding the use of the Service.</p>
                
                <p class="mt-4 text-center fw-bold">By using the Service, you acknowledge that you have read, understood, and agree to be bound by these Terms of Service.</p>
                
                <p class="mt-4"><small class="text-muted">Last updated: January 1, 2024</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
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

            select.addEventListener('change', function() {
                updateTotalCost();
                
                // Update reason dropdown based on first selected document
                const firstDocumentSelect = document.querySelector('.document-select');
                const selectedOption = firstDocumentSelect.options[firstDocumentSelect.selectedIndex];
                
                if (selectedOption && selectedOption.value) {
                    const documentName = selectedOption.text.split(' - ')[0].trim();
                    updateReasonOptions(documentName);
                }
            });
            
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
                
                // Update reason dropdown if this was the first document
                const firstDocumentSelect = document.querySelector('.document-select');
                if (firstDocumentSelect) {
                    const selectedOption = firstDocumentSelect.options[firstDocumentSelect.selectedIndex];
                    if (selectedOption && selectedOption.value) {
                        const documentName = selectedOption.text.split(' - ')[0].trim();
                        updateReasonOptions(documentName);
                    } else {
                        updateReasonOptions('');
                    }
                }
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
                            @foreach($documents as $doc)
                                <option value="{{ $doc->id }}" data-price="{{ $doc->price }}" data-name="{{ $doc->type_document }}">
                                    {{ $doc->type_document }} - {{ $doc->price > 0 ? '₱' . number_format($doc->price, 2) : 'Free' }}
                                </option>
                            @endforeach
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

        // Document-specific reasons mapping
        const documentReasons = {
            'Transcript of Records with Documentary Stamp': [
                'Required for transfer to another school or university',
                'Needed for employment application or job requirement',
                'For evaluation or credential verification abroad',
                'Requirement for licensure examination or board exam application',
                'Other'
            ],
            'Certificates (Any) with Documentary Stamp': [
                'For authentication or submission to CHED, DFA, or POEA',
                'Requirement for overseas employment or study',
                'Needed for official endorsement or verification purposes',
                'Other'
            ],
            'Certificates (Any) without Documentary Stamp': [
                'For personal record or local employment application',
                'Needed for scholarship or internship requirements',
                'Requirement for organization or club membership verification',
                'Other'
            ],
            'Form 137': [
                'Required for transfer to another academic institution',
                'Needed for student record completion or enrollment verification',
                'For evaluation of previous academic performance',
                'Other'
            ],
            'CTC of Grades Per Term (Certified True Copy)': [
                'Needed for scholarship renewal or application',
                'Requirement for internship or OJT documentation',
                'For employment or promotion qualification verification',
                'Other'
            ],
            'CTC of Diploma (Per Set)': [
                'Requirement for employment application or promotion',
                'Needed for foreign credential evaluation or visa processing',
                'For personal record or framing purposes',
                'Other'
            ],
            'CTC of TOR (Per Set)': [
                'Requirement for graduate school admission',
                'For professional licensing or board examination application',
                'Needed for job application, especially abroad',
                'Other'
            ],
            'Copy of Diploma with Documentary Stamp': [
                'Requirement for DFA authentication or Apostille',
                'Needed for employment abroad or immigration purposes',
                'For official submission to an employer or institution',
                'Other'
            ],
            'Honorable Dismissal (HD/Transfer Credentials w/ Doc. Stamp)': [
                'Required when transferring to another college or university',
                'Needed for clearance or exit documentation from previous school',
                'For authentication or verification by another institution',
                'Other'
            ],
            'Reprinting of COR-Stamp Enrolled/CTC/Copy of Grades': [
                'Lost or damaged original Certificate of Registration',
                'Needed as proof of enrollment for scholarship or internship',
                'Requirement for employment or verification of student status',
                'Other'
            ],
            'Certificate of Good Moral': [
                'Requirement for transfer, scholarship, or graduation',
                'Needed for employment or internship application',
                'Requirement for licensure exam or government application',
                'Other'
            ],
            'Course Descriptions': [
                'Needed for subject evaluation or credit transfer to another institution',
                'Requirement for graduate school or foreign credential assessment',
                'For employment verification or curriculum equivalency evaluation',
                'Other'
            ],
            'Documentary Stamp': [
                'Required for official authentication or notarization of documents',
                'Needed for submission to government agencies or embassies',
                'To comply with official certification or verification requirements',
                'Other'
            ]
        };

        // Function to normalize document name for matching (removes spaces, dashes, etc.)
        function normalizeDocumentName(name) {
            return name.toLowerCase()
                .replace(/\s+/g, '')
                .replace(/-/g, '')
                .replace(/–/g, '')
                .replace(/\//g, '')
                .replace(/\(/g, '')
                .replace(/\)/g, '')
                .replace(/\./g, '');
        }

        // Function to find matching document reasons with fuzzy matching
        function findDocumentReasons(documentName) {
            // First try exact match
            if (documentReasons[documentName]) {
                return documentReasons[documentName];
            }

            // Try fuzzy match
            const normalizedSearch = normalizeDocumentName(documentName);
            
            for (const [key, reasons] of Object.entries(documentReasons)) {
                if (normalizeDocumentName(key) === normalizedSearch) {
                    return reasons;
                }
            }

            // If still no match, check if document name contains key words
            const lowerDocName = documentName.toLowerCase();
            
            if (lowerDocName.includes('transcript') && lowerDocName.includes('records')) {
                return documentReasons['Transcript of Records with Documentary Stamp'];
            }
            if (lowerDocName.includes('certificate') && lowerDocName.includes('with') && lowerDocName.includes('stamp')) {
                return documentReasons['Certificates (Any) with Documentary Stamp'];
            }
            if (lowerDocName.includes('certificate') && lowerDocName.includes('without')) {
                return documentReasons['Certificates (Any) without Documentary Stamp'];
            }
            if (lowerDocName.includes('form') && lowerDocName.includes('137')) {
                return documentReasons['Form 137'];
            }
            if (lowerDocName.includes('ctc') && lowerDocName.includes('grades')) {
                return documentReasons['CTC of Grades Per Term (Certified True Copy)'];
            }
            if (lowerDocName.includes('ctc') && lowerDocName.includes('diploma')) {
                return documentReasons['CTC of Diploma (Per Set)'];
            }
            if (lowerDocName.includes('ctc') && lowerDocName.includes('tor')) {
                return documentReasons['CTC of TOR (Per Set)'];
            }
            if (lowerDocName.includes('copy') && lowerDocName.includes('diploma')) {
                return documentReasons['Copy of Diploma with Documentary Stamp'];
            }
            if (lowerDocName.includes('honorable') && lowerDocName.includes('dismissal')) {
                return documentReasons['Honorable Dismissal (HD/Transfer Credentials w/ Doc. Stamp)'];
            }
            if (lowerDocName.includes('reprinting') && lowerDocName.includes('cor')) {
                return documentReasons['Reprinting of COR-Stamp Enrolled/CTC/Copy of Grades'];
            }
            if (lowerDocName.includes('good') && lowerDocName.includes('moral')) {
                return documentReasons['Certificate of Good Moral'];
            }
            if (lowerDocName.includes('course') && lowerDocName.includes('description')) {
                return documentReasons['Course Descriptions'];
            }
            if (lowerDocName.includes('documentary') && lowerDocName.includes('stamp') && !lowerDocName.includes('transcript')) {
                return documentReasons['Documentary Stamp'];
            }

            return null;
        }

        // Function to update reason dropdown based on selected document
        function updateReasonOptions(documentName) {
            const reasonSelect = document.getElementById('reason_select');
            const reasons = findDocumentReasons(documentName);
            
            // Clear existing options
            reasonSelect.innerHTML = '';
            
            if (reasons && reasons.length > 0) {
                // Enable the select and add new options
                reasonSelect.disabled = false;
                reasonSelect.innerHTML = '<option value="" disabled selected>-- Select Reason --</option>';
                
                reasons.forEach(reason => {
                    const option = document.createElement('option');
                    option.value = reason;
                    option.textContent = reason;
                    reasonSelect.appendChild(option);
                });
            } else {
                // No specific reasons, disable the select
                reasonSelect.disabled = true;
                reasonSelect.innerHTML = '<option value="" disabled selected>-- Please select a document first --</option>';
            }
            
            // Reset hidden reason field
            document.getElementById('reason').value = '';
            
            // Hide other reason container
            document.getElementById('other_reason_container').style.display = 'none';
            document.getElementById('other_reason').disabled = true;
            document.getElementById('other_reason').value = '';
        }

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

            // Check if privacy agreement is checked
            const privacyAgreement = document.getElementById('privacy_agreement');
            if (privacyAgreement && !privacyAgreement.checked) {
                e.preventDefault();
                alert('Please read and agree to the Privacy Notice and Terms of Agreement to continue.');
                privacyAgreement.focus();
                return;
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
    @if($pendingRequest)
        // Load Pusher
        const pusherScript = document.createElement('script');
        pusherScript.src = 'https://js.pusher.com/8.2.0/pusher.min.js';
        document.head.appendChild(pusherScript);

        pusherScript.onload = function() {
            // Initialize Pusher
            const pusher = new Pusher('{{ config('broadcasting.connections.pusher.key') }}', {
                cluster: '{{ config('broadcasting.connections.pusher.options.cluster') }}',
                encrypted: true
            });

            // Subscribe to the specific request channel
            const requestChannel = pusher.subscribe('request-{{ $pendingRequest->reference_no }}');

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
    @endif
</script>
@endsection
