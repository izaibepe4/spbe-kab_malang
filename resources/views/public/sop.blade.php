@extends('layouts.public')
@section('title','SOP SPBE')

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
    <span class="page-header__eyebrow">SOP SPBE</span>
    <h1 class="page-header__title">Monitoring dan Evaluasi SPBE</h1>
    <p class="page-header__description">
        Dokumen Standard Operating Procedure (SOP) untuk monitoring dan evaluasi SPBE.
    </p>
</header>

{{-- LIST SOP ITEMS --}}
<div class="space-y-8 md:space-y-10">
    @forelse(($items ?? collect()) as $item)
    <div class="space-y-4">
        {{-- Ringkasan SOP --}}
        @if ($item->ringkasan)
        <div class="text-[#1C2B78]">
            <p class="text-slate-700 text-base leading-relaxed">{{ $item->ringkasan }}</p>
        </div>
        @endif

        {{-- Preview PDF atau Gambar --}}
        @if ($item->tipe === 'pdf')
        <div class="w-full rounded-lg overflow-hidden shadow-lg border border-gray-200 bg-white" style="height: 800px; min-height: 600px;">
            {{-- Gunakan iframe untuk preview PDF --}}
            <iframe
                src="{{ route('public.sop.view', $item->id) }}"
                frameborder="0"
                allowfullscreen="true"
                class="w-full h-full"
                style="border: none;"
                title="Preview PDF SOP">
            </iframe>
        </div>
        @elseif ($item->tipe === 'image')
        <div class="w-full rounded-lg overflow-hidden shadow-lg border border-gray-200 bg-white">
            <img
                src="{{ $item->url }}"
                alt="SOP Image"
                class="w-full h-auto object-contain">
        </div>
        @endif
    </div>
    @empty
    <div class="text-slate-500 text-center py-8">
        <p>Belum ada dokumen SOP yang tersedia.</p>
    </div>
    @endforelse
</div>
@endsection