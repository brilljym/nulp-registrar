<?php
require 'vendor/autoload.php';
echo 'Testing dotenv...' . PHP_EOL;
try {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    echo 'APP_ENV: ' . getenv('APP_ENV') . PHP_EOL;
    echo 'APP_KEY length: ' . strlen(getenv('APP_KEY')) . PHP_EOL;
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}
