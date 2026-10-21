<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - SPBE Kabupaten Malang</title>
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
            <img src="{{ asset('assets/img/logo-pemkab.png') }}" alt="Logo" class="h-16 mx-auto mb-4">
            <h1 class="text-2xl font-bold text-[#102A66]">Daftar Akun OPD</h1>
            <p class="text-gray-600 mt-2">Sistem Pemerintahan Berbasis Elektronik</p>
        </div>

        @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('register') }}" x-data="{
            showPassword: false,
            showPasswordConfirmation: false,
            password: '',
            passwordConfirmation: '',
            passwordRules: {
                minLength: false,
                noSpecialChars: false,
                hasLetterAndNumber: false
            },
            checkPasswordRules() {
                this.passwordRules.minLength = this.password.length >= 8;
                // Check if password contains only alphanumeric characters (no special chars)
                this.passwordRules.noSpecialChars = /^[a-zA-Z0-9]+$/.test(this.password);
                // Check if password contains at least one letter AND at least one number
                this.passwordRules.hasLetterAndNumber = /[a-zA-Z]/.test(this.password) && /[0-9]/.test(this.password);
            },
            isPasswordValid() {
                return this.passwordRules.minLength && this.passwordRules.noSpecialChars && this.passwordRules.hasLetterAndNumber;
            },
            isPasswordMatch() {
                return this.password.length > 0 && this.passwordConfirmation.length > 0 && this.password === this.passwordConfirmation;
            }
        }" @submit.prevent="if (!isPasswordValid()) { alert('Password tidak memenuhi syarat!\\n\\nSyarat password:\\n- Minimal 8 karakter\\n- Tidak menggunakan karakter khusus\\n- Mengandung huruf dan angka'); return false; } else if (!isPasswordMatch()) { alert('Konfirmasi password tidak cocok dengan password!'); return false; } else { $el.submit(); }" autocomplete="off">
            @csrf
            <div class="mb-4">
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap *</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#FFA84A] focus:border-transparent outline-none transition">
            </div>

            <div class="mb-4">
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email *</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="off" placeholder="Masukkan email Anda" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#FFA84A] focus:border-transparent outline-none transition">
                {{-- Informasi domain email (akan diaktifkan di production) --}}
                {{-- <p class="mt-1 text-xs text-gray-500">
                    <span class="font-semibold">Catatan:</span> Email harus menggunakan domain resmi <span class="font-semibold text-[#102A66]">@malangkab.go.id</span>
                </p> --}}
            </div>

            <div class="mb-4">
                <label for="nip" class="block text-sm font-semibold text-gray-700 mb-2">NIP *</label>
                <input type="text" id="nip" name="nip" value="{{ old('nip') }}" required placeholder="Masukkan NIP" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#FFA84A] focus:border-transparent outline-none transition">
            </div>

            <div class="mb-4">
                <label for="no_hp" class="block text-sm font-semibold text-gray-700 mb-2">No HP *</label>
                <input type="text" id="no_hp" name="no_hp" value="{{ old('no_hp') }}" required placeholder="Masukkan No HP" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#FFA84A] focus:border-transparent outline-none transition">
            </div>

            <div class="mb-4">
                <label for="nama_opd" class="block text-sm font-semibold text-gray-700 mb-2">Nama OPD/Unit Pelaksana *</label>
                @if(empty($availableOpds))
                <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded mb-4">
                    <p class="text-sm">Semua OPD sudah terdaftar. Silakan hubungi admin untuk informasi lebih lanjut.</p>
                </div>
                <select id="nama_opd" name="nama_opd" disabled class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed">
                    <option>Tidak ada OPD tersedia</option>
                </select>
                @else
                <select id="nama_opd" name="nama_opd" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#FFA84A] focus:border-transparent outline-none transition">
                    <option value="">Pilih OPD/Unit Pelaksana</option>
                    @foreach($availableOpds as $opd)
                    <option value="{{ $opd }}" {{ old('nama_opd') === $opd ? 'selected' : '' }}>{{ $opd }}</option>
                    @endforeach
                </select>
                @endif
            </div>

            <div class="mb-4">
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Password *</label>
                <div class="relative">
                    <input
                        :type="showPassword ? 'text' : 'password'"
                        id="password"
                        name="password"
                        x-model="password"
                        @input="checkPasswordRules()"
                        required
                        autocomplete="new-password"
                        class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#FFA84A] focus:border-transparent outline-none transition">
                    <button type="button"
                        @click="showPassword = !showPassword"
                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 focus:outline-none transition cursor-pointer"
                        aria-label="Toggle password visibility">
                        <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        <svg x-show="showPassword" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                        </svg>
                    </button>
                </div>
                <!-- Password Requirements -->
                <div class="mt-2 space-y-1" x-show="password.length > 0">
                    <div class="flex items-center text-sm">
                        <span x-show="!passwordRules.minLength" class="text-red-500 mr-2">!</span>
                        <span x-show="passwordRules.minLength" class="text-green-500 mr-2">✓</span>
                        <span :class="passwordRules.minLength ? 'text-green-600' : 'text-gray-600'">Minimal 8 karakter</span>
                    </div>
                    <div class="flex items-center text-sm">
                        <span x-show="!passwordRules.noSpecialChars" class="text-red-500 mr-2">!</span>
                        <span x-show="passwordRules.noSpecialChars" class="text-green-500 mr-2">✓</span>
                        <span :class="passwordRules.noSpecialChars ? 'text-green-600' : 'text-gray-600'">Tidak menggunakan karakter khusus</span>
                    </div>
                    <div class="flex items-center text-sm">
                        <span x-show="!passwordRules.hasLetterAndNumber" class="text-red-500 mr-2">!</span>
                        <span x-show="passwordRules.hasLetterAndNumber" class="text-green-500 mr-2">✓</span>
                        <span :class="passwordRules.hasLetterAndNumber ? 'text-green-600' : 'text-gray-600'">Mengandung huruf dan angka</span>
                    </div>
                </div>
            </div>

            <div class="mb-6">
                <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">Konfirmasi Password *</label>
                <div class="relative">
                    <input
                        :type="showPasswordConfirmation ? 'text' : 'password'"
                        id="password_confirmation"
                        name="password_confirmation"
                        x-model="passwordConfirmation"
                        required
                        autocomplete="new-password"
                        :class="passwordConfirmation.length > 0 ? (isPasswordMatch() ? 'border-green-500 focus:ring-green-500' : 'border-red-500 focus:ring-red-500') : 'border-gray-300 focus:ring-[#FFA84A]'"
                        class="w-full px-4 py-3 pr-12 border rounded-lg focus:ring-2 focus:border-transparent outline-none transition">
                    <button type="button"
                        @click="showPasswordConfirmation = !showPasswordConfirmation"
                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 focus:outline-none transition cursor-pointer"
                        aria-label="Toggle password visibility">
                        <svg x-show="!showPasswordConfirmation" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        <svg x-show="showPasswordConfirmation" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                        </svg>
                    </button>
                </div>
                <!-- Password Match Indicator -->
                <div class="mt-2" x-show="passwordConfirmation.length > 0">
                    <div class="flex items-center text-sm">
                        <span x-show="!isPasswordMatch()" class="text-red-500 mr-2">!</span>
                        <span x-show="isPasswordMatch()" class="text-green-500 mr-2">✓</span>
                        <span :class="isPasswordMatch() ? 'text-green-600' : 'text-red-600'">
                            <span x-show="!isPasswordMatch()">Konfirmasi password tidak cocok</span>
                            <span x-show="isPasswordMatch()">Password cocok</span>
                        </span>
                    </div>
                </div>
            </div>

            {{-- reCAPTCHA --}}
            @include('partials.recaptcha', [
            'action' => config('recaptcha.actions.register', 'register'),
            'recaptchaEnabled' => $recaptchaEnabled ?? false,
            'recaptchaSiteKey' => $recaptchaSiteKey ?? ''
            ])

            <button type="submit" {{ empty($availableOpds) ? 'disabled' : '' }} class="w-full bg-[#FFA84A] hover:bg-[#FF9A1F] text-white font-semibold py-3 px-4 rounded-lg transition duration-200 shadow-lg hover:shadow-xl mb-4 {{ empty($availableOpds) ? 'opacity-50 cursor-not-allowed' : '' }}">
                Daftar
            </button>

            <div class="text-center">
                <p class="text-sm text-gray-600">
                    Sudah punya akun?
                    <a href="{{ route('login') }}" class="text-[#FFA84A] hover:underline font-semibold">Masuk di sini</a>
                </p>
            </div>
        </form>
    </div>
</body>

</html>