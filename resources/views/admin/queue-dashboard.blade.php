@extends('layouts.app')

@section('title', 'Queue Management Dashboard')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-users me-2"></i>
                        Smart Queue Management Dashboard
                    </h4>
                    <div>
                        <button class="btn btn-light btn-sm me-2" onclick="refreshDashboard()">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                        <span class="badge bg-success" id="api-status">
                            <i class="fas fa-circle"></i> API Connected
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Analytics Summary -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card border-primary">
                                <div class="card-body text-center">
                                    <i class="fas fa-clock fa-2x text-primary mb-2"></i>
                                    <h5 class="card-title">Currently Waiting</h5>
                                    <h3 class="text-primary mb-0" id="current-waiting">-</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-warning">
                                <div class="card-body text-center">
                                    <i class="fas fa-user-clock fa-2x text-warning mb-2"></i>
                                    <h5 class="card-title">Being Served</h5>
                                    <h3 class="text-warning mb-0" id="currently-serving">-</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-success">
                                <div class="card-body text-center">
                                    <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                                    <h5 class="card-title">Completed Today</h5>
                                    <h3 class="text-success mb-0" id="completed-today">-</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-info">
                                <div class="card-body text-center">
                                    <i class="fas fa-chart-line fa-2x text-info mb-2"></i>
                                    <h5 class="card-title">Avg Wait Time</h5>
                                    <h3 class="text-info mb-0" id="avg-wait-time">-</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Queue Controls -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Queue Controls</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="service-counters" class="form-label">Service Counters</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" id="service-counters" value="3" min="1" max="10">
                                                <button class="btn btn-primary" onclick="updateServiceCounters()">Update</button>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Quick Actions</label>
                                            <div class="d-grid gap-2">
                                                <button class="btn btn-success btn-sm" onclick="callNextCustomer()">
                                                    <i class="fas fa-arrow-right"></i> Next Customer
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">System Status</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <small class="text-muted">Service Utilization</small>
                                            <div class="progress mb-2">
                                                <div class="progress-bar" id="utilization-bar" style="width: 0%"></div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">No Show Rate</small>
                                            <div class="progress mb-2">
                                                <div class="progress-bar bg-danger" id="no-show-bar" style="width: 0%"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <small class="text-muted">Last updated: <span id="last-update">-</span></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Current Queue -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Current Queue</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Position</th>
                                            <th>Customer</th>
                                            <th>Service Type</th>
                                            <th>Wait Time</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="queue-table-body">
                                        <tr>
                                            <td colspan="6" class="text-center">
                                                <div class="spinner-border spinner-border-sm me-2"></div>
                                                Loading queue data...
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Customer Details Modal -->
<div class="modal fade" id="customerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Customer Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="customer-details">
                <!-- Customer details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="startService()">Start Service</button>
                <button type="button" class="btn btn-success" onclick="completeService()">Complete Service</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let currentCustomerId = null;
let refreshInterval = null;

document.addEventListener('DOMContentLoaded', function() {
    loadDashboardData();
    startAutoRefresh();
});

function startAutoRefresh() {
    refreshInterval = setInterval(loadDashboardData, 30000); // Refresh every 30 seconds
}

function stopAutoRefresh() {
    if (refreshInterval) {
        clearInterval(refreshInterval);
    }
}

function refreshDashboard() {
    loadDashboardData();
}

