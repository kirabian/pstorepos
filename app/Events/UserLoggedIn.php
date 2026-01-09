<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UserLoggedIn implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user_name;
    public $user_role;
    public $location;
    public $cabang_id;
    public $distributor_id;

    public function __construct(User $user)
    {
        $this->user_name = $user->nama_lengkap;
        $this->user_role = strtoupper($user->role);
        $this->cabang_id = $user->cabang_id;
        $this->distributor_id = $user->distributor_id;

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
        // Debugging ke Laravel Log
        Log::info('BROADCASTING EVENT: UserLoggedIn', ['channels' => 'superadmin-notify']);
        
        $channels = [];
        $channels[] = new PrivateChannel('superadmin-notify');

        if ($this->cabang_id) {
            $channels[] = new PrivateChannel('branch-notify.' . $this->cabang_id);
        }

        return $channels;
    }

    public function broadcastAs()
    {
        // Nama event yang pasti
        return 'login-event'; 
    }
}