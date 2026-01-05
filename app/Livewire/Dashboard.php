<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Lazy;
use App\Models\User;
use App\Models\Cabang;
use App\Models\Gudang;
use App\Models\Distributor;
use Illuminate\Support\Facades\Cache;

#[Lazy]
class Dashboard extends Component
{
    public $totalUsers;
    public $totalCabang;
    public $totalGudang;
    public $totalDistributor;
    public $onlineUsersCount;

    public function mount()
    {
        // Hitung statistik untuk Dashboard Superadmin
        $this->totalUsers = User::count();
        $this->totalCabang = Cabang::count();
        $this->totalGudang = Gudang::count();
        $this->totalDistributor = Distributor::count();
        
        // Hitung user online via Cache
        $this->onlineUsersCount = User::all()->filter(fn($user) => $user->isOnline())->count();
    }

    #[On('echo:pstore-channel,inventory.updated')]
    public function handleUpdate($event)
    {
        session()->flash('info', 'Notifikasi: ' . ($event['message'] ?? 'Data baru masuk!'));
        $this->mount(); // Refresh statistik
    }

    public function placeholder()
    {
        return view('livewire.dashboard-skeleton');
    }

    public function testSinyal()
    {
        $name = auth()->user()->nama_lengkap ?? 'Admin';
        broadcast(new \App\Events\InventoryUpdate("Sinyal dikirim oleh " . $name))->toOthers();
        session()->flash('info', 'Sinyal berhasil dikirim!');
    }

    #[Layout('layouts.master')]
    public function render()
    {
        // Jika bukan superadmin, arahkan ke tampilan dashboard biasa atau 403
        if (auth()->user()->role !== 'superadmin') {
            return view('livewire.dashboard-user'); 
        }

        return view('livewire.dashboard');
    }
}