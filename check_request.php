<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$request = App\Models\StudentRequest::find(14);
if ($request) {
    echo "Request ID: {$request->id}\n";
    echo "Status: {$request->status}\n";
    echo "Remarks: " . substr($request->remarks ?? 'none', 0, 100) . "\n";
    echo "Updated at: {$request->updated_at}\n";
    echo "Assigned registrar: " . ($request->assigned_registrar_id ?? 'none') . "\n";
} else {
    echo "Request not found\n";
}