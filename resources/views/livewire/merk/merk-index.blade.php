<div class="container-fluid">
    
    {{-- CSS Jaga-jaga agar modal tetap paling depan --}}
    <style>
        .modal-backdrop { z-index: 1055 !important; }
        .modal { z-index: 1060 !important; }
    </style>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-black">Manajemen Merk</h4>
        <button wire:click="resetInputFields" data-bs-toggle="modal" data-bs-target="#merkModal" class="btn btn-dark rounded-3 px-4 shadow-sm">
            <i class="fas fa-plus me-2"></i> Tambah Merk
        </button>
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <div class="mb-3 d-flex justify-content-end">
                <div class="input-group w-25">
                    <span class="input-group-text bg-white border-end-0 rounded-start-3"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" class="form-control border-start-0 rounded-end-3 ps-0" placeholder="Cari Merk..." wire:model.live.debounce.300ms="search">
                </div>
            </div>

            <div class="table-responsive" wire:poll.5s>
                <table class="table table-hover align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-3 px-3 rounded-start-3 text-secondary small text-uppercase fw-bold">No</th>
                            <th class="py-3 text-secondary small text-uppercase fw-bold">Nama</th>
                            <th class="py-3 text-secondary small text-uppercase fw-bold">Deskripsi</th>
                            <th class="py-3 text-secondary small text-uppercase fw-bold">Dibuat</th>
                            <th class="py-3 text-secondary small text-uppercase fw-bold">Diperbarui</th>
                            <th class="py-3 px-3 rounded-end-3 text-secondary small text-uppercase fw-bold text-end">#</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($merks as $index => $merk)
                            <tr>
                                <td class="px-3 fw-bold">{{ $merks->firstItem() + $index }}</td>
                                <td class="fw-bold text-primary">{{ $merk->nama }}</td>
                                <td class="text-muted">{{ $merk->deskripsi ?? '-' }}</td>
                                <td class="small text-secondary">{{ $merk->created_at->format('d M Y, H:i') }}</td>
                                <td class="small text-secondary">{{ $merk->updated_at->format('d M Y, H:i') }}</td>
                                <td class="px-3 text-end">
                                    <button wire:click="edit({{ $merk->id }})" class="btn btn-sm btn-light border rounded-3 me-1" data-bs-toggle="modal" data-bs-target="#merkModal">
                                        <i class="fas fa-pencil-alt text-dark"></i>
                                    </button>
                                    <button wire:confirm="Yakin ingin menghapus merk ini?" wire:click="delete({{ $merk->id }})" class="btn btn-sm btn-light border rounded-3 hover-danger">
                                        <i class="fas fa-trash-alt text-danger"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">Belum ada data merk.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $merks->links() }}
            </div>
        </div>
    </div>

    {{-- 
        SOLUSI UTAMA: @teleport('body') 
        Ini akan memindahkan Modal keluar dari Layout, langsung ke body HTML.
        Sehingga tidak akan tertutup background atau sidebar lagi.
    --}}
    @teleport('body')
    <div wire:ignore.self class="modal fade" id="merkModal" tabindex="-1" aria-labelledby="merkModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold" id="merkModalLabel">
                        {{ $isEdit ? 'Edit Merk' : 'Tambah Merk Baru' }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" wire:click="resetInputFields"></button>
                </div>
                
                <div class="modal-body pt-4">
                    <form wire:submit.prevent="store">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase text-secondary">Nama Merk <span class="text-danger">*</span></label>
                            <input type="text" class="form-control rounded-3 py-2 @error('nama') is-invalid @enderror" 
                                   wire:model="nama" 
                                   placeholder="Contoh: Apple, Samsung">
                            @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase text-secondary">Deskripsi</label>
                            <textarea class="form-control rounded-3 py-2 @error('deskripsi') is-invalid @enderror" 
                                      wire:model="deskripsi" 
                                      rows="3" 
                                      placeholder="Deskripsi singkat..."></textarea>
                            @error('deskripsi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-dark rounded-3 py-2 fw-bold">
                                <span wire:loading.remove wire:target="store">
                                    {{ $isEdit ? 'Update Data' : 'Simpan Data' }}
                                </span>
                                <span wire:loading wire:target="store">
                                    <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                    Menyimpan...
                                </span>
                            </button>
                            <button type="button" class="btn btn-light rounded-3 py-2 text-muted" data-bs-dismiss="modal" wire:click="resetInputFields">
                                Batal
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
        if (modal) { modal.hide(); }
        
        // Hapus manual backdrop jika tersisa
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
    });
</script>
@endscript