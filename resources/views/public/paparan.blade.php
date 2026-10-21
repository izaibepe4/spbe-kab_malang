@extends('layouts.public')
@section('title','Paparan SPBE')

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
    <span class="page-header__eyebrow">Paparan SPBE</span>
    <h1 class="page-header__title">Dokumen dan Presentasi</h1>
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
                        <span class="font-medium">{{ $item->judul_item }}</span>
                    </div>
                </button>

                {{-- Preview PDF jika ada file_pdf_path --}}
                @if ($item->file_pdf_path && $item->tipe_embed === 'pdf')
                <div x-show="selectedItem === {{ $item->id }}" 
                     x-transition.opacity 
                     x-transition.duration.200ms
                     class="ml-5 mt-2">
                    <div class="w-full rounded-lg overflow-hidden shadow-lg border border-gray-200 bg-white" style="height: 800px; min-height: 600px;">
                        <iframe
                            src="{{ route('public.paparan.view', $item->id) }}"
                            frameborder="0"
                            allowfullscreen="true"
                            class="w-full h-full"
                            style="border: none;"
                            title="Preview PDF {{ $item->judul_item }}">
                        </iframe>
                    </div>
                </div>
                @endif

                {{-- Preview Google Slides jika ada embed_url --}}
                @if ($item->embed_url && $item->tipe_embed === 'slides')
                @php
                // Ubah embed URL menjadi preview URL untuk menampilkan full interface
                $previewUrl = str_replace('/embed', '/preview', $item->embed_url);
                // Jika sudah preview, tetap gunakan; jika tidak ada /embed, tambahkan /preview
                if (strpos($item->embed_url, '/preview') === false && strpos($item->embed_url, '/embed') === false) {
                // Jika URL tidak mengandung /embed atau /preview, coba ekstrak ID dan buat preview
                if (preg_match('/\/d\/([a-zA-Z0-9-_]+)/', $item->embed_url, $matches)) {
                $previewUrl = 'https://docs.google.com/presentation/d/' . $matches[1] . '/preview';
                } else {
                $previewUrl = $item->embed_url;
                }
                }
                @endphp
                <div x-show="selectedItem === {{ $item->id }}" 
                     x-transition.opacity 
                     x-transition.duration.200ms
                     class="ml-5 mt-2">
                    <div class="w-full rounded-lg overflow-hidden shadow-lg border border-gray-200 bg-white">
                        <iframe
                            src="{{ $previewUrl }}"
                            frameborder="0"
                            allowfullscreen="true"
                            mozallowfullscreen="true"
                            webkitallowfullscreen="true"
                            class="w-full"
                            style="height: 700px; min-height: 600px;"
                            loading="lazy">
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