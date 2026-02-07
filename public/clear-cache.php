<?php
// Clear OPcache
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "✅ OPcache cleared successfully<br>";
} else {
    echo "⚠️ OPcache not available<br>";
}

// Load Laravel
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Clear Laravel caches
echo "Clearing Laravel caches...<br>";

Artisan::call('config:clear');
echo "✅ Config cache cleared<br>";

Artisan::call('cache:clear');
echo "✅ Application cache cleared<br>";

Artisan::call('route:clear');
echo "✅ Route cache cleared<br>";

Artisan::call('view:clear');
echo "✅ View cache cleared<br>";

echo "<br><strong>🎉 All caches cleared successfully!</strong><br>";
echo "<br><em>Remember to delete this file after use for security.</em>";
