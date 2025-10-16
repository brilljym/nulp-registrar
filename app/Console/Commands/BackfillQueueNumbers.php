<?php

namespace App\Console\Commands;

use App\Models\OnsiteRequest;
use App\Services\QueueService;
use Illuminate\Console\Command;

class BackfillQueueNumbers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:backfill-queue-numbers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backfill queue numbers for existing onsite requests';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $queueService = app(QueueService::class);
        $requests = OnsiteRequest::whereNull('queue_number')->get();

        $this->info("Found {$requests->count()} requests without queue numbers");

        foreach ($requests as $request) {
            $queueNumber = $queueService->generateQueueNumber();
            $request->update(['queue_number' => $queueNumber]);
            $this->line("Updated request {$request->id} with queue number {$queueNumber}");
        }

        $this->info('Backfill completed successfully!');
    }
}
