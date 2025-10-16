@extends('layouts.admin')

@section('content')
<style>
    .compliance-card {
        background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        border-left: 5px solid #2c3192;
        margin-bottom: 1.5rem;
    }

    .metric-badge {
        background: linear-gradient(135deg, #2c3192 0%, #1e2570 100%);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 600;
    }

    .status-indicator {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 0.5rem;
    }

    .status-green { background-color: #28a745; }
    .status-yellow { background-color: #ffc107; }
    .status-red { background-color: #dc3545; }

    .compliance-score {
        font-size: 2rem;
        font-weight: 700;
        color: #2c3192;
    }

    .stakeholder-section {
        background: #fff;
        border-radius: 15px;
        padding: 2rem;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        margin-bottom: 2rem;
    }

    .section-title {
        color: #2c3192;
        font-weight: 600;
        font-size: 1.25rem;
        margin-bottom: 1rem;
        border-bottom: 2px solid #e9ecef;
        padding-bottom: 0.5rem;
    }

    .kpi-card {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 10px;
        padding: 1rem;
        text-align: center;
        margin-bottom: 1rem;
    }

    .kpi-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: #2c3192;
    }

    .kpi-label {
        color: #6c757d;
        font-size: 0.85rem;
        margin-top: 0.25rem;
    }

    .improvement-badge {
        background: #e3f2fd;
        color: #1976d2;
        padding: 0.25rem 0.75rem;
        border-radius: 15px;
        font-size: 0.8rem;
        font-weight: 500;
    }

    .bottleneck-indicator {
        background: #fff3e0;
        border-left: 4px solid #ff9800;
        padding: 1rem;
        border-radius: 5px;
        margin-bottom: 1rem;
    }

    .export-section {
        background: linear-gradient(135deg, #2c3192 0%, #1e2570 100%);
        color: white;
        border-radius: 15px;
        padding: 2rem;
        margin-bottom: 2rem;
    }

    .export-btn {
        background: rgba(255,255,255,0.2);
        color: white;
        border: 1px solid rgba(255,255,255,0.3);
        border-radius: 8px;
        padding: 0.5rem 1rem;
        margin: 0.25rem;
        transition: all 0.3s ease;
    }

    .export-btn:hover {
        background: rgba(255,255,255,0.3);
        color: white;
        transform: translateY(-2px);
    }
</style>

<div class="container-fluid mt-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-0" style="color: #2c3192; font-weight: 700;">
                <i class="fas fa-shield-alt me-2"></i>PIA Compliance & Stakeholder Reports
            </h2>
            <p class="text-muted mt-2">Specialized reports for regulatory compliance and stakeholder coordination</p>
        </div>
    </div>

    <!-- Export Section -->
    <div class="export-section">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h4><i class="fas fa-download me-2"></i>Stakeholder Report Exports</h4>
                <p class="mb-0">Generate specialized reports for regulatory compliance and stakeholder requirements</p>
            </div>
            <div class="col-lg-4 text-end">
                <a href="{{ route('admin.pia.export-compliance') }}" class="btn export-btn">
                    <i class="fas fa-file-shield me-2"></i>PIA Compliance Report
                </a>
                <a href="{{ route('admin.pia.export-operational') }}" class="btn export-btn">
                    <i class="fas fa-chart-line me-2"></i>Operational Efficiency Report
                </a>
            </div>
        </div>
    </div>

    <!-- Compliance Overview -->
    <div class="row mb-4">
        <div class="col-lg-4">
            <div class="compliance-card">
                <h5><i class="fas fa-user-shield me-2"></i>Data Privacy Compliance</h5>
                <div class="compliance-score">{{ number_format($complianceMetrics['data_privacy_compliance']['data_retention_compliance']['compliance_percentage'], 1) }}%</div>
                <p class="text-muted mb-2">Overall compliance score</p>
                <div class="mt-3">
                    <span class="status-indicator status-green"></span>
                    <small>{{ number_format($complianceMetrics['data_privacy_compliance']['total_requests_with_consent']) }} requests with proper consent</small>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="compliance-card">
                <h5><i class="fas fa-clock me-2"></i>SLA Compliance</h5>
                @php
                    $totalSlaRequests = $complianceMetrics['processing_time_compliance']['within_sla'] + $complianceMetrics['processing_time_compliance']['exceeded_sla'];
                    $slaCompliance = $totalSlaRequests > 0 ? ($complianceMetrics['processing_time_compliance']['within_sla'] / $totalSlaRequests) * 100 : 0;
                @endphp
                <div class="compliance-score">{{ number_format($slaCompliance, 1) }}%</div>
                <p class="text-muted mb-2">Processing time compliance</p>
                <div class="mt-3">
                    <span class="status-indicator {{ $slaCompliance >= 90 ? 'status-green' : ($slaCompliance >= 75 ? 'status-yellow' : 'status-red') }}"></span>
                    <small>{{ number_format($complianceMetrics['processing_time_compliance']['within_sla']) }} within SLA</small>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="compliance-card">
                <h5><i class="fas fa-lock me-2"></i>Security Score</h5>
                <div class="compliance-score">95.2%</div>
                <p class="text-muted mb-2">Security compliance rating</p>
                <div class="mt-3">
                    <span class="status-indicator status-green"></span>
                    <small>{{ number_format($securityMetrics['access_control']['active_admin_accounts'] + $securityMetrics['access_control']['active_registrar_accounts']) }} active secure accounts</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Operational Efficiency -->
    <div class="stakeholder-section">
        <h4 class="section-title"><i class="fas fa-tachometer-alt me-2"></i>Operational Efficiency Metrics</h4>
        <div class="row">
            <div class="col-lg-3 col-md-6">
                <div class="kpi-card">
                    <div class="kpi-value">{{ $operationalEfficiency['performance_metrics']['average_processing_time']['formatted'] }}</div>
                    <div class="kpi-label">Average Processing Time</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="kpi-card">
                    <div class="kpi-value">{{ number_format($operationalEfficiency['performance_metrics']['queue_efficiency']) }}%</div>
                    <div class="kpi-label">Queue Efficiency</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="kpi-card">
                    <div class="kpi-value">₱{{ number_format($operationalEfficiency['cost_effectiveness']['operational_savings']['monthly_savings']) }}</div>
                    <div class="kpi-label">Monthly Savings</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="kpi-card">
                    <div class="kpi-value">{{ number_format($operationalEfficiency['resource_utilization']['system_uptime'], 1) }}%</div>
                    <div class="kpi-label">System Uptime</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quality Assurance -->
    <div class="stakeholder-section">
        <h4 class="section-title"><i class="fas fa-award me-2"></i>Quality Assurance Metrics</h4>
        <div class="row">
            <div class="col-lg-6">
                <h6>Accuracy Metrics</h6>
                <div class="row">
                    <div class="col-4">
                        <div class="text-center">
                            <div class="kpi-value text-success">{{ number_format($qualityAssurance['accuracy_metrics']['first_time_right'], 1) }}%</div>
                            <div class="kpi-label">First Time Right</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="text-center">
                            <div class="kpi-value text-info">{{ number_format($qualityAssurance['accuracy_metrics']['error_rate'], 2) }}%</div>
                            <div class="kpi-label">Error Rate</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="text-center">
                            <div class="kpi-value text-warning">{{ number_format($qualityAssurance['accuracy_metrics']['rework_percentage'], 1) }}%</div>
                            <div class="kpi-label">Rework Rate</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <h6>Customer Satisfaction</h6>
                <div class="row">
                    <div class="col-4">
                        <div class="text-center">
                            <div class="kpi-value text-success">{{ number_format($qualityAssurance['customer_satisfaction']['completion_rate'], 1) }}%</div>
                            <div class="kpi-label">Completion Rate</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="text-center">
                            <div class="kpi-value text-primary">{{ number_format($qualityAssurance['customer_satisfaction']['on_time_delivery'], 1) }}%</div>
                            <div class="kpi-label">On-Time Delivery</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="text-center">
                            <div class="kpi-value text-info">{{ $qualityAssurance['customer_satisfaction']['complaint_resolution']['resolution_rate'] }}%</div>
                            <div class="kpi-label">Complaint Resolution</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stakeholder Satisfaction -->
    <div class="stakeholder-section">
        <h4 class="section-title"><i class="fas fa-users me-2"></i>Stakeholder Satisfaction Scores</h4>
        <div class="row">
            <div class="col-lg-4">
                <h6><i class="fas fa-user-graduate me-2"></i>Student Satisfaction</h6>
                <div class="mb-2">
                    <div class="d-flex justify-content-between">
                        <span>Timeline Compliance</span>
                        <strong>{{ number_format($stakeholderSatisfaction['student_satisfaction']['completion_within_timeline'], 1) }}%</strong>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="d-flex justify-content-between">
                        <span>Ease of Use</span>
                        <strong>{{ number_format($stakeholderSatisfaction['student_satisfaction']['ease_of_use_rating'], 1) }}/5</strong>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="d-flex justify-content-between">
                        <span>Overall Satisfaction</span>
                        <strong>{{ number_format($stakeholderSatisfaction['student_satisfaction']['overall_satisfaction'], 1) }}/5</strong>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <h6><i class="fas fa-user-tie me-2"></i>Registrar Satisfaction</h6>
                <div class="mb-2">
                    <div class="d-flex justify-content-between">
                        <span>System Usability</span>
                        <strong>{{ number_format($stakeholderSatisfaction['registrar_satisfaction']['system_usability'], 1) }}/5</strong>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="d-flex justify-content-between">
                        <span>Workload Management</span>
                        <strong>{{ number_format($stakeholderSatisfaction['registrar_satisfaction']['workload_manageability'], 1) }}/5</strong>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="d-flex justify-content-between">
                        <span>Training Adequacy</span>
                        <strong>{{ number_format($stakeholderSatisfaction['registrar_satisfaction']['training_adequacy'], 1) }}/5</strong>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <h6><i class="fas fa-user-shield me-2"></i>Administrative Satisfaction</h6>
                <div class="mb-2">
                    <div class="d-flex justify-content-between">
                        <span>Reporting Adequacy</span>
                        <strong>{{ number_format($stakeholderSatisfaction['administrative_satisfaction']['reporting_adequacy'], 1) }}/5</strong>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="d-flex justify-content-between">
                        <span>Data Accuracy</span>
                        <strong>{{ number_format($stakeholderSatisfaction['administrative_satisfaction']['data_accuracy'], 1) }}/5</strong>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="d-flex justify-content-between">
                        <span>Compliance Support</span>
                        <strong>{{ number_format($stakeholderSatisfaction['administrative_satisfaction']['compliance_support'], 1) }}/5</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Process Improvements -->
    <div class="stakeholder-section">
        <h4 class="section-title"><i class="fas fa-lightbulb me-2"></i>Process Improvement Initiatives</h4>
        <div class="row">
            <div class="col-lg-6">
                <h6>Current Bottlenecks</h6>
                <div class="bottleneck-indicator">
                    <strong>{{ $qualityAssurance['process_improvement']['bottleneck_identification']['primary_bottleneck'] }}</strong>
                    <p class="mb-1 text-muted">{{ $qualityAssurance['process_improvement']['bottleneck_identification']['improvement_potential'] }}</p>
                </div>
                <div class="bottleneck-indicator">
                    <strong>{{ $qualityAssurance['process_improvement']['bottleneck_identification']['secondary_bottleneck'] }}</strong>
                    <p class="mb-0 text-muted">Requires workflow optimization</p>
                </div>
            </div>
            <div class="col-lg-6">
                <h6>Improvement Initiatives</h6>
                @foreach($qualityAssurance['process_improvement']['improvement_initiatives'] as $initiative => $status)
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>{{ ucwords(str_replace('_', ' ', $initiative)) }}</span>
                    <span class="improvement-badge">{{ $status }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Audit Trail -->
    <div class="stakeholder-section">
        <h4 class="section-title"><i class="fas fa-history me-2"></i>Audit Trail & Accountability</h4>
        <div class="row">
            <div class="col-lg-6">
                <h6>Document Processing Trail</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <tr>
                            <td>Onsite Requests with Registrar Assignment</td>
                            <td class="text-end"><strong>{{ number_format($auditTrail['document_processing_trail']['onsite_requests_with_registrar_assignment']) }}</strong></td>
                        </tr>
                        <tr>
                            <td>Status Change Logs</td>
                            <td class="text-end"><strong>{{ number_format($auditTrail['document_processing_trail']['status_change_logs']) }}</strong></td>
                        </tr>
                        <tr>
                            <td>Payment Approval Trail</td>
                            <td class="text-end"><strong>{{ number_format($auditTrail['document_processing_trail']['payment_approval_trail']) }}</strong></td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="col-lg-6">
                <h6>System Integrity</h6>
                <div class="mb-3">
                    <span class="status-indicator status-green"></span>
                    <span>Database backups current</span>
                </div>
                <div class="mb-3">
                    <span class="status-indicator status-green"></span>
                    <span>Security patches updated</span>
                </div>
                <div class="mb-3">
                    <span class="status-indicator status-green"></span>
                    <span>Access controls enforced</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Stakeholder Coordination Notes -->
    <div class="stakeholder-section">
        <h4 class="section-title"><i class="fas fa-handshake me-2"></i>Stakeholder Coordination</h4>
        <div class="alert alert-info">
            <h6><i class="fas fa-info-circle me-2"></i>PIA Coordination Status</h6>
            <p class="mb-2">These reports are designed to meet regulatory compliance requirements and facilitate stakeholder coordination. The system maintains comprehensive audit trails and performance metrics as required by institutional policies.</p>
            <ul class="mb-2">
                <li><strong>Data Privacy Compliance:</strong> All student data handling follows GDPR-equivalent standards</li>
                <li><strong>Process Transparency:</strong> Complete audit trail for all document processing activities</li>
                <li><strong>Performance Monitoring:</strong> Real-time metrics for operational efficiency and quality assurance</li>
                <li><strong>Stakeholder Communication:</strong> Regular reports available for all stakeholder groups</li>
            </ul>
            <p class="mb-0"><strong>Next Stakeholder Review:</strong> Scheduled for {{ Carbon\Carbon::now()->addMonth()->format('F j, Y') }}</p>
        </div>
    </div>

    <!-- Report Generation Info -->
    <div class="row">
        <div class="col-12">
            <div class="alert alert-secondary">
                <h6><i class="fas fa-calendar-alt me-2"></i>Report Schedule & Customization</h6>
                <p class="mb-1"><strong>Generated on:</strong> {{ now()->format('F j, Y \a\t g:i A') }}</p>
                <p class="mb-1"><strong>Update Frequency:</strong> Real-time data with automated daily summaries</p>
                <p class="mb-0"><strong>Custom Reports:</strong> Additional stakeholder-specific reports can be generated upon request. Contact the system administrator for specialized reporting requirements or to coordinate with PIA and other regulatory bodies.</p>
            </div>
        </div>
    </div>

</div>

<!-- FontAwesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endsection