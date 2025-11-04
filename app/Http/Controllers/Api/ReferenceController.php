<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StudentRequest;
use App\Models\OnsiteRequest;
use App\Events\QueuePlacementConfirmed;
use Illuminate\Http\Request;
use App\Services\QueueService;
use Illuminate\Support\Facades\Log;

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
    public function getTransactionByReference(Request $request, $reference)
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

        // Update player_id if provided
        if ($request->has('player_id') && $request->player_id) {
            $studentRequest->update(['player_id' => $request->player_id]);
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

        // Calculate position if status is waiting or in_queue
        $position = 0;
        $displayStatus = $studentRequest->status;
        
        // For in_queue status, check if this is the first request or waiting
        if ($studentRequest->status === 'in_queue' && $studentRequest->assignedRegistrar) {
            // Get all in_queue requests for this registrar
            $registrarRequests = StudentRequest::where('assigned_registrar_id', $studentRequest->assignedRegistrar->id)
                ->whereIn('status', ['in_queue', 'waiting'])
                ->orderBy('created_at', 'asc')
                ->get();
            
            Log::info("API Debug - Reference: {$reference}, Status: {$studentRequest->status}, Registrar: {$studentRequest->assignedRegistrar->id}, Total requests: {$registrarRequests->count()}");
            
            // If this is not the first request, it's actually waiting
            if ($registrarRequests->isNotEmpty() && $registrarRequests->first()->id !== $studentRequest->id) {
                $displayStatus = 'waiting';
                $position = $registrarRequests->search(function($req) use ($studentRequest) {
                    return $req->id === $studentRequest->id;
                }) + 1; // Position in queue (1-based)
                Log::info("API Debug - Reference: {$reference}, Changed status to 'waiting', Position: {$position}");
            } else {
                Log::info("API Debug - Reference: {$reference}, Keeping status 'in_queue' (first in queue)");
            }
        } elseif ($studentRequest->status === 'waiting' && $studentRequest->assignedRegistrar) {
            $position = $this->queueService->getWaitingPositionForStudentRequest($studentRequest);
            $displayStatus = 'waiting';
            Log::info("API Debug - Reference: {$reference}, Status is 'waiting', Position: {$position}");
        }

        return response()->json([
            'id' => $studentRequest->id,
            'reference_no' => $studentRequest->reference_no,
            'student_name' => $studentName,
            'student_id' => $studentRequest->student->student_id ?? null,
            'document_name' => $documentName, // For backward compatibility
            'documents' => $documents, // New field with all documents
            'total_cost' => $studentRequest->total_cost,
            'status' => $displayStatus, // Use display status instead of raw status
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
    public function getOnsiteRequestByReference(Request $httpRequest, $refCode)
    {
        $request = OnsiteRequest::with(['document', 'window', 'registrar'])
            ->where('ref_code', $refCode)
            ->first();

        if (!$request) {
            return response()->json(['message' => 'Onsite request not found'], 404);
        }

        // Update player_id if provided
        if ($httpRequest->has('player_id') && $httpRequest->player_id) {
            $request->update(['player_id' => $httpRequest->player_id]);
        }

        // Calculate position if status is waiting or in_queue
        $position = 0;
        $displayStatus = $request->status;
        
        // For in_queue status, check if this is the first request or waiting
        if ($request->status === 'in_queue' && $request->assigned_registrar_id) {
            // Get all in_queue/waiting requests for this registrar
            $registrarRequests = OnsiteRequest::where('assigned_registrar_id', $request->assigned_registrar_id)
                ->whereIn('status', ['in_queue', 'waiting'])
                ->orderBy('created_at', 'asc')
                ->get();
            
            Log::info("API Debug - Onsite Reference: {$refCode}, Status: {$request->status}, Registrar: {$request->assigned_registrar_id}, Total requests: {$registrarRequests->count()}");
            
            // If this is not the first request, it's actually waiting
            if ($registrarRequests->isNotEmpty() && $registrarRequests->first()->id !== $request->id) {
                $displayStatus = 'waiting';
                $position = $registrarRequests->search(function($req) use ($request) {
                    return $req->id === $request->id;
                }) + 1; // Position in queue (1-based)
                Log::info("API Debug - Onsite Reference: {$refCode}, Changed status to 'waiting', Position: {$position}");
            } else {
                Log::info("API Debug - Onsite Reference: {$refCode}, Keeping status 'in_queue' (first in queue)");
            }
        } elseif ($request->status === 'waiting' && $request->assigned_registrar_id) {
            $position = $this->queueService->getWaitingPositionForRequest($request);
            $displayStatus = 'waiting';
            Log::info("API Debug - Onsite Reference: {$refCode}, Status is 'waiting', Position: {$position}");
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
     * Get a specific onsite request by queue number
     * Also updates status to "in_queue" when accessed (check-in functionality)
     */
    public function getKioskRequest(Request $request, $queueNumber)
    {
        // Look for student requests by queue number first
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

        if ($studentRequest) {
            // Update player_id if provided
            if ($request->has('player_id') && $request->player_id) {
                $studentRequest->update(['player_id' => $request->player_id]);
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

            // Calculate position if status is waiting or in_queue
            $position = 0;
            $displayStatus = $studentRequest->status;
            
            // For in_queue status, check if this is the first request or waiting
            if ($studentRequest->status === 'in_queue' && $studentRequest->assignedRegistrar) {
                // Get all in_queue/waiting requests for this registrar
                $registrarRequests = StudentRequest::where('assigned_registrar_id', $studentRequest->assignedRegistrar->id)
                    ->whereIn('status', ['in_queue', 'waiting'])
                    ->orderBy('created_at', 'asc')
                    ->get();
                
                // If this is not the first request, it's actually waiting
                if ($registrarRequests->isNotEmpty() && $registrarRequests->first()->id !== $studentRequest->id) {
                    $displayStatus = 'waiting';
                    $position = $registrarRequests->search(function($req) use ($studentRequest) {
                        return $req->id === $studentRequest->id;
                    }) + 1; // Position in queue (1-based)
                }
            } elseif ($studentRequest->status === 'waiting' && $studentRequest->assignedRegistrar) {
                $position = $this->queueService->getWaitingPositionForStudentRequest($studentRequest);
                $displayStatus = 'waiting';
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
                'status' => $displayStatus, // Use display status instead of raw status
                'current_step' => $this->mapStatusToStep($displayStatus),
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

        // If not a student request, check onsite requests
        $onsiteRequest = OnsiteRequest::with(['requestItems.document', 'assignedWindow', 'registrar'])
            ->where('queue_number', $queueNumber)
            ->whereIn('status', $acceptableStatuses)
            ->first();

        if (!$onsiteRequest) {
            return response()->json(['message' => 'Queue request not found'], 404);
        }

        // Update player_id if provided
        if ($request->has('player_id') && $request->player_id) {
            $onsiteRequest->update(['player_id' => $request->player_id]);
        }

        // Check-in functionality: Update status to "in_queue" if it's not already in_queue, processing, or ready_for_release
        $statusesThatShouldBecomeInQueue = ['accepted', 'pending', 'waiting', 'completed'];
        if (in_array($onsiteRequest->status, $statusesThatShouldBecomeInQueue)) {
            $oldStatus = $onsiteRequest->status;
            $onsiteRequest->update(['status' => 'in_queue']);
            $onsiteRequest->refresh(); // Refresh to get updated data
            
            // Broadcast queue update event for real-time display
            event(new QueuePlacementConfirmed(
                $onsiteRequest, 
                'onsite', 
                'checkin', 
                "Queue number {$queueNumber} checked in from kiosk (status changed from {$oldStatus} to in_queue)"
            ));
        }

        $studentName = $onsiteRequest->full_name;

        // Get all documents for this request
        $documents = $onsiteRequest->requestItems->map(function ($item) use ($onsiteRequest) {
            return [
                'name' => $item->document->type_document ?? 'Unknown Document',
                'quantity' => $item->quantity,
                'price' => $item->price ?? 0,
                'queue_number' => $onsiteRequest->queue_number
            ];
        });

        // For backward compatibility, return the first document name as document_name
        $firstDocument = $onsiteRequest->requestItems->first();
        $documentName = $firstDocument ? ($firstDocument->document->type_document ?? 'Unknown Document') : 'Unknown Document';

        // Calculate position if status is waiting or in_queue
        $position = 0;
        $displayStatus = $onsiteRequest->status;
        
        // For in_queue status, check if this is the first request or waiting
        if ($onsiteRequest->status === 'in_queue' && $onsiteRequest->assigned_registrar_id) {
            // Get all in_queue/waiting requests for this registrar
            $registrarRequests = OnsiteRequest::where('assigned_registrar_id', $onsiteRequest->assigned_registrar_id)
                ->whereIn('status', ['in_queue', 'waiting'])
                ->orderBy('created_at', 'asc')
                ->get();
            
            // If this is not the first request, it's actually waiting
            if ($registrarRequests->isNotEmpty() && $registrarRequests->first()->id !== $onsiteRequest->id) {
                $displayStatus = 'waiting';
                $position = $registrarRequests->search(function($req) use ($onsiteRequest) {
                    return $req->id === $onsiteRequest->id;
                }) + 1; // Position in queue (1-based)
            }
        } elseif ($onsiteRequest->status === 'waiting' && $onsiteRequest->registrar) {
            $position = $this->queueService->getWaitingPositionForRequest($onsiteRequest);
            $displayStatus = 'waiting';
        }

        return response()->json([
            'id' => $onsiteRequest->id,
            'ref_code' => $onsiteRequest->ref_code,
            'queue_number' => $onsiteRequest->queue_number,
            'kiosk_number' => $onsiteRequest->queue_number, // Alias for frontend compatibility
            'full_name' => $studentName,
            'student_id' => $onsiteRequest->student_id,
            'course' => $onsiteRequest->course ?? 'Not specified',
            'year_level' => $onsiteRequest->year_level ?? 'Not specified',
            'department' => $onsiteRequest->department ?? 'Not specified',
            'document_name' => $documentName, // For backward compatibility
            'documents' => $documents, // New field with documents array
            'quantity' => $onsiteRequest->requestItems->sum('quantity'),
            'reason' => $onsiteRequest->reason,
            'status' => $displayStatus, // Use display status instead of raw status
            'current_step' => $this->mapStatusToStep($displayStatus),
            'position' => $position, // Position in waiting queue
            'window_name' => $onsiteRequest->assignedWindow ? $onsiteRequest->assignedWindow->name : null,
            'registrar_name' => $onsiteRequest->registrar ?
                trim(($onsiteRequest->registrar->first_name ?? '') . ' ' . ($onsiteRequest->registrar->last_name ?? '')) : null,
            'expected_release_date' => $onsiteRequest->expected_release_date ?
                $onsiteRequest->expected_release_date->toISOString() : null,
            'created_at' => $onsiteRequest->created_at,
            'updated_at' => $onsiteRequest->updated_at,
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
