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
                            <a href="{{ route('sales.input') }}" class="btn btn-dark w-100 py-3 rounded-4 d-flex align-items-center justify-content-center gap-3 hover-scale shadow-sm">
                                <i class="fas fa-plus-circle fs-4 text-info"></i>
                                <div class="text-start">
                                    <span class="d-block fw-bold fs-6">Input Penjualan Baru</span>
                                    <span class="d-block small text-white-50">Catat transaksi customer</span>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('stok.index') }}" class="btn btn-light border w-100 py-3 rounded-4 d-flex align-items-center justify-content-center gap-3 hover-bg-light transition-all">
                                <i class="fas fa-search fs-4 text-dark"></i>
                                <div class="text-start">
                                    <span class="d-block fw-bold fs-6 text-dark">Cek Stok Cabang</span>
                                    <span class="d-block small text-muted">Lihat ketersediaan unit</span>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recent Transactions Table --}}
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-bottom border-light-subtle p-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-dark mb-0">Transaksi Terakhir Anda</h5>
                    <a href="{{ route('sales.history') }}" class="btn btn-sm btn-light rounded-pill px-3 fw-bold small">Lihat Semua</a>
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

        {{-- Sidebar Right: Ranking Only --}}
        <div class="col-lg-4">
            {{-- Card Ranking Personal (Hanya Ranking Dia) --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4 position-relative overflow-hidden h-100">
                <div class="card-body p-4 text-center d-flex flex-column justify-content-center align-items-center">
                    
                    {{-- Judul Kecil --}}
                    <h6 class="text-uppercase text-secondary fw-bold tracking-wider mb-4" style="font-size: 0.75rem;">
                        <i class="fas fa-chart-line me-1"></i> Peringkat Penjualan
                    </h6>

                    {{-- Icon Piala / Medali --}}
                    <div class="mb-3 position-relative">
                        <div class="bg-warning bg-opacity-10 p-4 rounded-circle d-inline-flex align-items-center justify-content-center" 
                             style="width: 100px; height: 100px; box-shadow: 0 0 0 8px rgba(255, 193, 7, 0.1);">
                            <i class="fas fa-trophy fa-3x text-warning"></i>
                        </div>
                        
                        {{-- Badge Cabang Kecil di sudut ikon --}}
                        <span class="position-absolute bottom-0 start-50 translate-middle-x badge bg-dark text-white rounded-pill border border-2 border-white small">
                            {{ $cabang }}
                        </span>
                    </div>

                    {{-- Angka Ranking Besar --}}
                    <div class="mb-2">
                        <h1 class="display-1 fw-black text-dark mb-0 lh-1">
                            <span class="fs-4 text-muted align-top me-1 fw-bold">#</span>{{ $my_rank ?? '-' }}
                        </h1>
                    </div>

                    {{-- Keterangan Total Sales --}}
                    <p class="text-muted small mb-4">
                        Dari total <span class="fw-bold text-dark">{{ $total_sales_people ?? '-' }} Sales</span> yang aktif di cabang ini.
                    </p>

                    {{-- Pesan Motivasi Berdasarkan Ranking --}}
                    <div class="w-100">
                        @if(isset($my_rank) && $my_rank == 1)
                            <div class="alert alert-success border-0 bg-success-subtle text-success fw-bold py-2 px-3 rounded-pill small mb-0">
                                👑 Luar Biasa! Kamu Juara 1!
                            </div>
                        @elseif(isset($my_rank) && $my_rank <= 5)
                            <div class="alert alert-info border-0 bg-info-subtle text-info fw-bold py-2 px-3 rounded-pill small mb-0">
                                🔥 Keren! Kamu masuk Top 5!
                            </div>
                        @else
                            <div class="alert alert-light border border-light-subtle text-secondary fw-bold py-2 px-3 rounded-pill small mb-0">
                                💪 Semangat! Kejar Top Ranking!
                            </div>
                        @endif
                    </div>

                </div>
                
                {{-- Hiasan Background Abstrak --}}
                <div class="position-absolute top-0 end-0 p-3 opacity-10">
                    <i class="fas fa-crown fa-5x text-secondary"></i>
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