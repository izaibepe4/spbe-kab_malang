<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PerformanceMonitoring
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('monitoring.performance.enabled')) {
            return $next($request);
        }

        $startTime = microtime(true);
        $startMemory = memory_get_usage();

        $response = $next($request);

        $duration = (microtime(true) - $startTime) * 1000; // Convert to ms
        $memoryUsed = memory_get_usage() - $startMemory;

        // Log performance metrics
        $this->logPerformance($request, $response, $duration, $memoryUsed);

        // Add performance headers (only in development)
        if (app()->environment('local')) {
            $response->headers->set('X-Response-Time', round($duration, 2).'ms');
            $response->headers->set('X-Memory-Usage', $this->formatBytes($memoryUsed));
        }

        return $response;
    }

    /**
     * Log performance metrics
     */
    private function logPerformance(Request $request, Response $response, float $duration, int $memoryUsed): void
    {
        $threshold = config('monitoring.performance.slow_request_threshold', 2000);

        // Log slow requests
        if ($duration > $threshold) {
            logger()->warning('Slow Request Detected', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'duration' => round($duration, 2).'ms',
                'memory' => $this->formatBytes($memoryUsed),
                'status_code' => $response->getStatusCode(),
                'user_id' => auth()->id(),
                'ip' => $request->ip(),
            ]);
        }

        // Don't log all requests in debug mode - only slow ones are logged above
        // This reduces I/O overhead and improves performance
    }

    /**
     * Format bytes to human readable
     */
    private function formatBytes(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes.' B';
        } elseif ($bytes < 1048576) {
            return round($bytes / 1024, 2).' KB';
        } else {
            return round($bytes / 1048576, 2).' MB';
        }
    }
}
