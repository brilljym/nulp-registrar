<?php

// Sample script to set expected release dates for testing
// Run this in your Laravel project using php artisan tinker or as a simple script

require_once 'vendor/autoload.php';

use App\Models\StudentRequest;
use App\Models\OnsiteRequest;
use Carbon\Carbon;

// Set expected release dates for student requests
$studentRequests = StudentRequest::whereIn('status', ['processing', 'ready_for_release', 'completed', 'pending'])->get();

foreach ($studentRequests as $request) {
    // Set expected release date to 8 days from now (Oct 9, 2025 as per your example)
    $expectedDate = Carbon::now()->addDays(8)->setTime(9, 0, 0); // 9:00 AM
    
    $request->update([
        'expected_release_date' => $expectedDate
    ]);
    
    echo "Updated StudentRequest {$request->reference_no} with expected release date: {$expectedDate}\n";
}

// Set expected release dates for onsite requests  
$onsiteRequests = OnsiteRequest::all();

foreach ($onsiteRequests as $request) {
    // Set expected release date to 8 days from now (Oct 9, 2025 as per your example)
    $expectedDate = Carbon::now()->addDays(8)->setTime(9, 0, 0); // 9:00 AM
    
    $request->update([
        'expected_release_date' => $expectedDate
    ]);
    
    echo "Updated OnsiteRequest {$request->ref_code} with expected release date: {$expectedDate}\n";
}

echo "Done updating expected release dates!\n";