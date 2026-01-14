<?php

namespace App\Livewire\Sales;

use App\Models\Penjualan;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http; // Untuk tembak API
use Illuminate\Support\Facades\Storage; // Untuk simpan file sementara
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

    // --- DOWNLOAD PDF MANUAL ---
    public function downloadNota($id)
    {
        return redirect()->route('nota.print', ['id' => $id]);
    }

    // --- KIRIM PDF KE WA OTOMATIS (VIA FONNTE) ---
    public function kirimWa($id)
    {
        $penjualan = Penjualan::with(['user', 'cabang', 'stok'])->findOrFail($id);
        
        // 1. Format Nomor WA (Wajib 62xxx untuk API)
        $target = $penjualan->nomor_wa;
        $target = preg_replace('/[^0-9]/', '', $target); 
        if(substr($target, 0, 1) == '0') {
            $target = '62' . substr($target, 1);
        }

        try {
            // 2. Generate PDF Binary
            $pdf = Pdf::loadView('pdf.nota_penjualan', ['penjualan' => $penjualan])
                      ->setPaper('a5', 'portrait');
            $content = $pdf->output();

            // 3. Simpan PDF Sementara di Public Storage
            // Agar bisa diakses oleh server Fonnte
            $fileName = 'Nota_TRX-' . $penjualan->id . '_' . time() . '.pdf';
            Storage::disk('public')->put('temp_pdf/' . $fileName, $content);
            
            // Generate URL Publik (PENTING: Ini harus bisa diakses internet)
            // Jika Anda di Localhost, Fonnte tidak bisa ambil file ini kecuali pakai Ngrok.
            $fileUrl = asset('storage/temp_pdf/' . $fileName);

            // 4. Kirim Request ke Fonnte
            $token = env('FONNTE_TOKEN'); // Ambil dari .env

            $pesan = "Halo Kak *{$penjualan->nama_customer}*,\n";
            $pesan .= "Terima kasih telah berbelanja di *PSTORE {$penjualan->cabang->nama_cabang}*.\n";
            $pesan .= "Berikut kami lampirkan Nota Resmi pembelian Anda.\n\n";
            $pesan .= "Mohon disimpan sebagai bukti garansi.\nSehat selalu!";

            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->post('https://api.fonnte.com/send', [
                'target' => $target,
                'url' => $fileUrl, // Fonnte akan download file dari link ini & kirim ke customer
                'message' => $pesan,
            ]);

            // 5. Cek Response
            if ($response->successful()) {
                $this->dispatch('swal', ['icon' => 'success', 'title' => 'Terikirim!', 'text' => 'Nota PDF berhasil dikirim ke WhatsApp Customer.']);
            } else {
                $this->dispatch('swal', ['icon' => 'error', 'title' => 'Gagal', 'text' => 'Gagal kirim WA: ' . $response->body()]);
            }

            // (Opsional) Hapus file temp nanti pakai Job/Scheduler agar storage tidak penuh
            // Storage::disk('public')->delete('temp_pdf/' . $fileName); 

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