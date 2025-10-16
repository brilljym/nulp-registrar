@extends('layouts.registrar')

@section('content')
<style>
    .stats-card {
        background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
        border-radius: 15px;
        padding: 2rem;
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        border: none;
        transition: all 0.3s ease;
        height: 100%;
    }
    
    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.15);
    }
    
                       <div class="col-md-6">
                        <p class="mb-2"><strong>🎯 Efficiency Goal:</strong> Process requests in under 1 hour</p>
                        <p class="mb-2"><strong>📈 Quality Focus:</strong> Maintain accuracy while improving speed</p>
                        <p class="mb-2"><strong>⏰ Peak Hours:</strong> 8 AM - 12 PM typically busiest</p>
                        <p class="mb-0"><strong>🎨 New Charts:</strong> Student vs Onsite request analytics</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-2"><strong>📊 Chart Features:</strong> Hover over charts for detailed data</p>
                        <p class="mb-2"><strong>🔄 Updates:</strong> Data refreshes automatically every 5 minutes</p>
                        <p class="mb-2"><strong>📱 Mobile:</strong> Charts are fully responsive on all devices</p>
                        <p class="mb-0"><strong>📈 Compare:</strong> Student & Onsite request patterns</p>
                    </div>mber {
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
    
    .primary-stat { color: #003399; }
    .success-stat { color: #28a745; }
    .warning-stat { color: #ffc107; }
    .danger-stat { color: #dc3545; }
    .info-stat { color: #0066cc; }
    
    .chart-card {
        background: #fff;
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        margin-bottom: 2rem;
    }
    
    .chart-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #003399;
        margin-bottom: 1rem;
        border-bottom: 2px solid #e9ecef;
        padding-bottom: 0.5rem;
    }
    
    .table-stats {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }
    
    .table-stats thead th {
        background: linear-gradient(135deg, #003399 0%, #001f5f 100%);
        color: #fff;
        font-weight: 600;
        border: none;
        padding: 1rem;
    }
    
    .table-stats tbody td {
        padding: 0.75rem 1rem;
        border-top: 1px solid #e9ecef;
    }

    .metric-card {
        background: #fff;
        border-radius: 10px;
        padding: 1.5rem;
        box-shadow: 0 3px 10px rgba(0,0,0,0.05);
        border-left: 4px solid #003399;
        margin-bottom: 1rem;
    }

    .metric-value {
        font-size: 1.8rem;
        font-weight: 700;
        color: #003399;
    }

    .metric-label {
        color: #6c757d;
        font-size: 0.9rem;
        margin-top: 0.5rem;
    }

    .performance-badge {
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .badge-excellent {
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
    }

    .badge-good {
        background: linear-gradient(135deg, #ffc107, #fd7e14);
        color: white;
    }

    .badge-needs-improvement {
        background: linear-gradient(135deg, #dc3545, #e83e8c);
        color: white;
    }
</style>

<div class="container-fluid mt-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-0" style="color: #003399; font-weight: 700;">
                <i class="fas fa-chart-bar me-2"></i>Reports & Analytics Dashboard
            </h2>
            <p class="text-muted mt-2">Your performance overview and document processing statistics</p>
        </div>
    </div>

    <!-- System Overview Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card position-relative">
                <i class="fas fa-file-alt stats-icon primary-stat"></i>
                <div class="stats-number primary-stat">{{ number_format($totalRequests ?? 0) }}</div>
                <div class="stats-label">Total Requests</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card position-relative">
                <i class="fas fa-building stats-icon info-stat"></i>
                <div class="stats-number info-stat">{{ number_format($onsiteRequests ?? 0) }}</div>
                <div class="stats-label">My Onsite Requests</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card position-relative">
                <i class="fas fa-check-circle stats-icon success-stat"></i>
                <div class="stats-number success-stat">{{ number_format($onsiteCompleted ?? 0) }}</div>
                <div class="stats-label">Completed by Me</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card position-relative">
                <i class="fas fa-clock stats-icon warning-stat"></i>
                <div class="stats-number warning-stat">{{ number_format($onsitePending ?? 0) }}</div>
                <div class="stats-label">Pending Assignment</div>
            </div>
        </div>
    </div>

    <!-- Request Status Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card position-relative">
                <i class="fas fa-clock stats-icon warning-stat"></i>
                <div class="stats-number warning-stat">{{ number_format($pendingRequests ?? 0) }}</div>
                <div class="stats-label">Pending</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card position-relative">
                <i class="fas fa-cogs stats-icon info-stat"></i>
                <div class="stats-number info-stat">{{ number_format($processingRequests ?? 0) }}</div>
                <div class="stats-label">Processing</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card position-relative">
                <i class="fas fa-check-circle stats-icon success-stat"></i>
                <div class="stats-number success-stat">{{ number_format($readyRequests ?? 0) }}</div>
                <div class="stats-label">Ready for Pickup</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card position-relative">
                <i class="fas fa-check-double stats-icon success-stat"></i>
                <div class="stats-number success-stat">{{ number_format($completedRequests ?? 0) }}</div>
                <div class="stats-label">Completed</div>
            </div>
        </div>
    </div>

    <!-- Personal Performance Analytics -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="chart-card">
                <h4 class="chart-title">
                    <i class="fas fa-stopwatch me-2"></i>My Processing Performance
                </h4>
                <div class="metric-card">
                    <div class="metric-value">{{ $myProcessingTime['formatted'] ?? '0 minutes' }}</div>
                    <div class="metric-label">Average Processing Time</div>
                </div>
                
                @php
                    $avgMinutes = ($myProcessingTime['hours'] ?? 0) * 60 + ($myProcessingTime['minutes'] ?? 0);
                    if ($avgMinutes < 60) {
                        $performanceClass = 'badge-excellent';
                        $performanceText = 'Excellent Performance';
                    } elseif ($avgMinutes < 240) {
                        $performanceClass = 'badge-good';
                        $performanceText = 'Good Performance';
                    } else {
                        $performanceClass = 'badge-needs-improvement';
                        $performanceText = 'Needs Improvement';
                    }
                @endphp
                
                <div class="text-center mt-3">
                    <span class="performance-badge {{ $performanceClass }}">
                        {{ $performanceText }}
                    </span>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="chart-card">
                <h4 class="chart-title">
                    <i class="fas fa-chart-bar me-2"></i>My Productivity Chart
                </h4>
                <div style="height: 300px; position: relative;">
                    <canvas id="productivityChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- 🆕 Charts & Visual Analytics Section -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="chart-card">
                <h4 class="chart-title">
                    <i class="fas fa-chart-pie me-2"></i>Document Request Distribution
                </h4>
                <div style="height: 350px; position: relative;">
                    <canvas id="documentDistributionChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="chart-card">
                <h4 class="chart-title">
                    <i class="fas fa-chart-donut me-2"></i>Request Status Overview
                </h4>
                <div style="height: 350px; position: relative;">
                    <canvas id="statusDistributionChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- 🆕 Time-based Analytics Chart -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="chart-card">
                <h4 class="chart-title">
                    <i class="fas fa-chart-line me-2"></i>Request Processing Trends
                </h4>
                <div style="height: 300px; position: relative;">
                    <canvas id="timeComparisonChart"></canvas>
                </div>
                <div class="row mt-3">
                    <div class="col-md-4 text-center">
                        <div class="metric-card">
                            <div class="metric-value text-primary">{{ $todayStats['new'] ?? 0 }}</div>
                            <div class="metric-label">New Today</div>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="metric-card">
                            <div class="metric-value text-success">{{ $weekStats['completed'] ?? 0 }}</div>
                            <div class="metric-label">Completed This Week</div>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="metric-card">
                            <div class="metric-value text-warning">{{ $monthStats['pending'] ?? 0 }}</div>
                            <div class="metric-label">Pending This Month</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 🆕 Student Requests vs Onsite Requests Analytics -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="chart-card">
                <h4 class="chart-title">
                    <i class="fas fa-graduation-cap me-2"></i>Student Request Status Distribution
                </h4>
                <div style="height: 350px; position: relative;">
                    <canvas id="studentRequestChart"></canvas>
                </div>
                <div class="row mt-3">
                    <div class="col-6 text-center">
                        <div class="metric-card">
                            <div class="metric-value text-info">{{ array_sum($studentRequestStats ?? []) }}</div>
                            <div class="metric-label">Total Student Requests</div>
                        </div>
                    </div>
                    <div class="col-6 text-center">
                        <div class="metric-card">
                            <div class="metric-value text-success">{{ $studentRequestStats['completed'] ?? 0 }}</div>
                            <div class="metric-label">Completed</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="chart-card">
                <h4 class="chart-title">
                    <i class="fas fa-building me-2"></i>Onsite Request Status Distribution
                </h4>
                <div style="height: 350px; position: relative;">
                    <canvas id="onsiteRequestChart"></canvas>
                </div>
                <div class="row mt-3">
                    <div class="col-6 text-center">
                        <div class="metric-card">
                            <div class="metric-value text-info">{{ array_sum($onsiteRequestStats ?? []) }}</div>
                            <div class="metric-label">Total Onsite Requests</div>
                        </div>
                    </div>
                    <div class="col-6 text-center">
                        <div class="metric-card">
                            <div class="metric-value text-success">{{ $onsiteRequestStats['completed'] ?? 0 }}</div>
                            <div class="metric-label">Completed</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 🆕 Document Type Distribution Comparison -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="chart-card">
                <h4 class="chart-title">
                    <i class="fas fa-file-alt me-2"></i>Student Request Document Types
                </h4>
                <div style="height: 350px; position: relative;">
                    <canvas id="studentDocumentChart"></canvas>
                </div>
                @if(isset($studentDocumentDistribution) && $studentDocumentDistribution->count() > 0)
                <div class="mt-3">
                    <small class="text-muted">
                        <strong>Most Popular:</strong> {{ $studentDocumentDistribution->first()->type_document ?? 'N/A' }}
                        ({{ $studentDocumentDistribution->first()->request_count ?? 0 }} requests)
                    </small>
                </div>
                @endif
            </div>
        </div>
        <div class="col-lg-6">
            <div class="chart-card">
                <h4 class="chart-title">
                    <i class="fas fa-clipboard-list me-2"></i>Onsite Request Document Types
                </h4>
                <div style="height: 350px; position: relative;">
                    <canvas id="onsiteDocumentChart"></canvas>
                </div>
                @if(isset($onsiteDocumentDistribution) && $onsiteDocumentDistribution->count() > 0)
                <div class="mt-3">
                    <small class="text-muted">
                        <strong>Most Popular:</strong> {{ $onsiteDocumentDistribution->first()->type_document ?? 'N/A' }}
                        ({{ $onsiteDocumentDistribution->first()->request_count ?? 0 }} requests)
                    </small>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Document Type Statistics with Enhanced Visualization -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="chart-card">
                <h4 class="chart-title">
                    <i class="fas fa-file-text me-2"></i>Document Type Request Statistics
                </h4>
                
                <!-- Summary Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="metric-card">
                            <div class="metric-value">{{ $documentStats->count() ?? 0 }}</div>
                            <div class="metric-label">Document Types</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="metric-card">
                            <div class="metric-value">{{ number_format($documentStats->sum('request_count') ?? 0) }}</div>
                            <div class="metric-label">Total Requests</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="metric-card">
                            <div class="metric-value">{{ number_format($documentStats->sum('total_quantity') ?? 0) }}</div>
                            <div class="metric-label">Total Documents</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="metric-card">
                            <div class="metric-value">{{ $documentStats->first()->type_document ?? 'N/A' }}</div>
                            <div class="metric-label">Most Popular</div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-stats">
                        <thead>
                            <tr>
                                <th>Document Type</th>
                                <th>Request Count</th>
                                <th>Total Quantity</th>
                                <th>Popularity</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php 
                                $totalRequestCount = $documentStats->sum('request_count');
                            @endphp
                            @forelse($documentStats as $stat)
                            <tr>
                                <td><strong>{{ $stat->type_document }}</strong></td>
                                <td>{{ number_format($stat->request_count) }}</td>
                                <td>{{ number_format($stat->total_quantity) }}</td>
                                <td>
                                    @php
                                        $percentage = $totalRequestCount > 0 ? ($stat->request_count / $totalRequestCount) * 100 : 0;
                                    @endphp
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar" style="width: {{ $percentage }}%; background: linear-gradient(135deg, #003399 0%, #001f5f 100%);"></div>
                                    </div>
                                    <small class="text-muted">{{ number_format($percentage, 1) }}%</small>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">No document requests found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Tips & Chart Guide -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="alert alert-info">
                <h6><i class="fas fa-lightbulb me-2"></i>Performance Tips & Chart Guide</h6>
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-2"><strong>🎯 Efficiency Goal:</strong> Process requests in under 1 hour</p>
                        <p class="mb-2"><strong>📈 Quality Focus:</strong> Maintain accuracy while improving speed</p>
                        <p class="mb-0"><strong>⏰ Peak Hours:</strong> 8 AM - 12 PM typically busiest</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-2"><strong>📊 Chart Features:</strong> Hover over charts for detailed data</p>
                        <p class="mb-2"><strong>🔄 Updates:</strong> Data refreshes automatically every 5 minutes</p>
                        <p class="mb-0"><strong>� Mobile:</strong> Charts are fully responsive on all devices</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="alert alert-success">
                <h6><i class="fas fa-chart-bar me-2"></i>Chart Legend</h6>
                <div class="d-flex align-items-center mb-2">
                    <div style="width: 20px; height: 20px; background: #003399; margin-right: 10px; border-radius: 3px;"></div>
                    <span>Primary Data</span>
                </div>
                <div class="d-flex align-items-center mb-2">
                    <div style="width: 20px; height: 20px; background: #28a745; margin-right: 10px; border-radius: 3px;"></div>
                    <span>Completed/Success</span>
                </div>
                <div class="d-flex align-items-center mb-2">
                    <div style="width: 20px; height: 20px; background: #ffc107; margin-right: 10px; border-radius: 3px;"></div>
                    <span>Pending/Warning</span>
                </div>
                <div class="d-flex align-items-center">
                    <div style="width: 20px; height: 20px; background: #0066cc; margin-right: 10px; border-radius: 3px;"></div>
                    <span>Processing/Info</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Instructions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-light border">
                <h6><i class="fas fa-info-circle me-2"></i>How to Read Your Charts</h6>
                <div class="row">
                    <div class="col-md-3">
                        <h6>📊 Pie Chart</h6>
                        <ul class="mb-0 small">
                            <li>Shows document distribution</li>
                            <li>Hover for percentages</li>
                            <li>Click legend to toggle sections</li>
                        </ul>
                    </div>
                    <div class="col-md-3">
                        <h6>🍩 Doughnut Chart</h6>
                        <ul class="mb-0 small">
                            <li>Request status overview</li>
                            <li>Center shows total count</li>
                            <li>Segments show proportions</li>
                        </ul>
                    </div>
                    <div class="col-md-3">
                        <h6>📈 Line Chart</h6>
                        <ul class="mb-0 small">
                            <li>Trends over time periods</li>
                            <li>Multiple data series</li>
                            <li>Smooth curve interpolation</li>
                        </ul>
                    </div>
                    <div class="col-md-3">
                        <h6>📊 Bar Chart</h6>
                        <ul class="mb-0 small">
                            <li>Personal productivity metrics</li>
                            <li>Comparative values</li>
                            <li>Color-coded categories</li>
                        </ul>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <h6>🎓 Student Request Charts</h6>
                        <ul class="mb-0 small">
                            <li><strong>Status Distribution:</strong> Pie chart showing request phases</li>
                            <li><strong>Document Types:</strong> Popular document requests</li>
                            <li><strong>Completion Rate:</strong> Success percentage tracking</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>🏢 Onsite Request Charts</h6>
                        <ul class="mb-0 small">
                            <li><strong>Status Distribution:</strong> Doughnut chart with center summary</li>
                            <li><strong>Document Types:</strong> Walk-in service patterns</li>
                            <li><strong>Processing Speed:</strong> Real-time completion tracking</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- FontAwesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 📊 Document Distribution Pie Chart
        @if(isset($documentStats) && $documentStats->count() > 0)
        const docDistCtx = document.getElementById('documentDistributionChart').getContext('2d');
        new Chart(docDistCtx, {
            type: 'pie',
            data: {
                labels: {!! json_encode($documentStats->pluck('type_document')) !!},
                datasets: [{
                    data: {!! json_encode($documentStats->pluck('request_count')) !!},
                    backgroundColor: [
                        '#003399', '#28a745', '#ffc107', '#dc3545', '#0066cc',
                        '#6f42c1', '#fd7e14', '#20c997', '#e83e8c', '#6c757d'
                    ],
                    borderWidth: 3,
                    borderColor: '#fff',
                    hoverBorderWidth: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            font: { size: 11 },
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.parsed / total) * 100).toFixed(1);
                                return context.label + ': ' + context.parsed + ' requests (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
        @endif

        // 📈 Status Distribution Doughnut Chart
        const statusCtx = document.getElementById('statusDistributionChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'Processing', 'Ready', 'Completed'],
                datasets: [{
                    data: [
                        {{ $pendingRequests ?? 0 }},
                        {{ $processingRequests ?? 0 }},
                        {{ $readyRequests ?? 0 }},
                        {{ $completedRequests ?? 0 }}
                    ],
                    backgroundColor: ['#ffc107', '#0066cc', '#28a745', '#003399'],
                    borderWidth: 3,
                    borderColor: '#fff',
                    hoverBorderWidth: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '60%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            font: { size: 11 },
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                                return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });

        // 📊 Personal Productivity Bar Chart
        @if(isset($myProductivity))
        const productivityCtx = document.getElementById('productivityChart').getContext('2d');
        new Chart(productivityCtx, {
            type: 'bar',
            data: {
                labels: ['Today', 'This Week', 'This Month', 'Total'],
                datasets: [{
                    label: 'Completed Requests',
                    data: [
                        {{ $myProductivity['today'] ?? 0 }},
                        {{ $myProductivity['this_week'] ?? 0 }},
                        {{ $myProductivity['this_month'] ?? 0 }},
                        {{ $myProductivity['total'] ?? 0 }}
                    ],
                    backgroundColor: [
                        'rgba(0, 51, 153, 0.8)',
                        'rgba(40, 167, 69, 0.8)',
                        'rgba(0, 102, 204, 0.8)',
                        'rgba(255, 193, 7, 0.8)'
                    ],
                    borderColor: [
                        '#003399',
                        '#28a745',
                        '#0066cc',
                        '#ffc107'
                    ],
                    borderWidth: 2,
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.parsed.y + ' requests completed';
                            }
                        }
                    }
                }
            }
        });
        @endif

        // 📈 Processing Time Comparison Line Chart
        @if(isset($todayStats) && isset($weekStats) && isset($monthStats))
        const timeComparisonCtx = document.getElementById('timeComparisonChart').getContext('2d');
        new Chart(timeComparisonCtx, {
            type: 'line',
            data: {
                labels: ['New Requests', 'Completed', 'Pending'],
                datasets: [{
                    label: 'Today',
                    data: [
                        {{ $todayStats['new'] ?? 0 }},
                        {{ $todayStats['completed'] ?? 0 }},
                        {{ $todayStats['pending'] ?? 0 }}
                    ],
                    borderColor: '#003399',
                    backgroundColor: 'rgba(0, 51, 153, 0.1)',
                    borderWidth: 3,
                    fill: false,
                    tension: 0.4
                }, {
                    label: 'This Week',
                    data: [
                        {{ $weekStats['new'] ?? 0 }},
                        {{ $weekStats['completed'] ?? 0 }},
                        {{ $weekStats['pending'] ?? 0 }}
                    ],
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    borderWidth: 3,
                    fill: false,
                    tension: 0.4
                }, {
                    label: 'This Month',
                    data: [
                        {{ $monthStats['new'] ?? 0 }},
                        {{ $monthStats['completed'] ?? 0 }},
                        {{ $monthStats['pending'] ?? 0 }}
                    ],
                    borderColor: '#ffc107',
                    backgroundColor: 'rgba(255, 193, 7, 0.1)',
                    borderWidth: 3,
                    fill: false,
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
                        position: 'top',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    }
                }
            }
        });
        @endif
        
        // 🆕 Student Request Status Distribution Chart
        @if(isset($studentRequestStats))
        const studentRequestCtx = document.getElementById('studentRequestChart').getContext('2d');
        new Chart(studentRequestCtx, {
            type: 'pie',
            data: {
                labels: ['Pending', 'Processing', 'Ready', 'Completed', 'Rejected'],
                datasets: [{
                    data: [
                        {{ $studentRequestStats['pending'] ?? 0 }},
                        {{ $studentRequestStats['processing'] ?? 0 }},
                        {{ $studentRequestStats['ready_for_release'] ?? 0 }},
                        {{ $studentRequestStats['completed'] ?? 0 }},
                        {{ $studentRequestStats['rejected'] ?? 0 }}
                    ],
                    backgroundColor: [
                        '#ffc107', // Pending - Warning Yellow
                        '#0066cc', // Processing - Info Blue  
                        '#17a2b8', // Ready - Info Cyan
                        '#28a745', // Completed - Success Green
                        '#dc3545'  // Rejected - Danger Red
                    ],
                    borderWidth: 3,
                    borderColor: '#fff',
                    hoverBorderWidth: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            font: { size: 10 },
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                                return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
        @endif

        // 🆕 Onsite Request Status Distribution Chart
        @if(isset($onsiteRequestStats))
        const onsiteRequestCtx = document.getElementById('onsiteRequestChart').getContext('2d');
        new Chart(onsiteRequestCtx, {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'Processing', 'Completed', 'Cancelled'],
                datasets: [{
                    data: [
                        {{ $onsiteRequestStats['pending'] ?? 0 }},
                        {{ $onsiteRequestStats['processing'] ?? 0 }},
                        {{ $onsiteRequestStats['completed'] ?? 0 }},
                        {{ $onsiteRequestStats['cancelled'] ?? 0 }}
                    ],
                    backgroundColor: [
                        '#ffc107', // Pending - Warning Yellow
                        '#0066cc', // Processing - Info Blue
                        '#28a745', // Completed - Success Green
                        '#6c757d'  // Cancelled - Secondary Gray
                    ],
                    borderWidth: 3,
                    borderColor: '#fff',
                    hoverBorderWidth: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '60%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            font: { size: 10 },
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                                return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
        @endif

        // 🆕 Student Document Type Distribution Chart
        @if(isset($studentDocumentDistribution) && $studentDocumentDistribution->count() > 0)
        const studentDocCtx = document.getElementById('studentDocumentChart').getContext('2d');
        new Chart(studentDocCtx, {
            type: 'pie',
            data: {
                labels: {!! json_encode($studentDocumentDistribution->pluck('type_document')) !!},
                datasets: [{
                    data: {!! json_encode($studentDocumentDistribution->pluck('request_count')) !!},
                    backgroundColor: [
                        '#003399', '#28a745', '#ffc107', '#dc3545', '#0066cc',
                        '#6f42c1', '#fd7e14', '#20c997', '#e83e8c', '#6c757d',
                        '#17a2b8', '#343a40', '#f8f9fa', '#495057', '#adb5bd'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff',
                    hoverBorderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 12,
                            font: { size: 9 },
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                                return context.label + ': ' + context.parsed + ' requests (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
        @endif

        // 🆕 Onsite Document Type Distribution Chart
        @if(isset($onsiteDocumentDistribution) && $onsiteDocumentDistribution->count() > 0)
        const onsiteDocCtx = document.getElementById('onsiteDocumentChart').getContext('2d');
        new Chart(onsiteDocCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($onsiteDocumentDistribution->pluck('type_document')) !!},
                datasets: [{
                    data: {!! json_encode($onsiteDocumentDistribution->pluck('request_count')) !!},
                    backgroundColor: [
                        '#0066cc', '#28a745', '#ffc107', '#dc3545', '#6f42c1',
                        '#fd7e14', '#20c997', '#e83e8c', '#6c757d', '#003399',
                        '#17a2b8', '#343a40', '#f8f9fa', '#495057', '#adb5bd'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff',
                    hoverBorderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '50%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 12,
                            font: { size: 9 },
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                                return context.label + ': ' + context.parsed + ' requests (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
        @endif
    });
</script>
@endsection
