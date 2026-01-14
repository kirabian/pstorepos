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

    // --- FUNGSI KIRIM WA (METODE UPLOAD V2 WABLAS) ---
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
            $path = storage_path('app/public/temp_nota/' . $fileName);
            
            if(!file_exists(dirname($path))) mkdir(dirname($path), 0777, true);
            $pdf->save($path);

            $domain = env('WABLAS_DOMAIN'); 
            $token  = env('WABLAS_TOKEN');

            // 2. UPLOAD FILE KE WABLAS (API V2)
            // Kita upload dulu agar file tersimpan di server Wablas
            $curl = curl_init();
            $fileData = new CURLFile($path, 'application/pdf', $fileName);
            
            curl_setopt($curl, CURLOPT_HTTPHEADER, ["Authorization: $token"]);
            curl_setopt($curl, CURLOPT_URL, "$domain/api/v2/send-media"); // Endpoint Upload
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, [
                'phone' => $target,
                'caption' => "Halo Kak *{$penjualan->nama_customer}*,\nBerikut Nota Resmi pembelian Anda di *PSTORE {$penjualan->cabang->nama_cabang}*.\n\nTotal: Rp " . number_format($penjualan->harga_jual_real, 0, ',', '.') . "\nSehat selalu!",
                'file' => $fileData, // Kirim file fisik
                'data' => json_encode(['type' => 'document', 'url' => '']) // Trik agar dianggap dokumen
            ]);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            
            $result = curl_exec($curl);
            $error = curl_error($curl);
            curl_close($curl);

            // Hapus file lokal
            if(file_exists($path)) unlink($path);

            $response = json_decode($result, true);

            // 3. Validasi
            if (isset($response['status']) && $response['status'] == true) {
                $this->dispatch('swal', ['icon' => 'success', 'title' => 'Terkirim!', 'text' => 'Nota PDF berhasil dikirim via Wablas V2.']);
            } else {
                // --- FALLBACK TERAKHIR: KIRIM LINK ---
                $linkDownload = route('nota.print', ['id' => $penjualan->id]);
                $pesanLink = "Halo Kak *{$penjualan->nama_customer}*,\nTerima kasih telah berbelanja.\n\n(Gagal melampirkan file otomatis, silakan download nota di sini):\n" . $linkDownload;
                
                $curl2 = curl_init();
                curl_setopt($curl2, CURLOPT_HTTPHEADER, ["Authorization: $token"]);
                curl_setopt($curl2, CURLOPT_URL, "$domain/api/send-message");
                curl_setopt($curl2, CURLOPT_POST, true);
                curl_setopt($curl2, CURLOPT_POSTFIELDS, http_build_query(['phone' => $target, 'message' => $pesanLink]));
                curl_setopt($curl2, CURLOPT_RETURNTRANSFER, true);
                curl_exec($curl2);
                curl_close($curl2);

                $reason = $response['message'] ?? 'Gagal Upload V2';
                $this->dispatch('swal', ['icon' => 'warning', 'title' => 'Info', 'text' => "File gagal, Link dikirim ($reason)."]);
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