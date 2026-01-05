<?php

namespace App\Livewire\Cabang;

use App\Models\Cabang;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.master')]
class CabangCreate extends Component
{
    public $kode_cabang, $nama_cabang, $lokasi, $timezone = 'Asia/Jakarta';

    public function store()
    {
        // Trim input lokasi untuk mengecek apakah isinya hanya spasi/enter
        $this->lokasi = trim($this->lokasi);

        $this->validate([
            'kode_cabang' => 'required|unique:cabangs,kode_cabang|min:3',
            'nama_cabang' => 'required|min:3',
            'lokasi'      => 'required|min:5|max:255', // Diubah menjadi required untuk mencegah alamat kosong
            'timezone'    => 'required|in:Asia/Jakarta,Asia/Makassar,Asia/Jayapura',
        ], [
            'lokasi.required' => 'Alamat cabang tidak boleh kosong atau hanya berisi spasi/enter.'
        ]);

        Cabang::create([
            'kode_cabang' => strtoupper($this->kode_cabang),
            'nama_cabang' => $this->nama_cabang,
            'lokasi'      => $this->lokasi,
            'timezone'    => $this->timezone,
        ]);

        session()->flash('info', 'Cabang ' . $this->nama_cabang . ' berhasil didaftarkan.');
        return redirect()->route('cabang.index');
    }

    public function render()
    {
        return view('livewire.cabang.cabang-create');
    }
}