@extends('layouts.public')

@section('title', 'Informasi SPBE - SPBE Kabupaten Malang')

@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

    /* Global Poppins Font */
    body,
    * {
        font-family: 'Poppins', sans-serif;
    }

    /* Font Weight Defaults */
    p,
    span,
    div,
    a,
    li,
    td,
    th {
        font-weight: 400;
    }

    h1,
    h2,
    h3,
    h4,
    h5,
    h6,
    .font-bold,
    .font-semibold {
        font-weight: 600;
    }

    .font-bold,
    h1,
    h2,
    .section-title {
        font-weight: 700;
    }

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
        flex-shrink: 0;
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
    }

    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Pagination Styles */
    .pagination-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 40px;
        height: 40px;
        padding: 0 12px;
        border-radius: 50%;
        font-size: 16px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }

    .pagination-nav {
        background: #102A66;
        color: white;
        border-color: #102A66;
    }

    .pagination-nav:hover:not(.pagination-disabled) {
        background: #FFA84A;
        color: #102A66;
        border-color: #FFA84A;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(255, 168, 74, 0.3);
    }

    .pagination-number {
        background: white;
        color: #102A66;
        border-color: rgba(16, 42, 102, 0.2);
    }

    .pagination-number:hover {
        background: #ECF3FE;
        border-color: #102A66;
        color: #102A66;
        transform: translateY(-2px);
    }

    .pagination-active {
        background: #FFA84A;
        color: #102A66;
        border-color: #FFA84A;
        box-shadow: 0 4px 12px rgba(255, 168, 74, 0.3);
        cursor: default;
    }

    .pagination-disabled {
        background: #E5E7EB;
        color: #9CA3AF;
        border-color: #E5E7EB;
        cursor: not-allowed;
        opacity: 0.5;
    }

    .pagination-disabled:hover {
        transform: none;
        box-shadow: none;
    }

    /* Filter Styles */
    .filter-select {
        padding: 8px 12px;
        border: 2px solid rgba(16, 42, 102, 0.2);
        border-radius: 8px;
        background: white;
        color: #102A66;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        min-width: 120px;
    }

    .filter-select:focus {
        outline: none;
        border-color: #FFA84A;
        box-shadow: 0 0 0 3px rgba(255, 168, 74, 0.1);
    }

    .filter-select:disabled {
        background: #F3F4F6;
        color: #9CA3AF;
        cursor: not-allowed;
        opacity: 0.5;
    }

    /* Styling untuk option di dropdown */
    .filter-select option {
        background: white;
        color: #102A66;
        padding: 8px 12px;
    }

    /* Option yang terpilih (checked) */
    .filter-select option:checked {
        background: #102A66;
        color: white;
    }

    /* Saat select dalam focus, option yang terlihat akan menggunakan styling ini */
    .filter-select:focus option:checked {
        background: #102A66;
        color: white;
    }
</style>

