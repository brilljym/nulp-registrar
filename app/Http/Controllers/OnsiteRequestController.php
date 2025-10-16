<?php

namespace App\Http\Controllers;

use App\Models\OnsiteRequestItem;
use App\Services\RealTimeNotificationService;
use App\Services\QueueService;
use App\Models\OnsiteRequest;
use App\Models\Window;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Models\Document;
use App\Models\Student;
use App\Models\User;
use App\Mail\ExpectedReleaseDateUpdatedMail;
use App\Mail\RequestReadyForReleaseMail;
use App\Mail\RequestCompletedMail;

class OnsiteRequestController extends Controller
{
    protected $notificationService;
    protected $queueService;

    public function __construct(RealTimeNotificationService $notificationService, QueueService $queueService)
    {
        $this->notificationService = $notificationService;
        $this->queueService = $queueService;
    }
    public function index()
    {
        $documents = Document::all(); // Fetch all documents
        return view('onsite.index', compact('documents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id'   => 'nullable|string|max:50',
            'full_name'    => 'required|string|max:255',
            'course'       => 'required|string|max:100',
            'year_level'   => 'required|string|max:50',
            'department'   => 'required|string|max:100',
            'documents'    => 'required|array|min:1',
            'documents.*.document_id' => 'required|exists:documents,id',
            'documents.*.quantity' => 'required|integer|min:1|max:150',
            'reason'       => 'nullable|string|max:1000',
        ]);

        // Start with start step, will move to payment after registrar approval
        $validated['current_step'] = 'start';

        // Generate a random reference code (e.g., 8 alphanumeric chars)
        $validated['ref_code'] = strtoupper('NU' . bin2hex(random_bytes(3)));

        // Generate queue number
        $validated['queue_number'] = $this->queueService->generateQueueNumber();

        // Resolve student identifier to database ID
        $studentFk = null;
        if (!empty($validated['student_id'])) {
            if (is_numeric($validated['student_id'])) {
                $studentFk = (int) $validated['student_id'];
            } else {
                $student = Student::where('student_id', $validated['student_id'])->first();
                if ($student) {
                    $studentFk = $student->id;
                }
            }
        }
        $validated['student_id'] = $studentFk;

        // Remove documents from validated data as we'll handle them separately
        $documents = $validated['documents'];
        unset($validated['documents']);

        $onsiteRequest = OnsiteRequest::create($validated);

        // Create request items for each document
        foreach ($documents as $docData) {
            OnsiteRequestItem::create([
                'onsite_request_id' => $onsiteRequest->id,
                'document_id' => $docData['document_id'],
                'quantity' => $docData['quantity'],
            ]);
        }

        // Mark window as occupied if it was assigned
        if (isset($validated['window_id'])) {
            \App\Models\Window::where('id', $validated['window_id'])->update(['is_occupied' => true]);
        }

        // Send real-time notification to registrars about new onsite request
        $totalQuantity = array_sum(array_column($documents, 'quantity'));
        
        // Load the requestItems relationship
        $onsiteRequest->load('requestItems.document');
        
        $documentNames = $onsiteRequest->requestItems->map(function($item) {
            return $item->document->type_document . ' (x' . $item->quantity . ')';
        })->join(', ');

        Log::info('Sending real-time notification for new onsite request', [
            'request_id' => $onsiteRequest->id,
            'ref_code' => $onsiteRequest->ref_code,
            'student_name' => $onsiteRequest->full_name
        ]);

        $this->notificationService->sendNotification(
            "New onsite request submitted: {$onsiteRequest->ref_code}",
            'new-request',
            [
                'request_id' => $onsiteRequest->id,
                'ref_code' => $onsiteRequest->ref_code,
                'student_name' => $onsiteRequest->full_name,
                'document_type' => $documentNames,
                'course' => $onsiteRequest->course,
                'year_level' => $onsiteRequest->year_level,
                'department' => $onsiteRequest->department,
                'quantity' => $totalQuantity,
                'current_step' => $onsiteRequest->current_step,
                'window_id' => $onsiteRequest->window_id,
                'created_at' => $onsiteRequest->created_at->toISOString(),
                'request_type' => 'onsite',
                'price' => $onsiteRequest->requestItems->sum(function($item) {
                    return $item->document->price * $item->quantity;
                })
            ],
            ['registrar-notifications', 'new-onsite-requests', 'accounting-notifications']
        );

        return redirect()->route('onsite.timeline', $onsiteRequest->id);
    }

