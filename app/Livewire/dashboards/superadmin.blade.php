<div class="animate__animated animate__fadeIn">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-black text-dark mb-0">Control Tower</h2>
            <p class="text-muted small">Superadmin Monitoring System</p>
        </div>
        <span class="badge bg-dark text-white p-2 rounded-3">GLOBAL ACCESS</span>
    </div>

    <div class="row g-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-4 rounded-4 bg-primary text-white">
                <h3 class="fw-bold">{{ $total_user }}</h3>
                <small class="text-uppercase opacity-75">Total Users</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
                <h3 class="fw-bold text-dark">{{ $total_cabang }}</h3>
                <small class="text-uppercase text-muted">Cabang Aktif</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
                <h3 class="fw-bold text-dark">{{ $total_distributor }}</h3>
                <small class="text-uppercase text-muted">Mitra Distributor</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
                <h3 class="fw-bold text-success">{{ $active_users }}</h3>
                <small class="text-uppercase text-muted">User Online</small>
            </div>
        </div>
    </div>
    
    {{-- Area Chart atau Tabel Log Global bisa ditaruh disini --}}
    <div class="mt-5 p-5 border border-dashed rounded-4 text-center text-muted">
        <i class="fas fa-chart-line fa-3x mb-3"></i>
        <p>Global Analytics Area (Superadmin Only)</p>
    </div>
</div>