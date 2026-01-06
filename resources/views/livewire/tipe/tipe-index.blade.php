<div class="container-fluid">
    
    {{-- DEPENDENCIES --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

    <style>
        .modal-backdrop { z-index: 1055 !important; }
        .modal { z-index: 1060 !important; }
        .ts-dropdown, .ts-control { z-index: 1070 !important; }
        
        .badge-ram { background-color: #eef2ff; color: #4f46e5; border: 1px solid #c7d2fe; font-weight: 500; font-size: 0.75rem; }
        .badge-jenis-imei { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .badge-jenis-non { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
        .badge-jenis-jasa { background: #e0e7ff; color: #3730a3; border: 1px solid #c7d2fe; }

        .search-container { width: 100%; }
        @media (min-width: 768px) { .search-container { width: 300px; } }
        
        .btn-check:checked + .btn-outline-custom { background-color: #212529; color: white; border-color: #212529; }
        .btn-outline-custom { border: 1px solid #dee2e6; color: #6c757d; }
        .btn-outline-custom:hover { background-color: #f8f9fa; }
    </style>

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold text-black mb-1">Manajemen Tipe & Produk</h4>
            <p class="text-secondary small mb-0">Kelola master data tipe HP, aksesoris, dan jasa.</p>
        </div>
        <button wire:click="resetInputFields" data-bs-toggle="modal" data-bs-target="#tipeModal" 
                class="btn btn-dark rounded-3 px-4 py-2 shadow-sm d-flex align-items-center justify-content-center w-100 w-md-auto gap-2">
            <i class="fas fa-plus-circle"></i> <span>Tambah Data</span>
        </button>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="p-3 p-md-4 border-bottom bg-white d-flex justify-content-end">
                <div class="input-group rounded-3 search-container" style="border: 1px solid #dee2e6;">
                    <span class="input-group-text bg-transparent border-0 text-muted ps-3"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control border-0 bg-transparent" placeholder="Cari Tipe..." wire:model.live.debounce.300ms="search">
                </div>
            </div>

            <div class="table-responsive" wire:poll.5s>
                <table class="table table-hover align-middle mb-0 text-nowrap">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-3 px-3 px-md-4 text-secondary small text-uppercase fw-bold d-none d-md-table-cell">No</th>
                            <th class="py-3 px-3 text-secondary small text-uppercase fw-bold">Nama Item</th>
                            <th class="py-3 text-secondary small text-uppercase fw-bold">Kategori</th>
                            <th class="py-3 text-secondary small text-uppercase fw-bold">Jenis</th>
                            <th class="py-3 text-secondary small text-uppercase fw-bold" width="30%">Varian / Keterangan</th>
                            <th class="py-3 px-3 px-md-4 text-secondary small text-uppercase fw-bold text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tipes as $index => $tipe)
                            <tr>
                                <td class="px-3 px-md-4 fw-bold text-muted d-none d-md-table-cell">{{ $tipes->firstItem() + $index }}</td>
                                <td class="px-3"><span class="fw-bold text-dark">{{ $tipe->nama }}</span></td>
                                <td>
                                    <span class="badge bg-light text-dark border rounded-pill px-3 fw-normal">
                                        {{ $tipe->merk->nama ?? 'Unknown' }}
                                    </span>
                                </td>
                                <td>
                                    @if($tipe->jenis == 'imei')
                                        <span class="badge badge-jenis-imei rounded-2 px-2"><i class="fas fa-mobile-alt me-1"></i> HP/Unit</span>
                                    @elseif($tipe->jenis == 'non_imei')
                                        <span class="badge badge-jenis-non rounded-2 px-2"><i class="fas fa-box me-1"></i> Aksesoris</span>
                                    @else
                                        <span class="badge badge-jenis-jasa rounded-2 px-2"><i class="fas fa-tools me-1"></i> Jasa</span>
                                    @endif
                                </td>
                                <td class="text-wrap" style="min-width: 200px;">
                                    <div class="d-flex flex-wrap gap-1">
                                        @if(!empty($tipe->ram_storage))
                                            @foreach($tipe->ram_storage as $var)
                                                <span class="badge badge-ram rounded-2 px-2 py-1">{{ $var }}</span>
                                            @endforeach
                                        @else <span class="text-muted small">-</span> @endif
                                    </div>
                                </td>
                                <td class="px-3 px-md-4 text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <button wire:click="edit({{ $tipe->id }})" class="btn btn-sm btn-light border rounded-circle text-primary" data-bs-toggle="modal" data-bs-target="#tipeModal"><i class="fas fa-pencil-alt"></i></button>
                                        <button wire:confirm="Hapus data ini?" wire:click="delete({{ $tipe->id }})" class="btn btn-sm btn-light border rounded-circle text-danger hover-danger"><i class="fas fa-trash-alt"></i></button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center py-5 text-muted">Belum ada data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-3 p-md-4 border-top">{{ $tipes->links() }}</div>
        </div>
    </div>

    @teleport('body')
    <div wire:ignore.self class="modal fade" id="tipeModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header border-bottom bg-light px-4 py-3">
                    <h5 class="modal-title fw-bold">{{ $isEdit ? 'Edit Data' : 'Tambah Data Baru' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" wire:click="resetInputFields"></button>
                </div>
                <div class="modal-body p-4">
                    <form wire:submit.prevent="store">
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase text-secondary mb-2">Jenis Barang <span class="text-danger">*</span></label>
                            <div class="row g-2">
                                <div class="col-4">
                                    <input type="radio" class="btn-check" name="jenis" id="opt_imei" value="imei" wire:model.live="jenis">
                                    <label class="btn btn-outline-custom w-100 py-2 rounded-3 text-center" for="opt_imei">
                                        <i class="fas fa-mobile-alt d-block mb-1 fs-5"></i>
                                        <span class="small fw-bold">HP/Unit</span>
                                    </label>
                                </div>
                                <div class="col-4">
                                    <input type="radio" class="btn-check" name="jenis" id="opt_non" value="non_imei" wire:model.live="jenis">
                                    <label class="btn btn-outline-custom w-100 py-2 rounded-3 text-center" for="opt_non">
                                        <i class="fas fa-box-open d-block mb-1 fs-5"></i>
                                        <span class="small fw-bold">Aksesoris</span>
                                    </label>
                                </div>
                                <div class="col-4">
                                    <input type="radio" class="btn-check" name="jenis" id="opt_jasa" value="jasa" wire:model.live="jenis">
                                    <label class="btn btn-outline-custom w-100 py-2 rounded-3 text-center" for="opt_jasa">
                                        <i class="fas fa-tools d-block mb-1 fs-5"></i>
                                        <span class="small fw-bold">Jasa</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase text-secondary">Merk / Kategori <span class="text-danger">*</span></label>
                            <select class="form-select rounded-3 py-2 @error('merk_id') is-invalid @enderror" wire:model="merk_id">
                                <option value="">-- Pilih --</option>
                                @foreach($merks as $m)
                                    <option value="{{ $m->id }}">{{ $m->nama }}</option>
                                @endforeach
                            </select>
                            @error('merk_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase text-secondary">Nama Item / Tipe <span class="text-danger">*</span></label>
                            <input type="text" class="form-control rounded-3 py-2 @error('nama') is-invalid @enderror" 
                                   wire:model="nama" placeholder="Contoh: iPhone 13 Pro atau Kabel Data C">
                            @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        @if($jenis == 'imei')
                            {{-- TAMPILAN KHUSUS HP (TOM SELECT) --}}
                            <div class="mb-4" wire:ignore 
                                 x-data="{
                                    tomSelectInstance: null,
                                    options: @js($ramOptions),
                                    initSelect() {
                                        if(this.tomSelectInstance) this.tomSelectInstance.destroy();
                                        this.tomSelectInstance = new TomSelect(this.$refs.selectInput, {
                                            plugins: ['remove_button', 'dropdown_input'], create: true, maxItems: null,
                                            valueField: 'value', labelField: 'value', searchField: 'value',
                                            options: this.options.map(o => ({value: o})),
                                            items: @entangle('ram_storage').live,
                                            onChange: (value) => { @this.set('ram_storage', value); }
                                        });
                                    }
                                 }"
                                 x-init="initSelect()"
                                 @set-select-values.window="
                                     if(tomSelectInstance) {
                                         tomSelectInstance.clear(true);
                                         $event.detail.values.forEach(v => {
                                             tomSelectInstance.addOption({value: v, text: v});
                                             tomSelectInstance.addItem(v, true);
                                         });
                                     }
                                 "
                                 @reset-select.window="if(tomSelectInstance) tomSelectInstance.clear()"
                            >
                                <label class="form-label fw-bold small text-uppercase text-secondary">Varian RAM/ROM <span class="text-danger">*</span></label>
                                <select x-ref="selectInput" multiple placeholder="Pilih RAM..." autocomplete="off"></select>
                                @error('ram_storage') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                        @else
                            {{-- TAMPILAN KHUSUS AKSESORIS / JASA (INPUT TEXT BIASA) --}}
                            <div class="mb-4">
                                <label class="form-label fw-bold small text-uppercase text-secondary">
                                    {{ $jenis == 'non_imei' ? 'Varian (Warna/Tipe)' : 'Keterangan Jasa' }} <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control rounded-3 py-2 @error('variasi_manual') is-invalid @enderror" 
                                       wire:model="variasi_manual" 
                                       placeholder="{{ $jenis == 'non_imei' ? 'Contoh: Merah, Putih, Hitam (Pisahkan koma)' : 'Contoh: LCD Original, LCD KW' }}">
                                <div class="form-text small text-muted">
                                    @if($jenis == 'non_imei')
                                        Jika lebih dari satu warna/tipe, pisahkan dengan koma (,).
                                    @else
                                        Masukkan detail jenis jasa yang tersedia.
                                    @endif
                                </div>
                                @error('variasi_manual') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        @endif

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-dark rounded-3 py-2 fw-bold">
                                <span wire:loading.remove wire:target="store">Simpan Data</span>
                                <span wire:loading wire:target="store">Menyimpan...</span>
                            </button>
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
        const modalEl = document.getElementById('tipeModal');
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
    });
    Livewire.on('swal', (data) => {
        const eventData = data[0];
        Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true })
            .fire({ icon: eventData.icon, title: eventData.title, text: eventData.text });
    });
</script>
@endscript