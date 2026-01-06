<div class="container-fluid">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-black">Manajemen Merk</h4>
        <button wire:click="resetInputFields" data-bs-toggle="modal" data-bs-target="#merkModal" class="btn btn-dark rounded-3 px-4">
            <i class="fas fa-plus me-2"></i> Tambah Merk
        </button>
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            
            <div class="mb-3 d-flex justify-content-end">
                <input type="text" class="form-control w-25 rounded-3" placeholder="Cari Merk..." wire:model.live.debounce.300ms="search">
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
                                <td class="fw-bold">{{ $merk->nama }}</td>
                                <td class="text-muted">{{ $merk->deskripsi ?? '-' }}</td>
                                <td class="small text-secondary">{{ $merk->created_at->format('d-m-Y H:i:s') }}</td>
                                <td class="small text-secondary">{{ $merk->updated_at->format('d-m-Y H:i:s') }}</td>
                                <td class="px-3 text-end">
                                    <button wire:click="edit({{ $merk->id }})" class="btn btn-sm btn-dark rounded-3 me-1" data-bs-toggle="modal" data-bs-target="#merkModal">
                                        <i class="fas fa-pencil-alt"></i>
                                    </button>
                                    <button wire:confirm="Yakin ingin menghapus merk ini?" wire:click="delete({{ $merk->id }})" class="btn btn-sm btn-outline-danger rounded-3">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fas fa-box-open fs-1 mb-3 d-block opacity-25"></i>
                                    Belum ada data merk.
                                </td>
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

    <div wire:ignore.self class="modal fade" id="merkModal" tabindex="-1" aria-labelledby="merkModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold" id="merkModalLabel">
                        {{ $isEdit ? 'Edit Merk' : 'Tambah Merk Baru' }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" wire:click="resetInputFields"></button>
                </div>
                <div class="modal-body pt-4">
                    <form wire:submit.prevent="store">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase text-secondary">Nama Merk</label>
                            <input type="text" class="form-control rounded-3 @error('nama') is-invalid @enderror" wire:model="nama" placeholder="Contoh: Apple, Samsung">
                            @error('nama') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase text-secondary">Deskripsi</label>
                            <textarea class="form-control rounded-3 @error('deskripsi') is-invalid @enderror" wire:model="deskripsi" rows="3" placeholder="Deskripsi singkat..."></textarea>
                            @error('deskripsi') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-dark rounded-3 py-2 fw-bold">
                                {{ $isEdit ? 'Update Data' : 'Simpan Data' }}
                                <div wire:loading wire:target="store" class="spinner-border spinner-border-sm ms-2" role="status"></div>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@script
<script>
    // Menutup modal otomatis saat event 'close-modal' dikirim dari component
    Livewire.on('close-modal', () => {
        var myModalEl = document.getElementById('merkModal');
        var modal = bootstrap.Modal.getInstance(myModalEl);
        if (modal) {
            modal.hide();
        }
    });

    // Membuka modal otomatis saat edit (opsional jika tombol edit sudah pakai data-bs-toggle)
    Livewire.on('open-modal', () => {
        // Logika tambahan jika diperlukan
    });
</script>
@endscript