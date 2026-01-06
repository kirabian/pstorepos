<?php

namespace App\Livewire\Merk;

use App\Models\Merk;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Rule;

class MerkIndex extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $merkId;
    public $isEdit = false;

    #[Rule('required|min:2|unique:merks,nama')]
    public $nama;

    #[Rule('nullable|string')]
    public $deskripsi;

    public function resetInputFields()
    {
        $this->nama = '';
        $this->deskripsi = '';
        $this->merkId = null;
        $this->isEdit = false;
        $this->resetErrorBag();
    }

    public function store()
    {
        // Validasi Custom untuk Edit
        $rules = [
            'nama' => 'required|min:2|unique:merks,nama,' . $this->merkId,
            'deskripsi' => 'nullable|string'
        ];
        
        $this->validate($rules);

        Merk::updateOrCreate(['id' => $this->merkId], [
            'nama' => $this->nama,
            'deskripsi' => $this->deskripsi
        ]);

        $this->dispatch('close-modal');
        
        // PENTING: Kirim event SweetAlert
        $this->dispatch('swal', [
            'title' => $this->merkId ? 'Berhasil Diperbarui!' : 'Berhasil Ditambahkan!',
            'text' => 'Data merk telah disimpan ke sistem.',
            'icon' => 'success'
        ]);
        
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $merk = Merk::findOrFail($id);
        $this->merkId = $id;
        $this->nama = $merk->nama;
        $this->deskripsi = $merk->deskripsi;
        $this->isEdit = true;
        // Buka modal dari backend tidak perlu jika sudah pakai data-bs-toggle di tombol
    }

    public function delete($id)
    {
        Merk::find($id)->delete();
        
        // PENTING: Kirim event SweetAlert Hapus
        $this->dispatch('swal', [
            'title' => 'Dihapus!',
            'text' => 'Data merk berhasil dihapus.',
            'icon' => 'success'
        ]);
    }

    public function render()
    {
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