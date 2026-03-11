<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\OnsiteRequest;

$request = new OnsiteRequest();
$request->ref_code = 'A003';
$request->full_name = 'Test User';
$request->student_id = 2; // Use existing student ID
$request->course = 'BSIT';
$request->year_level = '4';
$request->department = 'CCIS';
$request->document_id = 23;
$request->quantity = 1;
$request->reason = 'Test request for OneSignal';
$request->status = 'in_queue';
$request->queue_number = '12';
$request->save();

echo "Created onsite request with ID: " . $request->id . "\n";