<?php

return [

    /*
    |--------------------------------------------------------------------------
    | API Endpoints to Monitor
    |--------------------------------------------------------------------------
    |
    | List of external API endpoints that should be monitored for health
    | and performance. Add your API endpoints here.
    |
    */

    'api_endpoints' => [
        'berita_api' => env('BERITA_API_URL', 'https://api.malangkab.go.id'),
        'informasi_api' => env('INFORMASI_API_URL', 'https://api.malangkab.go.id'),
        // Add more endpoints as needed
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    |
    | Configure what should be logged and how.
    |
    */

    'logging' => [
        'enabled' => env('MONITORING_LOGGING_ENABLED', true),
        'log_slow_queries' => env('MONITORING_LOG_SLOW_QUERIES', true),
        'slow_query_threshold' => env('MONITORING_SLOW_QUERY_THRESHOLD', 1000), // milliseconds
        'log_api_calls' => env('MONITORING_LOG_API_CALLS', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Monitoring
    |--------------------------------------------------------------------------
    |
    | Configure performance monitoring thresholds and settings.
    |
    */

    'performance' => [
        'enabled' => env('MONITORING_PERFORMANCE_ENABLED', true),
        'slow_request_threshold' => env('MONITORING_SLOW_REQUEST_THRESHOLD', 2000), // milliseconds
        'track_memory_usage' => env('MONITORING_TRACK_MEMORY', true),
        'memory_threshold' => env('MONITORING_MEMORY_THRESHOLD', 100), // MB
    ],

    /*
    |--------------------------------------------------------------------------
    | Health Check Configuration
    |--------------------------------------------------------------------------
    |
    | Configure health check intervals and thresholds.
    |
    */

    'health_check' => [
        'enabled' => env('MONITORING_HEALTH_CHECK_ENABLED', true),
        'interval' => env('MONITORING_HEALTH_CHECK_INTERVAL', 300), // seconds (5 minutes)
        'timeout' => env('MONITORING_HEALTH_CHECK_TIMEOUT', 10), // seconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Alerts Configuration
    |--------------------------------------------------------------------------
    |
    | Configure when and how to send alerts.
    |
    */

    'alerts' => [
        'enabled' => env('MONITORING_ALERTS_ENABLED', true),
        'api_down_threshold' => env('MONITORING_API_DOWN_THRESHOLD', 3), // consecutive failures
        'slow_request_alert' => env('MONITORING_SLOW_REQUEST_ALERT', true),
        'high_memory_alert' => env('MONITORING_HIGH_MEMORY_ALERT', true),
    ],

];
