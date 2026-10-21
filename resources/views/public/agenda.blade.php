@extends('layouts.public')
@section('title','Agenda')

@push('styles')
<style>
    /* latar sesuai permintaan */
    body {
        background: #FFFFFF;
    }

    /* Popup overlay */
    .popup-overlay {
        background: rgba(0, 0, 0, 0.85);
        backdrop-filter: blur(4px);
    }

    /* Popup image container */
    .popup-image-container {
        max-width: 95vw;
        max-height: 95vh;
    }

    /* Ensure popup is visible */
    [x-cloak] {
        display: none !important;
    }
</style>
@endpush

@section('content')
<header class="page-header">
    <span class="page-header__eyebrow">Agenda</span>
    <h1 class="page-header__title">Dokumentasi Kegiatan</h1>
    <p class="page-header__description">
        Dokumentasi kegiatan dan agenda SPBE.
    </p>
</header>

{{-- Gallery Grid - Center Aligned --}}
<div class="w-full max-w-full mx-auto px-4 md:px-6 lg:px-8"
    x-data="{ 
         openPopup: false, 
         selectedImage: null,
         openImage(imageUrl) {
             this.selectedImage = imageUrl;
             this.openPopup = true;
             document.body.style.overflow = 'hidden';
         },
         closePopup() {
             this.openPopup = false;
             this.selectedImage = null;
             document.body.style.overflow = '';
         }
     }"
    @keydown.escape.window="closePopup()">

    <div class="space-y-6 md:space-y-8">
        @forelse(($images ?? collect()) as $image)
        <div class="group cursor-pointer" @click="openImage('{{ asset('storage/'.$image->image_path) }}')">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden transition-all duration-300 hover:shadow-xl hover:scale-[1.01]">
                <div class="w-full overflow-hidden bg-gray-100 flex items-center justify-center p-2 md:p-4">
                    <img
                        src="{{ asset('storage/'.$image->image_path) }}"
                        alt="Agenda"
                        class="w-full h-auto object-contain transition-transform duration-300 group-hover:scale-105 max-w-full">
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full text-slate-500 text-center py-12">
            <p>Belum ada gambar agenda yang tersedia.</p>
        </div>
        @endforelse
    </div>

    {{-- Popup Modal --}}
    <div
        x-show="openPopup"
        x-cloak
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-[9999] flex items-center justify-center popup-overlay"
        @click.self="closePopup()">

        <div class="relative popup-image-container mx-4"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95">

            {{-- Close Button --}}
            <button
                @click="closePopup()"
                class="absolute -top-12 right-0 text-white hover:text-[#FFA84A] transition-colors z-10 bg-black/50 rounded-full p-2 backdrop-blur-sm"
                aria-label="Tutup">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>

            {{-- Image --}}
            <div class="bg-white rounded-lg overflow-hidden shadow-2xl max-w-full">
                <div class="max-h-[85vh] overflow-auto">
                    <img
                        :src="selectedImage"
                        alt="Agenda"
                        class="w-full h-auto object-contain">
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection