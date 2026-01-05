<?php

namespace App\Livewire\Cabang;

use App\Models\Cabang;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

#[Lazy]
#[Layout('layouts.master')]
class CabangIndex extends Component
{
    use WithPagination;

    public $search;

    #[On('echo:pstore-channel,inventory.updated')]
    public function refreshCabang($event) {}

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        Cabang::destroy($id);
        session()->flash('info', 'Cabang berhasil dihapus.');
        broadcast(new \App\Events\InventoryUpdate('Cabang telah dihapus oleh '.auth()->user()->nama_lengkap))->toOthers();
    }

    public function placeholder()
    {
        return view('livewire.cabang.cabang-skeleton');
    }

public function render()
    {
        return view('livewire.cabang.cabang-index', [
            'cabangs' => Cabang::query()
                ->with(['users', 'regularStaff']) // Eager loading dua jenis relasi
                ->where('nama_cabang', 'like', '%'.$this->search.'%')
                ->orWhere('kode_cabang', 'like', '%'.$this->search.'%')
                ->latest()
                ->paginate(10),
        ]);
    }
}