async function loadDashboardData() {
    try {
        // Check API health
        const healthResponse = await fetch('/api/queue/health');
        const healthData = await healthResponse.json();
        
        const statusElement = document.getElementById('api-status');
        if (healthData.success && healthData.api_status === 'healthy') {
            statusElement.innerHTML = '<i class="fas fa-circle"></i> API Connected';
            statusElement.className = 'badge bg-success';
        } else {
            statusElement.innerHTML = '<i class="fas fa-circle"></i> API Disconnected';
            statusElement.className = 'badge bg-danger';
        }

        // Load analytics
        const analyticsResponse = await fetch('/api/queue/analytics');
        const analyticsData = await analyticsResponse.json();
        
        if (analyticsData.success && analyticsData.data) {
            updateAnalytics(analyticsData.data);
        }

        // Load queue status
        const queueResponse = await fetch('/api/queue/status');
        const queueData = await queueResponse.json();
        
        if (queueData.success) {
            updateQueueTable(queueData.data);
        }

        // Update last updated time
        document.getElementById('last-update').textContent = new Date().toLocaleTimeString();

    } catch (error) {
        console.error('Error loading dashboard data:', error);
        document.getElementById('api-status').innerHTML = '<i class="fas fa-circle"></i> Error';
        document.getElementById('api-status').className = 'badge bg-danger';
    }
}

function updateAnalytics(data) {
    document.getElementById('current-waiting').textContent = data.current_waiting || 0;
    document.getElementById('currently-serving').textContent = data.currently_serving || 0;
    document.getElementById('completed-today').textContent = data.completed_today || 0;
    document.getElementById('avg-wait-time').textContent = (data.average_wait_time_minutes || 0) + 'm';
    
    // Update progress bars
    const utilizationBar = document.getElementById('utilization-bar');
    const utilization = data.service_counter_utilization || 0;
    utilizationBar.style.width = utilization + '%';
    utilizationBar.textContent = utilization.toFixed(1) + '%';
    
    const noShowBar = document.getElementById('no-show-bar');
    const noShowRate = data.no_show_rate || 0;
    noShowBar.style.width = noShowRate + '%';
    noShowBar.textContent = noShowRate.toFixed(1) + '%';
}

function updateQueueTable(queueData) {
    const tbody = document.getElementById('queue-table-body');
    
    if (!queueData || queueData.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No customers in queue</td></tr>';
        return;
    }

    tbody.innerHTML = queueData.map(customer => `
        <tr>
            <td>
                <span class="badge bg-primary">${customer.position || 'N/A'}</span>
            </td>
            <td>
                <div>
                    <strong>${customer.customer_name || 'Unknown'}</strong>
                    ${customer.phone ? `<br><small class="text-muted">${customer.phone}</small>` : ''}
                </div>
            </td>
            <td>
                <span class="badge bg-info">${formatServiceType(customer.service_type)}</span>
            </td>
            <td>
                <span class="text-${getWaitTimeColor(customer.estimated_wait_time)}">
                    ${customer.estimated_wait_time || 0}m
                </span>
            </td>
            <td>
                <span class="badge bg-${getStatusColor(customer.status)}">
                    ${formatStatus(customer.status)}
                </span>
            </td>
            <td>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary" onclick="viewCustomer('${customer.id}')">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-outline-success" onclick="startServiceForCustomer('${customer.id}')">
                        <i class="fas fa-play"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

async function updateServiceCounters() {
    const counters = parseInt(document.getElementById('service-counters').value);
    
    try {
        const response = await fetch('/api/queue/settings/counters', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ counters: counters })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showAlert('success', 'Service counters updated successfully');
            loadDashboardData();
        } else {
            showAlert('error', 'Failed to update service counters');
        }
    } catch (error) {
        showAlert('error', 'Error updating service counters');
    }
}

async function callNextCustomer() {
    try {
        const response = await fetch('/api/queue/next-customer');
        const data = await response.json();
        
        if (data.success && data.has_next_customer) {
            const customer = data.data;
            showAlert('info', `Next customer: ${customer.customer_name} (Position ${customer.position})`);
            
            // Optionally start service for next customer
            if (confirm('Start service for this customer?')) {
                await startServiceForCustomer(customer.id);
            }
        } else {
            showAlert('info', 'No customers waiting in queue');
        }
    } catch (error) {
        showAlert('error', 'Error getting next customer');
    }
}

async function viewCustomer(customerId) {
    currentCustomerId = customerId;
    
    try {
        const response = await fetch(`/api/queue/customer/${customerId}`);
        const data = await response.json();
        
        if (data.success) {
            const customer = data.data;
            document.getElementById('customer-details').innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <strong>Name:</strong><br>${customer.customer_name}<br><br>
                        <strong>Phone:</strong><br>${customer.phone || 'N/A'}<br><br>
                        <strong>Email:</strong><br>${customer.email || 'N/A'}
                    </div>
                    <div class="col-md-6">
                        <strong>Service Type:</strong><br>${formatServiceType(customer.service_type)}<br><br>
                        <strong>Position:</strong><br>${customer.position}<br><br>
                        <strong>Wait Time:</strong><br>${customer.estimated_wait_time}m
                    </div>
                </div>
                <div class="mt-3">
                    <strong>Check-in Time:</strong><br>${new Date(customer.check_in_time).toLocaleString()}<br><br>
                    <strong>Notes:</strong><br>${customer.notes || 'None'}
                </div>
            `;
            
            new bootstrap.Modal(document.getElementById('customerModal')).show();
        }
    } catch (error) {
        showAlert('error', 'Error loading customer details');
    }
}

