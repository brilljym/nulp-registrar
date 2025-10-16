<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔍 Checking Carlo Arellano's Request Details\n";
echo "=" . str_repeat("=", 50) . "\n\n";

$request = App\Models\OnsiteRequest::where('full_name', 'Carlo Arellano')->first();
if ($request) {
    echo "✅ Request found:\n";
    echo "   ID: {$request->id}\n";
    echo "   Name: {$request->full_name}\n";
    echo "   Ref Code: {$request->ref_code}\n";
    echo "   Status: {$request->status}\n";
    echo "   Current Step: {$request->current_step}\n";
    echo "   Window ID: " . ($request->window_id ?? 'null') . "\n";
    echo "   Assigned Registrar ID: " . ($request->assigned_registrar_id ?? 'null') . "\n";
    echo "   Course: {$request->course}\n";
    echo "   Created: {$request->created_at}\n";
} else {
    echo "❌ Request not found!\n";
}

echo "\n📋 All Pending Requests:\n";
$pendingRequests = App\Models\OnsiteRequest::where('status', 'pending')
    ->whereNull('assigned_registrar_id')
    ->whereNull('window_id')
    ->get();

if ($pendingRequests->count() > 0) {
    foreach ($pendingRequests as $req) {
        echo "   Request #{$req->id}: {$req->full_name} - {$req->ref_code}\n";
    }
} else {
    echo "   No pending requests found.\n";
}