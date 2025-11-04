<?php

namespace App\Observers;

use App\Models\OnsiteRequest;
use App\Services\OneSignalNotificationService;
use App\Services\QueueService;

class OnsiteRequestObserver
{
    protected $oneSignalService;
    protected $queueService;

    public function __construct(OneSignalNotificationService $oneSignalService, QueueService $queueService)
    {
        $this->oneSignalService = $oneSignalService;
        $this->queueService = $queueService;
    }

    /**
     * Handle the OnsiteRequest "updated" event.
     */
    public function updated(OnsiteRequest $onsiteRequest): void
    {
        if ($onsiteRequest->wasChanged('status')) {
            $status = $onsiteRequest->status;
            $refCode = $onsiteRequest->ref_code;

            if ($status === 'waiting') {
                // Calculate sequential queue position across all waiting requests
                $position = $this->queueService->getWaitingPositionForRequest($onsiteRequest);

                // Send OneSignal notification for waiting status with position
                $this->oneSignalService->sendQueueWaitingNotification(
                    $refCode,
                    $position,
                    'onsite request'
                );
            } elseif ($status === 'in_queue') {
                // Send OneSignal notification for in queue status
                $this->oneSignalService->sendQueueStatusNotification(
                    $refCode,
                    'in queue'
                );
            } elseif ($status === 'ready_for_pickup') {
                // Send OneSignal notification for ready for pickup status
                $this->oneSignalService->sendQueueStatusNotification(
                    $refCode,
                    'ready for pickup'
                );
            }
        }
    }
}