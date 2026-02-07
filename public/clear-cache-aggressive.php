<?php
echo "<h1>Cache Clear - Aggressive Mode</h1>";

// 1. Clear OPcache
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "✅ OPcache reset<br>";
} else {
    echo "⚠️ OPcache not available<br>";
}

// 2. Clear OPcache for specific file
if (function_exists('opcache_invalidate')) {
    $filePath = __DIR__ . '/../app/Http/Controllers/Api/ReferenceController.php';
    opcache_invalidate($filePath, true);
    echo "✅ ReferenceController.php invalidated from OPcache<br>";
}

// 3. Clear realpath cache
clearstatcache(true);
echo "✅ Stat cache cleared<br>";

// 4. Load Laravel and clear its caches
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "<br><strong>Clearing Laravel caches...</strong><br>";

Artisan::call('config:clear');
echo "✅ Config cache cleared<br>";

Artisan::call('cache:clear');
echo "✅ Application cache cleared<br>";

Artisan::call('route:clear');
echo "✅ Route cache cleared<br>";

Artisan::call('view:clear');
echo "✅ View cache cleared<br>";

Artisan::call('clear-compiled');
echo "✅ Compiled classes cleared<br>";

// 5. Force Laravel to reload autoloader
if (file_exists(__DIR__ . '/../bootstrap/cache/services.php')) {
    unlink(__DIR__ . '/../bootstrap/cache/services.php');
    echo "✅ Services cache file deleted<br>";
}

if (file_exists(__DIR__ . '/../bootstrap/cache/packages.php')) {
    unlink(__DIR__ . '/../bootstrap/cache/packages.php');
    echo "✅ Packages cache file deleted<br>";
}

echo "<br><h2>🎉 All caches cleared!</h2>";
echo "<p><strong>IMPORTANT:</strong> You may need to wait 1-2 minutes for PHP-FPM to reload, or restart it manually.</p>";
echo "<p><em>Delete this file after use for security.</em></p>";

// 6. Test if new code is loaded
echo "<br><h2>Testing API...</h2>";
try {
    $testUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://{$_SERVER['HTTP_HOST']}/api/transactions/reference/SR-20251104-0002";
    echo "<p>Testing: <a href='$testUrl' target='_blank'>$testUrl</a></p>";
    
    $ch = curl_init($testUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);
    
    $data = json_decode($response, true);
    
    if (isset($data['debug_info'])) {
        echo "<p style='color: green; font-weight: bold;'>✅ NEW CODE IS LOADED! debug_info found</p>";
        echo "<p>Position: {$data['position']}</p>";
        echo "<p>Display Status: {$data['debug_info']['display_status']}</p>";
        echo "<p>Is First: " . ($data['debug_info']['is_first'] ? 'true' : 'false') . "</p>";
    } else {
        echo "<p style='color: red; font-weight: bold;'>❌ OLD CODE STILL RUNNING - debug_info NOT found</p>";
        echo "<p>You may need to:</p>";
        echo "<ul>";
        echo "<li>Wait 1-2 minutes for PHP-FPM to reload</li>";
        echo "<li>Restart PHP-FPM manually (if you have access)</li>";
        echo "<li>Contact Hostinger support to restart PHP-FPM</li>";
        echo "</ul>";
    }
} catch (Exception $e) {
    echo "<p style='color: orange;'>⚠️ Could not test API: " . $e->getMessage() . "</p>";
}
