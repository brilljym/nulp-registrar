<?php
// Temporary cache clearing script for Hostinger
// Upload this to your public folder and access via browser
// DELETE THIS FILE after use for security!

if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "✓ OPcache cleared<br>";
}

if (function_exists('apcu_clear_cache')) {
    apcu_clear_cache();
    echo "✓ APCu cache cleared<br>";
}

// Clear Laravel caches
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

echo "<br><strong>Clearing Laravel caches...</strong><br>";

$kernel->call('config:clear');
echo "✓ Config cache cleared<br>";

$kernel->call('cache:clear');
echo "✓ Application cache cleared<br>";

$kernel->call('route:clear');
echo "✓ Route cache cleared<br>";

$kernel->call('view:clear');
echo "✓ View cache cleared<br>";

echo "<br><strong style='color: green;'>All caches cleared successfully!</strong><br>";
echo "<br><strong style='color: red;'>IMPORTANT: Delete this file (clear-cache.php) now for security!</strong>";
