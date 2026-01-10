<div class="animate__animated animate__fadeIn">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h2 class="fw-black text-dark mb-1">Logistik Distributor</h2>
            <p class="text-secondary mb-0">
                <i class="fas fa-truck me-2"></i> Lokasi Kerja: <strong>{{ $lokasi }}</strong>
            </p>
        </div>
        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill">
            STAFF INVENTORY
        </span>
    </div>

    <div class="row g-4 mb-4">
        {{-- Supply Masuk --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-4 d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-uppercase fw-bold text-muted small mb-1">Supply Masuk</p>
                        <h2 class="fw-black text-dark mb-0">{{ $barang_masuk }}</h2>
                    </div>
                    <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle">
                        <i class="fas fa-arrow-down fa-lg"></i>
                    </div>
                </div>
                <div class="card-footer bg-light border-0 py-2 px-4">
                    <small class="text-success fw-bold"><i class="fas fa-level-up-alt"></i> Dari Principal</small>
                </div>
            </div>
        </div>

        {{-- Distribusi Keluar --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-4 d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-uppercase fw-bold text-muted small mb-1">Distribusi Keluar</p>
                        <h2 class="fw-black text-dark mb-0">{{ $barang_keluar }}</h2>
                    </div>
                    <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle">
                        <i class="fas fa-paper-plane fa-lg"></i>
                    </div>
                </div>
                <div class="card-footer bg-light border-0 py-2 px-4">
                    <small class="text-primary fw-bold"><i class="fas fa-store"></i> Ke Cabang</small>
                </div>
            </div>
        </div>

        {{-- Packing Pending --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-warning bg-opacity-10">
                <div class="card-body p-4 d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-uppercase fw-bold text-dark small mb-1">Perlu Packing</p>
                        <h2 class="fw-black text-dark mb-0">{{ $perlu_packing }}</h2>
                    </div>
                    <div class="bg-white text-warning p-3 rounded-circle shadow-sm">
                        <i class="fas fa-box fa-lg"></i>
                    </div>
                </div>
                <div class="card-footer bg-warning bg-opacity-25 border-0 py-2 px-4">
                    <small class="text-dark fw-bold">Segera Proses</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Tombol Aksi Cepat Distributor --}}
    <div class="row g-3">
        <div class="col-md-6">
            <button class="btn btn-dark w-100 py-3 rounded-4 fw-bold">
                <i class="fas fa-plus-circle me-2"></i> Input Barang Masuk (DO Principal)
            </button>
        </div>
        <div class="col-md-6">
            <button class="btn btn-outline-dark w-100 py-3 rounded-4 fw-bold">
                <i class="fas fa-shipping-fast me-2"></i> Buat Surat Jalan (Ke Cabang)
            </button>
        </div>
    </div>
</div>