<div class="container-fluid">
    
    {{-- 1. DEPENDENCIES (Wajib ada) --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

    <style>
        .modal-backdrop { z-index: 1055 !important; }
        .modal { z-index: 1060 !important; }
        
        /* PENTING: Agar dropdown muncul di atas modal */
        .ts-dropdown, .ts-control { z-index: 1070 !important; }
        
        /* Style Badge RAM di Tabel */
        .badge-ram {
            background-color: #eef2ff;
            color: #4f46e5;
            border: 1px solid #c7d2fe;
            font-weight: 500;
            font-size: 0.75rem; /* Font agak kecil di mobile */
        }

        /* Responsive Search Bar */
        .search-container {
            width: 100%;
        }
        @media (min-width: 768px) {
            .search-container { width: 300px; }
        }
    </style>

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold text-black mb-1">Manajemen Tipe (HP/Device)</h4>
            <p class="text-secondary small mb-0">Kelola model dan varian RAM/Storage produk.</p>
        </div>
        
        <button wire:click="resetInputFields" data-bs-toggle="modal" data-bs-target="#tipeModal" 
                class="btn btn-dark rounded-3 px-4 py-2 shadow-sm d-flex align-items-center justify-content-center w-100 w-md-auto gap-2">
            <i class="fas fa-plus-circle"></i> 
            <span>Tambah Tipe</span>
        </button>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            
            <div class="p-3 p-md-4 border-bottom bg-white d-flex justify-content-end">
                <div class="input-group rounded-3 search-container" style="border: 1px solid #dee2e6;">
                    <span class="input-group-text bg-transparent border-0 text-muted ps-3"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control border-0 bg-transparent" placeholder="Cari Tipe/Merk..." wire:model.live.debounce.300ms="search">
                </div>
            </div>

            <div class="table-responsive" wire:poll.5s>
                <table class="table table-hover align-middle mb-0 text-nowrap">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-3 px-3 px-md-4 text-secondary small text-uppercase fw-bold d-none d-md-table-cell">No</th>
                            
                            <th class="py-3 px-3 text-secondary small text-uppercase fw-bold">Tipe / Model</th>
                            <th class="py-3 text-secondary small text-uppercase fw-bold">Merek</th>
                            <th class="py-3 text-secondary small text-uppercase fw-bold" width="30%">Varian (RAM/ROM)</th>
                            
                            <th class="py-3 text-secondary small text-uppercase fw-bold d-none d-lg-table-cell">Diperbarui</th>
                            
                            <th class="py-3 px-3 px-md-4 text-secondary small text-uppercase fw-bold text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tipes as $index => $tipe)
                            <tr>
                                <td class="px-3 px-md-4 fw-bold text-muted d-none d-md-table-cell">{{ $tipes->firstItem() + $index }}</td>
                                
                                <td class="px-3">
                                    <span class="fw-bold text-dark">{{ $tipe->nama }}</span>
                                </td>
                                
                                <td>
                                    <span class="badge bg-dark text-white rounded-pill px-3 fw-normal">
                                        {{ $tipe->merk->nama ?? 'Merk Dihapus' }}
                                    </span>
                                </td>
                                
                                <td class="text-wrap" style="min-width: 200px;"> <div class="d-flex flex-wrap gap-1">
                                        @if(!empty($tipe->ram_storage))
                                            @foreach($tipe->ram_storage as $ram)
                                                <span class="badge badge-ram rounded-2 px-2 py-1">{{ $ram }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </div>
                                </td>
                                
                                <td class="small text-secondary d-none d-lg-table-cell">
                                    {{ $tipe->updated_at->format('d/m/Y H:i') }}
                                </td>
                                
                                <td class="px-3 px-md-4 text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <button wire:click="edit({{ $tipe->id }})" 
                                                class="btn btn-sm btn-light border rounded-circle text-primary" 
                                                data-bs-toggle="modal" data-bs-target="#tipeModal">
                                            <i class="fas fa-pencil-alt"></i>
                                        </button>
                                        <button wire:confirm="Hapus Tipe {{ $tipe->nama }}?" 
                                                wire:click="delete({{ $tipe->id }})" 
                                                class="btn btn-sm btn-light border rounded-circle text-danger hover-danger">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center py-5 text-muted">Belum ada data Tipe</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-3 p-md-4 border-top">
                {{ $tipes->links() }}
            </div>
        </div>
    </div>

    @teleport('body')
    <div wire:ignore.self class="modal fade" id="tipeModal" tabindex="-1" aria-labelledby="tipeModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered"> <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header border-bottom bg-light px-4 py-3">
                    <h5 class="modal-title fw-bold" id="tipeModalLabel">
                        {{ $isEdit ? 'Edit Tipe' : 'Tambah Tipe Baru' }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" wire:click="resetInputFields"></button>
                </div>
                
                <div class="modal-body p-4">
                    <form wire:submit.prevent="store">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase text-secondary">Pilih Merek <span class="text-danger">*</span></label>
                            <select class="form-select rounded-3 py-2 @error('merk_id') is-invalid @enderror" wire:model="merk_id">
                                <option value="">-- Pilih Merek --</option>
                                @foreach($merks as $m)
                                    <option value="{{ $m->id }}">{{ $m->nama }}</option>
                                @endforeach
                            </select>
                            @error('merk_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase text-secondary">Nama Tipe / Model <span class="text-danger">*</span></label>
                            <input type="text" class="form-control rounded-3 py-2 @error('nama') is-invalid @enderror" 
                                   wire:model="nama" placeholder="Contoh: Galaxy S24 Ultra">
                            @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4"
                             wire:ignore
                             x-data="{
                                tomSelectInstance: null,
                                options: [
                                    @foreach($ramOptions as $opt) '{{ $opt }}', @endforeach
                                ],
                                initSelect() {
                                    if(this.tomSelectInstance) this.tomSelectInstance.destroy();
                                    this.tomSelectInstance = new TomSelect(this.$refs.selectInput, {
                                        plugins: ['remove_button', 'dropdown_input'],
                                        create: true,
                                        maxItems: null,
                                        valueField: 'value',
                                        labelField: 'text',
                                        searchField: 'text',
                                        options: this.options.map(o => ({value: o, text: o})),
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
                            <label class="form-label fw-bold small text-uppercase text-secondary">Varian RAM & Penyimpanan <span class="text-danger">*</span></label>
                            <select x-ref="selectInput" multiple placeholder="Pilih atau ketik varian..." autocomplete="off"></select>
                            <div class="form-text small text-muted mt-1">Ketik lalu tekan enter untuk menambah opsi baru.</div>
                        </div>
                        
                        @error('ram_storage') 
                            <div class="text-danger small mt-n3 mb-3 d-block">{{ $message }}</div> 
                        @enderror

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

{{-- SCRIPT HANDLER --}}
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
        Swal.mixin({
            toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true
        }).fire({
            icon: eventData.icon, title: eventData.title, text: eventData.text
        });
    });
</script>
@endscript