<script>
    function updateFilter() {
        const month = document.getElementById('month').value;
        const year = document.getElementById('year').value;

        const url = new URL(window.location.href);

        // Hapus sort dan per_page dari URL saat filter berubah (reset ke default)
        url.searchParams.delete('sort');
        url.searchParams.delete('per_page');

        // Set month filter (remove if empty)
        if (month) {
            url.searchParams.set('month', month);
        } else {
            url.searchParams.delete('month');
        }

        // Set year filter (remove if empty)
        if (year) {
            url.searchParams.set('year', year);
        } else {
            url.searchParams.delete('year');
        }

        // Preserve search query if exists
        const searchParam = url.searchParams.get('search');
        if (!searchParam) {
            url.searchParams.delete('search');
        }

        url.searchParams.set('page', '1'); // Reset to page 1 when filter changes
        window.location.href = url.toString();
    }

    function updateSort() {
        const sort = document.getElementById('sort').value;
        const url = new URL(window.location.href);

        // Preserve all existing query params (search, month, year)
        // Set sort value (temporary, untuk request ini saja)
        url.searchParams.set('sort', sort);
        url.searchParams.set('page', '1');
        window.location.href = url.toString();
    }

    function updatePerPage() {
        const perPage = document.getElementById('per_page').value;
        const url = new URL(window.location.href);

        // Preserve all existing query params (search, month, year)
        // Set per_page value (temporary, untuk request ini saja)
        url.searchParams.set('per_page', perPage);
        url.searchParams.set('page', '1');
        window.location.href = url.toString();
    }

    function handleSearch(event) {
        event.preventDefault();
        const searchQuery = document.getElementById('searchInput').value.trim();
        const url = new URL(window.location.href);

        // Preserve existing query params (month, year, sort, per_page)
        if (searchQuery) {
            url.searchParams.set('search', searchQuery);
            // Reset sessionStorage agar search ditampilkan setelah reload
            sessionStorage.removeItem('informasi_spbe_page_loaded');
        } else {
            url.searchParams.delete('search');
        }

        url.searchParams.set('page', '1'); // Reset to page 1 when searching
        window.location.href = url.toString();
    }

    // Deteksi refresh dan hapus parameter search
    (function() {
        const pageKey = 'informasi_spbe_page_loaded';
        const urlParams = new URLSearchParams(window.location.search);
        const hasSearch = urlParams.has('search');

        // Cek jika ini adalah refresh (bukan navigasi pertama kali)
        if (sessionStorage.getItem(pageKey) && hasSearch) {
            // Ini adalah refresh dan ada parameter search, hapus search dan reload
            urlParams.delete('search');
            urlParams.delete('page'); // Reset ke page 1 juga
            const newUrl = window.location.pathname + (urlParams.toString() ? '?' + urlParams.toString() : '');
            window.location.href = newUrl;
            return;
        }

        // Tandai bahwa halaman sudah di-load
        sessionStorage.setItem(pageKey, 'true');
    })();

    // Menangani hover effect pada option dropdown
    document.addEventListener('DOMContentLoaded', function() {
        // Set sort dan per_page dari URL jika ada, atau default
        const sortSelect = document.getElementById('sort');
        const perPageSelect = document.getElementById('per_page');
        const urlParams = new URLSearchParams(window.location.search);

        if (sortSelect) {
            sortSelect.value = urlParams.get('sort') || 'newest'; // Baca dari URL atau default
        }
        if (perPageSelect) {
            perPageSelect.value = urlParams.get('per_page') || '12'; // Baca dari URL atau default
        }

        const filterSelects = document.querySelectorAll('.filter-select');

        filterSelects.forEach(select => {
            // Saat dropdown dibuka (focus)
            select.addEventListener('focus', function() {
                this.style.color = '#102A66';
            });

            // Saat dropdown ditutup (blur)
            select.addEventListener('blur', function() {
                this.style.color = '#102A66';
            });
        });

        // Disable bulan jika tahun belum dipilih
        const monthSelect = document.getElementById('month');
        const yearSelect = document.getElementById('year');

        function updateMonthDisabled() {
            if (!yearSelect.value) {
                monthSelect.disabled = true;
                monthSelect.style.opacity = '0.5';
                monthSelect.style.cursor = 'not-allowed';
            } else {
                monthSelect.disabled = false;
                monthSelect.style.opacity = '1';
                monthSelect.style.cursor = 'pointer';
            }
        }

        // Check on load
        updateMonthDisabled();

        // Update when year changes
        yearSelect.addEventListener('change', function() {
            updateMonthDisabled();
            // If year is cleared and month was selected, clear month too
            if (!this.value && monthSelect.value) {
                monthSelect.value = '';
            }
        });
    });
</script>
@endpush

