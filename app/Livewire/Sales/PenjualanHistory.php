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

    // --- FUNGSI KIRIM WA VIA WASENDERAPI (INDONESIA) ---
    public function kirimWa($id)
    {
        $penjualan = Penjualan::with(['user', 'cabang'])->find($id);

        if(!$penjualan) {
            $this->dispatch('swal', ['icon' => 'error', 'title' => 'Gagal', 'text' => 'Data penjualan tidak ditemukan.']);
            return;
        }
        
        // --- PERBAIKAN FORMAT NOMOR HP (AGAR LEBIH KEBAL ERROR) ---
        $originalNo = $penjualan->nomor_wa;
        $target = preg_replace('/[^0-9]/', '', $originalNo); // Hapus spasi, strip, dll

        // Logika perbaikan awalan nomor
        if (substr($target, 0, 2) == '62') {
            $target = '+' . $target; // Sudah 62, tambah +
        } elseif (substr($target, 0, 1) == '0') {
            $target = '+62' . substr($target, 1); // Ubah 08 jadi +628
        } elseif (substr($target, 0, 1) == '8') {
            $target = '+62' . $target; // Lupa 0, langsung 8 -> jadi +628
        } else {
            // Jika format aneh (misal 09...), tetap coba kirim tapi kemungkinan gagal
            $target = '+' . $target; 
        }

        try {
            // ==========================================
            // LANGKAH 1: BUAT PDF (DALAM BENTUK BINER)
            // ==========================================
            $pdf = Pdf::loadView('pdf.nota_penjualan', ['penjualan' => $penjualan])->setPaper('a5', 'portrait');
            $pdfContent = $pdf->output(); 
            
            $token = env('WASENDER_TOKEN');
            
            // ==========================================
            // LANGKAH 2: UPLOAD FILE KE SERVER WA (WASENDER)
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
                CURLOPT_POSTFIELDS => $pdfContent, // Kirim isi file PDF
                CURLOPT_HTTPHEADER => [
                    "Authorization: Bearer " . $token,
                    "Content-Type: application/pdf"
                ],
            ]);

            $responseUpload = curl_exec($curlUpload);
            $errUpload = curl_error($curlUpload);
            curl_close($curlUpload);

            if ($errUpload) {
                throw new Exception("Gagal Upload ke Server WA: " . $errUpload);
            }

            $resUpload = json_decode($responseUpload, true);

            // Cek apakah upload berhasil
            if (!isset($resUpload['success']) || !$resUpload['success'] || !isset($resUpload['publicUrl'])) {
                throw new Exception("Gagal mendapatkan Link File dari Server WA. Respon: " . json_encode($resUpload));
            }

            $documentUrl = $resUpload['publicUrl']; // Link File Publik

            // ==========================================
            // LANGKAH 3: KIRIM PESAN + LINK DOKUMEN
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
                "documentUrl" => $documentUrl, // Link dari langkah 2
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
            // Wasender sukses jika status = 'success' atau true
            if (!$errSend && isset($resSend['status']) && ($resSend['status'] == 'success' || $resSend['status'] == true)) {
                $this->dispatch('swal', ['icon' => 'success', 'title' => 'Terkirim!', 'text' => 'Nota PDF berhasil dikirim ke nomor ' . $target]);
            } else {
                // Ambil pesan error dari API
                $reason = $resSend['message'] ?? 'Kesalahan Tidak Diketahui';
                
                // Tambahkan info nomor agar sales sadar jika nomor salah
                $this->dispatch('swal', [
                    'icon' => 'warning', 
                    'title' => 'Gagal Kirim', 
                    'text' => "Pesan gagal dikirim ke $target. Alasan: $reason. Pastikan nomor WA valid dan terdaftar."
                ]);
            }

        } catch (Exception $e) {
            $this->dispatch('swal', ['icon' => 'error', 'title' => 'System Error', 'text' => $e->getMessage()]);
        }
    }

    public function render()
    {
        $user = Auth::user();
        $query = Penjualan::with(['stok', 'auditor'])->where('user_id', $user->id);

        // Filter Pencarian
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

        // Ringkasan Data
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