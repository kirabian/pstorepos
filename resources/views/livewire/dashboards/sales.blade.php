<div class="animate__animated animate__fadeIn">
    
    {{-- HEADER SECTION --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5 gap-4">
        <div>
            <h1 class="display-6 fw-black text-dark mb-1 tracking-tight">
                Halo, {{ Auth::user()->nama_lengkap }}! 👋
            </h1>
            <div class="d-flex align-items-center gap-2 text-secondary fw-bold small text-uppercase tracking-wider">
                <span class="badge bg-dark text-white rounded-pill px-3 py-2">
                    SALES FORCE
                </span>
                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle rounded-pill px-3 py-2">
                    <i class="fas fa-store me-1"></i> {{ strtoupper($cabang) }}
                </span>
            </div>
        </div>

        <div class="text-end d-none d-md-block">
            <p class="mb-0 fw-bold text-dark">{{ now()->format('l, d F Y') }}</p>
            <p class="mb-0 small text-muted">Ayo kejar targetmu hari ini!</p>
        </div>
    </div>

    {{-- STATS CARDS --}}
    <div class="row g-4 mb-5">
        {{-- Card 1: Penjualan Hari Ini --}}
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden hover-lift transition-all">
                <div class="card-body p-4 position-relative">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div class="bg-success bg-opacity-10 p-3 rounded-4 text-success">
                            <i class="fas fa-check-circle fa-lg"></i>
                        </div>
                        <span class="badge bg-light text-muted border rounded-pill extra-small">Hari Ini</span>
                    </div>
                    <h3 class="fw-black text-dark mb-1 tracking-tight">{{ $penjualan_hari_ini }} Unit</h3>
                    <p class="text-secondary small fw-bold text-uppercase mb-0 tracking-wide">Terjual</p>
                </div>
            </div>
        </div>

        {{-- Card 2: Omset Hari Ini --}}
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden hover-lift transition-all">
                <div class="card-body p-4 position-relative">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div class="bg-primary bg-opacity-10 p-3 rounded-4 text-primary">
                            <i class="fas fa-wallet fa-lg"></i>
                        </div>
                        <span class="badge bg-light text-muted border rounded-pill extra-small">Omset Harian</span>
                    </div>
                    <h3 class="fw-black text-dark mb-1 tracking-tight">Rp {{ number_format($omset_hari_ini / 1000000, 1, ',', '.') }} Jt</h3>
                    <p class="text-secondary small fw-bold text-uppercase mb-0 tracking-wide">Total Nilai</p>
                </div>
            </div>
        </div>

        {{-- Card 3: Capaian Target (Progress) --}}
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden hover-lift transition-all">
                <div class="card-body p-4 position-relative">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="bg-warning bg-opacity-10 p-3 rounded-4 text-warning">
                            <i class="fas fa-bullseye fa-lg"></i>
                        </div>
                        <span class="badge bg-warning bg-opacity-25 text-warning-emphasis border border-warning-subtle rounded-pill extra-small">
                            {{ round(($capaian_bulan / $target_bulan) * 100) }}% Achieved
                        </span>
                    </div>
                    <div class="mt-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small fw-bold text-dark">{{ $capaian_bulan }} / {{ $target_bulan }} Unit</span>
                        </div>
                        <div class="progress rounded-pill" style="height: 8px;">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: {{ ($capaian_bulan / $target_bulan) * 100 }}%"></div>
                        </div>
                        <p class="text-secondary small fw-bold text-uppercase mt-2 mb-0 tracking-wide">Target Bulanan</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 4: Estimasi Insentif --}}
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden hover-lift transition-all bg-dark text-white">
                <div class="card-body p-4 position-relative">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div class="bg-white bg-opacity-25 p-3 rounded-4 text-white">
                            <i class="fas fa-coins fa-lg"></i>
                        </div>
                        <span class="badge bg-white bg-opacity-25 text-white border border-white rounded-pill extra-small">Estimasi</span>
                    </div>
                    <h3 class="fw-black text-white mb-1 tracking-tight">Rp {{ number_format($insentif_estimasi / 1000000, 1, ',', '.') }} Jt</h3>
                    <p class="text-white-50 small fw-bold text-uppercase mb-0 tracking-wide">Komisi / Bonus</p>
                </div>
            </div>
        </div>
    </div>

    {{-- MAIN CONTENT GRID --}}
    <div class="row g-4">
        {{-- Quick Actions & Today's Sales --}}
        <div class="col-lg-8">
            {{-- Quick Action Buttons --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-dark mb-3">Aksi Cepat</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <button class="btn btn-dark w-100 py-3 rounded-4 d-flex align-items-center justify-content-center gap-3 hover-scale shadow-sm">
                                <i class="fas fa-plus-circle fs-4 text-info"></i>
                                <div class="text-start">
                                    <span class="d-block fw-bold fs-6">Input Penjualan Baru</span>
                                    <span class="d-block small text-white-50">Catat transaksi customer</span>
                                </div>
                            </button>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-light border w-100 py-3 rounded-4 d-flex align-items-center justify-content-center gap-3 hover-bg-light transition-all">
                                <i class="fas fa-search fs-4 text-dark"></i>
                                <div class="text-start">
                                    <span class="d-block fw-bold fs-6 text-dark">Cek Stok Cabang</span>
                                    <span class="d-block small text-muted">Lihat ketersediaan unit</span>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recent Transactions Table --}}
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-bottom border-light-subtle p-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-dark mb-0">Transaksi Terakhir Anda</h5>
                    <a href="#" class="btn btn-sm btn-light rounded-pill px-3 fw-bold small">Lihat Semua</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3 small fw-bold text-uppercase text-secondary">Jam</th>
                                <th class="py-3 small fw-bold text-uppercase text-secondary">Customer</th>
                                <th class="py-3 small fw-bold text-uppercase text-secondary">Unit</th>
                                <th class="py-3 small fw-bold text-uppercase text-secondary">Nominal</th>
                                <th class="py-3 small fw-bold text-uppercase text-secondary text-end pe-4">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recent_sales as $sale)
                            <tr>
                                <td class="ps-4 text-muted fw-bold small">{{ $sale['time'] }}</td>
                                <td class="fw-bold text-dark">{{ $sale['customer'] }}</td>
                                <td>{{ $sale['unit'] }}</td>
                                <td class="text-dark fw-bold">{{ $sale['harga'] }}</td>
                                <td class="text-end pe-4">
                                    @if($sale['status'] == 'Lunas')
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Lunas</span>
                                    @elseif($sale['status'] == 'Proses')
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill">Proses</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill">Booking</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">Belum ada transaksi hari ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Sidebar Right: Info --}}
        <div class="col-lg-4">
            {{-- Promo / Info Card --}}
            <div class="card border-0 shadow-sm rounded-4 bg-primary text-white mb-4 position-relative overflow-hidden">
                <div class="card-body p-4 position-relative z-1">
                    <span class="badge bg-white text-primary fw-bold mb-3">INFO PENTING</span>
                    <h5 class="fw-bold mb-2">Promo Weekend Deal!</h5>
                    <p class="small text-white-50 mb-3">Dapatkan insentif tambahan Rp 50.000 untuk setiap penjualan iPhone 13 series khusus hari Sabtu & Minggu ini.</p>
                    <button class="btn btn-sm btn-white text-primary fw-bold rounded-pill px-3">Detail Promo</button>
                </div>
                {{-- Decorative Elements --}}
                <div class="position-absolute top-0 end-0 p-3 opacity-25">
                    <i class="fas fa-tags fa-5x"></i>
                </div>
            </div>

            {{-- Top Selling Products in Branch --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-transparent border-0 p-4 pb-0">
                    <h6 class="fw-bold text-dark mb-0">Paling Laris di {{ $cabang }}</h6>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item border-light-subtle px-4 py-3 d-flex align-items-center gap-3">
                            <div class="bg-light p-2 rounded-3 text-dark fw-bold text-center" style="width: 40px;">1</div>
                            <div class="flex-grow-1">
                                <h6 class="fw-bold text-dark mb-0">iPhone 11 64GB</h6>
                                <small class="text-muted">32 Unit terjual minggu ini</small>
                            </div>
                        </div>
                        <div class="list-group-item border-light-subtle px-4 py-3 d-flex align-items-center gap-3">
                            <div class="bg-light p-2 rounded-3 text-dark fw-bold text-center" style="width: 40px;">2</div>
                            <div class="flex-grow-1">
                                <h6 class="fw-bold text-dark mb-0">Samsung A55 5G</h6>
                                <small class="text-muted">28 Unit terjual minggu ini</small>
                            </div>
                        </div>
                        <div class="list-group-item border-light-subtle px-4 py-3 d-flex align-items-center gap-3">
                            <div class="bg-light p-2 rounded-3 text-dark fw-bold text-center" style="width: 40px;">3</div>
                            <div class="flex-grow-1">
                                <h6 class="fw-bold text-dark mb-0">Xiaomi Redmi Note 13</h6>
                                <small class="text-muted">25 Unit terjual minggu ini</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .hover-lift:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important; }
        .hover-bg-light:hover { background-color: #f8f9fa; border-color: #dee2e6 !important; }
        .hover-scale:hover { transform: scale(1.02); transition: 0.3s; }
        .tracking-tight { letter-spacing: -0.025em; }
        .tracking-wide { letter-spacing: 0.025em; }
        .fw-black { font-weight: 900; }
        .extra-small { font-size: 0.7rem; }
    </style>
</div>