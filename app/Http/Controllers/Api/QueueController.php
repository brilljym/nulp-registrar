<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\QueueManagementService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class QueueController extends Controller
{
    protected $queueService;

    public function __construct(QueueManagementService $queueService)
    {
        $this->queueService = $queueService;
    }

    /**
     * Join the queue
     */
    public function joinQueue(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'type' => 'nullable|string|in:student,alumni,staff,walk_in,appointment',
            'service_type' => 'nullable|string',
            'reference_code' => 'nullable|string',
            'notes' => 'nullable|string|max:500',
        ]);

        $customerData = [
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'type' => $request->type ?? 'walk_in',
            'service_type' => $request->service_type ?? 'general',
            'notes' => $request->notes,
        ];

        $result = $this->queueService->joinQueue($customerData);

        if ($result) {
            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Successfully joined the queue'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to join queue'
        ], 500);
    }

    /**
     * Get current queue status
     */
    public function getQueueStatus(): JsonResponse
    {
        $queueStatus = $this->queueService->getQueueStatus();
        
        return response()->json([
            'success' => true,
            'data' => $queueStatus
        ]);
    }

    /**
     * Get customer status by ID
     */
    public function getCustomerStatus(string $customerId): JsonResponse
    {
        $status = $this->queueService->getCustomerStatus($customerId);

        if ($status) {
            return response()->json([
                'success' => true,
                'data' => $status
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Customer not found'
        ], 404);
    }

    /**
     * Update customer status
     */
    public function updateCustomerStatus(Request $request, string $customerId): JsonResponse
    {
        $request->validate([
            'status' => 'required|string|in:waiting,in_service,completed,no_show,cancelled',
            'service_duration' => 'nullable|integer|min:1'
        ]);

        $success = $this->queueService->updateCustomerStatus(
            $customerId,
            $request->status,
            $request->service_duration
        );

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'Customer status updated successfully'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to update customer status'
        ], 500);
    }

    /**
     * Remove customer from queue
     */
    public function removeFromQueue(string $customerId): JsonResponse
    {
        $success = $this->queueService->removeFromQueue($customerId);

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'Customer removed from queue'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to remove customer from queue'
        ], 500);
    }

    /**
     * Get wait time estimate
     */
    public function getWaitTimeEstimate(Request $request): JsonResponse
    {
        $request->validate([
            'position' => 'required|integer|min:1',
            'service_type' => 'nullable|string',
            'counters' => 'nullable|integer|min:1|max:10'
        ]);

        $estimate = $this->queueService->getWaitTimeEstimate(
            $request->position,
            $request->service_type ?? 'general',
            $request->counters ?? 3
        );

        return response()->json([
            'success' => true,
            'data' => [
                'estimated_wait_time' => $estimate['estimated_turnaround_time_minutes'] ?? 0,
                'formatted_wait_time' => $this->queueService->formatWaitTime($estimate['estimated_turnaround_time_minutes'] ?? 0),
                'position' => $request->position
            ]
        ]);
    }

    /**
     * Get analytics summary
     */
    public function getAnalytics(): JsonResponse
    {
        $analytics = $this->queueService->getAnalyticsSummary();
        
        return response()->json([
            'success' => true,
            'data' => $analytics
        ]);
    }

    /**
     * Get next customer
     */
    public function getNextCustomer(): JsonResponse
    {
        $nextCustomer = $this->queueService->getNextCustomer();

        return response()->json([
            'success' => true,
            'data' => $nextCustomer,
            'has_next_customer' => $nextCustomer !== null
        ]);
    }

    /**
     * Update service counters
     */
    public function updateServiceCounters(Request $request): JsonResponse
    {
        $request->validate([
            'counters' => 'required|integer|min:1|max:10'
        ]);

        $success = $this->queueService->updateServiceCounters($request->counters);

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'Service counters updated successfully'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to update service counters'
        ], 500);
    }

    /**
     * Health check
     */
    public function healthCheck(): JsonResponse
    {
        $isHealthy = $this->queueService->healthCheck();

        return response()->json([
            'success' => true,
            'api_status' => $isHealthy ? 'healthy' : 'unhealthy',
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Real-time queue updates (for WebSocket or polling)
     */
    public function getRealTimeUpdates(Request $request): JsonResponse
    {
        $lastUpdate = $request->input('last_update');
        
        // Get current queue status
        $queueStatus = $this->queueService->getQueueStatus();
        $analytics = $this->queueService->getAnalyticsSummary();
        
        return response()->json([
            'success' => true,
            'data' => [
                'queue' => $queueStatus,
                'analytics' => $analytics,
                'timestamp' => now()->toISOString(),
                'total_waiting' => count($queueStatus),
                'average_wait_time' => $analytics['average_wait_time_minutes'] ?? 0
            ]
        ]);
    }
}