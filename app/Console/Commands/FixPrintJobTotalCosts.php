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
            // Get the onsite request to access the items with document relationships
            $onsiteRequest = $job->onsiteRequest;

            if (!$onsiteRequest) {
                $this->warn("Print job {$job->id} has no associated onsite request, skipping.");
                continue;
            }

            $calculatedTotal = 0;

            // Calculate total from onsite request items using current document prices
            foreach ($onsiteRequest->items as $item) {
                $price = $item->document->price ?? 0;
                $quantity = $item->quantity ?? 1;
                $calculatedTotal += $price * $quantity;
            }

            if ($job->total_cost != $calculatedTotal) {
                $job->update(['total_cost' => $calculatedTotal]);
                $updated++;
                $this->info("Updated job {$job->id}: ₱{$job->total_cost} -> ₱{$calculatedTotal}");
            }
        }

        $this->info("Fixed {$updated} print jobs with incorrect total costs.");
        return Command::SUCCESS;
    }
}
