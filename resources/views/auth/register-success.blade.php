<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Berhasil - SPBE Kabupaten Malang</title>
    @include('partials.favicon')
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body class="bg-gradient-to-br from-[#102A66] to-[#1C2B78] min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8 text-center">
        <div class="mb-6">
            <div class="mx-auto w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-[#102A66] mb-2">Registrasi Berhasil!</h1>
            <p class="text-gray-600">Akun Anda sedang menunggu persetujuan admin</p>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <p class="text-sm text-blue-800">
                <strong>Informasi:</strong> Akun Anda akan diaktifkan setelah admin menyetujui registrasi. 
                Anda akan dapat login setelah menerima persetujuan.
            </p>
        </div>

        <div class="space-y-3">
            <a href="{{ route('login') }}" class="block w-full bg-[#FFA84A] hover:bg-[#FF9A1F] text-white font-semibold py-3 px-4 rounded-lg transition duration-200 shadow-lg hover:shadow-xl">
                Kembali ke Halaman Login
            </a>
            <a href="{{ route('public.home') }}" class="block w-full border border-gray-300 text-gray-700 font-semibold py-3 px-4 rounded-lg hover:bg-gray-50 transition">
                Kembali ke Beranda
            </a>
        </div>
    </div>
</body>
</html>

