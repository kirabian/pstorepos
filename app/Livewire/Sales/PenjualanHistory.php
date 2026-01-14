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

    // --- FUNGSI KIRIM WA VIA WASENDERAPI (DIRECT LINK MODE) ---
    public function kirimWa($id)
    {
        $penjualan = Penjualan::with(['user', 'cabang'])->find($id);

        if(!$penjualan) {
            $this->dispatch('swal', ['icon' => 'error', 'title' => 'Gagal', 'text' => 'Data tidak ditemukan']);
            return;
        }
        
        // 1. Format Nomor WA (E.164: +62...)
        $target = preg_replace('/[^0-9]/', '', $penjualan->nomor_wa);
        if(substr($target, 0, 1) == '0') {
            $target = '+62' . substr($target, 1);
        } elseif(substr($target, 0, 2) == '62') {
            $target = '+' . $target;
        } elseif(substr($target, 0, 1) == '8') {
            $target = '+62' . $target;
        }

        try {
            // ========================================================
            // LANGKAH 1: GENERATE PDF & SIMPAN DI STORAGE PUBLIK SERVER
            // ========================================================
            $pdf = Pdf::loadView('pdf.nota_penjualan', ['penjualan' => $penjualan])->setPaper('a5', 'portrait');
            
            // Nama file unik
            $fileName = 'Nota-' . $penjualan->id . '-' . time() . '.pdf';
            $filePath = 'temp_nota/' . $fileName;
            
            // Simpan file fisik di server kita sendiri (stokps.com)
            Storage::disk('public')->put($filePath, $pdf->output());
            
            // Generate Link Publik (Wajib HTTPS agar bisa diakses Wasender)
            $fileUrl = asset('storage/' . $filePath);
            
            // Paksa HTTPS jika tergenerate HTTP (Penting untuk API luar)
            if (!str_contains($fileUrl, 'https://')) {
                $fileUrl = str_replace('http://', 'https://', $fileUrl);
            }

            // ========================================================
            // LANGKAH 2: KIRIM URL TERSEBUT KE API WASENDER
            // (Sesuai contoh docs Guzzle yang Anda kirim)
            // ========================================================
            
            $token = env('WASENDER_TOKEN');
            if(empty($token)) throw new Exception("Token Wasender belum diisi.");

            $pesan = "Halo Kak *{$penjualan->nama_customer}*,\n";
            $pesan .= "Terima kasih telah berbelanja di *PSTORE {$penjualan->cabang->nama_cabang}*.\n";
            $pesan .= "Berikut Nota Resmi transaksi Anda.\n";
            $pesan .= "Total: Rp " . number_format($penjualan->harga_jual_real, 0, ',', '.') . "\n\n";
            $pesan .= "Sehat selalu!";

            // Setup CURL
            $curl = curl_init();
            
            $payload = [
                "to" => $target,
                "text" => $pesan,
                "documentUrl" => $fileUrl, // <--- Kita kirim URL file kita di sini
            ];

            curl_setopt_array($curl, [
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
                    "Content-Type: application/json",
                    "Accept: application/json"
                ],
            ]);

            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);

            // ========================================================
            // LANGKAH 3: VALIDASI RESPON
            // ========================================================
            
            if ($err) {
                throw new Exception("CURL Error: " . $err);
            }

            $res = json_decode($response, true);

            // Wasender biasanya return "status": "success" atau true
            if (isset($res['status']) && ($res['status'] == 'success' || $res['status'] == true)) {
                $this->dispatch('swal', ['icon' => 'success', 'title' => 'Terkirim!', 'text' => 'Nota PDF berhasil dikirim ke ' . $target]);
            } else {
                // Jika gagal, tampilkan pesan error dari API
                $reason = $res['message'] ?? 'Unknown API Error';
                $this->dispatch('swal', ['icon' => 'warning', 'title' => 'Gagal Kirim', 'text' => "Pesan gagal: $reason"]);
            }

        } catch (Exception $e) {
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