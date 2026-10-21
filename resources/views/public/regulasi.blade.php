@extends('layouts.public')
@section('title','Regulasi SPBE')

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
    <span class="page-header__eyebrow">Regulasi SPBE</span>
    <h1 class="page-header__title">Dokumen dan Pedoman</h1>
    <p class="page-header__description">
        Pilih kategori untuk melihat dokumen/pedoman yang dapat diunduh.
    </p>
</header>

{{-- LIST KATEGORI --}}
<ul class="space-y-5 md:space-y-6">
    @forelse(($categories ?? collect()) as $cat)
    <li x-data="{
          open:false,
          selectedItem: null,
          plus:'{{ asset('assets/icons/plus.png') }}',
          mins:'{{ asset('assets/icons/mins.png') }}',
          toggleItem(itemId) {
            this.selectedItem = this.selectedItem === itemId ? null : itemId;
          }
        }" class="select-none">
        {{-- tombol kategori (font 16px) --}}
        <button type="button" @click="open = !open" :aria-expanded="open"
            class="w-full flex items-center gap-3 text-left text-[#1C2B78] font-semibold text-base focus:outline-none">
            <img :src="open ? mins : plus" class="h-5 w-5 transition-transform duration-200"
                :class="open ? 'rotate-0' : 'rotate-180'" alt="">
            <span class="leading-tight">{{ $cat->nama_kategori }}</span>
        </button>

        {{-- Panel isi kategori (DOKUMEN) --}}
        <div x-show="open" x-transition.opacity x-transition.duration.200ms
            class="pl-10 md:pl-12 mt-3 md:mt-4 space-y-4">
            @forelse ($cat->items as $item)
            <div class="space-y-3">
                {{-- Judul Item --}}
                <button type="button" @click="toggleItem({{ $item->id }})" 
                    class="w-full flex items-start gap-3 group text-left hover:opacity-80 transition-opacity">
                    {{-- bullet kecil --}}
                    <div class="mt-1 h-2 w-2 rounded-full bg-[#FFA84A] shrink-0"></div>

                    <div class="text-base leading-relaxed text-[#1C2B78] flex-1">
                        <span class="font-medium">{{ $item->judul_regulasi }}</span>
                    </div>
                </button>

                {{-- Preview PDF jika ada file_pdf_path --}}
                @if ($item->file_pdf_path)
                <div x-show="selectedItem === {{ $item->id }}" 
                     x-transition.opacity 
                     x-transition.duration.200ms
                     class="ml-5 mt-2">
                    <div class="w-full rounded-lg overflow-hidden shadow-lg border border-gray-200 bg-white" style="height: 800px; min-height: 600px;">
                        <iframe 
                            src="{{ route('public.regulasi.view', $item->id) }}" 
                            frameborder="0" 
                            allowfullscreen="true"
                            class="w-full h-full"
                            style="border: none;"
                            title="Preview PDF {{ $item->judul_regulasi }}">
                        </iframe>
                    </div>
                </div>
                @endif
            </div>
            @empty
            <div class="text-slate-500 text-sm italic">Belum ada dokumen pada kategori ini.</div>
            @endforelse
        </div>
    </li>
    @empty
    <li class="text-slate-500">Kategori belum tersedia.</li>
    @endforelse
</ul>
@endsection