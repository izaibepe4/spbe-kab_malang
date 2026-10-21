<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CacheImages
{
    /**
     * Handle an incoming request.
     * Set cache headers for image files to improve performance
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Check if response is for an image file
        $path = $request->path();
        $isImage = preg_match('/\.(jpg|jpeg|png|gif|webp|svg|ico)(\?.*)?$/i', $path);

        // Also check storage paths
        if (str_contains($path, 'storage/') || str_contains($path, 'assets/')) {
            $isImage = $isImage || preg_match('/\.(jpg|jpeg|png|gif|webp|svg|ico)(\?.*)?$/i', $path);
        }

        if ($isImage && $response->getStatusCode() === 200) {
            // Set cache headers for images
            // Cache for 1 year (31536000 seconds)
            $response->headers->set('Cache-Control', 'public, max-age=31536000, immutable');
            $response->headers->set('Expires', gmdate('D, d M Y H:i:s', time() + 31536000).' GMT');

            // Set ETag for better cache validation
            if (! $response->headers->has('ETag')) {
                $etag = md5($response->getContent());
                $response->headers->set('ETag', '"'.$etag.'"');
            }
        }

        return $response;
    }
}
