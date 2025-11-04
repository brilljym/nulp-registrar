<?php

namespace App\Observers;

use App\Models\OnsiteRequest;
use App\Services\OneSignalNotificationService;
use App\Services\QueueService;
use Illuminate\Support\Facades\Log;

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

            Log::info("OnsiteRequestObserver: Status changed to '{$status}' for ref_code: {$refCode}");

            if ($status === 'waiting') {
                // Calculate sequential queue position across all waiting requests
                $position = $this->queueService->getWaitingPositionForRequest($onsiteRequest);

                Log::info("OnsiteRequestObserver: Sending waiting notification for {$refCode} at position {$position}");

                // Send OneSignal notification for waiting status with position
                $result = $this->oneSignalService->sendQueueWaitingNotification(
                    $refCode,
                    $position,
                    'onsite request'
                );

                Log::info("OnsiteRequestObserver: Waiting notification result for {$refCode}:", ['result' => $result]);

            } elseif ($status === 'in_queue') {
                Log::info("OnsiteRequestObserver: Sending in_queue notification for {$refCode}");

                // Send OneSignal notification for in queue status
                $result = $this->oneSignalService->sendQueueStatusNotification(
                    $refCode,
                    'in queue'
                );

                Log::info("OnsiteRequestObserver: In queue notification result for {$refCode}:", ['result' => $result]);

            } elseif ($status === 'ready_for_pickup') {
                Log::info("OnsiteRequestObserver: Sending ready_for_pickup notification for {$refCode}");

                // Send OneSignal notification for ready for pickup status
                $result = $this->oneSignalService->sendQueueStatusNotification(
                    $refCode,
                    'ready for pickup'
                );

                Log::info("OnsiteRequestObserver: Ready for pickup notification result for {$refCode}:", ['result' => $result]);
            }
        }
    }
}