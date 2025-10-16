<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\OnsiteRequest;

try {
    $request = OnsiteRequest::first();
    if($request) {
        echo "Current step: " . $request->current_step . "\n";
        echo "Status: " . $request->status . "\n";
    } else {
        echo "No requests found\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
