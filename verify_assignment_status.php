<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔍 Current Request Assignment Status\n";
echo "=" . str_repeat("=", 50) . "\n\n";

$carloRequest = App\Models\OnsiteRequest::where('ref_code', 'NU6D9D30')->first();

if ($carloRequest) {
    echo "📋 Carlo Arellano's Request (NU6D9D30):\n";
    echo "   Status: {$carloRequest->status}\n";
    echo "   Current Step: {$carloRequest->current_step}\n";
    echo "   Assigned Registrar ID: " . ($carloRequest->assigned_registrar_id ?? 'Not assigned yet') . "\n";
    
    if ($carloRequest->assigned_registrar_id) {
        $assignedUser = App\Models\User::find($carloRequest->assigned_registrar_id);
        if ($assignedUser && $assignedUser->registrar) {
            echo "   Assigned Registrar: Window {$assignedUser->registrar->window_number}\n";
        }
    }
    
    echo "   Window ID: " . ($carloRequest->window_id ?? 'Not assigned yet') . "\n";
    
    if ($carloRequest->window_id) {
        $assignedWindow = App\Models\Window::find($carloRequest->window_id);
        if ($assignedWindow) {
            echo "   Assigned Window: {$assignedWindow->name}\n";
        }
    }
} else {
    echo "❌ Request NU6D9D30 not found\n";
}

echo "\n🪟 Available Registrar Windows:\n";
$registrars = App\Models\Registrar::with('user')->get();
foreach($registrars as $registrar) {
    $user = $registrar->user;
    echo "   Window {$registrar->window_number}: User ID {$user->id}\n";
    
    // Check how many requests they can see
    $query = App\Models\OnsiteRequest::with(['window', 'registrar']);
    $query->where(function($q) use ($user, $registrar) {
        $q->where(function($subQ) {
            $subQ->where('status', 'pending')
                 ->whereNull('assigned_registrar_id')
                 ->whereNull('window_id');
        })
        ->orWhere(function($subQ) use ($user) {
            $subQ->where('assigned_registrar_id', $user->id);
        });
    });
    
    $visibleRequests = $query->get();
    echo "     Can see: " . $visibleRequests->count() . " request(s)\n";
    
    foreach($visibleRequests as $req) {
        echo "       - {$req->full_name} ({$req->status})\n";
    }
}

echo "\n📝 Process Flow:\n";
echo "   1. Request created with status 'pending'\n";
echo "   2. Available registrars can see it in their 'Pending' tab\n";
echo "   3. When a registrar clicks 'Approve & Take Request':\n";
echo "      - Request gets assigned to that registrar\n";
echo "      - Request gets assigned to that registrar's window\n";
echo "      - Status changes to 'registrar_approved'\n";
echo "      - Current step changes to 'payment'\n";
echo "   4. Request then appears in the tracking timeline\n";