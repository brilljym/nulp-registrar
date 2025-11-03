@extends('layouts.accounting')

@section('title', 'Transaction History - NU Lipa')

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
        background-color: rgba(44, 49, 146, 0.05);
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(44, 49, 146, 0.1);
    }

    .table tbody td {
        padding: 1rem 0.75rem;
        vertical-align: middle;
        border: none;
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

    .badge.bg-success {
        background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%) !important;
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

    .transaction-type-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
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
                    <span class="badge bg-success">{{ $transactionHistory->count() }} Total Transactions</span>
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
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="bi bi-check-circle me-2"></i>Approved Transactions</h6>
                </div>
                <div class="card-body">
                    @if($transactionHistory->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Type</th>
                                        <th>Student</th>
                                        <th>Total Amount</th>
                                        <th>Approved By</th>
                                        <th>Approved Date</th>
                                        <th>Receipt</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transactionHistory as $index => $transaction)
                                        <tr class="table-row">
                                            <td>
                                                <strong>{{ $index + 1 }}</strong>
                                            </td>
                                            <td>
                                                @if(isset($transaction->reference_no))
                                                    <span class="transaction-type-badge transaction-type-student">Student Request</span>
                                                @else
                                                    <span class="transaction-type-badge transaction-type-onsite">Onsite Request</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if(isset($transaction->student) && $transaction->student)
                                                    @if(isset($transaction->reference_no))
                                                        {{ $transaction->student->user->first_name }} {{ $transaction->student->user->last_name }}
                                                    @else
                                                        {{ $transaction->full_name }}
                                                    @endif
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                <strong class="text-success">
                                                    @if(isset($transaction->reference_no))
                                                        ₱{{ number_format($transaction->total_cost ?? 0, 2) }}
                                                    @else
                                                        ₱{{ number_format($transaction->calculated_total_cost ?? 0, 2) }}
                                                    @endif
                                                </strong>
                                            </td>
                                            <td>
                                                @if(isset($transaction->reference_no) && $transaction->approvedByAccounting)
                                                    {{ $transaction->approvedByAccounting->first_name }} {{ $transaction->approvedByAccounting->last_name }}
                                                @elseif($transaction->accountingApprover)
                                                    {{ $transaction->accountingApprover->first_name }} {{ $transaction->accountingApprover->last_name }}
                                                @else
                                                    <span class="text-muted">System</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $transaction->payment_approved_at ? $transaction->payment_approved_at->format('M d, Y') : 'N/A' }}
                                                </small>
                                                <br>
                                                <small class="text-muted">
                                                    {{ $transaction->payment_approved_at ? $transaction->payment_approved_at->format('h:i A') : '' }}
                                                </small>
                                            </td>
                                            <td>
                                                @if($transaction->payment_receipt_path)
                                                    @if(isset($transaction->reference_no))
                                                        <a href="{{ route('accounting.receipt.student', $transaction) }}" target="_blank" class="btn btn-sm btn-outline-primary action-btn">
                                                            <i class="bi bi-eye me-1"></i>View
                                                        </a>
                                                    @else
                                                        <a href="{{ route('accounting.receipt.onsite', $transaction) }}" target="_blank" class="btn btn-sm btn-outline-primary action-btn">
                                                            <i class="bi bi-eye me-1"></i>View
                                                        </a>
                                                    @endif
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
                            <i class="bi bi-clock-history" style="font-size: 3rem; color: #dee2e6;"></i>
                            <h5 class="mt-3 text-muted">No Transaction History</h5>
                            <p class="text-muted">There are no approved transactions yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection