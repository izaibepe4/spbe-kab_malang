<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Kata Sandi - SPBE Kabupaten Malang</title>
    @include('partials.favicon')
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    @php
        // Use variables from controller if available, otherwise get from service
        if (!isset($recaptchaEnabled) || !isset($recaptchaSiteKey)) {
            $recaptchaService = app(\App\Services\RecaptchaService::class);
            $recaptchaEnabled = $recaptchaService->isEnabled();
            $recaptchaSiteKey = $recaptchaService->getSiteKey();
        }
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
        body { font-family: 'Poppins', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gradient-to-br from-[#102A66] to-[#1C2B78] min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8">
        <div class="text-center mb-8">
            <img src="{{ asset('assets/img/logo-pemkab.png') }}" alt="Logo" class="h-16 mx-auto mb-4">
            <h1 class="text-2xl font-bold text-[#102A66]">Lupa Kata Sandi</h1>
            <p class="text-gray-600 mt-2">Masukkan email Anda untuk menerima link reset password</p>
        </div>

        @if (session('status'))
            <div class="bg-green-50 border-l-4 border-green-500 rounded-lg p-4 mb-6 shadow-sm">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium text-green-800">{{ session('status') }}</p>
                    </div>
                </div>
            </div>
        @endif

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

        <form method="POST" action="{{ route('password.email') }}" autocomplete="off">
            @csrf
            <div class="mb-6">
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email *</label>
                <input type="email" 
                    id="email" 
                    name="email" 
                    value="{{ old('email') }}" 
                    required 
                    autofocus
                    placeholder="Masukkan email Anda"
                    class="w-full px-4 py-3 border {{ $errors->has('email') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-[#FFA84A]' }} rounded-lg focus:ring-2 focus:border-transparent outline-none transition">
                @error('email')
                    <p class="mt-1 text-sm text-red-600 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- reCAPTCHA --}}
            @include('partials.recaptcha', [
                'action' => config('recaptcha.actions.password_reset', 'password_reset'),
                'recaptchaEnabled' => $recaptchaEnabled ?? false,
                'recaptchaSiteKey' => $recaptchaSiteKey ?? ''
            ])

            <button type="submit"
                class="w-full bg-[#FFA84A] hover:bg-[#FF9A1F] text-white font-semibold py-3 px-4 rounded-lg transition duration-200 shadow-lg hover:shadow-xl mb-4">
                Kirim Link Reset Password
            </button>

            <div class="text-center">
                <a href="{{ route('login') }}" class="text-sm text-[#FFA84A] hover:underline font-semibold">
                    Kembali ke halaman login
                </a>
            </div>
        </form>
    </div>
</body>
</html>

