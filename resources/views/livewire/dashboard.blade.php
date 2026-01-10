<div class="animate__animated animate__fadeIn">
    
    {{-- HEADER SECTION --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5 gap-4">
        <div>
            <h1 class="display-6 fw-black text-dark mb-1 tracking-tight">
                Selamat Datang, {{ Auth::user()->nama_lengkap }}
            </h1>
            <div class="d-flex align-items-center gap-2 text-secondary fw-bold small text-uppercase tracking-wider">
                <span class="badge bg-dark text-white rounded-pill px-3 py-2">
                    ROLE: {{ strtoupper(str_replace('_', ' ', Auth::user()->role)) }}
                </span>
                
                @if($mode === 'distributor')
                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle rounded-pill px-3 py-2">
                        <i class="fas fa-building me-1"></i> {{ strtoupper($location_name) }}
                    </span>
                @elseif($mode === 'gudang')
                    <span class="badge bg-warning bg-opacity-10 text-dark border border-warning-subtle rounded-pill px-3 py-2">
                        <i class="fas fa-warehouse me-1"></i> {{ strtoupper($location_name) }}
                    </span>
                @else
                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle rounded-pill px-3 py-2">
                        HEADQUARTERS
                    </span>
                @endif
            </div>
        </div>

        <div class="d-flex gap-2">
            <div class="text-end d-none d-md-block">
                <p class="mb-0 fw-bold text-dark">{{ now()->format('l, d F Y') }}</p>
                <p class="mb-0 small text-muted">System Status: <span class="text-success fw-bold">Online</span></p>
            </div>
        </div>
    </div>

    {{-- CONDITIONAL DASHBOARD CONTENT --}}

    {{-- ========================================== --}}
    {{-- SCENARIO A: DASHBOARD TIPE DISTRIBUTOR --}}
    {{-- ========================================== --}}
    @if($mode === 'distributor')
        
        {{-- Statistic Cards Distributor --}}
        <div class="row g-4 mb-5">
            @foreach($stats as $stat)
            <div class="col-12 col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden hover-lift transition-all">
                    <div class="card-body p-4 position-relative">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div class="bg-{{ $stat['color'] }} bg-opacity-10 p-3 rounded-4 text-{{ $stat['color'] }}">
                                <i class="fas {{ $stat['icon'] }} fa-lg"></i>
                            </div>
                            <span class="badge bg-light text-muted border rounded-pill extra-small">
                                {{ $stat['trend'] }}
                            </span>
                        </div>
                        <h3 class="fw-black text-dark mb-1 tracking-tight">{{ $stat['value'] }}</h3>
                        <p class="text-secondary small fw-bold text-uppercase mb-0 tracking-wide">{{ $stat['label'] }}</p>
                        
                        {{-- Decorative Blob --}}
                        <div class="position-absolute bottom-0 end-0 mb-n3 me-n3 opacity-10">
                            <i class="fas {{ $stat['icon'] }}" style="font-size: 5rem; color: var(--bs-{{ $stat['color'] }});"></i>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Main Operational Area (Distributor Fokus ke Pengiriman) --}}
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-5 overflow-hidden h-100">
                    <div class="card-header bg-white border-bottom border-light-subtle p-4 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold text-dark mb-0">Distribusi Terkini</h5>
                        <button class="btn btn-sm btn-light rounded-pill px-3 fw-bold small">Lihat Semua</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3 small fw-bold text-uppercase text-secondary">ID Pengiriman</th>
                                    <th class="py-3 small fw-bold text-uppercase text-secondary">Tujuan Cabang</th>
                                    <th class="py-3 small fw-bold text-uppercase text-secondary">Status</th>
                                    <th class="py-3 small fw-bold text-uppercase text-secondary text-end pe-4">Total Item</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Dummy Data Distributor --}}
                                <tr>
                                    <td class="ps-4 fw-bold text-primary">#TRX-9981</td>
                                    <td><i class="fas fa-store text-muted me-2"></i>Cabang Condet</td>
                                    <td><span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill">In Transit</span></td>
                                    <td class="text-end pe-4 fw-bold">50 Unit</td>
                                </tr>
                                <tr>
                                    <td class="ps-4 fw-bold text-primary">#TRX-9982</td>
                                    <td><i class="fas fa-store text-muted me-2"></i>Cabang Bogor</td>
                                    <td><span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Delivered</span></td>
                                    <td class="text-end pe-4 fw-bold">120 Unit</td>
                                </tr>
                                <tr>
                                    <td class="ps-4 fw-bold text-primary">#TRX-9983</td>
                                    <td><i class="fas fa-store text-muted me-2"></i>PStore Meruya</td>
                                    <td><span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill">Packing</span></td>
                                    <td class="text-end pe-4 fw-bold">15 Unit</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-5 bg-dark text-white h-100 position-relative overflow-hidden">
                    <div class="card-body p-5 d-flex flex-column justify-content-center text-center z-1">
                        <div class="mb-4">
                            <div class="bg-white bg-opacity-25 p-4 rounded-circle d-inline-flex mx-auto mb-3">
                                <i class="fas fa-plus fa-2x"></i>
                            </div>
                            <h4 class="fw-bold mb-2">Buat Distribusi Baru</h4>
                            <p class="text-white-50 small mb-0">Kirim stok ke cabang-cabang yang membutuhkan supply.</p>
                        </div>
                        <button class="btn btn-white text-dark fw-bold rounded-pill py-3 w-100 hover-scale shadow-lg">
                            <i class="fas fa-box me-2"></i> Input Barang Keluar
                        </button>
                    </div>
                    {{-- Abstract BG --}}
                    <div class="position-absolute top-0 start-0 w-100 h-100" 
                         style="background: linear-gradient(45deg, rgba(0,0,0,0.5), transparent); pointer-events: none;"></div>
                </div>
            </div>
        </div>

    {{-- ========================================== --}}
    {{-- SCENARIO B: DASHBOARD TIPE GUDANG (WAREHOUSE) --}}
    {{-- ========================================== --}}
    @elseif($mode === 'gudang')

        {{-- Statistic Cards Gudang (Bottom Border Accent) --}}
        <div class="row g-4 mb-5">
            @foreach($stats as $stat)
            <div class="col-12 col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden hover-lift transition-all" 
                     style="border-bottom: 4px solid var(--bs-{{ $stat['color'] }}) !important;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="bg-light p-2 rounded-3 text-{{ $stat['color'] }}">
                                <i class="fas {{ $stat['icon'] }} fa-lg"></i>
                            </div>
                            <h6 class="text-secondary small fw-bold text-uppercase mb-0 tracking-wide">{{ $stat['label'] }}</h6>
                        </div>
                        <div class="d-flex justify-content-between align-items-end">
                            <h2 class="fw-black text-dark mb-0 tracking-tight">{{ $stat['value'] }}</h2>
                            <span class="text-{{ $stat['color'] }} small fw-bold">
                                {{ $stat['trend'] }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Warehouse Operations Grid --}}
        <div class="row g-4">
            {{-- Quick Actions Grid --}}
            <div class="col-12">
                <div class="d-flex flex-wrap gap-3">
                    <button class="btn btn-white border shadow-sm rounded-4 py-3 px-4 d-flex align-items-center gap-3 flex-fill hover-bg-light transition-all">
                        <div class="bg-success bg-opacity-10 text-success p-2 rounded-circle">
                            <i class="fas fa-arrow-down"></i>
                        </div>
                        <div class="text-start">
                            <h6 class="fw-bold text-dark mb-0">Barang Masuk</h6>
                            <small class="text-muted">Input stok fisik baru</small>
                        </div>
                    </button>
                    <button class="btn btn-white border shadow-sm rounded-4 py-3 px-4 d-flex align-items-center gap-3 flex-fill hover-bg-light transition-all">
                        <div class="bg-danger bg-opacity-10 text-danger p-2 rounded-circle">
                            <i class="fas fa-arrow-up"></i>
                        </div>
                        <div class="text-start">
                            <h6 class="fw-bold text-dark mb-0">Barang Keluar</h6>
                            <small class="text-muted">Mutasi / Pemusnahan</small>
                        </div>
                    </button>
                    <button class="btn btn-white border shadow-sm rounded-4 py-3 px-4 d-flex align-items-center gap-3 flex-fill hover-bg-light transition-all">
                        <div class="bg-warning bg-opacity-10 text-warning p-2 rounded-circle">
                            <i class="fas fa-search"></i>
                        </div>
                        <div class="text-start">
                            <h6 class="fw-bold text-dark mb-0">Stock Opname</h6>
                            <small class="text-muted">Cek fisik rutin</small>
                        </div>
                    </button>
                    <button class="btn btn-white border shadow-sm rounded-4 py-3 px-4 d-flex align-items-center gap-3 flex-fill hover-bg-light transition-all">
                        <div class="bg-info bg-opacity-10 text-info p-2 rounded-circle">
                            <i class="fas fa-barcode"></i>
                        </div>
                        <div class="text-start">
                            <h6 class="fw-bold text-dark mb-0">Cetak Label</h6>
                            <small class="text-muted">Manajemen Barcode</small>
                        </div>
                    </button>
                </div>
            </div>

            {{-- Recent Activity Log (Gudang Style) --}}
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-5 h-100">
                    <div class="card-header bg-transparent border-0 p-4 pb-2">
                        <h5 class="fw-bold text-dark">Aktivitas Gudang</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            {{-- Dummy Activity --}}
                            <div class="list-group-item border-light-subtle px-4 py-3 d-flex align-items-center gap-3">
                                <div class="bg-light p-2 rounded-3 text-dark fw-bold small text-center" style="width: 50px;">
                                    10:42
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold text-dark mb-0">Penerimaan Barang PO-2026-001</h6>
                                    <small class="text-muted">Diterima oleh: <span class="text-dark fw-semibold">Budi Santoso</span> • 40 Unit iPhone 15</small>
                                </div>
                                <span class="badge bg-success-subtle text-success rounded-pill">Selesai</span>
                            </div>
                            <div class="list-group-item border-light-subtle px-4 py-3 d-flex align-items-center gap-3">
                                <div class="bg-light p-2 rounded-3 text-dark fw-bold small text-center" style="width: 50px;">
                                    09:15
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold text-dark mb-0">Stock Opname Rak A-04</h6>
                                    <small class="text-muted">Dicek oleh: <span class="text-dark fw-semibold">Arcisbian</span> • Selisih 0</small>
                                </div>
                                <span class="badge bg-info-subtle text-info rounded-pill">Verified</span>
                            </div>
                            <div class="list-group-item border-light-subtle px-4 py-3 d-flex align-items-center gap-3">
                                <div class="bg-light p-2 rounded-3 text-dark fw-bold small text-center" style="width: 50px;">
                                    08:30
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold text-dark mb-0">Laporan Barang Rusak</h6>
                                    <small class="text-muted">Pelapor: <span class="text-dark fw-semibold">Security</span> • Layar Pecah (2 Unit)</small>
                                </div>
                                <span class="badge bg-danger-subtle text-danger rounded-pill">Pending</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Inventory Alerts --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-5 h-100">
                    <div class="card-header bg-warning bg-opacity-10 border-0 p-4">
                        <h6 class="fw-bold text-dark mb-0"><i class="fas fa-exclamation-triangle me-2 text-warning"></i> Low Stock Alerts</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center gap-3 mb-3 border-bottom border-light-subtle pb-3">
                            <img src="https://via.placeholder.com/50" class="rounded-3" alt="Product">
                            <div class="flex-grow-1">
                                <h6 class="fw-bold text-dark mb-0">Samsung S24 Ultra</h6>
                                <div class="progress mt-2" style="height: 6px;">
                                    <div class="progress-bar bg-danger" role="progressbar" style="width: 15%"></div>
                                </div>
                            </div>
                            <div class="text-end">
                                <h6 class="fw-bold text-danger mb-0">3 Unit</h6>
                                <small class="text-muted extra-small">Sisa</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3 mb-3 border-bottom border-light-subtle pb-3">
                            <img src="https://via.placeholder.com/50" class="rounded-3" alt="Product">
                            <div class="flex-grow-1">
                                <h6 class="fw-bold text-dark mb-0">Xiaomi 14</h6>
                                <div class="progress mt-2" style="height: 6px;">
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: 30%"></div>
                                </div>
                            </div>
                            <div class="text-end">
                                <h6 class="fw-bold text-warning mb-0">8 Unit</h6>
                                <small class="text-muted extra-small">Sisa</small>
                            </div>
                        </div>
                        <button class="btn btn-outline-dark w-100 rounded-pill btn-sm fw-bold">Buat Permintaan Stok</button>
                    </div>
                </div>
            </div>
        </div>

    {{-- ========================================== --}}
    {{-- SCENARIO C: DEFAULT DASHBOARD (FALLBACK/ADMIN) --}}
    {{-- ========================================== --}}
    @else
        <div class="card border-0 shadow-sm rounded-5 p-5 text-center">
            <div class="mb-4">
                <img src="{{ asset('images/logo-pstore.png') }}" alt="PSTORE" style="height: 60px; opacity: 0.5;">
            </div>
            <h3 class="fw-bold text-dark">Dashboard Utama</h3>
            <p class="text-muted">Silakan pilih menu di sidebar untuk mulai mengelola sistem.</p>
        </div>
    @endif

    <style>
        .hover-lift:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important; }
        .hover-bg-light:hover { background-color: #f8f9fa; border-color: #dee2e6 !important; }
        .hover-scale:hover { transform: scale(1.02); transition: 0.3s; }
        .tracking-tight { letter-spacing: -0.025em; }
        .tracking-wide { letter-spacing: 0.025em; }
        .tracking-wider { letter-spacing: 0.05em; }
        .fw-black { font-weight: 900; }
        .extra-small { font-size: 0.7rem; }
    </style>
</div>