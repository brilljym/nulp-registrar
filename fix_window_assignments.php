<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔍 Checking Window and Registrar Data Integrity\n";
echo "=" . str_repeat("=", 50) . "\n\n";

echo "🪟 All Windows in Database:\n";
$windows = App\Models\Window::all();
foreach($windows as $window) {
    echo "   Window ID {$window->id}: {$window->name}\n";
}

echo "\n👥 All Registrars and Their Assignments:\n";
$registrars = App\Models\Registrar::with('user')->get();
foreach($registrars as $registrar) {
    $user = $registrar->user;
    echo "   Registrar ID {$registrar->id}:\n";
    echo "     User: {$user->name} (ID: {$user->id})\n";
    echo "     Window Number: {$registrar->window_number}\n";
    
    // Find expected window
    $expectedWindow = App\Models\Window::where('name', 'Window ' . $registrar->window_number)->first();
    if ($expectedWindow) {
        echo "     Expected Window: {$expectedWindow->name} (ID: {$expectedWindow->id})\n";
    } else {
        echo "     ❌ Expected Window 'Window {$registrar->window_number}' not found!\n";
    }
    echo "\n";
}

echo "📋 Current Request Assignments:\n";
$requests = App\Models\OnsiteRequest::with(['window', 'registrar'])->whereNotNull('assigned_registrar_id')->get();
foreach($requests as $req) {
    echo "   Request #{$req->id}: {$req->full_name}\n";
    echo "     Assigned Registrar ID: {$req->assigned_registrar_id}\n";
    echo "     Window ID: {$req->window_id}\n";
    if ($req->window) {
        echo "     Window Name: {$req->window->name}\n";
    }
    echo "   ---\n";
}

echo "\n🔧 Checking for Data Inconsistencies:\n";

// Check if registrar's assigned window matches their window_number
foreach($registrars as $registrar) {
    $user = $registrar->user;
    $expectedWindowName = 'Window ' . $registrar->window_number;
    $expectedWindow = App\Models\Window::where('name', $expectedWindowName)->first();
    
    if (!$expectedWindow) {
        echo "   ❌ Missing window: '{$expectedWindowName}' for registrar {$user->name}\n";
        continue;
    }
    
    // Check if this registrar has any requests assigned to wrong windows
    $wrongAssignments = App\Models\OnsiteRequest::where('assigned_registrar_id', $user->id)
        ->where('window_id', '!=', $expectedWindow->id)
        ->get();
    
    if ($wrongAssignments->count() > 0) {
        echo "   ❌ Wrong window assignments for {$user->name}:\n";
        foreach($wrongAssignments as $req) {
            echo "     Request #{$req->id} assigned to Window ID {$req->window_id} instead of {$expectedWindow->id}\n";
        }
    }
}

echo "\n🔧 Fixing Wrong Window Assignments:\n";
foreach($registrars as $registrar) {
    $user = $registrar->user;
    $expectedWindow = App\Models\Window::where('name', 'Window ' . $registrar->window_number)->first();
    
    if ($expectedWindow) {
        $updated = App\Models\OnsiteRequest::where('assigned_registrar_id', $user->id)
            ->where('window_id', '!=', $expectedWindow->id)
            ->update(['window_id' => $expectedWindow->id]);
        
        if ($updated > 0) {
            echo "   ✅ Fixed {$updated} request(s) for {$user->name} to use Window {$expectedWindow->id}\n";
        }
    }
}