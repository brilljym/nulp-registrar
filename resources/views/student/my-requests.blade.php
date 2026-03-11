@extends('layouts.app')

@section('content')
<style>
    .request-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        margin-bottom: 1.5rem;
    }

    .request-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
    }

    .status-badge {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.4rem 0.8rem;
        border-radius: 20px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-pending {
        background: #fff3cd;
        color: #856404;
        border: 1px solid #ffeaa7;
    }

    .status-processing {
        background: #cff4fc;
        color: #055160;
        border: 1px solid #a6e9f7;
    }

    .status-ready_for_release {
        background: #cfe2ff;
        color: #084298;
        border: 1px solid #9ec5fe;
    }

    .status-completed {
        background: #d1e7dd;
        color: #0f5132;
        border: 1px solid #a3cfbb;
    }

    .progress-indicator {
        height: 4px;
        background: #e9ecef;
        border-radius: 2px;
        overflow: hidden;
    }

    .progress-fill {
        height: 100%;
        transition: width 0.3s ease;
    }

    .document-item {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 0.75rem;
        margin-bottom: 0.5rem;
    }

    .btn-track {
        background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
        border: none;
        border-radius: 8px;
        color: white;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .btn-track:hover {
        background: linear-gradient(135deg, #0b5ed7 0%, #0a58ca 100%);
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(13, 110, 253, 0.3);
    }

    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        color: #6c757d;
    }

    .empty-state img {
        max-width: 200px;
        opacity: 0.5;
        margin-bottom: 1rem;
    }

    .info-item {
        display: flex;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    .info-item i {
        width: 20px;
        text-align: center;
        margin-right: 0.5rem;
        color: #6c757d;
    }

    .payment-indicator {
        display: inline-flex;
        align-items: center;
        font-size: 0.875rem;
        padding: 0.25rem 0.5rem;
        border-radius: 6px;
        margin-top: 0.5rem;
    }

    .payment-pending {
        background: #fff3cd;
        color: #856404;
        border: 1px solid #ffeaa7;
    }

    .payment-approved {
        background: #d1e7dd;
        color: #0f5132;
        border: 1px solid #a3cfbb;
    }

    .payment-free {
        background: #e2e3e5;
        color: #383d41;
        border: 1px solid #d6d8db;
    }

    .filter-tabs {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 0.25rem;
        margin-bottom: 2rem;
    }

    .filter-tab {
        border: none;
        background: transparent;
        color: #6c757d;
        font-weight: 500;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        transition: all 0.2s ease;
    }

    .filter-tab.active {
        background: #fff;
        color: #0d6efd;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .filter-tab:hover:not(.active) {
        background: rgba(255, 255, 255, 0.5);
        color: #495057;
    }

    @media (max-width: 768px) {
        .request-card {
            margin-bottom: 1rem;
        }

        .card-body {
            padding: 1rem;
        }

        .filter-tabs {
            overflow-x: auto;
            display: flex;
            gap: 0.5rem;
            padding: 0.5rem;
        }

        .filter-tab {
            white-space: nowrap;
            flex-shrink: 0;
        }
    }
</style>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">
            <i class="bi bi-file-earmark-text me-2 text-primary"></i>
            My Document Requests
        </h3>
        <a href="{{ route('student.request.document') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>New Request
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Filter Tabs --}}
    <div class="filter-tabs d-flex">
        <button class="filter-tab active" data-filter="all">
            All Requests <span class="badge bg-secondary ms-1">{{ $requests->count() }}</span>
        </button>
        <button class="filter-tab" data-filter="pending">
            Pending <span class="badge bg-warning ms-1">{{ $requests->where('status', 'pending')->count() }}</span>
        </button>
        <button class="filter-tab" data-filter="processing">
            Processing <span class="badge bg-info ms-1">{{ $requests->where('status', 'processing')->count() }}</span>
        </button>
        <button class="filter-tab" data-filter="ready_for_release">
            Ready <span class="badge bg-primary ms-1">{{ $requests->where('status', 'ready_for_release')->count() }}</span>
        </button>
        <button class="filter-tab" data-filter="completed">
            Completed <span class="badge bg-success ms-1">{{ $requests->where('status', 'completed')->count() }}</span>
        </button>
    </div>

    @if($requests->isEmpty())
        <div class="empty-state">
            <div class="mb-4">
                <i class="bi bi-file-earmark-text" style="font-size: 4rem; color: #dee2e6;"></i>
            </div>
            <h5 class="text-muted">No Document Requests Yet</h5>
            <p class="text-muted mb-4">You haven't submitted any document requests. Start by creating your first request.</p>
            <a href="{{ route('student.request.document') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Request Documents
            </a>
        </div>
    @else
        <div class="row" id="requests-container">
            @foreach($requests as $request)
                <div class="col-12 request-item" data-status="{{ $request->status }}">
                    <div class="card request-card">
                        <div class="card-body">
                            <div class="row g-3">
                                {{-- Request Header --}}
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <h6 class="card-title mb-1">
                                                <i class="bi bi-hash text-muted me-1"></i>
                                                {{ $request->reference_no }}
                                            </h6>
                                            <small class="text-muted">
                                                <i class="bi bi-calendar3 me-1"></i>
                                                {{ $request->created_at->format('M d, Y \a\t h:i A') }}
                                            </small>
                                        </div>
                                        <span class="status-badge status-{{ $request->status }}">
                                            {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                        </span>
                                    </div>

                                    {{-- Progress Indicator --}}
                                    <div class="progress-indicator mb-3">
                                        <div class="progress-fill" 
                                             style="width: {{ 
                                                $request->status === 'pending' ? '25%' :
                                                ($request->status === 'processing' ? '50%' :
                                                ($request->status === 'ready_for_release' ? '75%' : '100%'))
                                             }}; 
                                             background: {{ 
                                                $request->status === 'pending' ? '#ffc107' :
                                                ($request->status === 'processing' ? '#0dcaf0' :
                                                ($request->status === 'ready_for_release' ? '#0d6efd' : '#28a745'))
                                             }};"></div>
                                    </div>
                                </div>

                                {{-- Request Details --}}
                                <div class="col-md-8">
                                    <h6 class="mb-2">
                                        <i class="bi bi-file-earmark-text me-2 text-primary"></i>
                                        Requested Documents
                                    </h6>
                                    @foreach($request->requestItems as $item)
                                        <div class="document-item">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <strong>{{ $item->document->type_document }}</strong>
                                                    <small class="text-muted d-block">Quantity: {{ $item->quantity }}</small>
                                                </div>
                                                <div class="text-end">
                                                    @if($item->price > 0)
                                                        <strong class="text-success">₱{{ number_format($item->price * $item->quantity, 2) }}</strong>
                                                    @else
                                                        <span class="text-muted">Free</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach

                                    @if($request->reason)
                                        <div class="mt-3">
                                            <small class="text-muted text-uppercase fw-medium">Reason for Request</small>
                                            <div class="small">{{ $request->reason }}</div>
                                        </div>
                                    @endif
                                </div>

                                {{-- Request Info & Actions --}}
                                <div class="col-md-4">
                                    <div class="info-item">
                                        <i class="bi bi-cash-coin"></i>
                                        <span><strong>Total: ₱{{ number_format($request->total_cost, 2) }}</strong></span>
                                    </div>

                                    @if($request->expected_release_date)
                                        <div class="info-item">
                                            <i class="bi bi-calendar-check"></i>
                                            <span>Expected: {{ $request->expected_release_date->format('M d, Y') }}</span>
                                        </div>
                                    @endif

                                    @if($request->assignedRegistrar)
                                        <div class="info-item">
                                            <i class="bi bi-person-badge"></i>
                                            <span>{{ $request->assignedRegistrar->first_name }} {{ $request->assignedRegistrar->last_name }}</span>
                                        </div>
                                    @endif

                                    {{-- Payment Status --}}
                                    @if($request->total_cost > 0)
                                        @if($request->payment_approved)
                                            <div class="payment-indicator payment-approved">
                                                <i class="bi bi-check-circle-fill me-1"></i>
                                                Payment Approved
                                            </div>
                                        @elseif($request->payment_receipt_path)
                                            <div class="payment-indicator payment-pending">
                                                <i class="bi bi-clock-fill me-1"></i>
                                                Payment Under Review
                                            </div>
                                        @else
                                            <div class="payment-indicator payment-pending">
                                                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                                Payment Required
                                            </div>
                                        @endif
                                    @else
                                        <div class="payment-indicator payment-free">
                                            <i class="bi bi-gift-fill me-1"></i>
                                            Free Document
                                        </div>
                                    @endif

                                    {{-- Action Button --}}
                                    <div class="mt-3 d-grid">
                                        <a href="{{ route('student.track', $request->reference_no) }}" 
                                           class="btn btn-track">
                                            <i class="bi bi-search me-2"></i>Track Request
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Summary Stats --}}
        <div class="row mt-4">
            <div class="col-12">
                <div class="card bg-light">
                    <div class="card-body">
                        <h6 class="card-title">
                            <i class="bi bi-bar-chart me-2"></i>
                            Request Summary
                        </h6>
                        <div class="row text-center">
                            <div class="col-6 col-md-3">
                                <div class="h4 text-warning mb-0">{{ $requests->where('status', 'pending')->count() }}</div>
                                <small class="text-muted">Pending</small>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="h4 text-info mb-0">{{ $requests->where('status', 'processing')->count() }}</div>
                                <small class="text-muted">Processing</small>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="h4 text-primary mb-0">{{ $requests->where('status', 'ready_for_release')->count() }}</div>
                                <small class="text-muted">Ready</small>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="h4 text-success mb-0">{{ $requests->where('status', 'completed')->count() }}</div>
                                <small class="text-muted">Completed</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter functionality
    const filterTabs = document.querySelectorAll('.filter-tab');
    const requestItems = document.querySelectorAll('.request-item');
    
    filterTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const filter = this.dataset.filter;
            
            // Update active tab
            filterTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            // Filter requests
            requestItems.forEach(item => {
                if (filter === 'all' || item.dataset.status === filter) {
                    item.style.display = 'block';
                    // Add fade-in animation
                    item.style.opacity = '0';
                    setTimeout(() => {
                        item.style.opacity = '1';
                        item.style.transition = 'opacity 0.3s ease';
                    }, 100);
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });

    // Add hover effects to cards
    const requestCards = document.querySelectorAll('.request-card');
    requestCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-4px)';
            this.style.boxShadow = '0 8px 25px rgba(0, 0, 0, 0.15)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(-2px)';
            this.style.boxShadow = '0 4px 16px rgba(0, 0, 0, 0.15)';
        });
    });

    // Auto-refresh for pending requests (every 30 seconds)
    const hasPendingRequests = document.querySelector('.request-item[data-status="pending"]') !== null;
    if (hasPendingRequests) {
        setInterval(() => {
            // Check for updates without full page reload
            fetch(window.location.href)
                .then(response => response.text())
                .then(html => {
                    // Could implement partial updates here
                    console.log('Checked for updates');
                })
                .catch(error => {
                    console.log('Update check failed:', error);
                });
        }, 30000);
    }
});
</script>
@endsection