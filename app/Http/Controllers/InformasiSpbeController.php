<?php

namespace App\Http\Controllers;

use App\Models\ProgresSpbe;
use Carbon\Carbon;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class InformasiSpbeController extends Controller
{
    private const BASE_URL = 'https://web-admin.malangkab.go.id';

    private const LIST_ENDPOINT = self::BASE_URL.'/api/list-artikel/3/1445';

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

        // Try to get from cache, but handle cache errors gracefully
        $articles = null;
        try {
            $cached = Cache::get('informasi_spbe_index');
            // Validasi cache - hanya gunakan jika ada data dan tidak kosong
            if ($cached instanceof \Illuminate\Support\Collection && $cached->count() > 0) {
                $articles = $cached;
            } else {
                // Cache kosong atau tidak valid, fetch fresh
                $articles = $this->fetchArticles();
                // Hanya simpan ke cache jika ada data
                if ($articles->count() > 0) {
                    Cache::put('informasi_spbe_index', $articles, now()->addMinutes(5));
                }
            }
        } catch (\Throwable $cacheException) {
            // If cache fails (e.g., MySQL server gone away), fetch directly without cache
            logger()->warning('Cache error in informasi index, fetching directly', [
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

        // Ambil semua progres SPBE dengan category "Indeks SPBE" untuk digabungkan dengan artikel
        $progresItems = $this->getProgresAsArticles();

        // Pastikan progresItems adalah Collection
        if (! $progresItems instanceof \Illuminate\Support\Collection) {
            $progresItems = collect($progresItems);
        }

        // Gabungkan semua progres dengan artikel sebelum filtering dan sorting
        // Gunakan concat() untuk menggabungkan Collection yang berisi array
        $allItems = $progresItems->concat($articles);

        // Apply search filter first (if search query exists) - pada semua item (artikel + progres)
        if (! empty($search)) {
            $beforeCount = $allItems->count();
            $searchLower = mb_strtolower(trim($search));
            $allItems = $allItems->filter(function ($item) use ($searchLower) {
                $title = mb_strtolower($item['title'] ?? '');
                $category = mb_strtolower($item['category'] ?? '');

                return str_contains($title, $searchLower) || str_contains($category, $searchLower);
            })->values(); // Reset keys after filter

            logger()->info('Search filter applied', [
                'search' => $search,
                'before' => $beforeCount,
                'after' => $allItems->count(),
            ]);
        }

        // Apply month and year filter - pada semua item (artikel + progres)
        if (! empty($month) && ! empty($year)) {
            $beforeCount = $allItems->count();
            $allItems = $allItems->filter(function ($item) use ($month, $year) {
                if (! $item['date'] || ! ($item['date'] instanceof \Carbon\Carbon)) {
                    return false;
                }

                return $item['date']->month == (int) $month && $item['date']->year == (int) $year;
            })->values(); // Reset keys after filter

            logger()->info('Month and year filter applied', [
                'month' => $month,
                'year' => $year,
                'before' => $beforeCount,
                'after' => $allItems->count(),
            ]);
        } elseif (! empty($year)) {
            // Filter by year only if month is not specified
            $beforeCount = $allItems->count();
            $allItems = $allItems->filter(function ($item) use ($year) {
                if (! $item['date'] || ! ($item['date'] instanceof \Carbon\Carbon)) {
                    return false;
                }

                return $item['date']->year == (int) $year;
            })->values(); // Reset keys after filter

            logger()->info('Year filter applied', [
                'year' => $year,
                'before' => $beforeCount,
                'after' => $allItems->count(),
            ]);
        }

        // Apply sorting pada semua item (artikel + progres) berdasarkan tanggal dari created_at
        // Pastikan menggunakan created_at dari API response untuk sorting yang akurat
        if ($sort === 'oldest') {
            $allItems = $allItems->sortBy(function ($item) {
                $date = $item['date'] ?? null;
                // Jika date null, beri timestamp 0 agar diurutkan paling awal
                $timestamp = $date instanceof \Carbon\Carbon ? $date->timestamp : 0;

                // Secondary sort: untuk progres items, gunakan tahun dan urutan
                $secondaryKey = 0;
                if (($item['_is_progres'] ?? false) && isset($item['_sort_year'])) {
                    // Untuk oldest, tahun lebih kecil dulu, lalu urutan lebih kecil
                    $secondaryKey = ($item['_sort_year'] ?? 9999) * 1000000 + ($item['_sort_order'] ?? 9999);
                } else {
                    // Untuk artikel biasa, gunakan ID sebagai secondary sort
                    $secondaryKey = $item['id'] ?? 0;
                }

                // Gabungkan timestamp dan secondary key untuk multi-level sorting
                // Format: timestamp * 1000000000 + secondaryKey
                // Pastikan timestamp tidak negatif
                return (max(0, $timestamp) * 1000000000) + $secondaryKey;
            })->values();
        } else {
            // Default: newest first - urutkan berdasarkan created_at dari API
            $allItems = $allItems->sortByDesc(function ($item) {
                $date = $item['date'] ?? null;
                // Jika date null, beri timestamp 0 agar diurutkan paling akhir
                $timestamp = $date instanceof \Carbon\Carbon ? $date->timestamp : 0;

                // Secondary sort: untuk progres items, gunakan tahun dan urutan
                $secondaryKey = 0;
                if (($item['_is_progres'] ?? false) && isset($item['_sort_year'])) {
                    // Untuk newest, tahun lebih besar dulu, lalu urutan lebih kecil (untuk tahun yang sama)
                    // Gunakan negatif untuk urutan agar ascending dalam descending sort
                    $secondaryKey = ($item['_sort_year'] ?? 0) * 1000000 - ($item['_sort_order'] ?? 0);
                } else {
                    // Untuk artikel biasa, gunakan ID sebagai secondary sort (negatif untuk descending)
                    $secondaryKey = -($item['id'] ?? 0);
                }

                // Gabungkan timestamp dan secondary key untuk multi-level sorting
                // Format: timestamp * 1000000000 + secondaryKey
                // Pastikan timestamp tidak negatif
                return (max(0, $timestamp) * 1000000000) + $secondaryKey;
            })->values();
        }

        // Calculate total dengan progres
        $totalWithProgres = $allItems->count();
        $maxPage = max(1, (int) ceil($totalWithProgres / $perPage));

        // If page exceeds max, redirect to last page
        if ($page > $maxPage && $maxPage > 0) {
            $queryParams = request()->query();
            $queryParams['page'] = $maxPage;

            return redirect()->route('informasi-spbe.index', $queryParams);
        }

        // Get items for current page (before fetching id_pd)
        $currentPageItems = $allItems->forPage($page, $perPage)->values();

        // Resolve gambar menggunakan artikel_image_url dari list API
        $currentPageItemsWithImages = $this->resolveArticleImages($currentPageItems);

        $paginated = new LengthAwarePaginator(
            $currentPageItemsWithImages,
            $totalWithProgres,
            $perPage,
            $page,
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );

        // Get available years for filter dropdown
        try {
            $allArticles = Cache::remember('informasi_spbe_index', now()->addMinutes(5), function () {
                return $this->fetchArticles();
            });
        } catch (\Throwable $cacheException) {
            $allArticles = $this->fetchArticles();
        }

        $years = $allArticles
            ->pluck('date')
            ->filter()
            ->map(fn ($date) => $date->year)
            ->unique()
            ->sortDesc()
            ->values()
            ->all();

        return view('public.informasi-spbe.index', [
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
        // Cek jika ini adalah progres (slug dimulai dengan "progres-")
        if (Str::startsWith($slug, 'progres-')) {
            $progresId = (int) str_replace('progres-', '', $slug);
            $progres = ProgresSpbe::query()
                ->find($progresId);

            if (! $progres) {
                abort(404);
            }

            // Get related articles (exclude current progres)
            $otherArticles = $this->getOtherArticles($slug, 10);

            return view('public.informasi-spbe.show-progres', [
                'progres' => $progres,
                'otherArticles' => $otherArticles,
            ]);
        }

        // Gunakan hash untuk cache key agar tidak terlalu panjang
        $cacheKey = 'informasi_spbe_detail_v2_'.md5($slug);

        // Try to get from cache, but handle cache errors gracefully
        try {
            $article = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($slug) {
                return $this->fetchArticleDetail($slug);
            });
        } catch (\Throwable $cacheException) {
            logger()->warning('Cache error in informasi show, fetching directly', [
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

        return view('public.informasi-spbe.show', [
            'article' => $article,
            'otherArticles' => $otherArticles,
        ]);
    }

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

        $articles = $this->extractArticles($response->json());

        if (empty($articles)) {
            logger()->warning('No articles extracted from API response');

            return collect();
        }

        // Map articles
        $mapped = collect($articles)
            ->filter(fn (array $item) => ! empty($item['judul_artikel'] ?? $item['judul'] ?? $item['title'] ?? null))
            ->map(function (array $item) {
                return $this->mapArticle($item, false);
            })
            ->filter()
            ->values();

        return $mapped;
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

    private function mapArticle(array $item, bool $fetchIdPd = false): ?array
    {
        try {
            $title = $item['judul_artikel'] ?? $item['judul'] ?? $item['title'] ?? '-';

            if (empty($title) || $title === '-') {
                return null;
            }

            $slug = ! empty($item['slug'])
                ? $item['slug']
                : Str::slug($title ?: Str::random());

            if (empty($slug)) {
                return null;
            }

            // Ambil image path
            $imagePath = $item['artikel_image_url']
                ?? $item['artikel_image']
                ?? $item['image_url']
                ?? $item['image']
                ?? $item['thumbnail']
                ?? $item['foto']
                ?? null;

            // Ambil portal ID (id_pd)
            $portalId = $item['id_pd'] ?? null;
            if ($portalId) {
                $portalId = (int) $portalId;
            }

            // Resolve image URL
            $fullImageUrl = null;
            if ($imagePath) {
                try {
                    if ($imagePath && ! Str::startsWith($imagePath, ['http://', 'https://'])) {
                        if ($fetchIdPd && ! $portalId) {
                            $portalId = $this->getIdPdFromSlug($slug);
                        }
                        if ($portalId) {
                            $fullImageUrl = $this->resolveImageUrl($imagePath, $portalId);
                        }
                    } else {
                        $fullImageUrl = $imagePath;
                    }
                } catch (\Throwable $e) {
                    logger()->warning('Failed to resolve image URL', [
                        'imagePath' => $imagePath,
                        'portalId' => $portalId,
                        'error' => $e->getMessage(),
                    ]);
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
                '_image_path' => $imagePath,
                '_id_pd' => $portalId,
                'category' => $item['nama_kategori_artikel'] ?? null,
                'date' => $date,
            ];
        } catch (\Throwable $e) {
            logger()->error('Error in mapArticle', [
                'item' => $item,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    private function fetchArticleDetail(string $slug): ?array
    {
        $cacheKey = 'informasi_spbe_detail_'.md5($slug);

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($slug) {
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

            // Process content HTML - sama dengan BeritaSpbeController
            // Coba ambil dari konten_artikel dulu, jika tidak ada baru isi_artikel
            $content = $payload['konten_artikel'] ?? $payload['isi_artikel'] ?? $payload['content'] ?? $payload['body'] ?? '';

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
        });
    }

    private function getOtherArticles(string $currentSlug, int $limit): array
    {
        $cacheKey = 'informasi_spbe_others_'.md5($currentSlug.'_'.$limit);

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($currentSlug, $limit) {
            return $this->fetchOtherArticles($currentSlug, $limit);
        });
    }

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
            ->filter(fn ($articleData) => $articleData['slug'] !== $currentSlug)
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
                        logger()->debug('Fetched id_pd for informasi other article', [
                            'slug' => $slug,
                            'id_pd' => $idPd,
                        ]);
                    }
                } catch (\Throwable $e) {
                    logger()->warning('Failed to fetch id_pd for informasi other article', [
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
                    logger()->info('Set first image for informasi other article', [
                        'slug' => $slug,
                        'image_url' => $firstImage,
                    ]);
                } else {
                    logger()->warning('Failed to get first image for informasi other article', [
                        'slug' => $slug,
                    ]);
                }
            }

            $articleData['item'] = $item;
        }
        unset($articleData);

        // Map artikel dengan id_pd dan gambar yang sudah didapat
        $mapped = collect($topArticles)
            ->map(function (array $articleData) {
                try {
                    return $this->mapArticle($articleData['item'], false);
                } catch (\Throwable $e) {
                    logger()->error('Failed to map informasi other article', [
                        'slug' => $articleData['slug'] ?? 'unknown',
                        'error' => $e->getMessage(),
                    ]);

                    return null;
                }
            })
            ->filter() // Hapus null
            ->values()
            ->all();

        return $mapped;
    }

    /**
     * Ambil gambar pertama dari detail artikel
     */
    private function getFirstImageFromDetail(string $slug): ?string
    {
        // JANGAN gunakan cache untuk gambar - selalu fetch fresh untuk memastikan id_pd dan path benar
        try {
            $response = $this->httpClient()->get(self::DETAIL_ENDPOINT.$slug);
        } catch (\Throwable $exception) {
            logger()->warning('Failed to fetch first image from detail API (informasi)', [
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

        logger()->info('getFirstImageFromDetail - fresh fetch (informasi)', [
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

            logger()->info('getFirstImageFromDetail - first image item (informasi)', [
                'slug' => $slug,
                'firstImageItem' => $firstImageItem,
                'imgPath' => $imgPath,
                'prefix' => $prefix,
            ]);

            if ($imgPath && is_string($imgPath)) {
                // Build full URL dengan prefix yang fresh dari detail API
                if (! Str::startsWith($imgPath, ['http://', 'https://'])) {
                    // Pastikan path tidak ada spasi (trim)
                    $path = trim(ltrim($imgPath, '/'));
                    if ($prefix) {
                        $fullUrl = rtrim(self::BASE_URL, '/').'/'.$prefix.'//'.$path;
                    } else {
                        $fullUrl = rtrim(self::BASE_URL, '/').'/'.$path;
                    }
                    logger()->info('getFirstImageFromDetail - built URL (informasi)', [
                        'slug' => $slug,
                        'imgPath' => $imgPath,
                        'path' => $path,
                        'prefix' => $prefix,
                        'fullUrl' => $fullUrl,
                    ]);

                    return $fullUrl;
                } else {
                    // Sudah full URL, langsung return
                    return $imgPath;
                }
            }
        }

        // Fallback: coba ambil dari artikel_image_url jika ada
        $thumbnail = $payload['artikel_image_url'] ?? $payload['artikel_image'] ?? null;
        if ($thumbnail && $prefix) {
            if (! Str::startsWith($thumbnail, ['http://', 'https://'])) {
                // Pastikan path tidak ada spasi (trim)
                $path = trim(ltrim($thumbnail, '/'));
                $fullUrl = rtrim(self::BASE_URL, '/').'/'.$prefix.'//'.$path;
                logger()->info('getFirstImageFromDetail - fallback thumbnail (informasi)', [
                    'slug' => $slug,
                    'thumbnail' => $thumbnail,
                    'path' => $path,
                    'prefix' => $prefix,
                    'fullUrl' => $fullUrl,
                ]);

                return $fullUrl;
            } else {
                // Sudah full URL, langsung return
                return $thumbnail;
            }
        }

        return null;
    }

    /**
     * Resolve images untuk artikel
     * Hanya menggunakan id_pd yang sudah ada di item atau cache
     */
    private function resolveArticleImages($articles)
    {
        // Resolve images untuk semua artikel menggunakan id_pd yang sudah ada
        return $articles->map(function ($item) {
            // Jika image_url sudah ada dan valid, skip
            if (! empty($item['image_url'])) {
                return $item;
            }

            $imagePath = $item['_image_path'] ?? null;
            $slug = $item['slug'] ?? null;

            if ($imagePath && ! Str::startsWith($imagePath, ['http://', 'https://'])) {
                // Gunakan id_pd yang sudah ada di item
                $idPd = $item['_id_pd'] ?? null;

                // Jika tidak ada di item, cek cache
                if (! $idPd && $slug) {
                    $cacheKey = 'id_pd_informasi_'.md5($slug);
                    $idPd = Cache::get($cacheKey);
                }

                // Jika masih tidak ada id_pd dan ada slug, fetch dari detail API
                if (! $idPd && $slug) {
                    try {
                        $idPd = $this->getIdPdFromSlug($slug);
                        if ($idPd) {
                            // Simpan ke item untuk referensi selanjutnya
                            $item['_id_pd'] = $idPd;
                            logger()->debug('Fetched id_pd for informasi article in resolveArticleImages', [
                                'slug' => $slug,
                                'id_pd' => $idPd,
                            ]);
                        }
                    } catch (\Throwable $e) {
                        logger()->warning('Failed to fetch id_pd in resolveArticleImages (informasi)', [
                            'slug' => $slug,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }

                // Jika masih tidak ada id_pd, coba ambil gambar langsung dari detail API
                if (! $idPd && $slug) {
                    try {
                        $firstImage = $this->getFirstImageFromDetail($slug);
                        if ($firstImage) {
                            $item['image_url'] = $firstImage;
                            logger()->debug('Got image from detail API in resolveArticleImages (informasi)', [
                                'slug' => $slug,
                                'image_url' => $firstImage,
                            ]);

                            return $item;
                        }
                    } catch (\Throwable $e) {
                        logger()->warning('Failed to get image from detail API in resolveArticleImages (informasi)', [
                            'slug' => $slug,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }

                if ($idPd) {
                    $fullImageUrl = $this->resolveImageUrl($imagePath, (int) $idPd);
                    if ($fullImageUrl) {
                        $item['image_url'] = $fullImageUrl;
                        logger()->debug('Resolved image URL in resolveArticleImages (informasi)', [
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
     * Serve PDF Progres dengan header inline untuk preview di halaman
     */
    public function viewProgresPdf(int $id)
    {
        $progres = ProgresSpbe::query()
            ->findOrFail($id);

        // Wajib punya path file
        abort_unless($progres->file_pdf_path, 404);

        $filePath = ltrim($progres->file_pdf_path, '/');

        // Pastikan file ada di storage
        if (! \Illuminate\Support\Facades\Storage::disk('public')->exists($filePath)) {
            abort(404, 'File tidak ditemukan');
        }

        $filename = Str::slug($progres->judul ?: 'progres').'.pdf';
        $fullPath = \Illuminate\Support\Facades\Storage::disk('public')->path($filePath);

        // Serve PDF dengan header inline untuk preview di iframe (bukan download)
        return response()->file($fullPath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.addslashes($filename).'"',
            'Cache-Control' => 'public, max-age=3600',
            'Accept-Ranges' => 'bytes',
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'SAMEORIGIN', // Allow iframe dari same origin
        ]);
    }

    /**
     * Konversi ProgresSpbe menjadi format artikel untuk ditampilkan di index
     */
    private function getProgresAsArticles()
    {
        // Jangan lakukan pre-sorting di sini, karena akan di-sort setelah merge dengan articles
        $progresItems = ProgresSpbe::query()
            ->get(['id', 'judul', 'gambar', 'file_pdf_path', 'nilai', 'tahun', 'created_at']);

        return $progresItems->map(function ($progres) {
            // Gunakan created_at untuk sorting yang akurat berdasarkan waktu upload
            // Jika tidak ada created_at, gunakan tahun untuk membuat date representatif
            $date = null;
            if ($progres->created_at) {
                // Gunakan created_at yang sebenarnya untuk sorting yang akurat
                $date = Carbon::parse($progres->created_at);
            } elseif ($progres->tahun) {
                // Fallback: gunakan akhir tahun jika tidak ada created_at
                $date = Carbon::createFromDate((int) $progres->tahun, 12, 31);
            } else {
                $date = Carbon::now();
            }

            return [
                'id' => 'progres_'.$progres->id,
                'title' => $progres->judul,
                'slug' => 'progres-'.$progres->id,
                'image_url' => $progres->gambar ? $progres->gambar_url : null,
                '_image_path' => $progres->gambar,
                '_id_pd' => null,
                'category' => 'Indeks SPBE',
                'date' => $date,
                '_is_progres' => true, // Flag untuk membedakan dengan artikel biasa
                '_progres_id' => $progres->id,
                '_progres_pdf' => $progres->file_pdf_path,
                '_sort_year' => $progres->tahun ? (int) $progres->tahun : null, // Untuk secondary sort
                '_sort_order' => $progres->urutan ?? 0, // Untuk secondary sort
            ];
        });
    }

    private function getIdPdFromSlug(string $slug): ?int
    {
        $cacheKey = 'id_pd_informasi_'.md5($slug);

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

    private function resolveImageUrl(?string $path, ?int $portalId = null): ?string
    {
        if (! $path) {
            return null;
        }

        // Jika path sudah lengkap (http/https), langsung return
        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        // Jika tidak ada portal ID, return null
        if (! $portalId) {
            return null;
        }

        // Bersihkan path dari leading slash
        $path = ltrim($path, '/');

        // Build full URL dengan portal ID
        return rtrim(self::BASE_URL, '/').'/'.$portalId.'//'.$path;
    }

    private function httpClient(): PendingRequest
    {
        // SSL verification: disabled hanya di local, enabled di production
        $shouldVerify = ! app()->environment('local');

        return Http::connectTimeout(5)
            ->timeout(20)
            ->acceptJson()
            ->retry(2, 1000, throw: false)
            ->when(! $shouldVerify, fn (PendingRequest $request) => $request->withoutVerifying());
    }
}
