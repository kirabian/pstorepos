<div wire:poll.10s class="min-vh-100 bg-light-subtle mobile-spacer">
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="p-4 p-lg-5 animate__animated animate__fadeIn">
        
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5 gap-4">
            <div>
                <h1 class="display-6 fw-black text-dark mb-1 tracking-tight">Marketplace & Online</h1>
                <p class="text-secondary fw-medium mb-0">Kelola daftar toko online resmi PStore.</p>
            </div>
            
            <button wire:click="resetInputFields" data-bs-toggle="modal" data-bs-target="#shopModal" 
                class="btn btn-dark rounded-pill px-5 py-3 fw-bold shadow-lg d-flex align-items-center gap-2 hover-scale">
                <i class="fas fa-plus-circle fs-5"></i> <span>Tambah Toko</span>
            </button>
        </div>

        <div class="card border-0 shadow-xl rounded-5 overflow-hidden bg-white">
            
            <div class="p-4 border-bottom border-light-subtle bg-white sticky-top z-1">
                <div class="row g-3 align-items-center">
                    <div class="col-12 col-md-4">
                        <div class="position-relative">
                            <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-4 text-muted opacity-50"></i>
                            <input type="text" wire:model.live.debounce.300ms="search" 
                                class="form-control border-0 bg-light py-3 ps-5 rounded-pill fw-semibold text-dark placeholder-muted focus-ring-dark" 
                                placeholder="Cari nama toko atau platform...">
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-5 py-4 text-secondary text-uppercase extra-small fw-bold tracking-widest">Nama Toko</th>
                            <th class="py-4 text-secondary text-uppercase extra-small fw-bold tracking-widest">Platform</th>
                            <th class="py-4 text-secondary text-uppercase extra-small fw-bold tracking-widest">Link / URL</th>
                            <th class="py-4 text-center text-secondary text-uppercase extra-small fw-bold tracking-widest">Status</th>
                            <th class="pe-5 py-4 text-end text-secondary text-uppercase extra-small fw-bold tracking-widest">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($shops as $shop)
                        <tr class="group-hover-bg transition-all">
                            <td class="ps-5 py-4">
                                <span class="fw-bold text-dark">{{ $shop->nama_toko }}</span>
                                <div class="small text-muted">{{ Str::limit($shop->deskripsi, 40) }}</div>
                            </td>
                            <td>
                                @php
                                    $badgeColor = match($shop->platform) {
                                        'Shopee' => 'bg-warning text-dark',
                                        'Tokopedia' => 'bg-success text-white',
                                        'TikTok Shop' => 'bg-dark text-white',
                                        'Lazada' => 'bg-primary text-white',
                                        'Instagram Shop' => 'bg-danger text-white',
                                        default => 'bg-secondary text-white'
                                    };
                                @endphp
                                <span class="badge {{ $badgeColor }} rounded-pill px-3 py-2 fw-bold small">
                                    {{ $shop->platform }}
                                </span>
                            </td>
                            <td>
                                @if($shop->url_toko)
                                    <a href="{{ $shop->url_toko }}" target="_blank" class="text-primary fw-bold text-decoration-none small hover-underline">
                                        <i class="fas fa-external-link-alt me-1"></i> Kunjungi Toko
                                    </a>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="form-check form-switch d-flex justify-content-center align-items-center">
                                    <input class="form-check-input cursor-pointer shadow-none" type="checkbox" role="switch" 
                                        wire:click="toggleStatus({{ $shop->id }})" 
                                        {{ $shop->is_active ? 'checked' : '' }} 
                                        style="width: 2.5em; height: 1.25em;">
                                </div>
                            </td>
                            <td class="pe-5 text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    <button wire:click="edit({{ $shop->id }})" data-bs-toggle="modal" data-bs-target="#shopModal" 
                                        class="btn btn-icon btn-light rounded-circle shadow-sm hover-primary transition-all">
                                        <i class="fas fa-pen fa-xs"></i>
                                    </button>
                                    <button wire:confirm="Hapus toko ini?" wire:click="delete({{ $shop->id }})" 
                                        class="btn btn-icon btn-light rounded-circle shadow-sm hover-danger transition-all">
                                        <i class="fas fa-trash fa-xs"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-5 text-center text-muted">Belum ada data toko online.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-top bg-white">{{ $shops->links() }}</div>
        </div>
    </div>

    {{-- MODAL --}}
    @teleport('body')
    <div wire:ignore.self class="modal fade" id="shopModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-2xl rounded-5 overflow-hidden">
                <div class="modal-header bg-white border-0 p-4 pb-0">
                    <h5 class="fw-black text-dark mb-0">{{ $isEdit ? 'Edit Toko' : 'Tambah Toko Baru' }}</h5>
                    <button type="button" class="btn-close bg-light rounded-circle p-2" data-bs-dismiss="modal" wire:click="resetInputFields"></button>
                </div>
                <div class="modal-body p-4">
                    <form wire:submit.prevent="store">
                        <div class="mb-3">
                            <label class="fw-bold small text-secondary mb-1">Platform Marketplace</label>
                            <select class="form-select rounded-4 py-2 fw-bold" wire:model="platform">
                                <option value="">-- Pilih Platform --</option>
                                @foreach($platforms as $p)
                                    <option value="{{ $p }}">{{ $p }}</option>
                                @endforeach
                            </select>
                            @error('platform') <small class="text-danger fw-bold">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="fw-bold small text-secondary mb-1">Nama Toko</label>
                            <input type="text" class="form-control rounded-4 py-2 fw-bold" wire:model="nama_toko" placeholder="Contoh: PStore Shopee JKT">
                            @error('nama_toko') <small class="text-danger fw-bold">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="fw-bold small text-secondary mb-1">Link URL Toko (Opsional)</label>
                            <input type="url" class="form-control rounded-4 py-2" wire:model="url_toko" placeholder="https://shopee.co.id/...">
                            @error('url_toko') <small class="text-danger fw-bold">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="fw-bold small text-secondary mb-1">Deskripsi Singkat</label>
                            <textarea class="form-control rounded-4" wire:model="deskripsi" rows="2" placeholder="Keterangan tambahan..."></textarea>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-dark rounded-4 py-2 fw-bold shadow-sm">
                                {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Data' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endteleport

    <style>
        .fw-black { font-weight: 900; }
        .hover-scale:hover { transform: scale(1.02); transition: 0.2s; }
        .btn-icon { width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; }
        .hover-primary:hover { background-color: #0d6efd; color: white; }
        .hover-danger:hover { background-color: #dc3545; color: white; }
        .hover-underline:hover { text-decoration: underline !important; }
        @media (max-width: 991px) { .mobile-spacer { padding-top: 80px !important; } }
    </style>
</div>

@script
<script>
    Livewire.on('close-modal', () => {
        const modal = bootstrap.Modal.getInstance(document.getElementById('shopModal'));
        if(modal) modal.hide();
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
    });
    Livewire.on('swal', (data) => {
        Swal.fire({
            title: data[0].title, text: data[0].text, icon: data[0].icon,
            toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true
        });
    });
</script>
@endscript