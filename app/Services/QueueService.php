<?php

namespace App\Services;

use App\Models\OnsiteRequest;
use App\Models\StudentRequest;
use App\Models\Registrar;
use App\Models\Window;
use App\Models\QueueCounter;
use Illuminate\Support\Facades\DB;

class QueueService
{
    protected $notificationService;
    protected $oneSignalService;

    public function __construct(RealTimeNotificationService $notificationService, OneSignalNotificationService $oneSignalService)
    {
        $this->notificationService = $notificationService;
        $this->oneSignalService = $oneSignalService;
    }
    /**
     * Generate a unique queue number for requests (onsite and student)
     * Format: A001, A002, B001, etc. (resets daily)
     */
    public function generateQueueNumber(): string
    {
        $today = now()->format('Y-m-d');

        // Use database transaction to atomically increment the counter
        $number = DB::transaction(function () use ($today) {
            $counter = QueueCounter::where('date', $today)->lockForUpdate()->first();

            if (!$counter) {
                // Initialize counter based on existing queue numbers for today from both tables
                $lastQueueNumberOnsite = OnsiteRequest::whereDate('created_at', $today)
                    ->whereNotNull('queue_number')
                    ->orderBy('queue_number', 'desc')
                    ->value('queue_number');

                $lastQueueNumberStudent = StudentRequest::whereDate('created_at', $today)
                    ->whereNotNull('queue_number')
                    ->orderBy('queue_number', 'desc')
                    ->value('queue_number');

                $lastQueueNumbers = array_filter([$lastQueueNumberOnsite, $lastQueueNumberStudent]);
                $lastQueueNumber = !empty($lastQueueNumbers) ? max($lastQueueNumbers) : null;

                $initialCounter = 0;
                if ($lastQueueNumber) {
                    $letter = substr($lastQueueNumber, 0, 1);
                    $num = (int) substr($lastQueueNumber, 1);
                    $letterIndex = ord($letter) - ord('A');
                    $initialCounter = $letterIndex * 999 + $num;
                }

                $counter = QueueCounter::create([
                    'date' => $today,
                    'counter' => $initialCounter
                ]);
            } else {
                // Ensure counter is at least as high as existing queue numbers from both tables
                $lastQueueNumberOnsite = OnsiteRequest::whereDate('created_at', $today)
                    ->whereNotNull('queue_number')
                    ->orderBy('queue_number', 'desc')
                    ->value('queue_number');

                $lastQueueNumberStudent = StudentRequest::whereDate('created_at', $today)
                    ->whereNotNull('queue_number')
                    ->orderBy('queue_number', 'desc')
                    ->value('queue_number');

                $lastQueueNumbers = array_filter([$lastQueueNumberOnsite, $lastQueueNumberStudent]);
                $requiredCounter = 0;
                if (!empty($lastQueueNumbers)) {
                    $maxQueueNumber = max($lastQueueNumbers);
                    $letter = substr($maxQueueNumber, 0, 1);
                    $num = (int) substr($maxQueueNumber, 1);
                    $letterIndex = ord($letter) - ord('A');
                    $requiredCounter = $letterIndex * 999 + $num;
                }

                if ($counter->counter < $requiredCounter) {
                    $counter->counter = $requiredCounter;
                    $counter->save();
                }
            }

            $counter->increment('counter');
            return $counter->counter;
        });

        // Now calculate the letter and number
        $letterIndex = intdiv($number - 1, 999);
        $letter = chr(ord('A') + $letterIndex);
        $num = (($number - 1) % 999) + 1;

        return $letter . str_pad($num, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Assign a registrar to an onsite request based on availability and window
     */
    public function assignRegistrarToRequest(OnsiteRequest $request): ?Registrar
    {
        // If request already has an assigned registrar, return it
        if ($request->assigned_registrar_id) {
            return Registrar::where('user_id', $request->assigned_registrar_id)->first();
        }

        // Find available registrars (not currently busy with other requests)
        // Get registrar user IDs that are currently busy
        $busyRegistrarUserIds = OnsiteRequest::whereIn('current_step', ['processing', 'window'])
            ->whereIn('status', ['processing', 'in_queue', 'ready_for_pickup'])
            ->whereNotNull('assigned_registrar_id')
            ->where('id', '!=', $request->id) // Exclude current request
            ->pluck('assigned_registrar_id')
            ->unique()
            ->toArray();

        // Find windows that are currently occupied
        $occupiedWindowIds = \App\Models\Window::where('is_occupied', true)->pluck('id')->toArray();

        $availableRegistrars = Registrar::with('user')
            ->whereNotIn('user_id', $busyRegistrarUserIds)
            ->get();

        if ($availableRegistrars->isEmpty()) {
            return null;
        }

        // If request has a window assigned, check if that window's registrar is available
        if ($request->window_id) {
            $window = \App\Models\Window::find($request->window_id);
            if ($window) {
                $windowNumber = str_replace('Window ', '', $window->name);
                $windowRegistrar = $availableRegistrars->first(function($registrar) use ($windowNumber) {
                    return $registrar->window_number == $windowNumber;
                });

                if ($windowRegistrar && !in_array($request->window_id, $occupiedWindowIds)) {
                    // Check if this registrar already has an active request
                    $hasActiveRequest = OnsiteRequest::where('assigned_registrar_id', $windowRegistrar->user_id)
                        ->whereIn('status', ['processing', 'in_queue'])
                        ->where('id', '!=', $request->id)
                        ->exists();

                    $request->update(['assigned_registrar_id' => $windowRegistrar->user_id]);

                    if ($hasActiveRequest) {
                        // If registrar has active request, set this one to waiting
                        $request->update([
                            'status' => 'waiting',
                            'current_step' => 'waiting'
                        ]);

                        // Calculate queue position for this registrar
                        $position = $this->getQueuePositionForRegistrar($request->assigned_registrar_id);

                        // Send OneSignal notification for waiting status
                        $this->oneSignalService->sendQueueWaitingNotification(
                            $request->ref_code,
                            $position,
                            'onsite request'
                        );
                    } else {
                        // Mark window as occupied only if this is the first request
                        $window->update(['is_occupied' => true]);
                    }

                    return $windowRegistrar;
                }
            }
        }

        // Find the first available registrar with a free window
        foreach ($availableRegistrars as $registrar) {
            $windowName = 'Window ' . $registrar->window_number;
            $registrarWindow = \App\Models\Window::where('name', $windowName)->first();

            if ($registrarWindow && !$registrarWindow->is_occupied) {
                // Check if this registrar already has an active request
                $hasActiveRequest = OnsiteRequest::where('assigned_registrar_id', $registrar->user_id)
                    ->whereIn('status', ['processing', 'in_queue'])
                    ->where('id', '!=', $request->id)
                    ->exists();

                // Assign this registrar
                $request->update([
                    'assigned_registrar_id' => $registrar->user_id,
                    'window_id' => $registrarWindow->id
                ]);

                if ($hasActiveRequest) {
                    // If registrar has active request, set this one to waiting
                    $request->update([
                        'status' => 'waiting',
                        'current_step' => 'waiting'
                    ]);

                    // Calculate queue position for this registrar
                    $position = $this->getQueuePositionForRegistrar($request->assigned_registrar_id);

                    // Send OneSignal notification for waiting status
                    $this->oneSignalService->sendQueueWaitingNotification(
                        $request->ref_code,
                        $position,
                        'onsite request'
                    );
                } else {
                    // Mark window as occupied only if this is the first request
                    $registrarWindow->update(['is_occupied' => true]);
                }

                return $registrar;
            }
        }

        // If no free windows, don't assign yet
        return null;
    }

    /**
     * Process the next waiting request for a specific window
     */
    public function processNextRequestForWindow(?int $windowId): void
    {
        if (!$windowId) {
            return;
        }
        
        // Find the next request waiting for any window that could use this specific window
        $nextRequest = OnsiteRequest::whereIn('status', ['registrar_approved', 'in_queue'])
            ->where(function($query) use ($windowId) {
                $query->whereNull('window_id')
                      ->orWhere('window_id', $windowId);
            })
            ->whereNull('assigned_registrar_id')
            ->orderBy('created_at', 'asc')
            ->first();
            
        if ($nextRequest) {
            // Try to assign this request to the freed window
            $assignedRegistrar = $this->assignRegistrarToRequest($nextRequest);
            
            if ($assignedRegistrar) {
                $nextRequest->update([
                    'current_step' => 'processing',
                    'status' => 'processing'
                ]);
                
                // Send notification about the assignment
                $this->notificationService->sendRequestStatusUpdate(
                    $nextRequest->ref_code,
                    'processing',
                    "Your request has been assigned to a registrar and is now being processed",
                    [
                        'student_name' => $nextRequest->full_name,
                        'registrar_name' => $assignedRegistrar->user->first_name . ' ' . $assignedRegistrar->user->last_name,
                        'window_name' => $nextRequest->window ? $nextRequest->window->name : 'Window TBD'
                    ]
                );
            }
        }
    }

    /**
     * Get the current queue position for a request
     */
    public function getQueuePosition(OnsiteRequest $request): int
    {
        if ($request->current_step === 'completed' || $request->current_step === 'release') {
            return 0; // Not in queue anymore
        }

        $queue = OnsiteRequest::where('current_step', 'window')
            ->where('window_id', $request->window_id)
            ->orderBy('updated_at')
            ->pluck('id')
            ->toArray();

        $position = array_search($request->id, $queue) + 1;
        return $position ?: 0;
    }

    /**
     * Get the current queue position for a request among all waiting requests for a specific registrar
     */
    public function getQueuePositionForRegistrar(int $registrarUserId): int
    {
        // Get count of all waiting requests across all registrars
        $totalWaiting = OnsiteRequest::where('status', 'waiting')->count();

        // Return the next position in the unified waiting queue
        return $totalWaiting + 1;
    }

    /**
     * Get the position of a specific onsite request in the unified waiting queue
     */
    public function getWaitingPositionForRequest(OnsiteRequest $request): int
    {
        if ($request->status !== 'waiting') {
            return 0; // Not waiting, so no position
        }

        // Get ALL waiting requests across all registrars, ordered by creation time
        $allWaitingRequests = OnsiteRequest::where('status', 'waiting')
            ->orderBy('created_at', 'asc')
            ->pluck('id')
            ->toArray();

        // Find the position of this request in the unified queue
        $position = array_search($request->id, $allWaitingRequests) + 1;
        return $position ?: 0;
    }

    /**
     * Get the position of a specific student request in the unified waiting queue
     */
    public function getWaitingPositionForStudentRequest($studentRequest): int
    {
        if ($studentRequest->status !== 'waiting') {
            return 0; // Not waiting, so no position
        }

        // Get ALL waiting student requests across all registrars, ordered by creation time
        $allWaitingRequests = \App\Models\StudentRequest::where('status', 'waiting')
            ->orderBy('created_at', 'asc')
            ->pluck('id')
            ->toArray();

        // Find the position of this request in the unified queue
        $position = array_search($studentRequest->id, $allWaitingRequests) + 1;
        return $position ?: 0;
    }

    /**
     * Get estimated wait time for a request (in minutes)
     */
    public function getEstimatedWaitTime(OnsiteRequest $request): int
    {
        $position = $this->getQueuePosition($request);

        if ($position <= 1) {
            return 0; // Currently being served or next
        }

        // Assume average processing time of 5 minutes per request
        return ($position - 1) * 5;
    }

    /**
     * Process the next request in queue for a specific window
     */
    public function processNextInQueue(int $windowId): ?OnsiteRequest
    {
        $nextRequest = OnsiteRequest::where('current_step', 'window')
            ->where('window_id', $windowId)
            ->orderBy('updated_at')
            ->first();

        if ($nextRequest) {
            // Assign registrar and move to processing
            $registrar = $this->assignRegistrarToRequest($nextRequest);

            if ($registrar) {
                $nextRequest->update([
                    'current_step' => 'processing',
                    'status' => 'processing',
                    'assigned_registrar_id' => $registrar->user_id
                ]);

                // Send real-time notification to the user
                $this->notificationService->sendRequestStatusUpdate(
                    $nextRequest->ref_code,
                    'processing',
                    "Your onsite request {$nextRequest->ref_code} is now being processed",
                    [
                        'student_name' => $nextRequest->full_name,
                        'document_type' => $nextRequest->requestItems->pluck('document.type_document')->join(', '),
                        'registrar_name' => $registrar->user->first_name . ' ' . $registrar->user->last_name,
                        'request_type' => 'onsite',
                        'queue_number' => $nextRequest->queue_number,
                        'window_number' => $nextRequest->window_id
                    ]
                );

                return $nextRequest;
            }
        }

        return null;
    }

    /**
     * Complete a request and process the next one in queue
     */
    public function completeRequest(OnsiteRequest $request): void
    {
        $windowId = $request->window_id;

        // Mark request as completed
        $request->update(['current_step' => 'completed']);

        // Free up the window
        if ($windowId) {
            Window::where('id', $windowId)->update(['is_occupied' => false]);
        }

        // Process next request in queue
        $this->processNextInQueue($windowId);
    }

    /**
     * Ensure windows are freed for completed requests assigned to registrars
     * This method checks for any completed requests that still have occupied windows
     * and frees those windows to prevent orphaned occupied windows
     */
    public function freeWindowsForCompletedRequests(): void
    {
        // Find windows that are occupied but have no active requests
        $occupiedWindows = Window::where('is_occupied', true)->get();

        foreach ($occupiedWindows as $window) {
            // Check if this window has any active (non-completed) requests assigned to registrars
            $hasActiveRequest = OnsiteRequest::where('window_id', $window->id)
                ->where('assigned_registrar_id', '!=', null)
                ->whereIn('status', ['processing', 'in_queue', 'ready_for_pickup'])
                ->whereIn('current_step', ['processing', 'window', 'payment'])
                ->exists();

            // If no active requests but window is occupied, free it
            if (!$hasActiveRequest) {
                $window->update(['is_occupied' => false]);
                
                // Log this action (optional)
                \Illuminate\Support\Facades\Log::info("Freed orphaned window {$window->name} (ID: {$window->id}) - no active requests found");
            }
        }
    }

    /**
     * Promote waiting requests to in_queue when active request becomes ready for pickup
     * This ensures that when a registrar completes their current request, the next waiting request moves up
     */
    public function promoteWaitingRequestsForRegistrar(int $registrarUserId): void
    {
        // Find all in_queue requests for this registrar (excluding ready_for_pickup ones)
        $activeRequests = OnsiteRequest::where('assigned_registrar_id', $registrarUserId)
            ->where('status', 'in_queue')
            ->orderBy('created_at', 'asc')
            ->get();

        // If there are multiple in_queue requests, the first one should remain active,
        // but we don't need to do anything since the display logic handles the prioritization

        // However, if there are no in_queue requests, check if there are waiting requests to promote
        if ($activeRequests->isEmpty()) {
            $waitingRequest = OnsiteRequest::where('assigned_registrar_id', $registrarUserId)
                ->where('status', 'waiting')
                ->orderBy('created_at', 'asc')
                ->first();

            if ($waitingRequest) {
                // Promote the waiting request to in_queue
                $waitingRequest->update([
                    'status' => 'in_queue',
                    'current_step' => 'processing'
                ]);

                // Find and assign window if not already assigned
                if (!$waitingRequest->window_id) {
                    $registrar = \App\Models\Registrar::where('user_id', $registrarUserId)->first();
                    if ($registrar) {
                        $windowName = 'Window ' . $registrar->window_number;
                        $registrarWindow = \App\Models\Window::where('name', $windowName)->first();
                        if ($registrarWindow) {
                            $waitingRequest->update(['window_id' => $registrarWindow->id]);
                            $registrarWindow->update(['is_occupied' => true]);
                        }
                    }
                }
            }
        }
    }
}