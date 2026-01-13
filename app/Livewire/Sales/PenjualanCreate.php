<?php

namespace App\Livewire\Sales;

use App\Models\Stok;
use App\Models\Penjualan;
use App\Models\StokHistory;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination; // Tambahkan ini untuk pagination
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Rule;

class PenjualanCreate extends Component
{
    use WithFileUploads;
    use WithPagination; // Gunakan trait pagination

    protected $paginationTheme = 'bootstrap';

    // --- STEP 1: PILIH PRODUK ---
    public $searchStok = '';
    public $selectedStokId = null;
    public $selectedStokDetail = null;

    // --- STEP 2: FORM PENJUALAN ---
    #[Rule('required', as: 'Nama Customer')] 
    public $nama_customer = '';

    #[Rule('required|numeric', as: 'Nomor WA')] 
    public $nomor_wa = '';

    #[Rule('required|image|max:2048', as: 'Foto Bukti')] 
    public $foto_bukti = ''; 

    #[Rule('required|numeric', as: 'Harga Deal')] 
    public $harga_deal = '';

    #[Rule('nullable')] 
    public $catatan = '';

    // Reset pagination saat search berubah
    public function updatedSearchStok()
    {
        $this->resetPage();
    }

    public function selectStok($id)
    {
        $this->selectedStokId = $id;
        $this->selectedStokDetail = Stok::with(['merk', 'tipe'])->find($id);
        
        // Auto isi harga jual standar jika barang ditemukan
        if($this->selectedStokDetail) {
            $this->harga_deal = $this->selectedStokDetail->harga_jual;
        }
    }

    public function cancelSelection()
    {
        $this->selectedStokId = null;
        $this->selectedStokDetail = null;
        // Kita reset input form penjualan, tapi search stok biarkan saja
        $this->reset(['nama_customer', 'nomor_wa', 'foto_bukti', 'harga_deal', 'catatan']);
        $this->resetValidation();
    }

    public function storePenjualan()
    {
        $this->validate();

        $user = Auth::user();
        
        // Cek Stok Lagi (Concurrency Check / Validasi Akhir)
        // Pastikan barang masih ada di cabang user dan jumlahnya > 0
        $stok = Stok::where('id', $this->selectedStokId)
                    ->where('cabang_id', $user->cabang_id)
                    ->where('jumlah', '>', 0)
                    ->first();

        if (!$stok) {
            $this->dispatch('swal', [
                'icon' => 'error', 
                'title' => 'Gagal', 
                'text' => 'Stok barang ini sudah habis atau tidak ditemukan di cabang Anda!'
            ]);
            return;
        }

        DB::transaction(function () use ($user, $stok) {
            // 1. Upload Foto
            $path = $this->foto_bukti->store('bukti-penjualan', 'public');

            // 2. Buat Record Penjualan
            Penjualan::create([
                'user_id' => $user->id,
                'cabang_id' => $user->cabang_id,
                'stok_id' => $stok->id,
                'tipe_penjualan' => 'Unit',
                'imei_terjual' => $stok->imei,
                'nama_produk' => $stok->merk->nama . ' ' . $stok->tipe->nama,
                'nama_customer' => $this->nama_customer,
                'nomor_wa' => $this->nomor_wa,
                'foto_bukti_transaksi' => $path,
                'harga_jual_real' => $this->harga_deal,
                'catatan' => $this->catatan,
                'status_audit' => 'Pending',
            ]);

            // 3. Kurangi Stok (Otomatis jadi Barang Keluar)
            $stok->decrement('jumlah');

            // 4. Catat di Stok History
            StokHistory::create([
                'imei' => $stok->imei,
                'status' => 'Stok Keluar', 
                'cabang_id' => $user->cabang_id,
                'keterangan' => "[PENJUALAN SALES] Sold to {$this->nama_customer} by {$user->nama_lengkap}",
                'user_id' => $user->id
            ]);
        });

        $this->dispatch('swal', [
            'icon' => 'success', 
            'title' => 'Berhasil', 
            'text' => 'Penjualan berhasil disimpan & Stok berkurang!'
        ]);
        
        // Reset Form
        $this->cancelSelection();
    }

    public function render()
    {
        $user = Auth::user();

        // QUERY STOK:
        // 1. Ambil stok yang Cabang ID-nya SAMA dengan Cabang ID User Sales.
        // 2. Jumlah stok harus lebih dari 0.
        // 3. Filter pencarian berdasarkan IMEI, Nama Merk, atau Nama Tipe.
        
        $stoks = Stok::with(['merk', 'tipe'])
            ->where('cabang_id', $user->cabang_id) 
            ->where('jumlah', '>', 0)
            ->where(function($q) {
                $q->where('imei', 'like', '%' . $this->searchStok . '%')
                  ->orWhereHas('merk', fn($q2) => $q2->where('nama', 'like', '%'.$this->searchStok.'%'))
                  ->orWhereHas('tipe', fn($q2) => $q2->where('nama', 'like', '%'.$this->searchStok.'%'));
            })
            ->latest()
            ->paginate(5); // Tampilkan 5 barang per halaman di list kiri

        return view('livewire.sales.penjualan-create', [
            'stoks' => $stoks
        ])->title('Input Penjualan');
    }
}