<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Gate untuk Superadmin saja
        Gate::define('superadmin-only', function (User $user) {
            return $user->role === 'superadmin';
        });

        // Gate untuk Superadmin DAN Admin Produk
        Gate::define('manage-produk', function (User $user) {
            return in_array($user->role, ['superadmin', 'adminproduk']);
        });
    }
}
