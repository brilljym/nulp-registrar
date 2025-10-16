<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔍 Checking Current Request State\n";
echo "=" . str_repeat("=", 50) . "\n\n";

// Check Carlo's request (NU6D9D30 from the timeline)
$carloRequest = App\Models\OnsiteRequest::where('ref_code', 'NU6D9D30')->first();
if (!$carloRequest) {
    echo "❌ Request NU6D9D30 not found! Let's check all requests:\n";
    $allRequests = App\Models\OnsiteRequest::all();
    foreach($allRequests as $req) {
        echo "   Request #{$req->id}: {$req->full_name} - {$req->ref_code} ({$req->status})\n";
    }
} else {
    echo "✅ Found Carlo's request:\n";
    echo "   ID: {$carloRequest->id}\n";
    echo "   Name: {$carloRequest->full_name}\n";
    echo "   Ref Code: {$carloRequest->ref_code}\n";
    echo "   Status: {$carloRequest->status}\n";
    echo "   Current Step: {$carloRequest->current_step}\n";
    echo "   Assigned Registrar ID: " . ($carloRequest->assigned_registrar_id ?? 'null') . "\n";
    echo "   Window ID: " . ($carloRequest->window_id ?? 'null') . "\n";
}

echo "\n📋 All Pending Requests:\n";
$pendingRequests = App\Models\OnsiteRequest::where('status', 'pending')->get();
foreach($pendingRequests as $req) {
    echo "   Request #{$req->id}: {$req->full_name} - {$req->ref_code}\n";
    echo "     Status: {$req->status}\n";
    echo "     Assigned Registrar: " . ($req->assigned_registrar_id ?? 'null') . "\n";
    echo "     Window: " . ($req->window_id ?? 'null') . "\n";
    echo "   ---\n";
}

echo "\n🪟 Window and Registrar Status:\n";
$registrars = App\Models\Registrar::with('user')->get();
foreach($registrars as $registrar) {
    $user = $registrar->user;
    echo "   Registrar ID {$registrar->id} (Window {$registrar->window_number}):\n";
    if ($user) {
        echo "     User: {$user->name} (ID: {$user->id})\n";
        echo "     User Active: " . ($user->active ?? 'unknown') . "\n";
    } else {
        echo "     ❌ No user found for registrar!\n";
    }
    echo "   ---\n";
}