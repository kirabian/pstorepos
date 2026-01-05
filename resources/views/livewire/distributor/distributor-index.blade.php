<div> <div class="p-4 animate__animated animate__fadeIn">
        
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-5 gap-4">
            <div class="header-left ps-3 border-start border-5 border-dark">
                <h1 class="fw-900 text-dark mb-0 tracking-tighter display-5">Distributor</h1>
                <p class="text-muted small fw-bold text-uppercase mb-0 mt-1" style="letter-spacing: 4px; opacity: 0.7;">Manajemen Rantai Pasokan</p>
            </div>
            <div class="header-right">
                <a href="{{ route('distributor.create') }}" 
                   class="btn btn-dark rounded-pill px-5 py-3 fw-900 d-flex align-items-center shadow-premium hover-scale transition-all">
                    <i class="fas fa-plus-circle me-2 fs-5"></i> 
                    <span class="small tracking-widest">TAMBAH MITRA</span>
                </a>
            </div>
        </div>

        <div class="glass-card rounded-5 shadow-extra-lg bg-white overflow-hidden border border-light-subtle">
            
            <div class="p-4 bg-white d-flex flex-column flex-md-row justify-content-between align-items-center gap-4 border-bottom border-light">
                <div class="search-box-modern w-100 w-md-50 position-relative">
                    <i class="fas fa-filter position-absolute top-50 start-0 translate-middle-y ms-4 text-muted"></i>
                    <input type="text" wire:model.live="search" 
                        class="form-control border-0 bg-light-subtle py-3 ps-5 rounded-pill shadow-none fw-600 transition-all focus-white border-focus-dark" 
                        placeholder="Cari berdasarkan nama atau kode identitas...">
                </div>
                
                <div class="system-meta d-none d-lg-flex align-items-center">
                    <div class="text-end me-4">
                        <p class="mb-0 extra-small fw-900 text-dark text-uppercase opacity-40">Database V.1.4</p>
                        <div class="d-flex align-items-center justify-content-end mt-1">
                            <span class="status-dot bg-success me-2"></span>
                            <p class="mb-0 extra-small fw-bold text-success text-uppercase">Sistem Terkoneksi</p>
                        </div>
                    </div>
                    <div class="vertical-divider"></div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 border-0">
                    <thead class="bg-light-subtle">
                        <tr>
                            <th class="ps-5 py-4 border-0 extra-small fw-900 text-dark text-uppercase tracking-2">Identitas</th>
                            <th class="py-4 border-0 extra-small fw-900 text-dark text-uppercase tracking-2">Nama Entitas</th>
                            <th class="py-4 border-0 extra-small fw-900 text-dark text-uppercase tracking-2 text-center">Penanggung Jawab</th>
                            <th class="text-end pe-5 py-4 border-0 extra-small fw-900 text-dark text-uppercase tracking-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="border-0">
                        @forelse($distributors as $item)
                        <tr class="table-row-premium transition-all">
                            <td class="ps-5">
                                <div class="identity-tag font-mono fw-900">
                                    {{ $item->kode_distributor }}
                                </div>
                            </td>
                            <td>
                                <div class="fw-900 text-dark fs-5 mb-0 tracking-tight">{{ $item->nama_distributor }}</div>
                                <div class="extra-small text-muted fw-600 opacity-60 text-uppercase letter-spacing-1 mt-1">
                                    <i class="fas fa-map-marker-alt me-1"></i> {{ $item->alamat ?: 'Alamat Tidak Terdaftar' }}
                                </div>
                            </td>
                            <td class="text-center">
                                @forelse($item->users as $user)
                                    <div class="user-pill d-inline-flex align-items-center shadow-sm">
                                        <div class="avatar-circle bg-dark text-white fw-900">
                                            {{ strtoupper(substr($user->nama_lengkap, 0, 1)) }}
                                        </div>
                                        <span class="ms-2 me-3 extra-small fw-900 text-uppercase tracking-tight">{{ $user->nama_lengkap }}</span>
                                    </div>
                                @empty
                                    <div class="vacant-badge extra-small fw-900 opacity-30 italic">KOSONG</div>
                                @endforelse
                            </td>
                            <td class="text-end pe-5">
                                <div class="d-flex justify-content-end gap-3">
                                    <a href="{{ route('distributor.edit', $item->id) }}" 
                                       class="action-btn edit-btn shadow-sm rounded-circle d-flex align-items-center justify-content-center transition-all">
                                        <i class="fas fa-edit text-primary"></i>
                                    </a>
                                    <button onclick="confirm('Hapus mitra ini secara permanen?') || event.stopImmediatePropagation()" 
                                            wire:click="delete({{ $item->id }})" 
                                            class="action-btn delete-btn shadow-sm rounded-circle d-flex align-items-center justify-content-center transition-all">
                                        <i class="fas fa-trash-alt text-danger"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <div class="empty-state py-5">
                                    <h1 class="display-1 fw-900 opacity-5 mb-0">KOSONG</h1>
                                    <p class="text-muted fw-900 text-uppercase tracking-widest small">Tidak Ada Data Ditemukan</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($distributors->hasPages())
            <div class="p-5 bg-white border-top border-light d-flex justify-content-center">
                {{ $distributors->links() }}
            </div>
            @endif
        </div>
    </div>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap');

        /* Perbaikan Tipografi */
        .fw-900 { font-weight: 900 !important; }
        .fw-600 { font-weight: 600 !important; }
        .tracking-2 { letter-spacing: 2px; }
        .tracking-widest { letter-spacing: 4px; }
        .extra-small { font-size: 0.65rem; }
        .font-mono { font-family: 'SFMono-Regular', 'Liberation Mono', Menlo, monospace; }

        /* Kontainer & Kartu */
        .shadow-extra-lg { box-shadow: 0 40px 120px -20px rgba(0,0,0,0.12) !important; }
        .shadow-premium { box-shadow: 0 15px 35px -5px rgba(0,0,0,0.2) !important; }
        .rounded-5 { border-radius: 2.8rem !important; }
        
        /* Estetika Tabel */
        .table-row-premium { border-bottom: 1px solid #f1f1f1; }
        .table-row-premium:last-child { border-bottom: none; }
        .table-row-premium:hover {
            background-color: #fafafa !important;
            transform: scale(1.005);
        }

        /* Tag Identitas */
        .identity-tag {
            background: #f8f9fa;
            color: #000;
            padding: 8px 18px;
            border-radius: 14px;
            display: inline-block;
            border: 1px solid rgba(0,0,0,0.05);
            font-size: 0.85rem;
        }

        /* User PIC Pills */
        .user-pill {
            background: #fff;
            padding: 4px;
            border-radius: 50px;
            border: 1px solid #efefef;
        }
        .avatar-circle {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
        }

        /* Tombol Aksi */
        .action-btn {
            width: 48px;
            height: 48px;
            border: none;
            background: #fff;
            color: #000;
            text-decoration: none;
            border: 1px solid #f0f0f0;
        }
        .action-btn:hover {
            background: #000 !important;
            transform: rotate(10deg) scale(1.15);
        }
        .action-btn:hover i { color: #fff !important; }
        .edit-btn:hover { border-color: #0d6efd; }
        .delete-btn:hover { border-color: #dc3545; }

        /* Titik Status */
        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 10px rgba(25, 135, 84, 0.5);
        }

        /* Input Kustom */
        .search-box-modern input:focus {
            background: #fff !important;
            box-shadow: 0 15px 40px rgba(0,0,0,0.05) !important;
            padding-left: 3.8rem !important;
        }

        /* Pembagi Vertikal */
        .vertical-divider {
            width: 1px;
            height: 40px;
            background: #000;
            opacity: 0.1;
        }
        .transition-all { transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
        .hover-scale:hover { transform: translateY(-5px); }

        @media (max-width: 768px) {
            .rounded-5 { border-radius: 1.8rem !important; }
            .display-5 { font-size: 2.5rem; }
        }
    </style>
</div>