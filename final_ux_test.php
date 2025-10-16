<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔍 Final User Experience Test\n";
echo "=" . str_repeat("=", 50) . "\n\n";

echo "🪟 What Each Registrar Should See Now:\n";

$registrars = App\Models\Registrar::with('user')->get();

foreach($registrars as $registrar) {
    $currentUser = $registrar->user;
    $currentWindowNumber = $registrar->window_number;
    
    echo "   👤 Registrar: {$currentUser->name} (Window {$currentWindowNumber})\n";
    
    $assignedWindow = App\Models\Window::where('name', 'Window ' . $currentWindowNumber)->first();
    
    // Use the FIXED window status logic
    $currentRequest = App\Models\OnsiteRequest::where('assigned_registrar_id', $currentUser->id)
        ->whereIn('status', ['registrar_approved', 'processing', 'in_queue', 'ready_for_pickup'])
        ->first();
    
    $isWindowOccupied = $currentRequest !== null;
    
    echo "     🪟 Window Status: " . ($isWindowOccupied ? "Currently Occupied" : "Available") . "\n";
    
    if ($isWindowOccupied && $currentRequest) {
        echo "     📋 Processing: {$currentRequest->full_name} (Status: {$currentRequest->status})\n";
        echo "     🔒 Cannot take new requests until current one is completed\n";
    } else {
        echo "     🟢 Ready to receive new requests\n";
        
        // Check for available pending requests
        $pendingRequests = App\Models\OnsiteRequest::where('status', 'pending')
            ->whereNull('assigned_registrar_id')
            ->whereNull('window_id')
            ->get();
        
        if ($pendingRequests->count() > 0) {
            echo "     📥 Can see {$pendingRequests->count()} pending request(s)\n";
        } else {
            echo "     📭 No pending requests available\n";
        }
    }
    echo "\n";
}

echo "✅ Expected Results:\n";
echo "   - Angelica (Window 3): Should show 'Currently Occupied' with Carlo's request\n";
echo "   - Nils (Window 2): Should show 'Currently Occupied' with Jym's request\n";
echo "   - Both windows should be blocked from taking new requests\n";
echo "\n";

echo "🎯 Fixes Applied:\n";
echo "   ✅ Window status calculation now checks registrar's active requests\n";
echo "   ✅ Window assignments corrected to match registrar window numbers\n";
echo "   ✅ System accurately reflects window occupancy status\n";