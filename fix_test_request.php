<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\StudentRequest;
use App\Models\StudentRequestItem;

// Find the existing test request
$studentRequest = StudentRequest::find(22);

if (!$studentRequest) {
    echo "Test request not found\n";
    exit(1);
}

// Create a request item if it doesn't exist
$existingItem = $studentRequest->requestItems()->first();
if (!$existingItem) {
    $requestItem = new StudentRequestItem();
    $requestItem->student_request_id = $studentRequest->id;
    $requestItem->document_id = 23; // Reprinting of COR-Stamp Enrolled/CTC/Copy of Grades
    $requestItem->quantity = 1;
    $requestItem->price = 100.00;
    $requestItem->save();
    echo "Created request item for test request\n";
} else {
    echo "Request item already exists\n";
}

// Update status to in_queue
$studentRequest->status = 'in_queue';
$studentRequest->save();

echo "Test request updated. ID: " . $studentRequest->id . ", Status: " . $studentRequest->status . "\n";