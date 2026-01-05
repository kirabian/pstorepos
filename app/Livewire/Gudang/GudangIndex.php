<?php

namespace App\Livewire\Gudang;

use App\Models\Gudang;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

#[Lazy]
#[Layout('layouts.master')]
class GudangIndex extends Component
{
    use WithPagination;

    public $search;

    // Listener untuk refresh otomatis jika ada update dari user lain
    #[On('echo:pstore-channel,inventory.updated')]
    public function refreshGudang($event) {}

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        Gudang::destroy($id);
        session()->flash('info', 'Gudang berhasil dihapus.');
        
        // Memicu sinyal real-time
        broadcast(new \App\Events\InventoryUpdate('Gudang telah dihapus oleh '.auth()->user()->nama_lengkap))->toOthers();
    }

    public function placeholder()
    {
        // Anda bisa membuat file skeleton sendiri atau menggunakan yang sudah ada
        return view('livewire.cabang.cabang-skeleton');
    }

    public function render()
    {
        return view('livewire.gudang.gudang-index', [
            'gudangs' => Gudang::query()
                ->where('nama_gudang', 'like', '%'.$this->search.'%')
                ->orWhere('kode_gudang', 'like', '%'.$this->search.'%')
                ->latest()
                ->paginate(10),
        ]);
    }
}