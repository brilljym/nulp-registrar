<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\StudentRequest;

$requests = StudentRequest::with('requestItems')->take(5)->get();

echo "Student Requests:\n";
foreach ($requests as $request) {
    echo "ID: {$request->id}, Reference: {$request->reference_no}, Status: {$request->status}, Items: {$request->requestItems->count()}\n";
}