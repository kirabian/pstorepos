<?php

namespace App\Livewire\BarangKeluar;

use App\Models\StokHistory;
use Livewire\Component;
use Livewire\WithPagination;

class BarangKeluarIndex extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $bulan = '';
    public $tahun = '';
    public $kategori = '';

    // Opsi Kategori Filter sesuai gambar
    public $opsiFilter = [
        'Admin WhatsApp',
        'Shopee',
        'Pindah Cabang',
        'Giveaway',
        'Retur',
        'Penjualan',
        'Tukar Tambah',
        'Refund / Angkut Barang'
    ];

    public function mount()
    {
        $this->bulan = date('m');
        $this->tahun = date('Y');
    }

    public function updatedKategori()
    {
        $this->resetPage();
    }

    public function render()
    {
        // Logika: Barang keluar adalah history dengan status 'Stok Keluar'
        $query = StokHistory::with(['user', 'cabang'])
            ->where('status', 'Stok Keluar');

        // Filter Search IMEI
        if ($this->search) {
            $query->where('imei', 'like', '%' . $this->search . '%');
        }

        // Filter Bulan & Tahun
        if ($this->bulan) {
            $query->whereMonth('created_at', $this->bulan);
        }
        if ($this->tahun) {
            $query->whereYear('created_at', $this->tahun);
        }

        // Filter Kategori (Mencari teks kategori di dalam kolom keterangan)
        if ($this->kategori) {
            $query->where('keterangan', 'like', '%' . $this->kategori . '%');
        }

        $data = $query->latest()->paginate(10);

        return view('livewire.barang-keluar.barang-keluar-index', [
            'histories' => $data
        ])->title('Barang Keluar');
    }
}