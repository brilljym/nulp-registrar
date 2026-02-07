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
            $registrarRequests = null; // Initialize for debug info
            
            // For both in_queue and waiting status, calculate position matching web display logic
            if (in_array($studentRequest->status, ['in_queue', 'waiting'])) {
                // Get requests for THIS registrar only, sorted by creation time
                if ($studentRequest->assigned_registrar_id) {
                    $registrarRequests = StudentRequest::where('assigned_registrar_id', $studentRequest->assigned_registrar_id)
                        ->whereIn('status', ['in_queue', 'waiting'])
                        ->orderBy('created_at', 'asc')
                        ->get();
                    
                    // Check if this is the first request for this registrar
                    $isFirst = $registrarRequests->first()->id === $studentRequest->id;
                    
                    if ($isFirst) {
                        // First request is "in queue" being processed - position 0
                        $position = 0;
                        $displayStatus = 'in_queue';
                    } else {
                        // Calculate position among waiting requests for this registrar (excluding the first)
                        \$waitingForRegistrar = \$registrarRequests->skip(1)->values(); // Re-index after skip
                        $position = $waitingForRegistrar->search(function($req) use ($studentRequest) {
                            return $req->id === $studentRequest->id;
                        });
                        
                        // Convert from 0-based index to 1-based position
                        $position = $position !== false ? $position + 1 : 0;
                        $displayStatus = 'waiting';
                    }
                } else {
                    // No registrar assigned - just use simple position among all unassigned
                    $unassignedRequests = StudentRequest::whereNull('assigned_registrar_id')
                        ->whereIn('status', ['in_queue', 'waiting'])
                        ->orderBy('created_at', 'asc')
                        ->get();
                    
                    $position = $unassignedRequests->search(function($req) use ($studentRequest) {
                        return $req->id === $studentRequest->id;
                    });
                    
                    $position = $position !== false ? $position + 1 : 0;
                    $displayStatus = 'waiting';
                }
                
                // DEBUG: Log API position calculation
                \Log::info('API POSITION DEBUG (StudentRequest)', [
                    'reference_no' => $studentRequest->reference_no,
                    'queue_number' => $studentRequest->queue_number,
                    'status' => $studentRequest->status,
                    'display_status' => $displayStatus,
                    'assigned_registrar_id' => $studentRequest->assigned_registrar_id,
                    'created_at' => $studentRequest->created_at,
                    'total_for_registrar' => $registrarRequests ? $registrarRequests->count() : 0,
                    'is_first' => $registrarRequests ? ($registrarRequests->first()->id === $studentRequest->id) : null,
                    'final_position' => $position,
                    'registrar_queue_numbers' => $registrarRequests ? $registrarRequests->pluck('queue_number')->toArray() : []
                ]);
                
                // Get registrar-specific info for debug
                if ($studentRequest->assigned_registrar_id) {
                    $registrarRequests = StudentRequest::where('assigned_registrar_id', $studentRequest->assigned_registrar_id)
                        ->whereIn('status', ['in_queue', 'waiting'])
                        ->orderBy('created_at', 'asc')
                        ->get();
                }
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
            'debug_info' => [
                'raw_status' => $studentRequest->status,
                'display_status' => $displayStatus,
                'assigned_registrar_id' => $studentRequest->assigned_registrar_id,
                'position' => $position,
                'total_requests_for_registrar' => $registrarRequests ? $registrarRequests->count() : 0,
                'first_request_id' => $registrarRequests ? $registrarRequests->first()->id : null,
                'current_request_id' => $studentRequest->id,
                'is_first' => $registrarRequests ? ($registrarRequests->first()->id === $studentRequest->id) : null,
            ],
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
        $registrarRequests = null; // Initialize for debug info
        
        // For both in_queue and waiting status, calculate position matching web display logic
        if (in_array($request->status, ['in_queue', 'waiting'])) {
            // Get requests for THIS registrar only, sorted by creation time
            if ($request->assigned_registrar_id) {
                $registrarRequests = OnsiteRequest::where('assigned_registrar_id', $request->assigned_registrar_id)
                    ->whereIn('status', ['in_queue', 'waiting'])
                    ->orderBy('created_at', 'asc')
                    ->get();
                
                // Check if this is the first request for this registrar
                $isFirst = $registrarRequests->first()->id === $request->id;
                
                if ($isFirst) {
                    // First request is "in queue" being processed - position 0
                    $position = 0;
                    $displayStatus = 'in_queue';
                } else {
                    // Calculate position among waiting requests for this registrar (excluding the first)
                    $waitingForRegistrar = $registrarRequests->skip(1)->values(); // Re-index after skip
                    $position = $waitingForRegistrar->search(function($req) use ($request) {
                        return $req->id === $request->id;
                    });
                    
                    // Convert from 0-based index to 1-based position
                    $position = $position !== false ? $position + 1 : 0;
                    $displayStatus = 'waiting';
                }
            } else {
                // No registrar assigned - just use simple position among all unassigned
                $unassignedRequests = OnsiteRequest::whereNull('assigned_registrar_id')
                    ->whereIn('status', ['in_queue', 'waiting'])
                    ->orderBy('created_at', 'asc')
                    ->get();
                
                $position = $unassignedRequests->search(function($req) use ($request) {
                    return $req->id === $request->id;
                });
                
                $position = $position !== false ? $position + 1 : 0;
                $displayStatus = 'waiting';
            }
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
            'status' => $displayStatus, // Use display status instead of raw status
            'debug_info' => [
                'raw_status' => $request->status,
                'display_status' => $displayStatus,
                'assigned_registrar_id' => $request->assigned_registrar_id,
                'position' => $position,
                'total_requests_for_registrar' => $registrarRequests ? $registrarRequests->count() : 0,
                'first_request_id' => $registrarRequests ? $registrarRequests->first()->id : null,
                'current_request_id' => $request->id,
                'is_first' => $registrarRequests ? ($registrarRequests->first()->id === $request->id) : null,
            ],
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
            
            // For both in_queue and waiting status, calculate position matching web display logic
            if (in_array($studentRequest->status, ['in_queue', 'waiting'])) {
                // Get requests for THIS registrar only, sorted by creation time
                if ($studentRequest->assigned_registrar_id) {
                    $registrarRequests = StudentRequest::where('assigned_registrar_id', $studentRequest->assigned_registrar_id)
                        ->whereIn('status', ['in_queue', 'waiting'])
                        ->orderBy('created_at', 'asc')
                        ->get();
                    
                    // Check if this is the first request for this registrar
                    $isFirst = $registrarRequests->first()->id === $studentRequest->id;
                    
                    if ($isFirst) {
                        // First request is "in queue" being processed - position 0
                        $position = 0;
                        $displayStatus = 'in_queue';
                    } else {
                        // Calculate position among waiting requests for this registrar (excluding the first)
                        \$waitingForRegistrar = \$registrarRequests->skip(1)->values(); // Re-index after skip
                        $position = $waitingForRegistrar->search(function($req) use ($studentRequest) {
                            return $req->id === $studentRequest->id;
                        });
                        
                        // Convert from 0-based index to 1-based position
                        $position = $position !== false ? $position + 1 : 0;
                        $displayStatus = 'waiting';
                    }
                } else {
                    // No registrar assigned - just use simple position among all unassigned
                    $unassignedRequests = StudentRequest::whereNull('assigned_registrar_id')
                        ->whereIn('status', ['in_queue', 'waiting'])
                        ->orderBy('created_at', 'asc')
                        ->get();
                    
                    $position = $unassignedRequests->search(function($req) use ($studentRequest) {
                        return $req->id === $studentRequest->id;
                    });
                    
                    $position = $position !== false ? $position + 1 : 0;
                    $displayStatus = 'waiting';
                }
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
        
        // For both in_queue and waiting status, calculate position matching web display logic
        if (in_array($onsiteRequest->status, ['in_queue', 'waiting'])) {
            // Get requests for THIS registrar only, sorted by creation time
            if ($onsiteRequest->assigned_registrar_id) {
                $registrarRequests = OnsiteRequest::where('assigned_registrar_id', $onsiteRequest->assigned_registrar_id)
                    ->whereIn('status', ['in_queue', 'waiting'])
                    ->orderBy('created_at', 'asc')
                    ->get();
                
                // Check if this is the first request for this registrar
                $isFirst = $registrarRequests->first()->id === $onsiteRequest->id;
                
                if ($isFirst) {
                    // First request is "in queue" being processed - position 0
                    $position = 0;
                    $displayStatus = 'in_queue';
                } else {
                    // Calculate position among waiting requests for this registrar (excluding the first)
                    $waitingForRegistrar = $registrarRequests->skip(1)->values(); // Re-index after skip
                    $position = $waitingForRegistrar->search(function($req) use ($onsiteRequest) {
                        return $req->id === $onsiteRequest->id;
                    });
                    
                    // Convert from 0-based index to 1-based position
                    $position = $position !== false ? $position + 1 : 0;
                    $displayStatus = 'waiting';
                }
            } else {
                // No registrar assigned - just use simple position among all unassigned
                $unassignedRequests = OnsiteRequest::whereNull('assigned_registrar_id')
                    ->whereIn('status', ['in_queue', 'waiting'])
                    ->orderBy('created_at', 'asc')
                    ->get();
                
                $position = $unassignedRequests->search(function($req) use ($onsiteRequest) {
                    return $req->id === $onsiteRequest->id;
                });
                
                $position = $position !== false ? $position + 1 : 0;
                $displayStatus = 'waiting';
            }
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
