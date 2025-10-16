<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentRequest;
use App\Models\OnsiteRequest;
use App\Events\QueuePlacementConfirmed;
use App\Events\RealTimeEvent;
use App\Services\QueueManagementService;
use App\Services\ReceiptPrintingService;
use App\Services\HybridPrintingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KioskController extends Controller
{
    public function index()
    {
        return view('kiosk.index');
    }

    public function confirm(Request $request)
    {
        $request->validate([
            'queue_number' => 'required|string'
        ]);

        $queueNumber = trim(strtoupper($request->queue_number));

        // Check StudentRequest first
        $studentRequest = StudentRequest::where('queue_number', $queueNumber)
            ->whereIn('status', ['ready_for_release', 'ready_for_pickup', 'completed', 'in_queue', 'waiting'])
            ->with(['student.user', 'requestItems.document'])
            ->first();

        if ($studentRequest) {
            $queuePosition = $this->calculateQueuePosition($queueNumber);
            $windowAssignment = $this->getWindowAssignment($studentRequest);
            
            return view('kiosk.confirm', [
                'request' => $studentRequest,
                'type' => 'student',
                'queuePosition' => $queuePosition,
                'windowAssignment' => $windowAssignment,
                'isReady' => $this->isQueueNumberReady($queueNumber)
            ]);
        }

        // Check OnsiteRequest
        $onsiteRequest = OnsiteRequest::where('queue_number', $queueNumber)
            ->whereIn('status', ['released', 'in_queue', 'ready_for_pickup', 'completed', 'waiting'])
            ->with(['student.user', 'requestItems.document'])
            ->first();

        if ($onsiteRequest) {
            $queuePosition = $this->calculateQueuePosition($queueNumber);
            $windowAssignment = $this->getWindowAssignment($onsiteRequest);
            
            return view('kiosk.confirm', [
                'request' => $onsiteRequest,
                'type' => 'onsite',
                'queuePosition' => $queuePosition,
                'windowAssignment' => $windowAssignment,
                'isReady' => $this->isQueueNumberReady($queueNumber)
            ]);
        }

        return back()->with('error', 'Invalid queue number or request not ready for pickup.');
    }

    private function calculateQueuePosition($queueNumber)
    {
        // First check if this is the current queue number being served
        $currentInQueue = StudentRequest::where('status', 'in_queue')->where('queue_number', $queueNumber)->first()
            ?: OnsiteRequest::where('status', 'in_queue')->where('queue_number', $queueNumber)->first();
        
        if ($currentInQueue) {
            return 1; // Currently being served
        }
        
        // Check if this queue number is waiting
        $waitingStudent = StudentRequest::where('status', 'waiting')->where('queue_number', $queueNumber)->first();
        $waitingOnsite = OnsiteRequest::where('status', 'waiting')->where('queue_number', $queueNumber)->first();
        
        if ($waitingStudent || $waitingOnsite) {
            // Count how many waiting requests are ahead of this one
            $currentRequest = $waitingStudent ?: $waitingOnsite;
            
            $studentsAhead = StudentRequest::where('status', 'waiting')
                ->where('updated_at', '<', $currentRequest->updated_at)
                ->count();
                
            $onsiteAhead = OnsiteRequest::where('status', 'waiting')
                ->where('updated_at', '<', $currentRequest->updated_at)
                ->count();
            
            // Add 1 if someone is currently being served (in_queue), plus the number ahead
            $currentlyServing = StudentRequest::where('status', 'in_queue')->exists() 
                || OnsiteRequest::where('status', 'in_queue')->exists() ? 1 : 0;
                
            return $currentlyServing + $studentsAhead + $onsiteAhead + 1;
        }
        
        // For other statuses (ready_for_release, ready_for_pickup, completed, released)
        return 'Ready';
    }

    private function getWindowAssignment($request)
    {
        // Simple window assignment based on queue number
        // You can customize this logic based on your requirements
        $queueNumber = $request->queue_number;
        if (!$queueNumber) return null;
        
        $letter = substr($queueNumber, 0, 1);
        $windowNumber = (ord($letter) - ord('A')) % 3 + 1; // Rotate between windows 1, 2, 3
        
        return $windowNumber;
    }

    private function isQueueNumberReady($queueNumber)
    {
        // Check if the queue number is currently in the "in_queue" status (being served at a window)
        $studentInQueue = StudentRequest::where('queue_number', $queueNumber)
            ->where('status', 'in_queue')
            ->exists();
            
        $onsiteInQueue = OnsiteRequest::where('queue_number', $queueNumber)
            ->where('status', 'in_queue')
            ->exists();
        
        // Check if the queue number is ready for pickup
        $studentReady = StudentRequest::where('queue_number', $queueNumber)
            ->where('status', 'ready_for_pickup')
            ->exists();
            
        $onsiteReady = OnsiteRequest::where('queue_number', $queueNumber)
            ->where('status', 'ready_for_pickup')
            ->exists();
        
        // Only return true if the request is actually being served or ready for pickup
        return $studentInQueue || $onsiteInQueue || $studentReady || $onsiteReady;
    }

    private function isQueueAvailable()
    {
        // Use database transaction to ensure atomic check
        return DB::transaction(function () {
            // Get all available registrars/windows
            $availableRegistrars = \App\Models\Registrar::whereHas('user.role', function($query) {
                $query->where('name', 'registrar');
            })->get();
            
            $totalAvailableWindows = $availableRegistrars->count();
            
            if ($totalAvailableWindows === 0) {
                return false; // No registrars available
            }
            
            // Check how many windows are currently occupied
            $occupiedWindows = 0;
            
            foreach ($availableRegistrars as $registrar) {
                // Check if this registrar has any active requests
                $hasActiveStudentRequest = \App\Models\StudentRequest::where('assigned_registrar_id', $registrar->user_id)
                    ->whereIn('status', ['in_queue', 'ready_for_pickup', 'processing'])
                    ->exists();
                    
                $hasActiveOnsiteRequest = \App\Models\OnsiteRequest::where('assigned_registrar_id', $registrar->user_id)
                    ->whereIn('status', ['in_queue', 'ready_for_pickup', 'processing'])
                    ->exists();
                    
                if ($hasActiveStudentRequest || $hasActiveOnsiteRequest) {
                    $occupiedWindows++;
                }
            }
            
            // Queue is available if not all windows are occupied
            $availableWindows = $totalAvailableWindows - $occupiedWindows;
            
            Log::info("Queue availability check: {$availableWindows} available windows out of {$totalAvailableWindows} total");
            
            return $availableWindows > 0;
        });
    }
    
    private function moveNextPersonToQueue()
    {
        // Only move people if there are available windows
        if (!$this->isQueueAvailable()) {
            Log::info("Not moving next person to queue - no available windows");
            return;
        }
        
        // Find the next waiting person and move them to in_queue
        $nextStudent = StudentRequest::where('status', 'waiting')->orderBy('updated_at', 'asc')->first();
        $nextOnsite = OnsiteRequest::where('status', 'waiting')->orderBy('updated_at', 'asc')->first();
        
        // Determine which request is older and should go first
        $nextRequest = null;
        $nextType = null;
        
        if ($nextStudent && $nextOnsite) {
            if ($nextStudent->updated_at <= $nextOnsite->updated_at) {
                $nextRequest = $nextStudent;
                $nextType = 'student';
            } else {
                $nextRequest = $nextOnsite;
                $nextType = 'onsite';
            }
        } elseif ($nextStudent) {
            $nextRequest = $nextStudent;
            $nextType = 'student';
        } elseif ($nextOnsite) {
            $nextRequest = $nextOnsite;
            $nextType = 'onsite';
        }
        
        if ($nextRequest) {
            $nextRequest->update([
                'status' => 'in_queue',
                'updated_at' => now(),
            ]);
            
            Log::info("Moved {$nextType} request {$nextRequest->id} from waiting to in_queue");
            
            // Fire queue update event for the person moved from waiting to in_queue
            event(new QueuePlacementConfirmed(
                $nextRequest, 
                $nextType, 
                'moved_to_active_queue',
                'You have been moved to the active queue. Please wait to be called to your assigned window.'
            ));
            
            // Send real-time notification about queue update
            $this->sendQueueUpdateNotification($nextRequest, $nextType);
        }
    }
    
    private function sendQueueUpdateNotification($request, $type)
    {
        // Fire real-time notification about queue update
        event(new RealTimeEvent(
            "Queue updated: Customer moved to active queue",
            'queue_update',
            [
                'request_id' => $request->id,
                'queue_number' => $request->queue_number,
                'type' => $type,
                'status' => $request->status,
                'timestamp' => now()->toISOString()
            ],
            ['queue-updates', 'registrar-notifications', 'queue-display-updates']
        ));
        
        Log::info("Queue updated: {$type} request #{$request->id} moved to in_queue");
    }

    public function confirmPickup(Request $request, $type, $id)
    {
        if ($type === 'student') {
            $studentRequest = StudentRequest::findOrFail($id);
            
            if ($studentRequest->status === 'ready_for_release') {
                // Student is confirming they're here for pickup
                $studentRequest->update([
                    'status' => 'ready_for_pickup',
                    'updated_at' => now(),
                ]);

                return view('kiosk.success', [
                    'request' => $studentRequest,
                    'type' => 'student',
                    'message' => 'Thank you for confirming your presence. Please wait to be called to your assigned window.',
                    'action' => 'confirmed'
                ]);
            } elseif ($studentRequest->status === 'ready_for_pickup') {
                // Student is at the window being served
                $studentRequest->update([
                    'status' => 'completed',
                    'updated_at' => now(),
                ]);

                return view('kiosk.success', [
                    'request' => $studentRequest,
                    'type' => 'student',
                    'message' => 'Documents successfully collected. Thank you!',
                    'action' => 'completed'
                ]);
            } elseif ($studentRequest->status === 'completed') {
                // Check if queue is available (only 1 person can be in_queue at a time)
                return DB::transaction(function () use ($studentRequest) {
                    // Double-check queue availability within transaction
                    if ($this->isQueueAvailable()) {
                        // Queue is available, place them in queue
                        $studentRequest->update([
                            'status' => 'in_queue',
                            'updated_at' => now(),
                        ]);

                        // Fire queue placement confirmed event
                        event(new QueuePlacementConfirmed(
                            $studentRequest, 
                            'student', 
                            'queue_placement_confirmed',
                            'Queue placement confirmed. Please wait to be called to your assigned window.'
                        ));

                        // Also sync with FastAPI if available
                        try {
                            $queueService = app(QueueManagementService::class);
                            $queueService->updateCustomerStatus($studentRequest->queue_api_id ?? 'local-' . $studentRequest->id, 'in_service');
                        } catch (\Exception $e) {
                            Log::warning('Failed to sync with FastAPI: ' . $e->getMessage());
                        }

                        return view('kiosk.success', [
                            'request' => $studentRequest,
                            'type' => 'student',
                            'message' => 'Queue placement confirmed. Please wait to be called to your assigned window.',
                            'action' => 'confirmed'
                        ]);
                    } else {
                        // Queue is occupied, put them in waiting
                        $studentRequest->update([
                            'status' => 'waiting',
                            'updated_at' => now(),
                        ]);

                        // Fire waiting queue event
                        event(new QueuePlacementConfirmed(
                            $studentRequest, 
                            'student', 
                            'waiting_queue_placement',
                            'Someone is currently being served. You are in the waiting queue and will be moved automatically when available.'
                        ));

                        // Immediately check if queue became available and move them if so
                        $this->moveNextPersonToQueue();

                        return view('kiosk.success', [
                            'request' => $studentRequest,
                            'type' => 'student',
                            'message' => 'Someone is currently being served. You are in the waiting queue and will be moved automatically when available.',
                            'action' => 'waiting'
                        ]);
                    }
                });
            } else {
                return redirect()->route('kiosk.index')->with('error', 'Request is not ready for pickup.');
            }
        } elseif ($type === 'onsite') {
            $onsiteRequest = OnsiteRequest::findOrFail($id);
            
            if ($onsiteRequest->status === 'completed') {
                // Check if queue is available (only 1 person can be in_queue at a time)
                return DB::transaction(function () use ($onsiteRequest) {
                    // Double-check queue availability within transaction
                    if ($this->isQueueAvailable()) {
                        // Queue is available, place them in queue
                        $onsiteRequest->update([
                            'status' => 'in_queue',
                            'updated_at' => now(),
                        ]);

                        // Fire queue placement confirmed event
                        event(new QueuePlacementConfirmed(
                            $onsiteRequest, 
                            'onsite', 
                            'queue_placement_confirmed',
                            'Queue placement confirmed. Please wait to be called to your assigned window.'
                        ));

                        // Also sync with FastAPI if available
                        try {
                            $queueService = app(QueueManagementService::class);
                            $queueService->updateCustomerStatus($onsiteRequest->queue_api_id ?? 'local-' . $onsiteRequest->id, 'in_service');
                        } catch (\Exception $e) {
                            Log::warning('Failed to sync with FastAPI: ' . $e->getMessage());
                        }

                        return view('kiosk.success', [
                            'request' => $onsiteRequest,
                            'type' => 'onsite',
                            'message' => 'Queue placement confirmed. Please wait to be called to your assigned window.',
                            'action' => 'confirmed'
                        ]);
                    } else {
                        // Queue is occupied, put them in waiting
                        $onsiteRequest->update([
                            'status' => 'waiting',
                            'updated_at' => now(),
                        ]);

                        // Fire waiting queue event
                        event(new QueuePlacementConfirmed(
                            $onsiteRequest, 
                            'onsite', 
                            'waiting_queue_placement',
                            'Someone is currently being served. You are in the waiting queue and will be moved automatically when available.'
                        ));

                        return view('kiosk.success', [
                            'request' => $onsiteRequest,
                            'type' => 'onsite',
                            'message' => 'Someone is currently being served. You are in the waiting queue and will be moved automatically when available.',
                            'action' => 'waiting'
                        ]);
                    }
                });
            } elseif ($onsiteRequest->status === 'released') {
                // Check if queue is available
                return DB::transaction(function () use ($onsiteRequest) {
                    // Double-check queue availability within transaction  
                    if ($this->isQueueAvailable()) {
                        // Queue is available, place them in queue
                        $onsiteRequest->update([
                            'status' => 'in_queue',
                            'updated_at' => now(),
                        ]);

                        // Fire queue placement confirmed event
                        event(new QueuePlacementConfirmed(
                            $onsiteRequest, 
                            'onsite', 
                            'queue_placement_confirmed',
                            'Thank you for confirming your presence. Please wait to be called to your assigned window.'
                        ));

                        return view('kiosk.success', [
                            'request' => $onsiteRequest,
                            'type' => 'onsite',
                            'message' => 'Thank you for confirming your presence. Please wait to be called to your assigned window.',
                            'action' => 'confirmed'
                        ]);
                    } else {
                        // Queue is occupied, put them in waiting
                        $onsiteRequest->update([
                            'status' => 'waiting',
                            'updated_at' => now(),
                        ]);

                        // Fire waiting queue event
                        event(new QueuePlacementConfirmed(
                            $onsiteRequest, 
                            'onsite', 
                            'waiting_queue_placement',
                            'Someone is currently being served. You are in the waiting queue and will be moved automatically when available.'
                        ));

                        // Immediately check if queue became available and move them if so
                        $this->moveNextPersonToQueue();

                        return view('kiosk.success', [
                            'request' => $onsiteRequest,
                            'type' => 'onsite',
                            'message' => 'Someone is currently being served. You are in the waiting queue and will be moved automatically when available.',
                            'action' => 'waiting'
                        ]);
                    }
                });
            } elseif ($onsiteRequest->status === 'in_queue') {
                // Student confirms again - change to ready_for_pickup 
                $onsiteRequest->update([
                    'status' => 'ready_for_pickup',
                    'updated_at' => now(),
                ]);

                // Fire ready for pickup event
                event(new QueuePlacementConfirmed(
                    $onsiteRequest, 
                    'onsite', 
                    'ready_for_pickup_confirmed',
                    'Ready for pickup confirmed. Please wait to be called to your assigned window.'
                ));

                // Move next person from waiting to in_queue
                $this->moveNextPersonToQueue();

                return view('kiosk.success', [
                    'request' => $onsiteRequest,
                    'type' => 'onsite',
                    'message' => 'Ready for pickup confirmed. Please wait to be called to your assigned window.',
                    'action' => 'confirmed'
                ]);
            } elseif ($onsiteRequest->status === 'ready_for_pickup') {
                return view('kiosk.success', [
                    'request' => $onsiteRequest,
                    'type' => 'onsite',
                    'message' => 'You are already in the ready for pickup queue. Please wait to be called.',
                    'action' => 'already_ready'
                ]);
            } elseif ($onsiteRequest->status === 'waiting') {
                return view('kiosk.success', [
                    'request' => $onsiteRequest,
                    'type' => 'onsite',
                    'message' => 'You are in the waiting queue. You will be moved automatically when the current person is served.',
                    'action' => 'waiting'
                ]);
            } else {
                return redirect()->route('kiosk.index')->with('error', 'Request is not ready for pickup.');
            }
        }

        return redirect()->route('kiosk.index')->with('error', 'Invalid request type.');
    }

    public function queueStatus(Request $request)
    {
        $queueNumber = $request->get('queue_number');
        
        if (!$queueNumber) {
            return response()->json(['error' => 'Queue number required'], 400);
        }

        $queueNumber = trim(strtoupper($queueNumber));
        
        // Find the request
        $studentRequest = StudentRequest::where('queue_number', $queueNumber)
            ->whereIn('status', ['ready_for_release', 'ready_for_pickup', 'in_queue', 'waiting'])
            ->first();
            
        $onsiteRequest = OnsiteRequest::where('queue_number', $queueNumber)
            ->whereIn('status', ['released', 'in_queue', 'ready_for_pickup', 'waiting'])
            ->first();
            
        $requestFound = $studentRequest ?: $onsiteRequest;
        
        if (!$requestFound) {
            return response()->json(['error' => 'Queue number not found'], 404);
        }
        
        $queuePosition = $this->calculateQueuePosition($queueNumber);
        $windowAssignment = $this->getWindowAssignment($requestFound);
        $isReady = $this->isQueueNumberReady($queueNumber);
        
        return response()->json([
            'queue_number' => $queueNumber,
            'position' => $queuePosition,
            'window' => $windowAssignment,
            'is_ready' => $isReady,
            'status' => $isReady ? 'ready' : 'waiting'
        ]);
    }

    /**
     * Print receipt for confirmed queue
     */
    public function printReceipt(Request $request, $type, $id)
    {
        try {
            $hybridPrintingService = new HybridPrintingService(new ReceiptPrintingService());
            
            if ($type === 'student') {
                $requestModel = StudentRequest::with(['student.user', 'requestItems.document'])->findOrFail($id);
            } elseif ($type === 'onsite') {
                $requestModel = OnsiteRequest::with(['requestItems.document'])->findOrFail($id);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid request type'
                ], 400);
            }

            // Check if request is in a printable state
            $printableStatuses = ['completed', 'in_queue', 'ready_for_pickup', 'waiting'];
            if (!in_array($requestModel->status, $printableStatuses)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Request is not in a printable state'
                ], 400);
            }

            // Use hybrid printing service
            $result = $hybridPrintingService->handlePrintRequest($requestModel, $type);
            
            // Log the printing attempt
            Log::info("Receipt print requested via hybrid service", [
                'type' => $type,
                'request_id' => $id,
                'queue_number' => $requestModel->queue_number,
                'status' => $requestModel->status,
                'success' => $result['success'],
                'method' => $result['method'] ?? 'unknown'
            ]);

            return response()->json($result);
            
        } catch (\Exception $e) {
            Log::error("Hybrid receipt printing error: " . $e->getMessage(), [
                'type' => $type,
                'request_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to process print request: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test printer connection
     */
    public function testPrinter(Request $request)
    {
        try {
            $receiptService = new ReceiptPrintingService();
            
            // Allow custom printer name for testing
            if ($request->has('printer_name')) {
                $receiptService->setPrinterName($request->printer_name);
            }
            
            $result = $receiptService->testPrinter();
            
            Log::info("Printer test requested", [
                'result' => $result,
                'printer_name' => $request->printer_name ?? 'default'
            ]);
            
            return response()->json($result);
            
        } catch (\Exception $e) {
            Log::error("Printer test error: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Printer test failed: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
