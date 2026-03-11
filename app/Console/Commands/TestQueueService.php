<?php

namespace App\Console\Commands;

use App\Models\OnsiteRequest;
use App\Services\QueueService;
use Illuminate\Console\Command;

class TestQueueService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-queue-service';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the QueueService functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing QueueService...');

        $queueService = app(QueueService::class);

        // Test queue number generation
        $queueNumber = $queueService->generateQueueNumber();
        $this->info("Generated queue number: {$queueNumber}");

        // Test finding available registrars
        $availableRegistrars = \App\Models\Registrar::with('user')->get();
        $this->info("Total registrars: " . $availableRegistrars->count());

        // Test the new query logic
        $busyRegistrarIds = OnsiteRequest::whereIn('current_step', ['processing', 'window'])
            ->whereNotNull('assigned_registrar_id')
            ->pluck('assigned_registrar_id')
            ->unique()
            ->toArray();

        $this->info("Busy registrar IDs: " . implode(', ', $busyRegistrarIds));

        $available = \App\Models\Registrar::with('user')
            ->whereNotIn('user_id', $busyRegistrarIds)
            ->get();

        $this->info("Available registrars: " . $available->count());

        // Test real-time assignment simulation
        $this->info("\n=== Testing Real-Time Assignment ===");

        // Create a test request in window status
        $testRequest = OnsiteRequest::create([
            'ref_code' => 'TEST' . rand(1000, 9999),
            'queue_number' => $queueNumber,
            'full_name' => 'Test User',
            'course' => 'Computer Science',
            'year_level' => '4th Year',
            'department' => 'IT',
            'current_step' => 'window',
            'status' => 'processing',
            'window_id' => 5, // Assign to Window 5
            'assigned_registrar_id' => null
        ]);

        $this->info("Created test request: {$testRequest->ref_code} in window queue");

        // Test processing next in queue
        $processedRequest = $queueService->processNextInQueue(5);

        if ($processedRequest) {
            $this->info("✅ Real-time assignment successful!");
            $this->info("Request assigned_registrar_id: " . $processedRequest->assigned_registrar_id);
            
            // Reload the request with registrar relationship
            $processedRequest->load('registrar');
            
            if ($processedRequest->registrar) {
                $this->info("Request {$processedRequest->ref_code} assigned to registrar: " . $processedRequest->registrar->first_name . ' ' . $processedRequest->registrar->last_name);
            } else {
                $this->info("❌ Registrar relationship not loaded properly");
                $this->info("Registrar object: " . json_encode($processedRequest->registrar));
            }
        } else {
            $this->info("❌ No request was processed - check registrar availability");
        }

        // Clean up test data
        $testRequest->delete();

        $this->info('QueueService test completed successfully!');
    }
}
