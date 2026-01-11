<div class="container-fluid">
    <div class="d-flex align-items-center mb-4">
        <i class="fas fa-cubes fs-4 me-2 text-primary"></i>
        <h4 class="fw-bold text-black mb-0">Monitoring Stok Cabang</h4>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            {{-- Toolbar --}}
            <div class="p-4 border-bottom bg-white">
                <div class="row g-3">
                    <div class="col-md-4">
                        <input type="text" class="form-control rounded-3" placeholder="Cari Nama Cabang..." wire:model.live.debounce.300ms="search">
                    </div>
                </div>
            </div>

            {{-- Table --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-3 px-4 text-secondary small fw-bold">Kode Cabang</th>
                            <th class="py-3 text-secondary small fw-bold">Nama Cabang</th>
                            <th class="py-3 text-secondary small fw-bold">Lokasi</th>
                            <th class="py-3 text-secondary small fw-bold text-center">Total Stok Fisik</th>
                            <th class="py-3 text-secondary small fw-bold text-center">Status Stok</th>
                            <th class="py-3 px-4 text-secondary small fw-bold text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cabangs as $cabang)
                            <tr>
                                <td class="px-4 fw-bold text-muted">{{ $cabang->kode_cabang }}</td>
                                <td class="fw-bold text-dark">{{ $cabang->nama_cabang }}</td>
                                <td class="text-muted small">{{ $cabang->lokasi }}</td>
                                <td class="text-center">
                                    <span class="fs-5 fw-black text-primary">{{ $cabang->stoks_count }}</span> Unit
                                </td>
                                <td class="text-center">
                                    @if($cabang->stoks_count < 10)
                                        <span class="badge bg-danger">Kritis</span>
                                    @elseif($cabang->stoks_count < 50)
                                        <span class="badge bg-warning text-dark">Menipis</span>
                                    @else
                                        <span class="badge bg-success">Aman</span>
                                    @endif
                                </td>
                                <td class="px-4 text-end">
                                    <button class="btn btn-sm btn-outline-dark rounded-pill px-3">
                                        Detail Item
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">Tidak ada data cabang ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4">
                {{ $cabangs->links() }}
            </div>
        </div>
    </div>
</div>