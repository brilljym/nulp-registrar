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
        // Check if status changed to "waiting"
        if ($onsiteRequest->wasChanged('status') && $onsiteRequest->status === 'waiting') {
            // Calculate queue position for this registrar
            $position = $this->queueService->getQueuePositionForRegistrar($onsiteRequest->assigned_registrar_id);

            // Send OneSignal notification for waiting status
            $this->oneSignalService->sendQueueWaitingNotification(
                $onsiteRequest->ref_code,
                $position,
                'onsite request'
            );
        }
    }
}