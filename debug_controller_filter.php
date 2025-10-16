<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔍 Testing Controller Filtering Logic\n";
echo "=" . str_repeat("=", 50) . "\n\n";

// Simulate what happens when a registrar accesses the page
$registrars = App\Models\Registrar::with('user')->get();

foreach($registrars as $registrar) {
    $currentUser = $registrar->user;
    $currentWindowNumber = $registrar->window_number;
    
    echo "🪟 Testing for Registrar at Window {$currentWindowNumber} (User ID: {$currentUser->id}):\n";
    
    // Find the window ID that corresponds to this registrar's window number
    $assignedWindow = App\Models\Window::where('name', 'Window ' . $currentWindowNumber)->first();
    
    if ($assignedWindow) {
        echo "   Found Window: {$assignedWindow->name} (ID: {$assignedWindow->id})\n";
        
        // Check if window is currently occupied
        $currentRequest = App\Models\OnsiteRequest::where('window_id', $assignedWindow->id)
            ->where('assigned_registrar_id', $currentUser->id)
            ->whereIn('status', ['processing', 'in_queue', 'ready_for_pickup'])
            ->whereIn('current_step', ['processing', 'window'])
            ->first();
        
        $isWindowOccupied = $currentRequest !== null;
        echo "   Window Occupied: " . ($isWindowOccupied ? 'YES' : 'NO') . "\n";
        
        // Apply the exact filtering logic from the FIXED controller
        $query = App\Models\OnsiteRequest::with(['window', 'registrar']);
        
        // DO NOT filter by window first - this was the bug!
        
        if ($isWindowOccupied && $currentRequest) {
            $query->where('id', $currentRequest->id);
        } else {
            // Show pending requests OR assigned requests (this is the correct logic)
            $query->where(function($q) use ($currentUser, $assignedWindow) {
                $q->where(function($subQ) {
                    // Show all pending requests that need approval
                    $subQ->where('status', 'pending')
                         ->whereNull('assigned_registrar_id')
                         ->whereNull('window_id');
                })
                ->orWhere(function($subQ) use ($currentUser, $assignedWindow) {
                    // Show requests already assigned to this registrar/window
                    $subQ->whereIn('status', ['registrar_approved', 'processing', 'in_queue', 'ready_for_pickup'])
                         ->where(function($innerQ) use ($currentUser, $assignedWindow) {
                             $innerQ->where('assigned_registrar_id', $currentUser->id);
                             if ($assignedWindow) {
                                 $innerQ->orWhere('window_id', $assignedWindow->id);
                             }
                         });
                });
            });
        }
        
        $requests = $query->orderByDesc('created_at')->get();
        
        echo "   Requests visible to this registrar:\n";
        if ($requests->count() > 0) {
            foreach($requests as $req) {
                echo "     - Request #{$req->id}: {$req->full_name} ({$req->status})\n";
            }
        } else {
            echo "     - No requests visible (This is the problem!)\n";
        }
    } else {
        echo "   ❌ No window found for 'Window {$currentWindowNumber}'\n";
    }
    echo "\n";
}

echo "🔍 The issue is with the filtering logic!\n";
echo "The controller is filtering by window_id or assigned_registrar_id first,\n";
echo "but pending requests have both as NULL, so they don't pass the first filter!\n";