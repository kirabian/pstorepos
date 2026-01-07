<?php

namespace App\Livewire\BarangMasuk;

use App\Models\StokHistory;
use Livewire\Component;
use Livewire\WithPagination;

class BarangMasukIndex extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $bulan = '';
    public $tahun = '';
    public $kategori = '';

    public function mount()
    {
        $this->bulan = date('m');
        $this->tahun = date('Y');
    }

    public function render()
    {
        // Logika: Barang masuk biasanya statusnya 'Stok Masuk' atau 'Update Data' (tergantung sistem)
        // Disini kita filter history yang statusnya mengandung kata 'Masuk'
        
        $query = StokHistory::with(['user', 'cabang'])
            ->where('status', 'like', '%Masuk%');

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

        // Filter Kategori (Jika ada kolom kategori spesifik di history, sesuaikan)
        if ($this->kategori) {
            $query->where('keterangan', 'like', '%' . $this->kategori . '%');
        }

        $data = $query->latest()->paginate(10);

        return view('livewire.barang-masuk.barang-masuk-index', [
            'histories' => $data
        ])->title('Barang Masuk');
    }
}