<?php

namespace App\Livewire\Partials;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class LoginNotification extends Component
{
    public $channels = [];

    public function mount()
    {
        $user = Auth::user();

        if (!$user) return;

        // 1. Jika Superadmin, dengarkan channel global
        if ($user->role === 'superadmin') {
            $this->channels[] = 'superadmin-notify';
        }

        // 2. Jika Audit, dengarkan channel per-cabang yang dia pegang
        if ($user->role === 'audit') {
            // Mengambil accessor getAccessCabangIdsAttribute dari Model User
            $branchIds = $user->access_cabang_ids; 
            
            foreach ($branchIds as $id) {
                $this->channels[] = 'branch-notify.' . $id;
            }
        }
    }

    public function render()
    {
        return view('livewire.partials.login-notification');
    }
}