<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Nota #{{ $penjualan->id }}</title>
    <style>
        /* CSS Sederhana untuk PDF */
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
        .logo { height: 50px; margin-bottom: 5px; }
        .info-table, .product-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .info-table td { padding: 3px; }
        .product-table th, .product-table td { border: 1px solid #999; padding: 8px; text-align: left; }
        .product-table th { background-color: #eee; }
        .total { text-align: right; font-size: 14px; font-weight: bold; margin-top: 10px; }
        .footer { text-align: center; margin-top: 40px; font-size: 10px; color: #777; border-top: 1px solid #ccc; padding-top: 10px; }
        .status-lunas { color: green; font-weight: bold; border: 1px solid green; padding: 2px 5px; border-radius: 4px; }
        .status-pending { color: orange; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        {{-- Pastikan file gambar ada di public/images/logo-pstore.png --}}
        <img src="{{ public_path('images/logo-pstore.png') }}" class="logo" alt="PSTORE">
        <h2>NOTA PENJUALAN</h2>
        <p><strong>{{ strtoupper($penjualan->cabang->nama_cabang) }}</strong></p>
    </div>

    <table class="info-table">
        <tr>
            <td width="15%">No. Transaksi</td>
            <td width="35%">: #TRX-{{ $penjualan->id }}</td>
            <td width="15%">Tanggal</td>
            <td width="35%">: {{ $penjualan->created_at->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td>Customer</td>
            <td>: {{ $penjualan->nama_customer }}</td>
            <td>Sales</td>
            <td>: {{ $penjualan->user->nama_lengkap }}</td>
        </tr>
        <tr>
            <td>No. WA</td>
            <td>: {{ $penjualan->nomor_wa }}</td>
            <td>Status</td>
            <td>: 
                @if($penjualan->status_audit == 'Approved')
                    <span class="status-lunas">LUNAS</span>
                @else
                    <span class="status-pending">PROSES AUDIT</span>
                @endif
            </td>
        </tr>
    </table>

    <table class="product-table">
        <thead>
            <tr>
                <th>Produk</th>
                <th>Detail / IMEI</th>
                <th style="text-align: right;">Harga</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $penjualan->nama_produk }}</td>
                <td>
                    IMEI: {{ $penjualan->imei_terjual }}<br>
                    <small>{{ $penjualan->stok->kondisi ?? '' }} | {{ $penjualan->stok->ram_storage ?? '' }}</small>
                </td>
                <td style="text-align: right;">Rp {{ number_format($penjualan->harga_jual_real, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="total">
        TOTAL BAYAR: Rp {{ number_format($penjualan->harga_jual_real, 0, ',', '.') }}
    </div>

    @if($penjualan->catatan)
        <div style="margin-top: 10px; padding: 10px; background: #f9f9f9; border: 1px dashed #ccc;">
            <strong>Catatan:</strong> {{ $penjualan->catatan }}
        </div>
    @endif

    <div class="footer">
        <p>Terima kasih telah berbelanja di PSTORE.<br>Simpan nota ini sebagai bukti transaksi yang sah.</p>
    </div>
</body>
</html>