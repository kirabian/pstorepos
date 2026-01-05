<div>
    <div class="p-4 animate__animated animate__fadeIn">
        <div class="mb-5">
            <h2 class="fw-bold text-dark">Selamat Datang, {{ auth()->user()->nama_lengkap }}</h2>
            <p class="text-muted text-uppercase small" style="letter-spacing: 2px;">
                Role: {{ str_replace('_', ' ', auth()->user()->role) }} | Unit Kerja: {{ auth()->user()->cabang->nama_cabang ?? 'Pusat' }}
            </p>
        </div>

        <div class="row g-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-5 p-5 bg-white text-center">
                    <div class="py-5">
                        <i class="fas fa-chart-line fa-3x mb-4 opacity-20"></i>
                        <h4 class="fw-bold">Dashboard Operasional</h4>
                        <p class="text-muted">Silakan pilih menu di samping untuk mulai mengelola data sesuai otoritas Anda.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>