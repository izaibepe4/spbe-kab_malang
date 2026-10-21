@extends('layouts.public')
@section('title','Arsitektur SPBE Kabupaten Malang')

@push('styles')
<style>
    /* latar sesuai permintaan */
    body {
        background: #FFFFFF;
    }
</style>
@endpush

@section('content')
<header class="page-header">
    <span class="page-header__eyebrow">Arsitektur SPBE</span>
    <h1 class="page-header__title">Arsitektur SPBE Kabupaten Malang</h1>
    <p class="page-header__description">
        Halaman ini menampilkan informasi lengkap terkait arsitektur SPBE Kabupaten Malang.
    </p>
</header>

{{-- Area untuk chart dan tabel dari API (akan ditambahkan nanti) --}}
<div class="space-y-6">
    <div class="bg-white rounded-lg shadow p-6 overflow-hidden">
        <iframe
            width="100%"
            height="1500"
            src="https://lookerstudio.google.com/embed/u/0/reporting/2e8cb507-0228-45a6-8929-af4cab84a8d0/page/p_fooxc4uatd"
            frameborder="0"
            style="border:0; display:block;"
            allowfullscreen
            sandbox="allow-storage-access-by-user-activation allow-scripts allow-same-origin allow-popups allow-popups-to-escape-sandbox"
            scrolling="no"></iframe>
    </div>
</div>
@endsection