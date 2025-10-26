

<?php $__env->startSection('content'); ?>
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
        color: #2c3192;
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
        background: linear-gradient(135deg, #2c3192 0%, #1e2570 100%);
        color: #fff;
        font-weight: 600;
        border: none;
        padding: 1rem;
    }
    
    .table-stats tbody td {
        padding: 0.75rem 1rem;
        border-top: 1px solid #e9ecef;
    }

    .export-btn {
        background: linear-gradient(135deg, #2c3192 0%, #1e2570 100%);
        color: white;
        border: none;
        border-radius: 8px;
        padding: 0.5rem 1rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .export-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(44, 49, 146, 0.3);
        color: white;
    }

    .metric-card {
        background: #fff;
        border-radius: 10px;
        padding: 1.5rem;
        box-shadow: 0 3px 10px rgba(0,0,0,0.05);
        border-left: 4px solid #2c3192;
        margin-bottom: 1rem;
    }

    .metric-value {
        font-size: 1.8rem;
        font-weight: 700;
        color: #2c3192;
    }

    .metric-label {
        color: #6c757d;
        font-size: 0.9rem;
        margin-top: 0.5rem;
    }

    .progress-bar-custom {
        height: 20px;
        border-radius: 10px;
        background: linear-gradient(135deg, #2c3192 0%, #1e2570 100%);
    }

    .report-section {
        background: #fff;
        border-radius: 15px;
        padding: 2rem;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        margin-bottom: 2rem;
    }

    .print-btn {
        background: linear-gradient(135deg, #28a745 0%, #20873a 100%);
        color: white;
        border: none;
        border-radius: 8px;
        padding: 0.5rem 1rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .print-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        color: white;
    }

    @media print {
        .no-print {
            display: none !important;
        }
        
        .stats-card, .chart-card, .report-section {
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
                        <i class="fas fa-chart-bar me-2"></i>System Reports & Analytics
                    </h2>
                    <p class="text-muted mt-2">Comprehensive overview of system statistics and trends</p>
                </div>
                <div class="no-print">
                    <button class="btn print-btn me-2" onclick="window.print()">
                        <i class="fas fa-print me-2"></i>Print Report
                    </button>
                    <div class="dropdown d-inline-block">
                        <button class="btn export-btn dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-download me-2"></i>Export Reports
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?php echo e(route('admin.reports.export', ['type' => 'summary'])); ?>">
                                <i class="fas fa-file-csv me-2"></i>Summary Report (CSV)
                            </a></li>
                            <li><a class="dropdown-item" href="<?php echo e(route('admin.reports.export', ['type' => 'document_types'])); ?>">
                                <i class="fas fa-file-csv me-2"></i>Document Types Report (CSV)
                            </a></li>
                            <li><a class="dropdown-item" href="<?php echo e(route('admin.reports.export', ['type' => 'processing_times'])); ?>">
                                <i class="fas fa-file-csv me-2"></i>Processing Times Report (CSV)
                            </a></li>
                            <li><a class="dropdown-item" href="<?php echo e(route('admin.reports.export', ['type' => 'queue_performance'])); ?>">
                                <i class="fas fa-file-csv me-2"></i>Queue Performance Report (CSV)
                            </a></li>
                            <li><a class="dropdown-item" href="<?php echo e(route('admin.reports.export', ['type' => 'registrar_performance'])); ?>">
                                <i class="fas fa-file-csv me-2"></i>Registrar Performance Report (CSV)
                            </a></li>
                            <li><a class="dropdown-item" href="<?php echo e(route('admin.reports.export', ['type' => 'revenue'])); ?>">
                                <i class="fas fa-file-csv me-2"></i>Revenue Report (CSV)
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?php echo e(route('admin.reports.export', ['type' => 'predictive'])); ?>">
                                <i class="fas fa-file-csv me-2"></i>🆕 Predictive Analytics Report (CSV)
                            </a></li>
                            <li><a class="dropdown-item" href="<?php echo e(route('admin.reports.export', ['type' => 'trends'])); ?>">
                                <i class="fas fa-file-csv me-2"></i>🆕 Trend Analysis Report (CSV)
                            </a></li>
                            <li><a class="dropdown-item" href="<?php echo e(route('admin.reports.export', ['type' => 'performance'])); ?>">
                                <i class="fas fa-file-csv me-2"></i>🆕 Performance Metrics Report (CSV)
                            </a></li>
                            <li><a class="dropdown-item" href="<?php echo e(route('admin.reports.export', ['type' => 'complete'])); ?>">
                                <i class="fas fa-file-archive me-2"></i>🆕 Complete Analytics Package (ZIP)
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Overview Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card position-relative">
                <i class="fas fa-users stats-icon primary-stat"></i>
                <div class="stats-number primary-stat"><?php echo e(number_format($totalUsers)); ?></div>
                <div class="stats-label">Total Users</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card position-relative">
                <i class="fas fa-user-graduate stats-icon success-stat"></i>
                <div class="stats-number success-stat"><?php echo e(number_format($totalStudents)); ?></div>
                <div class="stats-label">Students</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card position-relative">
                <i class="fas fa-user-tie stats-icon info-stat"></i>
                <div class="stats-number info-stat"><?php echo e(number_format($totalRegistrars)); ?></div>
                <div class="stats-label">Registrars</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card position-relative">
                <i class="fas fa-file-alt stats-icon warning-stat"></i>
                <div class="stats-number warning-stat"><?php echo e(number_format($totalDocuments)); ?></div>
                <div class="stats-label">Documents</div>
            </div>
        </div>
    </div>

    <!-- Request Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card position-relative">
                <i class="fas fa-inbox stats-icon primary-stat"></i>
                <div class="stats-number primary-stat"><?php echo e(number_format($totalRequests ?? 0)); ?></div>
                <div class="stats-label">Total Requests</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card position-relative">
                <i class="fas fa-clock stats-icon warning-stat"></i>
                <div class="stats-number warning-stat"><?php echo e(number_format($pendingRequests ?? 0)); ?></div>
                <div class="stats-label">Pending</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card position-relative">
                <i class="fas fa-cogs stats-icon info-stat"></i>
                <div class="stats-number info-stat"><?php echo e(number_format($processingRequests ?? 0)); ?></div>
                <div class="stats-label">Processing</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card position-relative">
                <i class="fas fa-check-circle stats-icon success-stat"></i>
                <div class="stats-number success-stat"><?php echo e(number_format($completedRequests ?? 0)); ?></div>
                <div class="stats-label">Completed</div>
            </div>
        </div>
    </div>

    <!-- Document Type Analysis -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="report-section">
                <h4 class="chart-title">
                    <i class="fas fa-file-text me-2"></i>Document Type Requests Analysis
                </h4>
                <div class="table-responsive">
                    <table class="table table-stats">
                        <thead>
                            <tr>
                                <th>Document Type</th>
                                <th>Request Count</th>
                                <th>Total Quantity</th>
                                <th>Percentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $totalRequestCount = $documentTypeStats->sum('request_count');
                            ?>
                            <?php $__empty_1 = true; $__currentLoopData = $documentTypeStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><strong><?php echo e($stat->type_document); ?></strong></td>
                                <td><?php echo e(number_format($stat->request_count)); ?></td>
                                <td><?php echo e(number_format($stat->total_quantity)); ?></td>
                                <td>
                                    <?php
                                        $percentage = $totalRequestCount > 0 ? ($stat->request_count / $totalRequestCount) * 100 : 0;
                                    ?>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar progress-bar-custom" style="width: <?php echo e($percentage); ?>%"></div>
                                    </div>
                                    <small class="text-muted"><?php echo e(number_format($percentage, 1)); ?>%</small>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted">No document requests found</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="report-section">
                <h4 class="chart-title">
                    <i class="fas fa-chart-pie me-2"></i>Document Distribution
                </h4>
                <div style="height: 300px; display: flex; align-items: center; justify-content: center;">
                    <canvas id="documentTypesChart" width="300" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- 🆕 Charts & Visual Analytics Section -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="report-section">
                <h4 class="chart-title">
                    <i class="fas fa-chart-line me-2"></i>Monthly Request Trends
                </h4>
                <div style="height: 300px;">
                    <canvas id="monthlyTrendsChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="report-section">
                <h4 class="chart-title">
                    <i class="fas fa-chart-bar me-2"></i>Daily Requests This Month
                </h4>
                <div style="height: 300px;">
                    <canvas id="dailyRequestsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- 🆕 Student Requests vs Onsite Requests Analytics -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="report-section">
                <h4 class="chart-title">
                    <i class="fas fa-graduation-cap me-2"></i>Student Request Status Distribution
                </h4>
                <div style="height: 350px; display: flex; align-items: center; justify-content: center;">
                    <canvas id="studentRequestChart" width="350" height="350"></canvas>
                </div>
                <div class="row mt-3">
                    <div class="col-6 text-center">
                        <div class="metric-card">
                            <div class="metric-value text-info"><?php echo e($totalOnlineRequests ?? 0); ?></div>
                            <div class="metric-label">Total Student Requests</div>
                        </div>
                    </div>
                    <div class="col-6 text-center">
                        <div class="metric-card">
                            <div class="metric-value text-success"><?php echo e($completedRequests ?? 0); ?></div>
                            <div class="metric-label">Completed</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="report-section">
                <h4 class="chart-title">
                    <i class="fas fa-building me-2"></i>Onsite Request Status Distribution
                </h4>
                <div style="height: 350px; display: flex; align-items: center; justify-content: center;">
                    <canvas id="onsiteRequestChart" width="350" height="350"></canvas>
                </div>
                <div class="row mt-3">
                    <div class="col-6 text-center">
                        <div class="metric-card">
                            <div class="metric-value text-info"><?php echo e($totalOnsiteRequests ?? 0); ?></div>
                            <div class="metric-label">Total Onsite Requests</div>
                        </div>
                    </div>
                    <div class="col-6 text-center">
                        <div class="metric-card">
                            <div class="metric-value text-success"><?php echo e(\App\Models\OnsiteRequest::where('status', 'completed')->count()); ?></div>
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
            <div class="report-section">
                <h4 class="chart-title">
                    <i class="fas fa-file-alt me-2"></i>Student Request Document Types
                </h4>
                <div style="height: 350px; display: flex; align-items: center; justify-content: center;">
                    <canvas id="studentDocumentChart" width="350" height="350"></canvas>
                </div>
                <?php if(isset($chartData['studentDocumentTypes']) && $chartData['studentDocumentTypes']->count() > 0): ?>
                <div class="mt-3">
                    <small class="text-muted">
                        <strong>Most Popular:</strong> <?php echo e($chartData['studentDocumentTypes']->first()->label ?? 'N/A'); ?>

                        (<?php echo e($chartData['studentDocumentTypes']->first()->value ?? 0); ?> requests)
                    </small>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="report-section">
                <h4 class="chart-title">
                    <i class="fas fa-clipboard-list me-2"></i>Onsite Request Document Types
                </h4>
                <div style="height: 350px; display: flex; align-items: center; justify-content: center;">
                    <canvas id="onsiteDocumentChart" width="350" height="350"></canvas>
                </div>
                <?php if(isset($chartData['onsiteDocumentTypes']) && $chartData['onsiteDocumentTypes']->count() > 0): ?>
                <div class="mt-3">
                    <small class="text-muted">
                        <strong>Most Popular:</strong> <?php echo e($chartData['onsiteDocumentTypes']->first()->label ?? 'N/A'); ?>

                        (<?php echo e($chartData['onsiteDocumentTypes']->first()->value ?? 0); ?> requests)
                    </small>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Status Overview with Chart -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="report-section">
                <h4 class="chart-title">
                    <i class="fas fa-tasks me-2"></i>Current Request Status Overview
                </h4>
                <div class="row">
                    <div class="col-md-3">
                        <div class="metric-card">
                            <div class="metric-value text-warning"><?php echo e(number_format($pendingRequests)); ?></div>
                            <div class="metric-label">Pending</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="metric-card">
                            <div class="metric-value text-info"><?php echo e(number_format($processingRequests)); ?></div>
                            <div class="metric-label">Processing</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="metric-card">
                            <div class="metric-value text-success"><?php echo e(number_format($readyRequests)); ?></div>
                            <div class="metric-label">Ready</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="metric-card">
                            <div class="metric-value text-primary"><?php echo e(number_format($completedRequests)); ?></div>
                            <div class="metric-label">Completed</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="report-section">
                <h4 class="chart-title">
                    <i class="fas fa-chart-donut me-2"></i>Status Distribution
                </h4>
                <div style="height: 200px; display: flex; align-items: center; justify-content: center;">
                    <canvas id="statusChart" width="200" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- 🆕 Predictive Analytics Section -->
    <?php if(isset($predictiveAnalytics)): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="report-section">
                <h4 class="chart-title">
                    <i class="fas fa-crystal-ball me-2"></i>Predictive Analytics & Release Time Estimation
                </h4>
                
                <!-- Summary Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="metric-card">
                            <div class="metric-value text-warning"><?php echo e($predictiveAnalytics['summary']['total_pending']); ?></div>
                            <div class="metric-label">Requests in Queue</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="metric-card">
                            <div class="metric-value text-info"><?php echo e($predictiveAnalytics['summary']['avg_processing_hours']); ?>h</div>
                            <div class="metric-label">Avg Processing Time</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="metric-card">
                            <div class="metric-value text-primary" style="font-size: 1.4rem;"><?php echo e($predictiveAnalytics['summary']['estimated_queue_clear_time']); ?></div>
                            <div class="metric-label">Estimated Queue Clear Time</div>
                        </div>
                    </div>
                </div>

                <!-- Predictions Table -->
                <?php if($predictiveAnalytics['predictions']->count() > 0): ?>
                <h5 class="mb-3"><i class="fas fa-clock me-2"></i>Next 20 Requests - Estimated Release Times</h5>
                <div class="table-responsive">
                    <table class="table table-stats">
                        <thead>
                            <tr>
                                <th>Reference No.</th>
                                <th>Queue Position</th>
                                <th>Estimated Release</th>
                                <th>Processing Hours</th>
                                <th>Confidence</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $predictiveAnalytics['predictions']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prediction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><strong><?php echo e($prediction['reference_no']); ?></strong></td>
                                <td>
                                    <span class="badge bg-secondary"><?php echo e($prediction['queue_position']); ?></span>
                                </td>
                                <td><?php echo e($prediction['estimated_release']); ?></td>
                                <td><?php echo e($prediction['estimated_hours']); ?> hours</td>
                                <td>
                                    <?php
                                        $badgeClass = $prediction['confidence'] === 'high' ? 'success' : 
                                                     ($prediction['confidence'] === 'medium' ? 'warning' : 'danger');
                                    ?>
                                    <span class="badge bg-<?php echo e($badgeClass); ?>"><?php echo e(ucfirst($prediction['confidence'])); ?></span>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="alert alert-info mt-3">
                    <h6><i class="fas fa-info-circle me-2"></i>Prediction Model Information</h6>
                    <p class="mb-1"><strong>Algorithm:</strong> Historical processing time analysis with queue position weighting</p>
                    <p class="mb-1"><strong>Confidence Levels:</strong> High (>10 samples), Medium (3-10 samples), Low (<3 samples or no historical data)</p>
                    <p class="mb-0"><strong>Note:</strong> Estimates are based on historical data and current queue status. Actual processing times may vary due to document complexity, registrar availability, and other factors.</p>
                </div>
                <?php else: ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>No pending requests found for prediction analysis.
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- 🆕 Advanced Document Statistics -->
    <?php if(isset($documentStatistics)): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="report-section">
                <h4 class="chart-title">
                    <i class="fas fa-file-chart me-2"></i>Advanced Document Statistics & Trends
                </h4>
                
                <!-- Quick Stats -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="metric-card">
                            <div class="metric-value"><?php echo e($documentStatistics['total_document_types']); ?></div>
                            <div class="metric-label">Document Types Available</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="metric-card">
                            <div class="metric-value">₱<?php echo e(number_format($documentStatistics['avg_document_price'], 2)); ?></div>
                            <div class="metric-label">Average Document Price</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="metric-card">
                            <div class="metric-value">₱<?php echo e(number_format($documentStatistics['most_expensive']->price ?? 0, 2)); ?></div>
                            <div class="metric-label">Most Expensive</div>
                            <small class="text-muted"><?php echo e($documentStatistics['most_expensive']->type_document ?? 'N/A'); ?></small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="metric-card">
                            <div class="metric-value">₱<?php echo e(number_format($documentStatistics['least_expensive']->price ?? 0, 2)); ?></div>
                            <div class="metric-label">Least Expensive</div>
                            <small class="text-muted"><?php echo e($documentStatistics['least_expensive']->type_document ?? 'N/A'); ?></small>
                        </div>
                    </div>
                </div>

                <!-- Detailed Document Analysis -->
                <h5 class="mb-3"><i class="fas fa-trend-up me-2"></i>Document Performance & Trends</h5>
                <div class="table-responsive">
                    <table class="table table-stats">
                        <thead>
                            <tr>
                                <th>Document Type</th>
                                <th>Total Requests</th>
                                <th>Total Revenue</th>
                                <th>Avg Qty/Request</th>
                                <th>Trend</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $documentStatistics['top_documents']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><strong><?php echo e($doc['type_document']); ?></strong></td>
                                <td><?php echo e(number_format($doc['total_requests'])); ?></td>
                                <td>₱<?php echo e(number_format($doc['total_revenue'], 2)); ?></td>
                                <td><?php echo e(number_format($doc['avg_quantity_per_request'], 1)); ?></td>
                                <td>
                                    <?php if($doc['trend'] === 'increasing'): ?>
                                        <span class="text-success">
                                            <i class="fas fa-arrow-up"></i> +<?php echo e($doc['trend_percentage']); ?>%
                                        </span>
                                    <?php elseif($doc['trend'] === 'decreasing'): ?>
                                        <span class="text-danger">
                                            <i class="fas fa-arrow-down"></i> <?php echo e($doc['trend_percentage']); ?>%
                                        </span>
                                    <?php elseif($doc['trend'] === 'new'): ?>
                                        <span class="text-info">
                                            <i class="fas fa-star"></i> New
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">
                                            <i class="fas fa-minus"></i> Stable
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Original Processing Time Section (updated position) -->
    <!-- Original Processing Time Section (updated position) -->
    <div class="row mb-4">
        <div class="col-lg-4">
            <div class="report-section">
                <h4 class="chart-title">
                    <i class="fas fa-stopwatch me-2"></i>Processing Time Analytics
                </h4>
                <div class="metric-card">
                    <div class="metric-value"><?php echo e($avgProcessingTime['formatted'] ?? '0 minutes'); ?></div>
                    <div class="metric-label">Average Processing Time</div>
                </div>
                <?php if(isset($processingTimeStats)): ?>
                <div class="row">
                    <div class="col-6">
                        <div class="text-center mb-3">
                            <div class="metric-value text-success"><?php echo e(number_format($processingTimeStats['under_1_hour'])); ?></div>
                            <div class="metric-label">Under 1 Hour</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center mb-3">
                            <div class="metric-value text-warning"><?php echo e(number_format($processingTimeStats['under_4_hours'])); ?></div>
                            <div class="metric-label">1-4 Hours</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center mb-3">
                            <div class="metric-value text-info"><?php echo e(number_format($processingTimeStats['under_24_hours'])); ?></div>
                            <div class="metric-label">4-24 Hours</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center mb-3">
                            <div class="metric-value text-danger"><?php echo e(number_format($processingTimeStats['over_24_hours'])); ?></div>
                            <div class="metric-label">Over 24 Hours</div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

    <!-- 🆕 Trend Analytics Section -->
    <?php if(isset($trendAnalytics)): ?>
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="report-section">
                <h4 class="chart-title">
                    <i class="fas fa-chart-line me-2"></i>Trend Analytics & Patterns
                </h4>
                
                <!-- Weekly Trend -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="metric-card">
                            <div class="metric-value"><?php echo e($trendAnalytics['weekly_trend']['this_week']); ?></div>
                            <div class="metric-label">This Week's Requests</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="metric-card">
                            <div class="metric-value">
                                <?php if($trendAnalytics['weekly_trend']['trend_direction'] === 'up'): ?>
                                    <span class="text-success">
                                        <i class="fas fa-arrow-up"></i> +<?php echo e($trendAnalytics['weekly_trend']['percentage_change']); ?>%
                                    </span>
                                <?php elseif($trendAnalytics['weekly_trend']['trend_direction'] === 'down'): ?>
                                    <span class="text-danger">
                                        <i class="fas fa-arrow-down"></i> <?php echo e($trendAnalytics['weekly_trend']['percentage_change']); ?>%
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted">
                                        <i class="fas fa-minus"></i> Stable
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div class="metric-label">Weekly Trend</div>
                        </div>
                    </div>
                </div>

                <!-- Day of Week Patterns -->
                <h5 class="mb-3"><i class="fas fa-calendar-week me-2"></i>Request Patterns by Day of Week</h5>
                <div class="table-responsive">
                    <table class="table table-stats">
                        <thead>
                            <tr>
                                <th>Day</th>
                                <th>Total Requests</th>
                                <th>Distribution</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $maxDayRequests = $trendAnalytics['day_patterns']->max('requests'); ?>
                            <?php $__currentLoopData = $trendAnalytics['day_patterns']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dayPattern): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><strong><?php echo e($dayPattern['day']); ?></strong></td>
                                <td><?php echo e(number_format($dayPattern['requests'])); ?></td>
                                <td>
                                    <?php
                                        $percentage = $maxDayRequests > 0 ? ($dayPattern['requests'] / $maxDayRequests) * 100 : 0;
                                    ?>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar progress-bar-custom" style="width: <?php echo e($percentage); ?>%"></div>
                                    </div>
                                    <small class="text-muted"><?php echo e(number_format($percentage, 1)); ?>% of peak</small>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="report-section">
                <h4 class="chart-title">
                    <i class="fas fa-chart-area me-2"></i>Quarterly Analysis
                </h4>
                <?php $__currentLoopData = $trendAnalytics['quarterly_analysis']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $quarter): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="metric-card">
                    <div class="metric-value"><?php echo e(number_format($quarter['requests'])); ?></div>
                    <div class="metric-label"><?php echo e($quarter['quarter']); ?></div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                
                <div class="alert alert-info mt-3">
                    <h6><i class="fas fa-lightbulb me-2"></i>Insights</h6>
                    <p class="mb-1"><strong>Peak Day:</strong> <?php echo e($trendAnalytics['peak_day']['day'] ?? 'N/A'); ?> (<?php echo e(number_format($trendAnalytics['peak_day']['requests'] ?? 0)); ?> requests)</p>
                    <p class="mb-0"><strong>Slowest Day:</strong> <?php echo e($trendAnalytics['slowest_day']['day'] ?? 'N/A'); ?> (<?php echo e(number_format($trendAnalytics['slowest_day']['requests'] ?? 0)); ?> requests)</p>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- 🆕 Performance Metrics Section -->
    <?php if(isset($performanceMetrics)): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="report-section">
                <h4 class="chart-title">
                    <i class="fas fa-tachometer-alt me-2"></i>System Performance Metrics & KPIs
                </h4>
                
                <div class="row">
                    <div class="col-md-2">
                        <div class="metric-card">
                            <div class="metric-value text-success"><?php echo e($performanceMetrics['completion_rate']); ?>%</div>
                            <div class="metric-label">Completion Rate</div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="metric-card">
                            <div class="metric-value text-primary"><?php echo e($performanceMetrics['sla_compliance_rate']); ?>%</div>
                            <div class="metric-label">SLA Compliance</div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="metric-card">
                            <div class="metric-value text-info"><?php echo e($performanceMetrics['resource_utilization']); ?>%</div>
                            <div class="metric-label">Resource Utilization</div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="metric-card">
                            <div class="metric-value text-warning"><?php echo e($performanceMetrics['error_rate']); ?>%</div>
                            <div class="metric-label">Error Rate</div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="metric-card">
                            <div class="metric-value text-success"><?php echo e($performanceMetrics['customer_satisfaction']); ?>%</div>
                            <div class="metric-label">Satisfaction</div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="metric-card">
                            <div class="metric-value"><?php echo e($performanceMetrics['avg_daily_requests']); ?></div>
                            <div class="metric-label">Avg Daily Requests</div>
                        </div>
                    </div>
                </div>

                <?php if(isset($performanceMetrics['peak_load'])): ?>
                <div class="alert alert-warning mt-3">
                    <h6><i class="fas fa-exclamation-triangle me-2"></i>Peak Load Information</h6>
                    <p class="mb-0"><strong>Highest Load Day:</strong> <?php echo e($performanceMetrics['peak_load']['date']); ?> with <?php echo e(number_format($performanceMetrics['peak_load']['requests'])); ?> requests</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Queue Performance Metrics -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="report-section">
                <h4 class="chart-title">
                    <i class="fas fa-chart-line me-2"></i>Queue Performance Metrics
                </h4>
                <?php if(isset($queueStats)): ?>
                <div class="row">
                    <div class="col-6">
                        <div class="metric-card">
                            <div class="metric-value"><?php echo e(number_format($queueStats['daily_processed'])); ?></div>
                            <div class="metric-label">Processed Today</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="metric-card">
                            <div class="metric-value"><?php echo e(number_format($queueStats['weekly_processed'])); ?></div>
                            <div class="metric-label">Processed This Week</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="metric-card">
                            <div class="metric-value"><?php echo e(number_format($queueStats['monthly_processed'])); ?></div>
                            <div class="metric-label">Processed This Month</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="metric-card">
                            <div class="metric-value text-warning"><?php echo e(number_format($queueStats['current_queue_size'])); ?></div>
                            <div class="metric-label">Current Queue Size</div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="report-section">
                <h4 class="chart-title">
                    <i class="fas fa-money-bill-wave me-2"></i>Revenue Analytics
                </h4>
                <?php if(isset($revenueStats)): ?>
                <div class="metric-card">
                    <div class="metric-value text-success">₱<?php echo e(number_format($revenueStats['total'], 2)); ?></div>
                    <div class="metric-label">Total Revenue</div>
                </div>
                <div class="metric-card">
                    <div class="metric-value text-info">₱<?php echo e(number_format($revenueStats['monthly'], 2)); ?></div>
                    <div class="metric-label">This Month's Revenue</div>
                </div>
                <h6 class="mt-3 mb-2">Revenue by Document Type</h6>
                <?php $__currentLoopData = $revenueStats['by_document']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $revenue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span><?php echo e($revenue->type_document); ?></span>
                    <strong>₱<?php echo e(number_format($revenue->revenue, 2)); ?></strong>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Registrar Performance -->
    <?php if(isset($registrarPerformance) && $registrarPerformance->count() > 0): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="report-section">
                <h4 class="chart-title">
                    <i class="fas fa-users-cog me-2"></i>Registrar Performance Analysis
                </h4>
                <div class="table-responsive">
                    <table class="table table-stats">
                        <thead>
                            <tr>
                                <th>Registrar Name</th>
                                <th>Total Processed</th>
                                <th>Average Processing Time</th>
                                <th>Performance Rating</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $registrarPerformance; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $performance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><strong><?php echo e($performance->first_name); ?> <?php echo e($performance->last_name); ?></strong></td>
                                <td><?php echo e(number_format($performance->total_processed)); ?></td>
                                <td><?php echo e(number_format($performance->avg_processing_minutes, 1)); ?> minutes</td>
                                <td>
                                    <?php
                                        $rating = $performance->avg_processing_minutes < 60 ? 'excellent' : 
                                                 ($performance->avg_processing_minutes < 240 ? 'good' : 'needs-improvement');
                                        $badgeClass = $rating === 'excellent' ? 'success' : ($rating === 'good' ? 'warning' : 'danger');
                                    ?>
                                    <span class="badge bg-<?php echo e($badgeClass); ?>"><?php echo e(ucfirst(str_replace('-', ' ', $rating))); ?></span>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Monthly Trends -->
    <?php if(isset($monthlyTrends) && $monthlyTrends->count() > 0): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="report-section">
                <h4 class="chart-title">
                    <i class="fas fa-chart-area me-2"></i>Monthly Request Trends (Last 12 Months)
                </h4>
                <div class="table-responsive">
                    <table class="table table-stats">
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th>Total Requests</th>
                                <th>Trend</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $monthlyTrends; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $trend): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><strong><?php echo e($trend['month']); ?></strong></td>
                                <td><?php echo e(number_format($trend['requests'])); ?></td>
                                <td>
                                    <?php if($index > 0): ?>
                                        <?php
                                            $prevRequests = $monthlyTrends[$index - 1]['requests'];
                                            $change = $prevRequests > 0 ? (($trend['requests'] - $prevRequests) / $prevRequests) * 100 : 0;
                                        ?>
                                        <?php if($change > 0): ?>
                                            <span class="text-success"><i class="fas fa-arrow-up"></i> +<?php echo e(number_format($change, 1)); ?>%</span>
                                        <?php elseif($change < 0): ?>
                                            <span class="text-danger"><i class="fas fa-arrow-down"></i> <?php echo e(number_format($change, 1)); ?>%</span>
                                        <?php else: ?>
                                            <span class="text-muted"><i class="fas fa-minus"></i> No change</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Peak Hours Analysis -->
    <?php if(isset($peakHoursData) && $peakHoursData->count() > 0): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="report-section">
                <h4 class="chart-title">
                    <i class="fas fa-clock me-2"></i>Peak Hours Analysis
                </h4>
                <p class="text-muted mb-3">Request volume distribution throughout business hours</p>
                <div class="table-responsive">
                    <table class="table table-stats">
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>Total Requests</th>
                                <th>Distribution</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $maxRequests = $peakHoursData->max('requests'); ?>
                            <?php $__currentLoopData = $peakHoursData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hourData): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><strong><?php echo e($hourData['hour']); ?></strong></td>
                                <td><?php echo e(number_format($hourData['requests'])); ?></td>
                                <td>
                                    <?php
                                        $percentage = $maxRequests > 0 ? ($hourData['requests'] / $maxRequests) * 100 : 0;
                                    ?>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar progress-bar-custom" style="width: <?php echo e($percentage); ?>%"></div>
                                    </div>
                                    <small class="text-muted"><?php echo e(number_format($percentage, 1)); ?>% of peak</small>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Report Generation Info -->
    <div class="row">
        <div class="col-12">
            <div class="alert alert-info">
                <h6><i class="fas fa-info-circle me-2"></i>Report Information</h6>
                <p class="mb-1"><strong>Generated on:</strong> <?php echo e(now()->format('F j, Y \a\t g:i A')); ?></p>
                <p class="mb-1"><strong>Data Period:</strong> All time data up to current date</p>
                <p class="mb-1"><strong>🆕 New Features:</strong> Student vs Onsite request analytics with interactive charts</p>
                <p class="mb-0"><strong>Stakeholder Coordination:</strong> Reports can be customized based on PIA requirements and other stakeholder needs. Contact the system administrator for specific report requests.</p>
            </div>
        </div>
    </div>

    <!-- Chart Usage Guide -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-light border">
                <h6><i class="fas fa-chart-pie me-2"></i>Chart Analytics Guide</h6>
                <div class="row">
                    <div class="col-md-6">
                        <h6>📊 Student Request Analytics</h6>
                        <ul class="mb-0 small">
                            <li><strong>Status Distribution:</strong> Pie chart showing student request phases</li>
                            <li><strong>Document Types:</strong> Popular online document requests</li>
                            <li><strong>Trends:</strong> Monthly and daily request patterns</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>🏢 Onsite Request Analytics</h6>
                        <ul class="mb-0 small">
                            <li><strong>Status Distribution:</strong> Doughnut chart with walk-in service status</li>
                            <li><strong>Document Types:</strong> In-person document request patterns</li>
                            <li><strong>Comparison:</strong> Online vs walk-in service analysis</li>
                        </ul>
                    </div>
                </div>
                <div class="mt-3">
                    <p class="mb-0"><strong>💡 Usage Tips:</strong> Hover over charts for detailed percentages, click legend items to toggle sections, and use data for resource planning and trend analysis.</p>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- FontAwesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>

<script>
    // Auto-refresh data every 5 minutes
    setTimeout(function() {
        location.reload();
    }, 300000);

    // 🆕 Chart Initialization
    document.addEventListener('DOMContentLoaded', function() {
        // Document Types Pie Chart
        <?php if(isset($chartData['documentTypes']) && $chartData['documentTypes']->count() > 0): ?>
        const docTypeCtx = document.getElementById('documentTypesChart').getContext('2d');
        new Chart(docTypeCtx, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode($chartData['documentTypes']->pluck('label')); ?>,
                datasets: [{
                    data: <?php echo json_encode($chartData['documentTypes']->pluck('value')); ?>,
                    backgroundColor: [
                        '#2c3192', '#28a745', '#ffc107', '#dc3545', '#17a2b8',
                        '#6f42c1', '#fd7e14', '#20c997', '#e83e8c', '#6c757d'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            font: { size: 12 }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.parsed / total) * 100).toFixed(1);
                                return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
        <?php endif; ?>

        // Status Distribution Pie Chart
        <?php if(isset($chartData['statusDistribution']) && $chartData['statusDistribution']->count() > 0): ?>
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode($chartData['statusDistribution']->pluck('label')); ?>,
                datasets: [{
                    data: <?php echo json_encode($chartData['statusDistribution']->pluck('value')); ?>,
                    backgroundColor: ['#ffc107', '#17a2b8', '#28a745', '#2c3192'],
                    borderWidth: 3,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                cutout: '60%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            font: { size: 12 }
                        }
                    }
                }
            }
        });
        <?php endif; ?>

        // Monthly Trends Line Chart
        <?php if(isset($chartData['monthlyTrends']) && $chartData['monthlyTrends']->count() > 0): ?>
        const monthlyCtx = document.getElementById('monthlyTrendsChart').getContext('2d');
        new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($chartData['monthlyTrends']->pluck('label')); ?>,
                datasets: [{
                    label: 'Requests',
                    data: <?php echo json_encode($chartData['monthlyTrends']->pluck('value')); ?>,
                    borderColor: '#2c3192',
                    backgroundColor: 'rgba(44, 49, 146, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#2c3192',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 6
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.1)'
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(0,0,0,0.1)'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
        <?php endif; ?>

        // Daily Requests Bar Chart
        <?php if(isset($chartData['dailyRequests']) && $chartData['dailyRequests']->count() > 0): ?>
        const dailyCtx = document.getElementById('dailyRequestsChart').getContext('2d');
        new Chart(dailyCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($chartData['dailyRequests']->pluck('label')); ?>,
                datasets: [{
                    label: 'Daily Requests',
                    data: <?php echo json_encode($chartData['dailyRequests']->pluck('value')); ?>,
                    backgroundColor: 'rgba(44, 49, 146, 0.8)',
                    borderColor: '#2c3192',
                    borderWidth: 1,
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
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
        <?php endif; ?>
        
        // 🆕 Student Request Status Distribution Chart
        <?php if(isset($chartData['studentRequestDistribution']) && $chartData['studentRequestDistribution']->count() > 0): ?>
        const studentRequestCtx = document.getElementById('studentRequestChart').getContext('2d');
        new Chart(studentRequestCtx, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode($chartData['studentRequestDistribution']->pluck('label')); ?>,
                datasets: [{
                    data: <?php echo json_encode($chartData['studentRequestDistribution']->pluck('value')); ?>,
                    backgroundColor: [
                        '#ffc107', // Pending - Warning Yellow
                        '#17a2b8', // Processing - Info Cyan  
                        '#28a745', // Ready - Success Green
                        '#2c3192', // Completed - Primary Blue
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
        <?php endif; ?>

        // 🆕 Onsite Request Status Distribution Chart
        <?php if(isset($chartData['onsiteRequestDistribution']) && $chartData['onsiteRequestDistribution']->count() > 0): ?>
        const onsiteRequestCtx = document.getElementById('onsiteRequestChart').getContext('2d');
        new Chart(onsiteRequestCtx, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode($chartData['onsiteRequestDistribution']->pluck('label')); ?>,
                datasets: [{
                    data: <?php echo json_encode($chartData['onsiteRequestDistribution']->pluck('value')); ?>,
                    backgroundColor: [
                        '#ffc107', // Pending - Warning Yellow
                        '#17a2b8', // Processing - Info Cyan
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
        <?php endif; ?>

        // 🆕 Student Document Type Distribution Chart
        <?php if(isset($chartData['studentDocumentTypes']) && $chartData['studentDocumentTypes']->count() > 0): ?>
        const studentDocCtx = document.getElementById('studentDocumentChart').getContext('2d');
        new Chart(studentDocCtx, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode($chartData['studentDocumentTypes']->pluck('label')); ?>,
                datasets: [{
                    data: <?php echo json_encode($chartData['studentDocumentTypes']->pluck('value')); ?>,
                    backgroundColor: [
                        '#2c3192', '#28a745', '#ffc107', '#dc3545', '#17a2b8',
                        '#6f42c1', '#fd7e14', '#20c997', '#e83e8c', '#6c757d',
                        '#343a40', '#f8f9fa', '#495057', '#adb5bd', '#ced4da'
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
        <?php endif; ?>

        // 🆕 Onsite Document Type Distribution Chart
        <?php if(isset($chartData['onsiteDocumentTypes']) && $chartData['onsiteDocumentTypes']->count() > 0): ?>
        const onsiteDocCtx = document.getElementById('onsiteDocumentChart').getContext('2d');
        new Chart(onsiteDocCtx, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode($chartData['onsiteDocumentTypes']->pluck('label')); ?>,
                datasets: [{
                    data: <?php echo json_encode($chartData['onsiteDocumentTypes']->pluck('value')); ?>,
                    backgroundColor: [
                        '#17a2b8', '#28a745', '#ffc107', '#dc3545', '#6f42c1',
                        '#fd7e14', '#20c997', '#e83e8c', '#6c757d', '#2c3192',
                        '#343a40', '#f8f9fa', '#495057', '#adb5bd', '#ced4da'
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
        <?php endif; ?>
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Nu-Regisv2\resources\views/admin/reports.blade.php ENDPATH**/ ?>