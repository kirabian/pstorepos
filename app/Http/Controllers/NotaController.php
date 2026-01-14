<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class NotaController extends Controller
{
    public function print($id)
    {
        // Ambil data penjualan
        $penjualan = Penjualan::with(['user', 'cabang', 'stok'])->findOrFail($id);

        // Render PDF menggunakan View yang sama
        $pdf = Pdf::loadView('pdf.nota_penjualan', ['penjualan' => $penjualan])
                  ->setPaper('a5', 'portrait');

        // Stream (Tampilkan di browser) atau Download
        // stream() agar customer bisa lihat dulu baru download
        return $pdf->stream('Nota_PSTORE_TRX-'.$penjualan->id.'.pdf');
    }
}