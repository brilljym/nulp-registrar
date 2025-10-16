<?php

require 'vendor/autoload.php';
require 'bootstrap/app.php';

$app = Illuminate\Foundation\Application::getInstance();
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Queue Violation Fix ===\n";
echo "Timestamp: " . now()->format('Y-m-d H:i:s') . "\n\n";

// Get all requests currently in queue
$studentsInQueue = App\Models\StudentRequest::where('status', 'in_queue')->orderBy('updated_at', 'asc')->get();
$onsiteInQueue = App\Models\OnsiteRequest::where('status', 'in_queue')->orderBy('updated_at', 'asc')->get();

$allInQueue = collect();
foreach($studentsInQueue as $req) {
    $allInQueue->push([
        'type' => 'student',
        'request' => $req,
        'updated_at' => $req->updated_at
    ]);
}
foreach($onsiteInQueue as $req) {
    $allInQueue->push([
        'type' => 'onsite', 
        'request' => $req,
        'updated_at' => $req->updated_at
    ]);
}

// Sort by timestamp to find who was first
$allInQueue = $allInQueue->sortBy('updated_at');

echo "Found " . $allInQueue->count() . " requests in queue:\n";
foreach($allInQueue as $index => $item) {
    $req = $item['request'];
    $name = $item['type'] === 'student' 
        ? $req->student->user->first_name . ' ' . $req->student->user->last_name
        : $req->full_name;
    $queue_num = $req->queue_number;
    echo "  " . ($index + 1) . ". {$item['type']} ID: {$req->id}, Queue: {$queue_num}, Name: {$name}, Time: {$req->updated_at}\n";
}

// Keep only the first one in queue, move others to waiting
if ($allInQueue->count() > 1) {
    echo "\n⚠️  Multiple requests in queue! Fixing...\n";
    
    $first = $allInQueue->first();
    echo "✅ Keeping {$first['type']} ID: {$first['request']->id} in queue (earliest)\n";
    
    foreach($allInQueue->skip(1) as $item) {
        $req = $item['request'];
        echo "🔄 Moving {$item['type']} ID: {$req->id} to waiting status\n";
        
        $req->update([
            'status' => 'waiting',
            'updated_at' => now(),
        ]);
    }
    
    echo "✅ Queue violation fixed!\n";
} else {
    echo "✅ Queue is healthy (single slot maintained)\n";
}

echo "\n=== Fix Complete ===\n";