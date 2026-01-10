<div class="animate__animated animate__fadeIn">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center mb-5">
            <h2 class="fw-black text-dark">Point of Sales</h2>
            <p class="text-secondary">{{ $cabang }}</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card h-100 border-0 shadow-sm rounded-5 bg-primary text-white p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="fw-black display-4 mb-0">{{ $penjualan_hari_ini }}</h1>
                        <p class="mb-0 opacity-75">Unit Terjual Hari Ini</p>
                    </div>
                    <i class="fas fa-shopping-bag fa-4x opacity-25"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100 border-0 shadow-sm rounded-5 bg-white p-4">
                <h5 class="fw-bold text-dark mb-3">Target Bulanan</h5>
                <div class="progress" style="height: 25px; border-radius: 15px;">
                    <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" 
                         role="progressbar" style="width: {{ $target_bulan }}%">
                         {{ $target_bulan }}%
                    </div>
                </div>
                <p class="text-muted small mt-2 text-center">Sedikit lagi mencapai target!</p>
            </div>
        </div>
    </div>

    <div class="mt-5 d-grid gap-3">
        <button class="btn btn-dark btn-lg rounded-pill py-3 fw-bold shadow-lg">
            <i class="fas fa-cash-register me-2"></i> Buka Kasir / Transaksi Baru
        </button>
        <button class="btn btn-outline-secondary btn-lg rounded-pill py-3 fw-bold">
            <i class="fas fa-history me-2"></i> Riwayat Transaksi Saya
        </button>
    </div>
</div>