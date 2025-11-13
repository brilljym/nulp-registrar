<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\OnsiteRequest;

echo "🔍 Checking onsite requests in database...\n";

$count = OnsiteRequest::count();
echo "📊 Total onsite requests: {$count}\n";

if ($count > 0) {
    $requests = OnsiteRequest::latest()->take(5)->get();
    echo "\n📋 Recent requests:\n";
    foreach ($requests as $request) {
        echo "  ID: {$request->id} | Ref: {$request->ref_code} | Status: {$request->status} | Student: {$request->full_name}\n";
    }

    // Get the latest completed request for testing
    $completedRequest = OnsiteRequest::where('status', 'completed')->latest()->first();
    if ($completedRequest) {
        echo "\n✅ Found completed request for testing: ID {$completedRequest->id} ({$completedRequest->ref_code})\n";
        echo "💡 Update test_followup_email.php to use ID: {$completedRequest->id}\n";
    } else {
        echo "\n⚠️  No completed requests found. Create one first to test.\n";
    }
} else {
    echo "❌ No onsite requests found in database.\n";
}