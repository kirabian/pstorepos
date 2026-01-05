<?php

namespace App\Livewire\Distributor;

use App\Models\Distributor;
use Livewire\Component;

class DistributorEdit extends Component
{
    public $distributorId;

    public $nama_distributor;

    public $kode_distributor;

    public $alamat;

    public function mount($id)
    {
        $data = Distributor::findOrFail($id);
        $this->distributorId = $id;
        $this->nama_distributor = $data->nama_distributor;
        $this->kode_distributor = $data->kode_distributor;
        $this->alamat = $data->alamat;
    }

    public function update()
    {
        $this->validate([
            'nama_distributor' => 'required|min:3',
            'kode_distributor' => 'required|unique:distributors,kode_distributor,'.$this->distributorId,
        ]);

        $data = Distributor::find($this->distributorId);
        $data->update([
            'nama_distributor' => $this->nama_distributor,
            'kode_distributor' => $this->kode_distributor,
            'alamat' => $this->alamat,
        ]);

        session()->flash('info', 'Distributor data updated.');

        return redirect()->route('distributor.index');
    }

    public function render()
    {
        /** @var \Illuminate\View\View $view */
        $view = view('livewire.distributor.distributor-edit');

        return $view->layout('layouts.master');
    }
}
