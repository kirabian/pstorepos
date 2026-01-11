<?php

namespace App\Livewire\Distributor;

use App\Models\Cabang;
use App\Models\Stok;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.master')]
#[Title('Monitoring Stok Cabang')]
class StokCabangIndex extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search = '';

    public function mount()
    {
        $user = Auth::user();
        // Proteksi: Hanya Distributor atau Staff Distributor
        $isDistributor = ($user->role === 'distributor') || ($user->role === 'inventory_staff' && $user->distributor_id);
        
        if (!$isDistributor) {
            abort(403, 'Akses Ditolak. Khusus Distributor.');
        }
    }

    public function render()
    {
        // Ambil data cabang beserta jumlah stok aktif (status: ready)
        // Kita asumsikan ada relasi stoks() di model Cabang, atau kita query manual count
        $cabangs = Cabang::query()
            ->when($this->search, function($q) {
                $q->where('nama_cabang', 'like', '%'.$this->search.'%')
                  ->orWhere('kode_cabang', 'like', '%'.$this->search.'%');
            })
            // Menghitung jumlah stok per cabang
            ->withCount(['stoks' => function ($query) {
                $query->where('status', 'ready');
            }])
            ->orderBy('stoks_count', 'desc') // Urutkan dari stok terbanyak
            ->paginate(10);

        return view('livewire.distributor.stok-cabang-index', [
            'cabangs' => $cabangs
        ]);
    }
}