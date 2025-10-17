<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\StudentRequest;
use App\Services\RealTimeNotificationService;
use App\Services\QueueService;
use App\Mail\RequestReadyForReleaseMail;
use App\Mail\RequestCompletedMail;
use App\Mail\RequestRejectedMail;
use App\Mail\ExpectedReleaseDateUpdatedMail;
use Carbon\Carbon;

class RegistrarController extends Controller
{
    protected $notificationService;
    protected $queueService;

    public function __construct(RealTimeNotificationService $notificationService, QueueService $queueService)
    {
        $this->notificationService = $notificationService;
        $this->queueService = $queueService;
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
        
        // Find the window ID that corresponds to this registrar's window number
        $assignedWindow = \App\Models\Window::where('name', 'Window ' . $currentWindowNumber)->first();
        
        // Check if the registrar currently has any active requests (excluding completed ones)
        $isWindowOccupied = false;
        $currentRequest = null;
        
        // Check if this registrar has any active request assigned to them
        $currentRequest = StudentRequest::where('assigned_registrar_id', $currentUser->id)
            ->whereIn('status', ['registrar_approved', 'processing', 'in_queue', 'ready_for_pickup'])
            ->whereNotIn('status', ['completed', 'released'])
            ->first();
        
        $isWindowOccupied = $currentRequest !== null;
        
        // Check if registrar has any onsite requests in progress (to prevent concurrent onsite processing)
        $hasActiveOnsiteRequest = StudentRequest::where('assigned_registrar_id', $currentUser->id)
            ->whereIn('status', ['pending', 'registrar_approved', 'processing', 'ready_for_release'])
            ->whereNotIn('status', ['completed', 'released'])
            ->exists();
        
        $search = strtolower(trim($request->input('search')));

        $query = StudentRequest::with(['student.user', 'requestItems.document', 'window', 'registrar']);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('student.user', function ($userQuery) use ($search) {
                        $userQuery->where(DB::raw('LOWER(first_name)'), 'like', "%{$search}%")
                                  ->orWhere(DB::raw('LOWER(last_name)'), 'like', "%{$search}%")
                                  ->orWhere(DB::raw('LOWER(student_id)'), 'like', "%{$search}%");
                    })
                    ->orWhere(DB::raw('LOWER(reference_no)'), 'like', "%{$search}%")
                    ->orWhereHas('requestItems.document', function ($docQuery) use ($search) {
                        $docQuery->where(DB::raw('LOWER(type_document)'), 'like', "%{$search}%");
                    });
            });
        }

        // Apply window-based filtering: show pending requests to all registrars + assigned requests to specific registrar
        $query->where(function($q) use ($currentUser, $assignedWindow) {
            $q->where(function($subQ) {
                // Show all pending requests that need approval
                $subQ->where('status', 'pending')
                     ->whereNull('assigned_registrar_id')
                     ->whereNull('window_id');
            })
            ->orWhere(function($subQ) use ($currentUser, $assignedWindow) {
                // Show requests assigned to this registrar
                $subQ->where('assigned_registrar_id', $currentUser->id);
            });
        });

        $all = $query->latest()->paginate(10);

        // Set windowNumber for the view
        $windowNumber = $currentWindowNumber;

        return view('registrar.dashboard_all', compact('all', 'isWindowOccupied', 'currentRequest', 'windowNumber', 'assignedWindow', 'hasActiveOnsiteRequest'))
            ->with('windowNumber', $currentWindowNumber);
    }

    public function pending()
    {
        // Get the current registrar's information
        $currentUser = Auth::user();
        $currentRegistrar = $currentUser->registrar;
        
        if (!$currentRegistrar) {
            return redirect()->back()->with('error', 'Access denied. You are not assigned as a registrar.');
        }
        
        $currentWindowNumber = $currentRegistrar->window_number;
        
        // Find the window ID that corresponds to this registrar's window number
        $assignedWindow = \App\Models\Window::where('name', 'Window ' . $currentWindowNumber)->first();
        
        // Check if the registrar currently has any active requests (excluding completed ones)
        $isWindowOccupied = false;
        $currentRequest = null;
        
        // Check if this registrar has any active request assigned to them
        $currentRequest = StudentRequest::where('assigned_registrar_id', $currentUser->id)
            ->whereIn('status', ['registrar_approved', 'processing', 'in_queue', 'ready_for_pickup'])
            ->whereNotIn('status', ['completed', 'released'])
            ->first();
        
        $isWindowOccupied = $currentRequest !== null;
        
        // Check if registrar has any onsite requests in progress (to prevent concurrent onsite processing)
        $hasActiveOnsiteRequest = StudentRequest::where('assigned_registrar_id', $currentUser->id)
            ->whereIn('status', ['pending', 'registrar_approved', 'processing', 'ready_for_release'])
            ->whereNotIn('status', ['completed', 'released'])
            ->exists();

        // Show pending requests to all registrars + requests assigned to this registrar's window
        $pending = StudentRequest::with(['student.user', 'requestItems.document', 'window', 'registrar'])
            ->where(function($q) use ($currentUser, $assignedWindow) {
                $q->where(function($subQ) {
                    // Show all pending requests that need approval
                    $subQ->where('status', 'pending')
                         ->whereNull('assigned_registrar_id')
                         ->whereNull('window_id');
                })
                ->orWhere(function($subQ) use ($assignedWindow) {
                    // Show requests assigned to this registrar's window
                    if ($assignedWindow) {
                        $subQ->where('window_id', $assignedWindow->id)
                             ->whereIn('status', ['registrar_approved', 'processing', 'ready_for_release', 'in_queue', 'ready_for_pickup', 'waiting']);
                    }
                });
            })
            ->get();

        $windowNumber = $currentWindowNumber;

        return view('registrar.dashboard_pending', compact('pending', 'isWindowOccupied', 'currentRequest', 'windowNumber', 'assignedWindow', 'hasActiveOnsiteRequest'))
            ->with('windowNumber', $currentWindowNumber);
    }

    public function completed()
    {
        // Get the current registrar's information
        $currentUser = Auth::user();
        $currentRegistrar = $currentUser->registrar;
        
        if (!$currentRegistrar) {
            return redirect()->back()->with('error', 'Access denied. You are not assigned as a registrar.');
        }
        
        $currentWindowNumber = $currentRegistrar->window_number;
        
        // Find the window ID that corresponds to this registrar's window number
        $assignedWindow = \App\Models\Window::where('name', 'Window ' . $currentWindowNumber)->first();
        
        // Check if the registrar currently has any active requests (excluding completed ones)
        $isWindowOccupied = false;
        $currentRequest = null;
        
        // Check if this registrar has any active request assigned to them
        $currentRequest = StudentRequest::where('assigned_registrar_id', $currentUser->id)
            ->whereIn('status', ['registrar_approved', 'processing', 'in_queue', 'ready_for_pickup'])
            ->whereNotIn('status', ['completed', 'released'])
            ->first();
        
        $isWindowOccupied = $currentRequest !== null;

        $completed = StudentRequest::with(['student.user', 'requestItems.document'])
                                ->where('status', 'completed')
                                ->get();

        $windowNumber = $currentWindowNumber;

        return view('registrar.dashboard_completed', compact('completed', 'isWindowOccupied', 'currentRequest', 'windowNumber', 'assignedWindow'))
            ->with('windowNumber', $currentWindowNumber);
    }

    public function upload(Request $request, StudentRequest $studentRequest)
    {
        $request->validate([
            'document_file' => 'required|mimes:pdf|max:5120',
        ]);

        if ($request->hasFile('document_file')) {
            $path = $request->file('document_file')->store('documents', 'public');
            // Note: StudentRequest doesn't have document_file_path field, might need to add or handle differently
            // For now, just return success
        }

        return back()->with('success', 'PDF uploaded successfully.');
    }

    public function download(StudentRequest $studentRequest)
    {
        // StudentRequest doesn't have document_file_path, so for now return error
        return back()->with('error', 'No file available for this request.');
    }

    /**
     * Approve a pending request by registrar before forwarding to accounting
     */
    public function approveRequest(Request $request, StudentRequest $studentRequest)
    {
        if ($studentRequest->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending requests can be approved.');
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
            $occupiedRequest = StudentRequest::where('assigned_registrar_id', $currentUser->id)
                ->whereIn('status', ['registrar_approved', 'processing', 'in_queue', 'ready_for_pickup'])
                ->whereNotIn('status', ['completed', 'released'])
                ->first();
                
            if ($occupiedRequest && $occupiedRequest->id !== $studentRequest->id) {
                return redirect()->back()->with('error', 'Your window is currently occupied. Please complete the current request first.');
            }
        }

        $studentRequest->update([
            'status' => 'registrar_approved',
            'registrar_approved' => true,
            'approved_by_registrar_id' => $currentUser->id,
            'registrar_approved_at' => now(),
            'remarks' => $request->input('remarks'),
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
            $studentRequest->reference_no,
            'registrar_approved',
            "Request #{$studentRequest->reference_no} has been approved by registrar and is ready for accounting review",
            [
                'student_name' => $studentRequest->student->user->first_name . ' ' . $studentRequest->student->user->last_name,
                'document_type' => $studentRequest->requestItems->first()->document->type_document ?? 'Document',
                'registrar_name' => Auth::user()->first_name . ' ' . Auth::user()->last_name,
                'window_assignment' => $assignedWindow ? $assignedWindow->name : null,
            ]
        );

        return redirect()->back()->with('success', 'Request approved by registrar and assigned to your window. Forwarded to accounting for payment processing.');
    }

    /**
     * Reject a pending request and send it back to student timeline for re-approval
     */
    public function rejectRequest(Request $request, StudentRequest $studentRequest)
    {
        Log::info('rejectRequest method called for student request', [
            'request_id' => $studentRequest->id,
            'reference_no' => $studentRequest->reference_no,
            'status' => $studentRequest->status,
            'remarks' => $request->input('remarks')
        ]);

        if ($studentRequest->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending requests can be rejected.');
        }

        // Get current registrar
        $currentUser = Auth::user();
        $currentRegistrar = $currentUser->registrar;
        
        if (!$currentRegistrar) {
            return redirect()->back()->with('error', 'Access denied. You are not assigned as a registrar.');
        }

        // Reset the request to allow student re-approval from timeline
        $studentRequest->update([
            'status' => 'pending',
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
            $studentRequest->reference_no,
            'rejected',
            "Request #{$studentRequest->reference_no} has been rejected by registrar. Please review and re-approve from your timeline.",
            [
                'student_name' => $studentRequest->student->user->first_name . ' ' . $studentRequest->student->user->last_name,
                'document_type' => $studentRequest->requestItems->first()->document->type_document ?? 'Document',
                'registrar_name' => $currentUser->first_name . ' ' . $currentUser->last_name,
                'reason' => $request->input('remarks', 'Request rejected - please review and re-submit')
            ]
        );

        // Send email notification if student has an email
        Log::info('Starting email notification process for rejection', [
            'request_id' => $studentRequest->id,
            'has_student' => $studentRequest->student ? 'yes' : 'no',
            'has_user' => $studentRequest->student && $studentRequest->student->user ? 'yes' : 'no'
        ]);
        
        if ($studentRequest->student && $studentRequest->student->user) {
            $email = $studentRequest->student->user->personal_email ?? $studentRequest->student->user->school_email;
            Log::info('Student email detection for rejection', [
                'request_id' => $studentRequest->id,
                'student_id' => $studentRequest->student->id,
                'personal_email' => $studentRequest->student->user->personal_email,
                'school_email' => $studentRequest->student->user->school_email,
                'selected_email' => $email
            ]);
            
            if ($email) {
                $remarks = $request->input('remarks');
                if (!$remarks || trim($remarks) === '') {
                    $remarks = 'Request rejected by registrar - please review and re-submit';
                }
                Log::info('Sending rejection email to student', [
                    'request_id' => $studentRequest->id,
                    'email' => $email,
                    'remarks' => $remarks
                ]);
                try {
                    // Temporarily send to test email to debug
                    Mail::to('zetabrill@gmail.com')->send(new RequestRejectedMail($studentRequest, 'student', $remarks));
                    Log::info('Rejection email sent successfully to student', [
                        'request_id' => $studentRequest->id,
                        'email' => $email,
                        'actually_sent_to' => 'zetabrill@gmail.com'
                    ]);
                } catch (\Exception $e) {
                    // Log the error but don't fail the request
                    Log::error('Failed to send rejection email: ' . $e->getMessage(), [
                        'request_id' => $studentRequest->id,
                        'email' => $email,
                        'exception' => $e->getTraceAsString()
                    ]);
                }
            } else {
                Log::warning('No email found for student rejection notification', [
                    'request_id' => $studentRequest->id,
                    'student_id' => $studentRequest->student->id
                ]);
            }
        } else {
            Log::warning('Student or user relationship missing for rejection email', [
                'request_id' => $studentRequest->id,
                'has_student' => $studentRequest->student ? 'yes' : 'no',
                'has_user' => $studentRequest->student && $studentRequest->student->user ? 'yes' : 'no'
            ]);
        }

        return redirect()->back()->with('success', 'Request rejected and sent back to student timeline for re-approval.');
    }

    /**
     * Mark a processing request as ready for release
     */
    public function markAsReadyForRelease(StudentRequest $studentRequest)
    {
        if ($studentRequest->status !== 'processing') {
            return redirect()->back()->with('error', 'Only processing requests can be marked as ready for release.');
        }

        $studentRequest->update([
            'status' => 'ready_for_release',
            'queue_number' => $this->queueService->generateQueueNumber(),
            'updated_at' => now(),
        ]);

        // Broadcast real-time notification
        $this->notificationService->sendRequestStatusUpdate(
            $studentRequest->reference_no,
            'ready_for_release',
            "Request #{$studentRequest->reference_no} is ready for release",
            [
                'student_name' => $studentRequest->student->user->first_name . ' ' . $studentRequest->student->user->last_name,
                'document_type' => $studentRequest->requestItems->first()->document->type_document ?? 'Document',
                'registrar_name' => Auth::user()->first_name . ' ' . Auth::user()->last_name,
                'queue_number' => $studentRequest->queue_number
            ]
        );

        // Send email notification to student
        $email = $studentRequest->student->user->personal_email ?? $studentRequest->student->user->school_email;
        if ($email) {
            try {
                Mail::to($email)->send(new RequestReadyForReleaseMail($studentRequest, 'student'));
            } catch (\Exception $e) {
                // Log the error but don't fail the request
                \Illuminate\Support\Facades\Log::error('Failed to send ready for release email: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('success', 'Request marked as ready for release.');
    }

    /**
     * Close a ready-for-release request and mark it as completed
     */
    public function closeRequest(StudentRequest $studentRequest)
    {
        if ($studentRequest->status !== 'ready_for_release') {
            return redirect()->back()->with('error', 'Only ready-for-release requests can be closed.');
        }

        $studentRequest->update([
            'status' => 'completed',
            'updated_at' => now(),
        ]);

        // Broadcast real-time notification
        $this->notificationService->sendRequestStatusUpdate(
            $studentRequest->reference_no,
            'completed',
            "Request #{$studentRequest->reference_no} has been completed and closed",
            [
                'student_name' => $studentRequest->student->user->first_name . ' ' . $studentRequest->student->user->last_name,
                'document_type' => $studentRequest->requestItems->first()->document->type_document ?? 'Document',
                'registrar_name' => Auth::user()->first_name . ' ' . Auth::user()->last_name
            ]
        );

        // Note: Email notification removed as per request - no email sent when marking as completed

        return redirect()->back()->with('success', 'Request completed successfully.');
    }

    /**
     * Mark a ready-for-pickup request as completed (documents actually collected)
     */
    public function markAsCompleted(StudentRequest $studentRequest)
    {
        if ($studentRequest->status !== 'ready_for_pickup') {
            return redirect()->back()->with('error', 'Only ready-for-pickup requests can be marked as completed.');
        }

        $studentRequest->update([
            'status' => 'completed',
            'updated_at' => now(),
        ]);

        // Check if queue is available and move next person
        $this->moveNextPersonToQueue();

        // Broadcast real-time notification
        $this->notificationService->sendRequestStatusUpdate(
            $studentRequest->reference_no,
            'completed',
            "Request #{$studentRequest->reference_no} has been completed - documents collected",
            [
                'student_name' => $studentRequest->student->user->first_name . ' ' . $studentRequest->student->user->last_name,
                'document_type' => $studentRequest->requestItems->first()->document->type_document ?? 'Document',
                'registrar_name' => Auth::user()->first_name . ' ' . Auth::user()->last_name,
                'queue_number' => $studentRequest->queue_number
            ]
        );

        // Note: Email notification removed as per request - no email sent when marking as completed

        return redirect()->back()->with('success', 'Request marked as completed - documents successfully collected.');
    }

    /**
     * Mark a ready-for-release request as ready for pickup (student called to window)
     */
    public function markAsReadyForPickup(StudentRequest $studentRequest)
    {
        if (!in_array($studentRequest->status, ['ready_for_release', 'in_queue'])) {
            return redirect()->back()->with('error', 'Only ready-for-release or in-queue requests can be marked as ready for pickup.');
        }

        $studentRequest->update([
            'status' => 'ready_for_pickup',
            'updated_at' => now(),
        ]);

        // Move next person from waiting to in_queue since this person left the queue
        $this->moveNextPersonToQueue();

        // Broadcast real-time notification
        $this->notificationService->sendRequestStatusUpdate(
            $studentRequest->reference_no,
            'ready_for_pickup',
            "Request #{$studentRequest->reference_no} is ready for pickup at the window",
            [
                'student_name' => $studentRequest->student->user->first_name . ' ' . $studentRequest->student->user->last_name,
                'document_type' => $studentRequest->requestItems->first()->document->type_document ?? 'Document',
                'registrar_name' => Auth::user()->first_name . ' ' . Auth::user()->last_name,
                'queue_number' => $studentRequest->queue_number
            ]
        );

        return redirect()->back()->with('success', 'Request marked as ready for pickup - student called to window.');
    }

    /**
     * Move the next waiting person to in_queue status
     */
    private function moveNextPersonToQueue()
    {
        // Check if there's already someone in queue (only 1 person can be in_queue at a time)
        $studentInQueue = StudentRequest::where('status', 'in_queue')->exists();
        $onsiteInQueue = \App\Models\OnsiteRequest::where('status', 'in_queue')->exists();
        
        if (!($studentInQueue || $onsiteInQueue)) {
            // Find the next waiting person (student or onsite) and move them to in_queue
            $nextStudent = StudentRequest::where('status', 'waiting')->orderBy('updated_at', 'asc')->first();
            $nextOnsite = \App\Models\OnsiteRequest::where('status', 'waiting')->orderBy('updated_at', 'asc')->first();
            
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
                if ($nextType === 'student') {
                    $this->notificationService->sendRequestStatusUpdate(
                        $nextRequest->reference_no,
                        'in_queue',
                        "Request #{$nextRequest->reference_no} moved to active queue",
                        [
                            'student_name' => $nextRequest->student->user->first_name . ' ' . $nextRequest->student->user->last_name,
                            'document_type' => $nextRequest->requestItems->first()->document->type_document ?? 'Document',
                            'queue_number' => $nextRequest->queue_number
                        ]
                    );
                } else {
                    $this->notificationService->sendRequestStatusUpdate(
                        $nextRequest->ref_code,
                        'in_queue',
                        "Onsite request {$nextRequest->ref_code} moved to active queue",
                        [
                            'student_name' => $nextRequest->full_name,
                            'document_type' => $nextRequest->requestItems->pluck('document.type_document')->join(', '),
                            'queue_number' => $nextRequest->queue_number,
                            'request_type' => 'onsite'
                        ]
                    );
                }
            }
        }
    }

    /**
     * Update expected release date for completed requests
     */
    public function updateExpectedReleaseDate(Request $request, StudentRequest $studentRequest)
    {
        $request->validate([
            'expected_release_date' => 'required|date|after:now',
        ]);

        if ($studentRequest->status !== 'completed') {
            return redirect()->back()->with('error', 'Only completed requests can have their expected release date updated.');
        }

        $studentRequest->update([
            'expected_release_date' => $request->expected_release_date,
        ]);

        // Send email notification if student has an email
        if ($studentRequest->student && $studentRequest->student->user) {
            $email = $studentRequest->student->user->personal_email ?? $studentRequest->student->user->school_email;
            if ($email) {
                try {
                    Mail::to($email)->send(new ExpectedReleaseDateUpdatedMail($studentRequest, 'student'));
                } catch (\Exception $e) {
                    // Log the error but don't fail the request
                    Log::error('Failed to send expected release date update email: ' . $e->getMessage());
                }
            }
        }

        return redirect()->back()->with('success', 'Expected release date updated successfully.');
    }

    /**
     * Show analytics and reports dashboard
     */
    public function analytics()
    {
        // Get total counts
        $totalRequests = StudentRequest::count();
        $totalStudents = \App\Models\Student::count();
        $onsiteRequests = \App\Models\OnsiteRequest::count();
        $onlineRequests = StudentRequest::count();

        // Get request status counts
        $pendingRequests = StudentRequest::where('status', 'pending')->count();
        $processingRequests = StudentRequest::where('status', 'processing')->count();
        $readyRequests = StudentRequest::where('status', 'ready_for_release')->count();
        $completedRequests = StudentRequest::where('status', 'completed')->count();

        // Get document type statistics
        $documentStats = DB::table('student_requests')
            ->join('student_request_items', 'student_requests.id', '=', 'student_request_items.student_request_id')
            ->join('documents', 'student_request_items.document_id', '=', 'documents.id')
            ->select(
                'documents.type_document', 
                DB::raw('COUNT(*) as request_count'),
                DB::raw('SUM(student_request_items.quantity) as total_quantity')
            )
            ->groupBy('documents.type_document', 'documents.id')
            ->orderBy('request_count', 'desc')
            ->limit(10)
            ->get();

        // Get current registrar's onsite request statistics (if applicable)
        $currentUserId = Auth::id();
        $onsiteCompleted = \App\Models\OnsiteRequest::where('assigned_registrar_id', $currentUserId)
            ->where('status', 'completed')
            ->count();
        
        $onsitePending = \App\Models\OnsiteRequest::whereNull('assigned_registrar_id')
            ->orWhere('assigned_registrar_id', $currentUserId)
            ->where('status', 'pending')
            ->count();

        // Personal performance metrics
        $myProcessingTime = $this->calculateMyProcessingTime($currentUserId);
        $myProductivity = $this->getMyProductivity($currentUserId);

        // Get time-based statistics
        $today = now()->startOfDay();
        $weekStart = now()->startOfWeek();
        $monthStart = now()->startOfMonth();

        $todayStats = [
            'new' => StudentRequest::whereDate('created_at', $today)->count(),
            'completed' => StudentRequest::where('status', 'completed')
                ->whereDate('updated_at', $today)->count(),
            'pending' => StudentRequest::where('status', 'pending')
                ->whereDate('created_at', $today)->count(),
        ];

        $weekStats = [
            'new' => StudentRequest::where('created_at', '>=', $weekStart)->count(),
            'completed' => StudentRequest::where('status', 'completed')
                ->where('updated_at', '>=', $weekStart)->count(),
            'pending' => StudentRequest::where('status', 'pending')
                ->where('created_at', '>=', $weekStart)->count(),
        ];

        $monthStats = [
            'new' => StudentRequest::where('created_at', '>=', $monthStart)->count(),
            'completed' => StudentRequest::where('status', 'completed')
                ->where('updated_at', '>=', $monthStart)->count(),
            'pending' => StudentRequest::where('status', 'pending')
                ->where('created_at', '>=', $monthStart)->count(),
        ];

        // Calculate average processing times (in hours)
        $avgProcessingTime = DB::table('student_requests')
            ->whereNotNull('updated_at')
            ->where('status', 'completed')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, updated_at)) as avg_hours')
            ->value('avg_hours');

        $fastestProcessing = DB::table('student_requests')
            ->whereNotNull('updated_at')
            ->where('status', 'completed')
            ->selectRaw('MIN(TIMESTAMPDIFF(HOUR, created_at, updated_at)) as min_hours')
            ->value('min_hours');

        $slowestProcessing = DB::table('student_requests')
            ->whereNotNull('updated_at')
            ->where('status', 'completed')
            ->selectRaw('MAX(TIMESTAMPDIFF(HOUR, created_at, updated_at)) as max_hours')
            ->value('max_hours');

        // Student Request Distribution by Status
        $studentRequestStats = [
            'pending' => StudentRequest::where('status', 'pending')->count(),
            'processing' => StudentRequest::where('status', 'processing')->count(),
            'ready_for_release' => StudentRequest::where('status', 'ready_for_release')->count(),
            'completed' => StudentRequest::where('status', 'completed')->count(),
            'rejected' => StudentRequest::where('status', 'rejected')->count(),
        ];

        // Onsite Request Distribution by Status  
        $onsiteRequestStats = [
            'pending' => \App\Models\OnsiteRequest::where('status', 'pending')->count(),
            'processing' => \App\Models\OnsiteRequest::where('status', 'processing')->count(),
            'completed' => \App\Models\OnsiteRequest::where('status', 'completed')->count(),
            'cancelled' => \App\Models\OnsiteRequest::where('status', 'cancelled')->count(),
        ];

        // Student Request Document Type Distribution
        $studentDocumentDistribution = DB::table('student_requests')
            ->join('student_request_items', 'student_requests.id', '=', 'student_request_items.student_request_id')
            ->join('documents', 'student_request_items.document_id', '=', 'documents.id')
            ->select(
                'documents.type_document',
                DB::raw('COUNT(DISTINCT student_requests.id) as request_count'),
                DB::raw('SUM(student_request_items.quantity) as total_quantity')
            )
            ->groupBy('documents.type_document', 'documents.id')
            ->orderBy('request_count', 'desc')
            ->get();

        // Onsite Request Document Type Distribution
        $onsiteDocumentDistribution = DB::table('onsite_requests')
            ->join('onsite_request_items', 'onsite_requests.id', '=', 'onsite_request_items.onsite_request_id')
            ->join('documents', 'onsite_request_items.document_id', '=', 'documents.id')
            ->select(
                'documents.type_document',
                DB::raw('COUNT(DISTINCT onsite_requests.id) as request_count'),
                DB::raw('SUM(onsite_request_items.quantity) as total_quantity')
            )
            ->groupBy('documents.type_document', 'documents.id')
            ->orderBy('request_count', 'desc')
            ->get();

        return view('registrar.reports', compact(
            'totalRequests',
            'totalStudents', 
            'onsiteRequests',
            'onlineRequests',
            'pendingRequests',
            'processingRequests',
            'readyRequests',
            'completedRequests',
            'documentStats',
            'onsiteCompleted',
            'onsitePending',
            'myProcessingTime',
            'myProductivity',
            'todayStats',
            'weekStats',
            'monthStats',
            'avgProcessingTime',
            'fastestProcessing',
            'slowestProcessing',
            'studentRequestStats',
            'onsiteRequestStats',
            'studentDocumentDistribution',
            'onsiteDocumentDistribution'
        ));
    }

    /**
     * Calculate processing time for current registrar
     */
    private function calculateMyProcessingTime($userId)
    {
        // Since student_requests don't have assigned_registrar_id, 
        // we'll use onsite_requests for registrar-specific metrics
        $completedRequests = \App\Models\OnsiteRequest::where('assigned_registrar_id', $userId)
            ->where('status', 'completed')
            ->whereNotNull('updated_at')
            ->whereNotNull('created_at')
            ->get();
        
        if ($completedRequests->count() === 0) {
            return ['hours' => 0, 'minutes' => 0, 'formatted' => '0 minutes'];
        }
        
        $totalMinutes = 0;
        foreach ($completedRequests as $request) {
            $totalMinutes += $request->created_at->diffInMinutes($request->updated_at);
        }
        
        $avgMinutes = $totalMinutes / $completedRequests->count();
        $hours = floor($avgMinutes / 60);
        $minutes = $avgMinutes % 60;
        
        return [
            'hours' => $hours,
            'minutes' => round($minutes),
            'formatted' => $hours > 0 ? "{$hours} hours " . round($minutes) . " minutes" : round($minutes) . " minutes"
        ];
    }

    /**
     * Get productivity metrics for current registrar
     */
    private function getMyProductivity($userId)
    {
        $today = Carbon::today();
        $thisWeek = Carbon::now()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();
        
        return [
            'today' => \App\Models\OnsiteRequest::where('assigned_registrar_id', $userId)
                ->where('status', 'completed')
                ->whereDate('updated_at', $today)
                ->count(),
            'this_week' => \App\Models\OnsiteRequest::where('assigned_registrar_id', $userId)
                ->where('status', 'completed')
                ->where('updated_at', '>=', $thisWeek)
                ->count(),
            'this_month' => \App\Models\OnsiteRequest::where('assigned_registrar_id', $userId)
                ->where('status', 'completed')
                ->where('updated_at', '>=', $thisMonth)
                ->count(),
            'total' => \App\Models\OnsiteRequest::where('assigned_registrar_id', $userId)
                ->where('status', 'completed')
                ->count(),
        ];
    }

    public function getQueueInProgress()
    {
        try {
            // Get student requests that are in queue, waiting, or ready for pickup
            $studentRequests = StudentRequest::with(['student.user'])
                ->whereIn('status', ['ready_for_release', 'ready_for_pickup', 'in_queue', 'waiting'])
                ->whereNotNull('queue_number')
                ->orderByRaw("CASE 
                    WHEN status = 'ready_for_pickup' THEN 1 
                    WHEN status = 'in_queue' THEN 2
                    WHEN status = 'ready_for_release' THEN 3 
                    WHEN status = 'waiting' THEN 4
                    ELSE 5 
                END")
                ->orderBy('queue_number')
                ->limit(15)
                ->get();

            // Get onsite requests that are in queue, waiting, or ready for pickup
            $onsiteRequests = \App\Models\OnsiteRequest::whereIn('status', ['released', 'in_queue', 'ready_for_pickup', 'waiting'])
                ->whereNotNull('queue_number')
                ->orderByRaw("CASE 
                    WHEN status = 'ready_for_pickup' THEN 1 
                    WHEN status = 'in_queue' THEN 2 
                    WHEN status = 'released' THEN 3 
                    WHEN status = 'waiting' THEN 4
                    ELSE 5 
                END")
                ->orderBy('queue_number')
                ->limit(15)
                ->get();

            return response()->json([
                'success' => true,
                'student_requests' => $studentRequests,
                'onsite_requests' => $onsiteRequests,
                'total_count' => $studentRequests->count() + $onsiteRequests->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load queue data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
