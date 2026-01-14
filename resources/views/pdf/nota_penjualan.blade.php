<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Penjualan - {{ $penjualan->id }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .header img {
            max-height: 50px;
            margin-bottom: 5px;
        }
        .header h2 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
        }
        .header p {
            margin: 2px 0;
            font-size: 10px;
            color: #666;
        }
        .meta-info {
            width: 100%;
            margin-bottom: 20px;
        }
        .meta-info td {
            padding: 3px;
            vertical-align: top;
        }
        .label {
            font-weight: bold;
            width: 120px;
        }
        .content-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .content-table th, .content-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .content-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .total-section {
            text-align: right;
            margin-top: 10px;
        }
        .total-label {
            font-weight: bold;
            font-size: 14px;
        }
        .total-value {
            font-size: 16px;
            font-weight: bold;
            color: #000;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #888;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .notes {
            margin-top: 10px;
            font-size: 10px;
            font-style: italic;
            background: #f9f9f9;
            padding: 5px;
            border: 1px dashed #ccc;
        }
        .ttd {
            width: 100%;
            margin-top: 30px;
            text-align: center;
        }
        .ttd td {
            padding-top: 50px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ public_path('images/logo-pstore.png') }}" alt="PSTORE">
            <h2>NOTA PENJUALAN</h2>
            <p>{{ strtoupper($penjualan->cabang->nama_cabang) }}</p>
            <p>{{ $penjualan->cabang->alamat ?? 'Alamat Cabang Belum Diisi' }}</p>
        </div>

        <table class="meta-info">
            <tr>
                <td class="label">No. Transaksi</td>
                <td>: #TRX-{{ str_pad($penjualan->id, 6, '0', STR_PAD_LEFT) }}</td>
                <td class="label">Tanggal</td>
                <td>: {{ $penjualan->created_at->format('d F Y, H:i') }}</td>
            </tr>
            <tr>
                <td class="label">Customer</td>
                <td>: {{ $penjualan->nama_customer }}</td>
                <td class="label">Sales</td>
                <td>: {{ $penjualan->user->nama_lengkap }}</td>
            </tr>
            <tr>
                <td class="label">No. WA</td>
                <td>: {{ $penjualan->nomor_wa }}</td>
                <td class="label">Status</td>
                <td>: {{ $penjualan->status_audit == 'Approved' ? 'LUNAS (Verified)' : 'PROSES (Pending)' }}</td>
            </tr>
        </table>

        <table class="content-table">
            <thead>
                <tr>
                    <th>Deskripsi Produk</th>
                    <th>IMEI / Serial</th>
                    <th style="text-align: right;">Harga</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>{{ $penjualan->nama_produk }}</strong><br>
                        <small>
                            {{ $penjualan->stok->ram_storage ?? '-' }} | 
                            {{ $penjualan->stok->kondisi ?? '-' }}
                        </small>
                    </td>
                    <td>{{ $penjualan->imei_terjual }}</td>
                    <td style="text-align: right;">Rp {{ number_format($penjualan->harga_jual_real, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <div class="total-section">
            <span class="total-label">TOTAL BAYAR: </span>
            <span class="total-value">Rp {{ number_format($penjualan->harga_jual_real, 0, ',', '.') }}</span>
        </div>

        @if($penjualan->catatan)
            <div class="notes">
                <strong>Catatan / Bundling / Info Audit:</strong><br>
                {{ $penjualan->catatan }}
                @if($penjualan->stok && $penjualan->harga_jual_real < $penjualan->stok->harga_jual)
                    <br><span style="color: red;">* Harga Jual di bawah SRP (Selisih: Rp {{ number_format($penjualan->stok->harga_jual - $penjualan->harga_jual_real, 0, ',', '.') }})</span>
                @endif
            </div>
        @endif

        <div style="margin-top: 15px; font-size: 10px;">
            <strong>Metode Pembayaran:</strong> Transfer / EDC<br>
            <em>Bukti pembayaran telah terlampir di sistem audit.</em>
        </div>

        <table class="ttd">
            <tr>
                <td width="50%">Hormat Kami,<br><br><br><br>( {{ $penjualan->user->nama_lengkap }} )</td>
                <td width="50%">Penerima,<br><br><br><br>( {{ $penjualan->nama_customer }} )</td>
            </tr>
        </table>

        <div class="footer">
            <p>Terima kasih telah berbelanja di PSTORE.</p>
            <p>Barang yang sudah dibeli tidak dapat ditukar/dikembalikan kecuali ada perjanjian garansi.</p>
        </div>
    </div>
</body>
</html>