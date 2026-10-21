<?php

namespace App\Services;

class ImageOptimizationService
{
    /**
     * Generate lazy loading HTML attributes
     */
    public static function lazyLoadAttributes(): array
    {
        return [
            'loading' => 'lazy',
            'decoding' => 'async',
        ];
    }

    /**
     * Generate responsive image srcset
     */
    public static function generateSrcset(string $baseUrl, array $sizes = [320, 640, 1024, 1920]): string
    {
        $srcset = [];

        foreach ($sizes as $size) {
            $srcset[] = "{$baseUrl}?w={$size} {$size}w";
        }

        return implode(', ', $srcset);
    }

    /**
     * Get optimized image URL dengan query parameters
     */
    public static function optimize(string $url, ?int $width = null, int $quality = 85): string
    {
        if (empty($url)) {
            return '';
        }

        // Jika sudah full URL, tambahkan query params
        if (str_starts_with($url, 'http')) {
            $params = [];

            if ($width) {
                $params[] = "w={$width}";
            }

            $params[] = "q={$quality}";

            $separator = str_contains($url, '?') ? '&' : '?';

            return $url.$separator.implode('&', $params);
        }

        return $url;
    }

    /**
     * Get placeholder image
     * Priority: custom asset > default asset > SVG fallback
     */
    public static function placeholder(string $type = 'default', int $width = 400, int $height = 300): string
    {
        // Mapping placeholder berdasarkan type
        $placeholders = [
            'berita' => 'assets/img/bg_2.jpg',
            'informasi' => 'assets/img/bg_2.jpg',
            'regulasi' => 'assets/img/bg_2.jpg',
            'pengetahuan' => 'assets/img/bg_2.jpg',
            'default' => 'assets/img/bg_2.jpg',
        ];

        $assetPath = $placeholders[$type] ?? $placeholders['default'];

        // Check jika file exists di public folder
        if (file_exists(public_path($assetPath))) {
            return asset($assetPath);
        }

        // Fallback ke SVG placeholder jika file tidak ada
        return self::svgPlaceholder($width, $height);
    }

    /**
     * Generate SVG placeholder (fallback)
     */
    public static function svgPlaceholder(int $width = 400, int $height = 300, string $color = '#f0f0f0'): string
    {
        return "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 {$width} {$height}'%3E%3Crect fill='{$color}' width='{$width}' height='{$height}'/%3E%3Ctext x='50%25' y='50%25' text-anchor='middle' fill='%23999' font-family='Arial' font-size='16'%3ENo Image%3C/text%3E%3C/svg%3E";
    }

    /**
     * Get fallback image URL dengan error handling
     */
    public static function withFallback(?string $imageUrl, string $type = 'default'): string
    {
        // Jika image URL ada dan valid, return
        if (! empty($imageUrl) && filter_var($imageUrl, FILTER_VALIDATE_URL)) {
            return $imageUrl;
        }

        // Jika tidak ada, return placeholder
        return self::placeholder($type);
    }
}
