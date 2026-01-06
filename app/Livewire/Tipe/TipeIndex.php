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
    
    // Gunakan tema pagination bootstrap
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $tipeId;
    public $isEdit = false;

    // Form Properties
    #[Rule('required|exists:merks,id')]
    public $merk_id;

    #[Rule('required|min:2')]
    public $nama;

    // Array untuk Multi-Select RAM (Disimpan sebagai JSON di DB)
    #[Rule('required|array|min:1')]
    public $ram_storage = []; 

    /**
     * Opsi RAM & Storage Lengkap
     * Format: RAM/ROM (GB) atau Storage Only
     * Diurutkan dari spek terendah ke tertinggi sesuai screenshot
     */
    public $ramOptions = [
        // Entry Level / Old School
        '1/8', '1/16', '1/32', 
        '2/16', '2/32', '2/64', 
        '3/32', '3/64', '3/128', '3/256',

        // Mid Range Common
        '4/32', '4/64', '4/128', '4/256', '4/512',
        '6/64', '6/128', '6/256', '6/512',

        // High End / Flagship
        '8/64', '8/128', '8/256', '8/512', '8/1024',
        '12/128', '12/256', '12/512', '12/1024',

        // Monster Specs / Gaming Phone
        '16/128', '16/256', '16/512', '16/1024',
        '18/128', '18/256', '18/512', '18/1024',
        '24/512', '24/1024',

        // Storage Only (Tablet/iPad/Laptop Style)
        '8', '16', '32', '64', '128', '256', 
        '512', '1024', '2048'
    ];

    public function resetInputFields()
    {
        $this->merk_id = '';
        $this->nama = '';
        $this->ram_storage = []; // Reset jadi array kosong
        $this->tipeId = null;
        $this->isEdit = false;
        $this->resetErrorBag();
        
        // Reset Library JS di frontend (TomSelect) via Event
        $this->dispatch('reset-select');
    }

    public function store()
    {
        $this->validate();

        Tipe::updateOrCreate(['id' => $this->tipeId], [
            'merk_id' => $this->merk_id,
            'nama' => $this->nama,
            'ram_storage' => $this->ram_storage // Array otomatis di-cast ke JSON oleh Model
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
        // Jika null, jadikan array kosong
        $this->ram_storage = $tipe->ram_storage ?? []; 
        $this->isEdit = true;

        // Trigger event ke JS untuk mengisi nilai TomSelect yang terpilih
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
        // Menggunakan pencarian berdasarkan Nama Tipe atau Nama Merk
        $tipes = Tipe::with('merk')
            ->where('nama', 'like', '%' . $this->search . '%')
            ->orWhereHas('merk', function($q) {
                $q->where('nama', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        // Ambil semua merk untuk Dropdown, urutkan A-Z
        $merks = Merk::orderBy('nama', 'asc')->get();

        return view('livewire.tipe.tipe-index', [
            'tipes' => $tipes,
            'merks' => $merks
        ])->title('Manajemen Tipe');
    }
}