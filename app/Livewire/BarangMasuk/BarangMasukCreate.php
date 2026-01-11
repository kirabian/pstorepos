<?php

namespace App\Livewire\BarangMasuk;

use App\Models\Stok;
use App\Models\StokHistory;
use App\Models\Tipe; // Asumsi ada model Tipe HP
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.master')]
#[Title('Input Barang Masuk')]
class BarangMasukCreate extends Component
{
    public $imei;
    public $nama_barang; // Bisa diganti dropdown tipe_id jika pakai relasi
    public $kondisi = 'Baru';
    public $keterangan;

    protected $rules = [
        'imei' => 'required|unique:stoks,imei|min:10',
        'nama_barang' => 'required|string',
        'kondisi' => 'required|in:Baru,Bekas',
        'keterangan' => 'nullable|string',
    ];

    public function simpan()
    {
        $this->validate();
        $user = Auth::user();

        // 1. Simpan ke Tabel Stok (Inventory Aktif)
        Stok::create([
            'imei' => $this->imei,
            'nama_barang' => $this->nama_barang,
            'kondisi' => $this->kondisi,
            'gudang_id' => $user->gudang_id ?? null, // Otomatis masuk ke gudang user
            'cabang_id' => $user->cabang_id ?? null,  // Fallback jika user cabang
            'status' => 'ready', // Status default
            'harga_beli' => 0, // Bisa ditambahkan field harga jika perlu
        ]);

        // 2. Simpan ke History (Pencatatan Log)
        StokHistory::create([
            'user_id' => $user->id,
            'cabang_id' => $user->cabang_id ?? null, // Untuk tracking lokasi
            'imei' => $this->imei,
            'status' => 'Stok Masuk',
            'keterangan' => "Input Barang Masuk: " . ($this->keterangan ?? '-'),
        ]);

        // Reset form & Beri notifikasi
        $this->reset(['imei', 'nama_barang', 'kondisi', 'keterangan']);
        session()->flash('success', 'Barang berhasil ditambahkan ke stok gudang.');
        
        // Redirect opsional atau tetap di halaman ini untuk input massal
        // return redirect()->route('barang-masuk.index'); 
    }

    public function render()
    {
        return view('livewire.barang-masuk.barang-masuk-create');
    }
}