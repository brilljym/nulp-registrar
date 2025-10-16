<?php
require_once 'vendor/autoload.php';

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

try {
    echo "Testing QR code library...\n";
    
    // Test simple QR code creation
    $qrCode = new QrCode('https://example.com');
    echo "QR Code created: " . get_class($qrCode) . "\n";
    
    // Test constructor parameters
    $reflection = new ReflectionClass($qrCode);
    $constructor = $reflection->getConstructor();
    $params = $constructor->getParameters();
    
    echo "Constructor parameters:\n";
    foreach($params as $param) {
        echo "  - " . $param->getName() . " (" . $param->getType() . ")\n";
    }
    
    // Test writer
    $writer = new PngWriter();
    $result = $writer->write($qrCode);
    echo "Write result: " . get_class($result) . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}