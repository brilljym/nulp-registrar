<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Checking Windows Table Structure ===\n";

try {
    $columns = DB::select("DESCRIBE windows");
    
    echo "Windows table columns:\n";
    foreach ($columns as $column) {
        echo "- {$column->Field} ({$column->Type})\n";
    }
    
    // Check if registrar_id exists
    $hasRegistrarId = false;
    foreach ($columns as $column) {
        if ($column->Field === 'registrar_id') {
            $hasRegistrarId = true;
            break;
        }
    }
    
    if (!$hasRegistrarId) {
        echo "\n❌ registrar_id column is missing from windows table\n";
        echo "Need to add registrar_id column to windows table\n";
    } else {
        echo "\n✅ registrar_id column exists\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}