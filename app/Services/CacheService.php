<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CacheService
{
    /**
     * Remember dengan smart cache duration
     */
    public static function remember(string $key, string $type, callable $callback, ?int $customMinutes = null)
    {
        $minutes = $customMinutes ?? self::getDuration($type);

        return Cache::remember($key, now()->addMinutes($minutes), $callback);
    }

    /**
     * Get cache duration berdasarkan type
     */
    private static function getDuration(string $type): int
    {
        // Parse type: "berita.list", "informasi.detail", etc
        $parts = explode('.', $type);
        $category = $parts[0] ?? 'default';
        $subtype = $parts[1] ?? 'list';

        $config = config("cache-strategy.{$category}.{$subtype}");

        return $config ?? 10; // Default 10 menit
    }

    /**
     * Forget cache dengan pattern
     */
    public static function forgetPattern(string $pattern): void
    {
        // Untuk Laravel cache yang support tagging
        if (method_exists(Cache::store(), 'tags')) {
            Cache::tags($pattern)->flush();
        } else {
            // Fallback: forget individual keys
            Cache::forget($pattern);
        }
    }

    /**
     * Get cache key dengan prefix
     */
    public static function key(string $prefix, ...$parts): string
    {
        return $prefix.':'.implode(':', array_map('md5', $parts));
    }
}
