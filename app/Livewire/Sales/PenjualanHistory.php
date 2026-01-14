<?php

namespace App\Livewire\Sales;

use App\Models\Penjualan;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf; // Import DOMPDF

class PenjualanHistory extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    // Filter
    public $search = '';
    public $filterStatus = ''; // Kosong = Semua
    public $bulan = '';
    public $tahun = '';

    public function mount()
    {
        $this->bulan = date('m');
        $this->tahun = date('Y');
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    // --- FITUR BARU: DOWNLOAD PDF ---
    public function downloadNota($id)
    {
        $penjualan = Penjualan::with(['user', 'cabang', 'stok'])->findOrFail($id);
        
        // Pastikan hanya sales ybs atau admin yg bisa download
        if(Auth::user()->role == 'sales' && $penjualan->user_id != Auth::id()) {
            return abort(403);
        }

        $pdf = Pdf::loadView('pdf.nota_penjualan', ['penjualan' => $penjualan])
                  ->setPaper('a5', 'portrait'); // Ukuran A5 biar ringkas

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'Nota_PStore_TRX'.$penjualan->id.'.pdf');
    }

    // --- FITUR BARU: KIRIM WA ---
    public function kirimWa($id)
    {
        $penjualan = Penjualan::findOrFail($id);
        
        // Format Nomor WA (Ganti 08 jadi 628)
        $wa = $penjualan->nomor_wa;
        if(substr($wa, 0, 1) == '0') {
            $wa = '62' . substr($wa, 1);
        }

        // Pesan WA
        $pesan = "Halo Kak *{$penjualan->nama_customer}*,\n\n";
        $pesan .= "Terima kasih telah berbelanja di *PSTORE {$penjualan->cabang->nama_cabang}*.\n\n";
        $pesan .= "Berikut detail pesanan Anda:\n";
        $pesan .= "Unit: {$penjualan->nama_produk}\n";
        $pesan .= "IMEI: {$penjualan->imei_terjual}\n";
        $pesan .= "Total: Rp " . number_format($penjualan->harga_jual_real, 0, ',', '.') . "\n\n";
        $pesan .= "Nota digital Anda dapat diunduh melalui link berikut (Jika tersedia) atau minta sales kami mengirimkannya.\n\n";
        $pesan .= "Sehat selalu kak!";

        $encodedPesan = urlencode($pesan);
        
        // Redirect ke WA Web / API
        return redirect()->away("https://wa.me/{$wa}?text={$encodedPesan}");
    }

    public function render()
    {
        $user = Auth::user();

        $query = Penjualan::with(['stok', 'auditor'])
            ->where('user_id', $user->id);

        if ($this->search) {
            $query->where(function($q) {
                $q->where('nama_customer', 'like', '%' . $this->search . '%')
                  ->orWhere('imei_terjual', 'like', '%' . $this->search . '%')
                  ->orWhere('nama_produk', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterStatus) {
            $query->where('status_audit', $this->filterStatus);
        }

        if ($this->bulan) {
            $query->whereMonth('created_at', $this->bulan);
        }
        if ($this->tahun) {
            $query->whereYear('created_at', $this->tahun);
        }

        $penjualans = $query->latest()->paginate(10);

        // Hitung Summary
        $totalOmsetBulanIni = Penjualan::where('user_id', $user->id)
            ->whereMonth('created_at', $this->bulan)
            ->whereYear('created_at', $this->tahun)
            ->where('status_audit', '!=', 'Rejected')
            ->sum('harga_jual_real');

        $totalUnitBulanIni = Penjualan::where('user_id', $user->id)
            ->whereMonth('created_at', $this->bulan)
            ->whereYear('created_at', $this->tahun)
            ->where('status_audit', '!=', 'Rejected')
            ->count();

        return view('livewire.sales.penjualan-history', [
            'penjualans' => $penjualans,
            'omset' => $totalOmsetBulanIni,
            'total_unit' => $totalUnitBulanIni
        ])->title('Riwayat Penjualan Saya');
    }
}