<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Verifikasi Penjualan</h4>
            <p class="text-muted small mb-0">Validasi transaksi dari Sales.</p>
        </div>
        <div class="d-flex gap-2">
            <select wire:model.live="filterStatus" class="form-select rounded-pill">
                <option value="Pending">Menunggu Verifikasi</option>
                <option value="Approved">Approved (Valid)</option>
                <option value="Rejected">Rejected (Tidak Valid)</option>
            </select>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="p-3 border-bottom">
                <input type="text" class="form-control rounded-pill w-25" placeholder="Cari Customer / Sales / IMEI..." wire:model.live="search">
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Tanggal</th>
                            <th>Sales / Cabang</th>
                            <th>Produk & IMEI</th>
                            <th>Customer</th>
                            <th>Harga Jual</th>
                            <th>Bukti</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($penjualans as $p)
                            <tr>
                                <td class="ps-4 small text-muted">
                                    {{ $p->created_at->format('d M Y') }} <br>
                                    {{ $p->created_at->format('H:i') }}
                                </td>
                                <td>
                                    <span class="fw-bold d-block">{{ $p->user->nama_lengkap }}</span>
                                    <span class="badge bg-light text-dark border">{{ $p->cabang->nama_cabang }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $p->nama_produk }}</div>
                                    <small class="font-monospace text-primary">{{ $p->imei_terjual }}</small>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $p->nama_customer }}</div>
                                    <small class="text-muted"><i class="fab fa-whatsapp text-success"></i> {{ $p->nomor_wa }}</small>
                                </td>
                                <td class="fw-bold">Rp {{ number_format($p->harga_jual_real, 0,',','.') }}</td>
                                <td>
                                    @if($p->foto_bukti_transaksi)
                                        <a href="{{ asset('storage/'.$p->foto_bukti_transaksi) }}" target="_blank">
                                            <img src="{{ asset('storage/'.$p->foto_bukti_transaksi) }}" class="rounded-3 border" width="50" height="50" style="object-fit: cover;">
                                        </a>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($p->status_audit == 'Pending')
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @elseif($p->status_audit == 'Approved')
                                        <span class="badge bg-success">Approved</span>
                                        <div class="small text-muted" style="font-size: 0.65rem;">by {{ $p->auditor->nama_lengkap ?? '-' }}</div>
                                    @else
                                        <span class="badge bg-danger">Rejected</span>
                                        <div class="small text-muted" style="font-size: 0.65rem;">by {{ $p->auditor->nama_lengkap ?? '-' }}</div>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    @if($p->status_audit == 'Pending')
                                        <button wire:confirm="Yakin data ini valid?" wire:click="approve({{ $p->id }})" class="btn btn-sm btn-success rounded-circle" title="Approve">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button wire:confirm="Tolak penjualan ini?" wire:click="reject({{ $p->id }})" class="btn btn-sm btn-danger rounded-circle" title="Reject">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @else
                                        <span class="text-muted small"><i class="fas fa-lock"></i></span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">Data penjualan tidak ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-3">
                {{ $penjualans->links() }}
            </div>
        </div>
    </div>
</div>