<?php

require 'vendor/autoload.php';
require 'bootstrap/app.php';

$app = Illuminate\Foundation\Application::getInstance();
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Queue Status Check ===\n";
echo "Student requests in_queue: " . App\Models\StudentRequest::where('status', 'in_queue')->count() . "\n";
echo "Onsite requests in_queue: " . App\Models\OnsiteRequest::where('status', 'in_queue')->count() . "\n";
echo "Student requests waiting: " . App\Models\StudentRequest::where('status', 'waiting')->count() . "\n";
echo "Onsite requests waiting: " . App\Models\OnsiteRequest::where('status', 'waiting')->count() . "\n";

echo "\n=== Waiting Requests Details ===\n";
$waitingOnsite = App\Models\OnsiteRequest::where('status', 'waiting')->with('requestItems.document')->get();
foreach($waitingOnsite as $req) {
    echo "Onsite ID: {$req->id}, Queue: {$req->queue_number}, Name: {$req->full_name}, Updated: {$req->updated_at}\n";
}

$waitingStudent = App\Models\StudentRequest::where('status', 'waiting')->with('student.user')->get();
foreach($waitingStudent as $req) {
    echo "Student ID: {$req->id}, Queue: {$req->queue_number}, Name: {$req->student->user->first_name} {$req->student->user->last_name}, Updated: {$req->updated_at}\n";
}

echo "\n=== In Queue Requests Details ===\n";
$inQueueOnsite = App\Models\OnsiteRequest::where('status', 'in_queue')->with('requestItems.document')->get();
foreach($inQueueOnsite as $req) {
    echo "Onsite ID: {$req->id}, Queue: {$req->queue_number}, Name: {$req->full_name}, Updated: {$req->updated_at}\n";
}

$inQueueStudent = App\Models\StudentRequest::where('status', 'in_queue')->with('student.user')->get();
foreach($inQueueStudent as $req) {
    echo "Student ID: {$req->id}, Queue: {$req->queue_number}, Name: {$req->student->user->first_name} {$req->student->user->last_name}, Updated: {$req->updated_at}\n";
}
