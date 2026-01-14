<?php

namespace App\Livewire\Sales;

use App\Models\Penjualan;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;

class PenjualanHistory extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $filterStatus = ''; 
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

    // --- DOWNLOAD PDF (UNTUK SALES) ---
    public function downloadNota($id)
    {
        // Kita redirect ke route controller yang baru dibuat agar logicnya satu pintu
        return redirect()->route('nota.print', ['id' => $id]);
    }

    // --- KIRIM WA (DENGAN LINK PDF) ---
    public function kirimWa($id)
    {
        $penjualan = Penjualan::findOrFail($id);
        
        // 1. Format Nomor WA
        $wa = $penjualan->nomor_wa;
        $wa = preg_replace('/[^0-9]/', '', $wa); // Hapus karakter aneh
        if(substr($wa, 0, 1) == '0') {
            $wa = '62' . substr($wa, 1);
        }

        // 2. Generate Link PDF
        // route() akan membuat link otomatis: https://websiteanda.com/nota/print/1
        $linkPdf = route('nota.print', ['id' => $penjualan->id]);

        // 3. Susun Pesan
        $pesan = "Halo Kak *{$penjualan->nama_customer}*,\n\n";
        $pesan .= "Terima kasih telah berbelanja di *PSTORE {$penjualan->cabang->nama_cabang}*.\n\n";
        $pesan .= "Berikut detail pesanan Anda:\n";
        $pesan .= "🛍️ Unit: *{$penjualan->nama_produk}*\n";
        $pesan .= "📱 IMEI: {$penjualan->imei_terjual}\n";
        $pesan .= "💰 Total: Rp " . number_format($penjualan->harga_jual_real, 0, ',', '.') . "\n\n";
        $pesan .= "📄 *DOWNLOAD NOTA RESMI:*\n";
        $pesan .= $linkPdf . "\n\n";
        $pesan .= "Harap simpan nota ini sebagai bukti garansi/transaksi yang sah.\n";
        $pesan .= "Sehat selalu kak!";

        // 4. Encode & Kirim
        $encodedPesan = urlencode($pesan);
        $this->dispatch('open-wa', ['url' => "https://wa.me/{$wa}?text={$encodedPesan}"]);
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