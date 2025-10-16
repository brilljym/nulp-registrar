<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PrintJob;
use App\Services\HybridPrintingService;
use App\Services\ReceiptPrintingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PrintJobController extends Controller
{
    /**
     * Get pending print jobs for local print service
     */
    public function getPendingJobs(Request $request)
    {
        try {
            $environment = app()->environment();
            $isProduction = app()->environment('production');
            
            // Check if we should serve database jobs (production or if forced by parameter)
            $serveFromDatabase = $isProduction || $request->get('force_db', false);
            
            if ($serveFromDatabase) {
                $jobs = PrintJob::where('status', 'pending')
                    ->orderBy('created_at', 'asc')
                    ->limit(10)
                    ->get()
                    ->map(function ($job) {
                        // Extract service type from documents array
                        $documents = is_string($job->documents) ? json_decode($job->documents, true) : $job->documents;
                        $serviceType = is_array($documents) && !empty($documents) 
                            ? $documents[0]['name'] ?? 'Document Request'
                            : 'Document Request';
                        
                        return [
                            'id' => $job->id,
                            'queue_number' => $job->queue_number,
                            'service_type' => $serviceType,
                            'window_name' => 'Window 1', // Default window for now
                            'qr_code_data' => $job->qr_data ?? '',
                            'customer_name' => $job->customer_name ?? '',
                            'documents' => $documents,
                            'total_cost' => $job->total_cost ?? 0,
                            'created_at' => $job->created_at->toISOString()
                        ];
                    });

                return response()->json([
                    'success' => true,
                    'jobs' => $jobs,
                    'count' => $jobs->count(),
                    'message' => "Database mode - environment: {$environment}",
                    'debug' => [
                        'environment' => $environment,
                        'is_production' => $isProduction,
                        'serve_from_db' => $serveFromDatabase
                    ]
                ]);
            }
            
            // For local development, return empty
            return response()->json([
                'success' => true,
                'jobs' => [],
                'count' => 0,
                'message' => "Local development mode - environment: {$environment}",
                'debug' => [
                    'environment' => $environment,
                    'is_production' => $isProduction,
                    'serve_from_db' => $serveFromDatabase
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching jobs: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark a print job as completed
     */
    public function markCompleted(Request $request, $jobId)
    {
        try {
            if (app()->environment('production')) {
                $printJob = PrintJob::findOrFail($jobId);
                
                $printJob->update([
                    'status' => 'completed',
                    'printed_at' => now(),
                    'printer_name' => $request->get('printer_name', 'POS-58')
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Print job marked as completed'
                ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Local development mode - job marked as completed',
                'job_id' => $jobId
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark a print job as failed
     */
    public function markFailed(Request $request, $jobId)
    {
        try {
            if (app()->environment('production')) {
                $printJob = PrintJob::findOrFail($jobId);
                
                $printJob->update([
                    'status' => 'failed',
                    'error_message' => $request->get('error_message', 'Print failed')
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Print job marked as failed'
                ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Local development mode - job marked as failed',
                'job_id' => $jobId
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get print job statistics
     */
    public function getStatus(Request $request)
    {
        return response()->json([
            'success' => true,
            'statistics' => [
                'pending' => 0,
                'printing' => 0,
                'completed_today' => 0,
                'failed_today' => 0,
                'total_today' => 0
            ],
            'timestamp' => now()->toISOString(),
            'message' => 'Local development mode'
        ]);
    }
}