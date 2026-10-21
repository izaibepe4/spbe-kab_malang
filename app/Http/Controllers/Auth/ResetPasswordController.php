<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Rules\RecaptchaRule;
use App\Services\RecaptchaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ResetPasswordController extends Controller
{
    public function showResetForm(Request $request, $token = null)
    {
        $recaptchaService = app(RecaptchaService::class);

        return view('auth.reset-password')->with([
            'token' => $token,
            'email' => $request->email,
            'recaptchaEnabled' => $recaptchaService->isEnabled(),
            'recaptchaSiteKey' => $recaptchaService->getSiteKey(),
        ]);
    }

    public function reset(Request $request)
    {
        $recaptchaService = app(RecaptchaService::class);

        $rules = [
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => [
                'required',
                'confirmed',
                'string',
                'min:8',
                function ($attribute, $value, $fail) {
                    // Check if password contains only alphanumeric characters (no special chars)
                    if (! preg_match('/^[a-zA-Z0-9]+$/', $value)) {
                        $fail('Password tidak boleh menggunakan karakter khusus.');
                    }
                    // Check if password contains at least one letter AND at least one number
                    if (! preg_match('/[a-zA-Z]/', $value) || ! preg_match('/[0-9]/', $value)) {
                        $fail('Password harus mengandung huruf dan angka.');
                    }
                },
            ],
        ];

        // Tambahkan validasi reCAPTCHA jika enabled
        $messages = [];
        if ($recaptchaService->isEnabled()) {
            $rules['g-recaptcha-response'] = ['required', new RecaptchaRule(config('recaptcha.actions.password_update', 'password_update'))];
            $messages['g-recaptcha-response.required'] = 'Silakan centang kotak "I\'m not a robot" untuk melanjutkan.';
        }

        $request->validate($rules, $messages);

        // Reset password
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $user->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('status', 'Password berhasil direset. Silakan login dengan password baru Anda.');
        }

        throw ValidationException::withMessages([
            'email' => [__($status)],
        ]);
    }
}
