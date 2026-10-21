<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class ArticleService
{
    private ExternalApiService $apiService;

    private CacheService $cacheService;

    public function __construct()
    {
        $this->apiService = new ExternalApiService('article_api');
        $this->cacheService = new CacheService;
    }

    /**
     * Fetch articles dari API dengan caching
     */
    public function fetchArticles(string $endpoint, string $cacheKey, int $cacheMinutes = 10): Collection
    {
        return Cache::remember($cacheKey, now()->addMinutes($cacheMinutes), function () use ($endpoint) {
            try {
                $data = $this->apiService->fetchWithCircuitBreaker(
                    $endpoint,
                    fallback: fn () => []
                );

                return $this->extractAndMapArticles($data);
            } catch (\Throwable $e) {
                logger()->error('Failed to fetch articles', [
                    'endpoint' => $endpoint,
                    'error' => $e->getMessage(),
                ]);

                return collect([]);
            }
        });
    }

    /**
     * Extract articles dari API response
     */
    private function extractAndMapArticles(mixed $payload): Collection
    {
        if (! is_array($payload)) {
            return collect([]);
        }

        // Extract articles dari berbagai format response
        $articles = $payload['data']['data'] ?? $payload['data'] ?? $payload['articles'] ?? $payload;

        if (! is_array($articles)) {
            return collect([]);
        }

        return collect($articles)
            ->filter(fn ($item) => ! empty($item['judul_artikel'] ?? $item['judul'] ?? $item['title'] ?? null))
            ->map(fn ($item) => $this->mapArticle($item))
            ->filter()
            ->values();
    }

    /**
     * Map article data ke format konsisten
     */
    private function mapArticle(array $item): ?array
    {
        try {
            $title = $item['judul_artikel'] ?? $item['judul'] ?? $item['title'] ?? '';
            $slug = $item['slug_artikel'] ?? $item['slug'] ?? '';

            if (empty($title) || empty($slug)) {
                return null;
            }

            // Parse date
            $dateString = $item['created_at'] ?? $item['tanggal'] ?? $item['date'] ?? null;
            $date = $dateString ? Carbon::parse($dateString) : null;

            // Get image
            $imagePath = $item['artikel_image_url']
                ?? $item['artikel_image']
                ?? $item['image_url']
                ?? $item['image']
                ?? null;

            $portalId = isset($item['id_pd']) ? (int) $item['id_pd'] : null;

            $imageUrl = null;
            if ($imagePath) {
                if (str_starts_with($imagePath, 'http')) {
                    $imageUrl = $imagePath;
                } else {
                    $imageUrl = $this->apiService->resolveImageUrl($imagePath, $portalId);
                }
            }

            // Fallback to placeholder if no image
            $imageUrl = ImageOptimizationService::withFallback($imageUrl, 'berita');

            return [
                'title' => $title,
                'slug' => $slug,
                'image_url' => $imageUrl,
                'category' => $item['nama_kategori_artikel'] ?? $item['category'] ?? null,
                'date' => $date,
                'excerpt' => $item['excerpt'] ?? null,
            ];
        } catch (\Throwable $e) {
            logger()->warning('Failed to map article', [
                'item' => $item,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Fetch article detail
     */
    public function fetchArticleDetail(string $endpoint, string $slug, int $cacheMinutes = 60): ?array
    {
        $cacheKey = "article_detail_{$slug}";

        return Cache::remember($cacheKey, now()->addMinutes($cacheMinutes), function () use ($endpoint, $slug) {
            try {
                $data = $this->apiService->fetchWithCircuitBreaker(
                    $endpoint.$slug,
                    fallback: fn () => null
                );

                if (! $data) {
                    return null;
                }

                return $this->mapArticleDetail($data);
            } catch (\Throwable $e) {
                logger()->error('Failed to fetch article detail', [
                    'slug' => $slug,
                    'error' => $e->getMessage(),
                ]);

                return null;
            }
        });
    }

    /**
     * Map article detail
     */
    private function mapArticleDetail(array $payload): ?array
    {
        $data = $payload['data'] ?? $payload;

        if (empty($data)) {
            return null;
        }

        $title = $data['judul_artikel'] ?? $data['judul'] ?? '';
        $content = $data['isi_artikel'] ?? $data['content'] ?? '';

        if (empty($title)) {
            return null;
        }

        // Parse date
        $dateString = $data['created_at'] ?? $data['tanggal'] ?? null;
        $date = $dateString ? Carbon::parse($dateString) : null;

        // Get images
        $images = $this->extractImages($data);

        return [
            'title' => $title,
            'content' => $content,
            'date' => $date,
            'thumbnail' => $images['thumbnail'] ?? ImageOptimizationService::placeholder('berita'),
            'other_images' => $images['others'] ?? [],
            'category' => $data['nama_kategori_artikel'] ?? null,
        ];
    }

    /**
     * Extract images dari article data
     */
    private function extractImages(array $data): array
    {
        $artikelImageUrls = $data['artikel_image_urls'] ?? [];
        $allImages = [];

        if (is_array($artikelImageUrls) && ! empty($artikelImageUrls)) {
            foreach ($artikelImageUrls as $imageItem) {
                $imgPath = is_array($imageItem)
                    ? ($imageItem['artikel_image_url'] ?? $imageItem['url'] ?? null)
                    : $imageItem;

                if ($imgPath) {
                    $portalId = isset($data['id_pd']) ? (int) $data['id_pd'] : null;

                    if (str_starts_with($imgPath, 'http')) {
                        $allImages[] = $imgPath;
                    } else {
                        $fullUrl = $this->apiService->resolveImageUrl($imgPath, $portalId);
                        if ($fullUrl) {
                            $allImages[] = $fullUrl;
                        }
                    }
                }
            }
        }

        return [
            'thumbnail' => $allImages[0] ?? null,
            'others' => array_slice($allImages, 1),
        ];
    }
}
