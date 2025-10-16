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
$printerName = 'POS-58'; // Default
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

// Allow environment variable override
if (isset($_ENV['POS_PRINTER_NAME'])) {
    $printerName = $_ENV['POS_PRINTER_NAME'];
}

echo "Using printer: {$printerName}\n\n";

try {
    // Test 1: Printer Connection Test
    echo "1. Testing printer connection...\n";
    
    try {
        $connector = new WindowsPrintConnector($printerName);
        $printer = new Printer($connector);
        
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text("--------------------------------\n");
        $printer->text("NU REGIS - PRINTER TEST\n");
        $printer->text("--------------------------------\n");
        $printer->text("Test Date: " . date("Y-m-d H:i:s") . "\n");
        $printer->text("Printer: {$printerName}\n");
        $printer->text("Status: CONNECTED ✓\n");
        $printer->text("--------------------------------\n\n");
        
        $printer->feed(2);
        $printer->cut();
        $printer->close();
        
        echo "✅ Printer test successful! Test page printed.\n\n";
        
    } catch (Exception $e) {
        echo "❌ Printer test failed: " . $e->getMessage() . "\n\n";
        echo "Please check:\n";
        echo "- Printer is connected and turned on\n";
        echo "- Printer name '{$printerName}' is correct (check Windows printer settings)\n";
        echo "- Printer drivers are installed\n\n";
    }
    
    // Test 2: Available Printers
    echo "2. Common printer names to try:\n";
    $commonPrinters = [
        'POS-58',
        'POS-80', 
        'TM-T20',
        'TM-T88',
        'XP-58',
        'Microsoft Print to PDF',
        'Microsoft XPS Document Writer'
    ];
    
    foreach ($commonPrinters as $printer) {
        echo "   - {$printer}\n";
    }
    echo "\n";
    
    // Test 3: Sample Receipt Format
    echo "3. Testing sample receipt format...\n";
    
    try {
        $connector = new WindowsPrintConnector($printerName);
        $printer = new Printer($connector);

        // Sample receipt similar to what the service would print
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text("--------------------------------\n");
        $printer->text("NU REGISTRAR QUEUE SYSTEM\n");
        $printer->text("NU LIPA - REGISTRAR OFFICE\n");
        $printer->text("--------------------------------\n");
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->text("Date: " . date("Y-m-d H:i:s") . "\n");
        $printer->text("Queue No: SAMPLE-001\n");
        $printer->text("Student: John Doe\n");
        $printer->text("Course: BSIT\n");
        $printer->text("Year: 4th Year\n");
        $printer->text("Reference: REF-12345\n");
        $printer->text("--------------------------------\n");

        // Sample document items
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->text(str_pad("Document", 18) . str_pad("Qty", 4) . str_pad("Price", 8, ' ', STR_PAD_LEFT) . "\n");
        $printer->text("--------------------------------\n");
        $printer->text(str_pad("Transcript", 18) . str_pad("1", 4) . str_pad("₱150.00", 8, ' ', STR_PAD_LEFT) . "\n");
        $printer->text(str_pad("Certificate", 18) . str_pad("2", 4) . str_pad("₱200.00", 8, ' ', STR_PAD_LEFT) . "\n");
        $printer->text("--------------------------------\n");
        $printer->setJustification(Printer::JUSTIFY_RIGHT);
        $printer->text("TOTAL: ₱350.00\n");
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->text("--------------------------------\n\n");

        // Status
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text("STATUS: IN QUEUE\n");
        $printer->text("*** Please wait to be called ***\n\n");

        // QR Code (sample data)
        $qrData = "Queue: SAMPLE-001\nName: John Doe\nTotal: ₱350.00\nStatus: IN QUEUE";
        $printer->qrCode($qrData, Printer::QR_ECLEVEL_M, 6);
        $printer->text("\nScan to Verify Request\n\n");

        // Footer
        $printer->text("Please wait for your queue number\n");
        $printer->text("to be called.\n\n");
        $printer->text("Thank you!\n");
        $printer->text("--------------------------------\n");
        $printer->text("Powered by NU LIPA\n");
        $printer->text("Registrar Office\n");
        $printer->text("--------------------------------\n\n");

        $printer->feed(3);
        $printer->cut();
        $printer->close();
        
        echo "✅ Sample receipt printed successfully!\n\n";
        
    } catch (Exception $e) {
        echo "❌ Sample receipt failed: " . $e->getMessage() . "\n\n";
    }
    
    // Test 4: Instructions
    echo "4. To test with actual request data:\n";
    echo "   - Go to the kiosk confirmation page\n";
    echo "   - Click the 'Print Receipt' button\n";
    echo "   - Or use Ctrl+P keyboard shortcut\n\n";
    
    echo "=== TEST COMPLETED ===\n";
    echo "If printer test failed, update the POS_PRINTER_NAME in your .env file\n";
    echo "Example: POS_PRINTER_NAME=\"Your Actual Printer Name\"\n\n";
    echo "You can also test other printer names by running:\n";
    echo "set POS_PRINTER_NAME=\"Other Printer\" && php test_receipt_printer.php\n";
    
} catch (Exception $e) {
    echo "❌ Test failed with error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
?>