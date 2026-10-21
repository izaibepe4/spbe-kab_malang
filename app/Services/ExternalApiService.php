<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class ExternalApiService
{
    private const BASE_URL = 'https://web-admin.malangkab.go.id';

    private CircuitBreaker $circuitBreaker;

    public function __construct(string $serviceName = 'external_api')
    {
        $this->circuitBreaker = new CircuitBreaker($serviceName, 3, 20, 60);
    }

    /**
     * Get HTTP client dengan SSL verification
     */
    public function httpClient(): PendingRequest
    {
        // SSL verification: disabled hanya di local, enabled di production
        $shouldVerify = ! app()->environment('local');

        return Http::connectTimeout(5)
            ->timeout(20)
            ->acceptJson()
            ->retry(2, 1000, throw: false)
            ->when(! $shouldVerify, fn (PendingRequest $request) => $request->withoutVerifying());
    }

    /**
     * Fetch dari API dengan circuit breaker protection
     */
    public function fetchWithCircuitBreaker(string $url, ?callable $fallback = null)
    {
        return $this->circuitBreaker->call(
            callback: function () use ($url) {
                $response = $this->httpClient()->get($url);

                if (! $response->successful()) {
                    throw new \Exception("API returned {$response->status()}");
                }

                return $response->json();
            },
            fallback: $fallback
        );
    }

    /**
     * Resolve image URL dengan portal ID
     */
    public function resolveImageUrl(?string $path, ?int $portalId = null): ?string
    {
        if (! $path) {
            return null;
        }

        // Jika path sudah lengkap (http/https), langsung return
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        // Jika tidak ada portal ID, return null
        if (! $portalId) {
            return null;
        }

        // Bersihkan path dari leading slash
        $path = ltrim($path, '/');

        // Build full URL dengan portal ID
        return rtrim(self::BASE_URL, '/').'/'.$portalId.'//'.$path;
    }

    /**
     * Get base URL
     */
    public function getBaseUrl(): string
    {
        return self::BASE_URL;
    }

    /**
     * Get circuit breaker status
     */
    public function getCircuitBreakerStatus(): array
    {
        return $this->circuitBreaker->getStatus();
    }
}
