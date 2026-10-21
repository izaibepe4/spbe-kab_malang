<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Sentry DSN
    |--------------------------------------------------------------------------
    |
    | Your Sentry DSN (Data Source Name) for error tracking.
    | Get this from your Sentry project settings.
    |
    */

    'dsn' => env('SENTRY_LARAVEL_DSN', env('SENTRY_DSN')),

    /*
    |--------------------------------------------------------------------------
    | Environment
    |--------------------------------------------------------------------------
    |
    | The environment your application is running in.
    |
    */

    'environment' => env('SENTRY_ENVIRONMENT', env('APP_ENV', 'production')),

    /*
    |--------------------------------------------------------------------------
    | Tracing
    |--------------------------------------------------------------------------
    |
    | Enable performance tracing for your application.
    |
    */

    'enable_tracing' => filter_var(env('SENTRY_TRACING_ENABLED', false), FILTER_VALIDATE_BOOLEAN),
    'traces_sample_rate' => env('APP_ENV') === 'local' ? 0.0 : (float) env('SENTRY_TRACES_SAMPLE_RATE', 0.2),

    /*
    |--------------------------------------------------------------------------
    | Profiling
    |--------------------------------------------------------------------------
    |
    | Enable profiling for performance analysis.
    |
    */

    'profiles_sample_rate' => (float) env('SENTRY_PROFILES_SAMPLE_RATE', 0.2),

    /*
    |--------------------------------------------------------------------------
    | Release
    |--------------------------------------------------------------------------
    |
    | The release version of your application.
    |
    */

    'release' => env('SENTRY_RELEASE'),

    /*
    |--------------------------------------------------------------------------
    | Ignore Exceptions
    |--------------------------------------------------------------------------
    |
    | List of exception classes that should not be reported to Sentry.
    |
    */

    'ignore_exceptions' => [
        \Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class,
        \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException::class,
    ],

];
