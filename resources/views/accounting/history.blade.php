@extends('layouts.accounting')

@section('title', 'Transaction History - NU Lipa')

@section('content')
<style>
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

    .table-row:hover {
        background-color: rgba(44, 49, 146, 0.05);
    }

    .table tbody td {
        padding: 0.85rem 0.75rem;
        vertical-align: middle;
        border: none;
    }

    .transaction-type-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.6rem;
        border-radius: 0.25rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .transaction-type-onsite {
        background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
        color: white;
    }

    .transaction-type-student {
        background: linear-gradient(135deg, #6f42c1 0%, #5a32a3 100%);
        color: white;
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
                    <h1 class="h3 mb-0 text-gray-800"><i class="bi bi-clock-history me-2"></i>Transaction History</h1>
                    <p class="text-muted mb-0">Approved Payment Transactions</p>
                </div>
                <div class="d-flex gap-2">
                    <span class="badge" style="background:linear-gradient(135deg,#17a2b8,#138496)">
                        {{ $approvedOnsiteRequests->count() }} Onsite
                    </span>
                    <span class="badge" style="background:linear-gradient(135deg,#6f42c1,#5a32a3)">
                        {{ $approvedStudentRequests->count() }} Student
                    </span>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card shadow mb-4">
                <div class="card-body pb-0">
                    <ul class="nav nav-tabs" id="historyTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="onsite-tab" data-bs-toggle="tab" data-bs-target="#onsite-pane" type="button" role="tab">
                                <i class="bi bi-building me-1"></i> Onsite Requests
                                <span class="tab-count onsite">{{ $approvedOnsiteRequests->count() }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="student-tab" data-bs-toggle="tab" data-bs-target="#student-pane" type="button" role="tab">
                                <i class="bi bi-person-badge me-1"></i> Student Requests
                                <span class="tab-count student">{{ $approvedStudentRequests->count() }}</span>
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="tab-content" id="historyTabContent">

                    {{-- ONSITE TAB --}}
                    <div class="tab-pane fade show active" id="onsite-pane" role="tabpanel">
                        <div class="card-body">
                            @if($approvedOnsiteRequests->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Full Name</th>
                                                <th>Total Amount</th>
                                                <th>Approved By</th>
                                                <th>Payment Date</th>
                                                <th>Receipt</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($approvedOnsiteRequests as $i => $t)
                                                <tr class="table-row">
                                                    <td><strong>{{ $i + 1 }}</strong></td>
                                                    <td>{{ $t->full_name ?? 'N/A' }}</td>
                                                    <td><strong class="text-success">â‚±{{ number_format($t->calculated_total_cost ?? 0, 2) }}</strong></td>
                                                    <td>
                                                        @if($t->accountingApprover)
                                                            {{ $t->accountingApprover->first_name }} {{ $t->accountingApprover->last_name }}
                                                        @else
                                                            <span class="text-muted">System</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <small class="text-muted d-block">{{ $t->payment_approved_at ? $t->payment_approved_at->format('M d, Y') : 'N/A' }}</small>
                                                        <small class="text-muted">{{ $t->payment_approved_at ? $t->payment_approved_at->format('h:i A') : '' }}</small>
                                                    </td>
                                                    <td>
                                                        @if($t->payment_receipt_path)
                                                            <a href="{{ route('accounting.receipt.onsite', $t) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                                                <i class="bi bi-eye me-1"></i>View
                                                            </a>
                                                        @else
                                                            <span class="text-muted">No receipt</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="bi bi-building" style="font-size:3rem;color:#dee2e6;"></i>
                                    <h5 class="mt-3 text-muted">No Onsite Transactions</h5>
                                    <p class="text-muted">There are no approved onsite payment transactions yet.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- STUDENT TAB --}}
                    <div class="tab-pane fade" id="student-pane" role="tabpanel">
                        <div class="card-body">
                            @if($approvedStudentRequests->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Student Name</th>
                                                <th>Reference No.</th>
                                                <th>Total Amount</th>
                                                <th>Approved By</th>
                                                <th>Payment Date</th>
                                                <th>Receipt</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($approvedStudentRequests as $i => $t)
                                                <tr class="table-row">
                                                    <td><strong>{{ $i + 1 }}</strong></td>
                                                    <td>
                                                        @if($t->student && $t->student->user)
                                                            {{ $t->student->user->first_name }} {{ $t->student->user->last_name }}
                                                        @else
                                                            <span class="text-muted">N/A</span>
                                                        @endif
                                                    </td>
                                                    <td><code>{{ $t->reference_no ?? 'N/A' }}</code></td>
                                                    <td><strong class="text-success">â‚±{{ number_format($t->total_cost ?? 0, 2) }}</strong></td>
                                                    <td>
                                                        @if($t->approvedByAccounting)
                                                            {{ $t->approvedByAccounting->first_name }} {{ $t->approvedByAccounting->last_name }}
                                                        @else
                                                            <span class="text-muted">System</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <small class="text-muted d-block">{{ $t->payment_approved_at ? $t->payment_approved_at->format('M d, Y') : 'N/A' }}</small>
                                                        <small class="text-muted">{{ $t->payment_approved_at ? $t->payment_approved_at->format('h:i A') : '' }}</small>
                                                    </td>
                                                    <td>
                                                        @if($t->payment_receipt_path)
                                                            <a href="{{ route('accounting.receipt.student', $t) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                                <i class="bi bi-eye me-1"></i>View
                                                            </a>
                                                        @else
                                                            <span class="text-muted">No receipt</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="bi bi-person-badge" style="font-size:3rem;color:#dee2e6;"></i>
                                    <h5 class="mt-3 text-muted">No Student Transactions</h5>
                                    <p class="text-muted">There are no approved student payment transactions yet.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
