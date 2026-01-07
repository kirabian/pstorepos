<?php

namespace App\Livewire\Stok;

use App\Models\Stok;
use App\Models\Merk;
use App\Models\Tipe;
use App\Models\Cabang; // Import Model Cabang
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

    // Field Umum / Penerima
    public $nama_penerima = '';
    public $nomor_handphone = '';
    public $alamat = '';
    public $catatan = '';
    
    // Field Khusus Pindah Cabang
    public $target_cabang_id = '';
    
    // Field Khusus Retur
    public $nama_petugas = '';
    public $segel = '';
    public $kendala_retur = '';
    public $tanggal_pembelian = '';
    public $nama_customer = '';
    public $instagram_customer = ''; // Opsional
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

    // --- FORM PROPERTIES (CRUD BIASA) ---
    #[Rule('required')] public $merk_id = '';
    #[Rule('required')] public $tipe_id = '';
    #[Rule('required')] public $ram_storage = '';
    #[Rule('required|in:Baru,Second')] public $kondisi = 'Baru';
    #[Rule('required|unique:stoks,imei')] public $imei = '';
    #[Rule('nullable|numeric')] public $harga_modal = 0;
    #[Rule('required|numeric')] public $harga_jual = 0;

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

    // --- LOGIKA RUMUS OTOMATIS ---
    public function updatedHargaModal($value)
    {
        $modal = (int) $value;
        if($modal > 0) {
            $this->harga_jual = $modal + ($modal * 0.1);
        } else {
            $this->harga_jual = 0;
        }
    }

    // --- LOGIKA CHECKBOX ---
    public function updatedSelectAll($value)
    {
        if ($value) {
            $stoksDiHalamanIni = Stok::where('imei', 'like', '%' . $this->search . '%')
                ->latest()->paginate(10)->pluck('id')->map(fn($id) => (string) $id)->toArray();
            $this->selectedStok = $stoksDiHalamanIni;
        } else {
            $this->selectedStok = [];
        }
    }

    public function updatedSelectedStok()
    {
        $this->selectAll = false;
    }

    public function resetInputFields()
    {
        // Reset CRUD
        $this->merk_id = ''; $this->tipe_id = ''; $this->ram_storage = '';
        $this->kondisi = 'Baru'; $this->imei = ''; $this->harga_modal = 0; $this->harga_jual = 0;
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
        $this->target_cabang_id = '';
        $this->nama_petugas = ''; $this->segel = ''; $this->kendala_retur = ''; 
        $this->tanggal_pembelian = ''; $this->nama_customer = ''; $this->instagram_customer = '';
        $this->email_icloud = ''; $this->password_email = ''; $this->pola_pin = '';
    }

    // --- LOGIKA UTAMA: VALIDASI DINAMIS & SIMPAN KELUAR ---
    public function storeKeluarStok()
    {
        // 1. Validasi Dasar Kategori
        $this->validate(['kategoriKeluar' => 'required']);

        // 2. Validasi Dinamis Berdasarkan Kategori
        $rules = [];
        $keteranganDetail = ""; // String untuk deskripsi history

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
                $keteranganDetail = "Penerima: {$this->nama_penerima} | HP: {$this->nomor_handphone} | Alamat: {$this->alamat} | Note: {$this->catatan}";
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
                $cabangTujuan = Cabang::find($this->target_cabang_id)->nama ?? '-';
                $keteranganDetail = "Ke Cabang: {$cabangTujuan} | PIC: {$this->nama_penerima} | HP: {$this->nomor_handphone} | Note: {$this->catatan}";
                break;

            case 'Retur':
                $rules = [
                    'nama_petugas' => 'required',
                    'segel' => 'required',
                    'kendala_retur' => 'required',
                    'tanggal_pembelian' => 'required|date',
                    'nama_customer' => 'required',
                    'nomor_handphone' => 'required',
                    'email_icloud' => 'required',
                    'password_email' => 'required',
                    'pola_pin' => 'required',
                ];
                $keteranganDetail = "Retur oleh {$this->nama_petugas}. Customer: {$this->nama_customer} ({$this->nomor_handphone}). Kendala: {$this->kendala_retur}. Akun: {$this->email_icloud} / {$this->password_email}. PIN: {$this->pola_pin}.";
                break;
        }

        $this->validate($rules);

        // 3. Eksekusi
        $user = Auth::user();
        $cabangId = $user->cabang_id;
        $count = 0;

        foreach ($this->selectedStok as $id) {
            $stok = Stok::find($id);
            if ($stok) {
                // Simpan History Lengkap
                StokHistory::create([
                    'imei' => $stok->imei,
                    'status' => 'Stok Keluar', 
                    'cabang_id' => $cabangId,
                    'keterangan' => "[$this->kategoriKeluar] $keteranganDetail (Processed by: {$user->nama_lengkap})",
                    'user_id' => $user->id,
                ]);

                // Hapus Stok Aktif
                $stok->delete();
                $count++;
            }
        }

        // Reset
        $this->selectedStok = [];
        $this->selectAll = false;
        $this->resetInputFields();

        $this->dispatch('close-keluar-modal');
        $this->dispatch('swal', ['title' => 'Berhasil!', 'text' => "$count unit berhasil dikeluarkan.", 'icon' => 'success']);
    }

    // --- CRUD FUNCTION LAMA (TIDAK DIUBAH) ---
    public function store()
    {
        $this->validate([
            'merk_id' => 'required', 'tipe_id' => 'required', 'ram_storage' => 'required',
            'kondisi' => 'required', 'imei' => 'required|unique:stoks,imei,' . $this->stokId,
            'harga_jual' => 'required|numeric',
        ]);

        Stok::updateOrCreate(['id' => $this->stokId], [
            'merk_id' => $this->merk_id, 'tipe_id' => $this->tipe_id, 'ram_storage' => $this->ram_storage,
            'kondisi' => $this->kondisi, 'imei' => $this->imei, 
            'harga_modal' => $this->harga_modal ?: 0, 'harga_jual' => $this->harga_jual,
        ]);

        $user = Auth::user();
        $namaCabang = $user->cabang->nama ?? 'Pusat';

        StokHistory::create([
            'imei' => $this->imei,
            'status' => $this->stokId ? 'Update Data' : 'Stok Masuk',
            'cabang_id' => $user->cabang_id,
            'keterangan' => $this->stokId ? "Data unit diperbarui oleh {$user->nama_lengkap}." : "Stok baru masuk di $namaCabang.",
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
        $this->harga_modal = $stok->harga_modal;
        $this->harga_jual = $stok->harga_jual;
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
        $cabangs = Cabang::orderBy('nama', 'asc')->get(); // Ambil Data Cabang

        $selectedItems = [];
        if (!empty($this->selectedStok)) {
            $selectedItems = Stok::with(['merk', 'tipe'])->whereIn('id', $this->selectedStok)->get();
        }

        return view('livewire.stok.stok-index', [
            'stoks' => $stoks,
            'merks' => $merks,
            'cabangs' => $cabangs, // Kirim ke view
            'selectedItems' => $selectedItems
        ])->title('Manajemen Stok');
    }
}