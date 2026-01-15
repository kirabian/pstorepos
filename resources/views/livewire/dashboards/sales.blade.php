<x-layouts.master>
    @slot('title', 'Dashboard Sales Force')

    <div class="container-fluid p-0 animate__animated animate__fadeIn">
        {{-- HEADER SECTION --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div>
                <h1 class="h3 fw-bolder text-dark mb-1">
                    Halo, {{ Auth::user()->nama_lengkap ?? 'Sales Force' }}! 👋
                </h1>
                <div class="d-flex align-items-center gap-2 text-muted small">
                    <span class="badge bg-dark rounded-pill px-3">SALES FORCE</span>
                    <span class="badge bg-info bg-opacity-10 text-info border border-info-subtle rounded-pill px-3">
                        <i class="fas fa-store me-1"></i> {{ strtoupper($cabang ?? 'PSTORE PUSAT') }}
                    </span>
                </div>
            </div>

            <div class="text-end d-none d-md-block">
                <p class="mb-0 fw-bold text-dark">{{ now()->translatedFormat('l, d F Y') }}</p>
                <p class="mb-0 small text-muted">Let's make some profit today!</p>
            </div>
        </div>

        {{-- STATS CARDS (RINGKASAN) --}}
        <div class="row g-3 mb-4">
            {{-- Card 1: Penjualan Hari Ini --}}
            <div class="col-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 h-100 hover-lift">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-shape bg-success bg-opacity-10 text-success rounded-3 me-3">
                                <i class="fas fa-check-circle fa-lg"></i>
                            </div>
                            <span class="text-uppercase small fw-bold text-muted">Hari Ini</span>
                        </div>
                        <h3 class="fw-bold mb-0">{{ $penjualan_hari_ini ?? 0 }} Unit</h3>
                        <small class="text-success fw-bold"><i class="fas fa-arrow-up me-1"></i>Terjual</small>
                    </div>
                </div>
            </div>

            {{-- Card 2: Omset Hari Ini --}}
            <div class="col-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 h-100 hover-lift">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-shape bg-primary bg-opacity-10 text-primary rounded-3 me-3">
                                <i class="fas fa-wallet fa-lg"></i>
                            </div>
                            <span class="text-uppercase small fw-bold text-muted">Omset</span>
                        </div>
                        <h3 class="fw-bold mb-0">Rp {{ number_format(($omset_hari_ini ?? 0) / 1000000, 1, ',', '.') }} Jt</h3>
                        <small class="text-muted">Total Nilai</small>
                    </div>
                </div>
            </div>

            {{-- Card 3: Progress Target --}}
            <div class="col-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 h-100 hover-lift">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center">
                                <div class="icon-shape bg-warning bg-opacity-10 text-warning rounded-3 me-3">
                                    <i class="fas fa-bullseye fa-lg"></i>
                                </div>
                                <span class="text-uppercase small fw-bold text-muted">Target</span>
                            </div>
                            <span class="badge bg-warning bg-opacity-25 text-warning-emphasis rounded-pill">
                                {{ isset($target_bulan) && $target_bulan > 0 ? round(($capaian_bulan / $target_bulan) * 100) : 0 }}%
                            </span>
                        </div>
                        <h4 class="fw-bold mb-2">{{ $capaian_bulan ?? 0 }} / {{ $target_bulan ?? 0 }}</h4>
                        <div class="progress rounded-pill" style="height: 6px;">
                            <div class="progress-bar bg-warning" role="progressbar" 
                                 style="width: {{ isset($target_bulan) && $target_bulan > 0 ? ($capaian_bulan / $target_bulan) * 100 : 0 }}%">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 4: Estimasi Insentif --}}
            <div class="col-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 h-100 hover-lift bg-dark text-white">
                    <div class="card-body position-relative overflow-hidden">
                        <div class="d-flex align-items-center mb-3 position-relative z-1">
                            <div class="icon-shape bg-white bg-opacity-25 text-white rounded-3 me-3">
                                <i class="fas fa-coins fa-lg"></i>
                            </div>
                            <span class="text-uppercase small fw-bold text-white-50">Insentif</span>
                        </div>
                        <h3 class="fw-bold mb-0 position-relative z-1">Rp {{ number_format(($insentif_estimasi ?? 0) / 1000, 0, ',', '.') }} K</h3>
                        <small class="text-white-50 position-relative z-1">Estimasi Bonus</small>
                        
                        {{-- Decoration --}}
                        <i class="fas fa-money-bill-wave position-absolute bottom-0 end-0 text-white opacity-10" style="font-size: 5rem; transform: rotate(-20deg) translate(10px, 20px);"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            {{-- KOLOM KIRI: AKSI & TABEL --}}
            <div class="col-lg-8">
                
                {{-- Quick Actions --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold text-dark mb-3">Aksi Cepat</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <a href="{{ route('sales.input') }}" class="btn btn-dark w-100 py-3 rounded-4 d-flex align-items-center justify-content-center gap-3 hover-scale shadow">
                                    <i class="fas fa-plus-circle fs-4 text-info"></i>
                                    <div class="text-start lh-1">
                                        <span class="d-block fw-bold fs-6">Input Penjualan</span>
                                        <span class="d-block mt-1 small text-white-50" style="font-size: 0.75rem;">Catat transaksi baru</span>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-6">
                                <a href="{{ route('stok.index') }}" class="btn btn-light border w-100 py-3 rounded-4 d-flex align-items-center justify-content-center gap-3 hover-bg-gray">
                                    <i class="fas fa-search fs-4 text-dark"></i>
                                    <div class="text-start lh-1">
                                        <span class="d-block fw-bold fs-6 text-dark">Cek Stok Cabang</span>
                                        <span class="d-block mt-1 small text-muted" style="font-size: 0.75rem;">Lihat ketersediaan unit</span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Recent Transactions Table --}}
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-0 p-4 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold text-dark mb-0">Transaksi Terakhir</h5>
                        <a href="{{ route('sales.history') }}" class="btn btn-sm btn-light rounded-pill px-3 fw-bold small text-muted">Lihat Semua</a>
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
                                @forelse($recent_sales ?? [] as $sale)
                                <tr>
                                    <td class="ps-4 text-muted fw-bold small">{{ $sale['time'] }}</td>
                                    <td class="fw-bold text-dark">{{ $sale['customer'] }}</td>
                                    <td>
                                        <span class="d-inline-block text-truncate" style="max-width: 150px;">
                                            {{ $sale['unit'] }}
                                        </span>
                                    </td>
                                    <td class="text-dark fw-bold">{{ $sale['harga'] }}</td>
                                    <td class="text-end pe-4">
                                        @php
                                            $statusClass = match($sale['status']) {
                                                'Lunas' => 'success',
                                                'Proses' => 'warning',
                                                'Booking' => 'info',
                                                default => 'secondary'
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $statusClass }}-subtle text-{{ $statusClass }} border border-{{ $statusClass }}-subtle rounded-pill px-3">
                                            {{ $sale['status'] }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <img src="https://cdni.iconscout.com/illustration/premium/thumb/empty-state-2130362-1800926.png" alt="Empty" style="width: 100px; opacity: 0.5">
                                        <p class="text-muted mt-2 small">Belum ada transaksi hari ini.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: INFO & SIDEBAR --}}
            <div class="col-lg-4">
                
                {{-- Promo Banner --}}
                <div class="card border-0 shadow-sm rounded-4 bg-primary text-white mb-4 position-relative overflow-hidden">
                    <div class="card-body p-4 position-relative z-1">
                        <span class="badge bg-white text-primary fw-bold mb-3 px-2 py-1"><i class="fas fa-bell me-1"></i> INFO PENTING</span>
                        <h5 class="fw-bold mb-2">Promo Weekend Deal!</h5>
                        <p class="small text-white-50 mb-3 lh-sm">Dapatkan insentif tambahan <strong>Rp 50.000</strong> untuk setiap penjualan iPhone 13 series khusus Sabtu & Minggu.</p>
                        <button class="btn btn-sm btn-white text-primary fw-bold rounded-pill px-4 shadow-sm">Detail Promo</button>
                    </div>
                    {{-- Decorative Blob --}}
                    <div class="position-absolute top-0 end-0 p-3 opacity-25">
                        <i class="fas fa-tags fa-5x" style="transform: rotate(15deg);"></i>
                    </div>
                </div>

                {{-- Top Selling List --}}
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white border-0 p-4 pb-0">
                        <h6 class="fw-bold text-dark mb-0">🔥 Paling Laris di {{ $cabang ?? 'Cabang' }}</h6>
                    </div>
                    <div class="card-body p-0 pt-2">
                        <div class="list-group list-group-flush rounded-bottom-4">
                            {{-- Item 1 --}}
                            <div class="list-group-item border-0 px-4 py-3 d-flex align-items-center gap-3 hover-bg-light">
                                <div class="bg-warning bg-opacity-10 text-warning p-2 rounded-3 fw-bold text-center" style="width: 40px; height: 40px; display: grid; place-items: center;">1</div>
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold text-dark mb-0 text-truncate" style="max-width: 180px;">iPhone 11 64GB</h6>
                                    <small class="text-muted" style="font-size: 0.75rem;">32 Unit terjual minggu ini</small>
                                </div>
                                <i class="fas fa-chevron-right text-muted small"></i>
                            </div>
                            {{-- Item 2 --}}
                            <div class="list-group-item border-0 px-4 py-3 d-flex align-items-center gap-3 hover-bg-light">
                                <div class="bg-light text-dark p-2 rounded-3 fw-bold text-center" style="width: 40px; height: 40px; display: grid; place-items: center;">2</div>
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold text-dark mb-0 text-truncate" style="max-width: 180px;">Samsung A55 5G</h6>
                                    <small class="text-muted" style="font-size: 0.75rem;">28 Unit terjual minggu ini</small>
                                </div>
                            </div>
                            {{-- Item 3 --}}
                            <div class="list-group-item border-0 px-4 py-3 d-flex align-items-center gap-3 hover-bg-light">
                                <div class="bg-light text-dark p-2 rounded-3 fw-bold text-center" style="width: 40px; height: 40px; display: grid; place-items: center;">3</div>
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold text-dark mb-0 text-truncate" style="max-width: 180px;">Xiaomi Redmi Note 13</h6>
                                    <small class="text-muted" style="font-size: 0.75rem;">25 Unit terjual minggu ini</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Custom CSS untuk halaman ini --}}
    <style>
        .icon-shape {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .hover-lift { transition: all 0.3s ease; }
        .hover-lift:hover { transform: translateY(-5px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; }
        
        .hover-scale { transition: all 0.2s ease; }
        .hover-scale:hover { transform: scale(1.02); }
        
        .hover-bg-gray:hover { background-color: #f8f9fa; border-color: #dee2e6 !important; }
        
        /* Perbaiki tampilan table pada mobile */
        .table > :not(caption) > * > * { padding: 1rem 0.5rem; }
    </style>
</x-layouts.master>