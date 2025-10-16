<div class="row">
    <div class="col-12">
        <h5 class="mb-3">
            <i class="fas fa-file-alt me-2"></i>{{ $document->type_document }}
        </h5>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h3>{{ number_format($totalRequests) }}</h3>
                <p class="mb-0">Total Requests</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h3>{{ number_format($totalQuantity) }}</h3>
                <p class="mb-0">Documents Issued</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h3>₱{{ number_format($totalRevenue, 2) }}</h3>
                <p class="mb-0">Revenue Generated</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <h3>₱{{ number_format($document->price, 2) }}</h3>
                <p class="mb-0">Current Price</p>
            </div>
        </div>
    </div>
</div>

<!-- Monthly Trend Chart -->
<div class="row mb-4">
    <div class="col-12">
        <h6>Monthly Request Trend (Last 6 Months)</h6>
        <canvas id="monthlyTrendChart" height="100"></canvas>
    </div>
</div>

<!-- Recent Requests -->
<div class="row">
    <div class="col-12">
        <h6>Recent Requests</h6>
        <div class="table-responsive">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Quantity</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentRequests as $request)
                    <tr>
                        <td>
                            {{ $request->studentRequest->student->user->first_name }} 
                            {{ $request->studentRequest->student->user->last_name }}
                        </td>
                        <td>{{ $request->quantity }}</td>
                        <td>{{ $request->created_at->format('M j, Y') }}</td>
                        <td>
                            <span class="badge bg-{{ $request->studentRequest->status === 'completed' ? 'success' : 'warning' }}">
                                {{ ucfirst($request->studentRequest->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">No recent requests found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('monthlyTrendChart').getContext('2d');
    const chartData = @json($monthlyTrend);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.map(item => item.month),
            datasets: [{
                label: 'Requests',
                data: chartData.map(item => item.count),
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
});
</script>