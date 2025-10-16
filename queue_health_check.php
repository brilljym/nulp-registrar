<?php

require 'vendor/autoload.php';
require 'bootstrap/app.php';

$app = Illuminate\Foundation\Application::getInstance();
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

/**
 * Queue Health Check Script
 * This script ensures that if there are waiting requests but no one in queue,
 * it automatically moves the next waiting person to in_queue.
 */

echo "=== Queue Health Check ===\n";
echo "Timestamp: " . now()->format('Y-m-d H:i:s') . "\n\n";

// Check current queue status
$studentInQueue = App\Models\StudentRequest::where('status', 'in_queue')->count();
$onsiteInQueue = App\Models\OnsiteRequest::where('status', 'in_queue')->count();
$studentWaiting = App\Models\StudentRequest::where('status', 'waiting')->count();
$onsiteWaiting = App\Models\OnsiteRequest::where('status', 'waiting')->count();

echo "Current Status:\n";
echo "- Students in queue: {$studentInQueue}\n";
echo "- Onsite in queue: {$onsiteInQueue}\n";
echo "- Students waiting: {$studentWaiting}\n";
echo "- Onsite waiting: {$onsiteWaiting}\n\n";

// Check if queue is available but there are waiting requests
if (($studentInQueue + $onsiteInQueue) === 0 && ($studentWaiting + $onsiteWaiting) > 0) {
    echo "⚠️  Queue issue detected: No one in queue but there are waiting requests!\n";
    echo "Attempting to fix...\n\n";
    
    // Find the next waiting person
    $nextStudent = App\Models\StudentRequest::where('status', 'waiting')->orderBy('updated_at', 'asc')->first();
    $nextOnsite = App\Models\OnsiteRequest::where('status', 'waiting')->orderBy('updated_at', 'asc')->first();
    
    // Determine which request should go first (oldest)
    $nextRequest = null;
    $nextType = null;
    
    if ($nextStudent && $nextOnsite) {
        if ($nextStudent->updated_at <= $nextOnsite->updated_at) {
            $nextRequest = $nextStudent;
            $nextType = 'student';
        } else {
            $nextRequest = $nextOnsite;
            $nextType = 'onsite';
        }
    } elseif ($nextStudent) {
        $nextRequest = $nextStudent;
        $nextType = 'student';
    } elseif ($nextOnsite) {
        $nextRequest = $nextOnsite;
        $nextType = 'onsite';
    }
    
    if ($nextRequest) {
        echo "✅ Moving {$nextType} request ID {$nextRequest->id} (Queue: {$nextRequest->queue_number}) to in_queue\n";
        
        $nextRequest->update([
            'status' => 'in_queue',
            'updated_at' => now(),
        ]);
        
        echo "✅ Successfully fixed queue!\n";
    }
} elseif (($studentInQueue + $onsiteInQueue) > 1) {
    echo "⚠️  Queue violation detected: More than 1 person in queue!\n";
    echo "This violates the single-slot queue rule.\n";
    
    // Get all requests currently in queue and fix the violation
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
    $first = $allInQueue->first();
    echo "\n✅ Keeping {$first['type']} ID: {$first['request']->id} in queue (earliest)\n";
    
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
    echo "✅ Queue status is healthy.\n";
}

echo "\n=== Queue Health Check Complete ===\n";