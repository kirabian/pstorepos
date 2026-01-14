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

    // --- FIXING: DOWNLOAD PDF DENGAN ERROR HANDLING ---
    public function downloadNota($id)
    {
        try {
            $penjualan = Penjualan::with(['user', 'cabang', 'stok'])->findOrFail($id);
            
            // Validasi Hak Akses (Opsional)
            if(Auth::user()->role == 'sales' && $penjualan->user_id != Auth::id()) {
                $this->dispatch('swal', ['icon' => 'error', 'title' => 'Akses Ditolak', 'text' => 'Anda tidak berhak mengunduh nota ini.']);
                return;
            }

            // Generate PDF
            $pdf = Pdf::loadView('pdf.nota_penjualan', ['penjualan' => $penjualan])
                      ->setPaper('a5', 'portrait');

            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->output();
            }, 'Nota_TRX-'.$penjualan->id.'.pdf');

        } catch (Exception $e) {
            // Jika error, tampilkan notifikasi biar ga loading terus
            $this->dispatch('swal', [
                'icon' => 'error', 
                'title' => 'Gagal Download PDF', 
                'text' => 'Terjadi kesalahan saat membuat PDF: ' . $e->getMessage()
            ]);
        }
    }

    // --- FITUR KIRIM WA ---
    public function kirimWa($id)
    {
        $penjualan = Penjualan::findOrFail($id);
        
        $wa = $penjualan->nomor_wa;
        // Bersihkan nomor (hapus spasi, strip)
        $wa = preg_replace('/[^0-9]/', '', $wa);
        
        // Ubah 08 jadi 628
        if(substr($wa, 0, 1) == '0') {
            $wa = '62' . substr($wa, 1);
        }

        $pesan = "Halo Kak *{$penjualan->nama_customer}*,\n\n";
        $pesan .= "Terima kasih telah berbelanja di *PSTORE {$penjualan->cabang->nama_cabang}*.\n\n";
        $pesan .= "Berikut detail pesanan Anda:\n";
        $pesan .= "Unit: {$penjualan->nama_produk}\n";
        $pesan .= "IMEI: {$penjualan->imei_terjual}\n";
        $pesan .= "Total: Rp " . number_format($penjualan->harga_jual_real, 0, ',', '.') . "\n\n";
        $pesan .= "Simpan pesan ini sebagai bukti transaksi yang sah.\nSehat selalu kak!";

        $encodedPesan = urlencode($pesan);
        
        // Gunakan dispatch browser event untuk buka tab baru (lebih aman drpd redirect)
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