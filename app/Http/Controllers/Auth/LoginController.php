<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\RecaptchaRule;
use App\Services\RecaptchaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        // Jika sudah login sebagai OPD, redirect ke dashboard
        if (Auth::guard('web')->check()) {
            return redirect()->route('dashboard');
        }

        // Jika sudah login sebagai Admin, redirect ke dashboard
        if (Auth::guard('admin')->check()) {
            return redirect()->route('dashboard');
        }

        $recaptchaService = app(RecaptchaService::class);

        return view('auth.login', [
            'recaptchaEnabled' => $recaptchaService->isEnabled(),
            'recaptchaSiteKey' => $recaptchaService->getSiteKey(),
        ]);
    }

    public function login(Request $request)
    {
        $recaptchaService = app(RecaptchaService::class);
        $rules = [
            'email' => ['required', 'email'],
            'password' => ['required'],
        ];

        // Tambahkan validasi reCAPTCHA jika enabled (opsional untuk login, bisa di-disable di config)
        $messages = [];
        if ($recaptchaService->isEnabled()) {
            $rules['g-recaptcha-response'] = ['required', new RecaptchaRule(config('recaptcha.actions.login', 'login'))];
            $messages['g-recaptcha-response.required'] = 'Silakan centang kotak "I\'m not a robot" untuk melanjutkan.';
        }

        $validated = $request->validate($rules, $messages);

        // Filter credentials untuk authentication (hanya email dan password)
        // g-recaptcha-response tidak boleh masuk ke query database
        $credentials = [
            'email' => $validated['email'],
            'password' => $validated['password'],
        ];

        // Coba login sebagai Admin terlebih dahulu
        if (Auth::guard('admin')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Admin berhasil login, redirect ke dashboard OPD
            return redirect()->intended(route('dashboard'));
        }

        // Jika bukan admin, coba login sebagai OPD (User model)
        if (Auth::guard('web')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::guard('web')->user();

            // Pastikan user adalah instance User (OPD)
            if (! $user instanceof User) {
                Auth::guard('web')->logout();
                throw ValidationException::withMessages([
                    'email' => __('The provided credentials do not match our records.'),
                ]);
            }

            // Cek jika user belum di-approve (untuk OPD)
            if (! $user->is_approved) {
                Auth::guard('web')->logout();

                return back()->withErrors([
                    'email' => 'Akun Anda sedang menunggu persetujuan admin. Silakan hubungi administrator untuk informasi lebih lanjut.',
                ]);
            }

            return redirect()->intended(route('dashboard'));
        }

        throw ValidationException::withMessages([
            'email' => 'Email atau password yang Anda masukkan tidak sesuai. Silakan periksa kembali atau gunakan fitur lupa password jika Anda lupa.',
        ]);
    }

    public function logout(Request $request)
    {
        // Logout dari kedua guard (web dan admin)
        Auth::guard('web')->logout();
        Auth::guard('admin')->logout();

        // Invalidate session dan regenerate token
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Anda telah berhasil logout.');
    }
}
