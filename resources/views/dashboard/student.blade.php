@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h3 class="mb-4">
        <i class="bi bi-check-circle-fill text-success me-2"></i>
        Your Completed Documents
    </h3>
    
    <div class="alert alert-light border-start border-4 border-info">
        <div class="d-flex align-items-center">
            <i class="bi bi-info-circle text-info me-2"></i>
            <div>
                <strong>Note:</strong> This section shows only your completed document requests. 
                To track pending or active requests, please visit <a href="{{ route('student.my-requests') }}" class="text-decoration-none fw-bold">My Requests</a> page, 
                or <a href="{{ route('student.request.document') }}" class="text-decoration-none">submit a new request</a>.
            </div>
        </div>
    </div>

    @if($completedRequests->isEmpty())
        <div class="alert alert-info">
            <i class="bi bi-inbox me-2"></i>
            No completed documents yet. Your completed document requests will appear here.
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-bordered align-middle text-center">
                <thead class="table-success">
                    <tr>
                        <th>#</th>
                        <th>Reference #</th>
                        <th>Document Type</th>
                        <th>Total Cost</th>
                        <th>Status</th>
                        <th>Requested On</th>
                        <th>Release Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($completedRequests as $index => $request)
                        <tr>
                            <td class="text-center fw-bold text-muted">{{ $index + 1 }}</td>
                            <td class="fw-bold text-primary">{{ $request->reference_no }}</td>
                            <td class="text-start">
                                @if($request->requestItems->count() > 0)
                                    @foreach($request->requestItems as $item)
                                        <div class="mb-1">{{ $item->document->type_document ?? 'Unknown' }} (x{{ $item->quantity }})</div>
                                    @endforeach
                                    <div class="text-muted small mt-2">Total: ₱{{ number_format($request->total_cost, 2) }}</div>
                                @endif
                            </td>
                            <td>₱{{ number_format($request->total_cost, 2) }}</td>
                            <td>
                                <span class="badge bg-success">
                                    {{ ucfirst($request->status) }}
                                </span>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($request->created_at)->format('M j, Y') }}</td>
                            <td>{{ $request->expected_release_date ? \Carbon\Carbon::parse($request->expected_release_date)->format('M j, Y') : 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
