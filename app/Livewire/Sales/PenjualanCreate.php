<?php

namespace App\Livewire\Sales;

use App\Models\Stok;
use App\Models\Penjualan;
use App\Models\StokHistory;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Rule;

class PenjualanCreate extends Component
{
    use WithFileUploads;
    use WithPagination; 

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
    public $foto_bukti; 

    #[Rule('required|numeric', as: 'Harga Deal')] 
    public $harga_deal = '';

    #[Rule('nullable')] 
    public $catatan = '';

    // FIXING 1: Reset halaman pagination saat user mengetik search
    public function updatedSearchStok()
    {
        $this->resetPage();
    }

    public function selectStok($id)
    {
        $this->selectedStokId = $id;
        $this->selectedStokDetail = Stok::with(['merk', 'tipe'])->find($id);
        
        if($this->selectedStokDetail) {
            $this->harga_deal = $this->selectedStokDetail->harga_jual;
        }
    }

    public function cancelSelection()
    {
        $this->selectedStokId = null;
        $this->selectedStokDetail = null;
        $this->reset(['nama_customer', 'nomor_wa', 'foto_bukti', 'harga_deal', 'catatan']);
        $this->resetValidation();
    }

    public function storePenjualan()
    {
        $this->validate();

        $user = Auth::user();
        
        // Validasi stok masih ada sebelum simpan
        $stok = Stok::where('id', $this->selectedStokId)
                    ->where('cabang_id', $user->cabang_id)
                    ->where('jumlah', '>', 0)
                    ->first();

        if (!$stok) {
            $this->dispatch('swal', [
                'icon' => 'error', 
                'title' => 'Gagal', 
                'text' => 'Stok barang ini sudah habis atau tidak ditemukan!'
            ]);
            return;
        }

        DB::transaction(function () use ($user, $stok) {
            $path = $this->foto_bukti->store('bukti-penjualan', 'public');

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

            $stok->decrement('jumlah');

            StokHistory::create([
                'imei' => $stok->imei,
                'status' => 'Stok Keluar', 
                'cabang_id' => $user->cabang_id,
                'keterangan' => "[PENJUALAN] Sold to {$this->nama_customer}",
                'user_id' => $user->id
            ]);
        });

        $this->dispatch('swal', ['icon' => 'success', 'title' => 'Berhasil', 'text' => 'Penjualan berhasil disimpan!']);
        $this->cancelSelection();
        $this->updatedSearchStok(); // Refresh list
    }

    public function render()
    {
        $user = Auth::user();

        // FIXING 2: Logic Query yang lebih aman
        $stoks = Stok::with(['merk', 'tipe'])
            // 1. Pastikan stok ada di cabang user yg login
            ->where('cabang_id', $user->cabang_id) 
            // 2. Pastikan jumlah > 0
            ->where('jumlah', '>', 0)
            // 3. Filter Search (hanya jalan jika ada ketikan)
            ->when($this->searchStok, function($query) {
                $query->where(function($q) {
                    $q->where('imei', 'like', '%' . $this->searchStok . '%')
                      ->orWhereHas('merk', fn($q2) => $q2->where('nama', 'like', '%'.$this->searchStok.'%'))
                      ->orWhereHas('tipe', fn($q2) => $q2->where('nama', 'like', '%'.$this->searchStok.'%'));
                });
            })
            ->latest()
            ->paginate(5);

        return view('livewire.sales.penjualan-create', [
            'stoks' => $stoks
        ])->title('Input Penjualan');
    }
}