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

    // Opsi Kategori Filter
    public $opsiFilter = [
        'Admin WhatsApp',
        'Shopee',
        'Pindah Cabang', // Pastikan ini ada
        'Giveaway',
        'Retur',
        'Penjualan',
        'Tukar Tambah',
        'Refund / Angkut Barang',
        'Kesalahan Input'
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
        // PERBAIKAN QUERY: 
        // Menggunakan 'like' agar menangkap 'Stok Keluar' DAN 'Stok Keluar (Mutasi)'
        $query = StokHistory::with(['user', 'cabang'])
            ->where('status', 'like', 'Stok Keluar%'); 

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
            // Khusus Pindah Cabang, kadang keywordnya "Mutasi" atau "Pindah Cabang"
            if ($this->kategori == 'Pindah Cabang') {
                $query->where(function($q) {
                    $q->where('keterangan', 'like', '%Pindah Cabang%')
                      ->orWhere('keterangan', 'like', '%Mutasi%');
                });
            } else {
                $query->where('keterangan', 'like', '%' . $this->kategori . '%');
            }
        }

        $data = $query->latest()->paginate(10);

        return view('livewire.barang-keluar.barang-keluar-index', [
            'histories' => $data
        ])->title('Barang Keluar');
    }
}