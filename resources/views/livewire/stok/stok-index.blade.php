<div class="container-fluid">
    
    {{-- Dependencies --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .modal-backdrop { z-index: 1055 !important; }
        .modal { z-index: 1060 !important; }
        .bg-gradient-primary { background: linear-gradient(45deg, #1f2937, #111827); }
    </style>

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold text-black mb-1">Stok Unit / Inventory</h4>
            <p class="text-secondary small mb-0">Kelola stok unit fisik (IMEI), harga modal, dan harga jual.</p>
        </div>
        <button wire:click="resetInputFields" data-bs-toggle="modal" data-bs-target="#stokModal" 
                class="btn btn-dark rounded-3 px-4 py-2 shadow-sm">
            <i class="fas fa-plus-circle me-2"></i> Tambah Stok
        </button>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="p-4 border-bottom bg-white d-flex justify-content-end">
                <input type="text" class="form-control w-25 rounded-3" placeholder="Cari IMEI / Merk..." wire:model.live.debounce.300ms="search">
            </div>

            <div class="table-responsive" wire:poll.10s>
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-3 px-4 text-secondary small fw-bold">No</th>
                            <th class="py-3 text-secondary small fw-bold">Produk</th>
                            <th class="py-3 text-secondary small fw-bold">Spek / Kondisi</th>
                            <th class="py-3 text-secondary small fw-bold">IMEI</th>
                            <th class="py-3 text-secondary small fw-bold text-end">Harga Jual</th>
                            <th class="py-3 px-4 text-secondary small fw-bold text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stoks as $index => $stok)
                            <tr>
                                <td class="px-4 text-muted fw-bold">{{ $stoks->firstItem() + $index }}</td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold text-dark">{{ $stok->merk->nama }}</span>
                                        <span class="small text-muted">{{ $stok->tipe->nama }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary rounded-2">{{ $stok->ram_storage }}</span>
                                    @if($stok->kondisi == 'Baru')
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success rounded-2">NEW</span>
                                    @else
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning rounded-2">2ND</span>
                                    @endif
                                </td>
                                <td class="font-monospace text-primary">{{ $stok->imei }}</td>
                                <td class="text-end fw-bold">
                                    Rp {{ number_format($stok->harga_jual, 0, ',', '.') }}
                                    <div class="small text-muted fw-normal" style="font-size: 0.7rem;">
                                        Modal: Rp {{ number_format($stok->harga_modal, 0, ',', '.') }}
                                    </div>
                                </td>
                                <td class="px-4 text-end">
                                    <button wire:click="edit({{ $stok->id }})" data-bs-toggle="modal" data-bs-target="#stokModal" class="btn btn-sm btn-light border rounded-circle text-primary"><i class="fas fa-pencil-alt"></i></button>
                                    <button wire:confirm="Hapus stok ini?" wire:click="delete({{ $stok->id }})" class="btn btn-sm btn-light border rounded-circle text-danger hover-danger"><i class="fas fa-trash-alt"></i></button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center py-5 text-muted">Belum ada stok.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-top">{{ $stoks->links() }}</div>
        </div>
    </div>

    @teleport('body')
    <div wire:ignore.self class="modal fade" id="stokModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header border-bottom bg-light px-4 py-3">
                    <h5 class="modal-title fw-bold">{{ $isEdit ? 'Edit Stok' : 'Tambah Stok Unit' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" wire:click="resetInputFields"></button>
                </div>
                <div class="modal-body p-4">
                    <form wire:submit.prevent="store">
                        
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Merk <span class="text-danger">*</span></label>
                            <select class="form-select rounded-3 py-2" wire:model.live="merk_id">
                                <option value="">-- Pilih Merk --</option>
                                @foreach($merks as $m)
                                    <option value="{{ $m->id }}">{{ $m->nama }}</option>
                                @endforeach
                            </select>
                            @error('merk_id') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Tipe / Model <span class="text-danger">*</span></label>
                            <select class="form-select rounded-3 py-2" wire:model.live="tipe_id" {{ empty($merk_id) ? 'disabled' : '' }}>
                                <option value="">-- Pilih Tipe --</option>
                                @foreach($tipeOptions as $t)
                                    <option value="{{ $t->id }}">{{ $t->nama }}</option>
                                @endforeach
                            </select>
                            @error('tipe_id') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label small fw-bold text-secondary">RAM/Storage <span class="text-danger">*</span></label>
                                <select class="form-select rounded-3 py-2" wire:model="ram_storage" {{ empty($tipe_id) ? 'disabled' : '' }}>
                                    <option value="">-- Pilih --</option>
                                    @foreach($ramOptions as $ram)
                                        <option value="{{ $ram }}">{{ $ram }}</option>
                                    @endforeach
                                </select>
                                @error('ram_storage') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold text-secondary">Kondisi <span class="text-danger">*</span></label>
                                <select class="form-select rounded-3 py-2" wire:model="kondisi">
                                    <option value="Baru">Baru (New)</option>
                                    <option value="Second">Second (Bekas)</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Nomor IMEI <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-barcode text-muted"></i></span>
                                <input type="text" class="form-control border-start-0 rounded-end-3 py-2" wire:model="imei" placeholder="Scan atau ketik IMEI...">
                            </div>
                            @error('imei') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Harga Modal (Opsional)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">Rp</span>
                                <input type="number" class="form-control border-start-0 rounded-end-3 py-2" wire:model.live.debounce.500ms="harga_modal" placeholder="0">
                            </div>
                            <div class="form-text small">Kosongkan jika stok titipan / belum bayar.</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-secondary">Harga Jual (SRP) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">Rp</span>
                                <input type="number" class="form-control border-start-0 rounded-end-3 py-2 fw-bold text-success" wire:model="harga_jual">
                            </div>
                            <div class="form-text small">Otomatis dihitung (Modal + 10%), bisa diubah manual.</div>
                            @error('harga_jual') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-dark rounded-3 py-2 fw-bold">Simpan Stok</button>
                            <button type="button" class="btn btn-light rounded-3 py-2" data-bs-dismiss="modal" wire:click="resetInputFields">Batal</button>
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
        const modalEl = document.getElementById('stokModal');
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
    });
    
    Livewire.on('swal', (data) => {
        Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true })
            .fire({ icon: data[0].icon, title: data[0].title, text: data[0].text });
    });
</script>
@endscript