<div class="container-fluid">
    
    {{-- CDN SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        .modal-backdrop { z-index: 1055 !important; }
        .modal { z-index: 1060 !important; }
        .table-hover tbody tr:hover { background-color: #f8f9fa; transform: translateY(-1px); transition: all 0.2s ease; }
        .btn-action { width: 35px; height: 35px; display: inline-flex; align-items: center; justify-content: center; transition: all 0.2s; }
        .btn-action:hover { transform: scale(1.1); }
        .search-container .form-control:focus { box-shadow: none; border-color: #dee2e6; }
        .search-container { border: 1px solid #dee2e6; padding: 2px; background: white; }

        /* Style Custom Checkbox Button */
        .btn-check:checked + .btn-outline-custom { background-color: #212529; color: white; border-color: #212529; }
        .btn-outline-custom { border: 1px solid #dee2e6; color: #6c757d; transition: all 0.2s; }
        .btn-outline-custom:hover { background-color: #f8f9fa; }
    </style>

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold text-black mb-1">Manajemen Merk</h4>
            <p class="text-secondary small mb-0">Kelola daftar merk produk dan kategorinya.</p>
        </div>
        
        <button wire:click="resetInputFields" data-bs-toggle="modal" data-bs-target="#merkModal" 
                class="btn btn-dark rounded-3 px-4 py-2 shadow-sm d-flex align-items-center gap-2">
            <i class="fas fa-plus-circle"></i> 
            <span>Tambah Merk</span>
        </button>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            
            <div class="p-4 border-bottom bg-white d-flex justify-content-end">
                <div class="input-group search-container rounded-3" style="max-width: 300px;">
                    <span class="input-group-text bg-transparent border-0 text-muted ps-3"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control border-0 bg-transparent" 
                           placeholder="Cari nama merk..." 
                           wire:model.live.debounce.300ms="search">
                </div>
            </div>

            <div class="table-responsive" wire:poll.5s>
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-3 px-4 text-secondary small text-uppercase fw-bold" width="5%">No</th>
                            <th class="py-3 text-secondary small text-uppercase fw-bold" width="25%">Nama Merk</th>
                            <th class="py-3 text-secondary small text-uppercase fw-bold">Kategori Support</th>
                            <th class="py-3 text-secondary small text-uppercase fw-bold text-nowrap" width="15%">Terakhir Update</th>
                            <th class="py-3 px-4 text-secondary small text-uppercase fw-bold text-end" width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($merks as $index => $merk)
                            <tr>
                                <td class="px-4 fw-bold text-muted">{{ $merks->firstItem() + $index }}</td>
                                <td>
                                    <span class="fw-bold text-dark">{{ $merk->nama }}</span>
                                    <div class="small text-muted">{{ Str::limit($merk->deskripsi, 30) }}</div>
                                </td>
                                <td>
                                    @if(!empty($merk->kategori))
                                        <div class="d-flex gap-1 flex-wrap">
                                            @if(in_array('imei', $merk->kategori))
                                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-2">IMEI</span>
                                            @endif
                                            @if(in_array('non_imei', $merk->kategori))
                                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-2">Aksesoris</span>
                                            @endif
                                            @if(in_array('jasa', $merk->kategori))
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-2">Jasa</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td class="small text-secondary">
                                    {{ $merk->updated_at->format('d M Y') }}
                                </td>
                                <td class="px-4 text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <button wire:click="edit({{ $merk->id }})" data-bs-toggle="modal" data-bs-target="#merkModal"
                                                class="btn btn-action btn-light border rounded-circle text-primary" title="Edit">
                                            <i class="fas fa-pencil-alt fa-sm"></i>
                                        </button>
                                        <button wire:confirm="Hapus merk ini?" wire:click="delete({{ $merk->id }})" 
                                                class="btn btn-action btn-light border rounded-circle text-danger hover-danger" title="Hapus">
                                            <i class="fas fa-trash-alt fa-sm"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center py-4 opacity-50">
                                        <i class="fas fa-box-open fa-3x mb-3 text-secondary"></i>
                                        <h6 class="fw-bold text-secondary mb-1">Tidak ada data ditemukan</h6>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-top">
                {{ $merks->links() }}
            </div>
        </div>
    </div>

    @teleport('body')
    <div wire:ignore.self class="modal fade" id="merkModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
                <div class="modal-header border-bottom bg-light px-4 py-3">
                    <h5 class="modal-title fw-bold">
                        {{ $isEdit ? 'Edit Data Merk' : 'Tambah Merk Baru' }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" wire:click="resetInputFields"></button>
                </div>
                
                <div class="modal-body p-4">
                    <form wire:submit.prevent="store">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase text-secondary">Nama Merk <span class="text-danger">*</span></label>
                            <input type="text" class="form-control rounded-3 py-2 px-3 @error('nama') is-invalid @enderror" 
                                   wire:model="nama" placeholder="Contoh: Apple, Samsung">
                            @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- BAGIAN PILIH KATEGORI (CHECKBOX STYLE) --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase text-secondary">Kategori Support <span class="text-danger">*</span></label>
                            <div class="row g-2">
                                <div class="col-4">
                                    {{-- Menggunakan array wire:model untuk multiple select --}}
                                    <input type="checkbox" class="btn-check" id="cat_imei" value="imei" wire:model="kategori">
                                    <label class="btn btn-outline-custom w-100 py-2 rounded-3 text-center" for="cat_imei">
                                        <i class="fas fa-mobile-alt d-block mb-1 fs-5"></i> <span class="small fw-bold">HP / IMEI</span>
                                    </label>
                                </div>
                                <div class="col-4">
                                    <input type="checkbox" class="btn-check" id="cat_non" value="non_imei" wire:model="kategori">
                                    <label class="btn btn-outline-custom w-100 py-2 rounded-3 text-center" for="cat_non">
                                        <i class="fas fa-headphones d-block mb-1 fs-5"></i> <span class="small fw-bold">Aksesoris</span>
                                    </label>
                                </div>
                                <div class="col-4">
                                    <input type="checkbox" class="btn-check" id="cat_jasa" value="jasa" wire:model="kategori">
                                    <label class="btn btn-outline-custom w-100 py-2 rounded-3 text-center" for="cat_jasa">
                                        <i class="fas fa-tools d-block mb-1 fs-5"></i> <span class="small fw-bold">Jasa</span>
                                    </label>
                                </div>
                            </div>
                            @error('kategori') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            <div class="form-text small">Satu merk bisa mendukung lebih dari satu kategori.</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase text-secondary">Deskripsi</label>
                            <textarea class="form-control rounded-3 py-2 px-3" wire:model="deskripsi" rows="2" placeholder="Keterangan..."></textarea>
                        </div>

                        <div class="d-flex gap-2 pt-2">
                            <button type="button" class="btn btn-light rounded-3 w-100 py-2 fw-bold" data-bs-dismiss="modal" wire:click="resetInputFields">Batal</button>
                            <button type="submit" class="btn btn-dark rounded-3 w-100 py-2 fw-bold">
                                <span wire:loading.remove wire:target="store">{{ $isEdit ? 'Simpan Perubahan' : 'Simpan Data' }}</span>
                                <span wire:loading wire:target="store">Menyimpan...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endteleport
</div>

@script
<script>
    Livewire.on('close-modal', () => {
        const modalEl = document.getElementById('merkModal');
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
    });
    Livewire.on('swal', (data) => {
        const eventData = data[0]; 
        Swal.mixin({
            toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true
        }).fire({ icon: eventData.icon, title: eventData.title, text: eventData.text });
    });
</script>
@endscript