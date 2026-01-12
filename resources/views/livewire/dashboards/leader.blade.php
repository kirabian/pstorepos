<div class="animate__animated animate__fadeIn">
    
    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h1 class="display-6 fw-black text-dark mb-1 tracking-tight">
                Dashboard Leader
            </h1>
            <p class="text-secondary mb-0">
                Monitoring performa <span class="fw-bold text-primary">{{ $nama_cabang }}</span>
                <i class="fas fa-map-marker-alt ms-1 text-danger"></i> {{ $lokasi }}
            </p>
        </div>
        <div>
            <span class="badge bg-dark text-white rounded-pill px-4 py-2 fw-bold">
                {{ now()->format('d F Y') }}
            </span>
        </div>
    </div>

    {{-- STATS CARDS --}}
    <div class="row g-4 mb-5">
        {{-- Omset Hari Ini --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-5 h-100 bg-primary bg-gradient text-white overflow-hidden position-relative">
                <div class="card-body p-4 position-relative z-1">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="bg-white bg-opacity-25 p-2 rounded-3">
                            <i class="fas fa-cash-register fa-lg"></i>
                        </div>
                    </div>
                    <p class="text-white-50 small fw-bold text-uppercase mb-1">Omset Hari Ini</p>
                    <h2 class="fw-black mb-0 tracking-tight">Rp {{ number_format($omset_hari_ini, 0, ',', '.') }}</h2>
                    <small class="text-white fw-bold mt-2 d-block">
                        <i class="fas fa-shopping-bag me-1"></i> {{ $transaksi_hari_ini }} Transaksi
                    </small>
                </div>
                <div class="position-absolute top-0 end-0 mt-n3 me-n3 opacity-25">
                    <i class="fas fa-chart-area" style="font-size: 8rem;"></i>
                </div>
            </div>
        </div>

        {{-- Omset Bulan Ini --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-5 h-100 bg-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="bg-success bg-opacity-10 text-success p-2 rounded-3">
                            <i class="fas fa-calendar-check fa-lg"></i>
                        </div>
                        <span class="badge bg-success-subtle text-success rounded-pill">+5.2% Growth</span>
                    </div>
                    <p class="text-secondary small fw-bold text-uppercase mb-1">Akumulasi Bulan Ini</p>
                    <h2 class="fw-black text-dark mb-0 tracking-tight">Rp {{ number_format($omset_bulan_ini, 0, ',', '.') }}</h2>
                </div>
            </div>
        </div>

        {{-- Top Sales --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-5 h-100 bg-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="bg-warning bg-opacity-10 text-warning p-2 rounded-3">
                            <i class="fas fa-trophy fa-lg"></i>
                        </div>
                    </div>
                    <p class="text-secondary small fw-bold text-uppercase mb-1">Top Sales Hari Ini</p>
                    <h3 class="fw-black text-dark mb-0">{{ $top_sales }}</h3>
                    <small class="text-muted">Performa terbaik tim sales.</small>
                </div>
            </div>
        </div>
    </div>

    {{-- STAFF LIST --}}
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-5 overflow-hidden">
                <div class="card-header bg-white border-bottom border-light-subtle p-4">
                    <h5 class="fw-bold text-dark mb-0">Anggota Tim Cabang</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3 text-secondary small fw-bold text-uppercase">Nama Staff</th>
                                <th class="py-3 text-secondary small fw-bold text-uppercase">Role</th>
                                <th class="py-3 text-secondary small fw-bold text-uppercase">Status</th>
                                <th class="py-3 text-secondary small fw-bold text-uppercase text-end pe-4">Last Seen</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($staff_list as $staff)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ $staff->avatar_url }}" class="rounded-circle" width="35" height="35">
                                        <span class="fw-bold text-dark">{{ $staff->nama_lengkap }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border rounded-pill text-uppercase small">
                                        {{ str_replace('_', ' ', $staff->role) }}
                                    </span>
                                </td>
                                <td>
                                    @if($staff->is_online)
                                        <span class="badge bg-success-subtle text-success rounded-pill">Online</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary rounded-pill">Offline</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4 text-muted small">
                                    {{ $staff->last_seen ? \Carbon\Carbon::parse($staff->last_seen)->diffForHumans() : '-' }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">Belum ada anggota tim lain di cabang ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>