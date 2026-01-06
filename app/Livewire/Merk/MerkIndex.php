<?php

namespace App\Livewire\Merk;

use App\Models\Merk;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Rule;

class MerkIndex extends Component
{
    use WithPagination;

    // Supaya pagination menggunakan style Bootstrap
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $merkId;
    public $isEdit = false;

    // Validasi Langsung (Livewire 3)
    #[Rule('required|min:2|unique:merks,nama')]
    public $nama;

    #[Rule('nullable|string')]
    public $deskripsi;

    // Reset Form
    public function resetInputFields()
    {
        $this->nama = '';
        $this->deskripsi = '';
        $this->merkId = null;
        $this->isEdit = false;
        $this->resetErrorBag();
    }

    // Fungsi Create / Store
    public function store()
    {
        // Custom validasi untuk Edit agar unique ignore ID sendiri
        $rules = [
            'nama' => 'required|min:2|unique:merks,nama,' . $this->merkId,
            'deskripsi' => 'nullable|string'
        ];
        
        $this->validate($rules);

        Merk::updateOrCreate(['id' => $this->merkId], [
            'nama' => $this->nama,
            'deskripsi' => $this->deskripsi
        ]);

        // Kirim event browser untuk tutup modal dan notifikasi
        $this->dispatch('close-modal');
        $this->dispatch('alert', type: 'success', message: $this->merkId ? 'Merk berhasil diperbarui!' : 'Merk berhasil ditambahkan!');
        
        $this->resetInputFields();
    }

    // Fungsi Edit (Ambil Data)
    public function edit($id)
    {
        $merk = Merk::findOrFail($id);
        $this->merkId = $id;
        $this->nama = $merk->nama;
        $this->deskripsi = $merk->deskripsi;
        $this->isEdit = true;

        $this->dispatch('open-modal');
    }

    // Fungsi Delete
    public function delete($id)
    {
        Merk::find($id)->delete();
        $this->dispatch('alert', type: 'success', message: 'Merk berhasil dihapus!');
    }

    public function render()
    {
        // Search Logic
        $merks = Merk::query()
            ->where('nama', 'like', '%' . $this->search . '%')
            ->orWhere('deskripsi', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(10);

        return view('livewire.merk.merk-index', [
            'merks' => $merks
        ])->title('Manajemen Merk');
    }
}