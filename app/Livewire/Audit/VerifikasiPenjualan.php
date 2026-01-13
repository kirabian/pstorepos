<?php

namespace App\Livewire\Audit;

use App\Models\Penjualan;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class VerifikasiPenjualan extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $filterStatus = 'Pending'; // Default tampilkan yang pending
    public $search = '';

    // Action Methods
    public function approve($id)
    {
        $penjualan = Penjualan::find($id);
        if($penjualan) {
            $penjualan->update([
                'status_audit' => 'Approved',
                'audited_by' => Auth::id(),
                'audited_at' => now()
            ]);
            $this->dispatch('swal', ['icon' => 'success', 'title' => 'Approved', 'text' => 'Penjualan terverifikasi valid.']);
        }
    }

    public function reject($id)
    {
        $penjualan = Penjualan::find($id);
        if($penjualan) {
            $penjualan->update([
                'status_audit' => 'Rejected',
                'audited_by' => Auth::id(),
                'audited_at' => now()
            ]);
            
            // OPTIONAL: Jika direject, apakah stok dikembalikan? 
            // Untuk amannya, kita biarkan stok tetap keluar tapi status rejected 
            // nanti admin gudang yang revisi manual jika perlu, atau uncomment di bawah ini:
            /*
            if($penjualan->stok_id) {
                \App\Models\Stok::find($penjualan->stok_id)->increment('jumlah');
            }
            */

            $this->dispatch('swal', ['icon' => 'warning', 'title' => 'Rejected', 'text' => 'Penjualan ditolak.']);
        }
    }

    public function render()
    {
        $penjualans = Penjualan::with(['user', 'cabang', 'stok'])
            ->where('status_audit', $this->filterStatus)
            ->where(function($q) {
                $q->where('nama_customer', 'like', '%'.$this->search.'%')
                  ->orWhere('imei_terjual', 'like', '%'.$this->search.'%')
                  ->orWhereHas('user', fn($q2) => $q2->where('nama_lengkap', 'like', '%'.$this->search.'%'));
            })
            ->latest()
            ->paginate(10);

        return view('livewire.audit.verifikasi-penjualan', [
            'penjualans' => $penjualans
        ])->title('Verifikasi Penjualan');
    }
}