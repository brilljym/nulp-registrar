<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔍 Checking Registrar Data Structure\n";
echo "=" . str_repeat("=", 40) . "\n\n";

$registrars = App\Models\Registrar::with('user')->get();

foreach($registrars as $registrar) {
    echo "Registrar ID: {$registrar->id}\n";
    echo "User: {$registrar->user->name}\n";
    echo "Window Number: {$registrar->window_number}\n";
    echo "User Active: " . ($registrar->user->active ?? 'unknown') . "\n";
    echo "---\n";
}

echo "\nTotal registrars: " . $registrars->count() . "\n";