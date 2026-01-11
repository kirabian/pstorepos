<?php

namespace App\Livewire\Distributor;

use App\Models\Cabang;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.master')]
#[Title('Monitoring Omset Cabang')]
class OmsetCabangIndex extends Component
{
    public function mount()
    {
        $user = Auth::user();
        if (!($user->role === 'distributor' || ($user->role === 'inventory_staff' && $user->distributor_id))) {
            abort(403);
        }
    }

    public function render()
    {
        // Simulasi Data Omset (Karena belum ada tabel Transaksi di prompt sebelumnya)
        // Di real case, ini query sum('total_harga') from transactions where cabang_id = ...
        
        $cabangs = Cabang::all()->map(function($cabang) {
            // Dummy logic untuk angka acak agar terlihat hidup
            $cabang->omset_hari_ini = rand(1000000, 15000000); 
            $cabang->omset_bulan_ini = rand(50000000, 300000000);
            $cabang->transaksi_count = rand(5, 50);
            return $cabang;
        })->sortByDesc('omset_bulan_ini');

        $totalOmsetNasional = $cabangs->sum('omset_bulan_ini');

        return view('livewire.distributor.omset-cabang-index', [
            'cabangs' => $cabangs,
            'totalOmsetNasional' => $totalOmsetNasional
        ]);
    }
}