<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserManagementAccess
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Logika: Hanya izinkan Superadmin dan Audit
        if (Auth::check() && in_array(Auth::user()->role, ['superadmin', 'audit'])) {
            return $next($request);
        }

        // Jika bukan, tolak akses
        abort(403, 'Akses Ditolak: Halaman ini hanya untuk Superadmin & Audit.');
    }
}