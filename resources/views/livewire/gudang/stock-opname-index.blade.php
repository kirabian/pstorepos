<div wire:poll.10s class="min-vh-100 bg-light-subtle mobile-spacer">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    {{-- Tom Select CSS & JS --}}
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

    <div class="p-4 p-lg-5 animate__animated animate__fadeIn">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5 gap-4">
            <div>
                <h1 class="display-6 fw-black text-dark mb-1 tracking-tight">Stock Opname</h1>
                <p class="text-secondary fw-medium mb-0">Cek fisik stok gudang dan sesuaikan dengan sistem.</p>
            </div>
            <button wire:click="resetInputFields" data-bs-toggle="modal" data-bs-target="#opnameModal" 
                class="btn btn-dark rounded-pill px-5 py-3 fw-bold shadow-lg hover-scale transition-all d-flex align-items-center gap-2">
                <i class="fas fa-clipboard-check fs-5"></i>
                <span>Mulai Opname</span>
            </button>
        </div>

        {{-- TABLE CARD --}}
        <div class="card border-0 shadow-xl rounded-5 overflow-hidden bg-white">
            <div class="p-4 border-bottom border-light-subtle bg-white sticky-top z-1">
                <div class="row g-3 align-items-center">
                    <div class="col-12 col-md-4">
                        <div class="position-relative">
                            <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-4 text-muted opacity-50"></i>
                            <input type="text" wire:model.live.debounce.300ms="search" 
                                class="form-control border-0 bg-light py-3 ps-5 rounded-pill fw-semibold text-dark placeholder-muted focus-ring-dark" 
                                placeholder="Cari nama barang...">
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-5 py-4 text-secondary text-uppercase extra-small fw-bold tracking-widest">Tanggal</th>
                            <th class="py-4 text-secondary text-uppercase extra-small fw-bold tracking-widest">Petugas</th>
                            <th class="py-4 text-secondary text-uppercase extra-small fw-bold tracking-widest">Produk</th>
                            <th class="text-center py-4 text-secondary text-uppercase extra-small fw-bold tracking-widest">Sistem</th>
                            <th class="text-center py-4 text-secondary text-uppercase extra-small fw-bold tracking-widest">Fisik</th>
                            <th class="text-center py-4 text-secondary text-uppercase extra-small fw-bold tracking-widest">Selisih</th>
                            <th class="pe-5 py-4 text-secondary text-uppercase extra-small fw-bold tracking-widest">Ket</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($opnames as $opname)
                        <tr class="group-hover-bg transition-all">
                            <td class="ps-5 py-4">
                                <span class="fw-bold text-dark d-block">{{ \Carbon\Carbon::parse($opname->tanggal_opname)->format('d M Y') }}</span>
                                <span class="text-muted extra-small">{{ \Carbon\Carbon::parse($opname->created_at)->format('H:i') }} WIB</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-circle-sm bg-secondary-subtle text-secondary fw-bold rounded-circle d-flex align-items-center justify-content-center" style="width:30px; height:30px; font-size: 0.7rem;">
                                        {{ substr($opname->user->nama_lengkap ?? 'X', 0, 1) }}
                                    </div>
                                    <span class="fw-bold small text-dark">{{ $opname->user->nama_lengkap ?? 'Unknown' }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="fw-bold text-dark d-block">{{ $opname->tipe->nama }}</span>
                                <span class="badge bg-light text-secondary border extra-small">{{ $opname->tipe->merk->nama ?? '-' }}</span>
                            </td>
                            <td class="text-center fw-bold text-muted">{{ $opname->stok_sistem }}</td>
                            <td class="text-center fw-bold text-primary">{{ $opname->stok_fisik }}</td>
                            <td class="text-center">
                                @if($opname->selisih == 0)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3">Cocok</span>
                                @elseif($opname->selisih > 0)
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3">+{{ $opname->selisih }} (Lebih)</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3">{{ $opname->selisih }} (Kurang)</span>
                                @endif
                            </td>
                            <td class="pe-5 text-muted small fst-italic">
                                {{ $opname->keterangan ?: '-' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="py-5 text-center text-muted">Belum ada riwayat stock opname.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-top bg-white">{{ $opnames->links() }}</div>
        </div>
    </div>

    {{-- MODAL OPNAME --}}
    @teleport('body')
    <div wire:ignore.self class="modal fade" id="opnameModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-2xl rounded-5 overflow-hidden">
                <div class="modal-header bg-white border-0 p-4 pb-0">
                    <div>
                        <h5 class="fw-black text-dark mb-1">Input Stock Opname</h5>
                        <p class="text-secondary small mb-0">Sesuaikan stok fisik gudang.</p>
                    </div>
                    <button type="button" class="btn-close bg-light rounded-circle p-2" data-bs-dismiss="modal" wire:click="resetInputFields"></button>
                </div>
                <div class="modal-body p-4">
                    <form wire:submit.prevent="store">
                        
                        {{-- Select Produk --}}
                        <div class="mb-4" wire:ignore>
                            <label class="fw-bold small text-secondary mb-1">Pilih Produk</label>
                            <select id="select-product" placeholder="Cari barang..." autocomplete="off">
                                <option value="">-- Cari Barang --</option>
                                @foreach($products as $p)
                                    <option value="{{ $p->id }}">{{ $p->merk->nama }} - {{ $p->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        {{-- Hidden Input untuk binding Livewire --}}
                        <input type="hidden" wire:model.live="tipe_id" id="tipe_id_hidden">
                        @error('tipe_id') <small class="text-danger fw-bold d-block mt-n3 mb-3">{{ $message }}</small> @enderror

                        {{-- Info Stok Sistem --}}
                        <div class="p-3 bg-light rounded-4 mb-4 border border-light-subtle d-flex justify-content-between align-items-center">
                            <span class="text-muted small fw-bold">Stok Tercatat (Sistem)</span>
                            <span class="h4 fw-black text-dark mb-0">{{ $currentStokSistem }}</span>
                        </div>

                        {{-- Input Stok Fisik --}}
                        <div class="mb-3">
                            <label class="fw-bold small text-secondary mb-1">Stok Fisik (Hasil Hitung)</label>
                            <input type="number" class="form-control rounded-4 py-3 fw-bold fs-5 text-center border-2 border-dark" 
                                wire:model.live="stok_fisik" placeholder="0">
                            @error('stok_fisik') <small class="text-danger fw-bold">{{ $message }}</small> @enderror
                        </div>

                        {{-- Preview Selisih --}}
                        <div class="mb-3 text-center">
                            @if(is_numeric($selisihPreview) && $selisihPreview != 0)
                                <span class="badge {{ $selisihPreview > 0 ? 'bg-primary' : 'bg-danger' }} rounded-pill px-3 py-2">
                                    Selisih: {{ $selisihPreview > 0 ? '+' : '' }}{{ $selisihPreview }}
                                </span>
                            @elseif(is_numeric($stok_fisik) && $stok_fisik !== '')
                                <span class="badge bg-success rounded-pill px-3 py-2">Stok Cocok (Selisih 0)</span>
                            @endif
                        </div>

                        <div class="mb-4">
                            <label class="fw-bold small text-secondary mb-1">Keterangan / Alasan</label>
                            <textarea class="form-control rounded-4" wire:model="keterangan" rows="2" placeholder="Contoh: Barang rusak 1, Ketemu di rak atas 2..."></textarea>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-dark rounded-4 py-3 fw-bold shadow-sm">
                                <i class="fas fa-save me-2"></i> Simpan Penyesuaian
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
        .hover-scale:hover { transform: scale(1.02); }
        .ts-control { border-radius: 1rem !important; padding: 12px !important; border: 1px solid #dee2e6; }
        .ts-dropdown { border-radius: 1rem !important; overflow: hidden; z-index: 1060 !important; }
        @media (max-width: 991px) { .mobile-spacer { padding-top: 80px !important; } }
    </style>
</div>

@script
<script>
    let tomSelect;

    // Inisialisasi Tom Select
    function initTomSelect() {
        if(tomSelect) tomSelect.destroy();
        
        tomSelect = new TomSelect('#select-product', {
            create: false,
            sortField: { field: "text", direction: "asc" },
            onChange: function(value) {
                // Set nilai ke Livewire property secara manual
                @this.set('tipe_id', value);
            }
        });
    }

    initTomSelect();

    // Reset Dropdown saat modal ditutup/reset
    Livewire.on('reset-select-product', () => {
        if(tomSelect) tomSelect.clear();
    });

    Livewire.on('close-modal', () => {
        const modal = bootstrap.Modal.getInstance(document.getElementById('opnameModal'));
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