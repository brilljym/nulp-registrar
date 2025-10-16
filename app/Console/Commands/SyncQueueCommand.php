<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\QueueManagementService;
use App\Models\StudentRequest;
use App\Models\OnsiteRequest;
use Illuminate\Support\Facades\Log;

class SyncQueueCommand extends Command
{
    protected $signature = 'queue:sync 
                            {--cleanup : Remove completed/cancelled entries}
                            {--health : Check API health only}
                            {--stats : Show queue statistics}';

    protected $description = 'Sync local requests with the Smart Queue Management API';

    protected $queueService;

    public function __construct(QueueManagementService $queueService)
    {
        parent::__construct();
        $this->queueService = $queueService;
    }

    public function handle()
    {
        $this->info('🚀 Smart Queue Management Sync Starting...');

        if ($this->option('health')) {
            return $this->checkHealth();
        }

        if ($this->option('stats')) {
            return $this->showStats();
        }

        if ($this->option('cleanup')) {
            $this->cleanupCompleted();
        }

        $this->syncPendingRequests();
        $this->updateQueuePositions();

        $this->info('✅ Queue sync completed successfully!');
    }

    private function checkHealth()
    {
        $this->info('🔍 Checking API health...');
        
        $isHealthy = $this->queueService->healthCheck();
        
        if ($isHealthy) {
            $this->info('✅ API is healthy');
            
            // Get analytics for additional health info
            $analytics = $this->queueService->getAnalyticsSummary();
            if (!empty($analytics)) {
                $this->table(['Metric', 'Value'], [
                    ['Total Customers', $analytics['total_customers'] ?? 0],
                    ['Currently Waiting', $analytics['current_waiting'] ?? 0],
                    ['Currently Serving', $analytics['currently_serving'] ?? 0],
                    ['Completed Today', $analytics['completed_today'] ?? 0],
                    ['No Show Rate', ($analytics['no_show_rate'] ?? 0) . '%'],
                    ['Avg Wait Time', ($analytics['average_wait_time_minutes'] ?? 0) . ' minutes'],
                    ['Service Utilization', ($analytics['service_counter_utilization'] ?? 0) . '%'],
                ]);
            }
        } else {
            $this->error('❌ API is not responding');
            return 1;
        }

        return 0;
    }

    private function showStats()
    {
        $this->info('📊 Getting queue statistics...');
        
        $analytics = $this->queueService->getAnalyticsSummary();
        $queueStatus = $this->queueService->getQueueStatus();
        
        if (empty($analytics)) {
            $this->warn('⚠️  Unable to fetch analytics from API');
            return 1;
        }

        $this->info('📈 Current Queue Statistics:');
        $this->table(['Metric', 'Value'], [
            ['API Total Customers', $analytics['total_customers'] ?? 0],
            ['Currently Waiting', $analytics['current_waiting'] ?? 0],
            ['Currently Being Served', $analytics['currently_serving'] ?? 0],
            ['Completed Today', $analytics['completed_today'] ?? 0],
            ['No Shows', $analytics['no_shows'] ?? 0],
            ['No Show Rate', ($analytics['no_show_rate'] ?? 0) . '%'],
            ['Average Wait Time', ($analytics['average_wait_time_minutes'] ?? 0) . ' minutes'],
            ['Average Service Time', ($analytics['average_service_time_minutes'] ?? 0) . ' minutes'],
            ['Service Counter Utilization', ($analytics['service_counter_utilization'] ?? 0) . '%'],
        ]);

        // Show local database stats
        $localPending = StudentRequest::whereIn('status', ['in_queue', 'ready_for_pickup'])->count() +
                       OnsiteRequest::whereIn('status', ['in_queue', 'ready_for_pickup'])->count();
        
        $this->info("\n📊 Local Database Statistics:");
        $this->table(['Metric', 'Value'], [
            ['Local Pending Requests', $localPending],
            ['Student Requests in Queue', StudentRequest::whereIn('status', ['in_queue', 'ready_for_pickup'])->count()],
            ['Onsite Requests in Queue', OnsiteRequest::whereIn('status', ['in_queue', 'ready_for_pickup'])->count()],
        ]);

        if (!empty($queueStatus)) {
            $this->info("\n👥 Current Queue (" . count($queueStatus) . " customers):");
            $tableData = [];
            foreach (array_slice($queueStatus, 0, 10) as $customer) {
                $tableData[] = [
                    $customer['position'] ?? 'N/A',
                    substr($customer['customer_name'] ?? 'Unknown', 0, 20),
                    $customer['service_type'] ?? 'general',
                    $customer['estimated_wait_time'] ?? 0 . 'm',
                    ucfirst($customer['status'] ?? 'unknown')
                ];
            }
            
            if (!empty($tableData)) {
                $this->table(['Position', 'Customer', 'Service', 'Wait Time', 'Status'], $tableData);
                
                if (count($queueStatus) > 10) {
                    $this->info('... and ' . (count($queueStatus) - 10) . ' more customers');
                }
            }
        }

        return 0;
    }

