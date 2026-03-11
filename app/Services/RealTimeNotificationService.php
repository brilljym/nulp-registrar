<?php

namespace App\Services;

use App\Events\RealTimeEvent;
use Illuminate\Support\Facades\Log;

class RealTimeNotificationService
{
    /**
     * Send a real-time notification
     *
     * @param string $message
     * @param string $type
     * @param array $data
     * @param array $channels
     * @return void
     */
    public function sendNotification($message, $type = 'info', $data = [], $channels = ['registrar-notifications'])
    {
        try {
            Log::info('Attempting to send real-time notification', [
                'message' => $message,
                'type' => $type,
                'channels' => $channels,
                'broadcast_driver' => config('broadcasting.default')
            ]);
            
            // Broadcast the event
            event(new RealTimeEvent($message, $type, $data, $channels));
            
            Log::info('Real-time notification sent successfully', [
                'message' => $message,
                'type' => $type,
                'data' => $data,
                'channels' => $channels
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send real-time notification', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'broadcast_config' => config('broadcasting.connections.pusher')
            ]);
        }
    }

    /**
     * Send a success notification
     *
     * @param string $message
     * @param array $data
     * @param array $channels
     * @return void
     */
    public function sendSuccess($message, $data = [], $channels = ['registrar-notifications'])
    {
        $this->sendNotification($message, 'success', $data, $channels);
    }

    /**
     * Send an error notification
     *
     * @param string $message
     * @param array $data
     * @param array $channels
     * @return void
     */
    public function sendError($message, $data = [], $channels = ['registrar-notifications'])
    {
        $this->sendNotification($message, 'error', $data, $channels);
    }

    /**
     * Send a warning notification
     *
     * @param string $message
     * @param array $data
     * @param array $channels
     * @return void
     */
    public function sendWarning($message, $data = [], $channels = ['registrar-notifications'])
    {
        $this->sendNotification($message, 'warning', $data, $channels);
    }

    /**
     * Send a request status update notification
     *
     * @param string $requestId
     * @param string $status
     * @param string $message
     * @param array $data
     * @return void
     */
    public function sendRequestStatusUpdate($requestId, $status, $message, $data = [])
    {
        $channels = [
            'registrar-notifications',
            "request-{$requestId}"
        ];

        $notificationData = array_merge($data, [
            'request_id' => $requestId,
            'status' => $status,
            'status_update' => true
        ]);

        $this->sendNotification($message, 'status-update', $notificationData, $channels);
    }
}