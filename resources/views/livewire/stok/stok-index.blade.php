<div class="container-fluid">
    
    {{-- Dependencies --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .modal-backdrop { z-index: 1055 !important; }
        .modal { z-index: 1060 !important; }
        .bg-gradient-primary { background: linear-gradient(45deg, #1f2937, #111827); }
        
        /* Style Checkbox Custom */
        .form-check-input:checked {
            background-color: #212529;
            border-color: #212529;
        }
    </style>

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold text-black mb-1">Stok Unit / Inventory</h4>
            <p class="text-secondary small mb-0">Total Stok: <span class="fw-bold text-dark">{{ $stoks->total() }}</span> Unit</p>
        </div>
        <div class="d-flex gap-2">
            {{-- TOMBOL KELUAR STOK (BARU) --}}
            <button wire:click="openKeluarStokModal" 
                    class="btn btn-dark rounded-3 px-4 py-2 shadow-sm position-relative">
                <i class="fas fa-sign-out-alt me-2"></i> Keluar Stok
                @if(count($selectedStok) > 0)
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light">
                        {{ count($selectedStok) }}
                    </span>
                @endif
            </button>

            <button wire:click="resetInputFields" data-bs-toggle="modal" data-bs-target="#stokModal" 
                    class="btn btn-outline-dark rounded-3 px-4 py-2 shadow-sm">
                <i class="fas fa-plus-circle me-2"></i> Tambah / Masuk
            </button>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="p-4 border-bottom bg-white d-flex justify-content-between align-items-center">
                <div class="small text-muted">
                    @if(count($selectedStok) > 0)
                        <span class="text-primary fw-bold">{{ count($selectedStok) }}</span> item dipilih.
                    @else
                        Kelola stok unit fisik (IMEI).
                    @endif
                </div>
                <input type="text" class="form-control w-25 rounded-3" placeholder="Cari IMEI / Merk..." wire:model.live.debounce.300ms="search">
            </div>

            <div class="table-responsive" wire:poll.10s>
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            {{-- KOLOM CHECKBOX HEADER --}}
                            <th class="py-3 px-4 text-center" style="width: 50px;">
                                <input type="checkbox" class="form-check-input" wire:model.live="selectAll">
                            </th>
                            <th class="py-3 text-secondary small fw-bold">No</th>
                            <th class="py-3 text-secondary small fw-bold">Produk</th>
                            <th class="py-3 text-secondary small fw-bold">Spek / Kondisi</th>
                            <th class="py-3 text-secondary small fw-bold">IMEI</th>
                            <th class="py-3 text-secondary small fw-bold text-end">Harga Jual</th>
                            <th class="py-3 px-4 text-secondary small fw-bold text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stoks as $index => $stok)
                            <tr class="{{ in_array($stok->id, $selectedStok) ? 'table-active' : '' }}">
                                {{-- KOLOM CHECKBOX ROW --}}
                                <td class="px-4 text-center">
                                    <input type="checkbox" class="form-check-input" wire:model.live="selectedStok" value="{{ $stok->id }}">
                                </td>
                                <td class="text-muted fw-bold">{{ $stoks->firstItem() + $index }}</td>
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
                            <tr><td colspan="7" class="text-center py-5 text-muted">Belum ada stok.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-top">{{ $stoks->links() }}</div>
        </div>
    </div>

    {{-- MODAL 1: TAMBAH/EDIT STOK (FORM LENGKAP) --}}
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

    {{-- MODAL 2: STOK KELUAR (BARU) --}}
    @teleport('body')
    <div wire:ignore.self class="modal fade" id="keluarStokModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header border-0 px-4 pt-4 pb-0">
                    <div>
                        <h5 class="modal-title fw-bold">Stok Keluar</h5>
                        <p class="text-secondary small mb-0">Keluarkan stok dari gudang aktif.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <form wire:submit.prevent="storeKeluarStok">
                        
                        {{-- Input Kategori --}}
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-danger">* Kategori</label>
                            <select class="form-select rounded-3 py-2 {{ $errors->has('kategoriKeluar') ? 'is-invalid' : '' }}" 
                                    wire:model="kategoriKeluar">
                                <option value="">Pilih Kategori...</option>
                                @foreach($opsiKategori as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('kategoriKeluar') 
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                            <div class="form-text text-danger small" style="font-size: 0.75rem;">Kategori keluar barang dibutuhkan</div>
                        </div>

                        {{-- List Barang (Preview) --}}
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-secondary">List Barang ({{ count($selectedItems) }} Unit)</label>
                            <div class="border rounded-3 overflow-hidden">
                                <div class="table-responsive" style="max-height: 200px;">
                                    <table class="table table-sm table-striped mb-0 small">
                                        <thead class="bg-light sticky-top">
                                            <tr>
                                                <th class="ps-3">Merk</th>
                                                <th>Tipe</th>
                                                <th>IMEI</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($selectedItems as $item)
                                                <tr>
                                                    <td class="ps-3 fw-bold">{{ $item->merk->nama }}</td>
                                                    <td>{{ $item->tipe->nama }}</td>
                                                    <td class="font-monospace">{{ $item->imei }}</td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="3" class="text-center text-muted">Tidak ada item dipilih</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="text-end mt-1">
                                <small class="text-muted fst-italic">(geser tabel untuk melihat lengkap)</small>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-dark rounded-pill w-100 py-2" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-black rounded-pill w-100 py-2 fw-bold text-white" style="background-color: #000;">Simpan</button>
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
    // Tutup Modal Tambah Stok Biasa
    Livewire.on('close-modal', () => {
        const modalEl = document.getElementById('stokModal');
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
    });

    // Buka Modal Keluar Stok (Triggered by Controller)
    Livewire.on('open-keluar-modal', () => {
        const modalEl = document.getElementById('keluarStokModal');
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    });

    // Tutup Modal Keluar Stok
    Livewire.on('close-keluar-modal', () => {
        const modalEl = document.getElementById('keluarStokModal');
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
    });
    
    // Swal Alert
    Livewire.on('swal', (data) => {
        Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true })
            .fire({ icon: data[0].icon, title: data[0].title, text: data[0].text });
    });
</script>
@endscript