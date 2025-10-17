<?php

namespace App\Http\Controllers;

use App\Models\OnsiteRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Services\RealTimeNotificationService;
use App\Mail\RequestRejectedMail;

class RegistrarOnsiteController extends Controller
{
    protected $notificationService;

    public function __construct(RealTimeNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index(Request $request)
    {
        // Get the current registrar's information
        $currentUser = Auth::user();
        $currentRegistrar = $currentUser->registrar;
        
        if (!$currentRegistrar) {
            return redirect()->back()->with('error', 'Access denied. You are not assigned as a registrar.');
        }
        
        $currentWindowNumber = $currentRegistrar->window_number;
        
        // Determine which route was accessed to filter appropriately
        $routeName = $request->route()->getName();
        
        $query = OnsiteRequest::with('requestItems.document', 'window', 'feedback', 'registrar');
        
        // Find the window ID that corresponds to this registrar's window number
        $assignedWindow = \App\Models\Window::where('name', 'Window ' . $currentWindowNumber)->first();
        
        // Check if the registrar currently has any active requests (excluding completed ones)
        $isWindowOccupied = false;
        $currentRequest = null;
        
        // Check if this registrar has any active onsite request assigned to them
        $currentOnsiteRequest = OnsiteRequest::where('assigned_registrar_id', $currentUser->id)
            ->whereIn('status', ['registrar_approved', 'processing', 'in_queue', 'ready_for_pickup'])
            ->whereNotIn('status', ['completed', 'released']) // Exclude completed requests
            ->first();
            
        // Also check if this registrar has any active student request assigned to them
        $currentStudentRequest = \App\Models\StudentRequest::where('assigned_registrar_id', $currentUser->id)
            ->whereIn('status', ['registrar_approved', 'processing', 'in_queue', 'ready_for_pickup'])
            ->whereNotIn('status', ['completed', 'released']) // Exclude completed requests
            ->first();
        
        $currentRequest = $currentOnsiteRequest ?: $currentStudentRequest;
        $isWindowOccupied = $currentRequest !== null;
        
        // Check if registrar has any onsite requests in progress (to prevent concurrent onsite processing)
        $hasActiveOnsiteRequest = OnsiteRequest::where('assigned_registrar_id', $currentUser->id)
            ->whereIn('status', ['pending', 'registrar_approved', 'processing', 'ready_for_release'])
            ->whereNotIn('status', ['completed', 'released'])
            ->exists();
        
        // Show requests based on route, allowing both occupied and available windows to see pending requests
        if ($routeName === 'registrar.onsite.pending') {
            // Show pending requests (not yet assigned) and requests assigned to this registrar
            $query->where(function($q) use ($currentUser, $assignedWindow) {
                $q->where(function($subQ) {
                    // Show all pending requests that need approval (not assigned to any registrar yet)
                    $subQ->where('status', 'pending')
                         ->whereNull('assigned_registrar_id')
                         ->whereNull('window_id');
                })
                ->orWhere(function($subQ) {
                    // Show waiting requests that can be taken by available registrars
                    $subQ->where('status', 'waiting')
                         ->whereNull('assigned_registrar_id')
                         ->whereNull('window_id');
                })
                ->orWhere(function($subQ) use ($currentUser, $assignedWindow) {
                    // Show requests already assigned to this registrar/window
                    $subQ->whereIn('status', ['registrar_approved', 'processing', 'in_queue', 'ready_for_pickup'])
                         ->where('assigned_registrar_id', $currentUser->id);
                });
            });
        } elseif ($routeName === 'registrar.onsite.completed') {
            $query->where('status', 'completed')
                 ->where('assigned_registrar_id', $currentUser->id);
        } else {
            // For 'registrar.onsite.management', show all requests
            $query->where(function($q) use ($currentUser, $assignedWindow) {
                $q->where(function($subQ) {
                    // Show all pending requests that need approval
                    $subQ->where('status', 'pending')
                         ->whereNull('assigned_registrar_id')
                         ->whereNull('window_id');
                })
                ->orWhere(function($subQ) {
                    // Show waiting requests that can be taken by available registrars
                    $subQ->where('status', 'waiting')
                         ->whereNull('assigned_registrar_id')
                         ->whereNull('window_id');
                })
                ->orWhere(function($subQ) use ($currentUser, $assignedWindow) {
                    // Show requests assigned to this registrar/window
                    if ($assignedWindow) {
                        $subQ->where('window_id', $assignedWindow->id)
                             ->orWhere('assigned_registrar_id', $currentUser->id);
                    } else {
                        $subQ->where('assigned_registrar_id', $currentUser->id);
                    }
                });
            });
        }
        
        $requests = $query->orderByDesc('created_at')->paginate(10);
        
        // Auto-promote waiting requests if window is available
        if (!$isWindowOccupied) {
            // First, check for waiting requests already assigned to this registrar
            $waitingRequestsForThisRegistrar = OnsiteRequest::where('assigned_registrar_id', $currentUser->id)
                ->where('status', 'waiting')
                ->orderBy('created_at', 'asc')
                ->first(); // Get the oldest waiting request
                
            if ($waitingRequestsForThisRegistrar) {
                $waitingRequestsForThisRegistrar->update([
                    'status' => 'in_queue',
                    'current_step' => 'processing'
                ]);
                
                // Broadcast real-time notification
                $this->notificationService->sendRequestStatusUpdate(
                    $waitingRequestsForThisRegistrar->ref_code,
                    'in_queue',
                    "Your request has been moved to active queue and will be processed shortly",
                    [
                        'student_name' => $waitingRequestsForThisRegistrar->full_name,
                        'document_type' => $waitingRequestsForThisRegistrar->requestItems->pluck('document.type_document')->join(', '),
                        'registrar_name' => Auth::user()->first_name . ' ' . Auth::user()->last_name,
                        'request_type' => 'onsite',
                        'window_assignment' => $assignedWindow ? $assignedWindow->name : null,
                    ]
                );
            } else {
                // If no waiting requests for this registrar, check for waiting requests assigned to busy registrars
                // and reassign the oldest one to this available registrar
                $oldestWaitingRequest = OnsiteRequest::where('status', 'waiting')
                    ->whereNotNull('assigned_registrar_id') // Only check assigned requests
                    ->orderBy('created_at', 'asc')
                    ->first();
                    
                if ($oldestWaitingRequest) {
                    // Check if the assigned registrar is busy
                    $assignedRegistrarId = $oldestWaitingRequest->assigned_registrar_id;
                    
                    // Check if assigned registrar is busy with onsite requests
                    $registrarBusyWithOnsite = OnsiteRequest::where('assigned_registrar_id', $assignedRegistrarId)
                        ->whereIn('status', ['registrar_approved', 'processing', 'in_queue', 'ready_for_pickup'])
                        ->where('id', '!=', $oldestWaitingRequest->id)
                        ->exists();
                        
                    // Check if assigned registrar is busy with student requests
                    $registrarBusyWithStudent = \App\Models\StudentRequest::where('assigned_registrar_id', $assignedRegistrarId)
                        ->whereIn('status', ['registrar_approved', 'processing', 'in_queue', 'ready_for_pickup'])
                        ->exists();
                    
                    if ($registrarBusyWithOnsite || $registrarBusyWithStudent) {
                        // Reassign to current available registrar
                        $oldestWaitingRequest->update([
                            'assigned_registrar_id' => $currentUser->id,
                            'window_id' => $assignedWindow ? $assignedWindow->id : null,
                            'status' => 'in_queue',
                            'current_step' => 'processing'
                        ]);
                        
                        // Broadcast real-time notification
                        $this->notificationService->sendRequestStatusUpdate(
                            $oldestWaitingRequest->ref_code,
                            'in_queue',
                            "Your request has been reassigned to an available registrar and will be processed shortly",
                            [
                                'student_name' => $oldestWaitingRequest->full_name,
                                'document_type' => $oldestWaitingRequest->requestItems->pluck('document.type_document')->join(', '),
                                'registrar_name' => Auth::user()->first_name . ' ' . Auth::user()->last_name,
                                'request_type' => 'onsite',
                                'window_assignment' => $assignedWindow ? $assignedWindow->name : null,
                            ]
                        );
                    }
                }
            }
            
            // Refresh the query to show updated data if any changes were made
            $requests = $query->orderByDesc('created_at')->paginate(10);
        }
        
        // Apply queue categorization logic for display purposes
        // Group requests by registrar and mark subsequent requests as "waiting" for display
        $requests->getCollection()->transform(function ($request) use ($currentUser) {
            // Only apply this logic to requests assigned to the current registrar
            if ($request->assigned_registrar_id === $currentUser->id) {
                // Get all ACTIVE requests assigned to this registrar (exclude ready_for_pickup since they're completed)
                $registrarRequests = OnsiteRequest::where('assigned_registrar_id', $currentUser->id)
                    ->whereIn('status', ['registrar_approved', 'processing', 'in_queue'])
                    ->whereNotIn('status', ['completed', 'released', 'ready_for_pickup'])
                    ->orderBy('created_at', 'asc')
                    ->pluck('id')
                    ->toArray();
                
                // Find the position of this request in the registrar's queue
                $position = array_search($request->id, $registrarRequests);
                
                // If this is not the first request (position > 0), mark it as "waiting" for display
                if ($position > 0) {
                    $request->display_status = 'waiting';
                    $request->display_status_label = 'Waiting for registrar';
                } else {
                    $request->display_status = $request->status;
                    $request->display_status_label = ucfirst(str_replace('_', ' ', $request->status));
                }
            } else {
                $request->display_status = $request->status;
                $request->display_status_label = ucfirst(str_replace('_', ' ', $request->status));
            }
            
            return $request;
        });
        
        // Pass additional context to the view
        $viewData = [
            'requests' => $requests,
            'isWindowOccupied' => $isWindowOccupied,
            'currentRequest' => $currentRequest,
            'currentRequestType' => $currentOnsiteRequest ? 'onsite' : ($currentStudentRequest ? 'student' : null),
            'windowNumber' => $currentWindowNumber,
            'assignedWindow' => $assignedWindow,
            'hasActiveOnsiteRequest' => $hasActiveOnsiteRequest
        ];

        return view('registrar.onsite.index', $viewData);
    }

    /**
     * Approve a pending or waiting onsite request by registrar before forwarding to accounting
     */
    public function approveRequest(Request $request, OnsiteRequest $onsiteRequest)
    {
        if (!in_array($onsiteRequest->status, ['pending', 'waiting', 'ready_for_pickup'])) {
            return redirect()->back()->with('error', 'Only pending, waiting, or ready for pickup requests can be approved.');
        }

        // Get current registrar's window information
        $currentUser = Auth::user();
        $currentRegistrar = $currentUser->registrar;
        
        if (!$currentRegistrar) {
            return redirect()->back()->with('error', 'Access denied. You are not assigned as a registrar.');
        }
        
        // Find the registrar's assigned window
        $assignedWindow = \App\Models\Window::where('name', 'Window ' . $currentRegistrar->window_number)->first();
        
        // Check if window is currently occupied
        if ($assignedWindow) {
            $occupiedRequest = OnsiteRequest::where('window_id', $assignedWindow->id)
                ->where('assigned_registrar_id', $currentUser->id)
                ->whereIn('status', ['processing', 'in_queue', 'ready_for_pickup'])
                ->whereIn('current_step', ['processing', 'window'])
                ->first();
                
            if ($occupiedRequest && $occupiedRequest->id !== $onsiteRequest->id) {
                return redirect()->back()->with('error', 'Your window is currently occupied. Please complete the current request first.');
            }
        }

        $onsiteRequest->update([
            'status' => 'registrar_approved',
            'registrar_approved' => true,
            'approved_by_registrar_id' => Auth::id(),
            'registrar_approved_at' => now(),
            'remarks' => $request->input('remarks'),
            'current_step' => 'payment', // Now allow payment
            'updated_at' => now(),
            // Assign to this registrar's window if available
            'window_id' => $assignedWindow ? $assignedWindow->id : null,
            'assigned_registrar_id' => $currentUser->id,
        ]);

        // Mark window as occupied if assigned
        if ($assignedWindow) {
            $assignedWindow->update(['is_occupied' => true]);
        }

        // Broadcast real-time notification
        $this->notificationService->sendRequestStatusUpdate(
            $onsiteRequest->ref_code,
            'registrar_approved',
            "Onsite request #{$onsiteRequest->ref_code} has been approved by registrar and is ready for accounting review",
            [
                'student_name' => $onsiteRequest->full_name,
                'document_type' => $onsiteRequest->requestItems->first()->document->type_document ?? 'Document',
                'registrar_name' => Auth::user()->first_name . ' ' . Auth::user()->last_name,
                'window_assignment' => $assignedWindow ? $assignedWindow->name : null,
            ]
        );

        return redirect()->back()->with('success', 'Request approved by registrar and assigned to your window. Forwarded to accounting for payment processing.');
    }

    /**
     * Reject an onsite request and send it back to student timeline for re-approval
     */
    public function rejectRequest(Request $request, OnsiteRequest $onsiteRequest)
    {
        if (!in_array($onsiteRequest->status, ['pending', 'waiting'])) {
            return redirect()->back()->with('error', 'Only pending or waiting onsite requests can be rejected.');
        }

        // Get current registrar
        $currentUser = Auth::user();
        $currentRegistrar = $currentUser->registrar;

        if (!$currentRegistrar) {
            return redirect()->back()->with('error', 'Access denied. You are not assigned as a registrar.');
        }

        // Reset the onsite request to allow student re-approval from timeline
        $onsiteRequest->update([
            'status' => 'pending',
            'current_step' => 'start', // Reset to initial step
            'registrar_approved' => false,
            'approved_by_registrar_id' => null,
            'registrar_approved_at' => null,
            'remarks' => $request->input('remarks', 'Request rejected by registrar - please review and re-submit'),
            'window_id' => null,
            'assigned_registrar_id' => null,
            'updated_at' => now(),
        ]);

        // Broadcast real-time notification to student
        $this->notificationService->sendRequestStatusUpdate(
            $onsiteRequest->ref_code,
            'rejected',
            "Onsite request #{$onsiteRequest->ref_code} has been rejected by registrar. Please review and re-approve from your timeline.",
            [
                'student_name' => $onsiteRequest->full_name,
                'document_type' => $onsiteRequest->requestItems->first()->document->type_document ?? 'Document',
                'registrar_name' => $currentUser->first_name . ' ' . $currentUser->last_name,
                'reason' => $request->input('remarks', 'Request rejected - please review and re-submit')
            ]
        );

        // Send email notification if student has an email
        $email = null;
        if ($onsiteRequest->student && $onsiteRequest->student->user) {
            $email = $onsiteRequest->student->user->personal_email ?? $onsiteRequest->student->user->school_email;
        } elseif ($onsiteRequest->email) {
            $email = $onsiteRequest->email;
        }

        $remarks = $request->input('remarks');
        if (!$remarks || trim($remarks) === '') {
            $remarks = 'Request rejected by registrar - please review and re-submit';
        }

        if ($email) {
            try {
                Mail::to($email)->send(new RequestRejectedMail($onsiteRequest, 'onsite', $remarks));
            } catch (\Exception $e) {
                // Log the error but don't fail the request
                Log::error('Failed to send onsite rejection email: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('success', 'Onsite request rejected and sent back to student timeline for re-approval.');
    }

    /**
     * Handle registrar taking control of a request (when processing starts)
     */
    public function takeRequest(Request $request, OnsiteRequest $onsiteRequest)
    {
        $currentUser = Auth::user();
        $currentRegistrar = $currentUser->registrar;
        
        if (!$currentRegistrar) {
            return redirect()->back()->with('error', 'Access denied. You are not assigned as a registrar.');
        }
        
        // Verify the request can be taken (pending, waiting, or ready_for_pickup status)
        if (!in_array($onsiteRequest->status, ['pending', 'waiting', 'ready_for_pickup'])) {
            return redirect()->back()->with('error', 'Only pending, waiting, or ready for pickup requests can be taken.');
        }
        
        // Find the registrar's assigned window
        $assignedWindow = \App\Models\Window::where('name', 'Window ' . $currentRegistrar->window_number)->first();
        
        // Check if this registrar already has an active request
        $hasActiveRequest = OnsiteRequest::where('assigned_registrar_id', $currentUser->id)
            ->whereIn('status', ['processing', 'in_queue'])
            ->where('id', '!=', $onsiteRequest->id)
            ->exists();
        
        // Check if window is currently occupied
        if ($assignedWindow) {
            $occupiedRequest = OnsiteRequest::where('window_id', $assignedWindow->id)
                ->where('assigned_registrar_id', $currentUser->id)
                ->whereIn('status', ['processing', 'in_queue', 'ready_for_pickup'])
                ->whereNotIn('status', ['completed'])
                ->first();
                
            if ($occupiedRequest && $occupiedRequest->id !== $onsiteRequest->id) {
                return redirect()->back()->with('error', 'Your window is currently occupied. Please complete the current request first.');
            }
        }

        // Assign request to this registrar and window
        // Handle ready_for_pickup requests differently - keep the status but assign to registrar
        if ($onsiteRequest->status === 'ready_for_pickup') {
            $onsiteRequest->update([
                'assigned_registrar_id' => $currentUser->id,
                'window_id' => $assignedWindow ? $assignedWindow->id : null,
                'remarks' => $request->input('remarks'),
            ]);
        } else {
            $updateData = [
                'assigned_registrar_id' => $currentUser->id,
                'window_id' => $assignedWindow ? $assignedWindow->id : null,
                'current_step' => 'processing',
                'remarks' => $request->input('remarks'),
            ];

            // If registrar has active request, set this one to waiting
            if ($hasActiveRequest) {
                $updateData['status'] = 'waiting';
                $updateData['current_step'] = 'waiting';
            } else {
                $updateData['status'] = 'in_queue';
            }

            $onsiteRequest->update($updateData);
        }

        // Mark window as occupied
        if ($assignedWindow) {
            $assignedWindow->update(['is_occupied' => true]);
        }

        // Broadcast real-time notification
        $this->notificationService->sendRequestStatusUpdate(
            $onsiteRequest->ref_code,
            'in_queue',
            "Onsite request {$onsiteRequest->ref_code} is now in queue",
            [
                'student_name' => $onsiteRequest->full_name,
                'document_type' => $onsiteRequest->requestItems->pluck('document.type_document')->join(', '),
                'registrar_name' => Auth::user()->first_name . ' ' . Auth::user()->last_name,
                'request_type' => 'onsite',
                'window_assignment' => $assignedWindow ? $assignedWindow->name : null,
            ]
        );

        return redirect()->back()->with('success', 'Request assigned to your window and moved to queue.');
    }
    
    /**
     * Mark a request as ready for pickup
     */
    public function readyForPickup(OnsiteRequest $onsiteRequest)
    {
        $currentUser = Auth::user();
        
        // Verify this registrar owns this request
        if ($onsiteRequest->assigned_registrar_id !== $currentUser->id) {
            return redirect()->back()->with('error', 'You can only mark requests assigned to you as ready for pickup.');
        }
        
        // Verify the request is in the correct status
        if ($onsiteRequest->status !== 'in_queue') {
            return redirect()->back()->with('error', 'Only in-queue requests can be marked as ready for pickup.');
        }

        // Update request status
        $onsiteRequest->update([
            'status' => 'ready_for_pickup',
            'current_step' => 'release',
        ]);

        // Promote any waiting requests for this registrar to in_queue
        $queueService = app(\App\Services\QueueService::class);
        $queueService->promoteWaitingRequestsForRegistrar($currentUser->id);

        // Broadcast real-time notification
        $this->notificationService->sendRequestStatusUpdate(
            $onsiteRequest->ref_code,
            'ready_for_pickup',
            "Onsite request {$onsiteRequest->ref_code} is ready for pickup",
            [
                'student_name' => $onsiteRequest->full_name,
                'document_type' => $onsiteRequest->requestItems->pluck('document.type_document')->join(', '),
                'registrar_name' => Auth::user()->first_name . ' ' . Auth::user()->last_name,
                'request_type' => 'onsite',
                'queue_number' => $onsiteRequest->queue_number,
            ]
        );

        return redirect()->back()->with('success', 'Request marked as ready for pickup.');
    }
    
    /**
     * Complete a request and free up the window
     */
    public function completeRequest(OnsiteRequest $onsiteRequest)
    {
        $currentUser = Auth::user();
        
        // Verify this registrar owns this request
        if ($onsiteRequest->assigned_registrar_id !== $currentUser->id) {
            return redirect()->back()->with('error', 'You can only complete requests assigned to you.');
        }

        // Use QueueService to complete the request and free the window
        $queueService = app(\App\Services\QueueService::class);
        $queueService->completeRequest($onsiteRequest);

        // Update additional fields
        $onsiteRequest->update([
            'status' => 'completed',
        ]);

        // Ensure windows are freed for any other completed requests
        $queueService->freeWindowsForCompletedRequests();

        // Auto-promote next waiting request assigned to this registrar
        $nextWaitingRequest = OnsiteRequest::where('assigned_registrar_id', $currentUser->id)
            ->where('status', 'waiting')
            ->orderBy('updated_at', 'asc')
            ->first();
            
        if ($nextWaitingRequest) {
            $nextWaitingRequest->update([
                'status' => 'in_queue',
                'current_step' => 'processing'
            ]);
            
            // Get the window for notification
            $window = $onsiteRequest->window;
            
            // Broadcast notification for the promoted request
            $this->notificationService->sendRequestStatusUpdate(
                $nextWaitingRequest->ref_code,
                'in_queue',
                "Your request has been moved to active queue and will be processed shortly",
                [
                    'student_name' => $nextWaitingRequest->full_name,
                    'document_type' => $nextWaitingRequest->requestItems->pluck('document.type_document')->join(', '),
                    'registrar_name' => Auth::user()->first_name . ' ' . Auth::user()->last_name,
                    'request_type' => 'onsite',
                    'window_assignment' => $window ? $window->name : null,
                ]
            );
        }

        // Broadcast real-time notification
        $this->notificationService->sendRequestStatusUpdate(
            $onsiteRequest->ref_code,
            'completed',
            "Onsite request {$onsiteRequest->ref_code} has been completed",
            [
                'student_name' => $onsiteRequest->full_name,
                'document_type' => $onsiteRequest->requestItems->pluck('document.type_document')->join(', '),
                'registrar_name' => Auth::user()->first_name . ' ' . Auth::user()->last_name,
                'request_type' => 'onsite',
                'window_freed' => true,
            ]
        );

        return redirect()->back()->with('success', 'Request completed successfully. Your window is now available for new requests.');
    }

}
