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

    // --- DOWNLOAD PDF MANUAL ---
    public function downloadNota($id)
    {
        // Redirect ke route khusus download PDF agar stabil
        return redirect()->route('nota.print', ['id' => $id]);
    }

    // --- KIRIM FILE PDF KE WA (VIA FONNTE) ---
    public function kirimWa($id)
    {
        // 1. Ambil Data Penjualan
        $penjualan = Penjualan::with(['user', 'cabang', 'stok'])->find($id);

        if(!$penjualan) {
            $this->dispatch('swal', ['icon' => 'error', 'title' => 'Gagal', 'text' => 'Data penjualan tidak ditemukan.']);
            return;
        }
        
        // 2. Format Nomor WA (Wajib 62xxx untuk Fonnte)
        $target = $penjualan->nomor_wa;
        $target = preg_replace('/[^0-9]/', '', $target); // Hapus spasi/strip
        
        // Ubah 08 jadi 62
        if(substr($target, 0, 1) == '0') {
            $target = '62' . substr($target, 1);
        }

        try {
            // 3. Generate PDF Binary di Server (Tanpa Download)
            $pdf = Pdf::loadView('pdf.nota_penjualan', ['penjualan' => $penjualan])
                      ->setPaper('a5', 'portrait');
            $pdfContent = $pdf->output();

            // 4. Simpan File PDF Sementara di Public Storage
            // Nama file dibuat unik dengan time()
            $fileName = 'Nota_TRX-' . $penjualan->id . '_' . time() . '.pdf';
            $filePath = 'temp_nota/' . $fileName;
            
            // Pastikan simpan di 'public' disk agar bisa diakses URL-nya
            Storage::disk('public')->put($filePath, $pdfContent);
            
            // 5. Generate URL Publik File PDF
            // Fonnte membutuhkan URL ini untuk mendownload dan mengirim file ke WA
            $fileUrl = asset('storage/' . $filePath);

            // 6. Susun Pesan Caption
            $pesan = "Halo Kak *{$penjualan->nama_customer}*,\n";
            $pesan .= "Terima kasih telah berbelanja di *PSTORE {$penjualan->cabang->nama_cabang}*.\n";
            $pesan .= "Berikut kami lampirkan Nota Resmi pembelian Anda.\n\n";
            $pesan .= "Mohon disimpan sebagai bukti garansi.\nSehat selalu!";

            // 7. Ambil Token dari .env
            $token = env('FONNTE_TOKEN'); 

            if(empty($token)) {
                throw new Exception("Token Fonnte belum disetting di file .env");
            }

            // 8. Tembak API Fonnte
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->post('https://api.fonnte.com/send', [
                'target' => $target,
                'url' => $fileUrl, // URL PDF yang kita generate tadi
                'filename' => 'Nota-PStore-'.$penjualan->id.'.pdf', // Nama file di WA Customer
                'message' => $pesan, // Caption pesan
            ]);

            // 9. Cek Response dari Fonnte
            $resBody = $response->json();

            if ($response->successful() && isset($resBody['status']) && $resBody['status'] == true) {
                $this->dispatch('swal', ['icon' => 'success', 'title' => 'Terkirim!', 'text' => 'Nota PDF berhasil dikirim ke WhatsApp Customer.']);
            } else {
                // Tangkap pesan error dari Fonnte (misal: invalid token)
                $reason = $resBody['reason'] ?? 'Unknown Error';
                $this->dispatch('swal', ['icon' => 'error', 'title' => 'Gagal Kirim', 'text' => 'Fonnte Error: ' . $reason]);
            }

            // (Opsional) Hapus file temp agar server tidak penuh
            // Beri delay sedikit atau gunakan Job Queue di production
            // Storage::disk('public')->delete($filePath); 

        } catch (Exception $e) {
            // Tangkap Error System (misal gagal generate PDF)
            $this->dispatch('swal', ['icon' => 'error', 'title' => 'System Error', 'text' => $e->getMessage()]);
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

        // Summary Data
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