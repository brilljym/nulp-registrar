<?php

namespace App\Http\Controllers;

use App\Models\Window;
use App\Models\OnsiteRequest;
use App\Models\StudentRequest;
use Illuminate\Http\Request;

class WindowController extends Controller
{
    /**
     * Display all windows and their assigned requests (only in "window" step).
     */
    public function index()
    {
        $windows = Window::with([
            'assignedRequest' => function ($query) {
                $query->where('current_step', 'window');
            }
        ])->get();

        return view('registrar.windows', compact('windows'));
    }

    /**
     * Display queue status with windows for In Queue, Ready for Pickup, and Waiting
     */
    public function queueDisplay()
    {
        // Get all kiosk-entered requests (those that can be entered in kiosk)
        // Only include kiosk processing statuses: in_queue and waiting
        // Note: ready_for_release is for onsite processing and should not appear in queue display
        $kioskEnteredStatuses = [
            'student' => ['in_queue', 'waiting'],
            'onsite' => ['in_queue', 'waiting']
        ];

        // Get all student requests that can be entered in kiosk
        $allStudentRequests = StudentRequest::whereIn('status', $kioskEnteredStatuses['student'])
            ->with(['student.user', 'requestItems.document', 'window', 'assignedRegistrar'])
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($request) {
                return [
                    'id' => $request->id,
                    'type' => 'student',
                    'queue_number' => $request->queue_number,
                    'name' => $request->student && $request->student->user ? $request->student->user->first_name . ' ' . $request->student->user->last_name : 'N/A',
                    'student_id' => $request->student->student_id ?? 'N/A',
                    'documents' => $request->requestItems->pluck('document.type_document')->join(', '),
                    'created_at' => $request->created_at,
                    'status' => $request->status,
                    'assigned_registrar_id' => $request->assigned_registrar_id,
                    'window_assignment' => $this->getWindowAssignment($request)
                ];
            });

