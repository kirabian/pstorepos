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
    // Gunakan auth:sanctum jika pakai API, atau auth:web untuk session
    Broadcast::routes([
        'middleware' => ['web', 'auth'],
        'prefix' => 'broadcasting',
        'as' => 'broadcasting.'
    ]);

    require base_path('routes/channels.php');
}
}