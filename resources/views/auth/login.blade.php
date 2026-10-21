<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SPBE Kabupaten Malang</title>
    @include('partials.favicon')
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    @php
        $recaptchaService = app(\App\Services\RecaptchaService::class);
        $recaptchaEnabled = $recaptchaService->isEnabled();
        $recaptchaSiteKey = $recaptchaService->getSiteKey();
        $recaptchaVersion = config('recaptcha.version', 'v2'); // Default to v2 for checkbox
    @endphp
    
    @if($recaptchaEnabled && !empty($recaptchaSiteKey))
        @if($recaptchaVersion === 'v3')
            {{-- Load reCAPTCHA v3 script in head for better performance --}}
            <script src="https://www.google.com/recaptcha/api.js?render={{ $recaptchaSiteKey }}" async defer></script>
        @else
            {{-- Load reCAPTCHA v2 script in head to ensure it's ready when widget renders --}}
            <script src="https://www.google.com/recaptcha/api.js" async defer></script>
        @endif
    @endif
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-[#102A66] to-[#1C2B78] min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8">
        <div class="text-center mb-8">
            <img src="{{ asset('assets/img/logo-pemkab.png') }}" alt="Logo Pemkab Malang" class="h-16 mx-auto mb-4">
            <h1 class="text-2xl font-bold text-[#102A66]">Masuk ke Akun</h1>
            <p class="text-gray-600 mt-2">Sistem Pemerintahan Berbasis Elektronik</p>
        </div>

        @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 rounded-lg p-4 mb-6 shadow-sm">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-sm font-semibold text-red-800 mb-1">Terjadi Kesalahan</h3>
                    <div class="text-sm text-red-700">
                        @foreach ($errors->all() as $error)
                        <p class="mb-1 last:mb-0">{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if (session('status'))
        <div class="bg-green-50 border-l-4 border-green-500 rounded-lg p-4 mb-6 shadow-sm">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm text-green-700">{{ session('status') }}</p>
                </div>
            </div>
        </div>
        @endif

        <form method="POST" action="{{ route('login') }}" autocomplete="off" x-data="{ submitting: false }" @submit="submitting = true">
            @csrf
            <div class="mb-4">
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                    Email
                    <span class="text-red-500">*</span>
                </label>
                <input type="email"
                    id="email"
                    name="email"
                    placeholder="contoh: nama@email.com"
                    value="{{ old('email') }}"
                    autocomplete="email"
                    required
                    autofocus
                    aria-describedby="email-error email-help"
                    class="w-full px-4 py-3 border {{ $errors->has('email') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-[#FFA84A]' }} rounded-lg focus:ring-2 focus:border-transparent outline-none transition">
                @error('email')
                <p id="email-error" class="mt-1 text-sm text-red-600 flex items-center" role="alert">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    {{ $message }}
                </p>
                @else
                <p id="email-help" class="mt-1 text-xs text-gray-500">Masukkan alamat email yang valid</p>
                @enderror
            </div>

            <div class="mb-6" x-data="{ showPassword: false }">
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                    Password
                    <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input :type="showPassword ? 'text' : 'password'"
                        id="password"
                        name="password"
                        autocomplete="current-password"
                        required
                        aria-describedby="password-error password-help"
                        class="w-full px-4 py-3 pr-12 border {{ $errors->has('password') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-[#FFA84A]' }} rounded-lg focus:ring-2 focus:border-transparent outline-none transition">
                    <button type="button"
                        @click="showPassword = !showPassword"
                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 focus:outline-none transition cursor-pointer"
                        :aria-label="showPassword ? 'Sembunyikan password' : 'Tampilkan password'"
                        :aria-pressed="showPassword">
                        <!-- Eye icon (visible - shown when password is hidden) -->
                        <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        <!-- Eye slash icon (hidden - shown when password is visible) -->
                        <svg x-show="showPassword" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                        </svg>
                    </button>
                </div>
                @error('password')
                <p id="password-error" class="mt-1 text-sm text-red-600 flex items-center" role="alert">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    {{ $message }}
                </p>
                @else
                <p id="password-help" class="mt-1 text-xs text-gray-500">Masukkan password Anda</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="remember" class="rounded border-gray-300 text-[#FFA84A] focus:ring-[#FFA84A]">
                    <span class="ml-2 text-sm text-gray-600">Remember me</span>
                </label>
            </div>

            {{-- reCAPTCHA --}}
            @include('partials.recaptcha', [
                'action' => config('recaptcha.actions.login', 'login'),
                'recaptchaEnabled' => $recaptchaEnabled ?? false,
                'recaptchaSiteKey' => $recaptchaSiteKey ?? ''
            ])

            <button type="submit"
                :disabled="submitting"
                class="w-full bg-[#FFA84A] hover:bg-[#FF9A1F] disabled:opacity-50 disabled:cursor-not-allowed text-white font-semibold py-3 px-4 rounded-lg transition duration-200 shadow-lg hover:shadow-xl mb-4 flex items-center justify-center gap-2">
                <svg x-show="submitting" class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span x-text="submitting ? 'Memproses...' : 'Masuk'"></span>
            </button>

            <div class="text-center mb-4">
                <a href="{{ route('password.request') }}" class="text-sm text-[#FFA84A] hover:underline font-semibold">
                    Lupa kata sandi?
                </a>
            </div>

            <div class="text-center">
                <p class="text-sm text-gray-600">
                    Belum punya akun?
                    <a href="{{ route('register') }}" class="text-[#FFA84A] hover:underline font-semibold">Daftar di sini</a>
                </p>
            </div>

        </form>
    </div>
</body>

</html>