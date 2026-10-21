<?php

namespace App\Http\Controllers;

use App\Models\ProgresSpbe;
use Carbon\Carbon;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SearchController extends Controller
{
    private const BASE_URL = 'https://web-admin.malangkab.go.id';

    private const BERITA_ENDPOINT = self::BASE_URL.'/api/list-artikel/5';

    private const INFORMASI_ENDPOINT = self::BASE_URL.'/api/list-artikel/3/1445';

    public function index()
    {
        $search = request()->query('q') ?? request()->query('search', '');

        // Sanitize search input
        $search = \App\Helpers\InputSanitizer::sanitizeSearchQuery($search);

        $page = max((int) request()->query('page', 1), 1);
        $perPage = 12;

        if (empty(trim($search))) {
            return view('public.search.index', [
                'search' => '',
                'results' => collect(),
                'paginator' => null,
            ]);
        }

        $searchLower = mb_strtolower(trim($search));

        // Ambil berita
        $beritaResults = $this->searchBerita($searchLower);

        // Ambil informasi (termasuk progres)
        $informasiResults = $this->searchInformasi($searchLower);

        // Gabungkan semua hasil
        $allResults = $beritaResults->merge($informasiResults)->values();

        // Sort by date (newest first)
        $allResults = $allResults->sortByDesc(function ($item) {
            return $item['date'] ? $item['date']->timestamp : 0;
        })->values();

        // Paginate
        $total = $allResults->count();
        $currentPageItems = $allResults->slice(($page - 1) * $perPage, $perPage)->values();

        // HANYA resolve images untuk artikel yang akan ditampilkan di halaman ini (setelah pagination)
        // Ini untuk menghindari timeout karena terlalu banyak request ke detail API
        $currentPageItems = $this->resolveArticleImages($currentPageItems);

        $paginator = new LengthAwarePaginator(
            $currentPageItems,
            $total,
            $perPage,
            $page,
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );

        return view('public.search.index', [
            'search' => $search,
            'results' => $currentPageItems,
            'paginator' => $paginator,
        ]);
    }

    private function searchBerita(string $searchQuery)
    {
        $articles = collect();

        try {
            // Untuk search, bypass cache untuk mendapatkan data terbaru
            $articles = $this->fetchArticles(self::BERITA_ENDPOINT);

            if ($articles->isEmpty()) {
                // Jika fetch gagal, coba ambil dari cache
                $cached = Cache::get('berita_spbe_index');
                if ($cached instanceof \Illuminate\Support\Collection && $cached->count() > 0) {
                    $articles = $cached;
                    logger()->info('Using cached berita data for search');
                }
            }
        } catch (\Throwable $e) {
            logger()->error('Error fetching berita for search', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Fallback ke cache jika ada
            try {
                $cached = Cache::get('berita_spbe_index');
                if ($cached instanceof \Illuminate\Support\Collection && $cached->count() > 0) {
                    $articles = $cached;
                }
            } catch (\Throwable $cacheError) {
                logger()->error('Cache also failed', ['error' => $cacheError->getMessage()]);
            }
        }

        if (! $articles instanceof \Illuminate\Support\Collection) {
            $articles = collect();
        }

        logger()->info('Berita articles for search', [
            'total' => $articles->count(),
            'search_query' => $searchQuery,
        ]);

        // Filter berdasarkan search query
        $filtered = $articles->filter(function ($item) use ($searchQuery) {
            $title = mb_strtolower($item['title'] ?? '');
            $category = mb_strtolower($item['category'] ?? '');
            $searchLower = mb_strtolower($searchQuery);

            return str_contains($title, $searchLower) || str_contains($category, $searchLower);
        })->map(function ($item) {
            $item['type'] = 'berita';
            $item['route'] = route('berita-kab-malang.show', $item['slug']);

            return $item;
        });

        // JANGAN resolve images di sini - akan dilakukan setelah pagination untuk menghindari timeout

        logger()->info('Filtered berita results', ['count' => $filtered->count()]);

        return $filtered;
    }

    private function searchInformasi(string $searchQuery)
    {
        $results = collect();

        // Ambil artikel informasi dari API (menggunakan endpoint /6 seperti InformasiSpbeController)
        $articles = collect();

        try {
            // Untuk search, bypass cache untuk mendapatkan data terbaru
            $articles = $this->fetchArticles(self::INFORMASI_ENDPOINT);

            if ($articles->isEmpty()) {
                // Jika fetch gagal, coba ambil dari cache
                $cached = Cache::get('informasi_spbe_index');
                if ($cached instanceof \Illuminate\Support\Collection && $cached->count() > 0) {
                    $articles = $cached;
                    logger()->info('Using cached informasi data for search');
                }
            }
        } catch (\Throwable $e) {
            logger()->error('Error fetching informasi for search', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Fallback ke cache jika ada
            try {
                $cached = Cache::get('informasi_spbe_index');
                if ($cached instanceof \Illuminate\Support\Collection && $cached->count() > 0) {
                    $articles = $cached;
                }
            } catch (\Throwable $cacheError) {
                logger()->error('Cache also failed', ['error' => $cacheError->getMessage()]);
            }
        }

        if (! $articles instanceof \Illuminate\Support\Collection) {
            $articles = collect();
        }

        logger()->info('Informasi articles for search', [
            'total' => $articles->count(),
            'search_query' => $searchQuery,
        ]);

        // Filter artikel informasi
        $filteredArticles = $articles->filter(function ($item) use ($searchQuery) {
            $title = mb_strtolower($item['title'] ?? '');
            $category = mb_strtolower($item['category'] ?? '');
            $searchLower = mb_strtolower($searchQuery);

            return str_contains($title, $searchLower) || str_contains($category, $searchLower);
        })->map(function ($item) {
            $item['type'] = 'informasi';
            $item['route'] = route('informasi-spbe.show', $item['slug']);

            return $item;
        });

        // JANGAN resolve images di sini - akan dilakukan setelah pagination untuk menghindari timeout

        logger()->info('Filtered informasi results', ['count' => $filteredArticles->count()]);

        $results = $results->merge($filteredArticles);

        // Ambil progres SPBE
        $progresItems = ProgresSpbe::query()
            ->get(['id', 'judul', 'gambar', 'file_pdf_path', 'nilai', 'created_at']);

        $filteredProgres = $progresItems->filter(function ($progres) use ($searchQuery) {
            $judul = mb_strtolower($progres->judul ?? '');

            return str_contains($judul, $searchQuery);
        })->map(function ($progres) {
            return [
                'id' => 'progres_'.$progres->id,
                'title' => $progres->judul,
                'slug' => 'progres-'.$progres->id,
                'image_url' => $progres->gambar ? $progres->gambar_url : null,
                'category' => 'Indeks SPBE',
                'date' => $progres->created_at ? Carbon::parse($progres->created_at) : Carbon::now(),
                'type' => 'progres',
                'route' => route('informasi-spbe.show', 'progres-'.$progres->id),
            ];
        });

        $results = $results->merge($filteredProgres);

        return $results;
    }

    private function fetchArticles(string $endpoint)
    {
        logger()->info('Fetching articles from endpoint', ['endpoint' => $endpoint]);

        try {
            $response = $this->httpClient()->get($endpoint);

            logger()->info('API response received', [
                'endpoint' => $endpoint,
                'status' => $response->status(),
                'successful' => $response->successful(),
            ]);
        } catch (\Throwable $exception) {
            logger()->error('Failed to fetch articles from API', [
                'endpoint' => $endpoint,
                'error' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);
            report($exception);

            return collect();
        }

        if (! $response->successful()) {
            logger()->warning('API response not successful', [
                'endpoint' => $endpoint,
                'status' => $response->status(),
                'body' => substr($response->body(), 0, 500), // First 500 chars for debugging
            ]);

            return collect();
        }

        $jsonResponse = $response->json();

        logger()->info('JSON response parsed', [
            'endpoint' => $endpoint,
            'response_type' => gettype($jsonResponse),
            'is_array' => is_array($jsonResponse),
            'keys' => is_array($jsonResponse) ? array_keys($jsonResponse) : 'not_array',
        ]);

        $articles = $this->extractArticles($jsonResponse);

        logger()->info('Articles extracted', [
            'endpoint' => $endpoint,
            'extracted_count' => count($articles),
        ]);

        if (empty($articles)) {
            logger()->warning('No articles extracted from API response', [
                'endpoint' => $endpoint,
                'response_type' => gettype($jsonResponse),
                'response_sample' => is_array($jsonResponse) ? json_encode(array_slice($jsonResponse, 0, 2)) : 'not_array',
            ]);

            return collect();
        }

        // Map articles - gunakan pendekatan yang sama dengan BeritaSpbeController
        $mapped = collect($articles)
            ->filter(fn (array $item) => ! empty($item['judul_artikel'] ?? $item['judul'] ?? $item['title'] ?? null))
            ->map(function (array $item) {
                try {
                    return $this->mapArticle($item, false);
                } catch (\Throwable $e) {
                    logger()->error('Failed to map article', [
                        'item_title' => $item['judul_artikel'] ?? $item['judul'] ?? $item['title'] ?? 'unknown',
                        'error' => $e->getMessage(),
                    ]);

                    return null;
                }
            })
            ->filter() // Hapus null values
            ->values();

        // JANGAN resolve images di sini - akan dilakukan setelah pagination untuk menghindari timeout

        logger()->info('Articles fetched and mapped successfully', [
            'endpoint' => $endpoint,
            'total_items' => count($articles),
            'mapped_items' => $mapped->count(),
        ]);

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

    private function mapArticle(array $item, bool $fetchDetail = false): ?array
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

            // Ambil image path (sama seperti BeritaSpbeController)
            $imagePath = $item['artikel_image_url']
                ?? $item['artikel_image']
                ?? $item['image_url']
                ?? $item['image']
                ?? $item['thumbnail']
                ?? $item['foto']
                ?? null;

            // Ambil portal ID (id_pd) - sama seperti BeritaSpbeController
            $portalId = $item['id_pd'] ?? null;
            if ($portalId) {
                $portalId = (int) $portalId;
            } elseif ($fetchDetail) {
                // Prioritas 2: Ambil dari detail artikel via slug (id_pd selalu ada di detail)
                // Hanya fetch jika $fetchDetail = true
                $portalId = $this->getIdPdFromSlug($slug);
            }

            // Resolve image URL menggunakan helper (sama seperti BeritaSpbeController)
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
        } catch (\Throwable $e) {
            logger()->error('Error in mapArticle', [
                'item' => $item,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
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

        // Jika tidak ada portal ID, return null (tidak ada nilai default)
        if (! $portalId) {
            return null;
        }

        // Bersihkan path dari leading slash
        $path = ltrim($path, '/');

        // Build full URL dengan portal ID
        $fullUrl = rtrim(self::BASE_URL, '/').'/'.$portalId.'//'.$path;

        logger()->debug('Resolved image URL', [
            'original_path' => $path,
            'portal_id' => $portalId,
            'full_url' => $fullUrl,
        ]);

        return $fullUrl;
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
                $response = $this->httpClient()->get(self::BASE_URL.'/api/detail-artikel/'.$slug);
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
     * Ambil gambar pertama dari artikel_image_urls via detail API
     */
    private function getFirstImageFromDetail(string $slug): ?string
    {
        try {
            $response = $this->httpClient()->get(self::BASE_URL.'/api/detail-artikel/'.$slug);
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

        // Ambil id_pd untuk prefix gambar
        $idPd = $payload['id_pd'] ?? null;
        $prefix = $idPd ? (int) $idPd : null;

        // Ambil gambar pertama dari artikel_image_urls
        $artikelImageUrls = $payload['artikel_image_urls'] ?? [];

        if (is_array($artikelImageUrls) && ! empty($artikelImageUrls)) {
            $firstImageItem = $artikelImageUrls[0];
            $imgPath = is_array($firstImageItem)
                ? ($firstImageItem['artikel_image_url'] ?? $firstImageItem['url'] ?? $firstImageItem['image_url'] ?? null)
                : $firstImageItem;

            if ($imgPath && is_string($imgPath)) {
                if (! Str::startsWith($imgPath, ['http://', 'https://'])) {
                    $path = trim(ltrim($imgPath, '/'));
                    if ($prefix) {
                        $fullUrl = rtrim(self::BASE_URL, '/').'/'.$prefix.'//'.$path;
                    } else {
                        $fullUrl = rtrim(self::BASE_URL, '/').'/'.$path;
                    }

                    return $fullUrl;
                }

                return $imgPath;
            }
        }

        // Fallback: coba ambil dari artikel_image_url jika ada
        $thumbnail = $payload['artikel_image_url'] ?? $payload['artikel_image'] ?? null;
        if ($thumbnail && $prefix) {
            if (! Str::startsWith($thumbnail, ['http://', 'https://'])) {
                $path = trim(ltrim($thumbnail, '/'));
                $fullUrl = rtrim(self::BASE_URL, '/').'/'.$prefix.'//'.$path;

                return $fullUrl;
            }

            return $thumbnail;
        }

        return null;
    }

    /**
     * Resolve images untuk artikel menggunakan logika yang sama dengan halaman berita:
     * - gunakan image_url jika sudah tersedia
     * - jika hanya ada path relatif, resolve menggunakan id_pd (ambil dari item/cache/detail API)
     * - jika masih kosong, ambil gambar pertama dari detail artikel
     */
    private function resolveArticleImages($articles)
    {
        if (! ($articles instanceof \Illuminate\Support\Collection)) {
            $articles = collect($articles);
        }

        return $articles->map(function ($item) {
            if (! empty($item['image_url'])) {
                return $item;
            }

            $imagePath = $item['_image_path'] ?? null;
            $slug = $item['slug'] ?? null;
            $idPd = $item['_id_pd'] ?? null;

            if (! $idPd && $slug) {
                $cacheKey = 'id_pd_'.md5($slug);
                $cachedIdPd = Cache::get($cacheKey);

                if ($cachedIdPd) {
                    $idPd = $cachedIdPd;
                } else {
                    try {
                        $idPd = $this->getIdPdFromSlug($slug);
                    } catch (\Throwable $e) {
                        logger()->warning('Failed to fetch id_pd in resolveArticleImages (search)', [
                            'slug' => $slug,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }

                if ($idPd) {
                    $item['_id_pd'] = $idPd;
                }
            }

            if ($imagePath && ! Str::startsWith($imagePath, ['http://', 'https://'])) {
                if ($idPd) {
                    $fullImageUrl = $this->resolveImageUrl($imagePath, (int) $idPd);
                    if ($fullImageUrl) {
                        $item['image_url'] = $fullImageUrl;
                    }
                }
            } elseif ($imagePath && Str::startsWith($imagePath, ['http://', 'https://'])) {
                $item['image_url'] = $imagePath;
            }

            if (empty($item['image_url']) && $slug) {
                $detailImage = $this->getFirstImageFromDetail($slug);
                if ($detailImage) {
                    $item['image_url'] = $detailImage;
                }
            }

            return $item;
        });
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

    /**
     * API endpoint untuk pencarian dinamis (AJAX)
     * Mengembalikan hasil pencarian dalam format JSON
     */
    public function api()
    {
        $search = request()->query('q') ?? request()->query('search', '');
        $limit = min((int) request()->query('limit', 10), 50); // Max 50 hasil

        logger()->info('Search API called', [
            'query' => $search,
            'limit' => $limit,
        ]);

        if (empty(trim($search))) {
            return response()->json([
                'success' => true,
                'query' => '',
                'total' => 0,
                'results' => [],
            ]);
        }

        $searchLower = mb_strtolower(trim($search));

        try {
            // Ambil berita
            $beritaResults = $this->searchBerita($searchLower);
            logger()->info('Berita results', ['count' => $beritaResults->count()]);

            // Ambil informasi (termasuk progres)
            $informasiResults = $this->searchInformasi($searchLower);
            logger()->info('Informasi results', ['count' => $informasiResults->count()]);

            // Gabungkan semua hasil
            $allResults = $beritaResults->merge($informasiResults)->values();

            logger()->info('Combined results', [
                'berita_count' => $beritaResults->count(),
                'informasi_count' => $informasiResults->count(),
                'total_count' => $allResults->count(),
            ]);

            // Sort by date (newest first)
            $allResults = $allResults->sortByDesc(function ($item) {
                return $item['date'] ? $item['date']->timestamp : 0;
            })->values();

            // Resolve images hanya untuk jumlah hasil yang akan dikirim ke klien
            $resolvedLimitedResults = $this->resolveArticleImages($allResults->take($limit));

            // Limit hasil dan siapkan payload
            $limitedResults = $resolvedLimitedResults->map(function ($item) {
                return [
                    'id' => $item['id'] ?? null,
                    'title' => $item['title'] ?? '',
                    'slug' => $item['slug'] ?? '',
                    'image_url' => $item['image_url'] ?? null,
                    'category' => $item['category'] ?? '',
                    'type' => $item['type'] ?? 'unknown',
                    'route' => $item['route'] ?? '#',
                    'date' => $item['date'] ? $item['date']->format('Y-m-d H:i:s') : null,
                    'date_formatted' => $item['date'] ? $item['date']->translatedFormat('d F Y') : null,
                ];
            })->values();

            logger()->info('Final API response', [
                'query' => $search,
                'total' => $allResults->count(),
                'limited' => $limitedResults->count(),
                'result_types' => $limitedResults->pluck('type')->countBy()->toArray(),
            ]);

            return response()->json([
                'success' => true,
                'query' => $search,
                'total' => $allResults->count(),
                'results' => $limitedResults,
                'debug' => [
                    'berita_count' => $beritaResults->count(),
                    'informasi_count' => $informasiResults->count(),
                ],
            ]);
        } catch (\Throwable $e) {
            logger()->error('Error in search API', [
                'query' => $search,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'query' => $search,
                'total' => 0,
                'results' => [],
                'error' => 'Terjadi kesalahan saat mencari data',
            ], 500);
        }
    }
}
