<?php
/**
 * Test the hybrid printing system
 */

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\HybridPrintingService;
use App\Services\ReceiptPrintingService;
use App\Models\StudentRequest;
use App\Models\PrintJob;

echo "🧪 Testing Hybrid Printing System\n";
echo "================================\n\n";

try {
    // Initialize services
    $hybridService = new HybridPrintingService(new ReceiptPrintingService());
    echo "✅ Hybrid printing service initialized\n";

    // Check if we have test data
    $testRequest = StudentRequest::where('queue_number', 'TEST123')->first();
    if (!$testRequest) {
        echo "❌ No test request found with queue number TEST123\n";
        echo "💡 Go to kiosk and enter TEST123 to create test data\n";
        exit(1);
    }
    echo "✅ Found test request: {$testRequest->queue_number}\n";

    // Test the hybrid printing
    echo "\n📄 Testing hybrid print request...\n";
    $result = $hybridService->handlePrintRequest($testRequest, 'student');
    
    if ($result['success']) {
        echo "✅ Print request successful!\n";
        echo "   Method: " . ($result['method'] ?? 'direct') . "\n";
        echo "   Message: {$result['message']}\n";
        
        if (isset($result['job_id'])) {
            echo "   Job ID: {$result['job_id']}\n";
        }
    } else {
        echo "❌ Print request failed: {$result['message']}\n";
    }

    // Check print jobs in database
    echo "\n📊 Print Jobs Status:\n";
    $pendingJobs = PrintJob::where('status', 'pending')->count();
    $completedJobs = PrintJob::where('status', 'completed')->count();
    $failedJobs = PrintJob::where('status', 'failed')->count();
    
    echo "   Pending: {$pendingJobs}\n";
    echo "   Completed: {$completedJobs}\n";
    echo "   Failed: {$failedJobs}\n";

    if ($pendingJobs > 0) {
        echo "\n🖨️ You can now run the local print service:\n";
        echo "   Double-click: start_print_service.bat\n";
        echo "   Or run: php local_print_service.php\n";
    }

    echo "\n✅ Hybrid printing system test completed!\n";

} catch (Exception $e) {
    echo "❌ Test failed: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}