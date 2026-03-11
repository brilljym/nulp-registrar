<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\OnsiteRequest;
use App\Models\StudentRequest;
use App\Models\Registrar;

class FixWaitingRequests extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'queue:fix-waiting';

    /**
     * The console command description.
     */
    protected $description = 'Fix waiting requests that should be available for processing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking and fixing waiting requests...');

        // Show current waiting requests
        $waitingOnsiteRequests = OnsiteRequest::where('status', 'waiting')->with('registrar')->get();
        $waitingStudentRequests = StudentRequest::where('status', 'waiting')->with('registrar')->get();
        
        $this->info("Found {$waitingOnsiteRequests->count()} waiting onsite requests");
        $this->info("Found {$waitingStudentRequests->count()} waiting student requests");

        foreach ($waitingOnsiteRequests as $request) {
            $registrarInfo = $request->assigned_registrar_id ? 
                "assigned to registrar ID {$request->assigned_registrar_id}" :
                "not assigned to any registrar";
            $this->info("Onsite request {$request->id} ({$request->full_name}) - {$registrarInfo}");
        }

        // Get all registrars
        $registrars = Registrar::with('user')->get();
        
        $fixedOnsiteRequests = 0;
        $fixedStudentRequests = 0;

        foreach ($registrars as $registrar) {
            // Check if this registrar is currently occupied
            $hasActiveOnsiteRequest = OnsiteRequest::where('assigned_registrar_id', $registrar->user_id)
                ->whereIn('status', ['in_queue', 'ready_for_pickup', 'processing'])
                ->exists();
                
            $hasActiveStudentRequest = StudentRequest::where('assigned_registrar_id', $registrar->user_id)
                ->whereIn('status', ['in_queue', 'ready_for_pickup', 'processing'])
                ->exists();

            $isOccupied = $hasActiveOnsiteRequest || $hasActiveStudentRequest;

            $this->info("Registrar {$registrar->user->first_name} {$registrar->user->last_name}: " . ($isOccupied ? 'OCCUPIED' : 'AVAILABLE'));

            if (!$isOccupied) {
                // Registrar is available, promote their waiting requests
                
                // Fix onsite requests
                $waitingOnsiteRequest = OnsiteRequest::where('assigned_registrar_id', $registrar->user_id)
                    ->where('status', 'waiting')
                    ->orderBy('updated_at', 'asc')
                    ->first();
                    
                if ($waitingOnsiteRequest) {
                    $waitingOnsiteRequest->update([
                        'status' => 'in_queue',
                        'current_step' => 'processing'
                    ]);
                    $fixedOnsiteRequests++;
                    $this->info("✓ Fixed onsite request {$waitingOnsiteRequest->id} for registrar {$registrar->user->first_name} {$registrar->user->last_name}");
                }

                // Fix student requests
                $waitingStudentRequest = StudentRequest::where('assigned_registrar_id', $registrar->user_id)
                    ->where('status', 'waiting')
                    ->orderBy('updated_at', 'asc')
                    ->first();
                    
                if ($waitingStudentRequest) {
                    $waitingStudentRequest->update([
                        'status' => 'in_queue'
                    ]);
                    $fixedStudentRequests++;
                    $this->info("✓ Fixed student request {$waitingStudentRequest->id} for registrar {$registrar->user->first_name} {$registrar->user->last_name}");
                }
            }
        }

        $this->info("Fixed {$fixedOnsiteRequests} onsite requests and {$fixedStudentRequests} student requests.");
        return 0;
    }
}