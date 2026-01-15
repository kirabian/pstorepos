<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useBootstrap();

        Gate::define('superadmin-only', function (User $user) {
            return $user->role === 'superadmin';
        });

        Gate::define('manage-produk', function (User $user) {
            return in_array($user->role, ['superadmin', 'adminproduk']);
        });

        // --- KONFIGURASI GOOGLE DRIVE (FIXED ROOT FOLDER) ---
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
                
                // PERBAIKAN DI SINI:
                // Kita pastikan 'folder' ID dari .env dipakai sebagai root.
                // Jika kosong, baru pakai root '/'
                $rootFolderId = $config['folder'] ?? '/';

                $adapter = new \Masbug\Flysystem\GoogleDriveAdapter($service, $rootFolderId, $options);
                $driver  = new \League\Flysystem\Filesystem($adapter);

                return new \Illuminate\Filesystem\FilesystemAdapter($driver, $adapter);
            });
        } catch(\Exception $e) {
            // Silent fail
        }
    }
}