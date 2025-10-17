<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$requests = App\Models\StudentRequest::all();

echo "Student Requests:\n";
foreach($requests as $r) {
    echo "ID: {$r->id}, Status: {$r->status}, Remarks: " . substr($r->remarks ?? 'none', 0, 50) . "\n";
}