<?php

namespace App\Http\Controllers;

use App\Models\IndikatorSpbe;
use App\Models\ProgresSpbe;
use App\Models\SectionInfoSpbe;
use App\Models\TujuanSpbe;
use Carbon\Carbon;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    private const NEWS_BASE_URL = 'https://web-admin.malangkab.go.id';

    private const NEWS_LIST_ENDPOINT = self::NEWS_BASE_URL.'/api/list-artikel/5';

    private const INFORMASI_SPBE_ENDPOINT = self::NEWS_BASE_URL.'/api/list-artikel/3/1445';

    private const DETAIL_ENDPOINT = self::NEWS_BASE_URL.'/api/detail-artikel/';

    public function index()
    {
        // Ambil data hero slides dengan fallback dummy
        $heroSlideService = app(\App\Services\HeroSlideService::class);
        $heroSlides = $heroSlideService->getActiveSlides();

        // Ambil data definisi dan tujuan SPBE
        $section = SectionInfoSpbe::query()
            ->first(['id', 'judul', 'deskripsi_html']);

        $tujuan = TujuanSpbe::query()
            ->get(['id', 'ikon', 'judul', 'deskripsi']);

        $beritaKabupaten = $this->fetchLatestNews();
        $informasiSpbe = $this->fetchInformasiSpbe();
        $progresSpbe = $this->fetchProgresSpbe();
        $chartData = $this->prepareChartData();

        // Ambil data indikator SPBE dari database
        $indikatorSpbe = IndikatorSpbe::query()
            ->orderBy('kategori')
            ->get(['kategori', 'nilai']);

        // Fallback data jika database kosong
        if ($indikatorSpbe->isEmpty()) {
            $indikatorSpbe = collect([
                (object) ['kategori' => 'Kebijakan SPBE', 'nilai' => 10],
                (object) ['kategori' => 'Tata Kelola SPBE', 'nilai' => 10],
                (object) ['kategori' => 'Manajemen SPBE', 'nilai' => 11],
                (object) ['kategori' => 'Layanan SPBE', 'nilai' => 16],
            ]);
        }

        return view('public.home', [
            'heroSlides' => $heroSlides,
            'section' => $section,
            'tujuan' => $tujuan,
            'beritaKabupaten' => $beritaKabupaten,
            'informasiSpbe' => $informasiSpbe,
            'progresSpbe' => $progresSpbe,
            'chartData' => $chartData,
            'indikatorSpbe' => $indikatorSpbe,
        ]);
    }

    private function fetchLatestNews(): array
    {
        // Cek cache dulu, jika kosong atau tidak valid, fetch ulang
        // Gunakan cache key yang berbeda untuk memastikan fresh data
        $cached = Cache::get('berita_kabupaten_home_v3');
        if (is_array($cached) && ! empty($cached)) {
            // Validasi bahwa semua artikel punya image_url atau setidaknya sudah di-resolve
            $hasImages = collect($cached)->every(fn ($item) => ! empty($item['image_url']) || ! empty($item['_image_path']));
            if ($hasImages) {
                return $cached;
            }
            // Jika cache ada tapi gambar belum di-resolve, fetch ulang
            logger()->info('Cache berita_kabupaten_home_v3 found but images not resolved, fetching fresh');
        }

        // Fetch fresh data
        try {
            $response = $this->httpClient()->get(self::NEWS_LIST_ENDPOINT);
        } catch (\Throwable $exception) {
            report($exception);

            return [];
        }

        if (! $response->successful()) {
            return [];
        }

        $articles = $this->extractArticles($response->json());

        if (empty($articles)) {
            logger()->warning('No articles extracted from API response');

            return [];
        }

        // Filter, sort, dan ambil 4 artikel terbaru terlebih dahulu
        $topArticles = collect($articles)
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
            ->take(4)
            ->values()
            ->all();

        if (empty($topArticles)) {
            logger()->warning('No top articles after filtering');

            return [];
        }

        // Fetch id_pd dari detail API untuk 4 artikel terbaru (dengan error handling)
        foreach ($topArticles as &$articleData) {
            $item = $articleData['item'];
            $slug = $articleData['slug'];

            // Jika id_pd tidak ada di item, coba fetch dari detail API
            // Tapi jangan block jika gagal, artikel tetap harus muncul
            if (empty($item['id_pd'])) {
                try {
                    $idPd = $this->getIdPdFromSlug($slug);
                    if ($idPd) {
                        $item['id_pd'] = $idPd;
                        logger()->debug('Fetched id_pd for berita article', [
                            'slug' => $slug,
                            'id_pd' => $idPd,
                        ]);
                    } else {
                        logger()->debug('No id_pd found for berita article', [
                            'slug' => $slug,
                        ]);
                    }
                } catch (\Throwable $e) {
                    // Log error tapi jangan block, artikel tetap muncul tanpa gambar
                    logger()->warning('Failed to fetch id_pd for article', [
                        'slug' => $slug,
                        'error' => $e->getMessage(),
                    ]);
                }
            } else {
                logger()->debug('id_pd already exists in berita item', [
                    'slug' => $slug,
                    'id_pd' => $item['id_pd'],
                ]);
            }
            $articleData['item'] = $item;
        }
        unset($articleData);

        // Map artikel dengan id_pd yang sudah didapat
        // Pastikan artikel tetap muncul meskipun image_url null
        $mapped = collect($topArticles)
            ->map(function (array $articleData) {
                try {
                    return $this->mapArticle($articleData['item']);
                } catch (\Throwable $e) {
                    logger()->error('Failed to map article', [
                        'slug' => $articleData['slug'] ?? 'unknown',
                        'error' => $e->getMessage(),
                    ]);

                    return null;
                }
            })
            ->filter() // Hapus null
            ->values()
            ->all();

        logger()->info('Mapped articles count', ['count' => count($mapped)]);

        // Log untuk debugging
        foreach ($mapped as $item) {
            logger()->debug('Berita article mapped', [
                'title' => $item['title'] ?? 'N/A',
                'slug' => $item['slug'] ?? 'N/A',
                'has_image_url' => ! empty($item['image_url']),
                'image_url' => $item['image_url'] ?? 'null',
            ]);
        }

        // Hanya simpan ke cache jika ada data
        if (! empty($mapped)) {
            Cache::put('berita_kabupaten_home_v3', $mapped, now()->addMinutes(5));
        }

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

    private function mapArticle(array $item): ?array
    {
        try {
            // Ambil kandidat path gambar
            $imagePath = $item['artikel_image_url']
                ?? $item['artikel_image']
                ?? $item['image_url']
                ?? $item['image']
                ?? $item['thumbnail']
                ?? $item['foto']
                ?? null;

            $title = $item['judul_artikel'] ?? $item['judul'] ?? $item['title'] ?? '-';

            if (empty($title) || $title === '-') {
                // Skip artikel tanpa judul
                return null;
            }

            $slug = ! empty($item['slug'])
                ? $item['slug']
                : Str::slug($title ?: Str::random());

            if (empty($slug)) {
                // Skip artikel tanpa slug
                return null;
            }

            // Ambil portal ID (id_pd)
            // Prioritas 1: Cek dari item (jika list API sudah include id_pd atau sudah di-fetch sebelumnya)
            $portalId = $item['id_pd'] ?? null;
            if ($portalId) {
                $portalId = (int) $portalId;
            }
            // Jika id_pd tidak ada, akan return null di resolveImageUrl (tidak ada nilai default)

            // Resolve image URL menggunakan helper
            $fullImageUrl = null;
            if ($imagePath) {
                try {
                    $fullImageUrl = $this->resolveImageUrl($imagePath, $portalId);
                    // Log untuk debugging
                    logger()->debug('Resolved image URL', [
                        'imagePath' => $imagePath,
                        'portalId' => $portalId,
                        'fullImageUrl' => $fullImageUrl,
                        'title' => $title ?? 'N/A',
                    ]);
                } catch (\Throwable $e) {
                    logger()->warning('Failed to resolve image URL', [
                        'imagePath' => $imagePath,
                        'portalId' => $portalId,
                        'error' => $e->getMessage(),
                    ]);
                    $fullImageUrl = null;
                }
            } else {
                logger()->debug('No imagePath found', [
                    'title' => $title ?? 'N/A',
                    'item_keys' => array_keys($item),
                ]);
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
                'category' => $item['nama_kategori_artikel'] ?? null,
                'date' => $date,
            ];
        } catch (\Throwable $e) {
            logger()->error('Error in mapArticle', [
                'item' => $item,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return null;
        }
    }

    private function fetchInformasiSpbe(): array
    {
        // Cek cache dulu, jika kosong atau tidak valid, fetch ulang
        // Gunakan cache key yang berbeda untuk memastikan fresh data
        $cached = Cache::get('informasi_spbe_home_v3');
        if (is_array($cached) && ! empty($cached)) {
            // Validasi bahwa semua artikel punya image_url atau setidaknya sudah di-resolve
            $hasImages = collect($cached)->every(fn ($item) => ! empty($item['image_url']) || ! empty($item['_image_path']));
            if ($hasImages) {
                return $cached;
            }
            // Jika cache ada tapi gambar belum di-resolve, fetch ulang
            logger()->info('Cache informasi_spbe_home_v3 found but images not resolved, fetching fresh');
        }

        // Fetch fresh data
        try {
            $response = $this->httpClient()->get(self::INFORMASI_SPBE_ENDPOINT);
        } catch (\Throwable $exception) {
            report($exception);

            return [];
        }

        if (! $response->successful()) {
            return [];
        }

        $articles = $this->extractArticles($response->json());

        if (empty($articles)) {
            logger()->warning('No articles extracted from informasi SPBE API response');

            return [];
        }

        // Filter, sort, dan ambil 4 artikel terbaru terlebih dahulu
        $topArticles = collect($articles)
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
            ->take(4)
            ->values()
            ->all();

        if (empty($topArticles)) {
            logger()->warning('No top articles after filtering (informasi SPBE)');

            return [];
        }

        // Fetch id_pd dari detail API untuk 4 artikel terbaru (dengan error handling)
        foreach ($topArticles as &$articleData) {
            $item = $articleData['item'];
            $slug = $articleData['slug'];

            // Jika id_pd tidak ada di item, coba fetch dari detail API
            // Tapi jangan block jika gagal, artikel tetap harus muncul
            if (empty($item['id_pd'])) {
                try {
                    $idPd = $this->getIdPdFromSlug($slug);
                    if ($idPd) {
                        $item['id_pd'] = $idPd;
                        logger()->debug('Fetched id_pd for informasi article', [
                            'slug' => $slug,
                            'id_pd' => $idPd,
                        ]);
                    } else {
                        logger()->debug('No id_pd found for informasi article', [
                            'slug' => $slug,
                        ]);
                    }
                } catch (\Throwable $e) {
                    // Log error tapi jangan block, artikel tetap muncul tanpa gambar
                    logger()->warning('Failed to fetch id_pd for informasi article', [
                        'slug' => $slug,
                        'error' => $e->getMessage(),
                    ]);
                }
            } else {
                logger()->debug('id_pd already exists in item', [
                    'slug' => $slug,
                    'id_pd' => $item['id_pd'],
                ]);
            }
            $articleData['item'] = $item;
        }
        unset($articleData);

        // Map artikel dengan id_pd yang sudah didapat
        // Pastikan artikel tetap muncul meskipun image_url null
        // Dan pastikan urutan tetap berdasarkan created_at dari API
        $mapped = collect($topArticles)
            ->map(function (array $articleData) {
                try {
                    $mappedArticle = $this->mapArticle($articleData['item']);
                    if (! $mappedArticle) {
                        return null;
                    }

                    // Pastikan timestamp dari articleData dipertahankan untuk sorting
                    // karena mapArticle mungkin mengubah date atau tidak mempertahankan urutan
                    if (isset($articleData['timestamp'])) {
                        $mappedArticle['_sort_timestamp'] = $articleData['timestamp'];
                    } elseif (isset($mappedArticle['date']) && $mappedArticle['date'] instanceof \Carbon\Carbon) {
                        // Fallback: gunakan date dari mappedArticle jika timestamp tidak ada
                        $mappedArticle['_sort_timestamp'] = $mappedArticle['date']->timestamp;
                    } else {
                        // Jika tidak ada date, beri timestamp 0 agar diurutkan paling akhir
                        $mappedArticle['_sort_timestamp'] = 0;
                    }

                    return $mappedArticle;
                } catch (\Throwable $e) {
                    logger()->error('Failed to map informasi article', [
                        'slug' => $articleData['slug'] ?? 'unknown',
                        'error' => $e->getMessage(),
                    ]);

                    return null;
                }
            })
            ->filter() // Hapus null
            ->sortByDesc('_sort_timestamp') // Sort ulang berdasarkan timestamp dari created_at
            ->map(function ($item) {
                // Hapus _sort_timestamp setelah sorting
                if (isset($item['_sort_timestamp'])) {
                    unset($item['_sort_timestamp']);
                }

                return $item;
            })
            ->values()
            ->all();

        logger()->info('Mapped informasi articles count', ['count' => count($mapped)]);

        // Log untuk debugging
        foreach ($mapped as $item) {
            logger()->debug('Informasi article mapped', [
                'title' => $item['title'] ?? 'N/A',
                'slug' => $item['slug'] ?? 'N/A',
                'has_image_url' => ! empty($item['image_url']),
                'image_url' => $item['image_url'] ?? 'null',
            ]);
        }

        // Hanya simpan ke cache jika ada data
        if (! empty($mapped)) {
            Cache::put('informasi_spbe_home_v3', $mapped, now()->addMinutes(5));
        }

        return $mapped;
    }

    /**
     * Fetch data Progres SPBE dari database
     */
    private function fetchProgresSpbe()
    {
        return ProgresSpbe::query()
            ->orderBy('tahun')
            ->orderByDesc('created_at')
            ->get(['id', 'judul', 'gambar', 'file_pdf_path', 'nilai', 'tahun', 'created_at']);
    }

    /**
     * Siapkan data chart untuk Indeks SPBE
     */
    private function prepareChartData()
    {
        $progresData = $this->fetchProgresSpbe();

        $chartData = $progresData
            ->sortBy(function ($item) {
                return $item->tahun ?? optional($item->created_at)->format('Y');
            })
            ->values()
            ->map(function ($progres) {
                $year = $progres->tahun ?? optional($progres->created_at)->format('Y');

                return [
                    'year' => $year ? (string) $year : 'Tanpa Tahun',
                    'value' => $progres && $progres->nilai ? (float) $progres->nilai : null,
                    'judul' => $progres ? $progres->judul : null,
                ];
            })
            ->filter(fn ($item) => $item['year'] !== null)
            ->values();

        if ($chartData->isEmpty()) {
            return collect([
                ['year' => '2021', 'value' => null, 'judul' => null],
                ['year' => '2022', 'value' => null, 'judul' => null],
                ['year' => '2023', 'value' => null, 'judul' => null],
                ['year' => '2024', 'value' => null, 'judul' => null],
                ['year' => '2025', 'value' => null, 'judul' => null],
            ]);
        }

        return $chartData;
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
                // Log error tapi jangan throw, return null saja
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
     * Resolve image URL dengan portal ID
     * Jika path sudah lengkap (http/https) → langsung return
     * Jika relatif → tambahkan base URL dengan portal ID
     */
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
        return rtrim(self::NEWS_BASE_URL, '/').'/'.$portalId.'//'.$path;
    }

    /**
     * Fetch detail artikel dari API
     */
    private function fetchDetailArticle(string $slug): ?array
    {
        // Gunakan hash untuk cache key agar tidak terlalu panjang
        $cacheKey = 'detail_article_'.md5($slug);

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

            return $payload;
        });
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
