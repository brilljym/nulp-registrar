<?php
/**
 * Simplified Local Print Service for NU Regis System
 * 
 * This script runs on the local kiosk machine and polls the remote database
 * for pending print jobs, then prints them to the local thermal printer.
 * 
 * Usage: php simple_print_service.php
 */

require_once 'vendor/autoload.php';

use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Mike42\Escpos\CapabilityProfile;

class SimplePrintService
{
    private $config;
    private $running = true;
    
    public function __construct()
    {
        $this->config = [
            // For local testing (use this URL for development)
            // 'remote_api_url' => 'http://127.0.0.1:8000/api',
            
            // For production (pointing to Hostinger deployed website)
            // 'remote_api_url' => 'https://nu-registrar-v2.com/api',
            
            // Use local API since we're running in production environment locally
            'remote_api_url' => 'http://127.0.0.1:8000/api',
            
            'polling_interval' => 3, // Changed from 10 to 3 seconds for faster response
            'printer_name' => 'POS-58',
            'timeout' => 30,
            'max_retries' => 3
        ];
    }
    
    public function start()
    {
        $this->log("🚀 Simple Print Service started");
        $this->log("📡 Polling: {$this->config['remote_api_url']}");
        $this->log("🖨️ Printer: {$this->config['printer_name']}");
        $this->log("⏱️ Interval: {$this->config['polling_interval']} seconds");
        $this->log("----------------------------------------");
        
        $iteration = 0;
        while (true) {
            $iteration++;
            $this->log("🔄 Polling cycle #{$iteration}");
            
            try {
                $this->checkForPrintJobs();
            } catch (Exception $e) {
                $this->log("❌ Error: " . $e->getMessage());
            }
            
            $this->log("😴 Sleeping for {$this->config['polling_interval']} seconds...");
            sleep($this->config['polling_interval']);
        }
    }
    
    private function checkForPrintJobs()
    {
        $jobs = $this->fetchPendingJobs();
        
        if (empty($jobs)) {
            $this->log("✅ No pending print jobs");
            return;
        }
        
        $this->log("📄 Found " . count($jobs) . " pending print job(s)");
        
        foreach ($jobs as $job) {
            $this->processPrintJob($job);
        }
    }
    
    private function fetchPendingJobs()
    {
        $url = $this->config['remote_api_url'] . '/print-jobs/pending?force_db=true';
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->config['timeout'],
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'User-Agent: NU-Regis-Simple-Print-Service/1.0'
            ]
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        
        if ($response === false) {
            throw new Exception("cURL error: {$curlError}");
        }
        
        if ($httpCode !== 200) {
            throw new Exception("HTTP {$httpCode} when fetching jobs. Response: {$response}");
        }
        
        $data = json_decode($response, true);
        
        if (!$data || !$data['success']) {
            throw new Exception("Invalid response from server: " . $response);
        }
        
