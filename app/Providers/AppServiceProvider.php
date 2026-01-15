<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage; // Tambahan wajib
use App\Models\User;
use Illuminate\Pagination\Paginator;

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
        // Gunakan Bootstrap untuk Pagination (Opsional, tapi biasanya perlu)
        Paginator::useBootstrap();

        // --- GATE AUTHORIZATION ---
        
        // Gate untuk Superadmin saja
        Gate::define('superadmin-only', function (User $user) {
            return $user->role === 'superadmin';
        });

        // Gate untuk Superadmin DAN Admin Produk
        Gate::define('manage-produk', function (User $user) {
            return in_array($user->role, ['superadmin', 'adminproduk']);
        });

        // --- KONFIGURASI DRIVER GOOGLE DRIVE (SOLUSI ERROR) ---
        try {
            Storage::extend('google', function($app, $config) {
                $options = [];

                if (!empty($config['teamDriveId'] ?? null)) {
                    $options['teamDriveId'] = $config['teamDriveId'];
                }

                $client = new \Google\Client();
                $client->setClientId($config['clientId']);
                $client->setClientSecret($config['clientSecret']);
                $client->refreshToken($config['refreshToken']);
                
                $service = new \Google\Service\Drive($client);
                
                // Gunakan Adapter dari library masbug/flysystem-google-drive-ext
                $adapter = new \Masbug\Flysystem\GoogleDriveAdapter($service, $config['folder'] ?? '/', $options);
                $driver  = new \League\Flysystem\Filesystem($adapter);

                return new \Illuminate\Filesystem\FilesystemAdapter($driver, $adapter);
            });
        } catch(\Exception $e) {
            // Tangkap error diam-diam jika library belum siap, agar tidak crash total
        }
    }
}