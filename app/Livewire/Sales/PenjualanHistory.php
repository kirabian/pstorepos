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

    public function mount() {
        $this->bulan = date('m');
        $this->tahun = date('Y');
    }
    public function updatedSearch() { $this->resetPage(); }

    public function downloadNota($id) {
        return redirect()->route('nota.print', ['id' => $id]);
    }

    // --- FUNGSI KIRIM WA VIA WASENDERAPI (METODE UPLOAD V2) ---
    public function kirimWa($id)
    {
        $penjualan = Penjualan::with(['user', 'cabang'])->find($id);

        if(!$penjualan) {
            $this->dispatch('swal', ['icon' => 'error', 'title' => 'Gagal', 'text' => 'Data tidak ditemukan']);
            return;
        }
        
        // 1. Format Nomor WA (E.164 Format: +62...)
        $target = preg_replace('/[^0-9]/', '', $penjualan->nomor_wa);
        if(substr($target, 0, 1) == '0') {
            $target = '+62' . substr($target, 1);
        } elseif(substr($target, 0, 2) == '62') {
            $target = '+' . $target;
        }

        try {
            // ==========================================
            // LANGKAH 1: GENERATE PDF (BINARY)
            // ==========================================
            $pdf = Pdf::loadView('pdf.nota_penjualan', ['penjualan' => $penjualan])->setPaper('a5', 'portrait');
            $pdfContent = $pdf->output(); // Ambil raw binary data PDF
            
            $token = env('WASENDER_TOKEN');
            
            // ==========================================
            // LANGKAH 2: UPLOAD FILE KE WASENDERAPI
            // Docs: https://wasenderapi.com/api/upload
            // ==========================================
            $curlUpload = curl_init();

            curl_setopt_array($curlUpload, [
                CURLOPT_URL => "https://wasenderapi.com/api/upload",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $pdfContent, // Kirim Raw Binary
                CURLOPT_HTTPHEADER => [
                    "Authorization: Bearer " . $token,
                    "Content-Type: application/pdf" // Wajib sesuai tipe file
                ],
            ]);

            $responseUpload = curl_exec($curlUpload);
            $errUpload = curl_error($curlUpload);
            curl_close($curlUpload);

            if ($errUpload) {
                throw new Exception("Gagal Upload ke Server WA: " . $errUpload);
            }

            $resUpload = json_decode($responseUpload, true);

            // Cek apakah upload berhasil dan dapat URL
            if (!isset($resUpload['success']) || !$resUpload['success'] || !isset($resUpload['publicUrl'])) {
                // Jika gagal upload, fallback ke kirim link website saja
                throw new Exception("Gagal mendapatkan URL File dari Wasender: " . json_encode($resUpload));
            }

            $documentUrl = $resUpload['publicUrl']; // URL Publik dari Wasender

            // ==========================================
            // LANGKAH 3: KIRIM PESAN DENGAN URL DOKUMEN
            // Docs: https://www.wasenderapi.com/api/send-message
            // ==========================================
            $pesan = "Halo Kak *{$penjualan->nama_customer}*,\n";
            $pesan .= "Terima kasih telah berbelanja di *PSTORE {$penjualan->cabang->nama_cabang}*.\n";
            $pesan .= "Berikut Nota Resmi transaksi Anda.\n";
            $pesan .= "Total: Rp " . number_format($penjualan->harga_jual_real, 0, ',', '.') . "\n\n";
            $pesan .= "Sehat selalu!";

            $curlSend = curl_init();
            
            $payload = [
                "to" => $target,
                "text" => $pesan,
                "documentUrl" => $documentUrl, // URL yang didapat dari langkah 2
                "fileName" => "Nota-PStore-{$penjualan->id}.pdf"
            ];

            curl_setopt_array($curlSend, [
                CURLOPT_URL => "https://www.wasenderapi.com/api/send-message",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_HTTPHEADER => [
                    "Authorization: Bearer " . $token,
                    "Content-Type: application/json"
                ],
            ]);

            $responseSend = curl_exec($curlSend);
            $errSend = curl_error($curlSend);
            curl_close($curlSend);

            $resSend = json_decode($responseSend, true);

            // Cek Respon Akhir
            if (!$errSend && isset($resSend['success']) && $resSend['success'] == true) {
                $this->dispatch('swal', ['icon' => 'success', 'title' => 'Terkirim!', 'text' => 'Nota PDF berhasil dikirim via WasenderAPI.']);
            } else {
                $reason = $resSend['message'] ?? 'Unknown Error';
                $this->dispatch('swal', ['icon' => 'warning', 'title' => 'Gagal Kirim', 'text' => "Pesan gagal: $reason"]);
            }

        } catch (Exception $e) {
            // Error Handling: Jika semua gagal, info errornya
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