    public function update(Request $request, OnsiteRequest $onsiteRequest)
    {
        $validated = $request->validate([
            'student_id'   => 'nullable|string|max:50',
            'full_name'    => 'required|string|max:255',
            'course'       => 'required|string|max:100',
            'year_level'   => 'required|string|max:50',
            'department'   => 'required|string|max:100',
            'documents'    => 'required|array|min:1',
            'documents.*.document_id' => 'required|exists:documents,id',
            'documents.*.quantity' => 'required|integer|min:1|max:150',
            'reason'       => 'nullable|string|max:1000',
        ]);

        // Resolve student identifier (string like '2025-000001') to students.id (unsignedBigInteger FK)
        $studentFk = null;
        if (!empty($validated['student_id'])) {
            // If user submitted numeric id (primary key), accept it
            if (is_numeric($validated['student_id'])) {
                $studentFk = (int) $validated['student_id'];
            } else {
                // Try to find student by student_id code
                $student = Student::where('student_id', $validated['student_id'])->first();
                if ($student) {
                    $studentFk = $student->id;
                }
            }
        }

        // Overwrite student_id with FK (or null). Keep full_name separate.
        $validated['student_id'] = $studentFk;

        // Remove documents from validated data as we'll handle them separately
        $documents = $validated['documents'];
        unset($validated['documents']);

        // Update the onsite request
        $onsiteRequest->update($validated);

        // Delete existing request items and create new ones
        $onsiteRequest->requestItems()->delete();
        foreach ($documents as $docData) {
            OnsiteRequestItem::create([
                'onsite_request_id' => $onsiteRequest->id,
                'document_id' => $docData['document_id'],
                'quantity' => $docData['quantity'],
            ]);
        }

        // Always go to payment step so users can see payment information
        // and manually proceed regardless of whether document is free or paid
        $onsiteRequest->current_step = 'payment';
        $onsiteRequest->save();

        // Send real-time notification to registrars about updated onsite request
        $totalQuantity = array_sum(array_column($documents, 'quantity'));
        $documentNames = $onsiteRequest->requestItems->map(function($item) {
            return $item->document->type_document . ' (x' . $item->quantity . ')';
        })->join(', ');

        $this->notificationService->sendNotification(
            "Onsite request updated: {$onsiteRequest->ref_code}",
            'request-updated',
            [
                'request_id' => $onsiteRequest->id,
                'ref_code' => $onsiteRequest->ref_code,
                'student_name' => $onsiteRequest->full_name,
                'document_type' => $documentNames,
                'course' => $onsiteRequest->course,
                'year_level' => $onsiteRequest->year_level,
                'department' => $onsiteRequest->department,
                'quantity' => $totalQuantity,
                'current_step' => $onsiteRequest->current_step,
                'window_id' => $onsiteRequest->window_id,
                'updated_at' => $onsiteRequest->updated_at->toISOString(),
                'request_type' => 'onsite',
                'price' => $onsiteRequest->requestItems->sum(function($item) {
                    return $item->document->price * $item->quantity;
                })
            ],
            ['registrar-notifications', 'onsite-request-updates']
        );

        return redirect()->route('onsite.timeline', $onsiteRequest->id);
    }

    public function timeline(OnsiteRequest $onsiteRequest)
    {
        $onsiteRequest->load(['window', 'registrar', 'requestItems.document', 'feedback']);

        $ticketNumber = 'ticket-no:' . $onsiteRequest->created_at->format('Ymd') . '-i' . $onsiteRequest->id;

        $documents = Document::all(); // Add this for the modal

        // Render the timeline view
    return view('onsite.timeline', compact('onsiteRequest', 'ticketNumber', 'documents'));
    }

