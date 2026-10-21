@php
    $logoPath = public_path('assets/img/logo-pemkab.png');
    $faviconVersion = file_exists($logoPath) ? filemtime($logoPath) : time();
    $faviconQuery = '?v=' . $faviconVersion;
@endphp
<link rel="icon" type="image/x-icon" href="{{ url('/favicon.png') }}{{ $faviconQuery }}">
<link rel="shortcut icon" type="image/png" href="{{ url('/favicon.png') }}{{ $faviconQuery }}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ url('/favicon.png') }}{{ $faviconQuery }}">
<link rel="icon" type="image/png" sizes="16x16" href="{{ url('/favicon-16x16.png') }}{{ $faviconQuery }}">
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/img/logo-pemkab.png') }}{{ $faviconQuery }}">

