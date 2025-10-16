<?php
/**
 * Test script to check the local print service without background processing
 */

require_once 'vendor/autoload.php';

echo "Testing Local Print Service Components...\n";

// Test 1: Test API connectivity
echo "\n1. Testing API connectivity...\n";
$url = 'http://127.0.0.1:8000/api/print-jobs/pending';
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 10,
    CURLOPT_HTTPHEADER => [
        'Accept: application/json',
        'User-Agent: NU-Regis-Local-Print-Service-Test/1.0'
    ]
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($response === false) {
    echo "❌ cURL error: {$curlError}\n";
} else {
    echo "✅ HTTP {$httpCode}\n";
    echo "Response: {$response}\n";
    
    $data = json_decode($response, true);
    if ($data && $data['success']) {
        echo "✅ API response is valid\n";
        $jobs = $data['jobs'] ?? [];
        echo "📋 Found " . count($jobs) . " pending jobs\n";
    } else {
        echo "❌ Invalid API response\n";
    }
}

// Test 2: Test thermal printer
echo "\n2. Testing thermal printer availability...\n";
try {
    $printerName = 'POS-58';
    echo "Attempting to connect to printer: {$printerName}\n";
    
    // This will test if the printer is available without actually printing
    $connector = new \Mike42\Escpos\PrintConnectors\WindowsPrintConnector($printerName);
    echo "✅ Printer connection successful\n";
    
    // Test actual printing (optional)
    $choice = readline("Do you want to test print a sample receipt? (y/n): ");
    if (strtolower($choice) === 'y') {
        $printer = new \Mike42\Escpos\Printer($connector);
        $printer->text("Test Print from NU Regis\n");
        $printer->text("Timestamp: " . date('Y-m-d H:i:s') . "\n");
        $printer->cut();
        $printer->close();
        echo "✅ Test print sent to printer\n";
    }
    
} catch (Exception $e) {
    echo "❌ Printer error: " . $e->getMessage() . "\n";
    echo "💡 Make sure the printer '{$printerName}' is:\n";
    echo "   - Properly installed in Windows\n";
    echo "   - Set as the correct printer name\n";
    echo "   - Powered on and ready\n";
}

echo "\n✅ Test completed!\n";