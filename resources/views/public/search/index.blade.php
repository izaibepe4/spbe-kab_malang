@extends('layouts.public')

@section('title', 'Hasil Pencarian - SPBE Kabupaten Malang')

@push('styles')
<style>
    /* Shared card accents */
    :root {
        --card-border-color: rgba(16, 42, 102, 0.12);
        --card-shadow: 0 18px 40px -20px rgba(16, 42, 102, 0.35);
        --card-shadow-strong: 0 28px 60px -20px rgba(16, 42, 102, 0.45);
    }

    /* News Card Styles */
    .news-card {
        background: white;
        border-radius: 12px;
        padding: 0;
        border: 2px solid var(--card-border-color);
        box-shadow: var(--card-shadow);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .news-card:hover {
        transform: translateY(-6px);
        box-shadow: var(--card-shadow-strong);
    }

    .news-card-content {
        padding: 12px 18px 18px 18px;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .news-image-wrapper {
        position: relative;
        overflow: hidden;
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
    }

    .news-image-wrapper::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(0, 0, 0, 0.08) 0%, rgba(0, 0, 0, 0) 60%);
        pointer-events: none;
    }

    .news-category-tag {
        position: absolute;
        top: 16px;
        left: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #102A66;
        color: white;
        padding: 6px 16px;
        border-top-left-radius: 8px;
        border-top-right-radius: 0;
        border-bottom-left-radius: 0;
        border-bottom-right-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        box-shadow: 0 8px 20px rgba(16, 42, 102, 0.25);
        z-index: 2;
        transition: background 0.25s ease, color 0.25s ease, transform 0.25s ease;
    }

    .news-card:hover .news-category-tag {
        background: #FFA84A;
        color: #102A66;
        transform: translateY(-2px);
    }

    .news-title {
        font-size: 14px;
        font-weight: 700;
        color: #102A66;
        margin-bottom: 4px;
        line-height: 1.4;
        transition: color 0.3s ease;
        text-align: center;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .news-card:hover .news-title {
        color: #FFA84A;
    }

    .news-title a {
        color: inherit;
        text-decoration: none;
    }

    .news-title a:hover {
        color: #FFA84A;
    }

    .news-date {
        font-size: 12px;
        color: #5f6b94;
        margin-bottom: 0;
        margin-top: 0;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .news-date svg {
        color: #FFA84A;
    }

    .news-meta {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-size: 12px;
        color: #5f6b94;
        margin-top: 8px;
    }

    .news-meta-separator {
        color: #cbd5e1;
    }

    .news-read-article {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        color: #5f6b94;
        text-decoration: none;
        font-size: 12px;
        transition: color 0.3s ease;
    }

    .news-read-article:hover {
        color: #102A66;
    }

    .news-read-article svg {
        width: 14px;
        height: 14px;
        color: #FFA84A;
    }

    .news-read-article:hover svg {
        color: #102A66;
    }

    .news-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
        margin: 0;
        display: block;
    }

    .pagination-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 40px;
        height: 40px;
        padding: 0 12px;
        border: 1px solid #e5e7eb;
        background: white;
        color: #102A66;
        font-weight: 600;
        text-decoration: none;
        border-radius: 8px;
        transition: all 0.2s ease;
    }

    .pagination-btn:hover:not(.pagination-disabled) {
        background: #FFA84A;
        color: white;
        border-color: #FFA84A;
    }

    .pagination-active {
        background: #102A66;
        color: white;
        border-color: #102A66;
    }

    .pagination-disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endpush

