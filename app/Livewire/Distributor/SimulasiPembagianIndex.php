<?php

namespace App\Livewire\Distributor;

use App\Models\Cabang;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.master')]
#[Title('Simulasi Distribusi')]
class SimulasiPembagianIndex extends Component
{
    public $totalBarang = 0;
    public $cabangs = [];
    public $alokasi = []; // Array untuk menyimpan input alokasi per cabang
    public $sisaBarang = 0;

    public function mount()
    {
        $user = Auth::user();
        if (!($user->role === 'distributor' || ($user->role === 'inventory_staff' && $user->distributor_id))) {
            abort(403);
        }

        // Load semua cabang
        $this->cabangs = Cabang::all();
        
        // Inisialisasi alokasi 0
        foreach($this->cabangs as $c) {
            $this->alokasi[$c->id] = 0;
        }
    }

    // Update real-time sisa barang saat input diubah
    public function updated($propertyName)
    {
        $totalDialokasikan = array_sum($this->alokasi);
        $this->sisaBarang = (int)$this->totalBarang - $totalDialokasikan;
    }

    public function bagiRata()
    {
        if($this->totalBarang > 0 && count($this->cabangs) > 0) {
            $perCabang = floor($this->totalBarang / count($this->cabangs));
            
            foreach($this->cabangs as $c) {
                $this->alokasi[$c->id] = $perCabang;
            }
            
            $this->updated('alokasi'); // Recalculate sisa
        }
    }

    public function resetSimulasi()
    {
        foreach($this->cabangs as $c) {
            $this->alokasi[$c->id] = 0;
        }
        $this->sisaBarang = $this->totalBarang;
    }

    public function simpanRencana()
    {
        // Disini logika untuk menyimpan rencana distribusi ke database (jika diperlukan)
        // Untuk simulasi, kita beri notifikasi saja
        session()->flash('success', 'Rencana distribusi telah disimpan sebagai Draft.');
    }

    public function render()
    {
        return view('livewire.distributor.simulasi-pembagian-index');
    }
}