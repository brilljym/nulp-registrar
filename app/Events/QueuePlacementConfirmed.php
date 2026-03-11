<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QueuePlacementConfirmed implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $queueData;
    public $requestType;
    public $action;
    public $message;

    /**
     * Create a new event instance.
     */
    public function __construct($request, $requestType, $action, $message = null)
    {
        $this->requestType = $requestType;
        $this->action = $action;
        $this->message = $message;
        
        // Prepare queue data for broadcasting
        $this->queueData = [
            'id' => $request->id,
            'queue_number' => $request->queue_number,
            'reference_code' => $requestType === 'student' ? $request->reference_no : $request->ref_code,
            'status' => $request->status,
            'type' => $requestType,
            'action' => $action,
            'timestamp' => now()->toISOString(),
            'window_assignment' => $this->getWindowAssignment($request),
            'customer_name' => $this->getCustomerName($request, $requestType),
            'service_type' => $this->getServiceType($request, $requestType),
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('queue-updates'),
            new Channel('registrar-notifications'),
            new Channel('queue-display-updates'),
            new Channel('real-time-updates'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'realtime.notification';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'type' => 'queue_placement_confirmed',
            'message' => $this->message ?: "Queue placement confirmed for {$this->queueData['queue_number']}",
            'data' => $this->queueData,
            'timestamp' => now()->toISOString(),
            'action' => $this->action,
            'request_type' => $this->requestType,
        ];
    }

    /**
     * Get window assignment for the request
     */
    private function getWindowAssignment($request)
    {
        if (!$request->queue_number) return null;
        
        $letter = substr($request->queue_number, 0, 1);
        $windowNumber = (ord($letter) - ord('A')) % 3 + 1;
        
        return "Window {$windowNumber}";
    }

    /**
     * Get customer name based on request type
     */
    private function getCustomerName($request, $type)
    {
        if ($type === 'student' && $request->student && $request->student->user) {
            return trim($request->student->user->first_name . ' ' . $request->student->user->last_name);
        } elseif ($type === 'onsite') {
            return trim($request->first_name . ' ' . $request->last_name);
        }
        
        return 'Customer #' . $request->id;
    }

    /**
     * Get service type based on request
     */
    private function getServiceType($request, $type)
    {
        if ($type === 'student') {
            return 'student_documents';
        } elseif ($type === 'onsite') {
            // You can enhance this based on actual document types
            return 'alumni_documents';
        }
        
        return 'general';
    }
}