<?php

namespace App\Livewire\Sales;

use App\Models\Penjualan;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use CURLFile; 

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

    // --- FUNGSI KIRIM WA (METODE UPLOAD FILE - STABIL) ---
    public function kirimWa($id)
    {
        $penjualan = Penjualan::with(['user', 'cabang'])->find($id);

        if(!$penjualan) {
            $this->dispatch('swal', ['icon' => 'error', 'title' => 'Gagal', 'text' => 'Data tidak ditemukan']);
            return;
        }
        
        // 1. Format Nomor WA (Wajib 62xxx)
        $target = preg_replace('/[^0-9]/', '', $penjualan->nomor_wa);
        if(substr($target, 0, 1) == '0') $target = '62' . substr($target, 1);

        try {
            // 2. Generate PDF & Simpan Fisik di Server
            // Kita simpan dulu filenya agar bisa di-upload oleh CURL
            $pdf = Pdf::loadView('pdf.nota_penjualan', ['penjualan' => $penjualan])->setPaper('a5', 'portrait');
            $fileName = 'Nota-' . $penjualan->id . '.pdf';
            
            // Path folder penyimpanan (local storage)
            $storagePath = storage_path('app/public/temp_nota');
            $fullPath = $storagePath . '/' . $fileName;
            
            // Pastikan folder ada
            if(!file_exists($storagePath)) {
                mkdir($storagePath, 0755, true);
            }
            
            $pdf->save($fullPath);

            // 3. Konfigurasi Pesan & API
            $pesan = "Halo Kak *{$penjualan->nama_customer}*,\n";
            $pesan .= "Terima kasih telah berbelanja di *PSTORE {$penjualan->cabang->nama_cabang}*.\n";
            $pesan .= "Berikut Nota Resmi (PDF) transaksi Anda.\n";
            $pesan .= "Total: Rp " . number_format($penjualan->harga_jual_real, 0, ',', '.') . "\n\n";
            $pesan .= "Sehat selalu!";

            $domain = env('WABLAS_DOMAIN'); 
            $token  = env('WABLAS_TOKEN');
            $secret = env('WABLAS_SECRET'); 

            // Pastikan tidak ada slash di akhir domain
            $domain = rtrim($domain, '/');

            // 4. Kirim Request (UPLOAD FILE LANGSUNG)
            $curl = curl_init();
            
            // Siapkan File untuk Upload
            $cfile = new CURLFile($fullPath, 'application/pdf', $fileName);

            $data = [
                'phone' => $target,
                'message' => $pesan,
                'document' => $cfile, // Kirim file fisik
            ];

            curl_setopt_array($curl, [
                CURLOPT_URL => "$domain/api/send-message",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30, // Timeout 30 detik
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $data, // Kirim array data (otomatis multipart)
                CURLOPT_HTTPHEADER => [
                    "Authorization: $token",
                    "Secret: $secret", 
                    // JANGAN ADA Content-Type DISINI! Biarkan CURL yang atur boundary.
                ],
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
            ]);

            $result = curl_exec($curl);
            $error  = curl_error($curl);
            curl_close($curl);

            // Hapus file sementara setelah proses kirim selesai
            if(file_exists($fullPath)) unlink($fullPath);

            $response = json_decode($result, true);

            // 5. Cek Response & Fallback
            // Wablas sukses jika status = true
            if (isset($response['status']) && $response['status'] == true) {
                $this->dispatch('swal', ['icon' => 'success', 'title' => 'Terkirim!', 'text' => 'Nota PDF berhasil dikirim via Wablas.']);
            } else {
                // --- FALLBACK: KIRIM LINK DOWNLOAD JIKA FILE GAGAL ---
                // Ini Plan B agar customer tetap dapat nota meskipun upload file gagal
                
                $linkDownload = route('nota.print', ['id' => $penjualan->id]);
                $pesanLink = $pesan . "\n\n(Gagal melampirkan file, silakan download nota di sini):\n" . $linkDownload;
                
                $curl2 = curl_init();
                $data2 = ['phone' => $target, 'message' => $pesanLink];
                
                curl_setopt_array($curl2, [
                    CURLOPT_URL => "$domain/api/send-message",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => http_build_query($data2), // Kirim text biasa
                    CURLOPT_HTTPHEADER => [
                        "Authorization: $token",
                        "Secret: $secret"
                    ],
                    CURLOPT_SSL_VERIFYHOST => 0,
                    CURLOPT_SSL_VERIFYPEER => 0,
                ]);
                
                curl_exec($curl2);
                curl_close($curl2);

                $reason = $response['message'] ?? 'Unknown Error';
                $this->dispatch('swal', ['icon' => 'warning', 'title' => 'Info', 'text' => "File gagal ($reason), Link dikirim sebagai gantinya."]);
            }

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