<?php

require_once 'vendor/autoload.php';

use App\Models\Window;
use App\Models\Registrar;
use App\Models\OnsiteRequest;
use App\Models\User;

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔍 Testing Window Queue System\n";
echo "=" . str_repeat("=", 50) . "\n\n";

// Check if windows exist
$windows = Window::all();
echo "📊 Windows Status:\n";
foreach ($windows as $window) {
    $occupied = $window->is_occupied ? "🔴 OCCUPIED" : "🟢 AVAILABLE";
    echo "   {$window->name}: {$occupied}\n";
}

echo "\n📋 Registrars and their Window Assignments:\n";
$registrars = Registrar::with('user')->get();
foreach ($registrars as $registrar) {
    $userName = $registrar->user ? $registrar->user->first_name . ' ' . $registrar->user->last_name : 'No User';
    echo "   Window {$registrar->window_number}: {$userName}\n";
}

echo "\n📋 Current Onsite Requests:\n";
$requests = OnsiteRequest::with(['window', 'registrar'])->get();
if ($requests->count() > 0) {
    foreach ($requests as $request) {
        $windowName = $request->window ? $request->window->name : 'No Window';
        $registrarName = $request->registrar ? $request->registrar->first_name . ' ' . $request->registrar->last_name : 'Unassigned';
        echo "   Request #{$request->id}: {$request->full_name}\n";
        echo "     Status: {$request->status}\n";
        echo "     Window: {$windowName}\n";
        echo "     Registrar: {$registrarName}\n";
        echo "     --\n";
    }
} else {
    echo "   No onsite requests found.\n";
}

echo "\n✅ Window Queue System Test Complete!\n";
