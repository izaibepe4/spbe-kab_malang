@extends('layouts.public')

@section('title', ($article['title'] ?? 'Berita SPBE') . ' - SPBE Kabupaten Malang')

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

    .article-other-images {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
        margin-top: 2rem;
    }

    .article-other-images img {
        width: 100%;
        height: auto;
        object-fit: cover;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .article-content {
        font-family: 'Poppins', sans-serif;
        font-size: 14px;
        font-weight: 400;
        line-height: 1.6;
        color: #334155;
    }

    .article-content p {
        font-family: 'Poppins', sans-serif;
        font-size: 14px;
        font-weight: 400;
        margin-bottom: 1rem;
    }

    .article-content h1,
    .article-content h2,
    .article-content h3,
    .article-content h4,
    .article-content h5,
    .article-content h6 {
        font-family: 'Poppins', sans-serif;
        font-weight: 600;
        color: #102A66;
    }

    .article-content a {
        color: #008BF9;
        text-decoration: underline;
    }

    .article-content ul,
    .article-content ol {
        margin-bottom: 1rem;
        padding-left: 1.5rem;
    }

    .article-content li {
        font-family: 'Poppins', sans-serif;
        font-size: 14px;
        font-weight: 400;
        margin-bottom: 0.5rem;
    }

    /* News Card Styles (untuk section jelajahi berita lainnya) */
    .news-card {
        background: white;
        border-radius: 12px;
        padding: 0;
        border: 2px solid rgba(16, 42, 102, 0.12);
        box-shadow: 0 18px 40px -20px rgba(16, 42, 102, 0.35);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .news-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 28px 60px -20px rgba(16, 42, 102, 0.45);
    }

    .news-card-content {
        padding: 18px;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .news-card-footer {
        padding: 0 18px 18px 18px;
        display: flex;
        align-items: flex-start;
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
        font-size: 16px;
        font-weight: 700;
        color: #102A66;
        margin-bottom: 8px;
        line-height: 1.4;
        transition: color 0.3s ease;
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
        font-size: 14px;
        color: #5f6b94;
        margin-bottom: 0;
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

    .news-readmore {
        color: #008BF9;
        font-weight: 600;
        font-size: 14px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        margin-top: auto;
    }

    .news-readmore:hover {
        text-decoration: underline;
    }

    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
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
<section class="pt-4 pb-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('berita-kab-malang.index') }}" class="inline-flex items-center text-sm text-[#FFA84A] hover:text-[#FF9A1F]">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Kembali ke daftar berita
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Main Content (Kiri) --}}
            <div class="lg:col-span-2">
                <article class="bg-[#ECF3FE] p-8 rounded-3xl shadow-sm">
                    <header class="page-header mb-6">
                        <h1 class="page-header__title">
                            {{ $article['title'] }}
                        </h1>
                        @if(!empty($article['date']))
                        <p class="text-slate-600 text-sm" style="font-family: 'Poppins', sans-serif;">
                            {{ $article['date']->translatedFormat('d F Y') }}
                        </p>
                        @endif
                    </header>

                    {{-- Thumbnail Image (Gambar pertama dari artikel_image_urls) --}}
                    @if(!empty($article['thumbnail']))
                    <div class="mb-8">
                        <img src="{{ $article['thumbnail'] }}" alt="{{ $article['title'] }}" class="article-thumbnail">
                    </div>
                    @endif

                    {{-- Body Content --}}
                    <div class="article-content prose max-w-none prose-indigo">
                        {!! $article['content'] !!}
                    </div>

                    {{-- Other Images (Gambar lainnya) --}}
                    @if(!empty($article['other_images']) && count($article['other_images']) > 0)
                    <div class="article-other-images">
                        @foreach($article['other_images'] as $image)
                        <img src="{{ $image }}" alt="{{ $article['title'] }}">
                        @endforeach
                    </div>
                    @endif
                </article>
            </div>

            {{-- Sidebar Related Article (Kanan) --}}
            @if(!empty($otherArticles) && count($otherArticles) > 0)
            <div class="lg:col-span-1">
                <div class="sticky top-8">
                    <h2 class="text-xl md:text-2xl font-bold text-[#102A66] mb-4">
                        Related Article
                    </h2>
                    <div class="space-y-3">
                        @foreach($otherArticles as $item)
                        <a href="{{ route('berita-kab-malang.show', $item['slug']) }}" class="related-article-card block">
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