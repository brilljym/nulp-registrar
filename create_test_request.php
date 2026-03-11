<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\StudentRequest;

$request = new StudentRequest();
$request->student_id = 13;
$request->reference_no = 'A003';
$request->queue_number = '12';
$request->status = 'waiting';
$request->reason = 'Test request for OneSignal notifications';
$request->total_cost = '100.00';
$request->save();

echo "Created test request with ID: " . $request->id . "\n";