<?php
/**
 * Local Print Service for NU Regis System
 * 
 * This script runs on the local kiosk machine and polls the remote database
 * for pending print jobs, then prints them to the local thermal printer.
 * 
 * Usage: php local_print_service.php
 */

require_once 'vendor/autoload.php';

use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;

class LocalPrintService
{
    private $config;
    private $running = true;
    private $lastCheck;
    
    public function __construct()
    {
        $this->config = [
            // For local testing, use the test endpoint first
            // 'remote_api_url' => 'http://127.0.0.1:8000/api', // Local for testing
            'remote_api_url' => 'https://nu-registrar-v2.com/api', // Replace with your domain for production
            'polling_interval' => 3, // Changed from 10 to 3 seconds for faster response
            'printer_name' => 'POS-58', // Your printer name
            'max_retries' => 3,
            'timeout' => 30,
        ];
        
        $this->lastCheck = time();
        
        // Handle termination signals
        if (function_exists('pcntl_signal')) {
            if (defined('SIGTERM')) {
                pcntl_signal(SIGTERM, [$this, 'shutdown']);
            }
            if (defined('SIGINT')) {
                pcntl_signal(SIGINT, [$this, 'shutdown']);
            }
        }
    }
    
    public function start()
    {
        $this->log("🚀 Local Print Service started");
        $this->log("📡 Polling: {$this->config['remote_api_url']}");
        $this->log("🖨️ Printer: {$this->config['printer_name']}");
        $this->log("⏱️ Interval: {$this->config['polling_interval']} seconds");
        $this->log("----------------------------------------");
        
        while ($this->running) {
            try {
                $this->checkForPrintJobs();
                $this->sleep();
            } catch (Exception $e) {
                $this->log("❌ Error: " . $e->getMessage());
                $this->sleep(10); // Wait longer on error
            }
        }
        
        $this->log("🛑 Local Print Service stopped");
    }
    
    private function checkForPrintJobs()
    {
        $jobs = $this->fetchPendingJobs();
        
        if (empty($jobs)) {
            $this->logVerbose("✅ No pending print jobs");
            return;
        }
        
        $this->log("📄 Found " . count($jobs) . " pending print job(s)");
        
        foreach ($jobs as $job) {
            $this->processPrintJob($job);
        }
    }
    
    private function fetchPendingJobs()
    {
        $url = $this->config['remote_api_url'] . '/print-jobs/pending';
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->config['timeout'],
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'User-Agent: NU-Regis-Local-Print-Service/1.0'
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
            throw new Exception("Invalid response from server");
        }
        
        return $data['jobs'] ?? [];
    }
    
    private function processPrintJob($job)
    {
        $this->log("🖨️ Processing job #{$job['id']}: {$job['queue_number']}");
        
        try {
            $this->printReceipt($job);
            $this->markJobCompleted($job['id']);
            $this->log("✅ Job #{$job['id']} printed successfully");
        } catch (Exception $e) {
            $this->log("❌ Job #{$job['id']} failed: " . $e->getMessage());
            $this->markJobFailed($job['id'], $e->getMessage());
        }
    }
    
    private function printReceipt($job)
    {
        // Initialize printer
        $connector = new WindowsPrintConnector($this->config['printer_name']);
        $printer = new Printer($connector);
        
        // Header
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setTextSize(2, 2);
        $printer->text("NU LIPA\n");
        $printer->setTextSize(1, 1);
        $printer->text("REGISTRAR OFFICE\n");
        $printer->text("QUEUE RECEIPT\n");
        $printer->text("========================\n");
        
        // Queue info
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->text("Queue No: " . $job['queue_number'] . "\n");
        $printer->text("Customer: " . $job['customer_name'] . "\n");
        $printer->text("Date: " . date('Y-m-d H:i:s') . "\n");
        $printer->text("------------------------\n");
        
        // Documents
        $printer->text("DOCUMENTS:\n");
        $totalCost = 0;
        foreach ($job['documents'] as $doc) {
            $printer->text($doc['name']);
            if ($doc['quantity'] > 1) {
                $printer->text(" x" . $doc['quantity']);
            }
            if (isset($doc['price']) && $doc['price'] > 0) {
                $printer->text(" - P" . number_format($doc['price'], 2));
                $totalCost += $doc['price'] * $doc['quantity'];
            }
            $printer->text("\n");
        }
        
        if ($totalCost > 0) {
            $printer->text("------------------------\n");
            $printer->text("TOTAL: P" . number_format($totalCost, 2) . "\n");
        }
        
        $printer->text("========================\n");
        
        // QR Code
        if (!empty($job['qr_data'])) {
            try {
                // Use the existing ReceiptPrintingService QR method instead
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->text("\nQR: " . $job['qr_data'] . "\n");
                $printer->text("Scan to verify\n");
            } catch (Exception $e) {
                $this->log("⚠️ QR code generation failed: " . $e->getMessage());
            }
        }
        
        // Footer
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text("\nVerify at: nu-regis.com\n");
        $printer->text("Thank you!\n");
        $printer->text("========================\n");
        
        // Cut paper
        $printer->cut();
        $printer->close();
    }
    
    private function markJobCompleted($jobId)
    {
        $this->updateJobStatus($jobId, 'completed');
    }
    
    private function markJobFailed($jobId, $errorMessage)
    {
        $this->updateJobStatus($jobId, 'failed', $errorMessage);
    }
    
    private function updateJobStatus($jobId, $status, $errorMessage = null)
    {
        $url = $this->config['remote_api_url'] . "/print-jobs/{$jobId}/{$status}";
        
        $data = [
            'printer_name' => $this->config['printer_name']
        ];
        
        if ($errorMessage) {
            $data['error_message'] = $errorMessage;
        }
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_POSTFIELDS => json_encode($data),
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
            $this->log("⚠️ Failed to update job status: HTTP {$httpCode}");
        }
    }
    
    private function sleep($seconds = null)
    {
        $sleepTime = $seconds ?? $this->config['polling_interval'];
        sleep($sleepTime);
        
        // Handle signals if available
        if (function_exists('pcntl_signal_dispatch')) {
            pcntl_signal_dispatch();
        }
    }
    
    public function shutdown($signal = null)
    {
        $this->log("🛑 Shutdown signal received");
        $this->running = false;
    }
    
    private function log($message)
    {
        $timestamp = date('Y-m-d H:i:s');
        echo "[{$timestamp}] {$message}\n";
    }
    
    private function logVerbose($message)
    {
        if (time() - $this->lastCheck > 60) { // Log every minute
            $this->log($message);
            $this->lastCheck = time();
        }
    }
}

// Check if running from command line
if (php_sapi_name() === 'cli') {
    $service = new LocalPrintService();
    $service->start();
} else {
    echo "This script must be run from command line\n";
    exit(1);
}