        return $data['jobs'] ?? [];
    }
    
    private function processPrintJob($job)
    {
        $this->log("🖨️ Processing job #{$job['id']}: {$job['queue_number']}");
        
        try {
            // Print the receipt
            $this->printReceipt($job);
            
            // Mark as completed
            $this->markJobCompleted($job['id']);
            
            $this->log("✅ Job #{$job['id']} completed successfully");
            
        } catch (Exception $e) {
            $this->log("❌ Failed to process job #{$job['id']}: " . $e->getMessage());
            $this->markJobFailed($job['id'], $e->getMessage());
        }
    }
    
    private function printReceipt($job)
    {
        // Connect to printer
        $this->log("🔌 Connecting to printer: {$this->config['printer_name']}");
        $connector = new WindowsPrintConnector($this->config['printer_name']);
        $printer = new Printer($connector);
        $this->log("✅ Printer connected successfully");

        // ===========================
        // HEADER
        // ===========================
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text("--------------------------------\n");
        $printer->text("NU REGISTRAR QUEUE SYSTEM\n");
        $printer->text("--------------------------------\n");
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->text("Date: " . date("Y-m-d H:i:s") . "\n");
        $printer->text("Queue No: " . $job['queue_number'] . "\n");
        
        // Print customer name if available
        if (!empty($job['customer_name'])) {
            $printer->text("Student: " . $job['customer_name'] . "\n");
        }
        
        // Print service/document info
        $printer->text("Service: " . $job['service_type'] . "\n");
        $printer->text("Window: " . $job['window_name'] . "\n");
        $printer->text("--------------------------------\n");

        // ===========================
        // ITEMS / TRANSACTION DETAILS
        // ===========================
        $totalCost = 0;
        if (!empty($job['documents']) && is_array($job['documents'])) {
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text(str_pad("Document", 16) . str_pad("Qty", 6) . str_pad("Total", 10, ' ', STR_PAD_LEFT) . "\n");
            $printer->text("--------------------------------\n");
            
            foreach ($job['documents'] as $doc) {
                $name = $doc['name'] ?? 'Document';
                $qty = $doc['quantity'] ?? 1;
                $price = $doc['price'] ?? 0;
                $itemTotal = $price * $qty;
                $totalCost += $itemTotal;
                
                // Truncate long document names
                $shortName = strlen($name) > 15 ? substr($name, 0, 12) . '...' : $name;
                
                $printer->text(
                    str_pad($shortName, 16) . 
                    str_pad($qty, 6) . 
                    str_pad($itemTotal > 0 ? "₱" . number_format($itemTotal, 2) : "FREE", 10, ' ', STR_PAD_LEFT) . "\n"
                );
            }
            
            $printer->text("--------------------------------\n");
            $printer->setJustification(Printer::JUSTIFY_RIGHT);
            $printer->text("TOTAL: " . ($totalCost > 0 ? "₱" . number_format($totalCost, 2) : "FREE") . "\n");
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("--------------------------------\n\n");
        }

        // ===========================
        // QR CODE - PROFESSIONAL VERIFICATION
        // ===========================
        if (!empty($job['qr_code_data'])) {
            $this->log("📱 Starting QR code generation for job: " . $job['queue_number']);
            try {
                $printer->setJustification(Printer::JUSTIFY_CENTER);

                // Check printer capabilities first
                $this->log("🔍 Checking printer capabilities for QR code support");
                try {
                    // Try to get printer status or capabilities
                    $printer->initialize();
                    $this->log("✅ Printer initialized successfully");
                } catch (Exception $initError) {
                    $this->log("⚠️ Printer initialization warning: " . $initError->getMessage());
                }

                // Test with a simple QR code first
                $testQr = "TEST";
                $this->log("🧪 Testing QR code generation with simple data");
                $printer->qrCode($testQr, Printer::QR_ECLEVEL_M, 4);
                $this->log("✅ Simple QR test passed");

                // Create professional QR data with structured format
                $qrData = [
                    "NU REGISTRAR VERIFICATION",
                    "==============================",
                    "Queue Number: " . $job['queue_number'],
                    "Date/Time: " . date("Y-m-d H:i:s"),
                    "Service: " . $job['service_type'],
                    "Window: " . $job['window_name'],
                    "Status: " . (isset($job['status']) ? $job['status'] : 'Pending'),
                    "Position: " . (isset($job['queue_position']) ? $job['queue_position'] : 'N/A')
                ];

                if (!empty($job['customer_name'])) {
                    $qrData[] = "Student: " . $job['customer_name'];
                }

                if ($totalCost > 0) {
                    $qrData[] = "Total Amount: ₱" . number_format($totalCost, 2);
                }

                $qrData[] = "==============================";
                $qrData[] = "Status URL: " . $job['qr_code_data'];
                $qrData[] = "==============================";
                $qrData[] = "Scan to check your queue status";

                // Join with newlines for clean formatting
                $qrContent = implode("\n", $qrData);
                $this->log("📝 QR content length: " . strlen($qrContent) . " characters");

                // Print decorative border above QR
                $printer->text("╔══════════════════════════════╗\n");
                $printer->text("║        SCAN TO VERIFY        ║\n");
                $printer->text("╚══════════════════════════════╝\n\n");

                // Initialize printer for QR code (some printers need this)
                $printer->initialize();
                $this->log("🔄 Printer initialized for QR code");

                // Generate QR code with different models and shorter content
                $this->log("🔄 Generating QR code for URL: " . $job['qr_code_data']);
                
                // Try with QR Model 1 first (more compatible)
                try {
                    $printer->qrCode($job['qr_code_data'], Printer::QR_ECLEVEL_M, 4, Printer::QR_MODEL_1);
                    $printer->feed(1); // Ensure QR code is printed
                    $this->log("✅ QR code generated with Model 1");
                    $printer->text("QR Debug: Model 1 attempted\n");
                } catch (Exception $model1Error) {
                    $this->log("⚠️ Model 1 failed: " . $model1Error->getMessage());
                    // Try with Model 2
                    try {
                        $printer->qrCode($job['qr_code_data'], Printer::QR_ECLEVEL_M, 4, Printer::QR_MODEL_2);
                        $printer->feed(1); // Ensure QR code is printed
                        $this->log("✅ QR code generated with Model 2");
                        $printer->text("QR Debug: Model 2 attempted\n");
                    } catch (Exception $model2Error) {
                        $this->log("⚠️ Model 2 also failed: " . $model2Error->getMessage());
                        // Fallback to simple text QR representation
                        $printer->text("[QR CODE]\n");
                        $printer->text($job['qr_code_data'] . "\n");
                        $this->log("📝 Printed QR URL as text fallback");
                    }
                }
                $printer->text("\nScan to Check Status\n\n");

                $this->log("✅ Professional QR code section completed");
            } catch (Exception $e) {
                $this->log("⚠️ QR code generation failed: " . $e->getMessage());
                // Professional fallback with decorative elements
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->text("╔══════════════════════════════╗\n");
                $printer->text("║      STATUS CHECK LINK       ║\n");
                $printer->text("╚══════════════════════════════╝\n");
                $printer->text("Check status at:\n");
                $printer->text($job['qr_code_data'] . "\n");
                $printer->text("└──────────────────────────────┘\n\n");
            }
        }

        // ===========================
        // FOOTER
        // ===========================
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text("Please wait for your queue number to be called.\n");
        $printer->text("Thank you!\n");
        $printer->text("--------------------------------\n\n");

        $printer->feed(3);
        $printer->cut();
        $printer->close();
    }
    
    private function markJobCompleted($jobId)
    {
        $url = $this->config['remote_api_url'] . "/print-jobs/{$jobId}/completed?force_db=true";
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_POSTFIELDS => json_encode([
                'printer_name' => $this->config['printer_name'],
                'force_db' => true
            ]),
            CURLOPT_TIMEOUT => $this->config['timeout'],
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json'
            ]
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200) {
            $this->log("⚠️ Failed to mark job completed: HTTP {$httpCode} - {$response}");
        } else {
            $this->log("✅ Job marked as completed");
        }
    }
    
    private function markJobFailed($jobId, $errorMessage)
    {
        $url = $this->config['remote_api_url'] . "/print-jobs/{$jobId}/failed?force_db=true";
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_POSTFIELDS => json_encode([
                'error_message' => $errorMessage,
                'force_db' => true
            ]),
            CURLOPT_TIMEOUT => $this->config['timeout'],
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json'
            ]
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200) {
            $this->log("⚠️ Failed to mark job failed: HTTP {$httpCode}");
        }
    }
    
    private function log($message)
    {
        echo "[" . date('Y-m-d H:i:s') . "] " . $message . "\n";
        flush(); // Force output immediately
    }
}

// Start the service
$service = new SimplePrintService();
$service->start();