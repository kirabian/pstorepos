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

        // Jika Superadmin, subscribe ke channel 'superadmin-notify'
        if ($user->role === 'superadmin') {
            $this->channels[] = 'superadmin-notify';
        }

        // Jika Audit, loop semua cabang yang dipegang dan subscribe
        if ($user->role === 'audit') {
            $myBranches = $user->access_cabang_ids ?? [];
            foreach ($myBranches as $branchId) {
                $this->channels[] = 'branch-notify.' . $branchId;
            }
        }
    }

    public function render()
    {
        return view('livewire.partials.login-notification');
    }
}