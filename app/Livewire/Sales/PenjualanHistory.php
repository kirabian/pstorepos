<?php

namespace App\Livewire\Sales;

use App\Models\Penjualan;
use Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use CURLFile; 

class PenjualanHistory extends Component
{
    use WithPagination;
    // ... (property search, filter, mount, updatedSearch tetap sama) ...
    public function downloadNota($id) { return redirect()->route('nota.print', ['id' => $id]); }

    // --- FUNGSI KIRIM WA (METODE V1 STANDARD) ---
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

            $domain = env('WABLAS_DOMAIN'); // https://bdg.wablas.com
            $token  = env('WABLAS_TOKEN');

            // 2. KIRIM VIA ENDPOINT V1 (STANDARD)
            $curl = curl_init();
            
            // Gunakan CURLFile
            $cfile = new CURLFile($path, 'application/pdf', $fileName);

            $data = [
                'phone' => $target,
                'message' => "Halo Kak *{$penjualan->nama_customer}*,\nBerikut Nota Resmi pembelian Anda.\nSehat selalu!",
                'document' => $cfile, // Kirim file fisik
            ];

            curl_setopt($curl, CURLOPT_HTTPHEADER, [
                "Authorization: $token",
                // JANGAN ada Content-Type di sini
            ]);
            curl_setopt($curl, CURLOPT_URL, "$domain/api/send-message"); // v1 Endpoint
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            
            $result = curl_exec($curl);
            curl_close($curl);

            if(file_exists($path)) unlink($path); // Hapus file

            $response = json_decode($result, true);

            // 3. Validasi Response
            if (isset($response['status']) && $response['status'] == true) {
                $this->dispatch('swal', ['icon' => 'success', 'title' => 'Terkirim!', 'text' => 'Nota PDF berhasil dikirim via Wablas.']);
            } else {
                // --- FALLBACK (KIRIM LINK) ---
                $linkDownload = route('nota.print', ['id' => $penjualan->id]);
                $pesanLink = "Halo Kak *{$penjualan->nama_customer}*,\n(Gagal melampirkan file, download nota di sini):\n" . $linkDownload;
                
                $curl2 = curl_init();
                curl_setopt($curl2, CURLOPT_HTTPHEADER, ["Authorization: $token"]);
                curl_setopt($curl2, CURLOPT_URL, "$domain/api/send-message");
                curl_setopt($curl2, CURLOPT_POST, true);
                curl_setopt($curl2, CURLOPT_POSTFIELDS, http_build_query(['phone' => $target, 'message' => $pesanLink]));
                curl_setopt($curl2, CURLOPT_RETURNTRANSFER, true);
                curl_exec($curl2);
                curl_close($curl2);

                $reason = $response['message'] ?? 'Unknown Error';
                $this->dispatch('swal', ['icon' => 'warning', 'title' => 'Info', 'text' => "File gagal ($reason), Link dikirim."]);
            }

        } catch (Exception $e) {
            $this->dispatch('swal', ['icon' => 'error', 'title' => 'Error', 'text' => $e->getMessage()]);
        }
    }

    public function render()
    {
        $user = \Illuminate\Support\Facades\Auth::user();
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