<?php

namespace App\Livewire\Sales;

use App\Models\Stok;
use App\Models\Penjualan;
use App\Models\StokHistory;
use App\Models\User;
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

    // --- STATE KONTROL HALAMAN ---
    // false = Tampilkan halaman pilih akun
    // true  = Tampilkan halaman input penjualan
    public $isSalesSelected = false; 
    public $salesUserDetail = null;

    // --- STEP 1: PILIH PRODUK ---
    public $searchStok = '';
    public $selectedStokId = null;
    public $selectedStokDetail = null;

    // --- STEP 2: FORM PENJUALAN ---
    
    // Kita hapus rule 'required' dari property ini di validasi otomatis
    // karena kita set secara manual lewat fungsi chooseSales
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

    public function mount()
    {
        // Default: Belum ada sales yang dipilih saat halaman load
        $this->isSalesSelected = false;
        $this->sales_id = null;
    }

    // --- FUNGSI BARU: PILIH AKUN SALES ---
    public function chooseSales($id)
    {
        $user = User::find($id);
        
        // Validasi sederhana pastikan user ada dan satu cabang
        if($user && $user->cabang_id == Auth::user()->cabang_id) {
            $this->sales_id = $id;
            $this->salesUserDetail = $user;
            $this->isSalesSelected = true; // Pindah ke halaman form
        }
    }

    // --- FUNGSI BARU: GANTI AKUN (KEMBALI KE DEPAN) ---
    public function changeSalesAccount()
    {
        $this->resetForm();
        $this->isSalesSelected = false;
        $this->sales_id = null;
        $this->salesUserDetail = null;
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
        $this->resetForm();
        // Jangan reset sales_id agar tidak terlempar ke halaman depan
        $this->resetValidation();
    }

    // Helper untuk reset input form saja
    private function resetForm()
    {
        $this->reset(['nama_customer', 'nomor_wa', 'foto_bukti', 'harga_deal', 'catatan']);
    }

    public function storePenjualan()
    {
        // Manual Validasi untuk Sales ID
        if(empty($this->sales_id)) {
            $this->addError('sales_id', 'Akun sales belum dipilih.');
            return;
        }

        $this->validate();

        $user = Auth::user(); // User yang login (Operator/Tablet)
        
        // Cek stok
        $stok = Stok::where('id', $this->selectedStokId)
            ->where(function($q) use ($user) {
                $q->where('cabang_id', $user->cabang_id)
                  ->orWhereNull('cabang_id'); 
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

        // Pastikan data sales detail terisi
        if(!$this->salesUserDetail) {
            $this->salesUserDetail = User::find($this->sales_id);
        }
        $namaSales = $this->salesUserDetail->nama_lengkap;

        DB::transaction(function () use ($user, $stok, $namaSales) {
            $path = $this->foto_bukti->store('bukti-penjualan', 'public');

            Penjualan::create([
                'user_id' => $this->sales_id, // ID Sales yang dipilih di awal
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
                'keterangan' => "[PENJUALAN] Sold to {$this->nama_customer} (Sales: {$namaSales})",
                'user_id' => $user->id // Operator (Log asli sistem)
            ]);
        });

        $this->dispatch('swal', ['icon' => 'success', 'title' => 'Berhasil', 'text' => 'Penjualan berhasil disimpan atas nama ' . $namaSales]);
        
        $this->cancelSelection(); // Reset form barang
        // Kita biarkan tetap di halaman form sales tersebut (tidak kembali ke pemilihan akun)
        // Agar sales tersebut bisa input lagi kalau ada transaksi beruntun.
        // Jika mau ganti akun, user klik tombol "Ganti Akun".
        $this->updatedSearchStok(); 
    }

    public function render()
    {
        $user = Auth::user();

        // 1. Ambil List Sales untuk Halaman Depan
        $salesUsers = User::where('cabang_id', $user->cabang_id)
                        ->where('role', 'sales') // Pastikan hanya role sales yang muncul
                        ->orderBy('nama_lengkap', 'asc')
                        ->get();

        // 2. Ambil List Stok untuk Halaman Input
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
            'salesUsers' => $salesUsers
        ])->title('Input Penjualan');
    }
}