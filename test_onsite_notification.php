<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\OnsiteRequest;

// Find the onsite request with ref_code A003
$request = OnsiteRequest::where('ref_code', 'A003')->first();

if (!$request) {
    echo "Onsite request with ref_code A003 not found\n";
    exit(1);
}

echo "Found onsite request ID: {$request->id}, Status: {$request->status}\n";

// Update status to ready_for_pickup to trigger OneSignal notification
$request->status = 'ready_for_pickup';
$request->save();

echo "Updated onsite request status to: {$request->status}\n";
echo "OneSignal notification should be sent if player_id is set\n";