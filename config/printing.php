<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Printing Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for thermal receipt printing system
    |
    */

    // Local printing settings
    'local_enabled' => env('PRINT_LOCAL_ENABLED', true),
    'printer_name' => env('POS_PRINTER_NAME', 'POS-58'),
    
    // Polling settings for local print service
    'polling_interval' => env('PRINT_POLLING_INTERVAL', 5), // seconds
    'max_retry_attempts' => env('PRINT_MAX_RETRIES', 3),
    
    // Queue settings
    'batch_size' => env('PRINT_BATCH_SIZE', 10),
    'cleanup_after_days' => env('PRINT_CLEANUP_DAYS', 30),
    
    // Remote database settings (for deployed app)
    'remote_database' => [
        'enabled' => env('PRINT_REMOTE_DB_ENABLED', false),
        'host' => env('PRINT_REMOTE_DB_HOST'),
        'database' => env('PRINT_REMOTE_DB_NAME'),
        'username' => env('PRINT_REMOTE_DB_USER'),
        'password' => env('PRINT_REMOTE_DB_PASS'),
        'port' => env('PRINT_REMOTE_DB_PORT', 3306),
    ],
];