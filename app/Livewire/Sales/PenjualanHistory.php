<?php

namespace App\Livewire\Sales;

use App\Models\Penjualan;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http; // Untuk Request ke Fonnte
use Illuminate\Support\Facades\Storage; // Untuk Simpan PDF Sementara
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

    public function updatedSearch() { $this->resetPage(); }

    // --- DOWNLOAD PDF MANUAL (Tetap Ada) ---
    public function downloadNota($id)
    {
        return redirect()->route('nota.print', ['id' => $id]);
    }

    // --- KIRIM FILE PDF KE WA (VIA FONNTE) ---
    public function kirimWa($id)
    {
        $penjualan = Penjualan::with(['user', 'cabang', 'stok'])->findOrFail($id);
        
        // 1. Format Nomor WA (Wajib 62xxx untuk Fonnte)
        $target = $penjualan->nomor_wa;
        $target = preg_replace('/[^0-9]/', '', $target); 
        if(substr($target, 0, 1) == '0') {
            $target = '62' . substr($target, 1);
        }

        try {
            // 2. Generate PDF Binary di Server
            $pdf = Pdf::loadView('pdf.nota_penjualan', ['penjualan' => $penjualan])
                      ->setPaper('a5', 'portrait');
            $pdfContent = $pdf->output();

            // 3. Simpan File PDF Sementara di Public Storage
            // Nama file unik biar gak bentrok
            $fileName = 'Nota_TRX-' . $penjualan->id . '_' . time() . '.pdf';
            $filePath = 'temp_nota/' . $fileName;
            
            // Simpan ke storage/app/public/temp_nota
            Storage::disk('public')->put($filePath, $pdfContent);
            
            // Generate URL Publik (Fonnte butuh link internet untuk ambil filenya)
            // Contoh: https://domainanda.com/storage/temp_nota/Nota_TRX-1.pdf
            $fileUrl = asset('storage/' . $filePath);

            // 4. Susun Pesan Caption
            $pesan = "Halo Kak *{$penjualan->nama_customer}*,\n";
            $pesan .= "Terima kasih telah berbelanja di *PSTORE {$penjualan->cabang->nama_cabang}*.\n";
            $pesan .= "Berikut kami lampirkan Nota Resmi pembelian Anda (File PDF).\n\n";
            $pesan .= "Mohon disimpan sebagai bukti garansi.\nSehat selalu!";

            // 5. Tembak API Fonnte
            $token = env('FONNTE_TOKEN'); 

            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->post('https://api.fonnte.com/send', [
                'target' => $target,
                'url' => $fileUrl, // <--- INI KUNCINYA (Fonnte download dari sini & kirim sbg file)
                'filename' => 'Nota-PStore.pdf', // Nama file yang muncul di WA Customer
                'message' => $pesan, // Caption
            ]);

            // 6. Cek Response & Bersihkan File
            if ($response->successful()) {
                $this->dispatch('swal', ['icon' => 'success', 'title' => 'Terkirim!', 'text' => 'File PDF Nota berhasil dikirim ke WA Customer.']);
            } else {
                $this->dispatch('swal', ['icon' => 'error', 'title' => 'Gagal', 'text' => 'Gagal kirim: ' . $response->body()]);
            }

            // Hapus file sementara agar server tidak penuh (Opsional, kasih delay atau pakai Job)
            // Storage::disk('public')->delete($filePath); 

        } catch (Exception $e) {
            $this->dispatch('swal', ['icon' => 'error', 'title' => 'Error', 'text' => $e->getMessage()]);
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

        // Summary
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