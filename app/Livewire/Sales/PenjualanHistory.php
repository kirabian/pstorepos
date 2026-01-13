<?php

namespace App\Livewire\Sales;

use App\Models\Penjualan;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class PenjualanHistory extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    // Filter
    public $search = '';
    public $filterStatus = ''; // Kosong = Semua
    public $bulan = '';
    public $tahun = '';

    public function mount()
    {
        // Default ke bulan ini agar loading awal ringan
        $this->bulan = date('m');
        $this->tahun = date('Y');
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = Auth::user();

        // Query Dasar: HANYA MENAMPILKAN DATA SALES YANG LOGIN (user_id = Auth::id())
        $query = Penjualan::with(['stok', 'auditor'])
            ->where('user_id', $user->id);

        // Filter Search (Nama Customer / IMEI / Produk)
        if ($this->search) {
            $query->where(function($q) {
                $q->where('nama_customer', 'like', '%' . $this->search . '%')
                  ->orWhere('imei_terjual', 'like', '%' . $this->search . '%')
                  ->orWhere('nama_produk', 'like', '%' . $this->search . '%');
            });
        }

        // Filter Status Audit
        if ($this->filterStatus) {
            $query->where('status_audit', $this->filterStatus);
        }

        // Filter Waktu
        if ($this->bulan) {
            $query->whereMonth('created_at', $this->bulan);
        }
        if ($this->tahun) {
            $query->whereYear('created_at', $this->tahun);
        }

        $penjualans = $query->latest()->paginate(10);

        // Hitung Summary Sederhana untuk Card Atas
        $totalOmsetBulanIni = Penjualan::where('user_id', $user->id)
            ->whereMonth('created_at', $this->bulan)
            ->whereYear('created_at', $this->tahun)
            ->where('status_audit', '!=', 'Rejected') // Jangan hitung yang reject
            ->sum('harga_jual_real');

        $totalUnitBulanIni = Penjualan::where('user_id', $user->id)
            ->whereMonth('created_at', $this->bulan)
            ->whereYear('created_at', $this->tahun)
            ->where('status_audit', '!=', 'Rejected')
            ->count();

        return view('livewire.sales.penjualan-history', [
            'penjualans' => $penjualans,
            'omset' => $totalOmsetBulanIni,
            'total_unit' => $totalUnitBulanIni
        ])->title('Riwayat Penjualan Saya');
    }
}