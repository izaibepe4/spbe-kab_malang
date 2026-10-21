<?php

namespace App\Filament\Pages\Auth;

use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Auth\PasswordReset\RequestPasswordReset as BaseRequestPasswordReset;
use Illuminate\Support\Facades\Http;

class RequestPasswordReset extends BaseRequestPasswordReset
{
    protected function getForms(): array
    {
        $schema = [
            $this->getEmailFormComponent(),
        ];

        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema($schema)
                    ->statePath('data'),
            ),
        ];
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label(__('filament-panels::pages/auth/password-reset/request-password-reset.form.email.label'))
            ->email()
            ->required()
            ->autocomplete()
            ->autofocus()
            ->validationAttribute('Email');
    }

    public function request(): void
    {
        try {
            $this->rateLimit(2);
        } catch (TooManyRequestsException $exception) {
            Notification::make()
                ->title(__('filament-panels::pages/auth/password-reset/request-password-reset.notifications.throttled.title', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]))
                ->body(array_key_exists('body', __('filament-panels::pages/auth/password-reset/request-password-reset.notifications.throttled') ?: []) ? __('filament-panels::pages/auth/password-reset/request-password-reset.notifications.throttled.body', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]) : null)
                ->danger()
                ->send();

            return;
        }

        // Validate reCAPTCHA v2 BEFORE form validation
        if (config('services.recaptcha.enabled') && config('services.recaptcha.version') === 'v2') {
            $recaptchaResponse = request()->input('g-recaptcha-response');

            if (empty($recaptchaResponse)) {
                Notification::make()
                    ->title('Verifikasi reCAPTCHA Diperlukan')
                    ->body('Silakan centang kotak "I\'m not a robot" untuk melanjutkan.')
                    ->danger()
                    ->send();

                return;
            }

            $response = Http::timeout(10)->asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => config('services.recaptcha.secret_key'),
                'response' => $recaptchaResponse,
                'remoteip' => request()->ip(),
            ]);

            $result = $response->json();

            if (! isset($result['success']) || ! $result['success']) {
                Notification::make()
                    ->title('Verifikasi reCAPTCHA Gagal')
                    ->body('Verifikasi reCAPTCHA tidak berhasil. Silakan coba lagi atau refresh halaman.')
                    ->danger()
                    ->send();

                return;
            }
        }

        parent::request();
    }
}
