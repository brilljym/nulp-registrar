<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Test - NU Regis</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .qr-section {
            text-align: center;
            margin: 20px 0;
            padding: 20px;
            border: 2px dashed #007bff;
            border-radius: 10px;
        }
        .test-url {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            font-family: monospace;
            word-break: break-all;
            margin: 10px 0;
        }
        .btn {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 5px;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .success { color: #28a745; }
        .info { color: #17a2b8; }
        .instructions {
            background-color: #e9ecef;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔗 QR Code Verification Test</h1>
        <p class="info">This page helps you test the QR code functionality for thermal receipt printing.</p>
        
        <div class="instructions">
            <h3>📝 Instructions:</h3>
            <ol>
                <li>Print a test receipt from the <a href="/kiosk/print-test" class="btn">Print Test Page</a></li>
                <li>Scan the QR code on the receipt with your phone camera or QR scanner app</li>
                <li>The QR code should open the verification URL and show request details</li>
                <li>Test the URLs below to verify the system is working</li>
            </ol>
        </div>

        <div class="qr-section">
            <h3>🔗 Sample Verification URLs</h3>
            <p>These URLs simulate what the QR codes contain:</p>
            
            <div class="test-url">
                <strong>Sample URL:</strong><br>
                http://127.0.0.1:8000/verify/NU6D9D30
            </div>
            <a href="/verify/NU6D9D30" class="btn" target="_blank">Test Verification</a>
            
            <div class="test-url">
                <strong>Template:</strong><br>
                http://127.0.0.1:8000/verify/{REFERENCE_CODE}
            </div>
        </div>

        <div class="qr-section">
            <h3>🖨️ QR Code Improvements Made</h3>
            <ul style="text-align: left;">
                <li class="success">✅ Changed from complex text to simple URL format</li>
                <li class="success">✅ Increased QR error correction level to HIGH</li>
                <li class="success">✅ Increased QR code size from 6 to 8</li>
                <li class="success">✅ Centered QR code on receipt</li>
                <li class="success">✅ Added verification endpoint</li>
                <li class="success">✅ Shorter, more scannable data</li>
            </ul>
        </div>

        <div class="qr-section">
            <h3>📱 Testing Your QR Code</h3>
            <p>Use any of these methods to test:</p>
            <ul style="text-align: left;">
                <li>📱 Phone camera (iPhone/Android built-in scanner)</li>
                <li>📱 QR Scanner apps</li>
                <li>💻 Online QR readers</li>
                <li>🖥️ Click the test links above</li>
            </ul>
        </div>

        <div class="instructions">
            <h3>🔧 Troubleshooting:</h3>
            <ul>
                <li><strong>QR won't scan:</strong> Check printer quality settings, try larger size</li>
                <li><strong>URL doesn't work:</strong> Verify server is running on port 8000</li>
                <li><strong>404 Error:</strong> Make sure the reference code exists in database</li>
                <li><strong>Network issues:</strong> QR contains localhost URL - works only on local network</li>
            </ul>
        </div>

        <div style="text-align: center; margin-top: 30px;">
            <a href="/kiosk/print-test" class="btn">🖨️ Go to Print Test</a>
            <a href="/kiosk" class="btn">🏠 Back to Kiosk</a>
        </div>
    </div>
</body>
</html><?php /**PATH D:\Nu-Regisv2\resources\views\qr-test.blade.php ENDPATH**/ ?>