@extends('layouts.public')

@section('title', $progres->judul . ' - SPBE Kabupaten Malang')

@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

    .article-thumbnail {
        width: 100%;
        height: auto;
        object-fit: cover;
        border-radius: 12px;
        margin-bottom: 2rem;
    }

    .progres-pdf-preview {
        width: 100%;
        min-height: 800px;
        border: 2px solid rgba(16, 42, 102, 0.12);
        border-radius: 12px;
        margin-top: 2rem;
    }

    .article-content {
        font-family: 'Poppins', sans-serif;
        font-size: 14px;
        font-weight: 400;
        line-height: 1.6;
        color: #334155;
    }

    /* Related Article Styles */
    .related-article-card {
        background: white;
        border-radius: 8px;
        padding: 10px;
        border: 2px solid rgba(16, 42, 102, 0.12);
        box-shadow: 0 8px 20px -10px rgba(16, 42, 102, 0.25);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        text-decoration: none;
        display: block;
    }

    .related-article-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 30px -10px rgba(16, 42, 102, 0.35);
    }

    .related-article-image-wrapper {
        width: 80px;
        height: 60px;
        overflow: hidden;
        border-radius: 6px;
        position: relative;
        flex-shrink: 0;
    }

    .related-article-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .related-article-card:hover .related-article-image {
        transform: scale(1.05);
    }

    .related-article-content {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .related-article-category {
        display: inline-block;
        background: #102A66;
        color: white;
        padding: 2px 8px;
        border-top-left-radius: 6px;
        border-top-right-radius: 0;
        border-bottom-left-radius: 0;
        border-bottom-right-radius: 6px;
        font-size: 10px;
        font-weight: 600;
        width: fit-content;
        transition: background 0.3s ease;
    }

    .related-article-card:hover .related-article-category {
        background: #FFA84A;
        color: #102A66;
    }

    .related-article-title {
        font-size: 12px;
        font-weight: 700;
        color: #102A66;
        line-height: 1.3;
        margin: 0;
        transition: color 0.3s ease;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .related-article-card:hover .related-article-title {
        color: #FFA84A;
    }

    .related-article-date {
        font-size: 11px;
        color: #5f6b94;
        margin: 0;
    }

    @media (max-width: 1024px) {
        .related-article-image-wrapper {
            width: 70px;
            height: 52px;
        }

        .related-article-title {
            font-size: 11px;
        }

        .related-article-date {
            font-size: 10px;
        }
    }
</style>
@endpush

@section('content')
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('informasi-spbe.index') }}" class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-800">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Kembali ke daftar informasi
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Main Content (Kiri) --}}
            <div class="lg:col-span-2">
                <article class="bg-[#ECF3FE] p-8 rounded-3xl shadow-sm">
                    <header class="page-header mb-6">
                        <h1 class="page-header__title">
                            {{ $progres->judul }}
                        </h1>
                        @if($progres->created_at)
                        <p class="text-slate-600 text-sm" style="font-family: 'Poppins', sans-serif;">
                            {{ $progres->created_at->translatedFormat('d F Y') }}
                        </p>
                        @endif
                    </header>

                    {{-- Gambar Thumbnail --}}
                    @if($progres->gambar)
                    <div class="mb-8">
                        <img 
                            src="{{ $progres->gambar_url }}" 
                            alt="{{ $progres->judul }}"
                            class="article-thumbnail">
                    </div>
                    @endif

                    {{-- Preview PDF --}}
                    @if($progres->file_pdf_path)
                    <div class="mt-6">
                        <iframe 
                            src="{{ route('public.informasi.progres.view', $progres->id) }}" 
                            frameborder="0" 
                            allowfullscreen="true"
                            class="progres-pdf-preview"
                            title="Preview PDF {{ $progres->judul }}">
                        </iframe>
                    </div>
                    @endif
                </article>
            </div>

            {{-- Sidebar Related Article (Kanan) --}}
            @if(!empty($otherArticles) && count($otherArticles) > 0)
            <div class="lg:col-span-1">
                <div class="sticky top-8" style="margin-top: 120px;">
                    <h2 class="text-xl md:text-2xl font-bold text-[#102A66] mb-4">
                        Related Article
                    </h2>
                    <div class="space-y-3">
                        @foreach($otherArticles as $item)
                        <a href="{{ route('informasi-spbe.show', $item['slug']) }}" class="related-article-card block">
                            <div class="flex gap-3 items-start">
                                {{-- Image di kiri --}}
                                <div class="related-article-image-wrapper flex-shrink-0">
                                    @if($item['image_url'])
                                    <img src="{{ $item['image_url'] }}" alt="{{ $item['title'] }}" class="related-article-image">
                                    @else
                                    <img src="https://via.placeholder.com/400x200?text=Berita+SPBE" alt="Gambar Berita" class="related-article-image">
                                    @endif
                                </div>

                                {{-- Content di kanan --}}
                                <div class="related-article-content flex-1">
                                    @if($item['category'])
                                    <span class="related-article-category">{{ $item['category'] }}</span>
                                    @endif
                                    <h3 class="related-article-title">
                                        {{ $item['title'] }}
                                    </h3>
                                    <p class="related-article-date">
                                        {{ optional($item['date'])->translatedFormat('d F Y') }}
                                    </p>
                                </div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</section>
@endsection

