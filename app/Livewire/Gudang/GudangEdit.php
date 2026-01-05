<?php

namespace App\Livewire\Gudang;

use App\Models\Gudang;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.master')]
class GudangEdit extends Component
{
    public $gudangId, $kode_gudang, $nama_gudang, $alamat_gudang;

    public function mount($id)
    {
        $gudang = Gudang::findOrFail($id);
        $this->gudangId = $gudang->id;
        $this->kode_gudang = $gudang->kode_gudang;
        $this->nama_gudang = $gudang->nama_gudang;
        $this->alamat_gudang = $gudang->alamat_gudang;
    }

    public function update()
    {
        $this->validate([
            'nama_gudang' => 'required|min:3',
            'alamat_gudang' => 'required|max:255',
        ]);

        $gudang = Gudang::find($this->gudangId);
        $gudang->update([
            'nama_gudang' => $this->nama_gudang,
            'alamat_gudang' => $this->alamat_gudang,
        ]);

        session()->flash('info', 'Data gudang berhasil diperbarui.');
        return redirect()->route('gudang.index');
    }

    public function render()
    {
        return view('livewire.gudang.gudang-edit');
    }
}