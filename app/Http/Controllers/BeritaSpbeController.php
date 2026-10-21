<?php

namespace App\Http\Controllers;

use App\Services\CircuitBreaker;
use App\Traits\HandlesApiErrors;
use Carbon\Carbon;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BeritaSpbeController extends Controller
{
    use HandlesApiErrors;

    private CircuitBreaker $circuitBreaker;

    public function __construct()
    {
        $this->circuitBreaker = new CircuitBreaker('berita_api', 3, 20, 60);
    }

    private const BASE_URL = 'https://web-admin.malangkab.go.id';

    private const LIST_ENDPOINT = self::BASE_URL.'/api/list-artikel/5';

    private const DETAIL_ENDPOINT = self::BASE_URL.'/api/detail-artikel/';

    public function index()
    {
        // Get sort and per_page from request, fallback to default
        $sort = request()->query('sort', 'newest'); // Default: newest
        $perPage = (int) request()->query('per_page', 12); // Default: 12

        // Get filter values (bulan, tahun, dan search)
        $month = request()->query('month');
        $year = request()->query('year');
        $search = request()->query('search');

        $page = max((int) request()->query('page', 1), 1);

        // Build cache key untuk hasil filter/sort (jika tidak ada filter, pakai cache utama)
        $hasFilters = ! empty($search) || ! empty($month) || ! empty($year);
        $filterCacheKey = $hasFilters
            ? 'berita_spbe_filtered_'.md5(serialize(['search' => $search, 'month' => $month, 'year' => $year, 'sort' => $sort]))
            : null;

        // Try to get from cache, but handle cache errors gracefully
        $articles = null;
        try {
            $cached = Cache::get('berita_spbe_index');
            // Validasi cache - hanya gunakan jika ada data dan tidak kosong
            if ($cached instanceof \Illuminate\Support\Collection && $cached->count() > 0) {
                $articles = $cached;
            } else {
                // Cache kosong atau tidak valid, fetch fresh
                $articles = $this->fetchArticles();
                // Hanya simpan ke cache jika ada data
                if ($articles->count() > 0) {
                    Cache::put('berita_spbe_index', $articles, now()->addMinutes(5));
                }
            }
        } catch (\Throwable $cacheException) {
            // If cache fails (e.g., MySQL server gone away), fetch directly without cache
            logger()->warning('Cache error in berita index, fetching directly', [
                'error' => $cacheException->getMessage(),
            ]);
            $articles = $this->fetchArticles();
        }

        // Ensure articles is a collection and not empty
        if (! $articles instanceof \Illuminate\Support\Collection) {
            $articles = collect($articles);
        }

        // Jika masih kosong setelah semua upaya, log warning
        if ($articles->count() === 0) {
            logger()->warning('No articles found after fetching from API', [
                'month' => $month,
                'year' => $year,
            ]);
        }

        // Cek cache hasil filter/sort jika ada filter
        $filteredArticles = null;
        if ($filterCacheKey && $hasFilters) {
            try {
                $filteredArticles = Cache::get($filterCacheKey);
                if ($filteredArticles instanceof \Illuminate\Support\Collection && $filteredArticles->count() > 0) {
                    $articles = $filteredArticles;
                    logger()->info('Using cached filtered articles', ['cache_key' => $filterCacheKey]);
                }
            } catch (\Throwable $e) {
                // Continue dengan filter normal
            }
        }

        // Apply search filter first (if search query exists) - hanya jika belum di-cache
        if ($filteredArticles === null) {
            if (! empty($search)) {
                $beforeCount = $articles->count();
                $searchLower = mb_strtolower(trim($search));
                $articles = $articles->filter(function ($item) use ($searchLower) {
                    $title = mb_strtolower($item['title'] ?? '');
                    $category = mb_strtolower($item['category'] ?? '');

                    return str_contains($title, $searchLower) || str_contains($category, $searchLower);
                })->values(); // Reset keys after filter

                logger()->info('Search filter applied', [
                    'search' => $search,
                    'before' => $beforeCount,
                    'after' => $articles->count(),
                ]);
            }

            // Apply month and year filter - optimized dengan pre-computed values
            if (! empty($month) && ! empty($year)) {
                $beforeCount = $articles->count();
                $monthInt = (int) $month;
                $yearInt = (int) $year;
                $articles = $articles->filter(function ($item) use ($monthInt, $yearInt) {
                    // Gunakan pre-computed values untuk performa lebih cepat
                    $itemYear = $item['_sort_year'] ?? null;
                    $itemMonth = $item['_sort_month'] ?? null;

                    // Early return jika tidak ada date
                    if ($itemYear === null || $itemMonth === null) {
                        return false;
                    }

                    return $itemMonth === $monthInt && $itemYear === $yearInt;
                })->values(); // Reset keys after filter

                logger()->info('Month and year filter applied', [
                    'month' => $month,
                    'year' => $year,
                    'before' => $beforeCount,
                    'after' => $articles->count(),
                ]);
            } elseif (! empty($year)) {
                // Filter by year only if month is not specified - optimized
                $beforeCount = $articles->count();
                $yearInt = (int) $year;
                $articles = $articles->filter(function ($item) use ($yearInt) {
                    // Gunakan pre-computed year untuk performa lebih cepat
                    $itemYear = $item['_sort_year'] ?? null;

                    // Early return jika tidak ada date
                    if ($itemYear === null) {
                        return false;
                    }

                    return $itemYear === $yearInt;
                })->values(); // Reset keys after filter

                logger()->info('Year filter applied', [
                    'year' => $year,
                    'before' => $beforeCount,
                    'after' => $articles->count(),
                ]);
            }

            // Apply sorting - optimized dengan pre-computed timestamp
            if ($sort === 'oldest') {
                $articles = $articles->sortBy(function ($item) {
                    // Gunakan pre-computed timestamp untuk performa lebih cepat
                    return $item['_sort_timestamp'] ?? PHP_INT_MAX;
                })->values();
            } else {
                // Default: newest first
                $articles = $articles->sortByDesc(function ($item) {
                    // Gunakan pre-computed timestamp untuk performa lebih cepat
                    return $item['_sort_timestamp'] ?? 0;
                })->values();
            }

            // Cache hasil filter/sort jika ada filter (untuk request berikutnya)
            if ($filterCacheKey && $hasFilters) {
                try {
                    Cache::put($filterCacheKey, $articles, now()->addMinutes(2)); // Cache pendek untuk hasil filter
                    logger()->info('Cached filtered articles', ['cache_key' => $filterCacheKey, 'count' => $articles->count()]);
                } catch (\Throwable $e) {
                    // Ignore cache errors
                }
            }
        }

        // Calculate total and validate page
        $totalArticles = $articles->count();
        $maxPage = max(1, (int) ceil($totalArticles / $perPage));

        // If page exceeds max, redirect to last page
        if ($page > $maxPage && $maxPage > 0) {
            $queryParams = request()->query();
            $queryParams['page'] = $maxPage;

            return redirect()->route('berita-kab-malang.index', $queryParams);
        }

        // Get articles for current page (before fetching id_pd)
        $currentPageArticles = $articles->forPage($page, $perPage)->values();

        // Resolve gambar menggunakan artikel_image_url dari list API
        // Only resolve images for articles that don't have image_url yet
        $currentPageArticlesWithImages = $this->resolveArticleImages($currentPageArticles);

        $paginated = new LengthAwarePaginator(
            $currentPageArticlesWithImages,
            $totalArticles,
            $perPage,
            $page,
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );

        // Get available years for filter dropdown - reuse articles yang sudah di-fetch
        // Tidak perlu fetch lagi, gunakan articles yang sudah ada (setelah filter tapi sebelum pagination)
        $years = $articles
            ->pluck('date')
            ->filter()
            ->map(fn ($date) => $date->year)
            ->unique()
            ->sortDesc()
            ->values()
            ->all();

        return view('public.berita-spbe.index', [
            'articles' => $paginated,
            'sort' => $sort,
            'perPage' => $perPage,
            'month' => $month,
            'year' => $year,
            'search' => $search,
            'years' => $years,
        ]);
    }

    public function show(string $slug)
    {
        // Gunakan hash untuk cache key agar tidak terlalu panjang
        // Tambahkan version untuk force refresh cache
        $cacheKey = 'berita_spbe_detail_v2_'.md5($slug);

        // Try to get from cache, but handle cache errors gracefully
        try {
            $article = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($slug) {
                return $this->fetchArticleDetail($slug);
            });
        } catch (\Throwable $cacheException) {
            // If cache fails (e.g., MySQL server gone away), fetch directly without cache
            logger()->warning('Cache error in berita show, fetching directly', [
                'slug' => $slug,
                'error' => $cacheException->getMessage(),
            ]);
            $article = $this->fetchArticleDetail($slug);
        }

        if (! $article) {
            abort(404);
        }

        // Ambil berita lainnya (exclude current article)
        try {
            $otherArticles = $this->getOtherArticles($slug, 10);
        } catch (\Throwable $cacheException) {
            logger()->warning('Cache error in getOtherArticles, fetching directly', [
                'slug' => $slug,
                'error' => $cacheException->getMessage(),
            ]);
            $otherArticles = $this->fetchOtherArticles($slug, 10);
        }

        return view('public.berita-spbe.show', [
            'article' => $article,
            'otherArticles' => $otherArticles,
        ]);
    }

    /**
     * Ambil berita lainnya (exclude current article)
     */
    private function getOtherArticles(string $currentSlug, int $limit = 10): array
    {
        try {
            return Cache::remember('berita_spbe_others_'.md5($currentSlug), now()->addMinutes(5), function () use ($currentSlug, $limit) {
                return $this->fetchOtherArticles($currentSlug, $limit);
            });
        } catch (\Throwable $cacheException) {
            logger()->warning('Cache error in getOtherArticles, fetching directly', [
                'slug' => $currentSlug,
                'error' => $cacheException->getMessage(),
            ]);

            return $this->fetchOtherArticles($currentSlug, $limit);
        }
    }

    /**
     * Fetch article detail without cache
     */
    private function fetchArticleDetail(string $slug): ?array
    {
        try {
            $response = $this->httpClient()->get(self::DETAIL_ENDPOINT.$slug);
        } catch (\Throwable $exception) {
            report($exception);

            return null;
        }

        if (! $response->successful()) {
            return null;
        }

        $payload = $response->json();

        if (is_array($payload) && isset($payload[0]) && is_array($payload[0])) {
            $payload = $payload[0];
        }

        if (! is_array($payload) || empty($payload['judul_artikel'])) {
            return null;
        }

        // Ambil id_pd untuk prefix gambar (id_pd selalu ada di detail artikel)
        $idPd = $payload['id_pd'] ?? null;
        $prefix = $idPd ? (int) $idPd : null;

        // Process content HTML (tidak perlu extract gambar dari content)
        $content = $payload['konten_artikel'] ?? '';

        // Replace relative image paths dalam content dengan prefix yang benar (jika ada prefix)
        if ($content && $prefix) {
            $content = preg_replace_callback(
                '#(src|href)=["\']([^"\']*uploads/artikel/[^"\']*)["\']#i',
                function ($matches) use ($prefix) {
                    $attr = $matches[1];
                    $imgPath = $matches[2];

                    // Jika sudah full URL, skip
                    if (Str::startsWith($imgPath, ['http://', 'https://'])) {
                        return $matches[0];
                    }

                    $path = ltrim($imgPath, '/');
                    $fullUrl = rtrim(self::BASE_URL, '/').'/'.$prefix.'//'.$path;

                    return $attr.'="'.$fullUrl.'"';
                },
                $content
            );
        }

        // Ambil gambar dari artikel_image_urls (array of objects dengan key 'artikel_image_url')
        $artikelImageUrls = $payload['artikel_image_urls'] ?? [];
        $allImages = [];

        if (is_array($artikelImageUrls) && ! empty($artikelImageUrls)) {
            foreach ($artikelImageUrls as $imageItem) {
                // Handle jika array of objects dengan key 'artikel_image_url'
                $imgPath = is_array($imageItem)
                    ? ($imageItem['artikel_image_url'] ?? $imageItem['url'] ?? $imageItem['image_url'] ?? null)
                    : $imageItem;

                if ($imgPath && is_string($imgPath)) {
                    // Build full URL dengan prefix
                    if (! Str::startsWith($imgPath, ['http://', 'https://'])) {
                        $path = ltrim($imgPath, '/');
                        if ($prefix) {
                            $fullUrl = rtrim(self::BASE_URL, '/').'/'.$prefix.'//'.$path;
                        } else {
                            $fullUrl = rtrim(self::BASE_URL, '/').'/'.$path;
                        }
                        $allImages[] = $fullUrl;
                    } else {
                        $allImages[] = $imgPath;
                    }
                }
            }
        }

        // Gambar pertama dari artikel_image_urls sebagai thumbnail (untuk ditampilkan di bawah judul)
        $thumbnailUrl = ! empty($allImages) ? $allImages[0] : null;

        // Gambar sisanya (selain yang pertama) untuk ditampilkan setelah body
        $otherImages = count($allImages) > 1 ? array_slice($allImages, 1) : [];

        return [
            'title' => $payload['judul_artikel'],
            'content' => $content,
            'date' => ! empty($payload['created_at']) ? Carbon::parse($payload['created_at']) : null,
            'thumbnail' => $thumbnailUrl,
            'other_images' => $otherImages,
        ];
    }

    /**
     * Fetch other articles without cache
     */
    private function fetchOtherArticles(string $currentSlug, int $limit): array
    {
        try {
            $response = $this->httpClient()->get(self::LIST_ENDPOINT);
        } catch (\Throwable $exception) {
            report($exception);

            return [];
        }

        if (! $response->successful()) {
            return [];
        }

        $items = $this->extractArticles($response->json());

        // Filter, sort, dan ambil artikel terbaru (exclude current)
        $topArticles = collect($items)
            ->filter(fn (array $item) => ! empty($item['judul_artikel'] ?? $item['judul'] ?? $item['title'] ?? null))
            ->map(function (array $item) {
                // Map dulu untuk mendapatkan slug dan date
                $title = $item['judul_artikel'] ?? $item['judul'] ?? $item['title'] ?? '-';
                $slug = ! empty($item['slug'])
                    ? $item['slug']
                    : Str::slug($title);

                $dateRaw = $item['created_at'] ?? $item['tanggal_artikel'] ?? $item['published_at'] ?? null;
                $date = null;
                if ($dateRaw) {
                    try {
                        $date = Carbon::parse($dateRaw);
                    } catch (\Throwable) {
                        $date = null;
                    }
                }

                return [
                    'item' => $item,
                    'slug' => $slug,
                    'date' => $date,
                    'timestamp' => $date ? $date->timestamp : 0,
                ];
            })
            ->filter(function (array $articleData) use ($currentSlug) {
                // Exclude current article
                return $articleData['slug'] !== $currentSlug;
            })
            ->sortByDesc('timestamp')
            ->take($limit)
            ->values()
            ->all();

        if (empty($topArticles)) {
            return [];
        }

        // Fetch id_pd dan gambar pertama dari detail API untuk setiap artikel
        foreach ($topArticles as &$articleData) {
            $item = $articleData['item'];
            $slug = $articleData['slug'];

            // Fetch id_pd dari detail API (jika belum ada)
            if (empty($item['id_pd'])) {
                try {
                    $idPd = $this->getIdPdFromSlug($slug);
                    if ($idPd) {
                        $item['id_pd'] = $idPd;
                    }
                } catch (\Throwable $e) {
                    logger()->warning('Failed to fetch id_pd for other article', [
                        'slug' => $slug,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            // Fetch gambar pertama dari artikel_image_urls via detail API
            // Hanya jika artikel_image_url belum ada di item
            if (empty($item['artikel_image_url'])) {
                $firstImage = $this->getFirstImageFromDetail($slug);
                if ($firstImage) {
                    $item['artikel_image_url'] = $firstImage;
                    logger()->info('Set first image for other article', [
                        'slug' => $slug,
                        'image_url' => $firstImage,
                    ]);
                } else {
                    logger()->warning('Failed to get first image for other article', [
                        'slug' => $slug,
                    ]);
                }
            }

            $articleData['item'] = $item;
        }
        unset($articleData);

        // Map artikel dengan id_pd dan gambar yang sudah didapat
        return collect($topArticles)
            ->map(function (array $articleData) {
                try {
                    return $this->mapArticle($articleData['item'], false);
                } catch (\Throwable $e) {
                    logger()->error('Failed to map other article', [
                        'slug' => $articleData['slug'] ?? 'unknown',
                        'error' => $e->getMessage(),
                    ]);

                    return null;
                }
            })
            ->filter() // Hapus null
            ->values()
            ->all();
    }

    /**
     * Ambil gambar pertama dari artikel_image_urls via detail API
     * SELALU fetch fresh dari detail API untuk memastikan id_pd dan path gambar sesuai
     */
    private function getFirstImageFromDetail(string $slug): ?string
    {
        // JANGAN gunakan cache untuk gambar - selalu fetch fresh untuk memastikan id_pd dan path benar
        try {
            $response = $this->httpClient()->get(self::DETAIL_ENDPOINT.$slug);
        } catch (\Throwable $exception) {
            logger()->warning('Failed to fetch first image from detail API', [
                'slug' => $slug,
                'error' => $exception->getMessage(),
            ]);

            return null;
        }

        if (! $response->successful()) {
            return null;
        }

        $payload = $response->json();

        if (is_array($payload) && isset($payload[0]) && is_array($payload[0])) {
            $payload = $payload[0];
        }

        if (! is_array($payload)) {
            return null;
        }

        // Ambil id_pd untuk prefix gambar - SELALU ambil fresh dari detail API
        $idPd = $payload['id_pd'] ?? null;
        $prefix = $idPd ? (int) $idPd : null;

        logger()->info('getFirstImageFromDetail - fresh fetch', [
            'slug' => $slug,
            'id_pd' => $idPd,
            'prefix' => $prefix,
        ]);

        // Ambil gambar pertama dari artikel_image_urls
        $artikelImageUrls = $payload['artikel_image_urls'] ?? [];

        if (is_array($artikelImageUrls) && ! empty($artikelImageUrls)) {
            // Ambil gambar pertama
            $firstImageItem = $artikelImageUrls[0];
            $imgPath = is_array($firstImageItem)
                ? ($firstImageItem['artikel_image_url'] ?? $firstImageItem['url'] ?? $firstImageItem['image_url'] ?? null)
                : $firstImageItem;

            logger()->info('getFirstImageFromDetail - first image item', [
                'slug' => $slug,
                'firstImageItem' => $firstImageItem,
                'imgPath' => $imgPath,
                'prefix' => $prefix,
            ]);

            if ($imgPath && is_string($imgPath)) {
                // Build full URL dengan prefix yang fresh dari detail API
                // Mendukung semua format: jpg, jpeg, png, gif, webp, dll
                if (! Str::startsWith($imgPath, ['http://', 'https://'])) {
                    // Pastikan path tidak ada spasi (trim)
                    $path = trim(ltrim($imgPath, '/'));
                    if ($prefix) {
                        $fullUrl = rtrim(self::BASE_URL, '/').'/'.$prefix.'//'.$path;
                    } else {
                        $fullUrl = rtrim(self::BASE_URL, '/').'/'.$path;
                    }
                    logger()->info('getFirstImageFromDetail - built URL', [
                        'slug' => $slug,
                        'imgPath' => $imgPath,
                        'path' => $path,
                        'prefix' => $prefix,
                        'fullUrl' => $fullUrl,
                    ]);

                    return $fullUrl;
                } else {
                    // Sudah full URL, langsung return (semua format didukung)
                    return $imgPath;
                }
            }
        }

        // Fallback: coba ambil dari artikel_image_url jika ada
        // Mendukung semua format: jpg, jpeg, png, gif, webp, dll
        $thumbnail = $payload['artikel_image_url'] ?? $payload['artikel_image'] ?? null;
        if ($thumbnail && $prefix) {
            if (! Str::startsWith($thumbnail, ['http://', 'https://'])) {
                // Pastikan path tidak ada spasi (trim)
                $path = trim(ltrim($thumbnail, '/'));
                $fullUrl = rtrim(self::BASE_URL, '/').'/'.$prefix.'//'.$path;
                logger()->info('getFirstImageFromDetail - fallback thumbnail', [
                    'slug' => $slug,
                    'thumbnail' => $thumbnail,
                    'path' => $path,
                    'prefix' => $prefix,
                    'fullUrl' => $fullUrl,
                ]);

                return $fullUrl;
            } else {
                // Sudah full URL, langsung return (semua format didukung)
                return $thumbnail;
            }
        }

        return null;
    }

    /**
     * Fetch articles from API
     */
    private function fetchArticles()
    {
        try {
            $response = $this->httpClient()->get(self::LIST_ENDPOINT);
        } catch (\Throwable $exception) {
            report($exception);

            return collect();
        }

        if (! $response->successful()) {
            return collect();
        }

        $jsonResponse = $response->json();
        $items = $this->extractArticles($jsonResponse);

        logger()->info('fetchArticles - API response', [
            'response_successful' => $response->successful(),
            'items_count' => count($items),
            'json_keys' => is_array($jsonResponse) ? array_keys($jsonResponse) : 'not_array',
        ]);

        if (empty($items)) {
            logger()->warning('No articles extracted from API response', [
                'json_response' => is_array($jsonResponse) ? 'array with '.count($jsonResponse).' items' : gettype($jsonResponse),
            ]);

            return collect();
        }

        // Filter, sort, dan ambil artikel terbaru terlebih dahulu
        $topArticles = collect($items)
            ->filter(fn (array $item) => ! empty($item['judul_artikel'] ?? $item['judul'] ?? $item['title'] ?? null))
            ->map(function (array $item) {
                // Map dulu untuk mendapatkan slug dan date
                $title = $item['judul_artikel'] ?? $item['judul'] ?? $item['title'] ?? '-';
                $slug = ! empty($item['slug'])
                    ? $item['slug']
                    : Str::slug($title);

                $dateRaw = $item['created_at'] ?? $item['tanggal_artikel'] ?? $item['published_at'] ?? null;
                $date = null;
                if ($dateRaw) {
                    try {
                        $date = Carbon::parse($dateRaw);
                    } catch (\Throwable) {
                        $date = null;
                    }
                }

                return [
                    'item' => $item,
                    'slug' => $slug,
                    'date' => $date,
                    'timestamp' => $date ? $date->timestamp : 0,
                ];
            })
            ->sortByDesc('timestamp')
            ->values()
            ->all();

        if (empty($topArticles)) {
            logger()->warning('No top articles after filtering');

            return collect();
        }

        // Map artikel TANPA fetch id_pd (untuk menghindari timeout)
        // id_pd akan di-fetch nanti hanya untuk artikel di halaman saat ini
        // Pre-compute timestamp untuk performa filter/sort yang lebih cepat
        $mapped = collect($topArticles)
            ->map(function (array $articleData) {
                try {
                    $mapped = $this->mapArticle($articleData['item'], false);
                    // Pre-compute timestamp untuk performa filter/sort
                    if ($mapped && isset($articleData['timestamp'])) {
                        $mapped['_sort_timestamp'] = $articleData['timestamp'];
                        $mapped['_sort_year'] = $mapped['date'] ? $mapped['date']->year : null;
                        $mapped['_sort_month'] = $mapped['date'] ? $mapped['date']->month : null;
                    }

                    return $mapped;
                } catch (\Throwable $e) {
                    logger()->error('Failed to map article', [
                        'slug' => $articleData['slug'] ?? 'unknown',
                        'error' => $e->getMessage(),
                    ]);

                    return null;
                }
            })
            ->filter() // Hapus null
            ->values();

        logger()->info('Mapped articles count', ['count' => $mapped->count()]);

        return $mapped;
    }

    private function httpClient(): PendingRequest
    {
        // SSL verification: disabled hanya di local, enabled di production
        $shouldVerify = ! app()->environment('local');

        return Http::connectTimeout(5)
            ->timeout(20)
            ->acceptJson()
            ->when(! $shouldVerify, fn (PendingRequest $request) => $request->withoutVerifying());
    }

    private function extractArticles(mixed $payload): array
    {
        if (! is_array($payload)) {
            return [];
        }

        if (array_is_list($payload)) {
            return array_values(array_filter($payload, 'is_array'));
        }

        $candidates = [
            data_get($payload, 'data'),
            data_get($payload, 'data.data'),
            data_get($payload, 'artikel'),
            data_get($payload, 'articles'),
            data_get($payload, 'items'),
            data_get($payload, 'results'),
        ];

        foreach ($candidates as $candidate) {
            if (is_array($candidate) && array_is_list($candidate)) {
                return array_values(array_filter($candidate, 'is_array'));
            }
        }

        return array_values(array_filter($payload, 'is_array'));
    }

    private function mapArticle(array $item, bool $fetchDetail = false): array
    {
        $imagePath = $item['artikel_image_url']
            ?? $item['artikel_image']
            ?? $item['image_url']
            ?? $item['image']
            ?? $item['thumbnail']
            ?? $item['foto']
            ?? null;

        $title = $item['judul_artikel'] ?? $item['judul'] ?? $item['title'] ?? '-';
        $slug = ! empty($item['slug'])
            ? $item['slug']
            : Str::slug($title ?: Str::random());

        // Ambil portal ID (id_pd)
        // Prioritas 1: Cek dari item (jika list API sudah include id_pd)
        $portalId = $item['id_pd'] ?? null;
        if ($portalId) {
            $portalId = (int) $portalId;
        } elseif ($fetchDetail) {
            // Prioritas 2: Ambil dari detail artikel via slug (id_pd selalu ada di detail)
            // Hanya fetch jika $fetchDetail = true
            $portalId = $this->getIdPdFromSlug($slug);
        }

        // Resolve image URL menggunakan helper
        // Jika imagePath sudah full URL (dari getFirstImageFromDetail), langsung gunakan
        $fullImageUrl = null;
        if ($imagePath) {
            // Jika sudah full URL, langsung gunakan
            if (Str::startsWith($imagePath, ['http://', 'https://'])) {
                $fullImageUrl = $imagePath;
            } else {
                // Jika relatif, resolve dengan portalId
                $fullImageUrl = $this->resolveImageUrl($imagePath, $portalId);
            }
        }

        $dateRaw = $item['created_at']
            ?? $item['tanggal_artikel']
            ?? $item['published_at']
            ?? null;

        $date = null;

        if ($dateRaw) {
            try {
                $date = Carbon::parse($dateRaw);
            } catch (\Throwable) {
                $date = null;
            }
        }

        return [
            'id' => $item['id_artikel'] ?? null,
            'title' => $title,
            'slug' => $slug,
            'image_url' => $fullImageUrl,
            '_image_path' => $imagePath, // Simpan imagePath asli untuk re-resolve nanti
            '_id_pd' => $portalId, // Simpan id_pd jika ada (dari item atau dari detail API)
            'category' => $item['nama_kategori_artikel'] ?? null,
            'date' => $date,
        ];
    }

    /**
     * Ambil id_pd dari slug dengan memanggil API detail-artikel
     */
    private function getIdPdFromSlug(string $slug): ?int
    {
        // Gunakan hash untuk cache key agar tidak terlalu panjang
        $cacheKey = 'id_pd_'.md5($slug);

        return Cache::remember($cacheKey, now()->addHours(24), function () use ($slug) {
            try {
                $response = $this->httpClient()->get(self::DETAIL_ENDPOINT.$slug);
            } catch (\Throwable $exception) {
                logger()->warning('Failed to fetch id_pd from detail API', [
                    'slug' => $slug,
                    'error' => $exception->getMessage(),
                ]);

                return null;
            }

            if (! $response->successful()) {
                return null;
            }

            $payload = $response->json();

            if (is_array($payload) && isset($payload[0]) && is_array($payload[0])) {
                $payload = $payload[0];
            }

            if (! is_array($payload)) {
                return null;
            }

            $idPd = $payload['id_pd'] ?? null;

            return $idPd ? (int) $idPd : null;
        });
    }

    /**
     * Resolve images untuk artikel
     * Optimized: Skip articles with existing image_url, batch cache lookup
     */
    private function resolveArticleImages($articles)
    {
        // Batch cache lookup untuk id_pd - lebih efisien
        $slugsNeedingIdPd = [];
        $cachedIdPds = [];

        foreach ($articles as $item) {
            // Skip jika sudah punya image_url
            if (! empty($item['image_url'])) {
                continue;
            }

            $slug = $item['slug'] ?? null;
            $idPd = $item['_id_pd'] ?? null;

            // Jika tidak ada id_pd tapi ada slug, tambahkan ke list untuk cache lookup
            if (! $idPd && $slug) {
                $cacheKey = 'id_pd_'.md5($slug);
                $cachedIdPd = Cache::get($cacheKey);
                if ($cachedIdPd) {
                    $cachedIdPds[$slug] = $cachedIdPd;
                } else {
                    $slugsNeedingIdPd[] = $slug;
                }
            }
        }

        // Resolve images untuk semua artikel
        return $articles->map(function ($item) use ($cachedIdPds) {
            // Jika image_url sudah ada dan valid, skip
            if (! empty($item['image_url'])) {
                return $item;
            }

            $imagePath = $item['_image_path'] ?? null;
            $slug = $item['slug'] ?? null;

            if ($imagePath && ! Str::startsWith($imagePath, ['http://', 'https://'])) {
                // Gunakan id_pd yang sudah ada di item atau dari batch cache lookup
                $idPd = $item['_id_pd'] ?? ($slug ? ($cachedIdPds[$slug] ?? null) : null);

                // Jika id_pd tidak ada tapi ada slug, fetch dari API
                if (! $idPd && $slug) {
                    try {
                        $idPd = $this->getIdPdFromSlug($slug);
                        if ($idPd) {
                            // Simpan ke item untuk referensi selanjutnya
                            $item['_id_pd'] = $idPd;
                            logger()->debug('Fetched id_pd for article in resolveArticleImages', [
                                'slug' => $slug,
                                'id_pd' => $idPd,
                            ]);
                        }
                    } catch (\Throwable $e) {
                        logger()->warning('Failed to fetch id_pd in resolveArticleImages', [
                            'slug' => $slug,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }

                if ($idPd) {
                    $fullImageUrl = $this->resolveImageUrl($imagePath, (int) $idPd);
                    if ($fullImageUrl) {
                        $item['image_url'] = $fullImageUrl;
                        logger()->debug('Resolved image URL in resolveArticleImages', [
                            'slug' => $slug,
                            'imagePath' => $imagePath,
                            'id_pd' => $idPd,
                            'fullImageUrl' => $fullImageUrl,
                        ]);
                    }
                }
            } elseif ($imagePath && Str::startsWith($imagePath, ['http://', 'https://'])) {
                // Sudah full URL, langsung gunakan
                $item['image_url'] = $imagePath;
            }

            return $item;
        });
    }

    /**
     * Resolve image URL dengan portal ID
     * Jika path sudah lengkap (http/https) → langsung return
     * Jika relatif → tambahkan base URL dengan portal ID
     * Tidak ada nilai default, jika portalId tidak ada return null
     * Mendukung semua format gambar: jpg, jpeg, png, gif, webp, dll
     */
    private function resolveImageUrl(?string $path, ?int $portalId = null): ?string
    {
        if (! $path) {
            return null;
        }

        // Jika path sudah lengkap (http/https), langsung return (semua format didukung)
        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        // Jika tidak ada portal ID, return null (tidak ada nilai default)
        if (! $portalId) {
            return null;
        }

        // Bersihkan path dari leading slash
        // Path bisa berisi format apapun: .jpg, .jpeg, .png, .gif, .webp, dll
        $path = ltrim($path, '/');

        // Build full URL dengan portal ID
        // Tidak ada filter format - semua format gambar didukung
        $fullUrl = rtrim(self::BASE_URL, '/').'/'.$portalId.'//'.$path;

        logger()->debug('Resolved image URL', [
            'original_path' => $path,
            'portal_id' => $portalId,
            'full_url' => $fullUrl,
        ]);

        return $fullUrl;
    }
}
