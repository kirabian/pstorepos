<?php

namespace App\Livewire\Sales;

use App\Models\Penjualan;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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

    public function mount() {
        $this->bulan = date('m');
        $this->tahun = date('Y');
    }

    public function updatedSearch() { $this->resetPage(); }

    public function downloadNota($id) {
        return redirect()->route('nota.print', ['id' => $id]);
    }

    // --- FUNGSI KIRIM WA (METODE GOOGLE DRIVE) ---
    public function kirimWa($id)
    {
        $penjualan = Penjualan::with(['user', 'cabang'])->find($id);

        if(!$penjualan) {
            $this->dispatch('swal', ['icon' => 'error', 'title' => 'Gagal', 'text' => 'Data tidak ditemukan']);
            return;
        }
        
        // 1. Format Nomor WA (Internasional tanpa +)
        $target = preg_replace('/[^0-9]/', '', $penjualan->nomor_wa);
        if(substr($target, 0, 1) == '0') {
            $target = '62' . substr($target, 1);
        } elseif(substr($target, 0, 1) == '8') {
            $target = '62' . $target;
        }
        
        try {
            // 2. Generate PDF Binary
            $pdf = Pdf::loadView('pdf.nota_penjualan', ['penjualan' => $penjualan])->setPaper('a5', 'portrait');
            
            // Nama File Unik (Nota-ID-Timestamp.pdf)
            $fileName = 'Nota-PStore-' . $penjualan->id . '-' . time() . '.pdf';

            // 3. UPLOAD KE GOOGLE DRIVE
            // Menggunakan disk 'google' yang sudah disetting di config/filesystems.php
            Storage::disk('google')->put($fileName, $pdf->output());

            // 4. AMBIL LINK PUBLIK
            // Ini akan menghasilkan link: https://drive.google.com/file/d/ID_FILE/view
            $linkGoogleDrive = Storage::disk('google')->url($fileName);

            // 5. Susun Pesan WhatsApp
            $pesan = "Halo Kak *{$penjualan->nama_customer}*,\n";
            $pesan .= "Terima kasih telah berbelanja di *PSTORE {$penjualan->cabang->nama_cabang}*.\n\n";
            $pesan .= "Berikut Link Nota Resmi transaksi Anda (Google Drive):\n";
            $pesan .= "$linkGoogleDrive \n\n";
            $pesan .= "Total: Rp " . number_format($penjualan->harga_jual_real, 0, ',', '.') . "\n";
            $pesan .= "Link ini aman dan valid. Sehat selalu!";

            // 6. Buka WhatsApp di Tab Baru
            $encodedPesan = urlencode($pesan);
            $waUrl = "https://wa.me/{$target}?text={$encodedPesan}";
            
            $this->dispatch('open-wa', ['url' => $waUrl]);
            
            // Notifikasi Sukses
            $this->dispatch('swal', ['icon' => 'success', 'title' => 'Berhasil', 'text' => 'Nota berhasil diupload ke Drive & siap dikirim!']);

        } catch (Exception $e) {
            // Tangkap Error Khusus jika Driver Google belum disetting
            $msg = $e->getMessage();
            if(str_contains($msg, 'driver [google]')) {
                $msg = "Driver Google belum disetting di config/filesystems.php!";
            }
            
            $this->dispatch('swal', ['icon' => 'error', 'title' => 'Gagal Upload', 'text' => $msg]);
        }
    }

    public function render()
    {
        $user = Auth::user();
        $query = Penjualan::with(['stok', 'auditor'])->where('user_id', $user->id);

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

        $omset = Penjualan::where('user_id', $user->id)
            ->whereMonth('created_at', $this->bulan)->whereYear('created_at', $this->tahun)
            ->where('status_audit', '!=', 'Rejected')->sum('harga_jual_real');
        $unit = Penjualan::where('user_id', $user->id)
            ->whereMonth('created_at', $this->bulan)->whereYear('created_at', $this->tahun)
            ->where('status_audit', '!=', 'Rejected')->count();

        return view('livewire.sales.penjualan-history', [
            'penjualans' => $penjualans,
            'omset' => $omset,
            'total_unit' => $unit
        ])->title('Riwayat Penjualan Saya');
    }
}