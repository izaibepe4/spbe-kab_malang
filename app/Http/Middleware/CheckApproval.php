<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckApproval
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek guard 'web' untuk OPD (User model)
        $user = Auth::guard('web')->user();

        // Jika tidak ada user yang login di guard 'web', cek guard 'admin'
        if (! $user) {
            $admin = Auth::guard('admin')->user();

            // Admin (dari table admins) tidak perlu approval
            if ($admin instanceof \App\Models\Admin) {
                return $next($request);
            }

            // Jika tidak ada user yang login sama sekali, redirect ke login
            return redirect()->route('login');
        }

        // OPD (dari table users) harus di-approve dulu
        if (! $user->is_approved) {
            Auth::guard('web')->logout();

            return redirect()->route('login')
                ->withErrors(['email' => 'Akun Anda belum disetujui oleh admin. Silakan tunggu persetujuan.']);
        }

        return $next($request);
    }
}
