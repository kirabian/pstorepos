<div class="container-fluid">
    
    {{-- CDN SweetAlert2 (Wajib ada untuk notifikasi bagus) --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Style Custom untuk UI yang lebih premium --}}
    <style>
        .modal-backdrop { z-index: 1055 !important; }
        .modal { z-index: 1060 !important; }
        
        /* Table Styling */
        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
            transform: translateY(-1px);
            transition: all 0.2s ease;
        }
        .btn-action {
            width: 35px;
            height: 35px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }
        .btn-action:hover { transform: scale(1.1); }
        
        /* Search Bar Styling */
        .search-container .form-control:focus {
            box-shadow: none;
            border-color: #dee2e6;
        }
        .search-container {
            border: 1px solid #dee2e6;
            padding: 2px;
            background: white;
        }
    </style>

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold text-black mb-1">Manajemen Merk</h4>
            <p class="text-secondary small mb-0">Kelola daftar merk produk (Brands) dalam sistem.</p>
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
                    <span class="input-group-text bg-transparent border-0 text-muted ps-3">
                        <i class="fas fa-search"></i>
                    </span>
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
                            <th class="py-3 text-secondary small text-uppercase fw-bold">Deskripsi</th>
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
                                </td>
                                <td class="text-muted small">
                                    {{ Str::limit($merk->deskripsi, 50) ?: '-' }}
                                </td>
                                <td class="small text-secondary">
                                    <div class="d-flex flex-column">
                                        <span>{{ $merk->updated_at->format('d M Y') }}</span>
                                        <span class="text-xs text-muted">{{ $merk->updated_at->format('H:i') }} WIB</span>
                                    </div>
                                </td>
                                <td class="px-4 text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <button wire:click="edit({{ $merk->id }})" 
                                                data-bs-toggle="modal" data-bs-target="#merkModal"
                                                class="btn btn-action btn-light border rounded-circle text-primary" 
                                                title="Edit">
                                            <i class="fas fa-pencil-alt fa-sm"></i>
                                        </button>
                                        
                                        <button wire:confirm="Yakin ingin menghapus merk '{{ $merk->nama }}'?" 
                                                wire:click="delete({{ $merk->id }})" 
                                                class="btn btn-action btn-light border rounded-circle text-danger hover-danger"
                                                title="Hapus">
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
                                        <p class="small text-muted mb-0">Coba kata kunci lain atau tambahkan merk baru.</p>
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
    <div wire:ignore.self class="modal fade" id="merkModal" tabindex="-1" aria-labelledby="merkModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
                
                <div class="modal-header border-bottom bg-light px-4 py-3">
                    <h5 class="modal-title fw-bold" id="merkModalLabel">
                        <i class="{{ $isEdit ? 'fas fa-edit text-warning' : 'fas fa-plus-circle text-success' }} me-2"></i>
                        {{ $isEdit ? 'Edit Data Merk' : 'Tambah Merk Baru' }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" wire:click="resetInputFields"></button>
                </div>
                
                <div class="modal-body p-4">
                    <form wire:submit.prevent="store">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase text-secondary">Nama Merk <span class="text-danger">*</span></label>
                            <input type="text" class="form-control rounded-3 py-2 px-3 @error('nama') is-invalid @enderror" 
                                   wire:model="nama" 
                                   placeholder="Contoh: Apple, Samsung, Xiaomi">
                            @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase text-secondary">Deskripsi (Opsional)</label>
                            <textarea class="form-control rounded-3 py-2 px-3 @error('deskripsi') is-invalid @enderror" 
                                      wire:model="deskripsi" 
                                      rows="3" 
                                      placeholder="Tambahkan keterangan singkat..."></textarea>
                            @error('deskripsi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="d-flex gap-2 pt-2">
                            <button type="button" class="btn btn-light rounded-3 w-100 py-2 fw-bold text-secondary" data-bs-dismiss="modal" wire:click="resetInputFields">
                                Batal
                            </button>
                            <button type="submit" class="btn btn-dark rounded-3 w-100 py-2 fw-bold">
                                <span wire:loading.remove wire:target="store">
                                    {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Data' }}
                                </span>
                                <span wire:loading wire:target="store">
                                    <span class="spinner-border spinner-border-sm me-2"></span> Menyimpan...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endteleport

</div>

{{-- SCRIPT HANDLER --}}
@script
<script>
    // 1. Handle Tutup Modal
    Livewire.on('close-modal', () => {
        const modalEl = document.getElementById('merkModal');
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) { modal.hide(); }
        
        // Bersihkan sisa backdrop jika ada bug
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
    });

    // 2. Handle Notifikasi SweetAlert (Toast)
    Livewire.on('swal', (data) => {
        // Ambil data pertama dari array event (karena Livewire 3 kirim array)
        const eventData = data[0]; 

        // Inisialisasi Toast SweetAlert
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        // Tampilkan Toast
        Toast.fire({
            icon: eventData.icon,
            title: eventData.title,
            text: eventData.text
        });
    });
</script>
@endscript