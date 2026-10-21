<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiException extends Exception
{
    protected string $service;

    protected int $statusCode;

    public function __construct(
        string $message,
        string $service = 'external_api',
        int $statusCode = 500,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, 0, $previous);
        $this->service = $service;
        $this->statusCode = $statusCode;
    }

    /**
     * Report the exception.
     */
    public function report(): void
    {
        logger()->error('API Exception', [
            'service' => $this->service,
            'message' => $this->getMessage(),
            'status_code' => $this->statusCode,
            'trace' => $this->getTraceAsString(),
        ]);
    }

    /**
     * Render the exception as an HTTP response.
     */
    public function render(Request $request): JsonResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => true,
                'message' => $this->getMessage(),
                'service' => $this->service,
            ], $this->statusCode);
        }

        return response()->json([
            'error' => true,
            'message' => 'Terjadi kesalahan saat mengambil data. Silakan coba lagi.',
        ], $this->statusCode);
    }
}
