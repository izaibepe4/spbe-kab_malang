<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\RecaptchaRule;
use App\Services\RecaptchaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        $recaptchaService = app(RecaptchaService::class);

        return view('auth.forgot-password', [
            'recaptchaEnabled' => $recaptchaService->isEnabled(),
            'recaptchaSiteKey' => $recaptchaService->getSiteKey(),
        ]);
    }

    public function sendResetLinkEmail(Request $request)
    {
        $recaptchaService = app(RecaptchaService::class);

        $rules = [
            'email' => ['required', 'email'],
        ];

        // Tambahkan validasi reCAPTCHA jika enabled
        $messages = [];
        if ($recaptchaService->isEnabled()) {
            $rules['g-recaptcha-response'] = ['required', new RecaptchaRule(config('recaptcha.actions.password_reset', 'password_reset'))];
            $messages['g-recaptcha-response.required'] = 'Silakan centang kotak "I\'m not a robot" untuk melanjutkan.';
        }

        $request->validate($rules, $messages);

        // Check if user exists
        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return back()->withErrors([
                'email' => 'Email yang Anda masukkan belum terdaftar dalam sistem. Silakan periksa kembali atau hubungi administrator jika Anda yakin email ini sudah terdaftar.',
            ])->withInput();
        }

        // Send password reset link
        try {
            $status = Password::sendResetLink(
                $request->only('email')
            );

            if ($status === Password::RESET_LINK_SENT) {
                // Log untuk debugging
                \Log::info('Password reset email sent', [
                    'email' => $request->email,
                    'user_id' => $user->id,
                    'timestamp' => now(),
                ]);

                return back()->with('status', 'Link reset password telah dikirim ke email Anda. Silakan cek folder Spam/Junk jika tidak menemukannya di Inbox. Link berlaku selama 10 menit.');
            }
        } catch (\Exception $e) {
            // Log error
            \Log::error('Failed to send password reset email', [
                'email' => $request->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withErrors([
                'email' => 'Gagal mengirim email reset password. Silakan coba lagi atau hubungi administrator.',
            ])->withInput();
        }

        throw ValidationException::withMessages([
            'email' => [__($status)],
        ]);
    }
}
