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

    // Form Properties
    #[Rule('required|exists:merks,id')]
    public $merk_id;

    #[Rule('required|min:2')]
    public $nama;

    // Array untuk Multi-Select RAM
    #[Rule('required|array|min:1')]
    public $ram_storage = []; 

    // Opsi Hardcoded untuk Pilihan RAM (Bisa ditambah sesuai kebutuhan)
    public $ramOptions = [
        '2/32', '3/32', '3/64', '4/64', '4/128', 
        '6/128', '8/128', '8/256', '12/256', '12/512', 
        '16/512', '16/1TB', 'Laptop'
    ];

    public function resetInputFields()
    {
        $this->merk_id = '';
        $this->nama = '';
        $this->ram_storage = []; // Reset jadi array kosong
        $this->tipeId = null;
        $this->isEdit = false;
        $this->resetErrorBag();
        
        // Reset Library JS di frontend (TomSelect)
        $this->dispatch('reset-select');
    }

    public function store()
    {
        $this->validate();

        Tipe::updateOrCreate(['id' => $this->tipeId], [
            'merk_id' => $this->merk_id,
            'nama' => $this->nama,
            'ram_storage' => $this->ram_storage // Simpan array langsung (Model akan casting ke JSON)
        ]);

        $this->dispatch('close-modal');
        $this->dispatch('swal', [
            'title' => $this->tipeId ? 'Tipe Diperbarui!' : 'Tipe Ditambahkan!',
            'text' => 'Data tipe handphone berhasil disimpan.',
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
        // Pastikan formatnya array agar terbaca oleh Multi-Select
        $this->ram_storage = $tipe->ram_storage ?? []; 
        $this->isEdit = true;

        // Trigger event ke JS untuk isi nilai TomSelect
        $this->dispatch('set-select-values', values: $this->ram_storage);
    }

    public function delete($id)
    {
        Tipe::find($id)->delete();
        $this->dispatch('swal', [
            'title' => 'Dihapus!',
            'text' => 'Data tipe berhasil dihapus.',
            'icon' => 'success'
        ]);
    }

    public function render()
    {
        // Ambil data Tipe beserta relasi Merk-nya
        $tipes = Tipe::with('merk')
            ->where('nama', 'like', '%' . $this->search . '%')
            ->orWhereHas('merk', function($q) {
                $q->where('nama', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        // Ambil semua merk untuk Dropdown
        $merks = Merk::orderBy('nama', 'asc')->get();

        return view('livewire.tipe.tipe-index', [
            'tipes' => $tipes,
            'merks' => $merks
        ])->title('Manajemen Tipe');
    }
}