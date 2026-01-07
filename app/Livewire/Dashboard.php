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
    // Properties untuk Superadmin
    public $totalUsers = 0;
    public $totalCabang = 0;
    public $totalGudang = 0;
    public $totalDistributor = 0;
    public $onlineUsersCount = 0;

    // Properties untuk Audit
    public $cabangCount = 0;
    public $masukToday = 0;
    public $keluarToday = 0;
    public $pendingApprovals = [];
    public $priceLogs = [];

    public function mount()
    {
        $user = Auth::user();

        // 1. JIKA SUPERADMIN
        if ($user->role === 'superadmin') {
            $this->totalUsers = User::count();
            $this->totalCabang = Cabang::count();
            $this->totalGudang = Gudang::count();
            $this->totalDistributor = Distributor::count();
            $this->onlineUsersCount = User::all()->filter(fn($u) => $u->isOnline())->count();
        }

        // 2. JIKA AUDIT
        elseif ($user->role === 'audit') {
            
            // FIX: Pastikan ini selalu array, hindari null
            $cabangIds = $user->access_cabang_ids ?? []; 

            $this->cabangCount = count($cabangIds);

            // Jika punya cabang, baru query datanya
            if (!empty($cabangIds)) {
                $this->masukToday = StokHistory::whereIn('cabang_id', $cabangIds)
                    ->where('status', 'like', '%Masuk%')
                    ->whereDate('created_at', today())
                    ->count();

                $this->keluarToday = StokHistory::whereIn('cabang_id', $cabangIds)
                    ->where('status', 'Stok Keluar')
                    ->whereDate('created_at', today())
                    ->count();

                $this->pendingApprovals = StokHistory::with(['user', 'cabang'])
                    ->whereIn('cabang_id', $cabangIds)
                    ->where(function($q) {
                        $q->where('keterangan', 'like', '%Retur%')
                          ->orWhere('keterangan', 'like', '%Void%');
                    })
                    ->latest()
                    ->take(5)
                    ->get();

                $this->priceLogs = StokHistory::with(['user', 'cabang'])
                    ->whereIn('cabang_id', $cabangIds)
                    ->where('status', 'Update Data')
                    ->latest()
                    ->take(5)
                    ->get();
            } else {
                // Jika belum pegang cabang, kosongkan data
                $this->masukToday = 0;
                $this->keluarToday = 0;
                $this->pendingApprovals = collect([]); 
                $this->priceLogs = collect([]);
            }
        }
    }

    #[On('echo:pstore-channel,inventory.updated')]
    public function handleUpdate($event)
    {
        session()->flash('info', 'Notifikasi: ' . ($event['message'] ?? 'Data baru masuk!'));
        $this->mount(); 
    }

    public function placeholder()
    {
        if (view()->exists('livewire.dashboard-skeleton')) {
            return view('livewire.dashboard-skeleton');
        }
        return <<<'HTML'
        <div class="d-flex justify-content-center align-items-center vh-100">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
        HTML;
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
        $user = Auth::user();

        // 1. Tampilan Khusus AUDIT
        if ($user->role === 'audit') {
            // PASSING DATA SECARA EKSPLISIT KE VIEW AGAR VARIABEL DIKENALI
            return view('livewire.dashboard-audit', [
                'cabang_count' => $this->cabangCount,
                'masuk_today' => $this->masukToday,
                'keluar_today' => $this->keluarToday,
                'pending_approvals' => $this->pendingApprovals,
                'price_logs' => $this->priceLogs
            ])->title('Audit Control Center');
        }

        // 2. Tampilan Khusus SUPERADMIN
        if ($user->role === 'superadmin') {
            return view('livewire.dashboard', [
                'stok_menipis' => Stok::where('jumlah', '<', 5)->count(),
                'recent_history' => StokHistory::with('user')->latest()->take(5)->get()
            ])->title('Superadmin Overview');
        }

        // 3. Tampilan User Lain
        return view('livewire.dashboard-user')->title('Dashboard');
    }
}