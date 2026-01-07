<?php

namespace App\Livewire\User;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;

#[Layout('layouts.master')]
class UserIndex extends Component
{
    use WithPagination;

    public $search = '';

    protected $updatesQueryString = ['search'];

    /**
     * Listener Real-time untuk sinkronisasi data antar admin
     */
    #[On('echo:pstore-channel,inventory.updated')]
    public function refreshTable()
    {
        // Re-render otomatis saat ada broadcast event
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Fungsi Hapus Pengguna dengan proteksi diri sendiri
     */
    public function delete($id)
    {
        if ($id === auth()->id()) {
            session()->flash('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
            return;
        }

        User::destroy($id);
        session()->flash('info', 'Pengguna berhasil dihapus.');
        
        // Broadcast ke user lain agar tabel mereka terupdate
        broadcast(new \App\Events\InventoryUpdate('User telah dihapus oleh '.auth()->user()->nama_lengkap))->toOthers();
    }

    public function render()
    {
        // Eager Load relasi distributor, cabang, dan branches (multi-cabang audit)
        $users = User::with(['distributor', 'cabang', 'branches'])
            ->where(function($query) {
                $query->where('nama_lengkap', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                      ->orWhere('role', 'like', '%' . $this->search . '%')
                      ->orWhere('idlogin', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        return view('livewire.auth.user-index', [
            'users' => $users
        ]);
    }
}