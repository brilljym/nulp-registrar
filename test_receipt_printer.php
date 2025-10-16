<?php
require __DIR__ . '/vendor/autoload.php';

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

/**
 * Test script for receipt printing functionality
 * Usage: php test_receipt_printer.php
 */

echo "=== NU REGIS RECEIPT PRINTER TEST ===\n\n";

// Load environment variables if .env file exists
if (file_exists(__DIR__ . '/.env')) {
    $envFile = file_get_contents(__DIR__ . '/.env');
    $lines = explode("\n", $envFile);
    foreach ($lines as $line) {
        if (strpos($line, 'POS_PRINTER_NAME=') === 0) {
            $printerName = trim(str_replace('POS_PRINTER_NAME=', '', $line), '"');
            break;
        }
    }
}

// Default printer name if not found in .env
$printerName = $printerName ?? 'POS-58';

echo "Using printer: {$printerName}\n\n";

try {
    
    // Test 1: Printer Connection Test
    echo "1. Testing printer connection...\n";
    $testResult = $receiptService->testPrinter();
    
    if ($testResult['success']) {
        echo "✅ " . $testResult['message'] . "\n\n";
    } else {
        echo "❌ " . $testResult['message'] . "\n\n";
        echo "Please check:\n";
        echo "- Printer is connected and turned on\n";
        echo "- Printer name 'POS-58' is correct (check Windows printer settings)\n";
        echo "- Printer drivers are installed\n\n";
    }
    
    // Test 2: Available Printers
    echo "2. Available printer names:\n";
    $printers = ReceiptPrintingService::getAvailablePrinters();
    foreach ($printers as $printer) {
        echo "   - {$printer}\n";
    }
    echo "\n";
    
    // Test 3: Mock Receipt Print (if you want to test with sample data)
    echo "3. To test with actual request data:\n";
    echo "   - Go to the kiosk confirmation page\n";
    echo "   - Click the 'Print Receipt' button\n";
    echo "   - Or use Ctrl+P keyboard shortcut\n\n";
    
    echo "=== TEST COMPLETED ===\n";
    echo "If printer test failed, update the POS_PRINTER_NAME in your .env file\n";
    echo "Example: POS_PRINTER_NAME=\"Your Actual Printer Name\"\n";
    
} catch (Exception $e) {
    echo "❌ Test failed with error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
?>