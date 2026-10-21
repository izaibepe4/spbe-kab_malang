<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateWebOrAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah ada user yang login di guard 'web' (OPD)
        if (Auth::guard('web')->check()) {
            return $next($request);
        }

        // Cek apakah ada user yang login di guard 'admin' (Filament)
        if (Auth::guard('admin')->check()) {
            return $next($request);
        }

        // Coba cek session Filament secara manual
        $sessionKey = 'login_admin_'.sha1(static::class);
        if ($request->session()->has($sessionKey)) {
            return $next($request);
        }

        // Jika tidak ada user yang login, redirect ke login OPD
        return redirect()->route('login');
    }
}
