<?php

if (! function_exists('cdn')) {
    /**
     * Generate CDN URL for assets
     */
    function cdn(string $path): string
    {
        $cdnUrl = env('CDN_URL');

        if ($cdnUrl) {
            return rtrim($cdnUrl, '/').'/'.ltrim($path, '/');
        }

        return asset($path);
    }
}
