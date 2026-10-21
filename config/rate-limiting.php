<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting Configuration
    |--------------------------------------------------------------------------
    |
    | Configure rate limiting for different parts of the application.
    |
    */

    'login' => [
        'max_attempts' => env('LOGIN_MAX_ATTEMPTS', 5),
        'decay_minutes' => env('LOGIN_DECAY_MINUTES', 1),
    ],

    'api' => [
        'max_attempts' => env('API_MAX_ATTEMPTS', 60),
        'decay_minutes' => env('API_DECAY_MINUTES', 1),
    ],

    'export' => [
        'max_attempts' => env('EXPORT_MAX_ATTEMPTS', 10),
        'decay_minutes' => env('EXPORT_DECAY_MINUTES', 1),
    ],

];
