<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\StudentRequest;

// Find the student request with reference_no A003
$request = StudentRequest::where('reference_no', 'A003')->first();

if (!$request) {
    echo "Student request with reference_no A003 not found\n";
    exit(1);
}

echo "Found student request ID: {$request->id}, Reference: {$request->reference_no}, Status: {$request->status}\n";

// Update status to ready_for_pickup to trigger OneSignal notification
$request->status = 'ready_for_pickup';
$request->save();

echo "Updated student request status to: {$request->status}\n";
echo "OneSignal notification should be sent if player_id is set\n";