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
    public $tanggal_pembelian = '';
    public $nama_customer = '';
    public $instagram_customer = '';
    public $email_icloud = '';
    public $password_email = '';
    public $pola_pin = '';

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
    
    // Imei
    #[Rule('required|unique:stoks,imei')] public $imei = '';
    
    // Jumlah Stok (Angka)
    #[Rule('required|numeric|min:1')] public $jumlah = 1;

    // Harga (String karena ada format Rp)
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

    // --- HELPER: BERSIHKAN FORMAT RUPIAH ---
    private function cleanRupiah($value)
    {
        // Hapus "Rp", titik, dan spasi agar jadi integer murni
        return (int) str_replace(['Rp', '.', ' '], '', $value);
    }

    // --- LOGIKA CHECKBOX ---
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
        // Reset CRUD
        $this->merk_id = ''; $this->tipe_id = ''; $this->ram_storage = '';
        $this->kondisi = 'Baru'; $this->imei = ''; 
        $this->jumlah = 1; // Default 1
        $this->harga_modal = ''; 
        $this->harga_jual = '';
        
        $this->tipeOptions = []; $this->ramOptions = [];
        $this->stokId = null; $this->isEdit = false;

        // Reset Stok Keluar
        $this->kategoriKeluar = '';
        $this->resetFormKeluar();
        
        $this->resetErrorBag();
    }

    public function resetFormKeluar()
    {
        $this->nama_penerima = ''; $this->nomor_handphone = ''; $this->alamat = ''; $this->catatan = '';
        $this->target_cabang_id = ''; $this->nama_petugas = ''; $this->segel = ''; $this->kendala_retur = ''; 
        $this->tanggal_pembelian = ''; $this->nama_customer = ''; $this->instagram_customer = '';
        $this->email_icloud = ''; $this->password_email = ''; $this->pola_pin = '';
    }

    // --- LOGIKA UTAMA: VALIDASI DINAMIS & SIMPAN KELUAR ---
    public function storeKeluarStok()
    {
        // 1. Validasi Dasar
        $this->validate(['kategoriKeluar' => 'required']);

        // 2. Validasi Dinamis
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
                ];
                $cabangTujuan = Cabang::find($this->target_cabang_id)->nama_cabang ?? '-';
                $keteranganDetail = "Ke Cabang: {$cabangTujuan} | PIC: {$this->nama_penerima}";
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

        // 3. Eksekusi Pengurangan Stok
        $user = Auth::user();
        $count = 0;

        foreach ($this->selectedStok as $id) {
            $stok = Stok::find($id);
            if ($stok) {
                // Kurangi stok jika masih ada
                if ($stok->jumlah > 0) {
                    $stok->decrement('jumlah'); // Kurangi 1
                }

                // Catat History
                StokHistory::create([
                    'imei' => $stok->imei,
                    'status' => 'Stok Keluar', 
                    'cabang_id' => $user->cabang_id,
                    'keterangan' => "[$this->kategoriKeluar] $keteranganDetail (Sisa Stok: {$stok->jumlah})",
                    'user_id' => $user->id,
                ]);

                // Opsional: Hapus row jika stok 0? 
                // Sesuai request "kalau 0 habis", jadi kita biarkan datanya tetap ada tapi jumlah 0.
                
                $count++;
            }
        }

        // Reset
        $this->selectedStok = [];
        $this->selectAll = false;
        $this->resetInputFields();

        $this->dispatch('close-keluar-modal');
        $this->dispatch('swal', ['title' => 'Berhasil!', 'text' => "$count unit berhasil diproses.", 'icon' => 'success']);
    }

    // --- CRUD FUNCTION (STORE/UPDATE) ---
    public function store()
    {
        // Bersihkan Rupiah sebelum validasi
        $cleanModal = $this->cleanRupiah($this->harga_modal);
        $cleanJual = $this->cleanRupiah($this->harga_jual);

        $this->validate([
            'merk_id' => 'required', 'tipe_id' => 'required', 'ram_storage' => 'required',
            'kondisi' => 'required', 'imei' => 'required|unique:stoks,imei,' . $this->stokId,
            'jumlah' => 'required|numeric|min:0',
        ]);

        Stok::updateOrCreate(['id' => $this->stokId], [
            'merk_id' => $this->merk_id, 'tipe_id' => $this->tipe_id, 'ram_storage' => $this->ram_storage,
            'kondisi' => $this->kondisi, 'imei' => $this->imei, 
            'jumlah' => $this->jumlah, // Simpan Jumlah
            'harga_modal' => $cleanModal, 
            'harga_jual' => $cleanJual,
        ]);

        $user = Auth::user();
        $namaCabang = $user->cabang->nama_cabang ?? 'Pusat';

        StokHistory::create([
            'imei' => $this->imei,
            'status' => $this->stokId ? 'Update Data' : 'Stok Masuk',
            'cabang_id' => $user->cabang_id,
            'keterangan' => $this->stokId ? "Update stok oleh {$user->nama_lengkap} (Stok: $this->jumlah)" : "Stok masuk di $namaCabang (Stok: $this->jumlah)",
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
        $this->jumlah = $stok->jumlah; // Load Jumlah
        
        // Format ke Rupiah saat edit agar tampil cantik
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
        $stoks = Stok::with(['merk', 'tipe'])
            ->where(function($q) {
                $q->where('imei', 'like', '%' . $this->search . '%')
                  ->orWhereHas('merk', fn($q2) => $q2->where('nama', 'like', '%'.$this->search.'%'))
                  ->orWhereHas('tipe', fn($q2) => $q2->where('nama', 'like', '%'.$this->search.'%'));
            })
            ->latest()
            ->paginate(10);

        $merks = Merk::orderBy('nama', 'asc')->get();
        // Fixing: Gunakan nama_cabang
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