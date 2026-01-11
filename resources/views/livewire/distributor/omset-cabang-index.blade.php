<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div class="d-flex align-items-center">
            <i class="fas fa-chart-line fs-4 me-2 text-success"></i>
            <h4 class="fw-bold text-black mb-0">Monitoring Omset Cabang</h4>
        </div>
        <div class="bg-white px-4 py-2 rounded-pill shadow-sm border">
            <small class="text-muted fw-bold text-uppercase">Total Omset Bulan Ini</small>
            <h5 class="fw-black text-success mb-0">Rp {{ number_format($totalOmsetNasional, 0, ',', '.') }}</h5>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-3 px-4 text-secondary small fw-bold">Peringkat</th>
                            <th class="py-3 text-secondary small fw-bold">Nama Cabang</th>
                            <th class="py-3 text-secondary small fw-bold text-end">Transaksi (Hari Ini)</th>
                            <th class="py-3 text-secondary small fw-bold text-end">Omset Hari Ini</th>
                            <th class="py-3 px-4 text-secondary small fw-bold text-end">Omset Bulan Ini</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cabangs as $index => $cabang)
                            <tr>
                                <td class="px-4">
                                    @if($index == 0)
                                        <i class="fas fa-trophy text-warning"></i> 1
                                    @elseif($index == 1)
                                        <i class="fas fa-medal text-secondary"></i> 2
                                    @elseif($index == 2)
                                        <i class="fas fa-medal text-danger"></i> 3
                                    @else
                                        <span class="fw-bold text-muted ps-3">{{ $index + 1 }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $cabang->nama_cabang }}</div>
                                    <small class="text-muted">{{ $cabang->lokasi }}</small>
                                </td>
                                <td class="text-end">
                                    <span class="badge bg-light text-dark border">{{ $cabang->transaksi_count }} Trx</span>
                                </td>
                                <td class="text-end fw-bold text-success">
                                    Rp {{ number_format($cabang->omset_hari_ini, 0, ',', '.') }}
                                </td>
                                <td class="text-end px-4">
                                    <h6 class="fw-black text-dark mb-0">Rp {{ number_format($cabang->omset_bulan_ini, 0, ',', '.') }}</h6>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>