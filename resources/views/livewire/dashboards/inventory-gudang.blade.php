<div class="animate__animated animate__fadeIn">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h2 class="fw-black text-dark mb-1">Manajemen Gudang</h2>
            <p class="text-secondary mb-0">
                <i class="fas fa-warehouse me-2"></i> Lokasi: <strong>{{ $lokasi }}</strong>
            </p>
        </div>
        <span class="badge bg-info bg-opacity-10 text-info px-3 py-2 rounded-pill">
            STAFF GUDANG
        </span>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-lg-3 col-6">
            <div class="p-4 bg-white shadow-sm rounded-4 text-center h-100 border border-light-subtle">
                <i class="fas fa-boxes fa-2x text-secondary mb-3"></i>
                <h3 class="fw-bold mb-0">{{ number_format($total_sku) }}</h3>
                <small class="text-muted">Total SKU Fisik</small>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="p-4 bg-danger bg-opacity-10 shadow-sm rounded-4 text-center h-100 border border-danger border-opacity-25">
                <i class="fas fa-exclamation-triangle fa-2x text-danger mb-3"></i>
                <h3 class="fw-bold text-danger mb-0">{{ $stock_low }}</h3>
                <small class="text-danger fw-bold">Stok Menipis</small>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="p-4 bg-dark text-white shadow-sm rounded-4 h-100 d-flex flex-column justify-content-center position-relative overflow-hidden">
                <div class="position-relative z-1">
                    <h5 class="fw-bold mb-1">Jadwal Stock Opname</h5>
                    <p class="opacity-75 mb-0">{{ $jadwal_opname }}</p>
                    <button class="btn btn-light btn-sm rounded-pill px-3 fw-bold mt-3">Mulai Scan</button>
                </div>
                <i class="fas fa-barcode position-absolute end-0 bottom-0 mb-n2 me-3 opacity-10" style="font-size: 6rem;"></i>
            </div>
        </div>
    </div>

    <h5 class="fw-bold text-dark mb-3">Manajemen Rak</h5>
    <div class="row g-3">
        @for($i=1; $i<=4; $i++)
        <div class="col-md-3">
            <div class="p-3 border rounded-3 bg-white d-flex justify-content-between align-items-center">
                <div>
                    <span class="fw-bold d-block">Rak A-0{{ $i }}</span>
                    <small class="text-muted">Elektronik</small>
                </div>
                <span class="badge bg-success bg-opacity-10 text-success">Penuh</span>
            </div>
        </div>
        @endfor
    </div>
</div>