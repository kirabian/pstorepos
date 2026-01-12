<div class="animate__animated animate__fadeIn">
    
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5 gap-4">
        <div>
            <h1 class="display-6 fw-black text-dark mb-1">Laporan Penjualan</h1>
            <p class="text-secondary mb-0">
                Data transaksi lengkap untuk <span class="fw-bold text-primary">{{ $nama_cabang }}</span>.
            </p>
        </div>
        
        <div class="d-flex gap-2">
            <button class="btn btn-success rounded-pill px-4 fw-bold shadow-sm">
                <i class="fas fa-file-excel me-2"></i> Export Excel
            </button>
        </div>
    </div>

    {{-- Filter Section --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" wire:model.live="search" class="form-control bg-light border-0" placeholder="Cari Invoice / Customer...">
                    </div>
                </div>
                <div class="col-md-3">
                    <input type="date" wire:model.live="filterDate" class="form-control bg-light border-0">
                </div>
                <div class="col-md-5 text-end">
                    <span class="text-muted small fst-italic">Menampilkan data real-time cabang.</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Table Section --}}
    <div class="card border-0 shadow-sm rounded-5 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-primary text-white">
                    <tr>
                        <th class="ps-4 py-3 small fw-bold text-uppercase">Invoice</th>
                        <th class="py-3 small fw-bold text-uppercase">Tanggal</th>
                        <th class="py-3 small fw-bold text-uppercase">Customer</th>
                        <th class="py-3 small fw-bold text-uppercase">Detail Item</th>
                        <th class="py-3 small fw-bold text-uppercase">Metode</th>
                        <th class="py-3 small fw-bold text-uppercase text-end">Total</th>
                        <th class="pe-4 py-3 small fw-bold text-uppercase text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $trx)
                    <tr>
                        <td class="ps-4 fw-bold text-primary">{{ $trx['id'] }}</td>
                        <td>{{ $trx['tanggal']->format('d M Y, H:i') }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $trx['customer_name'] }}</div>
                            <small class="text-muted">Sales: {{ $trx['sales_name'] }}</small>
                        </td>
                        <td>{{ $trx['items'] }}</td>
                        <td>
                            <span class="badge bg-light text-dark border">
                                {{ $trx['payment_method'] }}
                            </span>
                        </td>
                        <td class="text-end fw-black text-dark">
                            Rp {{ number_format($trx['total_bayar'], 0, ',', '.') }}
                        </td>
                        <td class="pe-4 text-center">
                            <span class="badge bg-success-subtle text-success rounded-pill px-3">
                                <i class="fas fa-check-circle me-1"></i> {{ $trx['status'] }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <img src="https://illustrations.popsy.co/gray/surr-searching.svg" width="100" class="mb-3 opacity-50">
                            <p class="text-muted fw-bold">Tidak ada data penjualan ditemukan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        @if($transactions->hasPages())
        <div class="card-footer bg-white border-top border-light-subtle p-4">
            {{ $transactions->links() }}
        </div>
        @endif
    </div>
</div>