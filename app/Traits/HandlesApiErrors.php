<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

trait HandlesApiErrors
{
    /**
     * Handle API error and return consistent response
     */
    protected function handleApiError(\Exception $e, string $context = 'API Error'): JsonResponse
    {
        Log::error($context, [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ]);

        $statusCode = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;

        return response()->json([
            'success' => false,
            'message' => $this->getUserFriendlyMessage($e),
            'error' => app()->environment('local') ? $e->getMessage() : null,
        ], $statusCode);
    }

    /**
     * Get user-friendly error message
     */
    protected function getUserFriendlyMessage(\Exception $e): string
    {
        return match (true) {
            $e instanceof \Illuminate\Database\QueryException => 'Terjadi kesalahan pada database. Silakan coba lagi.',
            $e instanceof \Illuminate\Http\Client\ConnectionException => 'Tidak dapat terhubung ke server. Silakan periksa koneksi internet Anda.',
            $e instanceof \Illuminate\Http\Client\RequestException => 'Terjadi kesalahan saat mengambil data. Silakan coba lagi.',
            $e instanceof \Illuminate\Validation\ValidationException => 'Data yang Anda masukkan tidak valid.',
            $e instanceof \Illuminate\Auth\AuthenticationException => 'Anda harus login terlebih dahulu.',
            $e instanceof \Illuminate\Auth\Access\AuthorizationException => 'Anda tidak memiliki akses untuk melakukan aksi ini.',
            default => 'Terjadi kesalahan. Silakan coba lagi atau hubungi administrator.',
        };
    }

    /**
     * Log API call for monitoring
     */
    protected function logApiCall(string $service, string $endpoint, int $statusCode, float $duration, ?\Exception $error = null): void
    {
        Log::channel('api')->info('API Call', [
            'service' => $service,
            'endpoint' => $endpoint,
            'status_code' => $statusCode,
            'duration_ms' => round($duration, 2),
            'error' => $error ? $error->getMessage() : null,
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
