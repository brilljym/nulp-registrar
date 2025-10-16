<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔍 Testing Registrar View for Carlo's Request\n";
echo "=" . str_repeat("=", 50) . "\n\n";

// Simulate the exact logic from our updated controller
$availableRegistrars = App\Models\Registrar::whereHas('window', function($query) {
    $query->where('is_active', true);
})->where('is_active', true)->get();

echo "📋 Available Registrars:\n";
foreach ($availableRegistrars as $registrar) {
    echo "   Registrar: {$registrar->full_name} (Window {$registrar->window->window_number})\n";
    
    // Test the exact query logic from our controller
    $requests = App\Models\OnsiteRequest::with(['window', 'assignedRegistrar'])
        ->where(function($query) use ($registrar) {
            $query->where(function($subQuery) use ($registrar) {
                // Show pending requests to available registrars
                $subQuery->where('status', 'pending')
                    ->whereNull('assigned_registrar_id')
                    ->whereNull('window_id');
            })->orWhere(function($subQuery) use ($registrar) {
                // Show assigned requests to the specific registrar
                $subQuery->where('assigned_registrar_id', $registrar->id)
                    ->where('window_id', $registrar->window_id)
                    ->whereIn('status', ['in_progress', 'reviewing']);
            });
        })
        ->orderBy('created_at', 'asc')
        ->get();
    
    echo "   Requests visible to {$registrar->full_name}:\n";
    if ($requests->count() > 0) {
        foreach ($requests as $request) {
            echo "     - Request #{$request->id}: {$request->full_name} ({$request->status})\n";
        }
    } else {
        echo "     - No requests visible\n";
    }
    echo "\n";
}

echo "🎯 Summary:\n";
echo "   Total available registrars: " . $availableRegistrars->count() . "\n";
echo "   Carlo's request should be visible to all available registrars\n";