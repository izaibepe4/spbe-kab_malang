<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Google reCAPTCHA Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk Google reCAPTCHA v2 (checkbox)
    |
    */

    'enabled' => env('RECAPTCHA_ENABLED', true),

    // reCAPTCHA Site Key (untuk frontend)
    'site_key' => env('RECAPTCHA_SITE_KEY', ''),

    // reCAPTCHA Secret Key (untuk backend verification)
    'secret_key' => env('RECAPTCHA_SECRET_KEY', ''),

    // reCAPTCHA Version (v2 checkbox)
    'version' => env('RECAPTCHA_VERSION', 'v2'),

    // Score threshold untuk v3 (0.0 - 1.0)
    // Semakin tinggi semakin ketat (1.0 = sangat ketat, 0.0 = sangat longgar)
    // Recommended: 0.5
    'score_threshold' => env('RECAPTCHA_SCORE_THRESHOLD', 0.5),

    // Timeout settings (dalam detik)
    'connection_timeout' => env('RECAPTCHA_CONNECTION_TIMEOUT', 3), // Connection timeout
    'request_timeout' => env('RECAPTCHA_REQUEST_TIMEOUT', 5),      // Total request timeout
    'frontend_timeout' => env('RECAPTCHA_FRONTEND_TIMEOUT', 8),    // Frontend JS timeout (detik)

    // Action names untuk tracking di v3
    'actions' => [
        'login' => 'login',
        'register' => 'register',
        'password_reset' => 'password_reset',
        'password_update' => 'password_update',
    ],
];
