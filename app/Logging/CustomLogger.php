<?php

namespace App\Logging;

use Illuminate\Support\Facades\Log;

class CustomLogger
{
    /**
     * Log security event
     */
    public static function security(string $message, array $context = []): void
    {
        Log::channel('security')->warning($message, array_merge($context, [
            'timestamp' => now()->toIso8601String(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'user_id' => auth()->id(),
        ]));
    }

    /**
     * Log audit trail
     */
    public static function audit(string $action, string $model, $modelId, array $changes = []): void
    {
        Log::channel('audit')->info('Audit Trail', [
            'action' => $action, // created, updated, deleted
            'model' => $model,
            'model_id' => $modelId,
            'changes' => $changes,
            'user_id' => auth()->id(),
            'user_name' => auth()->user()?->name,
            'ip' => request()->ip(),
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Log API call
     */
    public static function apiCall(string $service, string $endpoint, int $statusCode, float $duration, ?string $error = null): void
    {
        $level = $statusCode >= 500 ? 'error' : ($statusCode >= 400 ? 'warning' : 'info');

        Log::channel('api')->{$level}('API Call', [
            'service' => $service,
            'endpoint' => $endpoint,
            'status_code' => $statusCode,
            'duration' => round($duration, 2).'ms',
            'error' => $error,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Log performance issue
     */
    public static function performance(string $type, float $value, array $context = []): void
    {
        Log::channel('performance')->warning('Performance Issue', array_merge([
            'type' => $type, // slow_query, slow_request, high_memory
            'value' => $value,
            'timestamp' => now()->toIso8601String(),
        ], $context));
    }

    /**
     * Log user activity
     */
    public static function activity(string $action, array $context = []): void
    {
        Log::channel('activity')->info('User Activity', array_merge([
            'action' => $action,
            'user_id' => auth()->id(),
            'user_name' => auth()->user()?->name,
            'ip' => request()->ip(),
            'timestamp' => now()->toIso8601String(),
        ], $context));
    }
}
