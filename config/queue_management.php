<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Queue Management API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the Smart Queue Management API integration
    |
    */

    'api_url' => env('QUEUE_API_URL', 'https://smart-queueing-waiting-time-ai-ylac.vercel.app'),
    
    'default_settings' => [
        'service_counters' => env('QUEUE_SERVICE_COUNTERS', 3),
        'cache_ttl' => env('QUEUE_CACHE_TTL', 30), // seconds
        'analytics_cache_ttl' => env('QUEUE_ANALYTICS_CACHE_TTL', 300), // 5 minutes
        'timeout' => env('QUEUE_API_TIMEOUT', 10), // seconds
    ],

    'service_types' => [
        'student_documents' => [
            'name' => 'Student Documents',
            'avg_service_time' => 8,
            'priority' => 'normal',
        ],
        'alumni_documents' => [
            'name' => 'Alumni Documents',
            'avg_service_time' => 12,
            'priority' => 'normal',
        ],
        'transcript' => [
            'name' => 'Official Transcript',
            'avg_service_time' => 15,
            'priority' => 'high',
        ],
        'certification' => [
            'name' => 'Certification',
            'avg_service_time' => 10,
            'priority' => 'normal',
        ],
        'verification' => [
            'name' => 'Document Verification',
            'avg_service_time' => 8,
            'priority' => 'normal',
        ],
        'general' => [
            'name' => 'General Service',
            'avg_service_time' => 10,
            'priority' => 'normal',
        ],
    ],

    'customer_types' => [
        'student' => [
            'name' => 'Current Student',
            'api_type' => 'walk_in',
            'priority_multiplier' => 1.0,
        ],
        'alumni' => [
            'name' => 'Alumni',
            'api_type' => 'returning',
            'priority_multiplier' => 1.1,
        ],
        'staff' => [
            'name' => 'Staff/Faculty',
            'api_type' => 'vip',
            'priority_multiplier' => 0.8,
        ],
        'walk_in' => [
            'name' => 'Walk-in',
            'api_type' => 'walk_in',
            'priority_multiplier' => 1.0,
        ],
        'appointment' => [
            'name' => 'Appointment',
            'api_type' => 'appointment',
            'priority_multiplier' => 0.9,
        ],
    ],

    'status_mapping' => [
        'waiting' => [
            'label' => 'Waiting in Queue',
            'color' => 'info',
            'icon' => 'fas fa-clock',
        ],
        'in_service' => [
            'label' => 'Being Served',
            'color' => 'warning',
            'icon' => 'fas fa-user-clock',
        ],
        'completed' => [
            'label' => 'Completed',
            'color' => 'success',
            'icon' => 'fas fa-check-circle',
        ],
        'no_show' => [
            'label' => 'No Show',
            'color' => 'danger',
            'icon' => 'fas fa-times-circle',
        ],
        'cancelled' => [
            'label' => 'Cancelled',
            'color' => 'secondary',
            'icon' => 'fas fa-ban',
        ],
    ],

    'real_time' => [
        'enabled' => env('QUEUE_REALTIME_ENABLED', true),
        'polling_interval' => env('QUEUE_POLLING_INTERVAL', 30), // seconds
        'pusher_enabled' => env('QUEUE_PUSHER_ENABLED', false),
    ],

    'notifications' => [
        'sms_enabled' => env('QUEUE_SMS_ENABLED', false),
        'email_enabled' => env('QUEUE_EMAIL_ENABLED', true),
        'notify_on_ready' => env('QUEUE_NOTIFY_ON_READY', true),
        'notify_minutes_before' => env('QUEUE_NOTIFY_MINUTES_BEFORE', 5),
    ],

    'analytics' => [
        'track_service_times' => true,
        'track_wait_times' => true,
        'track_no_shows' => true,
        'export_enabled' => true,
    ],
];