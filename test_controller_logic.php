<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔍 Testing Complete Controller Logic Simulation\n";
echo "=" . str_repeat("=", 50) . "\n\n";

// Get all registrars and their users
$registrars = App\Models\Registrar::with('user')->get();

echo "📋 Available Registrars:\n";
foreach ($registrars as $registrar) {
    $windowNumber = $registrar->window_number;
    echo "   Registrar ID: {$registrar->id}, Window: {$windowNumber}\n";
    
    // Find the window that corresponds to this registrar's window number
    $assignedWindow = App\Models\Window::where('name', 'Window ' . $windowNumber)->first();
    
    if ($assignedWindow) {
        echo "     Found Window: {$assignedWindow->name} (ID: {$assignedWindow->id})\n";
        
        // Check if window is currently occupied
        $currentRequest = App\Models\OnsiteRequest::where('window_id', $assignedWindow->id)
            ->where('assigned_registrar_id', $registrar->user_id)
            ->whereIn('status', ['processing', 'in_queue', 'ready_for_pickup'])
            ->whereIn('current_step', ['processing', 'window'])
            ->first();
        
        $isWindowOccupied = $currentRequest !== null;
        echo "     Window Occupied: " . ($isWindowOccupied ? 'YES' : 'NO') . "\n";
        
        // Simulate the exact filtering logic from controller
        if (!$isWindowOccupied) {
            // Show pending requests that are not assigned to any registrar yet
            $pendingRequests = App\Models\OnsiteRequest::where('status', 'pending')
                ->whereNull('assigned_registrar_id')
                ->whereNull('window_id')
                ->orderByDesc('created_at')
                ->get();
            
            echo "     Visible Pending Requests:\n";
            if ($pendingRequests->count() > 0) {
                foreach ($pendingRequests as $req) {
                    echo "       - Request #{$req->id}: {$req->full_name} ({$req->ref_code})\n";
                }
            } else {
                echo "       - No pending requests found\n";
            }
        } else {
            echo "     Window is occupied with current request.\n";
        }
    } else {
        echo "     ❌ No matching window found for 'Window {$windowNumber}'\n";
    }
    echo "\n";
}

echo "🎯 Summary:\n";
echo "   Carlo's request (ID: 8) should be visible to all registrars with unoccupied windows\n";

// Double-check Carlo's request
$carloRequest = App\Models\OnsiteRequest::find(8);
if ($carloRequest) {
    echo "   Carlo's Status: {$carloRequest->status}\n";
    echo "   Carlo's Assigned Registrar: " . ($carloRequest->assigned_registrar_id ?? 'null') . "\n";
    echo "   Carlo's Window: " . ($carloRequest->window_id ?? 'null') . "\n";
}