<div class="container-fluid p-4 animate__animated animate__fadeIn">
    
    {{-- Header Sambutan --}}
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h2 class="fw-900 text-dark mb-0 tracking-tighter">Audit Control Center</h2>
            <p class="text-secondary fw-bold small text-uppercase mb-0">
                Memantau {{ $cabang_count }} Cabang Aktif
            </p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-dark rounded-pill px-4 fw-bold shadow-sm">
                <i class="fas fa-file-download me-2"></i> Laporan Harian
            </button>
        </div>
    </div>

    {{-- STATISTIK CARDS --}}
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 position-relative">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="bg-success bg-opacity-10 p-3 rounded-circle text-success">
                            <i class="fas fa-arrow-down fs-4"></i>
                        </div>
                        <span class="badge bg-light text-dark border">Hari Ini</span>
                    </div>
                    <h3 class="fw-900 text-dark mb-1">{{ $masuk_today }} <span class="fs-6 text-muted fw-normal">Item</span></h3>
                    <p class="text-secondary small fw-bold mb-0 text-uppercase tracking-wide">Barang Masuk</p>
                </div>
                <div class="progress" style="height: 4px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: 70%"></div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 position-relative">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="bg-danger bg-opacity-10 p-3 rounded-circle text-danger">
                            <i class="fas fa-arrow-up fs-4"></i>
                        </div>
                        <span class="badge bg-light text-dark border">Hari Ini</span>
                    </div>
                    <h3 class="fw-900 text-dark mb-1">{{ $keluar_today }} <span class="fs-6 text-muted fw-normal">Item</span></h3>
                    <p class="text-secondary small fw-bold mb-0 text-uppercase tracking-wide">Barang Keluar</p>
                </div>
                <div class="progress" style="height: 4px;">
                    <div class="progress-bar bg-danger" role="progressbar" style="width: 50%"></div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 bg-dark text-white position-relative">
                <div class="card-body p-4 position-relative z-1">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="bg-white bg-opacity-20 p-3 rounded-circle text-white">
                            <i class="fas fa-check-double fs-4"></i>
                        </div>
                        <span class="badge bg-warning text-dark fw-bold">Action Needed</span>
                    </div>
                    {{-- Angka Dummy atau Real Count --}}
                    <h3 class="fw-900 mb-1">{{ $pending_approvals->count() }} <span class="fs-6 opacity-50 fw-normal">Request</span></h3>
                    <p class="text-white-50 small fw-bold mb-0 text-uppercase tracking-wide">Menunggu Approval</p>
                </div>
                {{-- Decoration --}}
                <i class="fas fa-stamp position-absolute bottom-0 end-0 text-white opacity-10" style="font-size: 8rem; margin-bottom: -20px; margin-right: -20px;"></i>
            </div>
        </div>
    </div>

    {{-- CONTENT SECTION --}}
    <div class="row g-4">
        
        {{-- KOLOM KIRI: NEED APPROVAL --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 py-4 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-900 text-dark mb-0">🔍 Perlu Tinjauan (Retur/Void)</h5>
                    <a href="#" class="btn btn-sm btn-light rounded-pill px-3 fw-bold small">Lihat Semua</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3 small fw-bold text-secondary">Waktu</th>
                                    <th class="small fw-bold text-secondary">Lokasi</th>
                                    <th class="small fw-bold text-secondary">Keterangan</th>
                                    <th class="small fw-bold text-secondary">Oleh</th>
                                    <th class="pe-4 text-end small fw-bold text-secondary">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pending_approvals as $item)
                                    <tr>
                                        <td class="ps-4 text-muted small fw-bold">
                                            {{ $item->created_at->format('d M, H:i') }}
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border">{{ $item->cabang->nama_cabang ?? '-' }}</span>
                                        </td>
                                        <td class="small text-dark fw-bold">
                                            {{ Str::limit($item->keterangan, 40) }}
                                        </td>
                                        <td class="small text-muted">
                                            {{ $item->user->nama_lengkap ?? 'System' }}
                                        </td>
                                        <td class="pe-4 text-end">
                                            <button class="btn btn-sm btn-success rounded-circle shadow-sm me-1" title="Approve"><i class="fas fa-check"></i></button>
                                            <button class="btn btn-sm btn-danger rounded-circle shadow-sm" title="Reject"><i class="fas fa-times"></i></button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="fas fa-check-circle fs-1 mb-3 text-success opacity-50"></i>
                                            <p class="fw-bold mb-0">Semua aman! Tidak ada pending approval.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: HARGA & AKTIVITAS --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 py-4 px-4">
                    <h5 class="fw-900 text-dark mb-0">🏷️ Log Perubahan Harga</h5>
                </div>
                <div class="card-body p-4 pt-0">
                    <div class="timeline-simple">
                        @forelse($price_logs as $log)
                            <div class="d-flex gap-3 mb-4">
                                <div class="flex-shrink-0">
                                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="fas fa-tag"></i>
                                    </div>
                                </div>
                                <div>
                                    <p class="small fw-bold text-dark mb-1 lh-sm">
                                        {{ $log->user->nama_lengkap ?? 'User' }} mengubah data stok.
                                    </p>
                                    <p class="extra-small text-muted mb-1">{{ $log->imei }}</p>
                                    <span class="badge bg-light text-secondary border extra-small">{{ $log->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-muted small">Belum ada aktivitas perubahan data.</div>
                        @endforelse
                    </div>
                    
                    <button class="btn btn-outline-dark w-100 rounded-pill fw-bold mt-2">Cek Manajemen Stok</button>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
    .fw-900 { font-weight: 900 !important; }
    .tracking-tighter { letter-spacing: -1px; }
    .tracking-wide { letter-spacing: 1px; }
    .extra-small { font-size: 0.65rem; }
</style>