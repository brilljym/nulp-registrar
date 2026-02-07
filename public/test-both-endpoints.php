<?php
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Both API Endpoints</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; }
        .endpoint { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .endpoint h2 { margin-top: 0; color: #333; }
        .result { background: #f8f9fa; padding: 15px; border-radius: 4px; margin-top: 10px; }
        .success { border-left: 4px solid #28a745; }
        .error { border-left: 4px solid #dc3545; }
        .field { margin: 8px 0; padding: 8px; background: white; border-radius: 4px; }
        .field strong { color: #0066cc; display: inline-block; width: 200px; }
        button { background: #0066cc; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-size: 16px; }
        button:hover { background: #0052a3; }
        pre { background: #2d2d2d; color: #f8f8f2; padding: 15px; border-radius: 4px; overflow-x: auto; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 API Endpoint Comparison Test</h1>
        
        <div class="endpoint">
            <h2>1️⃣ Transaction Reference Endpoint</h2>
            <button onclick="testTransactionEndpoint()">Test /api/transactions/reference/SR-20251104-0002</button>
            <div id="transaction-result" class="result" style="display:none;"></div>
        </div>

        <div class="endpoint">
            <h2>2️⃣ Kiosk/Queue Number Endpoint</h2>
            <button onclick="testKioskEndpoint()">Test /api/kiosk/A006</button>
            <div id="kiosk-result" class="result" style="display:none;"></div>
        </div>

        <div class="endpoint">
            <h2>3️⃣ Comparison</h2>
            <button onclick="compareBoth()">Compare Both Endpoints</button>
            <div id="comparison-result" class="result" style="display:none;"></div>
        </div>
    </div>

    <script>
        async function testTransactionEndpoint() {
            const resultDiv = document.getElementById('transaction-result');
            resultDiv.style.display = 'block';
            resultDiv.innerHTML = '<p>⏳ Testing...</p>';

            try {
                const response = await fetch('/api/transactions/reference/SR-20251104-0002');
                const data = await response.json();
                
                resultDiv.className = 'result success';
                resultDiv.innerHTML = `
                    <h3>✅ Success (Status: ${response.status})</h3>
                    <div class="field"><strong>ID:</strong> ${data.id || 'N/A'}</div>
                    <div class="field"><strong>Reference No:</strong> ${data.reference_no || 'N/A'}</div>
                    <div class="field"><strong>Queue Number:</strong> ${data.queue_number || 'N/A'}</div>
                    <div class="field"><strong>Position:</strong> <span style="font-size:24px;color:#28a745;font-weight:bold;">${data.position !== undefined ? data.position : 'N/A'}</span></div>
                    <div class="field"><strong>Status:</strong> ${data.status || 'N/A'}</div>
                    <div class="field"><strong>Student Name:</strong> ${data.student_name || 'N/A'}</div>
                    <div class="field"><strong>Registrar:</strong> ${data.registrar_name || 'N/A'}</div>
                    <h4>Debug Info:</h4>
                    <div class="field"><strong>Raw Status:</strong> ${data.debug_info?.raw_status || 'N/A'}</div>
                    <div class="field"><strong>Display Status:</strong> ${data.debug_info?.display_status || 'N/A'}</div>
                    <div class="field"><strong>Is First:</strong> ${data.debug_info?.is_first !== undefined ? data.debug_info.is_first : 'N/A'}</div>
                    <div class="field"><strong>Position (debug):</strong> ${data.debug_info?.position !== undefined ? data.debug_info.position : 'N/A'}</div>
                    <div class="field"><strong>Total for Registrar:</strong> ${data.debug_info?.total_requests_for_registrar || 'N/A'}</div>
                    <h4>Full Response:</h4>
                    <pre>${JSON.stringify(data, null, 2)}</pre>
                `;
            } catch (error) {
                resultDiv.className = 'result error';
                resultDiv.innerHTML = `<h3>❌ Error</h3><p>${error.message}</p>`;
            }
        }

        async function testKioskEndpoint() {
            const resultDiv = document.getElementById('kiosk-result');
            resultDiv.style.display = 'block';
            resultDiv.innerHTML = '<p>⏳ Testing...</p>';

            try {
                const response = await fetch('/api/kiosk/A006');
                const data = await response.json();
                
                resultDiv.className = 'result success';
                resultDiv.innerHTML = `
                    <h3>✅ Success (Status: ${response.status})</h3>
                    <div class="field"><strong>ID:</strong> ${data.id || 'N/A'}</div>
                    <div class="field"><strong>Reference No:</strong> ${data.reference_no || 'N/A'}</div>
                    <div class="field"><strong>Queue Number:</strong> ${data.queue_number || 'N/A'}</div>
                    <div class="field"><strong>Position:</strong> <span style="font-size:24px;color:#28a745;font-weight:bold;">${data.position !== undefined ? data.position : 'N/A'}</span></div>
                    <div class="field"><strong>Status:</strong> ${data.status || 'N/A'}</div>
                    <div class="field"><strong>Student Name:</strong> ${data.student_name || 'N/A'}</div>
                    <div class="field"><strong>Registrar:</strong> ${data.registrar_name || 'N/A'}</div>
                    <h4>Debug Info:</h4>
                    <div class="field"><strong>Raw Status:</strong> ${data.debug_info?.raw_status || 'N/A'}</div>
                    <div class="field"><strong>Display Status:</strong> ${data.debug_info?.display_status || 'N/A'}</div>
                    <div class="field"><strong>Is First:</strong> ${data.debug_info?.is_first !== undefined ? data.debug_info.is_first : 'N/A'}</div>
                    <div class="field"><strong>Position (debug):</strong> ${data.debug_info?.position !== undefined ? data.debug_info.position : 'N/A'}</div>
                    <div class="field"><strong>Total for Registrar:</strong> ${data.debug_info?.total_requests_for_registrar || 'N/A'}</div>
                    <h4>Full Response:</h4>
                    <pre>${JSON.stringify(data, null, 2)}</pre>
                `;
            } catch (error) {
                resultDiv.className = 'result error';
                resultDiv.innerHTML = `<h3>❌ Error</h3><p>${error.message}</p>`;
            }
        }

        async function compareBoth() {
            const resultDiv = document.getElementById('comparison-result');
            resultDiv.style.display = 'block';
            resultDiv.innerHTML = '<p>⏳ Comparing both endpoints...</p>';

            try {
                const [transactionResponse, kioskResponse] = await Promise.all([
                    fetch('/api/transactions/reference/SR-20251104-0002'),
                    fetch('/api/kiosk/A006')
                ]);

                const transactionData = await transactionResponse.json();
                const kioskData = await kioskResponse.json();

                const positionMatch = transactionData.position === kioskData.position;
                const queueMatch = transactionData.queue_number === kioskData.queue_number;
                const statusMatch = transactionData.status === kioskData.status;

                resultDiv.className = 'result ' + (positionMatch && queueMatch && statusMatch ? 'success' : 'error');
                resultDiv.innerHTML = `
                    <h3>${positionMatch && queueMatch && statusMatch ? '✅' : '⚠️'} Comparison Results</h3>
                    <table style="width:100%; border-collapse: collapse; margin-top: 15px;">
                        <tr style="background:#f0f0f0;">
                            <th style="padding:10px; text-align:left; border:1px solid #ddd;">Field</th>
                            <th style="padding:10px; text-align:left; border:1px solid #ddd;">Transaction Endpoint</th>
                            <th style="padding:10px; text-align:left; border:1px solid #ddd;">Kiosk Endpoint</th>
                            <th style="padding:10px; text-align:center; border:1px solid #ddd;">Match</th>
                        </tr>
                        <tr>
                            <td style="padding:10px; border:1px solid #ddd;"><strong>Position</strong></td>
                            <td style="padding:10px; border:1px solid #ddd; font-size:20px; font-weight:bold;">${transactionData.position}</td>
                            <td style="padding:10px; border:1px solid #ddd; font-size:20px; font-weight:bold;">${kioskData.position}</td>
                            <td style="padding:10px; border:1px solid #ddd; text-align:center;">${positionMatch ? '✅' : '❌'}</td>
                        </tr>
                        <tr style="background:#f9f9f9;">
                            <td style="padding:10px; border:1px solid #ddd;"><strong>Queue Number</strong></td>
                            <td style="padding:10px; border:1px solid #ddd;">${transactionData.queue_number}</td>
                            <td style="padding:10px; border:1px solid #ddd;">${kioskData.queue_number}</td>
                            <td style="padding:10px; border:1px solid #ddd; text-align:center;">${queueMatch ? '✅' : '❌'}</td>
                        </tr>
                        <tr>
                            <td style="padding:10px; border:1px solid #ddd;"><strong>Status</strong></td>
                            <td style="padding:10px; border:1px solid #ddd;">${transactionData.status}</td>
                            <td style="padding:10px; border:1px solid #ddd;">${kioskData.status}</td>
                            <td style="padding:10px; border:1px solid #ddd; text-align:center;">${statusMatch ? '✅' : '❌'}</td>
                        </tr>
                        <tr style="background:#f9f9f9;">
                            <td style="padding:10px; border:1px solid #ddd;"><strong>ID</strong></td>
                            <td style="padding:10px; border:1px solid #ddd;">${transactionData.id}</td>
                            <td style="padding:10px; border:1px solid #ddd;">${kioskData.id}</td>
                            <td style="padding:10px; border:1px solid #ddd; text-align:center;">${transactionData.id === kioskData.id ? '✅' : '❌'}</td>
                        </tr>
                    </table>
                    
                    <h4 style="margin-top:20px;">📊 Summary:</h4>
                    <div class="field">Both endpoints return <strong>position = ${transactionData.position}</strong></div>
                    <div class="field">Both endpoints return <strong>queue_number = ${transactionData.queue_number}</strong></div>
                    <div class="field">Both endpoints are ${positionMatch && queueMatch ? '<span style="color:#28a745;">✅ CONSISTENT</span>' : '<span style="color:#dc3545;">❌ INCONSISTENT</span>'}</div>
                `;
            } catch (error) {
                resultDiv.className = 'result error';
                resultDiv.innerHTML = `<h3>❌ Error</h3><p>${error.message}</p>`;
            }
        }
    </script>
</body>
</html>
