<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Events\QueuePlacementConfirmed;
use App\Models\StudentRequest;
use App\Models\OnsiteRequest;

class TestQueuePusherCommand extends Command
{
    protected $signature = 'queue:test-pusher {--type=student} {--id=}';
    protected $description = 'Test Pusher integration for queue placement confirmations';

    public function handle()
    {
        $type = $this->option('type');
        $id = $this->option('id');

        if (!$id) {
            // Find the latest request for testing
            if ($type === 'student') {
                $request = StudentRequest::latest()->first();
            } else {
                $request = OnsiteRequest::latest()->first();
            }

            if (!$request) {
                $this->error("No {$type} requests found for testing");
                return 1;
            }
        } else {
            if ($type === 'student') {
                $request = StudentRequest::find($id);
            } else {
                $request = OnsiteRequest::find($id);
            }

            if (!$request) {
                $this->error("{$type} request with ID {$id} not found");
                return 1;
            }
        }

        $this->info("Testing Pusher integration for {$type} request ID: {$request->id}");
        $this->info("Queue Number: {$request->queue_number}");
        $this->info("Reference: " . ($type === 'student' ? $request->reference_no : $request->ref_code));

        // Fire the event
        event(new QueuePlacementConfirmed(
            $request,
            $type,
            'test_queue_placement',
            "Test queue placement confirmation for {$request->queue_number}"
        ));

        $this->info("✅ QueuePlacementConfirmed event fired successfully!");
        $this->info("Check your Pusher dashboard and connected clients for the real-time update.");

        return 0;
    }
}