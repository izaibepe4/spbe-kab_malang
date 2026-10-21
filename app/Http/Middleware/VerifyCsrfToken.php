<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        // GET logout tidak perlu CSRF token (untuk handle direct access)
        // POST logout tetap memerlukan CSRF token untuk keamanan
    ];

    /**
     * Determine if the session and input CSRF tokens match.
     * Override untuk allow GET logout tanpa CSRF token.
     */
    public function handle($request, \Closure $next)
    {
        // Allow GET logout tanpa CSRF token
        if ($request->isMethod('GET') && $request->is('logout')) {
            return $next($request);
        }

        return parent::handle($request, $next);
    }
}
