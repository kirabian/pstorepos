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
use App\Models\Stok;
use App\Models\StokHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

#[Lazy]
class Dashboard extends Component
{
    // Properties untuk Superadmin (Public agar bisa diakses view dashboard utama)
    public $totalUsers = 0;
    public $totalCabang = 0;
    public $totalGudang = 0;
    public $totalDistributor = 0;
    public $onlineUsersCount = 0;

    // Properties untuk Audit (Public agar bisa diakses view dashboard audit)
    public $cabangCount = 0;
    public $masukToday = 0;
    public $keluarToday = 0;
    public $pendingApprovals = [];
    public $priceLogs = [];

    public function mount()
    {
        $user = Auth::user();

        // 1. JIKA SUPERADMIN: Load Statistik Global
        if ($user->role === 'superadmin') {
            $this->totalUsers = User::count();
            $this->totalCabang = Cabang::count();
            $this->totalGudang = Gudang::count();
            $this->totalDistributor = Distributor::count();
            // Hitung user online via Cache
            $this->onlineUsersCount = User::all()->filter(fn($u) => $u->isOnline())->count();
        }

        // 2. JIKA AUDIT: Load Statistik Khusus Cabang Pegangan
        elseif ($user->role === 'audit') {
            // Ambil array ID cabang yang dipegang audit ini
            $cabangIds = $user->access_cabang_ids; 
            
            $this->cabangCount = count($cabangIds);

            // Hitung Barang Masuk Hari Ini (Di cabang dia)
            $this->masukToday = StokHistory::whereIn('cabang_id', $cabangIds)
                ->where('status', 'like', '%Masuk%')
                ->whereDate('created_at', today())
                ->count();

            // Hitung Barang Keluar Hari Ini (Di cabang dia)
            $this->keluarToday = StokHistory::whereIn('cabang_id', $cabangIds)
                ->where('status', 'Stok Keluar')
                ->whereDate('created_at', today())
                ->count();

            // Ambil Data Pending Approval (Simulasi: Retur/Void)
            $this->pendingApprovals = StokHistory::with(['user', 'cabang'])
                ->whereIn('cabang_id', $cabangIds)
                ->where(function($q) {
                    $q->where('keterangan', 'like', '%Retur%')
                      ->orWhere('keterangan', 'like', '%Void%');
                })
                ->latest()
                ->take(5)
                ->get();

            // Ambil Log Perubahan Harga (Update Data)
            $this->priceLogs = StokHistory::with(['user', 'cabang'])
                ->whereIn('cabang_id', $cabangIds)
                ->where('status', 'Update Data')
                ->latest()
                ->take(5)
                ->get();
        }
    }

    #[On('echo:pstore-channel,inventory.updated')]
    public function handleUpdate($event)
    {
        session()->flash('info', 'Notifikasi: ' . ($event['message'] ?? 'Data baru masuk!'));
        $this->mount(); // Refresh statistik real-time
    }

    public function placeholder()
    {
        return view('livewire.dashboard-skeleton'); // Pastikan file view skeleton ada, atau hapus method ini jika error
    }

    public function testSinyal()
    {
        $name = auth()->user()->nama_lengkap ?? 'Admin';
        // Pastikan event InventoryUpdate sudah dibuat
        broadcast(new \App\Events\InventoryUpdate("Sinyal dikirim oleh " . $name))->toOthers();
        session()->flash('info', 'Sinyal berhasil dikirim!');
    }

    #[Layout('layouts.master')]
    public function render()
    {
        $user = Auth::user();

        // 1. Tampilan Khusus AUDIT
        if ($user->role === 'audit') {
            return view('livewire.dashboard-audit')
                ->title('Audit Control Center');
        }

        // 2. Tampilan Khusus SUPERADMIN
        if ($user->role === 'superadmin') {
            return view('livewire.dashboard', [
                // Data tambahan untuk superadmin table
                'stok_menipis' => Stok::where('jumlah', '<', 5)->count(),
                'recent_history' => StokHistory::with('user')->latest()->take(5)->get()
            ])->title('Superadmin Overview');
        }

        // 3. Tampilan User Lain (Sales, Gudang, dll)
        return view('livewire.dashboard-user')->title('Dashboard');
    }
}