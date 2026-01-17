<x-layouts.master>
    @slot('title', 'Dashboard Kasir Offline')

    <div class="container-fluid p-0 animate__animated animate__fadeIn">
        
        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark mb-0">{{ $cabang_name }}</h2>
                <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle px-3 py-2 rounded-pill mt-2">
                    <i class="fas fa-cash-register me-1"></i> POS SYSTEM ACTIVE
                </span>
            </div>
            <div class="text-end">
                <h1 class="display-6 fw-bold text-dark mb-0" id="clock">{{ now()->format('H:i') }}</h1>
                <p class="text-muted mb-0">{{ now()->translatedFormat('l, d F Y') }}</p>
            </div>
        </div>

        {{-- METRICS UTAMA --}}
        <div class="row g-3 mb-4">
            {{-- Omset Harian --}}
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 bg-dark text-white h-100">
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-white bg-opacity-25 p-2 rounded-3 me-3 text-white">
                                <i class="fas fa-wallet fa-lg"></i>
                            </div>
                            <span class="text-white-50 fw-bold text-uppercase small">Omset Harian (Cash)</span>
                        </div>
                        <h2 class="fw-bold mb-0">Rp {{ number_format($omset_today, 0, ',', '.') }}</h2>
                    </div>
                </div>
            </div>

            {{-- Transaksi Count --}}
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-3 text-primary">
                                <i class="fas fa-receipt fa-lg"></i>
                            </div>
                            <span class="text-muted fw-bold text-uppercase small">Total Struk</span>
                        </div>
                        <h2 class="fw-bold mb-0 text-dark">{{ $trx_today }} <span class="fs-6 text-muted fw-normal">Transaksi</span></h2>
                    </div>
                </div>
            </div>

            {{-- Stok Ready --}}
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-warning bg-opacity-10 p-2 rounded-3 me-3 text-warning">
                                <i class="fas fa-boxes fa-lg"></i>
                            </div>
                            <span class="text-muted fw-bold text-uppercase small">Unit Ready Stok</span>
                        </div>
                        <h2 class="fw-bold mb-0 text-dark">{{ $stok_ready }} <span class="fs-6 text-muted fw-normal">Unit</span></h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            {{-- TOMBOL KASIR CEPAT --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-0 p-4">
                        <h5 class="fw-bold mb-0">Menu Kasir</h5>
                    </div>
                    <div class="card-body p-4 pt-0">
                        <div class="d-grid gap-3">
                            <a href="{{ route('sales.input') }}" class="btn btn-primary btn-lg py-3 rounded-4 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2">
                                <i class="fas fa-cart-plus"></i> Transaksi Baru
                            </a>
                            <a href="#" class="btn btn-outline-dark btn-lg py-3 rounded-4 fw-bold d-flex align-items-center justify-content-center gap-2">
                                <i class="fas fa-barcode"></i> Cek Harga / Scan
                            </a>
                            <a href="#" class="btn btn-outline-danger btn-lg py-3 rounded-4 fw-bold d-flex align-items-center justify-content-center gap-2">
                                <i class="fas fa-door-closed"></i> Tutup Kasir (Z-Report)
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- LIVE TRANSACTIONS FEED --}}
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-0 p-4 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">Aktivitas Terkini</h5>
                        <span class="badge bg-danger animate__animated animate__pulse animate__infinite">Live</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Waktu</th>
                                    <th>No. Struk</th>
                                    <th>Kasir</th>
                                    <th>Customer</th>
                                    <th>Metode</th>
                                    <th class="text-end pe-4">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($last_transactions as $trx)
                                <tr>
                                    <td class="ps-4 font-monospace text-muted">{{ $trx['time'] }}</td>
                                    <td class="fw-bold text-dark">{{ $trx['invoice'] }}</td>
                                    <td><span class="badge bg-secondary rounded-pill px-3">{{ $trx['kasir'] }}</span></td>
                                    <td>{{ $trx['customer'] }}</td>
                                    <td><span class="badge border text-dark">{{ $trx['method'] }}</span></td>
                                    <td class="text-end pe-4 fw-bold text-success">Rp {{ $trx['total'] }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="text-center py-5 text-muted">Belum ada transaksi hari ini</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Simple Clock
        setInterval(() => {
            const now = new Date();
            document.getElementById('clock').innerText = now.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        }, 1000);
    </script>
</x-layouts.master>