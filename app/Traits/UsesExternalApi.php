<?php

namespace App\Traits;

use App\Services\CircuitBreaker;
use Illuminate\Support\Facades\Cache;

trait UsesExternalApi
{
    private ?CircuitBreaker $apiCircuitBreaker = null;

    /**
     * Get circuit breaker instance
     */
    protected function getCircuitBreaker(string $serviceName = 'external_api'): CircuitBreaker
    {
        if (! $this->apiCircuitBreaker) {
            $this->apiCircuitBreaker = new CircuitBreaker($serviceName, 3, 20, 60);
        }

        return $this->apiCircuitBreaker;
    }

    /**
     * Fetch dari API dengan circuit breaker protection
     */
    protected function fetchWithCircuitBreaker(
        string $url,
        string $cacheKey,
        int $cacheMinutes = 10,
        string $serviceName = 'external_api'
    ) {
        $circuitBreaker = $this->getCircuitBreaker($serviceName);

        return $circuitBreaker->call(
            // Primary: fetch dari API
            callback: function () use ($url, $cacheKey, $cacheMinutes) {
                $response = $this->httpClient()->get($url);

                if (! $response->successful()) {
                    throw new \Exception("API returned {$response->status()}");
                }

                $data = $response->json();

                // Cache hasil yang sukses
                if (! empty($data)) {
                    Cache::put($cacheKey, $data, now()->addMinutes($cacheMinutes));
                }

                return $data;
            },
            // Fallback: gunakan cached data
            fallback: function () use ($cacheKey) {
                logger()->info('Circuit breaker fallback: using cached data', [
                    'cache_key' => $cacheKey,
                ]);

                $cached = Cache::get($cacheKey);

                if ($cached) {
                    return $cached;
                }

                // Jika tidak ada cache, return empty
                logger()->warning('No cached data available for fallback', [
                    'cache_key' => $cacheKey,
                ]);

                return [];
            }
        );
    }

    /**
     * Get circuit breaker status untuk monitoring
     */
    protected function getCircuitBreakerStatus(string $serviceName = 'external_api'): array
    {
        return $this->getCircuitBreaker($serviceName)->getStatus();
    }
}