@section('content')
<section class="pt-8 pb-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Search Results Banner --}}
        @if(!empty($search))
        <div class="bg-red-600 text-white px-6 py-2 mb-6 rounded-none flex items-center justify-between">
            <div class="flex items-center gap-4">
                <span class="text-sm md:text-base font-semibold">
                    Menampilkan hasil pencarian untuk "{{ $search }}"
                </span>
                @if($paginator && $paginator->total() > 0)
                <span class="text-sm opacity-90">
                    ({{ number_format($paginator->total(), 0, ',', '.') }} hasil ditemukan)
                </span>
                @endif
            </div>
        </div>
        @endif

        <header class="page-header text-center mb-12">
            <span class="page-header__eyebrow">Pencarian</span>
            <h1 class="page-header__title">Hasil Pencarian</h1>
            @if(empty($search))
            <p class="page-header__description">
                Silakan masukkan kata kunci untuk mencari berita dan informasi.
            </p>
            @elseif($paginator && $paginator->total() == 0)
            <p class="page-header__description">
                Tidak ada hasil yang ditemukan untuk "{{ $search }}". Coba gunakan kata kunci lain.
            </p>
            @else
            <p class="page-header__description">
                Ditemukan {{ number_format($paginator->total(), 0, ',', '.') }} hasil dari berita dan informasi SPBE.
            </p>
            @endif
        </header>

        @if($paginator && $paginator->total() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach($results as $item)
            <div class="news-card">
                <div class="news-image-wrapper">
                    @if($item['image_url'])
                    <img src="{{ $item['image_url'] }}" alt="{{ $item['title'] }}" class="news-image">
                    @else
                    <img src="https://via.placeholder.com/400x200?text=SPBE" alt="Gambar" class="news-image">
                    @endif

                    @if(isset($item['category']) && $item['category'])
                    <span class="news-category-tag">{{ $item['category'] }}</span>
                    @endif
                </div>
                <div class="news-card-content">
                    <h2 class="news-title line-clamp-2">
                        <a href="{{ $item['route'] ?? '#' }}">
                            {{ $item['title'] }}
                        </a>
                    </h2>
                    <div class="news-meta">
                        <span class="news-date">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            {{ optional($item['date'])->translatedFormat('d M Y') ?? 'Tanggal tidak tersedia' }}
                        </span>
                        <span class="news-meta-separator">|</span>
                        <a href="{{ $item['route'] ?? '#' }}" class="news-read-article">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Baca Artikel
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-10">
            @if($paginator->hasPages())
            <div class="flex justify-center items-center gap-2">
                @php
                $currentPage = $paginator->currentPage();
                $lastPage = $paginator->lastPage();
                $prevPage = max(1, $currentPage - 1);
                $nextPage = min($lastPage, $currentPage + 1);
                $prev10Page = max(1, $currentPage - 10);
                $next10Page = min($lastPage, $currentPage + 10);
                @endphp

                {{-- Double arrow left (skip 10 pages back) --}}
                @if($currentPage > 1)
                <a href="{{ $paginator->appends(request()->query())->url($prev10Page) }}"
                    class="pagination-btn pagination-nav">
                    «
                </a>
                @else
                <span class="pagination-btn pagination-nav pagination-disabled">«</span>
                @endif

                {{-- Single arrow left (previous page) --}}
                @if($paginator->onFirstPage())
                <span class="pagination-btn pagination-nav pagination-disabled">‹</span>
                @else
                <a href="{{ $paginator->appends(request()->query())->previousPageUrl() }}"
                    class="pagination-btn pagination-nav">
                    ‹
                </a>
                @endif

                {{-- Page numbers --}}
                @foreach($paginator->appends(request()->query())->getUrlRange(max(1, $currentPage - 2), min($lastPage, $currentPage + 2)) as $page => $url)
                @if($page == $currentPage)
                <span class="pagination-btn pagination-number pagination-active">{{ $page }}</span>
                @else
                <a href="{{ $url }}" class="pagination-btn pagination-number">{{ $page }}</a>
                @endif
                @endforeach

                {{-- Single arrow right (next page) --}}
                @if($paginator->hasMorePages())
                <a href="{{ $paginator->appends(request()->query())->nextPageUrl() }}"
                    class="pagination-btn pagination-nav">
                    ›
                </a>
                @else
                <span class="pagination-btn pagination-nav pagination-disabled">›</span>
                @endif

                {{-- Double arrow right (skip 10 pages forward) --}}
                @if($currentPage < $lastPage)
                <a href="{{ $paginator->appends(request()->query())->url($next10Page) }}"
                    class="pagination-btn pagination-nav">
                    »
                </a>
                @else
                <span class="pagination-btn pagination-nav pagination-disabled">»</span>
                @endif
            </div>
            @endif
        </div>
        @endif
    </div>
</section>
@endsection

