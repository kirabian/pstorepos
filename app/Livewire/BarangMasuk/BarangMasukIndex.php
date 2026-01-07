<?php

namespace App\Livewire\BarangMasuk;

use App\Models\StokHistory;
use App\Models\Stok; // Pastikan Import Model Stok Aktif
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
        // LOGIKA BARU:
        // 1. Ambil History yang statusnya 'Masuk' (Stok Masuk)
        // 2. Filter hanya tampilkan jika IMEI tersebut MASIH ADA di tabel 'stoks' (Stok Aktif)
        // 3. Jika stok sudah keluar (dihapus dari tabel stoks), maka history masuknya hidden.

        $query = StokHistory::with(['user', 'cabang'])
            ->where('status', 'like', '%Masuk%')
            ->whereIn('imei', Stok::select('imei')); // <--- FILTER KUNCI DISINI

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

        // Filter Kategori (Jika ada)
        if ($this->kategori) {
            $query->where('keterangan', 'like', '%' . $this->kategori . '%');
        }

        $data = $query->latest()->paginate(10);

        return view('livewire.barang-masuk.barang-masuk-index', [
            'histories' => $data
        ])->title('Barang Masuk');
    }
}