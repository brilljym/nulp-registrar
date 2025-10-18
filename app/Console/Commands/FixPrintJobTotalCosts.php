<?php

namespace App\Console\Commands;

use App\Models\PrintJob;
use Illuminate\Console\Command;

class FixPrintJobTotalCosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'print-jobs:fix-total-costs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix total costs for print jobs by recalculating from documents array';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $jobs = PrintJob::all();
        $updated = 0;

        foreach ($jobs as $job) {
            $documents = $job->documents;
            
            // Handle both array and JSON string formats
            if (is_string($documents)) {
                $documents = json_decode($documents, true);
            }
            
            $calculatedTotal = 0;

            if (is_array($documents)) {
                foreach ($documents as $doc) {
                    $price = $doc['price'] ?? 0;
                    $quantity = $doc['quantity'] ?? 1;
                    $calculatedTotal += $price * $quantity;
                }
            }

            if ($job->total_cost != $calculatedTotal) {
                $job->update(['total_cost' => $calculatedTotal]);
                $updated++;
                $this->info("Updated job {$job->id}: {$job->total_cost} -> {$calculatedTotal}");
            }
        }

        $this->info("Fixed {$updated} print jobs with incorrect total costs.");
        return Command::SUCCESS;
    }
}
