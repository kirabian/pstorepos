<?php

namespace App\Livewire\Tipe;

use App\Models\Tipe;
use App\Models\Merk;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Rule;

class TipeIndex extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $tipeId;
    public $isEdit = false;

    #[Rule('required|exists:merks,id')]
    public $merk_id;

    #[Rule('required|min:2')]
    public $nama;

    // Tambahkan Validasi Jenis
    #[Rule('required|in:imei,non_imei,jasa')]
    public $jenis = 'imei'; // Default select

    #[Rule('required|array|min:1')]
    public $ram_storage = []; 

    // Opsi Varian digabung (HP + Aksesoris)
    public $ramOptions = [
        // --- HP / TABLET (IMEI) ---
        '2/32', '3/32', '4/64', '4/128', '6/128', '8/128', '8/256', 
        '12/256', '12/512', '16/512', '1TB', 
        
        // --- AKSESORIS / PART (NON-IMEI / JASA) ---
        'Original', 'OEM', 'Grade A', 
        'Black', 'White', 'Blue', 'Red', // Warna (jika tipe casing)
        '1 Meter', '2 Meter', // Panjang kabel
        'LCD Only', 'Fullset', // Tipe part
        'Jasa Only' // Untuk murni jasa
    ];

    public function resetInputFields()
    {
        $this->merk_id = '';
        $this->nama = '';
        $this->jenis = 'imei'; // Reset ke default
        $this->ram_storage = [];
        $this->tipeId = null;
        $this->isEdit = false;
        $this->resetErrorBag();
        $this->dispatch('reset-select');
    }

    public function store()
    {
        $this->validate();

        Tipe::updateOrCreate(['id' => $this->tipeId], [
            'merk_id' => $this->merk_id,
            'nama' => $this->nama,
            'jenis' => $this->jenis, // Simpan jenis
            'ram_storage' => $this->ram_storage
        ]);

        $this->dispatch('close-modal');
        $this->dispatch('swal', [
            'title' => $this->tipeId ? 'Berhasil Diperbarui!' : 'Berhasil Ditambahkan!',
            'text' => 'Data tipe telah disimpan.',
            'icon' => 'success'
        ]);
        
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $tipe = Tipe::findOrFail($id);
        $this->tipeId = $id;
        $this->merk_id = $tipe->merk_id;
        $this->nama = $tipe->nama;
        $this->jenis = $tipe->jenis; // Load jenis
        $this->ram_storage = $tipe->ram_storage ?? []; 
        $this->isEdit = true;
        $this->dispatch('set-select-values', values: $this->ram_storage);
    }

    public function delete($id)
    {
        Tipe::find($id)->delete();
        $this->dispatch('swal', ['title' => 'Dihapus!', 'text' => 'Data berhasil dihapus.', 'icon' => 'success']);
    }

    public function render()
    {
        $tipes = Tipe::with('merk')
            ->where('nama', 'like', '%' . $this->search . '%')
            ->orWhereHas('merk', function($q) {
                $q->where('nama', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        $merks = Merk::orderBy('nama', 'asc')->get();

        return view('livewire.tipe.tipe-index', [
            'tipes' => $tipes,
            'merks' => $merks
        ])->title('Manajemen Tipe');
    }
}