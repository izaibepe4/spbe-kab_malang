<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title','SPBE Kabupaten Malang')</title>
    @php
    $logoPath = public_path('assets/img/logo-pemkab.png');
    $faviconVersion = file_exists($logoPath)
    ? filemtime($logoPath)
    : time();
    $faviconQuery = '?v=' . $faviconVersion;
    @endphp
    {{-- Favicon menggunakan route yang sudah diperbaiki --}}
    <link rel="icon" type="image/x-icon" href="{{ url('/favicon.ico') }}{{ $faviconQuery }}">
    <link rel="shortcut icon" type="image/png" href="{{ url('/favicon.png') }}{{ $faviconQuery }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ url('/favicon.png') }}{{ $faviconQuery }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ url('/favicon-16x16.png') }}{{ $faviconQuery }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/img/logo-pemkab.png') }}{{ $faviconQuery }}">

    {{-- PWA Manifest --}}
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#1C2B78">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="SPBE">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

        [x-cloak] {
            display: none !important;
        }

        /* Global Header Styles - Konsisten dengan beranda */
        .page-header {
            margin-bottom: 2rem;
        }

        @media (min-width: 768px) {
            .page-header {
                margin-bottom: 2.5rem;
            }
        }

        .page-header__eyebrow {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            font-size: 20px;
            color: #FFA84A;
            letter-spacing: 0.01em;
            margin-bottom: 6px;
            display: block;
        }

        .page-header__title {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            font-size: 24px;
            color: #1C2B78;
            line-height: 1.2;
            margin-bottom: 12px;
        }

        @media (min-width: 768px) {
            .page-header__title {
                font-size: 28px;
            }
        }

        .page-header__description {
            font-family: 'Poppins', sans-serif;
            font-weight: 400;
            font-size: 16px;
            color: #64748b;
            line-height: 1.6;
            max-width: 48rem;
            text-align: center;
            margin-left: auto;
            margin-right: auto;
        }

        @media (min-width: 768px) {
            .page-header__description {
                font-size: 17px;
            }
        }
    </style>
    @vite(['resources/css/app.css','resources/js/app.js'])
    <script src="{{ asset('js/image-cache-manager.js') }}" defer></script>
    <link rel="stylesheet" href="{{ asset('css/accessibility.css') }}">
    <link rel="stylesheet" href="{{ asset('css/usability-heuristics.css') }}">
    <script>
        // Load accessibility preferences immediately before page render
        (function() {
            if (localStorage.getItem('a11y-highContrast') === 'true') {
                document.documentElement.classList.add('a11y-high-contrast');
            }
            if (localStorage.getItem('a11y-fontSize') === 'small') {
                document.body.classList.add('a11y-font-small');
            } else if (localStorage.getItem('a11y-fontSize') === 'large') {
                document.body.classList.add('a11y-font-large');
            }
            if (localStorage.getItem('a11y-largeCursor') === 'true') {
                document.body.classList.add('a11y-large-cursor');
            }
            if (localStorage.getItem('a11y-lineSpacing') === 'true') {
                document.body.classList.add('a11y-line-spacing');
            }
        })();
    </script>
    @stack('styles')
</head>

<body class="min-h-screen flex flex-col bg-white pt-[110px]">
    @include('partials._header')

    <main class="flex-1">
        @if(request()->routeIs('public.home'))
        <div class="home-hero-wrapper">
            @yield('content')
        </div>
        @else
        <div class="wrap section">
            @yield('content')
        </div>
        @endif
    </main>


    @include('partials._footer')
    @include('partials._floating-button')

    {{-- Gender Selection Popup --}}
    @include('components.gender-popup')



    {{-- Usability Heuristics Components --}}
    @include('components.toast-notification')
    @include('components.confirmation-dialog')
    @include('components.form-loading')

    @stack('scripts')

    {{-- Service Worker Registration for PWA --}}
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then((reg) => {
                        console.log('Service Worker registered successfully', reg);
                    })
                    .catch((err) => {
                        console.log('Service Worker registration failed', err);
                    });
            });
        }
    </script>
</body>

</html>