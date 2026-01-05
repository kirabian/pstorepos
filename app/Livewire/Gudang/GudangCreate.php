<?php

namespace App\Livewire\Gudang;

use App\Models\Gudang;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.master')]
class GudangCreate extends Component
{
    public $kode_gudang, $nama_gudang, $alamat_gudang;

    public function store()
    {
        $this->validate([
            'kode_gudang' => 'required|unique:gudangs,kode_gudang|min:3',
            'nama_gudang' => 'required|min:3',
            'alamat_gudang' => 'required|max:255',
        ]);

        Gudang::create([
            'kode_gudang' => strtoupper($this->kode_gudang),
            'nama_gudang' => $this->nama_gudang,
            'alamat_gudang' => $this->alamat_gudang,
        ]);

        session()->flash('info', 'Gudang ' . $this->nama_gudang . ' berhasil didaftarkan.');
        return redirect()->route('gudang.index');
    }

    public function render()
    {
        return view('livewire.gudang.gudang-create');
    }
}