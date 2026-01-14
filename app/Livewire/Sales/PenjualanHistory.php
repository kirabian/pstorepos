<?php

namespace App\Livewire\Sales;

use App\Models\Penjualan;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;

class PenjualanHistory extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    // ... (property search, filter, mount, updatedSearch tetap sama) ...
    public $search = '';
    public $filterStatus = ''; 
    public $bulan = '';
    public $tahun = '';

    public function mount() {
        $this->bulan = date('m');
        $this->tahun = date('Y');
    }
    public function updatedSearch() { $this->resetPage(); }

    public function downloadNota($id)
    {
        return redirect()->route('nota.print', ['id' => $id]);
    }

    // --- FUNGSI KIRIM WA (PERBAIKAN URL) ---
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
            // 1. Generate & Save PDF
            $pdf = Pdf::loadView('pdf.nota_penjualan', ['penjualan' => $penjualan])->setPaper('a5', 'portrait');
            $fileName = 'Nota-' . $penjualan->id . '-' . time() . '.pdf'; // Tambah time() biar unik
            
            // Simpan ke storage public
            Storage::disk('public')->put('temp_nota/' . $fileName, $pdf->output());
            
            // 2. GENERATE URL (FORCE HTTPS)
            // Kita paksa pakai https agar Fonnte mau baca
            $fileUrl = asset('storage/temp_nota/' . $fileName);
            if (!str_contains($fileUrl, 'https://')) {
                $fileUrl = str_replace('http://', 'https://', $fileUrl);
            }

            // 3. Pesan Caption
            $pesan = "Halo Kak *{$penjualan->nama_customer}*,\n";
            $pesan .= "Terima kasih telah berbelanja di *PSTORE {$penjualan->cabang->nama_cabang}*.\n";
            $pesan .= "Berikut Nota Resmi (PDF) Anda 👇";

            // 4. Kirim Pakai CURL
            $token = env('FONNTE_TOKEN');
            
            $curl = curl_init();

            curl_setopt_array($curl, array(
              CURLOPT_URL => 'https://api.fonnte.com/send',
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'POST',
              CURLOPT_POSTFIELDS => array(
                'target' => $target,
                'url' => $fileUrl, // Kirim URL File yang sudah HTTPS
                'filename' => 'Nota-PStore.pdf',
                'message' => $pesan,
              ),
              CURLOPT_HTTPHEADER => array(
                'Authorization: ' . $token
              ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);
            
            $res = json_decode($response, true);

            // 5. Cek Berhasil atau Gagal
            if (isset($res['status']) && $res['status'] == true) {
                $this->dispatch('swal', ['icon' => 'success', 'title' => 'Terkirim', 'text' => 'Nota PDF berhasil dikirim!']);
            } else {
                // --- FALLBACK (PLAN B) ---
                // Jika kirim file gagal, kirim LINK DOWNLOAD saja.
                // Ini pasti berhasil karena cuma kirim teks.
                
                // Gunakan route nota.print yang sudah kita buat sebelumnya
                $linkDownload = route('nota.print', ['id' => $penjualan->id]);
                
                $pesanFallback = $pesan . "\n\n(Mohon maaf, file PDF gagal terlampir otomatis. Silakan download melalui link di bawah ini):\n" . $linkDownload;
                
                $curl2 = curl_init();
                curl_setopt_array($curl2, array(
                  CURLOPT_URL => 'https://api.fonnte.com/send',
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_CUSTOMREQUEST => 'POST',
                  CURLOPT_POSTFIELDS => array(
                    'target' => $target,
                    'message' => $pesanFallback, 
                  ),
                  CURLOPT_HTTPHEADER => array(
                    'Authorization: ' . $token
                  ),
                ));
                curl_exec($curl2);
                curl_close($curl2);

                $this->dispatch('swal', [
                    'icon' => 'warning', 
                    'title' => 'Info', 
                    'text' => 'File PDF gagal terkirim (Fonnte tidak bisa akses server), tapi Link Download sudah dikirim sebagai gantinya.'
                ]);
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