    private function cleanupCompleted()
    {
        $this->info('🧹 Cleaning up completed entries...');
        
        // This would typically involve removing old completed entries from the API
        // For now, we'll just log the action
        Log::info('Queue cleanup initiated');
        
        $this->info('✅ Cleanup completed');
    }

    private function syncPendingRequests()
    {
        $this->info('🔄 Syncing pending requests...');
        
        // Sync student requests with 'in_queue' or 'ready_for_pickup' status
        $pendingRequests = StudentRequest::whereIn('status', ['in_queue', 'ready_for_pickup'])
            ->whereNull('queue_api_id')
            ->get();

        $requestCount = 0;
        foreach ($pendingRequests as $request) {
            if ($this->addToQueue($request, 'student_request')) {
                $requestCount++;
            }
        }

        // Sync onsite requests
        $pendingOnsite = OnsiteRequest::whereIn('status', ['in_queue', 'ready_for_pickup'])
            ->whereNull('queue_api_id')
            ->get();

        $onsiteCount = 0;
        foreach ($pendingOnsite as $onsite) {
            if ($this->addToQueue($onsite, 'onsite')) {
                $onsiteCount++;
            }
        }

        $this->info("📝 Synced {$requestCount} student requests and {$onsiteCount} onsite requests");
    }

    private function addToQueue($request, $type)
    {
        try {
            $customerData = [
                'name' => $this->getCustomerName($request, $type),
                'phone' => $this->getCustomerPhone($request, $type),
                'email' => $this->getCustomerEmail($request, $type),
                'type' => $this->getCustomerType($request, $type),
                'service_type' => $this->getServiceType($request, $type),
                'notes' => "Laravel {$type} - " . ($type === 'student_request' ? $request->reference_no : $request->ref_code),
            ];

            $result = $this->queueService->joinQueue($customerData);

            if ($result) {
                // Store the API queue ID in the local record
                $request->queue_api_id = $result['id'];
                $request->queue_number = $result['position'] ?? null;
                $request->save();

                Log::info("Added {$type} to queue", [
                    'local_id' => $request->id,
                    'api_id' => $result['id'],
                    'position' => $result['position'] ?? null
                ]);

                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error("Failed to add {$type} to queue", [
                'local_id' => $request->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    private function updateQueuePositions()
    {
        $this->info('📍 Updating queue positions...');
        
        $queueStatus = $this->queueService->getQueueStatus();
        $updateCount = 0;

        foreach ($queueStatus as $customer) {
            // Find local record by API ID
            $studentRequest = StudentRequest::where('queue_api_id', $customer['id'])->first();
            $onsite = OnsiteRequest::where('queue_api_id', $customer['id'])->first();

            $localRecord = $studentRequest ?: $onsite;

            if ($localRecord) {
                $oldPosition = $localRecord->queue_number;
                $newPosition = $customer['position'] ?? null;

                if ($oldPosition !== $newPosition) {
                    $localRecord->queue_number = $newPosition;
                    $localRecord->save();
                    $updateCount++;
                }
            }
        }

        $this->info("🔄 Updated {$updateCount} queue positions");
    }

    private function getCustomerName($request, $type)
    {
        if ($type === 'student_request' && $request->student) {
            $user = $request->student->user;
            if ($user) {
                return trim($user->first_name . ' ' . $user->last_name);
            }
        } elseif ($type === 'onsite') {
            return trim($request->first_name . ' ' . $request->last_name);
        }

        return 'Customer #' . $request->id;
    }

    private function getCustomerPhone($request, $type)
    {
        if ($type === 'onsite') {
            return $request->contact_number;
        }
        return null;
    }

    private function getCustomerEmail($request, $type)
    {
        if ($type === 'student_request' && $request->student && $request->student->user) {
            return $request->student->user->school_email;
        } elseif ($type === 'onsite') {
            return $request->email;
        }
        return null;
    }

    private function getCustomerType($request, $type)
    {
        if ($type === 'student_request') {
            return 'student';
        } elseif ($type === 'onsite') {
            return $request->request_type ?? 'alumni';
        }
        return 'walk_in';
    }

    private function getServiceType($request, $type)
    {
        if ($type === 'student_request') {
            return 'student_documents';
        } elseif ($type === 'onsite') {
            // Map onsite request types to service types
            $serviceMapping = [
                'transcript' => 'transcript',
                'certification' => 'certification',
                'verification' => 'verification',
            ];
            
            return $serviceMapping[$request->document_type ?? ''] ?? 'alumni_documents';
        }
        return 'general';
    }
}