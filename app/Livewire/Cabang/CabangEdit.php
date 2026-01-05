<?php

namespace App\Livewire\Cabang;

use App\Models\Cabang;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.master')]
class CabangEdit extends Component
{
    public $cabangId, $kode_cabang, $nama_cabang, $lokasi, $timezone;

    public function mount($id)
    {
        $cabang = Cabang::findOrFail($id);
        $this->cabangId = $cabang->id;
        $this->kode_cabang = $cabang->kode_cabang;
        $this->nama_cabang = $cabang->nama_cabang;
        $this->lokasi = $cabang->lokasi;
        $this->timezone = $cabang->timezone ?? 'Asia/Jakarta';
    }

    public function update()
    {
        // Trim input lokasi
        $this->lokasi = trim($this->lokasi);

        $this->validate([
            'nama_cabang' => 'required|min:3',
            'lokasi'      => 'required|min:5|max:255', // Required untuk mencegah spasi/enter saja
            'timezone'    => 'required|in:Asia/Jakarta,Asia/Makassar,Asia/Jayapura',
        ], [
            'lokasi.required' => 'Alamat cabang tidak boleh kosong atau hanya berisi spasi/enter.'
        ]);

        $cabang = Cabang::find($this->cabangId);
        $cabang->update([
            'nama_cabang' => $this->nama_cabang,
            'lokasi'      => $this->lokasi,
            'timezone'    => $this->timezone,
        ]);

        session()->flash('info', 'Data cabang berhasil diperbarui.');
        return redirect()->route('cabang.index');
    }

    public function render()
    {
        return view('livewire.cabang.cabang-edit');
    }
}