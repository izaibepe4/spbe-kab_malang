<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RefreshCsrfToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Regenerate CSRF token setiap 2 jam untuk mencegah token expired
        // tapi tetap menjaga session tetap aktif
        $lastRegeneration = $request->session()->get('csrf_last_regeneration', 0);
        $now = time();

        // Regenerate setiap 2 jam (7200 detik)
        if ($now - $lastRegeneration > 7200) {
            $request->session()->regenerateToken();
            $request->session()->put('csrf_last_regeneration', $now);
        }

        return $next($request);
    }
}
