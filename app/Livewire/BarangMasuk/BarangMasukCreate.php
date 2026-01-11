<?php

namespace App\Livewire\BarangMasuk;

use App\Models\Stok;
use App\Models\StokHistory;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.master')]
#[Title('Input Barang Masuk')]
class BarangMasukCreate extends Component
{
    public $imei;
    public $nama_barang;
    public $kondisi = 'Baru';
    public $keterangan;

    protected $rules = [
        'imei' => 'required|unique:stoks,imei|min:10',
        'nama_barang' => 'required|string',
        'kondisi' => 'required|in:Baru,Bekas',
        'keterangan' => 'nullable|string',
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

    public function simpan()
    {
        $this->validate();
        $user = Auth::user();

        // Simpan ke Stok
        Stok::create([
            'imei' => $this->imei,
            'nama_barang' => $this->nama_barang,
            'kondisi' => $this->kondisi,
            'gudang_id' => $user->gudang_id ?? null,
            'cabang_id' => $user->cabang_id ?? null,
            'status' => 'ready',
            'harga_beli' => 0,
        ]);

        // Simpan ke History
        StokHistory::create([
            'user_id' => $user->id,
            'cabang_id' => $user->cabang_id ?? null,
            'imei' => $this->imei,
            'status' => 'Stok Masuk',
            'keterangan' => "Input Barang Masuk: " . ($this->keterangan ?? '-'),
        ]);

        $this->reset(['imei', 'nama_barang', 'kondisi', 'keterangan']);
        session()->flash('success', 'Barang berhasil ditambahkan ke stok gudang.');
    }

    public function render()
    {
        return view('livewire.barang-masuk.barang-masuk-create');
    }
}