<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CircuitBreaker
{
    private string $serviceName;

    private int $failureThreshold;

    private int $timeout;

    private int $retryAfter;

    public function __construct(
        string $serviceName,
        int $failureThreshold = 3,
        int $timeout = 20,
        int $retryAfter = 60
    ) {
        $this->serviceName = $serviceName;
        $this->failureThreshold = $failureThreshold;
        $this->timeout = $timeout;
        $this->retryAfter = $retryAfter;
    }

    /**
     * Execute callable dengan circuit breaker protection
     */
    public function call(callable $callback, ?callable $fallback = null)
    {
        $state = $this->getState();

        // Jika circuit OPEN (API down), langsung fallback
        if ($state === 'open') {
            logger()->warning("Circuit breaker OPEN for {$this->serviceName}");

            return $fallback ? $fallback() : null;
        }

        try {
            $result = $callback();

            // Success: reset failure count
            $this->recordSuccess();

            return $result;
        } catch (\Throwable $e) {
            // Failure: increment failure count
            $this->recordFailure();

            logger()->error("Circuit breaker failure for {$this->serviceName}", [
                'error' => $e->getMessage(),
                'failures' => $this->getFailureCount(),
            ]);

            // Jika ada fallback, gunakan
            if ($fallback) {
                return $fallback();
            }

            throw $e;
        }
    }

    /**
     * Get current circuit state: closed, open, half-open
     */
    private function getState(): string
    {
        $failures = $this->getFailureCount();

        // Jika failures >= threshold, circuit OPEN
        if ($failures >= $this->failureThreshold) {
            $openedAt = Cache::get($this->getOpenedAtKey());

            // Jika sudah lewat retry period, coba lagi (half-open)
            if ($openedAt && now()->diffInSeconds($openedAt) >= $this->retryAfter) {
                return 'half-open';
            }

            return 'open';
        }

        return 'closed';
    }

    /**
     * Record successful call
     */
    private function recordSuccess(): void
    {
        Cache::forget($this->getFailureKey());
        Cache::forget($this->getOpenedAtKey());
    }

    /**
     * Record failed call
     */
    private function recordFailure(): void
    {
        $failures = $this->getFailureCount() + 1;
        Cache::put($this->getFailureKey(), $failures, now()->addMinutes(5));

        // Jika mencapai threshold, mark circuit as OPEN
        if ($failures >= $this->failureThreshold) {
            Cache::put($this->getOpenedAtKey(), now(), now()->addMinutes(5));

            logger()->warning("Circuit breaker OPENED for {$this->serviceName}", [
                'failures' => $failures,
                'retry_after' => $this->retryAfter,
            ]);
        }
    }

    /**
     * Get current failure count
     */
    private function getFailureCount(): int
    {
        return (int) Cache::get($this->getFailureKey(), 0);
    }

    private function getFailureKey(): string
    {
        return "circuit_breaker:{$this->serviceName}:failures";
    }

    private function getOpenedAtKey(): string
    {
        return "circuit_breaker:{$this->serviceName}:opened_at";
    }

    /**
     * Manually reset circuit breaker
     */
    public function reset(): void
    {
        Cache::forget($this->getFailureKey());
        Cache::forget($this->getOpenedAtKey());

        logger()->info("Circuit breaker RESET for {$this->serviceName}");
    }

    /**
     * Get circuit breaker status
     */
    public function getStatus(): array
    {
        return [
            'service' => $this->serviceName,
            'state' => $this->getState(),
            'failures' => $this->getFailureCount(),
            'threshold' => $this->failureThreshold,
        ];
    }
}
