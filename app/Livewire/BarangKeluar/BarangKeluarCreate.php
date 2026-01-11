<?php

namespace App\Livewire\BarangKeluar;

use App\Models\Stok;
use App\Models\StokHistory;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.master')]
#[Title('Input Barang Keluar')]
class BarangKeluarCreate extends Component
{
    public $imei;
    public $kategori = 'Penjualan';
    public $keterangan;
    public $barangDitemukan = null;

    public $opsiKategori = [
        'Penjualan',
        'Pindah Cabang',
        'Retur ke Distributor',
        'Barang Rusak / Musnah',
        'Giveaway'
    ];

    public function mount()
    {
        $user = Auth::user();

        // LOGIKA PENGECEKAN HAK AKSES
        // Boleh akses jika: Role 'gudang' ATAU (Role 'inventory_staff' DAN punya gudang_id)
        $isGudangMurni = $user->role === 'gudang';
        $isStaffGudang = ($user->role === 'inventory_staff' && $user->gudang_id && !$user->distributor_id);

        if (!$isGudangMurni && !$isStaffGudang) {
            abort(403, 'AKSES DITOLAK. Halaman ini hanya untuk Staff Gudang Fisik.');
        }
    }

    public function updatedImei($value)
    {
        $user = Auth::user();
        
        $stok = Stok::where('imei', $value)
            ->when($user->gudang_id, function($q) use ($user) {
                return $q->where('gudang_id', $user->gudang_id);
            })
            ->first();

        $this->barangDitemukan = $stok;
    }

    public function prosesKeluar()
    {
        $this->validate([
            'imei' => 'required|exists:stoks,imei',
            'kategori' => 'required',
            'keterangan' => 'nullable|string',
        ]);

        if (!$this->barangDitemukan) {
            $this->addError('imei', 'Barang tidak ditemukan di gudang Anda.');
            return;
        }

        $user = Auth::user();

        StokHistory::create([
            'user_id' => $user->id,
            'cabang_id' => $user->cabang_id ?? null,
            'imei' => $this->imei,
            'status' => 'Stok Keluar',
            'keterangan' => $this->kategori . " - " . ($this->keterangan ?? ''),
        ]);

        $this->barangDitemukan->delete();

        $this->reset(['imei', 'keterangan', 'barangDitemukan']);
        session()->flash('success', 'Barang berhasil dikeluarkan dari stok.');
    }

    public function render()
    {
        return view('livewire.barang-keluar.barang-keluar-create');
    }
}