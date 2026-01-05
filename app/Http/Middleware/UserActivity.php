<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class UserActivity
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Simpan waktu terakhir dilihat ke database SETIAP ada aktivitas request asli
            if (!$request->hasHeader('X-Livewire')) {
                $user->updateQuietly(['last_seen' => now()]);
            }

            // Perbarui Cache Online (Lampu Hijau)
            // expire dalam 11 detik
            Cache::put('user-is-online-' . $user->id, true, now()->addSeconds(11));
        }

        return $next($request);
    }
}