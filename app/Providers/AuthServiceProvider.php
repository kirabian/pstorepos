<?php

namespace App\Providers;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

    // Daftarkan logika pengecekan Superadmin di sini
    Gate::define('superadmin-only', function ($user) {
        return $user->role === 'superadmin';
    });

    // Gate untuk Superadmin DAN Admin Produk
    Gate::define('manage-produk', function (User $user) {
        return in_array($user->role, ['superadmin', 'adminproduk']);
    });
    }
}
