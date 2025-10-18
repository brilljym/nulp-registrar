<?php

namespace App\Services;

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Illuminate\Support\Facades\Log;
use App\Models\StudentRequest;
use App\Models\OnsiteRequest;

class ReceiptPrintingService
{
    private $printerName;

    public function __construct()
    {
        // Default printer name - can be configured via environment
        try {
            $this->printerName = config('app.pos_printer_name', 'POS-58');
        } catch (\Exception $e) {
            // Fallback for when Laravel config is not available (like in test scripts)
            $this->printerName = $_ENV['POS_PRINTER_NAME'] ?? 'POS-58';
        }
    }

    /**
     * Print queue confirmation receipt for student or onsite request
     */
    public function printQueueReceipt($request, $type = 'student')
    {
        try {
            $connector = new WindowsPrintConnector($this->printerName);
            $printer = new Printer($connector);

            // ===========================
            // HEADER
            // ===========================
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("--------------------------------\n");
            $printer->text("NU REGISTRAR QUEUE SYSTEM\n");
            $printer->text("NU LIPA - REGISTRAR OFFICE\n");
            $printer->text("--------------------------------\n");
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("Date: " . now()->format("Y-m-d H:i:s") . "\n");
            $printer->text("Queue No: " . ($request->queue_number ?? 'N/A') . "\n");
            
            // Student/Customer Information
            if ($type === 'student') {
                $printer->text("Student: " . $request->student->user->first_name . " " . $request->student->user->last_name . "\n");
                $printer->text("Course: " . $request->student->course . "\n");
                $printer->text("Year: " . $request->student->year_level . "\n");
                $printer->text("Reference: " . $request->reference_no . "\n");
            } else {
                $printer->text("Name: " . $request->full_name . "\n");
                $printer->text("Course: " . $request->course . "\n");
                $printer->text("Year: " . $request->year_level . "\n");
                $printer->text("Reference: " . $request->ref_code . "\n");
            }
            
            $printer->text("--------------------------------\n");

            // ===========================
            // DOCUMENT ITEMS
            // ===========================
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text(str_pad("Document", 18) . str_pad("Qty", 4) . str_pad("Price", 8, ' ', STR_PAD_LEFT) . "\n");
            $printer->text("--------------------------------\n");
            
            $totalAmount = 0;
            foreach ($request->requestItems as $item) {
                $docName = $this->truncateText($item->document->type_document, 17);
                $quantity = $item->quantity;
                $price = $item->document->price ?? 0;
                $itemTotal = $quantity * $price;
                $totalAmount += $itemTotal;
                
                $printer->text(
                    str_pad($docName, 18) . 
                    str_pad($quantity, 4) . 
                    str_pad("₱" . number_format($itemTotal, 2), 8, ' ', STR_PAD_LEFT) . "\n"
                );
            }
            
            $printer->text("--------------------------------\n");
            $printer->setJustification(Printer::JUSTIFY_RIGHT);
            $printer->text("TOTAL: ₱" . number_format($totalAmount, 2) . "\n");
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("--------------------------------\n\n");

            // ===========================
            // STATUS INFORMATION
            // ===========================
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("STATUS: " . strtoupper($request->status) . "\n");
            
            if ($request->status === 'ready_for_pickup') {
                $printer->text("*** READY FOR PICKUP ***\n");
                $printer->text("Please proceed to the window\n");
            } elseif ($request->status === 'in_queue') {
                $printer->text("*** IN QUEUE ***\n");
                $printer->text("Please wait to be called\n");
            } elseif ($request->status === 'waiting') {
                $printer->text("*** WAITING QUEUE ***\n");
                $printer->text("You will be moved automatically\n");
            }
            $printer->text("\n");

            // ===========================
            // QR CODE
            // ===========================
            $qrData = $this->generateQRData($request, $type, $totalAmount);
            $printer->text("\n");
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->qrCode($qrData, Printer::QR_ECLEVEL_H, 8);
            $printer->text("\nScan to Verify Request\n");
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("\n");

            // ===========================
            // FOOTER
            // ===========================
            $printer->text("Please wait for your queue number\n");
            $printer->text("to be called.\n\n");
            $printer->text("Keep this receipt for your\n");
            $printer->text("reference.\n\n");
            $printer->text("Thank you!\n");
            $printer->text("--------------------------------\n");
            $printer->text("Powered by NU LIPA\n");
            $printer->text("Registrar Office\n");
            $printer->text("--------------------------------\n\n");

            $printer->feed(3);
            $printer->cut();
            $printer->close();

            Log::info("Receipt printed successfully for " . ($type === 'student' ? 'student' : 'onsite') . " request ID: " . $request->id);
            
            return [
                'success' => true,
                'message' => '✅ Receipt printed successfully!',
                'queue_number' => $request->queue_number,
                'total_amount' => $totalAmount
            ];

        } catch (\Exception $e) {
            Log::error("Receipt printing failed: " . $e->getMessage());
            
            return [
                'success' => false,
                'message' => '❌ Printing failed: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Generate QR code data for the receipt
     */
    private function generateQRData($request, $type, $totalAmount)
    {
        $queueNumber = $request->queue_number ?? 'N/A';
        $referenceCode = $type === 'student' ? $request->reference_no : $request->ref_code;
        
        // Generate status page URL for QR code instead of verification URL
        try {
            $statusUrl = route('kiosk.status', ['queueNumber' => $queueNumber]);
        } catch (\Exception $e) {
            // Fallback if route helper fails
            $baseUrl = config('app.url', 'http://localhost:8000');
            $statusUrl = $baseUrl . '/kiosk/status/' . $queueNumber;
        }
        
        // For maximum QR code compatibility, use the URL format
        return $statusUrl;
        
        /* Alternative JSON format (comment the line above and uncomment below if you prefer JSON):
        $name = $type === 'student' 
            ? $request->student->user->first_name . ' ' . $request->student->user->last_name
            : $request->full_name;
        
        $qrData = [
            'type' => 'NU_REGIS',
            'queue' => $queueNumber,
            'ref' => $referenceCode,
            'name' => $name,
            'total' => $totalAmount,
            'status' => $request->status,
            'date' => now()->format('Y-m-d H:i'),
            'url' => $verifyUrl
        ];
        
        return json_encode($qrData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        */
    }

    /**
     * Truncate text to fit printer width
     */
    private function truncateText($text, $maxLength)
    {
        if (strlen($text) <= $maxLength) {
            return $text;
        }
        
        return substr($text, 0, $maxLength - 3) . '...';
    }

    /**
     * Test printer connection
     */
    public function testPrinter()
    {
        try {
            $connector = new WindowsPrintConnector($this->printerName);
            $printer = new Printer($connector);
            
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("--------------------------------\n");
            $printer->text("NU REGIS - PRINTER TEST\n");
            $printer->text("--------------------------------\n");
            $printer->text("Test Date: " . now()->format("Y-m-d H:i:s") . "\n");
            $printer->text("Printer: {$this->printerName}\n");
            $printer->text("Status: CONNECTED ✓\n");
            $printer->text("--------------------------------\n\n");
            
            $printer->feed(2);
            $printer->cut();
            $printer->close();
            
            return [
                'success' => true,
                'message' => '✅ Printer test successful!',
                'printer' => $this->printerName
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => '❌ Printer test failed: ' . $e->getMessage(),
                'printer' => $this->printerName,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Set printer name
     */
    public function setPrinterName($printerName)
    {
        $this->printerName = $printerName;
        return $this;
    }

    /**
     * Get available printer names (Windows only)
     */
    public static function getAvailablePrinters()
    {
        // This is a basic implementation - you might want to enhance this
        // based on your specific Windows environment
        $commonPrinterNames = [
            'POS-58',
            'POS-80',
            'TM-T20',
            'TM-T88',
            'XP-58',
            'Microsoft Print to PDF',
            'Microsoft XPS Document Writer'
        ];
        
        return $commonPrinterNames;
    }
}