    /**
     * AJAX: search students by student_id (or partial) to autofill form.
     */
    public function searchStudent(Request $request)
    {
        try {
            // Set JSON content type immediately to prevent HTML error pages
            header('Content-Type: application/json');
            
            $validated = $request->validate([
                'query' => 'required|string|min:2|max:50',
                'search_by' => 'nullable|string|in:student_id,full_name',
            ]);

            $q = trim($validated['query']);
            $searchBy = $validated['search_by'] ?? 'student_id';

            $students = collect();

            if ($searchBy === 'student_id') {
                // Search by student_id
                $students = Student::where('student_id', 'LIKE', "%{$q}%")
                    ->limit(10)
                    ->get();
            } elseif ($searchBy === 'full_name') {
                // Search by full name (first_name, middle_name, last_name)
                $students = Student::whereHas('user', function ($userQuery) use ($q) {
                    $userQuery->where(function ($nameQuery) use ($q) {
                        $nameQuery->where('first_name', 'LIKE', "%{$q}%")
                                 ->orWhere('middle_name', 'LIKE', "%{$q}%")
                                 ->orWhere('last_name', 'LIKE', "%{$q}%")
                                 ->orWhere(DB::raw("CONCAT(first_name, ' ', COALESCE(middle_name, ''), ' ', last_name)"), 'LIKE', "%{$q}%")
                                 ->orWhere(DB::raw("CONCAT(first_name, ' ', last_name)"), 'LIKE', "%{$q}%");
                    });
                })->limit(10)->get();
            }

            // If no results and searching by student_id, try searching by name as fallback
            if ($students->isEmpty() && $searchBy === 'student_id') {
                try {
                    $students = Student::whereHas('user', function ($userQuery) use ($q) {
                        $userQuery->where('first_name', 'LIKE', "%{$q}%")
                                 ->orWhere('last_name', 'LIKE', "%{$q}%");
                    })->limit(10)->get();
                } catch (\Exception $e) {
                    // If relationship query fails, stick with empty result
                    Log::warning('User relationship search failed', ['error' => $e->getMessage()]);
                }
            }

            $results = [];
            foreach ($students as $s) {
                $fullName = 'Student ID: ' . ($s->student_id ?? 'Unknown');
                
                // Safely try to get user information
                try {
                    // Load user relationship if not already loaded
                    if (!$s->relationLoaded('user') && $s->user_id) {
                        $user = \App\Models\User::find($s->user_id);
                    } else {
                        $user = $s->user;
                    }
                    
                    if ($user) {
                        $firstName = $user->first_name ?? '';
                        $lastName = $user->last_name ?? '';
                        $middleName = $user->middle_name ?? '';
                        
                        // Build full name
                        $nameParts = array_filter([$firstName, $middleName, $lastName]);
                        if (!empty($nameParts)) {
                            $fullName = implode(' ', $nameParts);
                        }
                    }
                } catch (\Exception $e) {
                    Log::warning('Error getting user full name', [
                        'error' => $e->getMessage(), 
                        'student_id' => $s->id,
                        'user_id' => $s->user_id ?? null
                    ]);
                }

                $results[] = [
                    'id' => $s->id ?? null,
                    'student_id' => $s->student_id ?? '',
                    'full_name' => $fullName,
                    'course' => $s->course ?? '',
                    'year_level' => $s->year_level ?? '',
                    'department' => $s->department ?? '',
                ];
            }

            return response()->json($results, 200, ['Content-Type' => 'application/json']);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Validation failed', 'messages' => $e->errors()], 422);
        } catch (\PDOException $e) {
            Log::error('Database error in student search: ' . $e->getMessage(), [
                'request' => $request->all(),
                'code' => $e->getCode()
            ]);
            
            return response()->json(['error' => 'Database connection issue'], 500);
        } catch (\Exception $e) {
            Log::error('Student search error: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json(['error' => 'Search failed'], 500);
        }
    }

    /**
     * Simple test endpoint to verify API is working
     */
    public function testStudentSearch()
    {
        try {
            $studentCount = Student::count();
            $userCount = User::count();
            
            return response()->json([
                'status' => 'ok',
                'student_count' => $studentCount,
                'user_count' => $userCount,
                'timestamp' => now()->toISOString(),
                'message' => 'Student search API is working'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'API test failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function submitReference(Request $request)
    {
        $request->validate([
            'onsite_id' => 'required|exists:onsite_requests,id',
            'ref_code'  => 'nullable|string|max:100',
        ]);

        $onsiteRequest = OnsiteRequest::findOrFail($request->onsite_id);

        // Check if payment is approved (required for paid documents)
        $totalPrice = $onsiteRequest->requestItems->sum(function($item) {
            return $item->document->price * $item->quantity;
        });

        if ($totalPrice > 0 && !$onsiteRequest->payment_approved) {
            return redirect()->back()->with('error', 'Payment must be approved by accounting before proceeding.');
        }

        if ($onsiteRequest->current_step === 'window' && $onsiteRequest->window_id) {
            return redirect()->route('onsite.timeline', $onsiteRequest->id)
                             ->with('info', 'Request already assigned to a window.');
        }

        // Find available window
        $availableWindow = Window::where('is_occupied', false)->first();

        if (!$availableWindow) {
            return redirect()->back()->with('error', 'No available window at the moment. Please wait.');
        }

        // Assign window and move to window queue
        $onsiteRequest->window_id = $availableWindow->id;
        $onsiteRequest->current_step = 'window';
        $onsiteRequest->status = 'processing';
        $onsiteRequest->save();

        $availableWindow->update(['is_occupied' => true]);

        // Try to assign registrar and move to processing immediately
        $assignedRegistrar = $this->queueService->assignRegistrarToRequest($onsiteRequest);

        if ($assignedRegistrar) {
            // Move directly to processing if registrar is available
            $onsiteRequest->update([
                'current_step' => 'processing',
                'status' => 'processing'
            ]);

            $message = "Request {$onsiteRequest->ref_code} assigned to Window {$availableWindow->name} and Registrar {$assignedRegistrar->user->first_name} {$assignedRegistrar->user->last_name}.";
        } else {
            // Stay in window queue if no registrar available
            $message = "Request {$onsiteRequest->ref_code} assigned to Window {$availableWindow->name} and waiting for registrar assignment.";
        }

        // Send real-time notification to registrars about window assignment
        $this->notificationService->sendNotification(
            $message,
            'window-assigned',
            [
                'request_id' => $onsiteRequest->id,
                'ref_code' => $onsiteRequest->ref_code,
                'queue_number' => $onsiteRequest->queue_number,
                'student_name' => $onsiteRequest->full_name,
                'document_type' => $onsiteRequest->requestItems->pluck('document.type_document')->join(', '),
                'window_name' => $availableWindow->name,
                'window_id' => $availableWindow->id,
                'current_step' => $onsiteRequest->current_step,
                'status' => $onsiteRequest->status,
                'assigned_registrar' => $assignedRegistrar ? $assignedRegistrar->user->first_name . ' ' . $assignedRegistrar->user->last_name : null,
                'request_type' => 'onsite'
            ],
            ['registrar-notifications', "window-{$availableWindow->id}"]
        );

        // Also broadcast a status update event for Pusher listeners (if needed)
        $this->notificationService->sendRequestStatusUpdate(
            $onsiteRequest->ref_code,
            $onsiteRequest->current_step,
            $message,
            [
                'student_name' => $onsiteRequest->full_name,
                'document_type' => $onsiteRequest->requestItems->pluck('document.type_document')->join(', '),
                'window_name' => $availableWindow->name,
                'window_id' => $availableWindow->id,
                'queue_number' => $onsiteRequest->queue_number,
                'assigned_registrar' => $assignedRegistrar ? $assignedRegistrar->user->first_name . ' ' . $assignedRegistrar->user->last_name : null,
            ]
        );

        return redirect()->route('onsite.timeline', $onsiteRequest->id)
                         ->with('success', $message);
    }

    /**
     * REGISTRAR: Accept request, assign to registrar, and move to processing
     */
    public function acceptRequest($id)
    {
        $onsiteRequest = OnsiteRequest::findOrFail($id);

        // Assign registrar and move to processing
        $onsiteRequest->assigned_registrar_id = Auth::id();
        $onsiteRequest->current_step = 'processing';
        $onsiteRequest->status = 'processing';
        $onsiteRequest->save();

        // Broadcast real-time notification
        $this->notificationService->sendRequestStatusUpdate(
            $onsiteRequest->ref_code,
            'processing',
            "Onsite request {$onsiteRequest->ref_code} has been accepted and is now being processed",
            [
                'student_name' => $onsiteRequest->full_name,
                'document_type' => $onsiteRequest->requestItems->pluck('document.type_document')->join(', '),
                'registrar_name' => Auth::user()->first_name . ' ' . Auth::user()->last_name,
                'request_type' => 'onsite',
                'queue_number' => $onsiteRequest->queue_number
            ]
        );

        return back()->with('success', 'Request accepted and moved to Processing.');
    }

    /**
     * REGISTRAR: Mark document ready for release and generate queue number
     */
    public function markAsReadyForRelease($id)
    {
        $onsiteRequest = OnsiteRequest::findOrFail($id);
        
        // Generate queue number using QueueService
        $queueNumber = $this->queueService->generateQueueNumber($onsiteRequest);
        
        $onsiteRequest->current_step = 'release';
        $onsiteRequest->status = 'released';  // Keep as 'released', not 'in_queue'
        $onsiteRequest->queue_number = $queueNumber;
        $onsiteRequest->save();

        // Broadcast real-time notification
        $this->notificationService->sendRequestStatusUpdate(
            $onsiteRequest->ref_code,
            'released',
            "Onsite request {$onsiteRequest->ref_code} is ready for pickup - Queue #{$queueNumber}",
            [
                'student_name' => $onsiteRequest->full_name,
                'document_type' => $onsiteRequest->requestItems->pluck('document.type_document')->join(', '),
                'registrar_name' => Auth::user()->first_name . ' ' . Auth::user()->last_name,
                'request_type' => 'onsite',
                'queue_number' => $queueNumber
            ]
        );

        // Send email notification to student if email is available
        if ($onsiteRequest->student && $onsiteRequest->student->user) {
            $email = $onsiteRequest->student->user->personal_email ?? $onsiteRequest->student->user->school_email;
            if ($email) {
                try {
                    Mail::to($email)->send(new RequestReadyForReleaseMail($onsiteRequest, 'onsite'));
                } catch (\Exception $e) {
                    // Log the error but don't fail the request
                    \Illuminate\Support\Facades\Log::error('Failed to send ready for release email: ' . $e->getMessage());
                }
            }
        }

        return back()->with('success', "Request marked as Ready for Pickup. Queue Number: {$queueNumber}");
    }

    /**
     * REGISTRAR: Close the request after releasing the document
     */
    public function closeRequest($id)
    {
        $onsiteRequest = OnsiteRequest::findOrFail($id);

        $windowId = $onsiteRequest->window_id;

        // Update the onsite request status and step
        $onsiteRequest->current_step = 'completed';
        $onsiteRequest->status = 'completed';
        $onsiteRequest->save();

        // Use QueueService to complete request and process next in queue
        $this->queueService->completeRequest($onsiteRequest);

        // Broadcast real-time notification
        $this->notificationService->sendRequestStatusUpdate(
            $onsiteRequest->ref_code,
            'completed',
            "Onsite request {$onsiteRequest->ref_code} has been completed and closed",
            [
                'student_name' => $onsiteRequest->full_name,
                'document_type' => $onsiteRequest->requestItems->pluck('document.type_document')->join(', '),
                'registrar_name' => Auth::user()->first_name . ' ' . Auth::user()->last_name,
                'request_type' => 'onsite',
                'window_freed' => $windowId ? true : false,
                'queue_number' => $onsiteRequest->queue_number
            ]
        );

        // Send email notification to student if email is available
        if ($onsiteRequest->student && $onsiteRequest->student->user) {
            $email = $onsiteRequest->student->user->personal_email ?? $onsiteRequest->student->user->school_email;
            if ($email) {
                try {
                    Mail::to($email)->send(new RequestCompletedMail($onsiteRequest, 'onsite'));
                } catch (\Exception $e) {
                    // Log the error but don't fail the request
                    \Illuminate\Support\Facades\Log::error('Failed to send request completed email: ' . $e->getMessage());
                }
            }
        }

        return back()->with('success', 'Request successfully closed and next request processed.');
    }

    /**
     * REGISTRAR: Mark a ready-for-pickup onsite request as completed (documents actually collected)
     */
    public function markAsCompleted($id)
    {
        $onsiteRequest = OnsiteRequest::findOrFail($id);

        if (!in_array($onsiteRequest->status, ['released', 'ready_for_pickup', 'in_queue'])) {
            return back()->with('error', 'Only released requests can be marked as completed.');
        }

        $windowId = $onsiteRequest->window_id;
        $wasInQueue = $onsiteRequest->status === 'ready_for_pickup';

        // Update the onsite request status and step
        $onsiteRequest->current_step = 'completed';
        $onsiteRequest->status = 'completed';
        $onsiteRequest->save();

        // If this person was ready for pickup, move next person from waiting to in_queue
        if ($wasInQueue) {
            $this->moveNextPersonToQueue();
        }

        // Use QueueService to complete request and process next in queue
        $this->queueService->completeRequest($onsiteRequest);

        // Broadcast real-time notification
        $this->notificationService->sendRequestStatusUpdate(
            $onsiteRequest->ref_code,
            'completed',
            "Onsite request {$onsiteRequest->ref_code} has been completed - documents collected",
            [
                'student_name' => $onsiteRequest->full_name,
                'document_type' => $onsiteRequest->requestItems->pluck('document.type_document')->join(', '),
                'registrar_name' => Auth::user()->first_name . ' ' . Auth::user()->last_name,
                'request_type' => 'onsite',
                'window_freed' => $windowId ? true : false,
                'queue_number' => $onsiteRequest->queue_number
            ]
        );

        // Note: Email notification removed as per request - no email sent when marking as completed

        return back()->with('success', 'Request marked as completed - documents successfully collected.');
    }

    /**
     * REGISTRAR: Mark an in-queue onsite request as ready for pickup
     */
    public function markAsReadyForPickup($id)
    {
        $onsiteRequest = OnsiteRequest::findOrFail($id);

        if ($onsiteRequest->status !== 'in_queue') {
            return back()->with('error', 'Only in-queue requests can be marked as ready for pickup.');
        }

        // Update the onsite request status to ready for pickup
        $onsiteRequest->status = 'ready_for_pickup';
        $onsiteRequest->save();

        // Move next person from waiting to in_queue
        $this->moveNextPersonToQueue();

        // Broadcast real-time notification
        $this->notificationService->sendRequestStatusUpdate(
            $onsiteRequest->ref_code,
            'ready_for_pickup',
            "Onsite request {$onsiteRequest->ref_code} is now ready for pickup",
            [
                'student_name' => $onsiteRequest->full_name,
                'document_type' => $onsiteRequest->requestItems->pluck('document.type_document')->join(', '),
                'registrar_name' => Auth::user()->first_name . ' ' . Auth::user()->last_name,
                'request_type' => 'onsite',
                'queue_number' => $onsiteRequest->queue_number,
                'status_update' => true
            ]
        );

        return back()->with('success', 'Request marked as ready for pickup.');
    }
    
    /**
     * Move next person from waiting to in_queue
     */
    private function moveNextPersonToQueue()
    {
        // Check if there's already someone in queue (only 1 person can be in_queue at a time)
        $studentInQueue = \App\Models\StudentRequest::where('status', 'in_queue')->exists();
        $onsiteInQueue = OnsiteRequest::where('status', 'in_queue')->exists();
        
        if (!($studentInQueue || $onsiteInQueue)) {
            // Find the next waiting person and move them to in_queue
            $nextStudent = \App\Models\StudentRequest::where('status', 'waiting')->orderBy('updated_at', 'asc')->first();
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
                
                // Send real-time notification about queue update
                $this->sendQueueUpdateNotification($nextRequest, $nextType);
            }
        }
    }
    
    /**
     * Send queue update notification
     */
    private function sendQueueUpdateNotification($request, $type)
    {
        // Send notification that someone moved from waiting to in_queue
        $this->notificationService->sendRequestStatusUpdate(
            $type === 'student' ? $request->reference_no : $request->ref_code,
            'in_queue',
            "Queue updated: {$type} request is now in queue",
            [
                'student_name' => $type === 'student' ? 
                    $request->student->user->first_name . ' ' . $request->student->user->last_name : 
                    $request->full_name,
                'request_type' => $type,
                'queue_number' => $request->queue_number,
                'status_update' => true
            ]
        );
    }

    /**
     * REGISTRAR: Update expected release date for completed requests
     */
    public function updateExpectedReleaseDate(Request $request, $id)
    {
        $request->validate([
            'expected_release_date' => 'required|date|after:now',
        ]);

        $onsiteRequest = OnsiteRequest::with('student.user', 'requestItems.document')->findOrFail($id);

        if ($onsiteRequest->status !== 'completed') {
            return back()->with('error', 'Only completed requests can have their expected release date updated.');
        }

        $onsiteRequest->update([
            'expected_release_date' => $request->expected_release_date,
        ]);

        // Send email notification if student has an email
        if ($onsiteRequest->student && $onsiteRequest->student->user) {
            $email = $onsiteRequest->student->user->personal_email ?? $onsiteRequest->student->user->school_email;
            if ($email) {
                try {
                    Mail::to($email)->send(new ExpectedReleaseDateUpdatedMail($onsiteRequest));
                } catch (\Exception $e) {
                    // Log the error but don't fail the request
                    Log::error('Failed to send expected release date update email: ' . $e->getMessage());
                }
            }
        }

        return back()->with('success', 'Expected release date updated successfully.');
    }

    public function uploadReceipt(Request $request, OnsiteRequest $onsiteRequest)
    {
        $request->validate([
            'payment_receipt' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB max
        ]);

        if ($onsiteRequest->current_step !== 'payment') {
            return redirect()->back()->with('error', 'Request is not in payment step.');
        }

        // Delete old receipt if exists
        if ($onsiteRequest->payment_receipt_path) {
            Storage::disk('public')->delete($onsiteRequest->payment_receipt_path);
        }

        // Store new receipt
        $path = $request->file('payment_receipt')->store('payment-receipts', 'public');

        $onsiteRequest->update([
            'payment_receipt_path' => $path,
            'payment_approved' => false, // Reset approval status
            'approved_by_accounting_id' => null,
            'payment_approved_at' => null,
        ]);

        // Send notification to accounting
        $this->notificationService->sendNotification(
            "New payment receipt uploaded for request {$onsiteRequest->ref_code}",
            'payment-receipt-uploaded',
            [
                'request_id' => $onsiteRequest->id,
                'ref_code' => $onsiteRequest->ref_code,
                'student_name' => $onsiteRequest->full_name,
            ]
        );

        return redirect()->back()->with('success', 'Payment receipt uploaded successfully. Waiting for accounting approval.');
    }
}
