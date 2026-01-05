<?php

namespace App\Livewire\Distributor;

use App\Models\Distributor;
use Livewire\Component;

class DistributorCreate extends Component
{
    public $nama_distributor, $kode_distributor, $alamat;

    public function store()
    {
        $this->validate([
            'nama_distributor' => 'required|min:3',
            'kode_distributor' => 'required|unique:distributors,kode_distributor',
            'alamat' => 'nullable'
        ]);

        Distributor::create([
            'nama_distributor' => $this->nama_distributor,
            'kode_distributor' => $this->kode_distributor,
            'alamat' => $this->alamat,
        ]);

        session()->flash('info', 'New distributor added.');
        return redirect()->route('distributor.index');
    }

    public function render()
    {
        return view('livewire.distributor.distributor-create');
    }
}