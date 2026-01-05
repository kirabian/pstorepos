<div>
    <div class="p-4 animate__animated animate__fadeIn">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-5 gap-4">
            <div class="header-left ps-3 border-start border-5 border-dark">
                <h1 class="fw-900 text-dark mb-0 tracking-tighter display-5">Manajemen Gudang</h1>
                <p class="text-muted small fw-bold text-uppercase mb-0 mt-1" style="letter-spacing: 4px; opacity: 0.7;">Pusat Logistik PSTORE</p>
            </div>
            <div class="header-right">
                <a href="{{ route('gudang.create') }}" class="btn btn-dark rounded-pill px-5 py-3 fw-900 d-flex align-items-center shadow-premium hover-scale transition-all">
                    <i class="fas fa-plus-circle me-2 fs-5"></i> 
                    <span class="small tracking-widest text-uppercase">Tambah Gudang</span>
                </a>
            </div>
        </div>

        <div class="glass-card rounded-5 shadow-extra-lg bg-white overflow-hidden border border-light-subtle">
            <div class="p-4 bg-white d-flex flex-column flex-md-row justify-content-between align-items-center gap-4 border-bottom border-light">
                <div class="search-box-modern w-100 w-md-50 position-relative">
                    <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-4 text-muted"></i>
                    <input type="text" wire:model.live.debounce.500ms="search" 
                        class="form-control border-0 bg-light-subtle py-3 ps-5 rounded-pill shadow-none fw-600 focus-white border-focus-dark" 
                        placeholder="Cari nama atau kode gudang...">
                </div>
            </div>

            <div class="table-responsive position-relative">
                <div wire:loading.delay class="position-absolute top-0 start-0 w-100 h-100 bg-white bg-opacity-75" style="z-index: 10; backdrop-filter: blur(2px);">
                    <div class="d-flex justify-content-center align-items-center h-100">
                        <div class="spinner-grow text-dark" role="status"></div>
                    </div>
                </div>

                <table class="table table-hover align-middle mb-0 border-0">
                    <thead class="bg-light-subtle">
                        <tr>
                            <th class="ps-5 py-4 border-0 extra-small fw-900 text-dark text-uppercase tracking-2">Info Gudang</th>
                            <th class="py-4 border-0 extra-small fw-900 text-dark text-uppercase tracking-2">Alamat Lengkap</th>
                            <th class="text-end pe-5 py-4 border-0 extra-small fw-900 text-dark text-uppercase tracking-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="border-0">
                        @forelse($gudangs as $gudang)
                        <tr class="table-row-premium transition-all border-bottom border-light-subtle">
                            <td class="ps-5">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle-lg bg-dark text-white fw-900 shadow-sm me-3 text-uppercase"><i class="fas fa-warehouse"></i></div>
                                    <div>
                                        <div class="fw-900 text-dark mb-0 fs-6">{{ $gudang->nama_gudang }}</div>
                                        <div class="extra-small text-muted fw-bold text-uppercase">ID: {{ $gudang->kode_gudang }}</div>
                                    </div>
                                </div>
                            </td>
                            <td><div class="small fw-bold text-dark opacity-75">{{ $gudang->alamat_gudang }}</div></td>
                            <td class="text-end pe-5">
                                <div class="d-flex justify-content-end gap-3">
                                    <a href="{{ route('gudang.edit', $gudang->id) }}" class="action-btn shadow-sm rounded-circle d-flex align-items-center justify-content-center transition-all">
                                        <i class="fas fa-edit text-primary small"></i>
                                    </a>
                                    <button onclick="confirm('Hapus gudang ini?') || event.stopImmediatePropagation()" 
                                            wire:click="delete({{ $gudang->id }})" 
                                            class="action-btn shadow-sm rounded-circle d-flex align-items-center justify-content-center transition-all">
                                        <i class="fas fa-trash text-danger small"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center py-5"><p class="text-muted fw-900 text-uppercase tracking-widest small">Data Gudang Kosong</p></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($gudangs->hasPages())
            <div class="p-5 bg-white border-top border-light d-flex justify-content-center">
                {{ $gudangs->links() }}
            </div>
            @endif
        </div>
    </div>
</div>