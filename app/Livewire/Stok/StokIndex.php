<?php

namespace App\Livewire\Stok;

use App\Models\Stok;
use App\Models\Merk;
use App\Models\Tipe;
use App\Models\StokHistory; // Tambahkan Model History
use Illuminate\Support\Facades\Auth; // Tambahkan Facade Auth
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

    // --- FORM PROPERTIES ---
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

    // --- DATA LISTS (Untuk Dropdown Dinamis) ---
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

        // Margin 10%
        if($modal > 0) {
            $margin = $modal * 0.1; 
            $this->harga_jual = $modal + $margin;
        } else {
            $this->harga_jual = 0;
        }
    }

    public function resetInputFields()
    {
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
        $this->resetErrorBag();
    }

    public function store()
    {
        // 1. Validasi Input
        $this->validate([
            'merk_id' => 'required',
            'tipe_id' => 'required',
            'ram_storage' => 'required',
            'kondisi' => 'required',
            'imei' => 'required|unique:stoks,imei,' . $this->stokId,
            'harga_jual' => 'required|numeric',
        ]);

        // 2. Simpan / Update Data Stok Utama
        Stok::updateOrCreate(['id' => $this->stokId], [
            'merk_id' => $this->merk_id,
            'tipe_id' => $this->tipe_id,
            'ram_storage' => $this->ram_storage,
            'kondisi' => $this->kondisi,
            'imei' => $this->imei,
            'harga_modal' => $this->harga_modal ?: 0,
            'harga_jual' => $this->harga_jual,
        ]);

        // ==========================================
        // 3. REKAM JEJAK KE HISTORY (LACAK IMEI)
        // ==========================================
        StokHistory::create([
            'imei' => $this->imei,
            'status' => $this->stokId ? 'Update Data' : 'Stok Masuk',
            'keterangan' => $this->stokId 
                ? 'Data unit diperbarui oleh admin.' 
                : 'Stok baru ditambahkan ke sistem.',
            'user_id' => Auth::id() ?? 1, // Menggunakan ID user yang login
        ]);
        // ==========================================

        $this->dispatch('close-modal');
        $this->dispatch('swal', [
            'title' => 'Berhasil!',
            'text' => 'Data stok unit berhasil disimpan & tercatat di history.',
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
        // Opsional: Bisa tambah history 'Stok Dihapus' di sini sebelum delete jika mau
        Stok::find($id)->delete();
        $this->dispatch('swal', ['title' => 'Dihapus!', 'text' => 'Unit berhasil dihapus.', 'icon' => 'success']);
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

        return view('livewire.stok.stok-index', [
            'stoks' => $stoks,
            'merks' => $merks
        ])->title('Manajemen Stok');
    }
}