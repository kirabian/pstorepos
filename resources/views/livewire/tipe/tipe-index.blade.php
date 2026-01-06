<div class="container-fluid">
    
    {{-- Dependencies: SweetAlert2 & TomSelect (Untuk Multi Select keren) --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

    <style>
        .modal-backdrop { z-index: 1055 !important; }
        .modal { z-index: 1060 !important; }
        
        /* Badge RAM Styling */
        .badge-ram {
            font-size: 0.75rem;
            font-weight: 500;
            background-color: #eef2ff; /* Warna biru muda lembut */
            color: #4f46e5;
            border: 1px solid #c7d2fe;
        }

        /* TomSelect Custom Fixes */
        .ts-control { border-radius: 0.5rem !important; padding: 0.6rem !important; }
        .ts-dropdown { z-index: 1070 !important; } /* Agar muncul di atas modal */
    </style>

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold text-black mb-1">Manajemen Tipe (HP/Device)</h4>
            <p class="text-secondary small mb-0">Kelola model dan varian RAM/Storage produk.</p>
        </div>
        
        <button wire:click="resetInputFields" data-bs-toggle="modal" data-bs-target="#tipeModal" 
                class="btn btn-dark rounded-3 px-4 py-2 shadow-sm d-flex align-items-center gap-2">
            <i class="fas fa-plus-circle"></i> 
            <span>Tambah Tipe</span>
        </button>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            
            <div class="p-4 border-bottom bg-white d-flex justify-content-end">
                <div class="input-group rounded-3" style="max-width: 300px; border: 1px solid #dee2e6;">
                    <span class="input-group-text bg-transparent border-0 text-muted ps-3"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control border-0 bg-transparent" placeholder="Cari Tipe/Merk..." wire:model.live.debounce.300ms="search">
                </div>
            </div>

            <div class="table-responsive" wire:poll.5s>
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-3 px-4 text-secondary small text-uppercase fw-bold">No</th>
                            <th class="py-3 text-secondary small text-uppercase fw-bold">Tipe / Model</th>
                            <th class="py-3 text-secondary small text-uppercase fw-bold">Merek</th>
                            <th class="py-3 text-secondary small text-uppercase fw-bold" width="30%">Varian (RAM/ROM)</th>
                            <th class="py-3 text-secondary small text-uppercase fw-bold text-nowrap">Diperbarui</th>
                            <th class="py-3 px-4 text-secondary small text-uppercase fw-bold text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tipes as $index => $tipe)
                            <tr>
                                <td class="px-4 fw-bold text-muted">{{ $tipes->firstItem() + $index }}</td>
                                
                                <td>
                                    <span class="fw-bold text-dark">{{ $tipe->nama }}</span>
                                </td>

                                <td>
                                    <span class="badge bg-dark text-white rounded-pill px-3 fw-normal">
                                        {{ $tipe->merk->nama ?? 'Merk Dihapus' }}
                                    </span>
                                </td>

                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        @if(!empty($tipe->ram_storage))
                                            @foreach($tipe->ram_storage as $ram)
                                                <span class="badge badge-ram rounded-2 px-2 py-1">
                                                    {{ $ram }} GB
                                                </span>
                                            @endforeach
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </div>
                                </td>

                                <td class="small text-secondary">
                                    {{ $tipe->updated_at->format('d/m/Y H:i') }}
                                </td>
                                
                                <td class="px-4 text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <button wire:click="edit({{ $tipe->id }})" 
                                                data-bs-toggle="modal" data-bs-target="#tipeModal"
                                                class="btn btn-sm btn-light border rounded-circle text-primary" title="Edit">
                                            <i class="fas fa-pencil-alt"></i>
                                        </button>
                                        
                                        <button wire:confirm="Hapus Tipe {{ $tipe->nama }}?" 
                                                wire:click="delete({{ $tipe->id }})" 
                                                class="btn btn-sm btn-light border rounded-circle text-danger hover-danger" title="Hapus">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 opacity-50">
                                    <i class="fas fa-mobile-alt fa-3x mb-3 text-secondary"></i>
                                    <h6 class="fw-bold text-secondary">Belum ada data Tipe</h6>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-top">
                {{ $tipes->links() }}
            </div>
        </div>
    </div>

    @teleport('body')
    <div wire:ignore.self class="modal fade" id="tipeModal" tabindex="-1" aria-labelledby="tipeModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg">
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

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase text-secondary">Varian RAM & Penyimpanan <span class="text-danger">*</span></label>
                            
                            <div wire:ignore>
                                <select id="ram-select" multiple placeholder="Pilih Varian (Bisa lebih dari satu)..." autocomplete="off">
                                    @foreach($ramOptions as $opt)
                                        <option value="{{ $opt }}">{{ $opt }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('ram_storage') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            <div class="form-text small">Klik kolom untuk memilih varian.</div>
                        </div>

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

{{-- SCRIPT: Menghubungkan TomSelect dengan Livewire --}}
@script
<script>
    let tomSelectInstance;

    // Inisialisasi TomSelect saat halaman dimuat
    const initTomSelect = () => {
        const el = document.getElementById('ram-select');
        if (el) {
            tomSelectInstance = new TomSelect(el, {
                plugins: ['remove_button'], // Ada tombol X kecil untuk hapus
                create: true, // User bisa ketik manual jika opsi tidak ada (misal: 20/1TB)
                onItemAdd: function() {
                    // Update property Livewire saat item dipilih
                    @this.set('ram_storage', this.getValue());
                },
                onItemRemove: function() {
                    // Update property Livewire saat item dihapus
                    @this.set('ram_storage', this.getValue());
                }
            });
        }
    };

    initTomSelect();

    // Event: Saat tombol Edit ditekan, isi TomSelect dengan data dari database
    Livewire.on('set-select-values', (data) => {
        if (tomSelectInstance) {
            tomSelectInstance.clear(true); // Bersihkan dulu (silent mode)
            // Tambahkan opsi jika belum ada (untuk kasus create:true)
            data.values.forEach(val => {
                tomSelectInstance.addOption({value: val, text: val});
                tomSelectInstance.addItem(val, true); // Select item (silent mode)
            });
        }
    });

    // Event: Saat Modal ditutup/Reset, kosongkan TomSelect
    Livewire.on('reset-select', () => {
        if (tomSelectInstance) {
            tomSelectInstance.clear();
        }
    });

    // Event: Tutup Modal Bootstrap
    Livewire.on('close-modal', () => {
        const modalEl = document.getElementById('tipeModal');
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
    });

    // Event: Notifikasi SweetAlert
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