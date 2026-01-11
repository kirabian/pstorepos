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
    public $kategori = 'Penjualan'; // Default
    public $keterangan;
    public $barangDitemukan = null; // Untuk preview barang sebelum keluar

    // Opsi Alasan Keluar
    public $opsiKategori = [
        'Penjualan',
        'Pindah Cabang',
        'Retur ke Distributor',
        'Barang Rusak / Musnah',
        'Giveaway'
    ];

    // Real-time check saat IMEI diketik/scan
    public function updatedImei($value)
    {
        $user = Auth::user();
        
        // Cari barang di stok gudang user ini saja
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

        // 1. Catat History Keluar
        StokHistory::create([
            'user_id' => $user->id,
            'cabang_id' => $user->cabang_id ?? null,
            'imei' => $this->imei,
            'status' => 'Stok Keluar',
            'keterangan' => $this->kategori . " - " . ($this->keterangan ?? ''),
        ]);

        // 2. Hapus dari Tabel Stok (Hard Delete karena barang keluar fisik)
        // Jika sistem Anda pakai SoftDelete, gunakan ->delete() biasa.
        // Jika ingin memindahkan status jadi 'sold' tanpa hapus, ubah logika di sini.
        // Di sini saya pakai logika HAPUS agar sesuai dashboard history barang keluar.
        $this->barangDitemukan->delete();

        // Reset
        $this->reset(['imei', 'keterangan', 'barangDitemukan']);
        session()->flash('success', 'Barang berhasil dikeluarkan dari stok.');
    }

    public function render()
    {
        return view('livewire.barang-keluar.barang-keluar-create');
    }
}