        // Get all onsite requests that can be entered in kiosk
        $allOnsiteRequests = OnsiteRequest::whereIn('status', $kioskEnteredStatuses['onsite'])
            ->with(['student.user', 'requestItems.document', 'window', 'registrar'])
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($request) {
                return [
                    'id' => $request->id,
                    'type' => 'onsite',
                    'queue_number' => $request->queue_number,
                    'kiosk_number' => $request->ref_code, // Add kiosk number for display
                    'name' => $request->full_name ?? ($request->student && $request->student->user ? $request->student->user->first_name . ' ' . $request->student->user->last_name : 'N/A'),
                    'student_id' => $request->student_id ?? 'N/A',
                    'documents' => $request->requestItems->pluck('document.type_document')->join(', '),
                    'created_at' => $request->created_at,
                    'status' => $request->status,
                    'assigned_registrar_id' => $request->assigned_registrar_id,
                    'window_assignment' => $this->getWindowAssignment($request)
                ];
            });

        // Combine all kiosk-entered requests
        $allKioskRequests = collect()
            ->merge($allStudentRequests)
            ->merge($allOnsiteRequests)
            ->sortBy('created_at')
            ->values();

        // Categorize requests based on registrar workload:
        // - In Queue: requests that are actively being processed (first request per registrar)
        // - Waiting: requests that are assigned to registrars who already have active requests
        $inQueueRequests = collect();
        $waitingRequests = collect();

        // Group requests by assigned registrar
        $requestsByRegistrar = $allKioskRequests->groupBy('assigned_registrar_id');

        foreach ($requestsByRegistrar as $registrarId => $registrarRequests) {
            if ($registrarId) {
                // Sort requests by creation time for this registrar
                $sortedRequests = $registrarRequests->sortBy('created_at');

                // First request for this registrar goes to "In Queue" if it's in_queue status
                $firstRequest = $sortedRequests->first();
                if ($firstRequest && $firstRequest['status'] === 'in_queue') {
                    $inQueueRequests->push($firstRequest);
                } elseif ($firstRequest && in_array($firstRequest['status'], ['in_queue', 'waiting'])) {
                    // If first request is waiting, it should be in queue
                    $inQueueRequests->push($firstRequest);
                }

                // All subsequent requests for this registrar go to "Waiting"
                $remainingRequests = $sortedRequests->skip(1);
                $waitingRequests = $waitingRequests->merge($remainingRequests);
            } else {
                // Requests not assigned to any registrar go to waiting
                $waitingRequests = $waitingRequests->merge($registrarRequests);
            }
        }

        // Handle unassigned requests (no registrar assigned) - put them in waiting
        $unassignedRequests = $allKioskRequests->where('assigned_registrar_id', null);
        $waitingRequests = $waitingRequests->merge($unassignedRequests);

        // Sort all waiting requests by creation time and assign sequential positions
        $waitingRequests = $waitingRequests->sortBy('created_at')->values()->map(function ($request, $index) {
            $request['position'] = $index + 1; // Position 1, 2, 3, etc. for all waiting requests
            return $request;
        });

        // For Ready for Pickup, keep the current logic (requests ready for pickup)
        $readyForPickupRequests = collect()
            ->merge(StudentRequest::where('status', 'ready_for_pickup')
                ->with(['student.user', 'requestItems.document', 'window', 'assignedRegistrar'])
                ->orderBy('created_at', 'asc')
                ->limit(3)
                ->get()
                ->map(function ($request) {
                    return [
                        'id' => $request->id,
                        'type' => 'student',
                        'queue_number' => $request->queue_number,
                        'name' => $request->student && $request->student->user ? $request->student->user->first_name . ' ' . $request->student->user->last_name : 'N/A',
                        'student_id' => $request->student->student_id ?? 'N/A',
                        'documents' => $request->requestItems->pluck('document.type_document')->join(', '),
                        'created_at' => $request->created_at,
                        'status' => $request->status,
                        'window_assignment' => $this->getWindowAssignment($request)
                    ];
                }))
            ->merge(OnsiteRequest::where('status', 'ready_for_pickup')
                ->with(['student.user', 'requestItems.document', 'window', 'registrar'])
                ->orderBy('created_at', 'asc')
                ->limit(3)
                ->get()
                ->map(function ($request) {
                    return [
                        'id' => $request->id,
                        'type' => 'onsite',
                        'queue_number' => $request->queue_number,
                        'name' => $request->full_name ?? ($request->student && $request->student->user ? $request->student->user->first_name . ' ' . $request->student->user->last_name : 'N/A'),
                        'student_id' => $request->student_id ?? 'N/A',
                        'documents' => $request->requestItems->pluck('document.type_document')->join(', '),
                        'created_at' => $request->created_at,
                        'status' => $request->status,
                        'window_assignment' => $this->getWindowAssignment($request)
                    ];
                }))
            ->sortBy('created_at')
            ->take(3)
            ->values();

        return view('queue.display', compact('inQueueRequests', 'readyForPickupRequests', 'waitingRequests'));
    }

    /**
     * Get window assignment for a request (based on assigned registrar's window)
     */
    private function getWindowAssignment($request)
    {
        // Priority 1: If request has an assigned registrar, get their window
        if ($request->assigned_registrar_id) {
            $registrar = \App\Models\Registrar::where('user_id', $request->assigned_registrar_id)->first();
            if ($registrar && $registrar->window_number) {
                return 'Window ' . $registrar->window_number;
            }
        }

        // Priority 2: If request has an assigned window, use it
        if ($request->window_id) {
            $window = \App\Models\Window::find($request->window_id);
            if ($window) {
                return $window->name;
            }
        }

        // Fallback to queue number calculation if no assignment
        if ($request->queue_number) {
            $letter = substr($request->queue_number, 0, 1);
            $windowNumber = (ord($letter) - ord('A')) % 3 + 1;
            return 'Window ' . $windowNumber;
        }

        return 'Window TBD';
    }

    /**
     * Get the number of available registrar slots
     */
    private function getAvailableRegistrarSlots()
    {
        // Get all available registrars/windows
        $availableRegistrars = \App\Models\Registrar::whereHas('user.role', function($query) {
            $query->where('name', 'registrar');
        })->get();
        
        $totalAvailableWindows = $availableRegistrars->count();
        
        if ($totalAvailableWindows === 0) {
            return 0; // No registrars available
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
        
        // Return available slots
        return $totalAvailableWindows - $occupiedWindows;
    }

    /**
     * Toggle the window's occupied status (e.g., Free <-> Occupied).
     */
    public function toggleOccupied(Window $window)
    {
        $window->update([
            'is_occupied' => !$window->is_occupied,
        ]);

        return redirect()->route('registrar.windows')->with('success', 'Window status updated.');
    }
}
