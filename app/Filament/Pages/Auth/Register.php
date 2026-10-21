<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Auth\Register as BaseRegister;
use Illuminate\Support\Facades\Http;

class Register extends BaseRegister
{
    protected function getForms(): array
    {
        $schema = [
            $this->getNameFormComponent(),
            $this->getEmailFormComponent(),
            $this->getPasswordFormComponent(),
            $this->getPasswordConfirmationFormComponent(),
        ];

        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema($schema)
                    ->statePath('data'),
            ),
        ];
    }

    protected function getNameFormComponent(): Component
    {
        return TextInput::make('name')
            ->label(__('filament-panels::pages/auth/register.form.name.label'))
            ->required()
            ->maxLength(255)
            ->autofocus();
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label(__('filament-panels::pages/auth/register.form.email.label'))
            ->email()
            ->required()
            ->maxLength(255)
            ->unique($this->getUserModel());
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label(__('filament-panels::pages/auth/register.form.password.label'))
            ->password()
            ->required()
            ->rule(\Illuminate\Validation\Rules\Password::default())
            ->dehydrateStateUsing(fn ($state) => \Illuminate\Support\Facades\Hash::make($state))
            ->same('passwordConfirmation')
            ->validationAttribute(__('filament-panels::pages/auth/register.form.password.validation_attribute'));
    }

    protected function getPasswordConfirmationFormComponent(): Component
    {
        return TextInput::make('passwordConfirmation')
            ->label(__('filament-panels::pages/auth/register.form.password_confirmation.label'))
            ->password()
            ->required()
            ->dehydrated(false);
    }

    public function register(): ?\Filament\Http\Responses\Auth\Contracts\RegistrationResponse
    {
        // Validate reCAPTCHA v2
        if (config('services.recaptcha.enabled') && config('services.recaptcha.version') === 'v2') {
            $recaptchaResponse = request()->input('g-recaptcha-response');

            if (empty($recaptchaResponse)) {
                \Filament\Notifications\Notification::make()
                    ->title('Verifikasi reCAPTCHA Diperlukan')
                    ->body('Silakan centang kotak "I\'m not a robot" untuk melanjutkan.')
                    ->danger()
                    ->send();

                $this->halt();

                return null;
            }

            $response = Http::timeout(10)->asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => config('services.recaptcha.secret_key'),
                'response' => $recaptchaResponse,
                'remoteip' => request()->ip(),
            ]);

            $result = $response->json();

            if (! isset($result['success']) || ! $result['success']) {
                \Filament\Notifications\Notification::make()
                    ->title('Verifikasi reCAPTCHA Gagal')
                    ->body('Verifikasi reCAPTCHA tidak berhasil. Silakan coba lagi atau refresh halaman.')
                    ->danger()
                    ->send();

                $this->halt();

                return null;
            }
        }

        return parent::register();
    }
}
