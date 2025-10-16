<?php

require 'vendor/autoload.php';
require 'bootstrap/app.php';

$app = Illuminate\Foundation\Application::getInstance();
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Check if queue is available (no one currently in_queue)
$studentInQueue = App\Models\StudentRequest::where('status', 'in_queue')->exists();
$onsiteInQueue = App\Models\OnsiteRequest::where('status', 'in_queue')->exists();

if (!$studentInQueue && !$onsiteInQueue) {
    echo "Queue is available, moving next waiting person...\n";
    
    // Find the next waiting person
    $nextStudent = App\Models\StudentRequest::where('status', 'waiting')->orderBy('updated_at', 'asc')->first();
    $nextOnsite = App\Models\OnsiteRequest::where('status', 'waiting')->orderBy('updated_at', 'asc')->first();
    
    // Determine which request is older and should go first
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
        echo "Moving {$nextType} request ID {$nextRequest->id} (Queue: {$nextRequest->queue_number}) to in_queue\n";
        
        $nextRequest->update([
            'status' => 'in_queue',
            'updated_at' => now(),
        ]);
        
        echo "Successfully moved to in_queue!\n";
    } else {
        echo "No waiting requests found.\n";
    }
} else {
    echo "Queue is occupied, cannot move waiting requests.\n";
    echo "Student in queue: " . ($studentInQueue ? 'Yes' : 'No') . "\n";
    echo "Onsite in queue: " . ($onsiteInQueue ? 'Yes' : 'No') . "\n";
}