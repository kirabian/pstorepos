<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 1. Registrasi Route Auth dengan Middleware Web & Auth
        // Ini menggantikan kode manual di web.php tadi
        Broadcast::routes(['middleware' => ['web', 'auth']]);

        // 2. Load File Channels
        require base_path('routes/channels.php');
    }
}