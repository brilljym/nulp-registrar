<?php
// Load Laravel
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Get all waiting requests
$studentRequests = \App\Models\StudentRequest::whereIn('status', ['in_queue', 'waiting'])
    ->orderBy('created_at', 'asc')
    ->get(['id', 'queue_number', 'status', 'assigned_registrar_id', 'created_at']);

$onsiteRequests = \App\Models\OnsiteRequest::whereIn('status', ['in_queue', 'waiting'])
    ->orderBy('created_at', 'asc')
    ->get(['id', 'queue_number', 'status', 'assigned_registrar_id', 'created_at']);

echo "=== STUDENT REQUESTS (in_queue/waiting) ===\n";
foreach ($studentRequests as $index => $req) {
    $position = $index + 1;
    echo "Position {$position}: {$req->queue_number} | Status: {$req->status} | Registrar: {$req->assigned_registrar_id} | Created: {$req->created_at}\n";
}

echo "\n=== ONSITE REQUESTS (in_queue/waiting) ===\n";
foreach ($onsiteRequests as $index => $req) {
    $position = $index + 1;
    echo "Position {$position}: {$req->queue_number} | Status: {$req->status} | Registrar: {$req->assigned_registrar_id} | Created: {$req->created_at}\n";
}

// Check specific queue number from mobile app
echo "\n=== CHECKING QUEUE NUMBER '15' ===\n";
$checkStudent = \App\Models\StudentRequest::where('queue_number', '15')->first();
if ($checkStudent) {
    echo "Found in StudentRequest:\n";
    echo "  ID: {$checkStudent->id}\n";
    echo "  Queue: {$checkStudent->queue_number}\n";
    echo "  Status: {$checkStudent->status}\n";
    echo "  Reference: {$checkStudent->reference_no}\n";
    echo "  Created: {$checkStudent->created_at}\n";
}

$checkOnsite = \App\Models\OnsiteRequest::where('queue_number', '15')->first();
if ($checkOnsite) {
    echo "Found in OnsiteRequest:\n";
    echo "  ID: {$checkOnsite->id}\n";
    echo "  Queue: {$checkOnsite->queue_number}\n";
    echo "  Status: {$checkOnsite->status}\n";
    echo "  Ref Code: {$checkOnsite->ref_code}\n";
    echo "  Created: {$checkOnsite->created_at}\n";
}

if (!$checkStudent && !$checkOnsite) {
    echo "Queue number '15' not found in database\n";
}

echo "\n=== CHECKING QUEUE NUMBER 'A006' ===\n";
$checkA006Student = \App\Models\StudentRequest::where('queue_number', 'A006')->first();
if ($checkA006Student) {
    echo "Found in StudentRequest:\n";
    echo "  ID: {$checkA006Student->id}\n";
    echo "  Queue: {$checkA006Student->queue_number}\n";
    echo "  Status: {$checkA006Student->status}\n";
    echo "  Reference: {$checkA006Student->reference_no}\n";
    echo "  Created: {$checkA006Student->created_at}\n";
}

$checkA006Onsite = \App\Models\OnsiteRequest::where('queue_number', 'A006')->first();
if ($checkA006Onsite) {
    echo "Found in OnsiteRequest:\n";
    echo "  ID: {$checkA006Onsite->id}\n";
    echo "  Queue: {$checkA006Onsite->queue_number}\n";
    echo "  Status: {$checkA006Onsite->status}\n";
    echo "  Ref Code: {$checkA006Onsite->ref_code}\n";
    echo "  Created: {$checkA006Onsite->created_at}\n";
}
