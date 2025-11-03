<?php

namespace App\Services;

use Berkayk\OneSignal\OneSignalClient;
use Illuminate\Support\Facades\Log;

class OneSignalNotificationService
{
    protected $oneSignal;

    public function __construct()
    {
        $this->oneSignal = new OneSignalClient(
            config('onesignal.app_id'),
            config('onesignal.rest_api_key'),
            config('onesignal.user_auth_key')
        );
    }

    /**
     * Send a push notification to all users
     *
     * @param string $title
     * @param string $message
     * @param array $data
     * @return mixed
     */
    public function sendToAll($title, $message, $data = [])
    {
        try {
            $params = [
                'contents' => [
                    'en' => $message
                ],
                'headings' => [
                    'en' => $title
                ],
                'included_segments' => ['All']
            ];

            if (!empty($data)) {
                $params['data'] = $data;
            }

            $response = $this->oneSignal->sendNotificationCustom($params);

            return $response;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Send a push notification to specific users by player IDs
     *
     * @param array $playerIds
     * @param string $title
     * @param string $message
     * @param array $data
     * @return mixed
     */
    public function sendToPlayers($playerIds, $title, $message, $data = [])
    {
        try {
            $params = [
                'contents' => [
                    'en' => $message
                ],
                'headings' => [
                    'en' => $title
                ],
                'include_player_ids' => $playerIds
            ];

            if (!empty($data)) {
                $params['data'] = $data;
            }

            $response = $this->oneSignal->sendNotificationCustom($params);

            Log::info('OneSignal notification sent to specific players', [
                'player_ids' => $playerIds,
                'title' => $title,
                'message' => $message,
                'response' => $response
            ]);

            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to send OneSignal notification to players', [
                'error' => $e->getMessage(),
                'player_ids' => $playerIds,
                'title' => $title,
                'message' => $message
            ]);

            return null;
        }
    }

    /**
     * Send queue position notification when status changes to waiting
     *
     * @param string $referenceId
     * @param int $position
     * @param string $requestType
     * @param array $playerIds
     * @return array|null
     */
    public function sendQueueWaitingNotification($referenceId, $position, $requestType = 'request', $playerIds = [])
    {
        $title = 'Queue Status Update';
        $message = "Your $requestType is now waiting in queue. You are position #$position.";

        $data = [
            'type' => 'queue_status_update',
            'reference_id' => $referenceId,
            'status' => 'waiting',
            'position' => $position,
            'request_type' => $requestType
        ];

        if (!empty($playerIds)) {
            return $this->sendToPlayers($playerIds, $title, $message, $data);
        } else {
            return $this->sendToAll($title, $message, $data);
        }
    }

    /**
     * Send general queue status notification
     *
     * @param string $referenceId
     * @param string $status
     * @param array $additionalData
     * @param array $playerIds
     * @return array|null
     */
    public function sendQueueStatusNotification($referenceId, $status, $additionalData = [], $playerIds = [])
    {
        $title = 'Queue Status Update';
        $message = "Your request status has been updated to: " . ucfirst($status);

        $data = array_merge([
            'type' => 'queue_status_update',
            'reference_id' => $referenceId,
            'status' => $status
        ], $additionalData);

        if (!empty($playerIds)) {
            return $this->sendToPlayers($playerIds, $title, $message, $data);
        } else {
            return $this->sendToAll($title, $message, $data);
        }
    }
}