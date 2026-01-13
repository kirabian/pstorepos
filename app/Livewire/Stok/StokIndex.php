<?php

namespace App\Livewire\Stok;

use App\Models\Stok;
use App\Models\Merk;
use App\Models\Tipe;
use App\Models\Cabang;
use App\Models\StokHistory; 
use Illuminate\Support\Facades\Auth; 
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Rule;

class StokIndex extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $stokId;
    public $isEdit = false;

    // --- FITUR SELEKSI & KELUAR STOK ---
    public $selectedStok = []; 
    public $selectAll = false; 

    // --- FORM PROPERTIES: STOK KELUAR ---
    #[Rule('required', as: 'Kategori')]
    public $kategoriKeluar = '';

    // Field Dinamis Stok Keluar
    public $nama_penerima = '';
    public $nomor_handphone = '';
    public $alamat = '';
    public $catatan = '';
    public $target_cabang_id = '';
    public $nama_petugas = '';
    public $segel = '';
    public $kendala_retur = '';
    
    // Jumlah yang dipindahkan (Default 1)
    public $jumlah_pindah = 1; 

    public $opsiKategori = [
        'Admin WhatsApp' => 'Admin WhatsApp',
        'Shopee' => 'Shopee',
        'Tokopedia' => 'Tokopedia',
        'Giveaway' => 'Giveaway',
        'Kesalahan Input' => 'Kesalahan Input',
        'Pindah Cabang' => 'Pindah Cabang', 
        'Retur' => 'Retur',
    ];

    // --- FORM PROPERTIES (CRUD STOK) ---
    #[Rule('required')] public $merk_id = '';
    #[Rule('required')] public $tipe_id = '';
    #[Rule('required')] public $ram_storage = '';
    #[Rule('required|in:Baru,Second')] public $kondisi = 'Baru';
    #[Rule('required|unique:stoks,imei')] public $imei = '';
    #[Rule('required|numeric|min:1')] public $jumlah = 1;
    #[Rule('nullable')] public $harga_modal = ''; 
    #[Rule('required')] public $harga_jual = ''; 

    // --- DATA LISTS ---
    public $tipeOptions = []; 
    public $ramOptions = [];  

    // --- LOGIKA DEPENDENT DROPDOWN ---
    public function updatedMerkId($value)
    {
        $this->tipeOptions = Tipe::where('merk_id', $value)->get();
        $this->tipe_id = ''; $this->ram_storage = ''; $this->ramOptions = []; 
    }

    public function updatedTipeId($value)
    {
        if(!empty($value)) {
            $tipe = Tipe::find($value);
            $this->ramOptions = $tipe->ram_storage ?? []; 
        } else {
            $this->ramOptions = [];
        }
        $this->ram_storage = ''; 
    }

    private function cleanRupiah($value)
    {
        return (int) str_replace(['Rp', '.', ' '], '', $value);
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedStok = Stok::where('imei', 'like', '%' . $this->search . '%')
                ->latest()->paginate(10)->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selectedStok = [];
        }
    }

    public function updatedSelectedStok() { $this->selectAll = false; }

    public function resetInputFields()
    {
        $this->merk_id = ''; $this->tipe_id = ''; $this->ram_storage = '';
        $this->kondisi = 'Baru'; $this->imei = ''; 
        $this->jumlah = 1; 
        $this->harga_modal = ''; 
        $this->harga_jual = '';
        
        $this->tipeOptions = []; $this->ramOptions = [];
        $this->stokId = null; $this->isEdit = false;

        $this->kategoriKeluar = '';
        $this->resetFormKeluar();
        
        $this->resetErrorBag();
    }

    public function resetFormKeluar()
    {
        $this->nama_penerima = ''; $this->nomor_handphone = ''; $this->alamat = ''; $this->catatan = '';
        $this->target_cabang_id = ''; $this->nama_petugas = ''; $this->segel = ''; $this->kendala_retur = ''; 
        $this->jumlah_pindah = 1; 
    }

    // --- LOGIKA UTAMA: STORE KELUAR / PINDAH CABANG ---
    public function storeKeluarStok()
    {
        $this->validate(['kategoriKeluar' => 'required']);

        $rules = [];
        $keteranganDetail = ""; 

        switch ($this->kategoriKeluar) {
            case 'Admin WhatsApp':
            case 'Shopee':
            case 'Tokopedia':
            case 'Giveaway':
                $rules = [
                    'nama_penerima' => 'required',
                    'nomor_handphone' => 'required',
                    'alamat' => 'required',
                    'catatan' => 'required',
                ];
                $keteranganDetail = "Penerima: {$this->nama_penerima} | HP: {$this->nomor_handphone} | Note: {$this->catatan}";
                break;

            case 'Kesalahan Input':
                $rules = ['catatan' => 'required'];
                $keteranganDetail = "Alasan: {$this->catatan}";
                break;

            case 'Pindah Cabang':
                $rules = [
                    'target_cabang_id' => 'required',
                    'nama_penerima' => 'required', 
                    'nomor_handphone' => 'required', 
                    'alamat' => 'required', 
                    'catatan' => 'required',
                    'jumlah_pindah' => 'required|numeric|min:1' 
                ];
                $cabangTujuan = Cabang::find($this->target_cabang_id);
                $namaCabangTujuan = $cabangTujuan->nama_cabang ?? '-';
                $keteranganDetail = "Ke Cabang: {$namaCabangTujuan} | PIC: {$this->nama_penerima} | Note: {$this->catatan}";
                break;

            case 'Retur':
                $rules = [
                    'nama_petugas' => 'required',
                    'kendala_retur' => 'required',
                    'nama_customer' => 'required',
                ];
                $keteranganDetail = "Retur: {$this->kendala_retur} (Customer: {$this->nama_customer})";
                break;
        }

        $this->validate($rules);

        $user = Auth::user();
        $count = 0;

        foreach ($this->selectedStok as $id) {
            $stok = Stok::find($id);
            
            if ($stok) {
                // --- LOGIKA PINDAH CABANG (MUTASI & PECAH STOK) ---
                if ($this->kategoriKeluar == 'Pindah Cabang') {
                    
                    // ========================================================
                    // VALIDASI STOK CUKUP ATAU TIDAK
                    // ========================================================
                    if ($stok->jumlah < $this->jumlah_pindah) {
                        // Jika stok kurang, tampilkan error dan hentikan proses
                        $this->dispatch('swal', [
                            'title' => 'Gagal!',
                            'text' => "Stok {$stok->merk->nama} {$stok->tipe->nama} (IMEI: {$stok->imei}) hanya ada {$stok->jumlah}, tapi Anda ingin memindahkan {$this->jumlah_pindah} unit.",
                            'icon' => 'error'
                        ]);
                        return; // Stop eksekusi function
                    }

                    // Jika Cukup, Lanjutkan Proses
                    
                    // 1. KURANGI STOK DI CABANG ASAL
                    $stok->decrement('jumlah', $this->jumlah_pindah);

                    // 2. BUAT STOK BARU DI CABANG TUJUAN
                    Stok::create([
                        'merk_id' => $stok->merk_id,
                        'tipe_id' => $stok->tipe_id,
                        'ram_storage' => $stok->ram_storage,
                        'kondisi' => $stok->kondisi,
                        'imei' => $stok->imei, 
                        'jumlah' => $this->jumlah_pindah, // Jumlah yang dipindah
                        'harga_modal' => $stok->harga_modal,
                        'harga_jual' => $stok->harga_jual,
                        'cabang_id' => $this->target_cabang_id, 
                    ]);

                    // 3. CATAT HISTORY KELUAR (DARI ASAL)
                    StokHistory::create([
                        'imei' => $stok->imei,
                        'status' => 'Stok Keluar (Mutasi)', 
                        'cabang_id' => $stok->cabang_id, 
                        'keterangan' => "Mutasi {$this->jumlah_pindah} Unit ke {$namaCabangTujuan}. PIC: {$this->nama_penerima}",
                        'user_id' => $user->id,
                    ]);

                    // 4. CATAT HISTORY MASUK (KE TUJUAN)
                    StokHistory::create([
                        'imei' => $stok->imei,
                        'status' => 'Stok Masuk (Mutasi)', 
                        'cabang_id' => $this->target_cabang_id, 
                        'keterangan' => "Mutasi {$this->jumlah_pindah} Unit dari " . ($stok->cabang->nama_cabang ?? 'Pusat') . ". PIC: {$this->nama_penerima}",
                        'user_id' => $user->id,
                    ]);

                } else {
                    // --- LOGIKA BARANG KELUAR BIASA (JUAL/RUSAK/DLL) ---
                    if ($stok->jumlah > 0) {
                        $stok->decrement('jumlah'); 
                    }

                    StokHistory::create([
                        'imei' => $stok->imei,
                        'status' => 'Stok Keluar', 
                        'cabang_id' => $user->cabang_id,
                        'keterangan' => "[$this->kategoriKeluar] $keteranganDetail (Sisa Stok: {$stok->jumlah})",
                        'user_id' => $user->id,
                    ]);
                }
                
                $count++;
            }
        }

        // Reset & Sukses
        $this->selectedStok = [];
        $this->selectAll = false;
        $this->resetInputFields();

        $this->dispatch('close-keluar-modal');
        $this->dispatch('swal', ['title' => 'Berhasil!', 'text' => "$count unit berhasil diproses.", 'icon' => 'success']);
    }

    // --- CRUD STORE ---
    public function store()
    {
        $cleanModal = $this->cleanRupiah($this->harga_modal);
        $cleanJual = $this->cleanRupiah($this->harga_jual);

        $this->validate([
            'merk_id' => 'required', 'tipe_id' => 'required', 'ram_storage' => 'required',
            'kondisi' => 'required', 'imei' => 'required|unique:stoks,imei,' . $this->stokId,
            'jumlah' => 'required|numeric|min:0',
        ]);

        $user = Auth::user();

        Stok::updateOrCreate(['id' => $this->stokId], [
            'merk_id' => $this->merk_id, 
            'tipe_id' => $this->tipe_id, 
            'ram_storage' => $this->ram_storage,
            'kondisi' => $this->kondisi, 
            'imei' => $this->imei, 
            'jumlah' => $this->jumlah, 
            'harga_modal' => $cleanModal, 
            'harga_jual' => $cleanJual,
            'cabang_id' => $user->cabang_id, 
        ]);

        $namaCabang = $user->cabang->nama_cabang ?? 'Pusat';

        StokHistory::create([
            'imei' => $this->imei,
            'status' => $this->stokId ? 'Update Data' : 'Stok Masuk',
            'cabang_id' => $user->cabang_id,
            'keterangan' => $this->stokId ? "Update stok oleh {$user->nama_lengkap}" : "Stok masuk di $namaCabang (Stok: $this->jumlah)",
            'user_id' => $user->id,
        ]);

        $this->dispatch('close-modal');
        $this->dispatch('swal', ['title' => 'Berhasil!', 'text' => 'Data disimpan.', 'icon' => 'success']);
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $stok = Stok::findOrFail($id);
        $this->stokId = $id;
        $this->merk_id = $stok->merk_id;
        $this->tipeOptions = Tipe::where('merk_id', $stok->merk_id)->get();
        $this->tipe_id = $stok->tipe_id;
        $tipe = Tipe::find($stok->tipe_id);
        $this->ramOptions = $tipe->ram_storage ?? [];
        $this->ram_storage = $stok->ram_storage;
        $this->kondisi = $stok->kondisi;
        $this->imei = $stok->imei;
        $this->jumlah = $stok->jumlah; 
        $this->harga_modal = number_format($stok->harga_modal, 0, ',', '.');
        $this->harga_jual = number_format($stok->harga_jual, 0, ',', '.');
        $this->isEdit = true;
    }

    public function delete($id)
    {
        Stok::find($id)->delete();
        $this->dispatch('swal', ['title' => 'Dihapus!', 'text' => 'Unit dihapus.', 'icon' => 'success']);
    }

    public function openKeluarStokModal()
    {
        if (empty($this->selectedStok)) {
            $this->dispatch('swal', [['title' => 'Oops!', 'text' => 'Pilih stok dulu.', 'icon' => 'warning']]);
            return;
        }
        $this->resetFormKeluar();
        $this->kategoriKeluar = ''; 
        $this->resetErrorBag();
        $this->dispatch('open-keluar-modal');
    }

    public function render()
    {
        $stoks = Stok::with(['merk', 'tipe', 'cabang'])
            ->where(function($q) {
                $q->where('imei', 'like', '%' . $this->search . '%')
                  ->orWhereHas('merk', fn($q2) => $q2->where('nama', 'like', '%'.$this->search.'%'))
                  ->orWhereHas('tipe', fn($q2) => $q2->where('nama', 'like', '%'.$this->search.'%'));
            })
            ->latest()
            ->paginate(10);

        $merks = Merk::orderBy('nama', 'asc')->get();
        $cabangs = Cabang::orderBy('nama_cabang', 'asc')->get(); 

        $selectedItems = [];
        if (!empty($this->selectedStok)) {
            $selectedItems = Stok::with(['merk', 'tipe'])->whereIn('id', $this->selectedStok)->get();
        }

        return view('livewire.stok.stok-index', [
            'stoks' => $stoks,
            'merks' => $merks,
            'cabangs' => $cabangs, 
            'selectedItems' => $selectedItems
        ])->title('Manajemen Stok');
    }
}