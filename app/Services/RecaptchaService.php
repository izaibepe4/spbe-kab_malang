<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RecaptchaService
{
    private string $secretKey;

    private string $siteKey;

    private float $scoreThreshold;

    private bool $enabled;

    public function __construct()
    {
        $this->secretKey = config('recaptcha.secret_key', '');
        $this->siteKey = config('recaptcha.site_key', '');
        $this->scoreThreshold = config('recaptcha.score_threshold', 0.5);
        $this->enabled = config('recaptcha.enabled', true);
    }

    /**
     * Verify reCAPTCHA token
     *
     * @return array ['success' => bool, 'score' => float|null, 'message' => string]
     */
    public function verify(?string $token, string $action = 'submit'): array
    {
        // If disabled in config, skip verification
        if (! $this->enabled) {
            return [
                'success' => true,
                'score' => 1.0,
                'message' => 'reCAPTCHA is disabled',
            ];
        }

        // If no token provided, return failure
        if (empty($token)) {
            Log::warning('reCAPTCHA token is empty', [
                'action' => $action,
                'ip' => request()->ip(),
            ]);

            return [
                'success' => false,
                'score' => null,
                'message' => 'reCAPTCHA token tidak ditemukan. Silakan coba lagi.',
            ];
        }

        // Verify with Google reCAPTCHA API
        try {
            $connectionTimeout = config('recaptcha.connection_timeout', 3);
            $requestTimeout = config('recaptcha.request_timeout', 5);

            $response = Http::connectTimeout($connectionTimeout)
                ->timeout($requestTimeout)
                ->retry(1, 500)  // Retry once after 500ms if fails
                ->asForm()
                ->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret' => $this->secretKey,
                    'response' => $token,
                    'remoteip' => request()->ip(),
                ]);

            $data = $response->json();

            if (! $response->successful() || ! isset($data['success'])) {
                Log::warning('reCAPTCHA API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'action' => $action,
                ]);

                return [
                    'success' => false,
                    'score' => null,
                    'message' => 'Gagal memverifikasi reCAPTCHA. Silakan coba lagi.',
                ];
            }

            // Log the verification result
            Log::info('reCAPTCHA verification result', [
                'success' => $data['success'] ?? false,
                'error-codes' => $data['error-codes'] ?? [],
                'action' => $action,
                'ip' => request()->ip(),
            ]);

            // For reCAPTCHA v3, check score
            if (isset($data['score'])) {
                $score = (float) $data['score'];
                $success = $data['success'] && $score >= $this->scoreThreshold;

                if (! $success) {
                    Log::info('reCAPTCHA v3 score below threshold', [
                        'score' => $score,
                        'threshold' => $this->scoreThreshold,
                        'action' => $action,
                    ]);
                }

                return [
                    'success' => $success,
                    'score' => $score,
                    'message' => $success
                        ? 'Verifikasi berhasil'
                        : 'Skor reCAPTCHA terlalu rendah. Aktivitas Anda terdeteksi mencurigakan.',
                ];
            }

            // For reCAPTCHA v2 checkbox
            return [
                'success' => $data['success'] === true,
                'score' => $data['success'] ? 1.0 : 0.0,
                'message' => $data['success']
                    ? 'Verifikasi berhasil'
                    : 'reCAPTCHA gagal diverifikasi. Silakan coba lagi.',
            ];
        } catch (\Exception $e) {
            Log::error('reCAPTCHA verification exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'score' => null,
                'message' => 'Terjadi kesalahan saat memverifikasi reCAPTCHA. Silakan coba lagi.',
            ];
        }
    }

    /**
     * Get site key for frontend
     */
    public function getSiteKey(): string
    {
        return $this->siteKey;
    }

    /**
     * Check if reCAPTCHA is enabled
     */
    public function isEnabled(): bool
    {
        return $this->enabled && ! empty($this->secretKey) && ! empty($this->siteKey);
    }
}
