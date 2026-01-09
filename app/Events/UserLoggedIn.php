<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserLoggedIn implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user_name;
    public $user_role;
    public $location;
    public $cabang_id;
    public $distributor_id;

    /**
     * Create a new event instance.
     */
    public function __construct(User $user)
    {
        $this->user_name = $user->nama_lengkap;
        $this->user_role = str_replace('_', ' ', strtoupper($user->role));
        $this->cabang_id = $user->cabang_id;
        $this->distributor_id = $user->distributor_id;

        // Tentukan lokasi string untuk notifikasi
        if ($user->cabang) {
            $this->location = $user->cabang->nama_cabang;
        } elseif ($user->distributor) {
            $this->location = $user->distributor->nama_distributor;
        } else {
            $this->location = 'Headquarters';
        }
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        $channels = [];

        // 1. Channel Khusus Superadmin (Menerima SEMUA login)
        $channels[] = new PrivateChannel('superadmin-notify');

        // 2. Channel Khusus Cabang (Untuk Audit yang megang cabang ini)
        if ($this->cabang_id) {
            $channels[] = new PrivateChannel('branch-notify.' . $this->cabang_id);
        }

        // 3. Channel Khusus Distributor (Opsional jika Audit juga pegang distributor)
        if ($this->distributor_id) {
            $channels[] = new PrivateChannel('distributor-notify.' . $this->distributor_id);
        }

        return $channels;
    }

    public function broadcastAs()
    {
        return 'user.logged.in';
    }
}