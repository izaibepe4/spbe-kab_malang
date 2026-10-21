<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyApiToken
{
    /**
     * Handle an incoming request.
     *
     * Verify API token untuk AJAX requests
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip verification untuk GET requests (read-only)
        if ($request->isMethod('GET')) {
            return $next($request);
        }

        // Verify CSRF token untuk POST/PUT/DELETE
        if (! $request->hasValidSignature() && ! $request->session()->token()) {
            return response()->json([
                'message' => 'Invalid or missing CSRF token.',
            ], 419);
        }

        return $next($request);
    }
}
