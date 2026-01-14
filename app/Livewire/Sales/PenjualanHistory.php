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

    public function kirimWa($id)
    {
        $penjualan = Penjualan::with(['user', 'cabang'])->find($id);

        if(!$penjualan) {
            $this->dispatch('swal', ['icon' => 'error', 'title' => 'Gagal', 'text' => 'Data tidak ditemukan']);
            return;
        }
        
        $target = preg_replace('/[^0-9]/', '', $penjualan->nomor_wa);
        if(substr($target, 0, 1) == '0') $target = '62' . substr($target, 1);

        try {
            // 1. Generate & Save PDF Locally
            $pdf = Pdf::loadView('pdf.nota_penjualan', ['penjualan' => $penjualan])->setPaper('a5', 'portrait');
            $fileName = 'Nota-' . $penjualan->id . '.pdf';
            
            // Simpan di storage public agar ada URL-nya (untuk fallback)
            $filePath = 'temp_nota/' . $fileName;
            Storage::disk('public')->put($filePath, $pdf->output());
            $publicUrl = asset('storage/' . $filePath);

            // Path Fisik untuk Upload CURL
            $realPath = storage_path('app/public/temp_nota/' . $fileName);

            // 2. Pesan Caption
            $pesan = "Halo Kak *{$penjualan->nama_customer}*,\n";
            $pesan .= "Terima kasih telah berbelanja di *PSTORE {$penjualan->cabang->nama_cabang}*.\n";
            $pesan .= "Berikut Nota Resmi (PDF) transaksi Anda.\n";
            $pesan .= "Total: Rp " . number_format($penjualan->harga_jual_real, 0, ',', '.') . "\n\n";
            $pesan .= "Sehat selalu!";

            // 3. Config Wablas
            $domain = env('WABLAS_DOMAIN'); 
            $token  = env('WABLAS_TOKEN');

            // --- METODE 1: COBA UPLOAD FILE FISIK ---
            $curl = curl_init();
            
            // Gunakan array murni untuk post fields
            $postFields = [
                'phone' => $target,
                'message' => $pesan,
                'document' => new CURLFile($realPath, 'application/pdf', $fileName),
            ];

            curl_setopt($curl, CURLOPT_HTTPHEADER, [
                "Authorization: $token",
                // JANGAN set Content-Type, biarkan CURL yang handle boundary
            ]);
            curl_setopt($curl, CURLOPT_URL, "$domain/api/send-message");
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postFields);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

            $result = curl_exec($curl);
            $error = curl_error($curl);
            curl_close($curl);

            $response = json_decode($result, true);

            // 4. Validasi Response
            if (isset($response['status']) && $response['status'] == true) {
                $this->dispatch('swal', ['icon' => 'success', 'title' => 'Terkirim!', 'text' => 'Nota PDF berhasil dikirim via Wablas.']);
            } else {
                // --- METODE 2 (FALLBACK): KIRIM LINK DOWNLOAD ---
                // Jika upload gagal, kita kirim link publik saja. Ini 99% pasti berhasil.
                
                $pesanFallback = $pesan . "\n\n(Klik link di bawah untuk download Nota):\n" . $publicUrl;

                $curl2 = curl_init();
                $postFields2 = [
                    'phone' => $target,
                    'message' => $pesanFallback,
                ];

                curl_setopt($curl2, CURLOPT_HTTPHEADER, [
                    "Authorization: $token",
                    "Content-Type: application/x-www-form-urlencoded" // Kirim text biasa
                ]);
                curl_setopt($curl2, CURLOPT_URL, "$domain/api/send-message");
                curl_setopt($curl2, CURLOPT_POST, true);
                curl_setopt($curl2, CURLOPT_POSTFIELDS, http_build_query($postFields2));
                curl_setopt($curl2, CURLOPT_RETURNTRANSFER, true);
                curl_exec($curl2);
                curl_close($curl2);

                $reason = $response['message'] ?? 'Gagal upload file';
                $this->dispatch('swal', ['icon' => 'warning', 'title' => 'Info', 'text' => "File gagal terlampir ($reason), Link download dikirim sebagai gantinya."]);
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