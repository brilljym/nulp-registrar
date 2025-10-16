<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pusher Real-time Demo - NU Regis</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="bi bi-broadcast"></i> Real-time Pusher Demo</h4>
                        <small>Testing real-time notifications for NU Regis v2</small>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Test Broadcasting</h5>
                                <div class="mb-3">
                                    <label class="form-label">Request ID</label>
                                    <input type="text" id="requestId" class="form-control" value="DEMO123" placeholder="Enter request ID">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Test Type</label>
                                    <select id="testType" class="form-select">
                                        <option value="basic">Basic Notification</option>
                                        <option value="request">Request Status Update</option>
                                    </select>
                                </div>
                                <button onclick="sendTestNotification()" class="btn btn-success">
                                    <i class="bi bi-send"></i> Send Test Notification
                                </button>
                                <button onclick="clearMessages()" class="btn btn-secondary ms-2">
                                    <i class="bi bi-trash"></i> Clear Messages
                                </button>
                            </div>
                            
                            <div class="col-md-6">
                                <h5>Connection Status</h5>
                                <div class="alert alert-info">
                                    <div><strong>Pusher Status:</strong> <span id="pusherStatus">Connecting...</span></div>
                                    <div><strong>Channels:</strong> <span id="channelInfo">None</span></div>
                                    <div><strong>Key:</strong> <code><?php echo e(config('broadcasting.connections.pusher.key')); ?></code></div>
                                    <div><strong>Cluster:</strong> <code><?php echo e(config('broadcasting.connections.pusher.options.cluster')); ?></code></div>
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <h5>Received Messages</h5>
                        <div id="messageLog" class="border rounded p-3 bg-white" style="height: 300px; overflow-y: auto;">
                            <p class="text-muted">Waiting for messages...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    
    <script>
        let pusher, registrarChannel, requestChannel;
        
        // Initialize Pusher
        function initPusher() {
            pusher = new Pusher('<?php echo e(config('broadcasting.connections.pusher.key')); ?>', {
                cluster: '<?php echo e(config('broadcasting.connections.pusher.options.cluster')); ?>',
                encrypted: true
            });

            // Connection event handlers
            pusher.connection.bind('connected', function() {
                document.getElementById('pusherStatus').innerHTML = '<span class="text-success"><i class="bi bi-check-circle"></i> Connected</span>';
                subscribeToChannels();
            });

            pusher.connection.bind('disconnected', function() {
                document.getElementById('pusherStatus').innerHTML = '<span class="text-danger"><i class="bi bi-x-circle"></i> Disconnected</span>';
            });

            pusher.connection.bind('error', function(error) {
                document.getElementById('pusherStatus').innerHTML = '<span class="text-danger"><i class="bi bi-exclamation-triangle"></i> Error</span>';
                console.error('Pusher connection error:', error);
            });
        }

        // Subscribe to channels
        function subscribeToChannels() {
            // Subscribe to registrar notifications channel
            registrarChannel = pusher.subscribe('registrar-notifications');
            
            // Subscribe to request-specific channel
            const requestId = document.getElementById('requestId').value;
            requestChannel = pusher.subscribe(`request-${requestId}`);
            
            // Update channel info
            document.getElementById('channelInfo').innerHTML = `
                registrar-notifications, request-${requestId}
            `;

            // Bind event listeners
            registrarChannel.bind('realtime.notification', function(data) {
                logMessage('registrar-notifications', data);
            });

            requestChannel.bind('realtime.notification', function(data) {
                logMessage(`request-${requestId}`, data);
            });
        }

        // Log received messages
        function logMessage(channel, data) {
            const messageLog = document.getElementById('messageLog');
            const timestamp = new Date().toLocaleTimeString();
            
            const messageDiv = document.createElement('div');
            messageDiv.className = `border-bottom pb-2 mb-2`;
            messageDiv.innerHTML = `
                <div class="d-flex justify-content-between">
                    <strong class="text-primary">${channel}</strong>
                    <small class="text-muted">${timestamp}</small>
                </div>
                <div><strong>Type:</strong> ${data.type}</div>
                <div><strong>Message:</strong> ${data.message}</div>
                <details>
                    <summary class="text-muted" style="cursor: pointer;">Show Data</summary>
                    <pre class="mt-2 bg-light p-2 rounded"><code>${JSON.stringify(data, null, 2)}</code></pre>
                </details>
            `;
            
            messageLog.appendChild(messageDiv);
            messageLog.scrollTop = messageLog.scrollHeight;
        }

        // Send test notification
        async function sendTestNotification() {
            const requestId = document.getElementById('requestId').value;
            const testType = document.getElementById('testType').value;
            
            let url;
            if (testType === 'basic') {
                url = '/test-pusher';
            } else {
                url = `/test-request-update/${requestId}`;
            }
            
            try {
                const response = await fetch(url);
                const result = await response.json();
                
                if (result.status === 'success') {
                    alert('Test notification sent successfully!');
                } else {
                    alert('Failed to send notification: ' + result.message);
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        }

        // Clear messages
        function clearMessages() {
            document.getElementById('messageLog').innerHTML = '<p class="text-muted">Waiting for messages...</p>';
        }

        // Update request channel when request ID changes
        document.getElementById('requestId').addEventListener('change', function() {
            if (requestChannel) {
                pusher.unsubscribe(`request-${this.dataset.oldValue || 'DEMO123'}`);
            }
            
            if (pusher.connection.state === 'connected') {
                const requestId = this.value;
                requestChannel = pusher.subscribe(`request-${requestId}`);
                requestChannel.bind('realtime.notification', function(data) {
                    logMessage(`request-${requestId}`, data);
                });
                
                document.getElementById('channelInfo').innerHTML = `
                    registrar-notifications, request-${requestId}
                `;
            }
            
            this.dataset.oldValue = this.value;
        });

        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', initPusher);

        // Clean up on page unload
        window.addEventListener('beforeunload', function() {
            if (pusher) {
                pusher.disconnect();
            }
        });
    </script>
</body>
</html><?php /**PATH D:\Nu-Regisv2\resources\views\pusher-demo.blade.php ENDPATH**/ ?>