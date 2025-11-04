<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StudentRequest;
use App\Models\OnsiteRequest;
use App\Events\QueuePlacementConfirmed;
use Illuminate\Http\Request;
use App\Services\QueueService;

class ReferenceController extends Controller
{
    protected $queueService;

    public function __construct(QueueService $queueService)
    {
        $this->queueService = $queueService;
    }
    /**
     * Search for student requests by reference number
     */
    public function searchTransactions(Request $request)
    {
        $reference = $request->get('reference');
        
        if (!$reference || strlen($reference) < 1) {
            return response()->json([], 200);
        }

        // Only return student requests that are ready to be processed or completed
        $acceptableStatuses = [
            'processing',
            'ready_for_release', 
            'completed',
            'pending' // Include pending as they can be tracked
        ];

        $studentRequests = StudentRequest::with(['student.user', 'requestItems.document', 'assignedRegistrar'])
            ->where('reference_no', 'like', "%$reference%")
            ->whereIn('status', $acceptableStatuses)
            ->limit(10)
            ->get()
            ->map(function ($studentRequest) {
                $studentName = '';
                if ($studentRequest->student && $studentRequest->student->user) {
                    $studentName = trim(
                        ($studentRequest->student->user->first_name ?? '') . ' ' . 
                        ($studentRequest->student->user->last_name ?? '')
                    );
                }

                // Get all documents for this request
                $documents = $studentRequest->requestItems->map(function ($item) {
                    return [
                        'name' => $item->document->type_document ?? 'Unknown Document',
                        'quantity' => $item->quantity,
                        'price' => $item->price
                    ];
                });

                return [
                    'id' => $studentRequest->id,
                    'reference_no' => $studentRequest->reference_no,
                    'student_name' => $studentName,
                    'student_id' => $studentRequest->student->student_id ?? null,
                    'documents' => $documents,
                    'total_cost' => $studentRequest->total_cost,
                    'status' => $studentRequest->status,
                    'expected_release_date' => $studentRequest->expected_release_date ? 
                        $studentRequest->expected_release_date->toISOString() : null,
                    'created_at' => $studentRequest->created_at,
                ];
            });

        return response()->json($studentRequests);
    }

    /**
     * Search for onsite requests by reference code
     */
    public function searchOnsiteRequests(Request $request)
    {
        $refCode = $request->get('ref_code');
        
        if (!$refCode || strlen($refCode) < 1) {
            return response()->json([], 200);
        }

        $requests = OnsiteRequest::with(['document', 'window', 'registrar'])
            ->where('ref_code', 'like', "%$refCode%")
            ->limit(10)
            ->get()
            ->map(function ($onsiteRequest) {
                return [
                    'id' => $onsiteRequest->id,
                    'ref_code' => $onsiteRequest->ref_code,
                    'full_name' => $onsiteRequest->full_name,
                    'student_id' => $onsiteRequest->student_id,
                    'course' => $onsiteRequest->course,
                    'year_level' => $onsiteRequest->year_level,
                    'department' => $onsiteRequest->department,
                    'document_name' => $onsiteRequest->document->type_document ?? null,
                    'quantity' => $onsiteRequest->quantity,
                    'reason' => $onsiteRequest->reason,
                    'status' => $onsiteRequest->status,
                    'current_step' => $onsiteRequest->current_step,
                    'window_name' => $onsiteRequest->window->name ?? null,
                    'registrar_name' => $onsiteRequest->registrar ? 
                        trim(($onsiteRequest->registrar->first_name ?? '') . ' ' . ($onsiteRequest->registrar->last_name ?? '')) : null,
                    'expected_release_date' => $onsiteRequest->expected_release_date ? 
                        $onsiteRequest->expected_release_date->toISOString() : null,
                    'created_at' => $onsiteRequest->created_at,
                ];
            });

        return response()->json($requests);
    }

