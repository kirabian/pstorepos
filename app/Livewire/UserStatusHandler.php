<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Cache;
use App\Models\User;

class UserStatusHandler extends Component
{
    // Fungsi untuk mematikan (diam 10 detik)
    #[On('setUserOffline')]
    public function makeOffline()
    {
        if (auth()->check()) {
            $user = auth()->user();
            $user->updateQuietly(['last_seen' => now()]);
            Cache::forget('user-is-online-' . $user->id);
        }
    }

    // Fungsi BARU untuk menyalakan (saat mouse bergerak lagi)
    #[On('setUserOnline')]
    public function makeOnline()
    {
        if (auth()->check()) {
            $user = auth()->user();
            // Nyalakan kembali cache online selama 11 detik
            Cache::put('user-is-online-' . $user->id, true, now()->addSeconds(11));
            $user->updateQuietly(['last_seen' => now()]);
        }
    }

    public function render()
    {
        return <<<'HTML'
            <div style="display: none;"></div>
        HTML;
    }
}