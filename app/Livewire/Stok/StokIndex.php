<?php

namespace App\Livewire\Stok;

use App\Models\Stok;
use App\Models\Merk;
use App\Models\Tipe;
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
    public $tipeOptions = []; // Menampung Tipe berdasarkan Merk yg dipilih
    public $ramOptions = [];  // Menampung RAM berdasarkan Tipe yg dipilih

    // --- LOGIKA DEPENDENT DROPDOWN ---
    
    // 1. Saat Merk dipilih, cari Tipe-nya
    public function updatedMerkId($value)
    {
        $this->tipeOptions = Tipe::where('merk_id', $value)->get();
        $this->tipe_id = ''; // Reset Tipe
        $this->ram_storage = ''; // Reset RAM
        $this->ramOptions = []; 
    }

    // 2. Saat Tipe dipilih, ambil JSON RAM dari database
    public function updatedTipeId($value)
    {
        if(!empty($value)) {
            $tipe = Tipe::find($value);
            // Ambil kolom ram_storage (JSON) dari tabel Tipes
            $this->ramOptions = $tipe->ram_storage ?? []; 
        } else {
            $this->ramOptions = [];
        }
        $this->ram_storage = ''; // Reset pilihan RAM
    }

    // --- LOGIKA RUMUS OTOMATIS ---
    // Setiap kali harga modal diketik, hitung harga jual
    public function updatedHargaModal($value)
    {
        // Pastikan value angka
        $modal = (int) $value;

        // RUMUS: Misal Margin 10% (Ganti 0.1 dengan persentase yg diinginkan)
        // Atau bisa ganti rumus flat: $modal + 200000;
        if($modal > 0) {
            $margin = $modal * 0.1; // 10% Profit
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
        // Custom validasi untuk edit (ignore unique IMEI milik sendiri)
        $this->validate([
            'merk_id' => 'required',
            'tipe_id' => 'required',
            'ram_storage' => 'required',
            'kondisi' => 'required',
            'imei' => 'required|unique:stoks,imei,' . $this->stokId,
            'harga_jual' => 'required|numeric',
        ]);

        Stok::updateOrCreate(['id' => $this->stokId], [
            'merk_id' => $this->merk_id,
            'tipe_id' => $this->tipe_id,
            'ram_storage' => $this->ram_storage,
            'kondisi' => $this->kondisi,
            'imei' => $this->imei,
            'harga_modal' => $this->harga_modal ?: 0,
            'harga_jual' => $this->harga_jual,
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
        
        // Load Tipe Options manual karena ini mode edit
        $this->tipeOptions = Tipe::where('merk_id', $stok->merk_id)->get();
        $this->tipe_id = $stok->tipe_id;

        // Load RAM Options manual
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

    public function render()
    {
        // Ambil data stok dengan relasinya
        $stoks = Stok::with(['merk', 'tipe'])
            ->where('imei', 'like', '%' . $this->search . '%')
            ->orWhereHas('merk', fn($q) => $q->where('nama', 'like', '%'.$this->search.'%'))
            ->orWhereHas('tipe', fn($q) => $q->where('nama', 'like', '%'.$this->search.'%'))
            ->latest()
            ->paginate(10);

        // Dropdown Merk (Level 1)
        $merks = Merk::orderBy('nama', 'asc')->get();

        return view('livewire.stok.stok-index', [
            'stoks' => $stoks,
            'merks' => $merks
        ])->title('Manajemen Stok');
    }
}