    /**
     * Get a specific student request by reference number
     */
    public function getTransactionByReference($reference)
    {
        // Only return student requests that are ready to be processed or completed
        $acceptableStatuses = [
            'accepted',      // Request has been accepted and is ready for processing
            'pending',       // Include pending as they can be tracked
            'in_queue',      // Request is in the queue waiting to be processed
            'processing',    // Currently being processed
            'ready_for_release', // Ready for pickup
            'ready_for_pickup',  // Alternative status name
            'completed',     // Fully completed
            'waiting',       // Waiting in queue
            'released'       // Document has been released
        ];

        $studentRequest = StudentRequest::with(['student.user', 'requestItems.document', 'assignedRegistrar'])
            ->where('reference_no', $reference)
            ->whereIn('status', $acceptableStatuses)
            ->first();

        if (!$studentRequest) {
            return response()->json(['message' => 'Student request not found'], 404);
        }

        $studentName = '';
        if ($studentRequest->student && $studentRequest->student->user) {
            $studentName = trim(
                ($studentRequest->student->user->first_name ?? '') . ' ' . 
                ($studentRequest->student->user->last_name ?? '')
            );
        }

        // Get all documents for this request
        $documents = $studentRequest->requestItems->map(function ($item) use ($studentRequest) {
            return [
                'name' => $item->document->type_document ?? 'Unknown Document',
                'quantity' => $item->quantity,
                'price' => $item->price,
                'queue_number' => $studentRequest->queue_number
            ];
        });

        // For backward compatibility, return the first document name as document_name
        $firstDocument = $studentRequest->requestItems->first();
        $documentName = $firstDocument ? ($firstDocument->document->type_document ?? 'Unknown Document') : 'Unknown Document';

        // Calculate position if status is waiting
        $position = 0;
        if ($studentRequest->status === 'waiting' && $studentRequest->assignedRegistrar) {
            $position = $this->queueService->getWaitingPositionForStudentRequest($studentRequest);
        }

        return response()->json([
            'id' => $studentRequest->id,
            'reference_no' => $studentRequest->reference_no,
            'student_name' => $studentName,
            'student_id' => $studentRequest->student->student_id ?? null,
            'document_name' => $documentName, // For backward compatibility
            'documents' => $documents, // New field with all documents
            'total_cost' => $studentRequest->total_cost,
            'status' => $studentRequest->status,
            'queue_number' => $studentRequest->queue_number,
            'position' => $position, // Position in waiting queue
            'registrar_name' => $studentRequest->assignedRegistrar ? 
                trim(($studentRequest->assignedRegistrar->first_name ?? '') . ' ' . ($studentRequest->assignedRegistrar->last_name ?? '')) : null,
            'expected_release_date' => $studentRequest->expected_release_date ? 
                $studentRequest->expected_release_date->toISOString() : null,
            'created_at' => $studentRequest->created_at,
            'updated_at' => $studentRequest->updated_at,
        ]);
    }

    /**
     * Get a specific onsite request by reference code
     */
    public function getOnsiteRequestByReference($refCode)
    {
        $request = OnsiteRequest::with(['document', 'window', 'registrar'])
            ->where('ref_code', $refCode)
            ->first();

        if (!$request) {
            return response()->json(['message' => 'Onsite request not found'], 404);
        }

        // Calculate position if status is waiting
        $position = 0;
        if ($request->status === 'waiting' && $request->assigned_registrar_id) {
            $position = $this->queueService->getWaitingPositionForRequest($request);
        }

        return response()->json([
            'id' => $request->id,
            'ref_code' => $request->ref_code,
            'full_name' => $request->full_name,
            'student_id' => $request->student_id,
            'course' => $request->course,
            'year_level' => $request->year_level,
            'department' => $request->department,
            'document_name' => $request->document->type_document ?? null, // For backward compatibility
            'documents' => [[ // New field with documents array
                'name' => $request->document->type_document ?? 'Unknown Document',
                'quantity' => $request->quantity,
                'queue_number' => $request->queue_number
            ]],
            'quantity' => $request->quantity,
            'reason' => $request->reason,
            'status' => $request->status,
            'current_step' => $request->current_step,
            'queue_number' => $request->queue_number,
            'position' => $position, // Position in waiting queue
            'window_name' => $request->window->name ?? null,
            'registrar_name' => $request->registrar ? 
                trim(($request->registrar->first_name ?? '') . ' ' . ($request->registrar->last_name ?? '')) : null,
            'expected_release_date' => $request->expected_release_date ? 
                $request->expected_release_date->toISOString() : null,
            'created_at' => $request->created_at,
            'updated_at' => $request->updated_at,
        ]);
    }

