<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ApiHealthMonitor
{
    private const CACHE_PREFIX = 'api_health:';

    private const HISTORY_PREFIX = 'api_health_history:';

    private const HISTORY_LIMIT = 100;

    /**
     * Check health of a specific API service
     */
    public function checkHealth(string $serviceName, string $url, int $timeout = 10): array
    {
        $startTime = microtime(true);

        try {
            $response = Http::timeout($timeout)->get($url);
            $responseTime = (microtime(true) - $startTime) * 1000; // Convert to ms

            $health = [
                'service' => $serviceName,
                'status' => $response->successful() ? 'healthy' : 'unhealthy',
                'response_time' => round($responseTime, 2),
                'status_code' => $response->status(),
                'checked_at' => now()->toIso8601String(),
                'message' => $response->successful() ? 'Service is operational' : 'Service returned error',
            ];

        } catch (\Exception $e) {
            $responseTime = (microtime(true) - $startTime) * 1000;

            $health = [
                'service' => $serviceName,
                'status' => 'down',
                'response_time' => round($responseTime, 2),
                'status_code' => 0,
                'checked_at' => now()->toIso8601String(),
                'message' => 'Service is unreachable: '.$e->getMessage(),
            ];

            Log::channel('api')->error('API health check failed', [
                'service' => $serviceName,
                'url' => $url,
                'error' => $e->getMessage(),
            ]);
        }

        // Cache the result
        Cache::put(self::CACHE_PREFIX.$serviceName, $health, now()->addMinutes(5));

        // Store in history
        $this->addToHistory($serviceName, $health);

        return $health;
    }

    /**
     * Get cached health status
     */
    public function getHealthStatus(string $serviceName): ?array
    {
        return Cache::get(self::CACHE_PREFIX.$serviceName);
    }

    /**
     * Get health status for all monitored services
     */
    public function getAllHealthStatus(): array
    {
        $services = config('monitoring.api_endpoints', []);
        $results = [];

        foreach ($services as $name => $url) {
            $cached = $this->getHealthStatus($name);
            if ($cached) {
                $results[$name] = $cached;
            }
        }

        return [
            'overall_status' => $this->calculateOverallStatus($results),
            'services' => $results,
            'checked_at' => now()->toIso8601String(),
        ];
    }

    /**
     * Get health metrics for a service
     */
    public function getHealthMetrics(string $serviceName): array
    {
        $history = $this->getHistory($serviceName);

        if (empty($history)) {
            return [
                'uptime_percentage' => 0,
                'average_response_time' => 0,
                'total_checks' => 0,
                'healthy_checks' => 0,
                'unhealthy_checks' => 0,
            ];
        }

        $totalChecks = count($history);
        $healthyChecks = count(array_filter($history, fn ($h) => $h['status'] === 'healthy'));
        $responseTimes = array_column($history, 'response_time');

        return [
            'uptime_percentage' => round(($healthyChecks / $totalChecks) * 100, 2),
            'average_response_time' => round(array_sum($responseTimes) / count($responseTimes), 2),
            'total_checks' => $totalChecks,
            'healthy_checks' => $healthyChecks,
            'unhealthy_checks' => $totalChecks - $healthyChecks,
        ];
    }

    /**
     * Add health check to history
     */
    private function addToHistory(string $serviceName, array $health): void
    {
        $key = self::HISTORY_PREFIX.$serviceName;
        $history = Cache::get($key, []);

        array_unshift($history, $health);

        // Keep only last N checks
        if (count($history) > self::HISTORY_LIMIT) {
            $history = array_slice($history, 0, self::HISTORY_LIMIT);
        }

        Cache::put($key, $history, now()->addDays(7));
    }

    /**
     * Get health check history
     */
    public function getHistory(string $serviceName, int $limit = 20): array
    {
        $history = Cache::get(self::HISTORY_PREFIX.$serviceName, []);

        return array_slice($history, 0, $limit);
    }

    /**
     * Calculate overall status from multiple services
     */
    private function calculateOverallStatus(array $services): string
    {
        if (empty($services)) {
            return 'unknown';
        }

        $statuses = array_column($services, 'status');

        if (in_array('down', $statuses)) {
            return 'degraded';
        }

        if (in_array('unhealthy', $statuses)) {
            return 'degraded';
        }

        return 'healthy';
    }
}
