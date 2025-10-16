<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Receipt Printing Test - NU Regis</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4><i class="fas fa-print me-2"></i>Receipt Printing Test</h4>
                    </div>
                    <div class="card-body">
                        
                        <!-- Test Printer Connection -->
                        <div class="mb-4">
                            <h5>1. Test Printer Connection</h5>
                            <button type="button" class="btn btn-primary" id="testPrinterBtn">
                                <i class="fas fa-print me-2"></i>Test Printer
                            </button>
                            <div id="testResult" class="mt-2"></div>
                        </div>

                        <hr>

                        <!-- Print Sample Receipt -->
                        <div class="mb-4">
                            <h5>2. Print Sample Receipt (Onsite Request)</h5>
                            <p class="text-muted">This will attempt to print a receipt for an existing onsite request.</p>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="onsiteId" class="form-label">Onsite Request ID:</label>
                                    <input type="number" class="form-control" id="onsiteId" value="14" placeholder="Enter onsite request ID">
                                </div>
                                <div class="col-md-6 d-flex align-items-end">
                                    <button type="button" class="btn btn-success" id="printOnsiteBtn">
                                        <i class="fas fa-receipt me-2"></i>Print Onsite Receipt
                                    </button>
                                </div>
                            </div>
                            <div id="onsiteResult" class="mt-2"></div>
                        </div>

                        <hr>

                        <!-- Print Student Receipt -->
                        <div class="mb-4">
                            <h5>3. Print Sample Receipt (Student Request)</h5>
                            <p class="text-muted">This will attempt to print a receipt for an existing student request.</p>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="studentId" class="form-label">Student Request ID:</label>
                                    <input type="number" class="form-control" id="studentId" value="1" placeholder="Enter student request ID">
                                </div>
                                <div class="col-md-6 d-flex align-items-end">
                                    <button type="button" class="btn btn-success" id="printStudentBtn">
                                        <i class="fas fa-receipt me-2"></i>Print Student Receipt
                                    </button>
                                </div>
                            </div>
                            <div id="studentResult" class="mt-2"></div>
                        </div>

                        <hr>

                        <!-- Instructions -->
                        <div class="mb-4">
                            <h5>4. Instructions</h5>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item"><strong>Printer Test:</strong> Tests if the thermal printer is connected and working</li>
                                <li class="list-group-item"><strong>Receipt Printing:</strong> Prints actual receipts with queue information</li>
                                <li class="list-group-item"><strong>Error Handling:</strong> Shows detailed error messages if printing fails</li>
                            </ul>
                        </div>

                        <!-- Log Area -->
                        <div class="mb-4">
                            <h5>5. Log Output</h5>
                            <textarea id="logOutput" class="form-control" rows="8" readonly placeholder="Log messages will appear here..."></textarea>
                            <button type="button" class="btn btn-sm btn-secondary mt-2" onclick="clearLog()">Clear Log</button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // CSRF token for requests
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Log function
        function log(message) {
            const logArea = document.getElementById('logOutput');
            const timestamp = new Date().toLocaleTimeString();
            logArea.value += `[${timestamp}] ${message}\n`;
            logArea.scrollTop = logArea.scrollHeight;
        }

        function clearLog() {
            document.getElementById('logOutput').value = '';
        }

        // Test printer connection
        document.getElementById('testPrinterBtn').addEventListener('click', function() {
            const btn = this;
            const resultDiv = document.getElementById('testResult');
            
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Testing...';
            log('Starting printer connection test...');
            
            fetch('/kiosk/test-printer', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    resultDiv.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                    log(`✅ Printer test successful: ${data.message}`);
                } else {
                    resultDiv.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                    log(`❌ Printer test failed: ${data.message}`);
                }
            })
            .catch(error => {
                resultDiv.innerHTML = `<div class="alert alert-danger">Error: ${error.message}</div>`;
                log(`❌ Printer test error: ${error.message}`);
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-print me-2"></i>Test Printer';
            });
        });

        // Print onsite receipt
        document.getElementById('printOnsiteBtn').addEventListener('click', function() {
            const btn = this;
            const resultDiv = document.getElementById('onsiteResult');
            const onsiteId = document.getElementById('onsiteId').value;
            
            if (!onsiteId) {
                resultDiv.innerHTML = '<div class="alert alert-warning">Please enter an onsite request ID</div>';
                return;
            }
            
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Printing...';
            log(`Printing onsite receipt for request ID: ${onsiteId}`);
            
            fetch(`/kiosk/print-receipt/onsite/${onsiteId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    resultDiv.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                    log(`✅ Onsite receipt printed: ${data.message}`);
                    if (data.queue_number) {
                        log(`Queue Number: ${data.queue_number}, Total: ₱${data.total_amount || 0}`);
                    }
                } else {
                    resultDiv.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                    log(`❌ Onsite receipt failed: ${data.message}`);
                }
            })
            .catch(error => {
                resultDiv.innerHTML = `<div class="alert alert-danger">Error: ${error.message}</div>`;
                log(`❌ Onsite receipt error: ${error.message}`);
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-receipt me-2"></i>Print Onsite Receipt';
            });
        });

        // Print student receipt
        document.getElementById('printStudentBtn').addEventListener('click', function() {
            const btn = this;
            const resultDiv = document.getElementById('studentResult');
            const studentId = document.getElementById('studentId').value;
            
            if (!studentId) {
                resultDiv.innerHTML = '<div class="alert alert-warning">Please enter a student request ID</div>';
                return;
            }
            
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Printing...';
            log(`Printing student receipt for request ID: ${studentId}`);
            
            fetch(`/kiosk/print-receipt/student/${studentId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    resultDiv.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                    log(`✅ Student receipt printed: ${data.message}`);
                    if (data.queue_number) {
                        log(`Queue Number: ${data.queue_number}, Total: ₱${data.total_amount || 0}`);
                    }
                } else {
                    resultDiv.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                    log(`❌ Student receipt failed: ${data.message}`);
                }
            })
            .catch(error => {
                resultDiv.innerHTML = `<div class="alert alert-danger">Error: ${error.message}</div>`;
                log(`❌ Student receipt error: ${error.message}`);
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-receipt me-2"></i>Print Student Receipt';
            });
        });

        // Initial log message
        log('Receipt printing test page loaded');
        log('Make sure your thermal printer is connected and powered on');
    </script>
</body>
</html><?php /**PATH D:\Nu-Regisv2\resources\views\print-test.blade.php ENDPATH**/ ?>