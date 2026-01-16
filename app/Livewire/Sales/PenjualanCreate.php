<?php

namespace App\Livewire\Sales;

use App\Models\Stok;
use App\Models\Penjualan;
use App\Models\StokHistory;
use App\Models\User; // Tambahkan Model User
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
    
    // Tambahan: Pilihan Sales
    #[Rule('required', as: 'Sales / Akun')]
    public $sales_id = '';

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

    // Set default sales ke user yang login saat halaman dibuka
    public function mount()
    {
        $this->sales_id = Auth::id();
    }

    // Reset pagination saat search berubah
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
        // Reset form tapi biarkan sales_id tetap default (atau user login)
        $this->reset(['nama_customer', 'nomor_wa', 'foto_bukti', 'harga_deal', 'catatan']);
        $this->sales_id = Auth::id(); 
        $this->resetValidation();
    }

    public function storePenjualan()
    {
        $this->validate();

        $user = Auth::user();
        
        // FIX VALIDASI: Cek stok di cabang user ATAU stok pusat (null)
        $stok = Stok::where('id', $this->selectedStokId)
            ->where(function($q) use ($user) {
                $q->where('cabang_id', $user->cabang_id)
                  ->orWhereNull('cabang_id'); // Izinkan jual stok pusat jika null
            })
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

        // Ambil data sales yang dipilih untuk nama di history (opsional)
        $selectedSales = User::find($this->sales_id);
        $namaSales = $selectedSales ? $selectedSales->nama_lengkap : $user->nama_lengkap;

        DB::transaction(function () use ($user, $stok, $namaSales) {
            $path = $this->foto_bukti->store('bukti-penjualan', 'public');

            Penjualan::create([
                'user_id' => $this->sales_id, // MENGGUNAKAN ID SALES YANG DIPILIH
                'cabang_id' => $user->cabang_id,
                'stok_id' => $stok->id,
                'tipe_penjualan' => 'Unit',
                'imei_terjual' => $stok->imei,
                'nama_produk' => optional($stok->merk)->nama . ' ' . optional($stok->tipe)->nama,
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
                // Mencatat bahwa dijual ke customer X oleh Sales Y (Input by Operator Z)
                'keterangan' => "[PENJUALAN] Sold to {$this->nama_customer} by Sales: {$namaSales}",
                'user_id' => $user->id // Tetap mencatat ID user yang login sebagai operator input (audit trail)
            ]);
        });

        $this->dispatch('swal', ['icon' => 'success', 'title' => 'Berhasil', 'text' => 'Penjualan berhasil disimpan!']);
        $this->cancelSelection();
        $this->updatedSearchStok(); 
    }

    public function render()
    {
        $user = Auth::user();

        // LOGIKA AMBIL LIST SALES: Hanya user di cabang yang sama
        $salesUsers = User::where('cabang_id', $user->cabang_id)
                        ->orderBy('nama_lengkap', 'asc')
                        ->get();

        // FIX QUERY: Ambil stok cabang user ATAU stok yang cabang_id nya NULL (Pusat/Belum diset)
        $stoks = Stok::with(['merk', 'tipe'])
            ->where(function($q) use ($user) {
                $q->where('cabang_id', $user->cabang_id)
                  ->orWhereNull('cabang_id'); 
            })
            ->where('jumlah', '>', 0)
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
            'stoks' => $stoks,
            'salesUsers' => $salesUsers // Kirim data user sales ke view
        ])->title('Input Penjualan');
    }
}