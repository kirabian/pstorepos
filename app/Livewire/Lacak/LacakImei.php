<?php

namespace App\Livewire\Lacak;

use App\Models\Stok;
use App\Models\StokHistory;
use Livewire\Component;
use Livewire\Attributes\Title;

class LacakImei extends Component
{
    #[Title('Lacak IMEI')]
    
    public $searchImei = '';
    public $riwayat = [];
    public $stokDetail = null;
    public $notFound = false;

    public function lacak()
    {
        $this->validate([
            'searchImei' => 'required|min:5'
        ]);

        // 1. Cari Detail Stok Saat Ini
        $this->stokDetail = Stok::with(['merk', 'tipe'])
                            ->where('imei', $this->searchImei)
                            ->first();

        // 2. Cari Riwayat Perjalanan (Log)
        $this->riwayat = StokHistory::with('user')
                            ->where('imei', $this->searchImei)
                            ->latest()
                            ->get();

        // 3. Logika Not Found
        if (!$this->stokDetail && $this->riwayat->isEmpty()) {
            $this->notFound = true;
        } else {
            $this->notFound = false;
        }
    }

    public function render()
    {
        return view('livewire.lacak.lacak-imei');
    }
}