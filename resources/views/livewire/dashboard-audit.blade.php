<div class="container-fluid p-4 animate__animated animate__fadeIn">
    
    {{-- Header Section --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5 gap-3">
        <div>
            <h2 class="fw-bold text-dark mb-1 tracking-tight">Audit Control Center</h2>
            <p class="text-secondary small mb-0">
                <i class="fas fa-building me-1 text-muted"></i> Memantau {{ $cabang_count }} Cabang Aktif
            </p>
        </div>
        <div>
            <button class="btn btn-dark px-4 py-2 small fw-bold">
                <i class="fas fa-file-alt me-2"></i> Download Laporan Harian
            </button>
        </div>
    </div>

    {{-- STATISTIK CARDS (Modern Square Style) --}}
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 p-4 position-relative overflow-hidden bg-white">
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-success bg-opacity-10 text-success p-2 rounded-2 me-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                        <i class="fas fa-arrow-down"></i>
                    </div>
                    <div>
                        <p class="text-uppercase text-secondary extra-small fw-bold mb-0 tracking-wide">Barang Masuk</p>
                        <span class="badge bg-light text-success border border-success border-opacity-25 extra-small">Hari Ini</span>
                    </div>
                </div>
                <h2 class="fw-bold text-dark mb-0">{{ $masuk_today }} <span class="fs-6 text-muted fw-normal">Unit</span></h2>
                <div class="progress mt-4 bg-light" style="height: 6px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: 70%"></div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 p-4 position-relative overflow-hidden bg-white">
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-danger bg-opacity-10 text-danger p-2 rounded-2 me-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                        <i class="fas fa-arrow-up"></i>
                    </div>
                    <div>
                        <p class="text-uppercase text-secondary extra-small fw-bold mb-0 tracking-wide">Barang Keluar</p>
                        <span class="badge bg-light text-danger border border-danger border-opacity-25 extra-small">Hari Ini</span>
                    </div>
                </div>
                <h2 class="fw-bold text-dark mb-0">{{ $keluar_today }} <span class="fs-6 text-muted fw-normal">Unit</span></h2>
                <div class="progress mt-4 bg-light" style="height: 6px;">
                    <div class="progress-bar bg-danger" role="progressbar" style="width: 50%"></div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 p-4 position-relative overflow-hidden bg-dark text-white">
                <div class="d-flex align-items-center mb-4 position-relative z-1">
                    <div class="bg-white bg-opacity-20 text-white p-2 rounded-2 me-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                        <i class="fas fa-exclamation"></i>
                    </div>
                    <div>
                        <p class="text-uppercase text-white-50 extra-small fw-bold mb-0 tracking-wide">Butuh Persetujuan</p>
                        <span class="badge bg-warning text-dark border-0 extra-small">Urgent</span>
                    </div>
                </div>
                <h2 class="fw-bold mb-0 position-relative z-1">{{ $pending_approvals->count() }} <span class="fs-6 opacity-50 fw-normal">Request</span></h2>
                
                {{-- Abstract Decoration --}}
                <div class="position-absolute bottom-0 end-0 opacity-10 me-n3 mb-n3">
                    <i class="fas fa-file-signature display-1"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- KOLOM KIRI: Approval List --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom border-light py-3 px-4 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold text-dark mb-0">Permintaan Persetujuan</h6>
                    <a href="#" class="text-decoration-none text-primary extra-small fw-bold">Lihat Semua &rarr;</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0 table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3 text-secondary extra-small text-uppercase fw-bold">Tanggal</th>
                                    <th class="text-secondary extra-small text-uppercase fw-bold">Lokasi</th>
                                    <th class="text-secondary extra-small text-uppercase fw-bold">Deskripsi</th>
                                    <th class="text-secondary extra-small text-uppercase fw-bold">Pengaju</th>
                                    <th class="text-end pe-4 text-secondary extra-small text-uppercase fw-bold">Tindakan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pending_approvals as $item)
                                    <tr>
                                        <td class="ps-4">
                                            <span class="d-block fw-bold text-dark small">{{ $item->created_at->format('d M') }}</span>
                                            <span class="d-block text-muted extra-small">{{ $item->created_at->format('H:i') }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border rounded-1 fw-normal">{{ $item->cabang->nama_cabang ?? '-' }}</span>
                                        </td>
                                        <td>
                                            <p class="mb-0 small text-dark fw-medium text-truncate" style="max-width: 200px;">
                                                {{ $item->keterangan }}
                                            </p>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-light text-secondary rounded-1 d-flex align-items-center justify-content-center me-2" style="width: 24px; height: 24px; font-size: 0.7rem;">
                                                    {{ substr($item->user->nama_lengkap ?? 'U', 0, 1) }}
                                                </div>
                                                <span class="small text-muted">{{ $item->user->nama_lengkap ?? 'System' }}</span>
                                            </div>
                                        </td>
                                        <td class="text-end pe-4">
                                            <div class="btn-group">
                                                <button class="btn btn-sm btn-success px-3" title="Setujui"><i class="fas fa-check"></i></button>
                                                <button class="btn btn-sm btn-outline-danger px-3" title="Tolak"><i class="fas fa-times"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <div class="d-flex flex-column align-items-center justify-content-center">
                                                <div class="bg-light p-3 rounded-2 mb-3">
                                                    <i class="fas fa-check-double text-success fs-3"></i>
                                                </div>
                                                <h6 class="fw-bold text-dark mb-1">Semua Bersih!</h6>
                                                <p class="text-muted small mb-0">Tidak ada permintaan persetujuan pending.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: Activity Log --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom border-light py-3 px-4">
                    <h6 class="fw-bold text-dark mb-0">Aktivitas Harga & Stok</h6>
                </div>
                <div class="card-body p-4">
                    <div class="activity-feed">
                        @forelse($price_logs as $log)
                            <div class="activity-item d-flex mb-4">
                                <div class="flex-shrink-0 me-3">
                                    <div class="bg-light border text-dark rounded-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                        <i class="fas fa-tag small"></i>
                                    </div>
                                </div>
                                <div>
                                    <p class="small mb-1 text-dark">
                                        <span class="fw-bold">{{ $log->user->nama_lengkap ?? 'User' }}</span> memperbarui data stok.
                                    </p>
                                    <div class="bg-light p-2 rounded-1 border mb-1">
                                        <code class="text-primary small">{{ $log->imei }}</code>
                                    </div>
                                    <small class="text-muted extra-small">{{ $log->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-muted small">Belum ada aktivitas tercatat.</div>
                        @endforelse
                    </div>
                    
                    <button class="btn btn-outline-dark w-100 py-2 small fw-bold mt-2">Lihat Log Lengkap</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .tracking-tight { letter-spacing: -0.5px; }
    .tracking-wide { letter-spacing: 1px; }
    .extra-small { font-size: 0.65rem; }
    
    /* Custom Card Styling */
    .card {
        border-radius: 12px !important; /* Rounded kotak yang halus */
    }
    
    /* Table Styling */
    .table th {
        font-weight: 600;
        letter-spacing: 0.5px;
    }
    
    /* Timeline Effect */
    .activity-feed {
        position: relative;
    }
    .activity-feed::before {
        content: '';
        position: absolute;
        top: 0;
        bottom: 0;
        left: 15px; /* Sesuaikan dengan icon */
        width: 2px;
        background-color: #f8f9fa;
        z-index: 0;
    }
    .activity-item {
        position: relative;
        z-index: 1;
    }
</style>