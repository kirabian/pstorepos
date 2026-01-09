<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow; // PENTING: Pakai Now
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserLoggedIn implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user_name;
    public $user_role;
    public $location;
    public $cabang_id;

    public function __construct(User $user)
    {
        $this->user_name = $user->nama_lengkap;
        $this->user_role = $user->role;
        $this->cabang_id = $user->cabang_id;

        // Logic penentuan lokasi string
        if ($user->cabang) {
            $this->location = $user->cabang->nama_cabang;
        } elseif ($user->distributor) {
            $this->location = $user->distributor->nama_distributor;
        } else {
            $this->location = 'Headquarters';
        }
    }

    public function broadcastOn(): array
    {
        $channels = [];

        // 1. Kirim ke Channel Superadmin
        $channels[] = new PrivateChannel('superadmin-notify');

        // 2. Kirim ke Channel Cabang (Jika user punya cabang)
        if ($this->cabang_id) {
            $channels[] = new PrivateChannel('branch-notify.' . $this->cabang_id);
        }

        return $channels;
    }

    public function broadcastAs()
    {
        return 'user.logged.in';
    }
}