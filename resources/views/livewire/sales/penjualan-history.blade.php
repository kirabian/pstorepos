<div class="container-fluid">
    
    {{-- FIX: TAMBAHKAN LIBRARY SWEETALERT DISINI --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- HEADER & SUMMARY --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Riwayat Penjualan Saya</h4>
            <small class="text-muted">
                Cabang: <span class="fw-bold text-primary">{{ Auth::user()->cabang->nama_cabang ?? '-' }}</span>
            </small>
        </div>
        
        <div class="d-flex gap-3">
            <div class="bg-white border shadow-sm rounded-4 px-4 py-2 d-flex align-items-center gap-3">
                <div class="bg-success bg-opacity-10 text-success rounded-circle p-3">
                    <i class="fas fa-coins fa-lg"></i>
                </div>
                <div>
                    <small class="text-muted d-block fw-bold text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Omset Bulan Ini</small>
                    <span class="fw-black text-dark fs-5">Rp {{ number_format($omset, 0,',','.') }}</span>
                </div>
            </div>

            <div class="bg-white border shadow-sm rounded-4 px-4 py-2 d-flex align-items-center gap-3">
                <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3">
                    <i class="fas fa-box fa-lg"></i>
                </div>
                <div>
                    <small class="text-muted d-block fw-bold text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Unit Terjual</small>
                    <span class="fw-black text-dark fs-5">{{ $total_unit }} <span class="fs-6 text-muted fw-normal">Unit</span></span>
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
                        <span class="input-group-text bg-white border-end-0 text-muted ps-3 rounded-start-pill"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control border-start-0 rounded-end-pill ps-2" placeholder="Cari Customer, IMEI, Produk..." wire:model.live.debounce.300ms="search">
                    </div>
                </div>
                <div class="col-md-2">
                    <select class="form-select rounded-pill" wire:model.live="filterStatus">
                        <option value="">Semua Status</option>
                        <option value="Pending">⏳ Pending</option>
                        <option value="Approved">✅ Valid</option>
                        <option value="Rejected">❌ Ditolak</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select rounded-pill" wire:model.live="bulan">
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
                    <select class="form-select rounded-pill" wire:model.live="tahun">
                        @for($i = date('Y'); $i >= date('Y')-2; $i--)
                            <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2 text-end">
                    <a href="{{ route('sales.input') }}" class="btn btn-dark w-100 fw-bold rounded-pill shadow-sm hover-scale">
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
                        <th class="ps-4 py-3 text-secondary small fw-bold text-uppercase">Tanggal</th>
                        <th class="py-3 text-secondary small fw-bold text-uppercase">Produk & IMEI</th>
                        <th class="py-3 text-secondary small fw-bold text-uppercase">Customer</th>
                        <th class="py-3 text-secondary small fw-bold text-uppercase text-end">Harga Deal</th>
                        <th class="py-3 text-secondary small fw-bold text-uppercase text-center">Status</th>
                        <th class="py-3 px-4 text-secondary small fw-bold text-uppercase text-end">Nota & Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($penjualans as $p)
                        <tr>
                            <td class="ps-4">
                                <span class="fw-bold d-block text-dark">{{ $p->created_at->format('d M Y') }}</span>
                                <small class="text-muted font-monospace">{{ $p->created_at->format('H:i') }} WIB</small>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-light p-2 rounded-3 me-2 text-primary">
                                        <i class="fas fa-mobile-alt"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $p->nama_produk }}</div>
                                        <span class="badge bg-white text-secondary border font-monospace mt-1" style="font-size: 0.7rem;">
                                            {{ $p->imei_terjual }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $p->nama_customer }}</div>
                                <small class="text-muted d-flex align-items-center">
                                    <i class="fab fa-whatsapp text-success me-1"></i> {{ $p->nomor_wa }}
                                </small>
                            </td>
                            <td class="text-end">
                                <span class="fw-bold text-dark fs-6">Rp {{ number_format($p->harga_jual_real, 0,',','.') }}</span>
                            </td>
                            <td class="text-center">
                                @if($p->status_audit == 'Pending')
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-2 rounded-pill">
                                        <i class="fas fa-clock me-1"></i> Menunggu
                                    </span>
                                @elseif($p->status_audit == 'Approved')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill">
                                        <i class="fas fa-check-circle me-1"></i> Valid
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded-pill">
                                        <i class="fas fa-times-circle me-1"></i> Ditolak
                                    </span>
                                @endif
                            </td>
                            <td class="text-end px-4">
                                <div class="d-flex justify-content-end gap-2">
                                    {{-- Tombol Download PDF --}}
                                    <button type="button" 
                                            wire:click="downloadNota({{ $p->id }})" 
                                            class="btn btn-sm btn-outline-danger rounded-circle shadow-sm" 
                                            title="Download Nota PDF">
                                        <i class="fas fa-file-pdf"></i>
                                    </button>

                                    {{-- Tombol Kirim WA --}}
                                    <button type="button" 
                                            wire:click="kirimWa({{ $p->id }})" 
                                            wire:loading.attr="disabled"
                                            class="btn btn-sm btn-success rounded-circle text-white shadow-sm position-relative" 
                                            title="Kirim ke WhatsApp">
                                        
                                        {{-- Icon Normal --}}
                                        <i class="fab fa-whatsapp" wire:loading.remove wire:target="kirimWa({{ $p->id }})"></i>
                                        
                                        {{-- Icon Loading --}}
                                        <span wire:loading wire:target="kirimWa({{ $p->id }})" class="spinner-border spinner-border-sm text-white" role="status" aria-hidden="true"></span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted opacity-50 mb-3">
                                    <i class="fas fa-receipt fa-4x"></i>
                                </div>
                                <h6 class="fw-bold text-secondary">Belum ada riwayat penjualan</h6>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        @if($penjualans->hasPages())
            <div class="p-4 border-top">
                {{ $penjualans->links() }}
            </div>
        @endif
    </div>

    <style>
        .hover-scale:hover { transform: scale(1.02); transition: 0.2s; }
        .fw-black { font-weight: 900; }
    </style>
    
    {{-- Script untuk buka WA & SweetAlert --}}
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('open-wa', (data) => {
                window.open(data[0].url, '_blank');
            });
            
            Livewire.on('swal', (data) => {
                Swal.fire({
                    icon: data[0].icon,
                    title: data[0].title,
                    text: data[0].text,
                    confirmButtonColor: '#0d6efd'
                });
            });
        });
    </script>
</div>