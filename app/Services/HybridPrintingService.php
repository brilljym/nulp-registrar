<?php

namespace App\Services;

use App\Models\PrintJob;
use App\Models\StudentRequest;
use App\Models\OnsiteRequest;
use Illuminate\Support\Facades\Log;

class HybridPrintingService
{
    private $receiptPrintingService;

    public function __construct(ReceiptPrintingService $receiptPrintingService)
    {
        $this->receiptPrintingService = $receiptPrintingService;
    }

    /**
     * Handle printing based on environment (local vs production)
     */
    public function handlePrintRequest($request, $type = 'student')
    {
        // Check if we can print locally (development/local kiosk)
        if ($this->canPrintLocally()) {
            return $this->printDirectly($request, $type);
        } else {
            // Production environment - queue the print job
            return $this->queuePrintJob($request, $type);
        }
    }

    /**
     * Print directly to thermal printer (local environment)
     */
    private function printDirectly($request, $type)
    {
        try {
            $result = $this->receiptPrintingService->printQueueReceipt($request, $type);
            
            // Still log to print jobs table for tracking
            $this->createPrintJobRecord($request, $type, 'completed');
            
            return $result;
        } catch (\Exception $e) {
            // If direct printing fails, queue it as fallback
            Log::warning('Direct printing failed, queuing job: ' . $e->getMessage());
            return $this->queuePrintJob($request, $type);
        }
    }

    /**
     * Queue print job for later processing (production environment)
     */
    private function queuePrintJob($request, $type)
    {
        try {
            $printJob = $this->createPrintJobRecord($request, $type, 'pending');
            
            return [
                'success' => true,
                'message' => 'Print job queued successfully. Receipt will print shortly.',
                'method' => 'queued',
                'job_id' => $printJob->id
            ];
        } catch (\Exception $e) {
            Log::error('Failed to queue print job: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to queue print job: ' . $e->getMessage(),
                'method' => 'error'
            ];
        }
    }

    /**
     * Create print job record in database
     */
    private function createPrintJobRecord($request, $type, $status = 'pending')
    {
        $documents = $request->requestItems->map(function($item) {
            return [
                'name' => $item->document->type_document,
                'quantity' => $item->quantity,
                'price' => $item->price ?? 0
            ];
        })->toArray();

        $customerName = $type === 'student' 
            ? $request->student->user->first_name . ' ' . $request->student->user->last_name
            : $request->full_name;

        $qrData = url('/verify/' . ($type === 'student' ? $request->reference_no : $request->ref_code));

        // Generate status page URL for QR code
        $statusUrl = config('app.url', 'http://nu-registrar-v2.com') . '/kiosk/status/' . $request->queue_number;

        return PrintJob::create([
            'request_type' => $type,
            'request_id' => $request->id,
            'queue_number' => $request->queue_number,
            'customer_name' => $customerName,
            'documents' => $documents,
            'total_cost' => $request->total_cost ?? 0,
            'qr_data' => $statusUrl,
            'status' => $status,
            'printed_at' => $status === 'completed' ? now() : null
        ]);
    }

    /**
     * Check if local printing is available
     */
    private function canPrintLocally()
    {
        // Check environment and system requirements
        return config('app.env') === 'local' && 
               PHP_OS_FAMILY === 'Windows' && 
               class_exists('Mike42\Escpos\PrintConnectors\WindowsPrintConnector') &&
               config('printing.local_enabled', true);
    }

    /**
     * Get pending print jobs for local print service
     */
    public function getPendingPrintJobs($limit = 10)
    {
        return PrintJob::pending()
            ->orderBy('created_at', 'asc')
            ->limit($limit)
            ->get();
    }

    /**
     * Process a specific print job
     */
    public function processPrintJob(PrintJob $printJob)
    {
        try {
            // Mark as printing
            $printJob->update(['status' => 'printing']);

            // Get the original request
            if ($printJob->request_type === 'student') {
                $request = StudentRequest::find($printJob->request_id);
            } else {
                $request = OnsiteRequest::find($printJob->request_id);
            }

            if (!$request) {
                throw new \Exception('Original request not found');
            }

            // Print the receipt
            $result = $this->receiptPrintingService->printQueueReceipt($request, $printJob->request_type);

            if ($result['success']) {
                $printJob->update([
                    'status' => 'completed',
                    'printed_at' => now(),
                    'printer_name' => config('printing.printer_name', 'POS-58')
                ]);
            } else {
                throw new \Exception($result['message'] ?? 'Print failed');
            }

            return $result;
        } catch (\Exception $e) {
            $printJob->update([
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);

            Log::error('Print job failed: ' . $e->getMessage(), [
                'job_id' => $printJob->id,
                'queue_number' => $printJob->queue_number
            ]);

            return [
                'success' => false,
                'message' => 'Print job failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Mark print job as completed (for external processing)
     */
    public function markPrintJobCompleted($jobId)
    {
        $printJob = PrintJob::find($jobId);
        if ($printJob) {
            $printJob->update([
                'status' => 'completed',
                'printed_at' => now()
            ]);
            return true;
        }
        return false;
    }
}