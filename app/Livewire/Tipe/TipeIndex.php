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

    #[Rule('required|in:imei,non_imei,jasa')]
    public $jenis = 'imei';

    public $ram_storage = []; 
    public $variasi_manual = ''; 

    public $ramOptions = [
        '1/8', '1/16', '1/32', '2/16', '2/32', '2/64', '3/32', '3/64', '3/128', '3/256',
        '4/32', '4/64', '4/128', '4/256', '4/512', '6/64', '6/128', '6/256', '6/512',
        '8/64', '8/128', '8/256', '8/512', '8/1024', '12/128', '12/256', '12/512', '12/1024',
        '16/128', '16/256', '16/512', '16/1024', '18/128', '18/256', '18/512', '18/1024',
        '24/512', '24/1024', '8', '16', '32', '64', '128', '256', '512', '2048'
    ];

    public function updatedJenis()
    {
        // 1. Reset variasi
        $this->ram_storage = [];
        $this->variasi_manual = '';
        $this->dispatch('reset-select');
        
        // 2. Reset merk_id karena daftar merk berubah
        // Jika merk yg dipilih sebelumnya TIDAK mendukung jenis baru, reset.
        if ($this->merk_id) {
            $merk = Merk::find($this->merk_id);
            if ($merk && !in_array($this->jenis, $merk->kategori ?? [])) {
                $this->merk_id = '';
            }
        }
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

        switch ($this->jenis) {
            case 'imei':
                $this->validate(['ram_storage' => 'required|array|min:1']);
                $final_variasi = $this->ram_storage;
                break;
            default:
                $this->validate(['variasi_manual' => 'required|string|min:1']);
                $pecah = explode(',', $this->variasi_manual);
                $final_variasi = array_map('trim', $pecah);
                break;
        }

        Tipe::updateOrCreate(['id' => $this->tipeId], [
            'merk_id' => $this->merk_id,
            'nama' => $this->nama,
            'jenis' => $this->jenis,
            'ram_storage' => $final_variasi
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

        switch ($this->jenis) {
            case 'imei':
                $this->ram_storage = $dataVarian;
                $this->dispatch('set-select-values', values: $this->ram_storage);
                break;
            default:
                $this->variasi_manual = implode(', ', $dataVarian);
                break;
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
            ->where('nama', 'like', "%{$this->search}%")
            ->orWhereHas('merk', function($q) {
                $q->where('nama', 'like', "%{$this->search}%");
            })
            ->latest()
            ->paginate(10);

        // LOGIKA FILTER MERK BERDASARKAN KATEGORI
        // Kita gunakan whereJsonContains karena kolom kategori di database adalah JSON
        $merks = Merk::query()
            ->whereJsonContains('kategori', $this->jenis)
            ->orderBy('nama', 'asc')
            ->get();

        return view('livewire.tipe.tipe-index', [
            'tipes' => $tipes,
            'merks' => $merks // Ini sekarang berisi merk yang sudah difilter
        ]);
    }
}