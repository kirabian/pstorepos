<div class="container-fluid p-0 animate__animated animate__fadeIn">
    
    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-0">{{ $store_name }}</h2>
            <div class="d-flex gap-2 mt-2">
                <span class="badge bg-info bg-opacity-10 text-info border border-info-subtle px-3 py-1 rounded-pill">
                    <i class="fas fa-globe me-1"></i> Online Store
                </span>
                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning-subtle px-3 py-1 rounded-pill">
                    <i class="fas fa-star me-1"></i> 4.9 Rating
                </span>
            </div>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-white border shadow-sm rounded-pill px-4 fw-bold text-muted">
                <i class="fas fa-cog me-1"></i> Settings
            </button>
            <button class="btn btn-dark rounded-pill px-4 fw-bold shadow-sm">
                <i class="fab fa-whatsapp me-1"></i> Buka WebChat
            </button>
        </div>
    </div>

    {{-- KPIS --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-danger text-white h-100">
                <div class="card-body p-4">
                    <h1 class="display-4 fw-bold mb-1">{{ $pending_orders }}</h1>
                    <span class="text-white-50 fw-bold">Orderan Masuk (Pending)</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <h4 class="fw-bold mb-1 text-success">{{ $shipped_today }} Paket</h4>
                    <span class="text-muted small">Dikirim Hari Ini</span>
                    <div class="progress mt-3" style="height: 5px;">
                        <div class="progress-bar bg-success" style="width: 70%"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <h4 class="fw-bold mb-1 text-primary">{{ $chat_response_rate }}%</h4>
                    <span class="text-muted small">Performa Chat Reply</span>
                    <div class="progress mt-3" style="height: 5px;">
                        <div class="progress-bar bg-primary" style="width: {{ $chat_response_rate }}%"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-dark text-white">
                <div class="card-body p-4 text-end">
                    <small class="text-white-50 d-block mb-1">Omset Bulan Ini</small>
                    <h3 class="fw-bold mb-0">Rp {{ number_format(($omset_month ?? 0) / 1000000, 1, ',', '.') }} Jt</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- ORDER MANAGEMENT --}}
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 p-4 pb-0 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">📦 Orderan Terbaru</h5>
            <div class="input-group w-auto">
                <span class="input-group-text bg-light border-0"><i class="fas fa-search"></i></span>
                <input type="text" class="form-control border-0 bg-light" placeholder="Cari Resi / Nama...">
            </div>
        </div>
        <div class="card-body p-0 pt-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Order ID</th>
                            <th>Customer</th>
                            <th>Platform</th>
                            <th>Ekspedisi</th>
                            <th>Status</th>
                            <th>Waktu</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recent_orders as $order)
                        <tr>
                            <td class="ps-4 fw-bold text-primary">{{ $order['order_id'] }}</td>
                            <td class="fw-bold text-dark">{{ $order['customer'] }}</td>
                            <td>
                                <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle rounded-pill">
                                    <i class="fab fa-whatsapp me-1"></i> {{ $order['platform'] }}
                                </span>
                            </td>
                            <td><span class="text-muted small fw-bold text-uppercase">{{ $order['courier'] }}</span></td>
                            <td>
                                @if($order['status'] == 'Dikirim')
                                    <span class="badge bg-primary rounded-pill px-3">Dikirim</span>
                                @else
                                    <span class="badge bg-warning text-dark rounded-pill px-3">Perlu Proses</span>
                                @endif
                            </td>
                            <td class="text-muted small">{{ $order['time'] }}</td>
                            <td class="text-end pe-4">
                                <button class="btn btn-sm btn-light border rounded-pill px-3 fw-bold">Detail</button>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center py-5">Belum ada orderan baru</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>