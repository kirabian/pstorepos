<div wire:poll.5s>
    <div class="p-4 animate__animated animate__fadeIn">
        
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5 gap-4">
            <div class="header-left ps-3 border-start border-5 border-dark">
                <h1 class="fw-900 text-dark mb-0 tracking-tighter display-5">Manajemen Cabang</h1>
                <p class="text-muted small fw-bold text-uppercase mb-0 mt-1" style="letter-spacing: 3px; opacity: 0.7;">Network Operasional PSTORE</p>
            </div>
            <div class="header-right">
                <a href="{{ route('cabang.create') }}" 
                   class="btn btn-dark rounded-pill px-5 py-3 fw-900 d-flex align-items-center shadow-premium hover-scale transition-all">
                    <i class="fas fa-plus-circle me-2 fs-5"></i> 
                    <span class="small tracking-widest text-uppercase">Tambah Cabang</span>
                </a>
            </div>
        </div>

        <div class="glass-card rounded-5 shadow-extra-lg bg-white overflow-hidden border-0">
            
            <div class="p-4 bg-white d-flex flex-column flex-md-row justify-content-between align-items-center gap-4 border-bottom border-light">
                <div class="search-box-modern w-100 w-md-50 position-relative">
                    <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-4 text-muted"></i>
                    <input type="text" wire:model.live.debounce.500ms="search" 
                        class="form-control border-0 bg-light-subtle py-3 ps-5 rounded-pill shadow-none fw-600 focus-white border-focus-dark" 
                        placeholder="Cari lokasi, nama, atau kode...">
                </div>
                
                <div class="system-meta d-none d-lg-flex align-items-center">
                    <div class="text-end me-4">
                        <p class="mb-0 extra-small fw-900 text-dark text-uppercase opacity-40">Kapasitas</p>
                        <p class="mb-0 h5 fw-black text-dark">{{ $cabangs->total() }} Titik</p>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 border-0">
                    <thead class="bg-light-subtle">
                        <tr>
                            <th class="ps-5 py-4 border-0 extra-small fw-900 text-dark text-uppercase tracking-2">Identitas Cabang</th>
                            <th class="py-4 border-0 extra-small fw-900 text-dark text-uppercase tracking-2 text-center">Tim Operasional</th>
                            <th class="py-4 border-0 extra-small fw-900 text-dark text-uppercase tracking-2 text-center">Internal Audit</th>
                            <th class="py-4 border-0 extra-small fw-900 text-dark text-uppercase tracking-2">Area / Alamat</th>
                            <th class="text-end pe-5 py-4 border-0 extra-small fw-900 text-dark text-uppercase tracking-2">Kontrol</th>
                        </tr>
                    </thead>
                    <tbody class="border-0">
                        @forelse($cabangs as $cabang)
                        <tr class="table-row-premium transition-all border-bottom border-light-subtle">
                            <td class="ps-5">
                                <div class="d-flex align-items-center py-2">
                                    <div class="avatar-square-lg bg-black text-white fw-900 shadow-sm me-3 d-flex align-items-center justify-content-center rounded-4" style="width: 48px; height: 48px;">
                                        <i class="fas fa-store"></i>
                                    </div>
                                    <div>
                                        <div class="fw-900 text-dark mb-0 fs-6">{{ $cabang->nama_cabang }}</div>
                                        <div class="d-flex align-items-center mt-1">
                                            <span class="badge bg-dark-subtle text-dark extra-small fw-bold px-2 py-1 rounded-1 me-2">{{ $cabang->kode_cabang }}</span>
                                            <span class="extra-small text-muted fw-bold text-uppercase opacity-50">{{ $cabang->timezone ?? 'WIB' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            
                            <td class="text-center">
                                @php $staff = $cabang->regularStaff; @endphp
                                @if($staff->count() > 0)
                                    <div class="avatar-group d-flex justify-content-center">
                                        @foreach($staff->take(3) as $s)
                                            <div class="avatar-stack-item border border-2 border-white rounded-circle bg-dark text-white d-flex align-items-center justify-content-center shadow-sm" 
                                                 title="{{ $s->nama_lengkap }}" 
                                                 style="width: 32px; height: 32px; margin-left: -10px; font-size: 0.7rem;">
                                                {{ strtoupper(substr($s->nama_lengkap, 0, 1)) }}
                                            </div>
                                        @endforeach
                                        @if($staff->count() > 3)
                                            <div class="avatar-stack-item border border-2 border-white rounded-circle bg-light text-dark d-flex align-items-center justify-content-center shadow-sm fw-bold" 
                                                 style="width: 32px; height: 32px; margin-left: -10px; font-size: 0.6rem;">
                                                +{{ $staff->count() - 3 }}
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <span class="extra-small fw-bold text-muted opacity-30">— Empty —</span>
                                @endif
                            </td>

                            <td class="text-center">
                                {{-- PERBAIKAN: Gunakan relasi auditUsers --}}
                                @php $auditors = $cabang->auditUsers; @endphp 
                                @if($auditors->count() > 0)
                                    @foreach($auditors as $auditor)
                                        <div class="d-inline-flex align-items-center bg-primary-subtle text-primary px-3 py-1 rounded-pill extra-small fw-900 border border-primary-subtle shadow-sm mb-1">
                                            <div class="status-dot bg-primary me-2"></div>
                                            {{ strtoupper($auditor->nama_lengkap) }}
                                        </div>
                                    @endforeach
                                @else
                                    <span class="extra-small fw-bold text-muted opacity-30">NOT ASSIGNED</span>
                                @endif
                            </td>

                            <td>
                                <div class="address-wrapper" style="max-width: 250px;">
                                    <p class="extra-small fw-bold text-dark opacity-75 mb-0 leading-tight">
                                        <i class="fas fa-map-marker-alt me-1 opacity-25"></i> {{ Str::limit($cabang->lokasi, 60) }}
                                    </p>
                                </div>
                            </td>
                            
                            <td class="text-end pe-5">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('cabang.edit', $cabang->id) }}" 
                                       class="btn-action-modern edit shadow-sm transition-all d-flex align-items-center justify-content-center rounded-circle">
                                        <i class="fas fa-pen small"></i>
                                    </a>
                                    <button onclick="confirm('Yakin ingin menghapus cabang ini?') || event.stopImmediatePropagation()" 
                                            wire:click="delete({{ $cabang->id }})" 
                                            class="btn-action-modern delete shadow-sm transition-all d-flex align-items-center justify-content-center rounded-circle">
                                        <i class="fas fa-trash-alt small"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="py-5">
                                    <i class="fas fa-store-slash fa-3x text-muted mb-3 opacity-20"></i>
                                    <p class="text-muted fw-900 text-uppercase tracking-widest small">Belum ada titik operasional</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($cabangs->hasPages())
            <div class="p-5 bg-white border-top border-light d-flex justify-content-center">
                {{ $cabangs->links() }}
            </div>
            @endif
        </div>
    </div>

    <style>
        .fw-900 { font-weight: 900 !important; }
        .fw-black { font-weight: 950 !important; }
        .fw-600 { font-weight: 600 !important; }
        .extra-small { font-size: 0.65rem; }
        .tracking-2 { letter-spacing: 2px; }
        
        .shadow-extra-lg { box-shadow: 0 40px 100px -20px rgba(0,0,0,0.08) !important; }
        .shadow-premium { box-shadow: 0 15px 35px -5px rgba(0,0,0,0.2) !important; }
        .rounded-5 { border-radius: 2.5rem !important; }
        
        .table-row-premium { border-bottom: 1px solid #f8f9fa; }
        .table-row-premium:hover { 
            background-color: #fafafa !important; 
            transform: scale(1.002);
            box-shadow: inset 4px 0 0 #000;
        }

        .avatar-group .avatar-stack-item {
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .avatar-group .avatar-stack-item:hover {
            z-index: 10;
            transform: translateY(-5px);
            margin-right: 5px;
        }

        .btn-action-modern {
            width: 38px;
            height: 38px;
            background: #fff;
            border: 1px solid #eee;
            color: #333;
            text-decoration: none;
        }
        .btn-action-modern.edit:hover { background: #000; color: #fff; border-color: #000; }
        .btn-action-modern.delete:hover { background: #dc3545; color: #fff; border-color: #dc3545; }

        .status-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
        }

        @media (max-width: 768px) {
            .display-5 { font-size: 2rem; }
            .rounded-5 { border-radius: 1.5rem !important; }
        }
    </style>
</div>