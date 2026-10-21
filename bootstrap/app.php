<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\CacheImages::class,
            \App\Http\Middleware\SecurityHeaders::class,
            \App\Http\Middleware\PerformanceMonitoring::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Report exceptions to Sentry (if enabled)
        if (config('sentry.dsn')) {
            $exceptions->report(function (\Throwable $e) {
                if (app()->bound('sentry')) {
                    app('sentry')->captureException($e);
                }
            });
        }

        // Handle 419 CSRF Token Mismatch
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Sesi Anda telah berakhir. Silakan refresh halaman.',
                    'error' => 'token_mismatch',
                ], 419);
            }

            return redirect()
                ->back()
                ->withInput($request->except('_token', 'password'))
                ->with('error', 'Sesi Anda telah berakhir. Silakan coba lagi.');
        });
    })->create();
