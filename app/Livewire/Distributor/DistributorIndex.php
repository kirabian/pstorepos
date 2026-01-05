<?php

namespace App\Livewire\Distributor;

use App\Models\Distributor;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.master')]
class DistributorIndex extends Component
{
    use WithPagination;
    public $search = '';

    public function delete($id)
    {
        Distributor::destroy($id);
        session()->flash('info', 'Distributor deleted successfully.');
    }

    public function render()
    {
        return view('livewire.distributor.distributor-index', [
            'distributors' => Distributor::where('nama_distributor', 'like', '%'.$this->search.'%')
                ->latest()->paginate(10)
        ]);
    }
}