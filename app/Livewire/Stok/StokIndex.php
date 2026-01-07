<?php

namespace App\Livewire\Stok;

use App\Models\Stok;
use App\Models\Merk;
use App\Models\Tipe;
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

    // --- FITUR BARU: SELEKSI & KELUAR STOK ---
    public $selectedStok = []; // Array ID stok yang dicentang
    public $selectAll = false; // Status checkbox header
    
    #[Rule('required', as: 'Kategori')]
    public $kategoriKeluar = ''; // Alasan keluar stok
    
    // Opsi Kategori sesuai request gambar
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
    #[Rule('required')]
    public $merk_id = '';

    #[Rule('required')]
    public $tipe_id = '';

    #[Rule('required')]
    public $ram_storage = '';

    #[Rule('required|in:Baru,Second')]
    public $kondisi = 'Baru';

    #[Rule('required|unique:stoks,imei')] 
    public $imei = '';

    #[Rule('nullable|numeric')]
    public $harga_modal = 0;

    #[Rule('required|numeric')]
    public $harga_jual = 0;

    // --- DATA LISTS ---
    public $tipeOptions = []; 
    public $ramOptions = [];  

    // --- LOGIKA DEPENDENT DROPDOWN ---
    public function updatedMerkId($value)
    {
        $this->tipeOptions = Tipe::where('merk_id', $value)->get();
        $this->tipe_id = ''; 
        $this->ram_storage = ''; 
        $this->ramOptions = []; 
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
            $margin = $modal * 0.1; // 10% Profit
            $this->harga_jual = $modal + $margin;
        } else {
            $this->harga_jual = 0;
        }
    }

    // --- LOGIKA CHECKBOX (BULK SELECTION) ---
    public function updatedSelectAll($value)
    {
        if ($value) {
            // Ambil ID dari halaman yang sedang aktif saja agar tidak berat
            $stoksDiHalamanIni = Stok::where('imei', 'like', '%' . $this->search . '%')
                ->orWhereHas('merk', fn($q) => $q->where('nama', 'like', '%'.$this->search.'%'))
                ->orWhereHas('tipe', fn($q) => $q->where('nama', 'like', '%'.$this->search.'%'))
                ->latest()
                ->paginate(10)
                ->pluck('id')
                ->map(fn($id) => (string) $id)
                ->toArray();
                
            $this->selectedStok = $stoksDiHalamanIni;
        } else {
            $this->selectedStok = [];
        }
    }

    // Jika user uncheck satu item, matikan selectAll
    public function updatedSelectedStok()
    {
        $this->selectAll = false;
    }

    public function resetInputFields()
    {
        // Reset Form CRUD
        $this->merk_id = '';
        $this->tipe_id = '';
        $this->ram_storage = '';
        $this->kondisi = 'Baru';
        $this->imei = '';
        $this->harga_modal = 0;
        $this->harga_jual = 0;
        
        $this->tipeOptions = [];
        $this->ramOptions = [];
        
        $this->stokId = null;
        $this->isEdit = false;

        // Reset Form Keluar Stok
        $this->kategoriKeluar = '';
        
        $this->resetErrorBag();
    }

    // --- LOGIKA TAMBAH/EDIT STOK (SINGLE) ---
    public function store()
    {
        $this->validate([
            'merk_id' => 'required',
            'tipe_id' => 'required',
            'ram_storage' => 'required',
            'kondisi' => 'required',
            'imei' => 'required|unique:stoks,imei,' . $this->stokId,
            'harga_jual' => 'required|numeric',
        ]);

        // 1. Simpan Data Stok
        Stok::updateOrCreate(['id' => $this->stokId], [
            'merk_id' => $this->merk_id,
            'tipe_id' => $this->tipe_id,
            'ram_storage' => $this->ram_storage,
            'kondisi' => $this->kondisi,
            'imei' => $this->imei,
            'harga_modal' => $this->harga_modal ?: 0,
            'harga_jual' => $this->harga_jual,
        ]);

        // 2. LOGIKA HISTORY + TIMEZONE CABANG
        $user = Auth::user();
        $cabangId = $user->cabang_id; 
        $namaCabang = $user->cabang->nama ?? 'Pusat (Web)';

        StokHistory::create([
            'imei' => $this->imei,
            'status' => $this->stokId ? 'Update Data' : 'Stok Masuk',
            'cabang_id' => $cabangId, 
            'keterangan' => $this->stokId 
                ? "Data unit diperbarui oleh {$user->nama_lengkap} ($namaCabang)." 
                : "Stok baru masuk di $namaCabang.",
            'user_id' => $user->id,
        ]);

        $this->dispatch('close-modal');
        $this->dispatch('swal', [
            'title' => 'Berhasil!',
            'text' => 'Data stok unit berhasil disimpan.',
            'icon' => 'success'
        ]);
        
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
        $this->dispatch('swal', ['title' => 'Dihapus!', 'text' => 'Unit berhasil dihapus.', 'icon' => 'success']);
    }

    // --- LOGIKA BARU: BUKA MODAL KELUAR STOK ---
    public function openKeluarStokModal()
    {
        if (empty($this->selectedStok)) {
            $this->dispatch('swal', [['title' => 'Oops!', 'text' => 'Pilih minimal satu stok unit untuk dikeluarkan.', 'icon' => 'warning']]);
            return;
        }
        
        $this->kategoriKeluar = ''; // Reset pilihan kategori
        $this->resetErrorBag();
        $this->dispatch('open-keluar-modal');
    }

    // --- LOGIKA BARU: PROSES SIMPAN STOK KELUAR ---
    public function storeKeluarStok()
    {
        $this->validate([
            'kategoriKeluar' => 'required'
        ], [
            'kategoriKeluar.required' => 'Kategori keluar barang dibutuhkan'
        ]);

        $user = Auth::user();
        $cabangId = $user->cabang_id;
        $count = 0;

        // Loop semua ID yang dipilih checkbox
        foreach ($this->selectedStok as $id) {
            $stok = Stok::find($id);

            if ($stok) {
                // 1. Simpan ke History dulu sebelum dihapus
                StokHistory::create([
                    'imei' => $stok->imei,
                    'status' => 'Stok Keluar', // Status agar tampil beda di timeline
                    'cabang_id' => $cabangId,  // Penting untuk timezone
                    'keterangan' => "Unit keluar dengan keterangan: {$this->kategoriKeluar}. (Oleh: {$user->nama_lengkap})",
                    'user_id' => $user->id,
                ]);

                // 2. Hapus dari database Stok Aktif
                $stok->delete();
                $count++;
            }
        }

        // Reset semua pilihan
        $this->selectedStok = [];
        $this->selectAll = false;
        $this->kategoriKeluar = '';

        $this->dispatch('close-keluar-modal');
        $this->dispatch('swal', [
            'title' => 'Berhasil!', 
            'text' => "$count unit berhasil dikeluarkan dari stok.", 
            'icon' => 'success'
        ]);
    }

    public function render()
    {
        $stoks = Stok::with(['merk', 'tipe'])
            ->where('imei', 'like', '%' . $this->search . '%')
            ->orWhereHas('merk', fn($q) => $q->where('nama', 'like', '%'.$this->search.'%'))
            ->orWhereHas('tipe', fn($q) => $q->where('nama', 'like', '%'.$this->search.'%'))
            ->latest()
            ->paginate(10);

        $merks = Merk::orderBy('nama', 'asc')->get();

        // Ambil data detail item yang dipilih untuk ditampilkan di Modal Keluar Stok
        $selectedItems = [];
        if (!empty($this->selectedStok)) {
            $selectedItems = Stok::with(['merk', 'tipe'])->whereIn('id', $this->selectedStok)->get();
        }

        return view('livewire.stok.stok-index', [
            'stoks' => $stoks,
            'merks' => $merks,
            'selectedItems' => $selectedItems
        ])->title('Manajemen Stok');
    }
}