async function startServiceForCustomer(customerId) {
    await updateCustomerStatus(customerId, 'in_service');
}

async function startService() {
    if (currentCustomerId) {
        await updateCustomerStatus(currentCustomerId, 'in_service');
        bootstrap.Modal.getInstance(document.getElementById('customerModal')).hide();
    }
}

async function completeService() {
    if (currentCustomerId) {
        const duration = prompt('Enter service duration in minutes:');
        if (duration && !isNaN(duration)) {
            await updateCustomerStatus(currentCustomerId, 'completed', parseInt(duration));
        } else {
            await updateCustomerStatus(currentCustomerId, 'completed');
        }
        bootstrap.Modal.getInstance(document.getElementById('customerModal')).hide();
    }
}

async function updateCustomerStatus(customerId, status, serviceDuration = null) {
    try {
        const payload = { status: status };
        if (serviceDuration) {
            payload.service_duration = serviceDuration;
        }
        
        const response = await fetch(`/api/queue/customer/${customerId}/status`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(payload)
        });
        
        const data = await response.json();
        
        if (data.success) {
            showAlert('success', `Customer status updated to ${status}`);
            loadDashboardData();
        } else {
            showAlert('error', 'Failed to update customer status');
        }
    } catch (error) {
        showAlert('error', 'Error updating customer status');
    }
}

// Helper functions
function formatServiceType(type) {
    const types = {
        'student_documents': 'Student Docs',
        'alumni_documents': 'Alumni Docs',
        'transcript': 'Transcript',
        'certification': 'Certification',
        'verification': 'Verification',
        'general': 'General'
    };
    return types[type] || type;
}

function formatStatus(status) {
    const statuses = {
        'waiting': 'Waiting',
        'in_service': 'Being Served',
        'completed': 'Completed',
        'no_show': 'No Show',
        'cancelled': 'Cancelled'
    };
    return statuses[status] || status;
}

function getStatusColor(status) {
    const colors = {
        'waiting': 'info',
        'in_service': 'warning',
        'completed': 'success',
        'no_show': 'danger',
        'cancelled': 'secondary'
    };
    return colors[status] || 'secondary';
}

function getWaitTimeColor(minutes) {
    if (minutes <= 5) return 'success';
    if (minutes <= 15) return 'warning';
    return 'danger';
}

function showAlert(type, message) {
    // Create and show a Bootstrap alert
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.querySelector('.container-fluid').insertBefore(alertDiv, document.querySelector('.row'));
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    stopAutoRefresh();
});
</script>
@endpush

@push('styles')
<style>
.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.progress {
    height: 8px;
}

.btn-group-sm > .btn {
    padding: 0.25rem 0.5rem;
}

.table td {
    vertical-align: middle;
}

.badge {
    font-size: 0.75em;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}

.spinner-border-sm {
    animation: pulse 2s infinite;
}
</style>
@endpush