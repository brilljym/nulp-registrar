<?php

namespace App\Observers;

use App\Models\StudentRequest;
use App\Services\OneSignalNotificationService;
use App\Services\QueueService;

class StudentRequestObserver
{
    protected $oneSignalService;
    protected $queueService;

    public function __construct(OneSignalNotificationService $oneSignalService, QueueService $queueService)
    {
        $this->oneSignalService = $oneSignalService;
        $this->queueService = $queueService;
    }

    /**
     * Handle the StudentRequest "updated" event.
     */
    public function updated(StudentRequest $studentRequest): void
    {
        if ($studentRequest->wasChanged('status')) {
            $status = $studentRequest->status;
            $refCode = $studentRequest->reference_no;

            if ($status === 'waiting') {
                // Calculate sequential queue position across all waiting requests
                $position = $this->queueService->getWaitingPositionForStudentRequest($studentRequest);

                // Send OneSignal notification for waiting status with position
                $this->oneSignalService->sendQueueWaitingNotification(
                    $refCode,
                    $position,
                    'student request',
                    $studentRequest->player_id ? [$studentRequest->player_id] : []
                );
            } elseif ($status === 'in_queue') {
                // Send OneSignal notification for in queue status
                $this->oneSignalService->sendQueueStatusNotification(
                    $refCode,
                    'in_queue',
                    [],
                    $studentRequest->player_id ? [$studentRequest->player_id] : []
                );
            } elseif ($status === 'ready_for_pickup') {
                // Send OneSignal notification for ready for pickup status
                $this->oneSignalService->sendQueueStatusNotification(
                    $refCode,
                    'ready_for_pickup',
                    [],
                    $studentRequest->player_id ? [$studentRequest->player_id] : []
                );
            }
        }
    }
}