<div class="container-fluid">
    {{-- HEADER & SUMMARY --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold mb-1">Riwayat Penjualan Saya</h4>
            <small class="text-muted">Cabang: {{ Auth::user()->cabang->nama_cabang ?? '-' }}</small>
        </div>
        
        <div class="d-flex gap-3">
            {{-- Card Ringkasan Kecil --}}
            <div class="bg-white border shadow-sm rounded-3 px-3 py-2 d-flex align-items-center gap-3">
                <div class="bg-success bg-opacity-10 text-success rounded-circle p-2">
                    <i class="fas fa-coins"></i>
                </div>
                <div>
                    <small class="text-muted d-block" style="font-size: 0.7rem;">Omset Bulan Ini</small>
                    <span class="fw-bold">Rp {{ number_format($omset, 0,',','.') }}</span>
                </div>
            </div>
            <div class="bg-white border shadow-sm rounded-3 px-3 py-2 d-flex align-items-center gap-3">
                <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2">
                    <i class="fas fa-box"></i>
                </div>
                <div>
                    <small class="text-muted d-block" style="font-size: 0.7rem;">Unit Terjual</small>
                    <span class="fw-bold">{{ $total_unit }} Unit</span>
                </div>
            </div>
        </div>
    </div>

    {{-- FILTER SECTION --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <div class="row g-2 align-items-center">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" class="form-control border-start-0 ps-0" placeholder="Cari Customer, IMEI, Produk..." wire:model.live.debounce.300ms="search">
                    </div>
                </div>
                <div class="col-md-2">
                    <select class="form-select" wire:model.live="filterStatus">
                        <option value="">Semua Status</option>
                        <option value="Pending">Pending</option>
                        <option value="Approved">Approved</option>
                        <option value="Rejected">Rejected</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" wire:model.live="bulan">
                        <option value="01">Januari</option>
                        <option value="02">Februari</option>
                        <option value="03">Maret</option>
                        <option value="04">April</option>
                        <option value="05">Mei</option>
                        <option value="06">Juni</option>
                        <option value="07">Juli</option>
                        <option value="08">Agustus</option>
                        <option value="09">September</option>
                        <option value="10">Oktober</option>
                        <option value="11">November</option>
                        <option value="12">Desember</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" wire:model.live="tahun">
                        @for($i = date('Y'); $i >= date('Y')-2; $i--)
                            <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2 text-end">
                    <a href="{{ route('sales.input') }}" class="btn btn-dark w-100 fw-bold">
                        <i class="fas fa-plus me-1"></i> Input Baru
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- TABEL DATA --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-secondary small fw-bold">Tanggal</th>
                        <th class="py-3 text-secondary small fw-bold">Produk</th>
                        <th class="py-3 text-secondary small fw-bold">Customer</th>
                        <th class="py-3 text-secondary small fw-bold">Harga Deal</th>
                        <th class="py-3 text-secondary small fw-bold text-center">Bukti</th>
                        <th class="py-3 text-secondary small fw-bold text-center">Status Audit</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($penjualans as $p)
                        <tr>
                            <td class="ps-4">
                                <span class="fw-bold d-block text-dark">{{ $p->created_at->format('d M Y') }}</span>
                                <small class="text-muted">{{ $p->created_at->format('H:i') }} WIB</small>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $p->nama_produk }}</div>
                                <span class="badge bg-light text-primary border border-primary-subtle font-monospace mt-1">
                                    {{ $p->imei_terjual }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold">{{ $p->nama_customer }}</div>
                                <small class="text-muted"><i class="fab fa-whatsapp text-success"></i> {{ $p->nomor_wa }}</small>
                            </td>
                            <td>
                                <span class="fw-bold text-dark">Rp {{ number_format($p->harga_jual_real, 0,',','.') }}</span>
                                @if($p->catatan)
                                    <i class="fas fa-info-circle text-muted ms-1" data-bs-toggle="tooltip" title="{{ $p->catatan }}"></i>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($p->foto_bukti_transaksi)
                                    <a href="{{ asset('storage/'.$p->foto_bukti_transaksi) }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-circle">
                                        <i class="fas fa-image"></i>
                                    </a>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($p->status_audit == 'Pending')
                                    <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">
                                        <i class="fas fa-clock me-1"></i> Menunggu
                                    </span>
                                @elseif($p->status_audit == 'Approved')
                                    <span class="badge bg-success px-3 py-2 rounded-pill">
                                        <i class="fas fa-check-circle me-1"></i> Valid
                                    </span>
                                @else
                                    <span class="badge bg-danger px-3 py-2 rounded-pill">
                                        <i class="fas fa-times-circle me-1"></i> Ditolak
                                    </span>
                                    {{-- Tampilkan siapa yang menolak jika rejected --}}
                                    <div class="small text-danger mt-1" style="font-size: 0.7rem;">
                                        Oleh: {{ $p->auditor->nama_lengkap ?? 'Audit' }}
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted opacity-50 mb-2">
                                    <i class="fas fa-receipt fa-3x"></i>
                                </div>
                                <p class="text-muted mb-0">Belum ada riwayat penjualan pada periode ini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-top">
            {{ $penjualans->links() }}
        </div>
    </div>
</div>