<?php

namespace App\Livewire\Leader;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\Cabang;

class PenjualanCabang extends Component
{
    use WithPagination;

    public $cabang_id;
    public $nama_cabang;
    public $search = '';
    public $filterDate = '';

    public function mount()
    {
        $user = Auth::user();
        
        // Pastikan user adalah Leader
        if ($user->role !== 'leader') {
            abort(403, 'Akses Ditolak. Halaman ini khusus Leader.');
        }

        $this->cabang_id = $user->cabang_id;
        
        // Ambil nama cabang
        $cabang = Cabang::find($this->cabang_id);
        $this->nama_cabang = $cabang ? $cabang->nama_cabang : 'Cabang Unknown';
    }

    public function render()
    {
        // ======================================================
        // SIMULASI DATA TRANSAKSI (Karena tabel transaksi belum ada)
        // ======================================================
        // Kita buat collection dummy, lalu kita filter manual
        
        $dummyTransactions = collect([]);

        // Generate 50 Transaksi Dummy
        for ($i = 1; $i <= 50; $i++) {
            $tgl = now()->subDays(rand(0, 30));
            $total = rand(100000, 5000000);
            
            $dummyTransactions->push([
                'id' => 'INV-' . date('Ymd') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'tanggal' => $tgl,
                'customer_name' => 'Customer ' . $i,
                'items' => rand(1, 5) . ' Item (HP/Acc)',
                'sales_name' => 'Sales Team',
                'total_bayar' => $total,
                'payment_method' => rand(0, 1) ? 'Cash' : 'Transfer',
                'status' => 'Lunas'
            ]);
        }

        // Filter Search
        $filtered = $dummyTransactions->filter(function ($item) {
            if ($this->search) {
                return stripos($item['id'], $this->search) !== false || 
                       stripos($item['customer_name'], $this->search) !== false;
            }
            return true;
        });

        // Filter Date
        $filtered = $filtered->filter(function ($item) {
            if ($this->filterDate) {
                return $item['tanggal']->format('Y-m-d') === $this->filterDate;
            }
            return true;
        });

        // Manual Pagination untuk Collection
        $perPage = 10;
        $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage();
        $currentItems = $filtered->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $paginatedItems = new \Illuminate\Pagination\LengthAwarePaginator($currentItems, count($filtered), $perPage);
        $paginatedItems->setPath(request()->url());

        return view('livewire.leader.penjualan-cabang', [
            'transactions' => $paginatedItems
        ])->layout('layouts.master', ['title' => 'Laporan Penjualan Cabang']);
    }
}