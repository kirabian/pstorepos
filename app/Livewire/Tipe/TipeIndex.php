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

    // --- FORM PROPERTIES ---
    #[Rule('required|exists:merks,id')]
    public $merk_id;

    #[Rule('required|min:2')]
    public $nama;

    #[Rule('required|in:imei,non_imei,jasa')]
    public $jenis = 'imei'; // Default

    // Property HP (Array)
    public $ram_storage = []; 

    // Property Aksesoris/Jasa (String Manual)
    public $variasi_manual = ''; 

    // Opsi RAM Predefined
    public $ramOptions = [
        '2/32', '3/32', '4/64', '4/128', '6/128', '8/128', '8/256', 
        '12/256', '12/512', '16/512', '1TB'
    ];

    // PENTING: Reset input saat jenis berubah
    public function updatedJenis()
    {
        $this->ram_storage = [];
        $this->variasi_manual = '';
        $this->dispatch('reset-select'); // Reset JS TomSelect
    }

    public function resetInputFields()
    {
        $this->merk_id = '';
        $this->nama = '';
        $this->jenis = 'imei';
        $this->ram_storage = [];
        $this->variasi_manual = '';
        $this->tipeId = null;
        $this->isEdit = false;
        $this->resetErrorBag();
        $this->dispatch('reset-select');
    }

    public function store()
    {
        $this->validate([
            'merk_id' => 'required',
            'nama' => 'required|min:2',
            'jenis' => 'required',
        ]);

        $final_variasi = [];

        if ($this->jenis == 'imei') {
            // Validasi HP: Wajib Array
            $this->validate(['ram_storage' => 'required|array|min:1']);
            $final_variasi = $this->ram_storage;
        } else {
            // Validasi Lainnya: Wajib Text
            $this->validate(['variasi_manual' => 'required|string|min:1']);
            // Pecah string koma jadi array
            $pecah = explode(',', $this->variasi_manual);
            $final_variasi = array_map('trim', $pecah);
        }

        Tipe::updateOrCreate(['id' => $this->tipeId], [
            'merk_id' => $this->merk_id,
            'nama' => $this->nama,
            'jenis' => $this->jenis,
            'ram_storage' => $final_variasi // Simpan JSON
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
        $this->jenis = $tipe->jenis;
        
        $dataVarian = $tipe->ram_storage ?? [];

        if ($this->jenis == 'imei') {
            $this->ram_storage = $dataVarian;
            $this->dispatch('set-select-values', values: $this->ram_storage);
        } else {
            // Gabung array jadi string untuk input text
            $this->variasi_manual = implode(', ', $dataVarian);
        }

        $this->isEdit = true;
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