    /**
     * Get a specific onsite request by queue number (instead of kiosk number)
     * Also updates status to "in_queue" when accessed (check-in functionality)
     */
    public function getKioskRequest($queueNumber)
    {
        // Look for student requests by queue number instead of onsite requests
        $acceptableStatuses = [
            'accepted',
            'pending',
            'in_queue',
            'processing',
            'ready_for_release',
            'ready_for_pickup',
            'completed',
            'waiting',
            'released'
        ];

        $studentRequest = StudentRequest::with(['student.user', 'requestItems.document', 'assignedRegistrar'])
            ->where('queue_number', $queueNumber)
            ->whereIn('status', $acceptableStatuses)
            ->first();

        if (!$studentRequest) {
            return response()->json(['message' => 'Queue request not found'], 404);
        }

        // Check-in functionality: Update status to "in_queue" if it's not already in_queue, processing, or ready_for_release
        $statusesThatShouldBecomeInQueue = ['accepted', 'pending', 'waiting', 'completed'];
        if (in_array($studentRequest->status, $statusesThatShouldBecomeInQueue)) {
            $oldStatus = $studentRequest->status;
            $studentRequest->update(['status' => 'in_queue']);
            $studentRequest->refresh(); // Refresh to get updated data
            
            // Broadcast queue update event for real-time display
            event(new QueuePlacementConfirmed(
                $studentRequest, 
                'student', 
                'checkin', 
                "Queue number {$queueNumber} checked in from kiosk (status changed from {$oldStatus} to in_queue)"
            ));
        }

        $studentName = '';
        if ($studentRequest->student && $studentRequest->student->user) {
            $studentName = trim(
                ($studentRequest->student->user->first_name ?? '') . ' ' .
                ($studentRequest->student->user->last_name ?? '')
            );
        }

        // Get all documents for this request
        $documents = $studentRequest->requestItems->map(function ($item) use ($studentRequest) {
            return [
                'name' => $item->document->type_document ?? 'Unknown Document',
                'quantity' => $item->quantity,
                'price' => $item->price,
                'queue_number' => $studentRequest->queue_number
            ];
        });

        // For backward compatibility, return the first document name as document_name
        $firstDocument = $studentRequest->requestItems->first();
        $documentName = $firstDocument ? ($firstDocument->document->type_document ?? 'Unknown Document') : 'Unknown Document';

        // Calculate position if status is waiting
        $position = 0;
        if ($studentRequest->status === 'waiting' && $studentRequest->assignedRegistrar) {
            $position = $this->queueService->getWaitingPositionForStudentRequest($studentRequest);
        }

        return response()->json([
            'id' => $studentRequest->id,
            'ref_code' => $studentRequest->reference_no, // Use reference_no as ref_code
            'queue_number' => $studentRequest->queue_number, // Now the primary identifier
            'kiosk_number' => $studentRequest->queue_number, // Alias for frontend compatibility
            'full_name' => $studentName,
            'student_id' => $studentRequest->student->student_id ?? null,
            'course' => $studentRequest->student->course ?? 'Not specified',
            'year_level' => $studentRequest->student->year_level ?? 'Not specified',
            'department' => $studentRequest->student->department ?? 'Not specified',
            'document_name' => $documentName, // For backward compatibility
            'documents' => $documents, // New field with documents array
            'quantity' => $studentRequest->requestItems->sum('quantity'),
            'reason' => $studentRequest->reason,
            'status' => $studentRequest->status,
            'current_step' => $this->mapStatusToStep($studentRequest->status),
            'position' => $position, // Position in waiting queue
            'window_name' => null, // Student requests don't have windows assigned yet
            'registrar_name' => $studentRequest->assignedRegistrar ?
                trim(($studentRequest->assignedRegistrar->first_name ?? '') . ' ' . ($studentRequest->assignedRegistrar->last_name ?? '')) : null,
            'expected_release_date' => $studentRequest->expected_release_date ?
                $studentRequest->expected_release_date->toISOString() : null,
            'created_at' => $studentRequest->created_at,
            'updated_at' => $studentRequest->updated_at,
        ]);
    }

    /**
     * Map student request status to current step
     */
    private function mapStatusToStep($status)
    {
        return match($status) {
            'pending' => 'payment_pending',
            'in_queue' => 'in_queue',
            'processing' => 'processing',
            'ready_for_release' => 'ready_for_release',
            'ready_for_pickup' => 'ready_for_pickup',
            'completed' => 'completed',
            'accepted' => 'accepted',
            'waiting' => 'waiting',
            'released' => 'released',
            default => 'unknown'
        };
    }

    /**
     * Debug endpoint to see what student requests and statuses exist
     */
    public function debugTransactions()
    {
        $studentRequests = StudentRequest::select('reference_no', 'status', 'created_at')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $statuses = StudentRequest::distinct('status')
            ->pluck('status')
            ->toArray();

        return response()->json([
            'total_student_requests' => StudentRequest::count(),
            'available_statuses' => $statuses,
            'recent_student_requests' => $studentRequests,
        ]);
    }
}
