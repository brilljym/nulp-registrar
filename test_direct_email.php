<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing direct email sending to zetabrill@gmail.com...\n";

try {
    \Illuminate\Support\Facades\Mail::raw('Test rejection email from NU Registrar System', function ($message) {
        $message->to('zetabrill@gmail.com')
                ->subject('Test Rejection Email - NU Registrar');
    });
    echo "✅ Email sent successfully!\n";
} catch (Exception $e) {
    echo "❌ Email failed: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}