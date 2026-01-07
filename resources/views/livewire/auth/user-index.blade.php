<div wire:poll.5s> 
    <div class="p-4 animate__animated animate__fadeIn">
        
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-5 gap-4">
            <div class="header-left ps-3 border-start border-5 border-dark">
                <h1 class="fw-900 text-dark mb-0 tracking-tighter display-5">Manajemen User</h1>
                <p class="text-muted small fw-bold text-uppercase mb-0 mt-1" style="letter-spacing: 4px; opacity: 0.7;">Otoritas & Akses Pengguna</p>
            </div>
            <div class="header-right">
                <a href="{{ route('user.create') }}" 
                   class="btn btn-dark rounded-pill px-5 py-3 fw-900 d-flex align-items-center shadow-premium hover-scale transition-all">
                    <i class="fas fa-plus-circle me-2 fs-5"></i> 
                    <span class="small tracking-widest text-uppercase">Tambah Pengguna</span>
                </a>
            </div>
        </div>

        <div class="glass-card rounded-5 shadow-extra-lg bg-white overflow-hidden border border-light-subtle">
            
            <div class="p-4 bg-white d-flex flex-column flex-md-row justify-content-between align-items-center gap-4 border-bottom border-light">
                <div class="search-box-modern w-100 w-md-50 position-relative">
                    <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-4 text-muted"></i>
                    <input type="text" wire:model.live.debounce.300ms="search" 
                        class="form-control border-0 bg-light-subtle py-3 ps-5 rounded-pill shadow-none fw-600 transition-all focus-white border-focus-dark" 
                        placeholder="Cari nama, email, atau role...">
                </div>
                
                <div class="system-meta d-none d-lg-flex align-items-center">
                    <div class="text-end me-4">
                        <p class="mb-0 extra-small fw-900 text-dark text-uppercase opacity-40">Total Akun</p>
                        <p class="mb-0 h5 fw-black text-dark">{{ $users->total() }}</p>
                    </div>
                    <div class="vertical-divider"></div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 border-0">
                    <thead class="bg-light-subtle">
                        <tr>
                            <th class="ps-5 py-4 border-0 extra-small fw-900 text-dark text-uppercase tracking-2">Pengguna & ID</th>
                            <th class="py-4 border-0 extra-small fw-900 text-dark text-uppercase tracking-2">Hak Akses</th>
                            <th class="py-4 border-0 extra-small fw-900 text-dark text-uppercase tracking-2">Cakupan Cabang</th>
                            <th class="py-4 border-0 extra-small fw-900 text-dark text-uppercase tracking-2 text-center">Afiliasi Mitra</th>
                            <th class="text-end pe-5 py-4 border-0 extra-small fw-900 text-dark text-uppercase tracking-2 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="border-0">
                        @forelse($users as $user)
                        <tr class="table-row-premium transition-all">
                            <td class="ps-5">
                                <div class="d-flex align-items-center">
                                    <div class="position-relative">
                                        <div class="avatar-circle-lg bg-dark text-white fw-900 shadow-sm me-3">
                                            {{ strtoupper(substr($user->nama_lengkap ?? $user->name, 0, 1)) }}
                                        </div>
                                        @if($user->isOnline())
                                            <span class="position-absolute top-0 start-0 translate-middle p-1 bg-success border border-2 border-white rounded-circle animate__animated animate__pulse animate__infinite" 
                                                  style="width: 14px; height: 14px; margin-top: 5px; margin-left: 5px;"></span>
                                        @endif
                                    </div>
                                    
                                    <div>
                                        <div class="fw-900 text-dark mb-0 fs-6 d-flex align-items-center gap-2">
                                            {{ $user->nama_lengkap }}
                                            @if($user->id === auth()->id())
                                                <span class="badge bg-secondary-subtle text-dark extra-small py-1 px-2 border-0">SAYA</span>
                                            @endif
                                        </div>
                                        <div class="d-flex flex-column">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="extra-small text-muted fw-bold">{{ $user->email }}</span>
                                                <span class="v-line h-10px w-1px bg-dark opacity-20"></span>
                                                <span class="extra-small text-dark fw-black opacity-70">ID: {{ $user->idlogin }}</span>
                                            </div>
                                            <div class="extra-small mt-1">
                                                @if($user->isOnline())
                                                    <span class="text-success fw-bold text-uppercase tracking-1" style="font-size: 0.55rem;">
                                                        <i class="fas fa-circle me-1" style="font-size: 0.4rem;"></i> Aktif Sekarang
                                                    </span>
                                                @else
                                                    <span class="text-muted opacity-50 italic" style="font-size: 0.55rem;">
                                                        Terakhir dilihat: {{ $user->last_seen_formatted }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge {{ $user->role == 'superadmin' ? 'bg-danger shadow-danger-sm' : 'bg-dark' }} rounded-pill px-3 py-2 extra-small fw-900 text-uppercase tracking-widest">
                                    {{ str_replace('_', ' ', $user->role) }}
                                </span>
                            </td>
                            
                            <td>
                                @if($user->role === 'superadmin')
                                    <span class="extra-small fw-900 text-dark text-uppercase tracking-tight bg-light px-2 py-1 rounded border">
                                        <i class="fas fa-globe me-1"></i> Seluruh Cabang
                                    </span>
                                @elseif($user->role === 'audit')
                                    <div class="d-flex flex-wrap gap-1">
                                        @forelse($user->branches as $b)
                                            <span class="badge bg-white text-dark border extra-small fw-bold">{{ $b->nama_cabang }}</span>
                                        @empty
                                            <span class="text-danger extra-small fw-bold">Belum ada cabang</span>
                                        @endforelse
                                    </div>
                                @elseif($user->cabang)
                                    <span class="extra-small fw-bold text-dark">
                                        <i class="fas fa-map-marker-alt me-1 text-secondary"></i> {{ $user->cabang->nama_cabang }}
                                    </span>
                                @else
                                    <span class="text-muted extra-small fw-bold opacity-30 italic">N/A</span>
                                @endif
                            </td>

                            <td class="text-center">
                                @if($user->distributor)
                                    <div class="d-inline-flex align-items-center bg-light border border-light-subtle px-3 py-2 rounded-4">
                                        <i class="fas fa-truck me-2 opacity-50 small text-dark"></i>
                                        <span class="extra-small fw-900 text-dark text-uppercase tracking-tight">{{ $user->distributor->nama_distributor }}</span>
                                    </div>
                                @else
                                    <span class="text-muted extra-small fw-bold opacity-30 italic">STAF INTERNAL</span>
                                @endif
                            </td>
                            <td class="text-end pe-5">
                                <div class="d-flex justify-content-center gap-3">
                                    <a href="{{ route('user.edit', $user->id) }}" 
                                       class="action-btn edit-btn shadow-sm rounded-circle d-flex align-items-center justify-content-center transition-all">
                                        <i class="fas fa-user-edit text-primary small"></i>
                                    </a>
                                    @if($user->id !== auth()->id())
                                    <button onclick="confirm('Hapus akun ini?') || event.stopImmediatePropagation()" 
                                            wire:click="delete({{ $user->id }})" 
                                            class="action-btn delete-btn shadow-sm rounded-circle d-flex align-items-center justify-content-center transition-all">
                                        <i class="fas fa-user-minus text-danger small"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="empty-state py-5">
                                    <h1 class="display-1 fw-900 opacity-5 mb-0">KOSONG</h1>
                                    <p class="text-muted fw-900 text-uppercase tracking-widest small">Tidak Ada Pengguna Terdaftar</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
            <div class="p-5 bg-white border-top border-light d-flex justify-content-center">
                {{ $users->links() }}
            </div>
            @endif
        </div>
    </div>

    <style>
        /* Tetap menggunakan style premium Anda sebelumnya */
        .fw-900, .fw-black { font-weight: 900 !important; }
        .fw-600 { font-weight: 600 !important; }
        .tracking-2 { letter-spacing: 2px; }
        .tracking-1 { letter-spacing: 1px; }
        .extra-small { font-size: 0.65rem; }
        .h-10px { height: 10px; }
        .w-1px { width: 1px; }
        .shadow-extra-lg { box-shadow: 0 40px 120px -20px rgba(0,0,0,0.12) !important; }
        .shadow-premium { box-shadow: 0 15px 35px -5px rgba(0,0,0,0.2) !important; }
        .rounded-5 { border-radius: 2.8rem !important; }
        .rounded-4 { border-radius: 1.2rem !important; }
        .table-row-premium { border-bottom: 1px solid #f8f9fa; transition: all 0.3s ease; }
        .table-row-premium:hover { background-color: #fafafa !important; }
        .avatar-circle-lg { width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; border: 2px solid #fff; }
        .action-btn { width: 42px; height: 42px; border: 1px solid #f0f0f0; background: #fff; text-decoration: none; display: flex; align-items: center; justify-content: center; transition: all 0.2s ease-in-out; }
        .action-btn:hover { background: #000 !important; transform: scale(1.1); border-color: #000; }
        .action-btn:hover i { color: #fff !important; }
        .shadow-danger-sm { box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3); }
        .vertical-divider { width: 1px; height: 40px; background: #000; opacity: 0.1; }
        .transition-all { transition: all 0.3s ease; }
        .hover-scale:hover { transform: translateY(-3px); }
        .italic { font-style: italic; }
    </style>
</div>