@section('content')
<section class="pt-4 pb-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Breadcrumbs - Heuristic 3 & 6 --}}
        <x-breadcrumbs :items="[
            ['label' => 'Beranda', 'url' => route('public.home')],
            ['label' => 'Informasi SPBE']
        ]" />
        
        {{-- Search Results Banner --}}
        @if(isset($search) && !empty($search))
        <div class="bg-red-600 text-white px-6 py-2 mb-6 rounded-none flex items-center justify-between">
            <div class="flex items-center gap-4">
                <span class="text-sm md:text-base font-semibold">
                    Menampilkan postingan yang sesuai dengan penelusuran untuk "{{ $search }}"
                </span>
            </div>
            <a href="{{ route('informasi-spbe.index', request()->except('search')) }}"
                class="text-white hover:text-gray-200 font-semibold text-sm underline transition-colors">
                Tunjukkan semua
            </a>
        </div>
        @endif

        <header class="page-header text-center mb-6">
            <span class="page-header__eyebrow">Informasi SPBE</span>
            <h1 class="page-header__title">Seputar SPBE Kabupaten Malang</h1>
            <p class="page-header__description text-center">
                Kumpulan informasi terbaru seputar SPBE Kabupaten Malang.
            </p>
        </header>

        {{-- Filter Section --}}
        <div class="flex flex-wrap justify-between items-center gap-4 mb-6">
            {{-- Left: Date Filters --}}
            <div class="flex flex-wrap items-center gap-4">
                <div class="flex items-center gap-2">
                    <label for="month" class="text-sm font-semibold text-[#102A66]">Bulan:</label>
                    <select id="month" name="month" onchange="updateFilter()"
                        class="filter-select">
                        <option value="">Semua Bulan</option>
                        <option value="1" {{ $month == '1' ? 'selected' : '' }}>Januari</option>
                        <option value="2" {{ $month == '2' ? 'selected' : '' }}>Februari</option>
                        <option value="3" {{ $month == '3' ? 'selected' : '' }}>Maret</option>
                        <option value="4" {{ $month == '4' ? 'selected' : '' }}>April</option>
                        <option value="5" {{ $month == '5' ? 'selected' : '' }}>Mei</option>
                        <option value="6" {{ $month == '6' ? 'selected' : '' }}>Juni</option>
                        <option value="7" {{ $month == '7' ? 'selected' : '' }}>Juli</option>
                        <option value="8" {{ $month == '8' ? 'selected' : '' }}>Agustus</option>
                        <option value="9" {{ $month == '9' ? 'selected' : '' }}>September</option>
                        <option value="10" {{ $month == '10' ? 'selected' : '' }}>Oktober</option>
                        <option value="11" {{ $month == '11' ? 'selected' : '' }}>November</option>
                        <option value="12" {{ $month == '12' ? 'selected' : '' }}>Desember</option>
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <label for="year" class="text-sm font-semibold text-[#102A66]">Tahun:</label>
                    <select id="year" name="year" onchange="updateFilter()"
                        class="filter-select">
                        <option value="">Semua Tahun</option>
                        @foreach($years as $y)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Right: Search, Sort and Per Page --}}
            <div class="flex flex-wrap items-center gap-4">
                {{-- Search Box --}}
                <div class="flex items-center gap-2">
                    <form id="searchForm" class="flex items-center gap-2" onsubmit="handleSearch(event)">
                        <input
                            type="text"
                            id="searchInput"
                            name="search"
                            value="{{ $search ?? '' }}"
                            placeholder="Cari informasi..."
                            class="filter-select px-4 py-2 min-w-[200px]"
                            style="min-width: 200px;">
                        <button
                            type="submit"
                            class="bg-[#FFA84A] hover:bg-[#102A66] text-white font-semibold px-6 py-2 rounded-lg text-sm transition-all duration-200">
                            Cari
                        </button>
                    </form>
                </div>
                <div class="flex items-center gap-2">
                    <label for="sort" class="text-sm font-semibold text-[#102A66]">Sort by:</label>
                    <select id="sort" name="sort" onchange="updateSort()"
                        class="filter-select">
                        <option value="newest" selected>Newest</option>
                        <option value="oldest">Oldest</option>
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <label for="per_page" class="text-sm font-semibold text-[#102A66]">Show per page:</label>
                    <select id="per_page" name="per_page" onchange="updatePerPage()"
                        class="filter-select">
                        @for($i = 8; $i <= 40; $i +=4)
                            <option value="{{ $i }}" {{ $i == 12 ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                    </select>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            @forelse($articles as $item)
            <div class="news-card">
                <div class="news-image-wrapper">
                    @if($item['image_url'])
                    <img src="{{ $item['image_url'] }}" alt="{{ $item['title'] }}" class="news-image">
                    @else
                    <img src="https://via.placeholder.com/400x200?text=Informasi+SPBE" alt="Gambar Informasi" class="news-image">
                    @endif

                    @if($item['category'])
                    <span class="news-category-tag">{{ $item['category'] }}</span>
                    @endif
                </div>
                <div class="news-card-content">
                    <h2 class="news-title line-clamp-2">
                        <a href="{{ route('informasi-spbe.show', $item['slug']) }}">
                            {{ $item['title'] }}
                        </a>
                    </h2>
                    <div class="news-meta">
                        <span class="news-date">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            {{ optional($item['date'])->translatedFormat('d M Y') }}
                        </span>
                        <span class="news-meta-separator">|</span>
                        <a href="{{ route('informasi-spbe.show', $item['slug']) }}" class="news-read-article">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Baca Artikel
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <p class="col-span-full text-center text-slate-500">
                Informasi SPBE belum tersedia untuk saat ini.
            </p>
            @endforelse
        </div>

        <div class="mt-10">
            @if($articles->hasPages())
            <div class="flex justify-center items-center gap-2">
                @php
                $currentPage = $articles->currentPage();
                $lastPage = $articles->lastPage();
                $prevPage = max(1, $currentPage - 1);
                $nextPage = min($lastPage, $currentPage + 1);
                $prev10Page = max(1, $currentPage - 10);
                $next10Page = min($lastPage, $currentPage + 10);
                @endphp

                {{-- Double arrow left (skip 10 pages back) --}}
                @if($currentPage > 1)
                <a href="{{ $articles->appends(request()->query())->url($prev10Page) }}"
                    class="pagination-btn pagination-nav">
                    «
                </a>
                @else
                <span class="pagination-btn pagination-nav pagination-disabled">«</span>
                @endif

                {{-- Single arrow left (previous page) --}}
                @if($articles->onFirstPage())
                <span class="pagination-btn pagination-nav pagination-disabled">‹</span>
                @else
                <a href="{{ $articles->appends(request()->query())->previousPageUrl() }}"
                    class="pagination-btn pagination-nav">
                    ‹
                </a>
                @endif

                {{-- Page numbers --}}
                @foreach($articles->appends(request()->query())->getUrlRange(max(1, $currentPage - 2), min($lastPage, $currentPage + 2)) as $page => $url)
                @if($page == $currentPage)
                <span class="pagination-btn pagination-number pagination-active">{{ $page }}</span>
                @else
                <a href="{{ $url }}" class="pagination-btn pagination-number">{{ $page }}</a>
                @endif
                @endforeach

                {{-- Single arrow right (next page) --}}
                @if($articles->hasMorePages())
                <a href="{{ $articles->appends(request()->query())->nextPageUrl() }}"
                    class="pagination-btn pagination-nav">
                    ›
                </a>
                @else
                <span class="pagination-btn pagination-nav pagination-disabled">›</span>
                @endif

                {{-- Double arrow right (skip 10 pages forward) --}}
                @if($currentPage < $lastPage)
                    <a href="{{ $articles->appends(request()->query())->url($next10Page) }}"
                    class="pagination-btn pagination-nav">
                    »
                    </a>
                    @else
                    <span class="pagination-btn pagination-nav pagination-disabled">»</span>
                    @endif
            </div>
            @endif
        </div>
    </div>
</section>
@endsection