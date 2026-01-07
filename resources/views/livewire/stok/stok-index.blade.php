<div class="container-fluid">
    
    {{-- Dependencies --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .modal-backdrop { z-index: 1055 !important; }
        .modal { z-index: 1060 !important; }
        .form-check-input:checked { background-color: #212529; border-color: #212529; }
        
        /* Floating Labels Style Custom untuk Modal Stok Keluar */
        .form-label-custom { font-size: 0.85rem; font-weight: 500; color: #6c757d; margin-bottom: 0.25rem; }
        .required-star { color: #dc3545; margin-left: 2px; }
    </style>

    {{-- Header Section --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold text-black mb-1">Stok Unit / Inventory</h4>
            <p class="text-secondary small mb-0">Total Stok Aktif: <span class="fw-bold text-dark">{{ $stoks->total() }}</span> Unit</p>
        </div>
        <div class="d-flex gap-2">
            {{-- Tombol Keluar Stok --}}
            <button wire:click="openKeluarStokModal" 
                    class="btn btn-dark rounded-3 px-4 py-2 shadow-sm position-relative">
                <i class="fas fa-sign-out-alt me-2"></i> Keluar Stok
                @if(count($selectedStok) > 0)
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light">
                        {{ count($selectedStok) }}
                    </span>
                @endif
            </button>
            {{-- Tombol Tambah --}}
            <button wire:click="resetInputFields" data-bs-toggle="modal" data-bs-target="#stokModal" 
                    class="btn btn-outline-dark rounded-3 px-4 py-2 shadow-sm">
                <i class="fas fa-plus-circle me-2"></i> Tambah Stok
            </button>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="p-4 border-bottom bg-white d-flex justify-content-between align-items-center">
                <div class="small text-muted">
                    @if(count($selectedStok) > 0)
                        <span class="text-primary fw-bold">{{ count($selectedStok) }}</span> item dipilih.
                    @else
                        Silakan pilih item untuk bulk action.
                    @endif
                </div>
                <input type="text" class="form-control w-25 rounded-3" placeholder="Cari IMEI / Merk..." wire:model.live.debounce.300ms="search">
            </div>

            <div class="table-responsive" wire:poll.10s>
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
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
                                <td class="text-end fw-bold">Rp {{ number_format($stok->harga_jual, 0, ',', '.') }}</td>
                                <td class="px-4 text-end">
                                    <button wire:click="edit({{ $stok->id }})" data-bs-toggle="modal" data-bs-target="#stokModal" class="btn btn-sm btn-light border rounded-circle text-primary"><i class="fas fa-pencil-alt"></i></button>
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

    {{-- MODAL 1: TAMBAH/EDIT STOK (FORM LAMA) --}}
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
                                @foreach($merks as $m) <option value="{{ $m->id }}">{{ $m->nama }}</option> @endforeach
                            </select>
                            @error('merk_id') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Tipe <span class="text-danger">*</span></label>
                            <select class="form-select rounded-3 py-2" wire:model.live="tipe_id" {{ empty($merk_id) ? 'disabled' : '' }}>
                                <option value="">-- Pilih Tipe --</option>
                                @foreach($tipeOptions as $t) <option value="{{ $t->id }}">{{ $t->nama }}</option> @endforeach
                            </select>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label small fw-bold text-secondary">RAM <span class="text-danger">*</span></label>
                                <select class="form-select rounded-3 py-2" wire:model="ram_storage">
                                    <option value="">-- Pilih --</option>
                                    @foreach($ramOptions as $ram) <option value="{{ $ram }}">{{ $ram }}</option> @endforeach
                                </select>
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
                            <label class="form-label small fw-bold text-secondary">IMEI <span class="text-danger">*</span></label>
                            <input type="text" class="form-control rounded-3 py-2" wire:model="imei" placeholder="Scan IMEI...">
                            @error('imei') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Modal</label>
                            <input type="number" class="form-control rounded-3 py-2" wire:model.live.debounce.500ms="harga_modal" placeholder="0">
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-secondary">Jual <span class="text-danger">*</span></label>
                            <input type="number" class="form-control rounded-3 py-2 fw-bold text-success" wire:model="harga_jual">
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-dark rounded-3 py-2 fw-bold">Simpan Stok</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endteleport

    {{-- MODAL 2: STOK KELUAR (FORM DINAMIS BARU) --}}
    @teleport('body')
    <div wire:ignore.self class="modal fade" id="keluarStokModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header border-0 px-4 pt-4 pb-2">
                    <div>
                        <h5 class="modal-title fw-bold">Stok Keluar</h5>
                        <p class="text-secondary small mb-0">Isi detail form untuk mengeluarkan stok.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 pb-4">
                    <form wire:submit.prevent="storeKeluarStok">
                        
                        {{-- Kategori Dropdown --}}
                        <div class="mb-4">
                            <label class="form-label-custom">Kategori <span class="required-star">*</span></label>
                            <select class="form-select rounded-3 py-2 fw-bold {{ $errors->has('kategoriKeluar') ? 'is-invalid' : '' }}" 
                                    wire:model.live="kategoriKeluar">
                                <option value="">Pilih Kategori...</option>
                                @foreach($opsiKategori as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('kategoriKeluar') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        {{-- AREA DINAMIS FORM FIELDS --}}
                        @if($kategoriKeluar)
                            <div class="bg-light p-3 rounded-3 mb-4 border animate__animated animate__fadeIn">
                                
                                {{-- 1. FORM UTAMA (Sales, Giveaway, Pindah Cabang) --}}
                                @if(in_array($kategoriKeluar, ['Admin WhatsApp', 'Shopee', 'Tokopedia', 'Giveaway', 'Pindah Cabang']))
                                    
                                    @if($kategoriKeluar == 'Pindah Cabang')
                                        <div class="mb-3">
                                            <label class="form-label-custom">Ke Cabang Mana? <span class="required-star">*</span></label>
                                            <select class="form-select rounded-3 py-2" wire:model="target_cabang_id">
                                                <option value="">-- Pilih Cabang Tujuan --</option>
                                                @foreach($cabangs as $c) <option value="{{ $c->id }}">{{ $c->nama }}</option> @endforeach
                                            </select>
                                            @error('target_cabang_id') <span class="text-danger small">{{ $message }}</span> @enderror
                                        </div>
                                    @endif

                                    <div class="mb-3">
                                        <label class="form-label-custom">Nama Penerima / PIC <span class="required-star">*</span></label>
                                        <input type="text" class="form-control rounded-3 py-2" wire:model="nama_penerima" placeholder="Masukkan nama penerima">
                                        @error('nama_penerima') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label-custom">Nomor Handphone <span class="required-star">*</span></label>
                                        <input type="text" class="form-control rounded-3 py-2" wire:model="nomor_handphone" placeholder="Contoh: 0812...">
                                        @error('nomor_handphone') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label-custom">Alamat Lengkap <span class="required-star">*</span></label>
                                        <textarea class="form-control rounded-3 py-2" rows="2" wire:model="alamat" placeholder="Masukkan alamat lengkap..."></textarea>
                                        @error('alamat') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                @endif

                                {{-- 2. FORM KHUSUS RETUR --}}
                                @if($kategoriKeluar == 'Retur')
                                    <div class="row g-2">
                                        <div class="col-md-6 mb-2">
                                            <label class="form-label-custom">Petugas Pemeriksa <span class="required-star">*</span></label>
                                            <input type="text" class="form-control rounded-3" wire:model="nama_petugas">
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label class="form-label-custom">Kondisi Segel <span class="required-star">*</span></label>
                                            <input type="text" class="form-control rounded-3" wire:model="segel" placeholder="Utuh / Rusak">
                                        </div>
                                        <div class="col-12 mb-2">
                                            <label class="form-label-custom">Kendala Retur <span class="required-star">*</span></label>
                                            <input type="text" class="form-control rounded-3" wire:model="kendala_retur">
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label class="form-label-custom">Tgl Pembelian <span class="required-star">*</span></label>
                                            <input type="date" class="form-control rounded-3" wire:model="tanggal_pembelian">
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label class="form-label-custom">Nama Customer <span class="required-star">*</span></label>
                                            <input type="text" class="form-control rounded-3" wire:model="nama_customer">
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label class="form-label-custom">No HP Customer <span class="required-star">*</span></label>
                                            <input type="text" class="form-control rounded-3" wire:model="nomor_handphone">
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label class="form-label-custom">Instagram (Opsional)</label>
                                            <input type="text" class="form-control rounded-3" wire:model="instagram_customer">
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label class="form-label-custom">Email / iCloud <span class="required-star">*</span></label>
                                            <input type="text" class="form-control rounded-3" wire:model="email_icloud">
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label class="form-label-custom">Password Email <span class="required-star">*</span></label>
                                            <input type="text" class="form-control rounded-3" wire:model="password_email">
                                        </div>
                                        <div class="col-12 mb-2">
                                            <label class="form-label-custom">Pola / PIN Layar <span class="required-star">*</span></label>
                                            <input type="text" class="form-control rounded-3" wire:model="pola_pin">
                                        </div>
                                    </div>
                                    {{-- Validasi Error ditampilkan grouping agar rapi --}}
                                    @if($errors->any())
                                        <div class="alert alert-danger small mt-2 py-1 px-2">Lengkapi semua field bertanda bintang (*)</div>
                                    @endif
                                @endif

                                {{-- 3. CATATAN (Wajib untuk Semua Kecuali Retur yg sudah detail) --}}
                                @if($kategoriKeluar != 'Retur')
                                    <div class="mb-3">
                                        <label class="form-label-custom">Catatan / Keterangan <span class="required-star">*</span></label>
                                        <textarea class="form-control rounded-3 py-2" rows="2" wire:model="catatan" placeholder="Masukkan catatan..."></textarea>
                                        @error('catatan') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                @endif

                            </div>
                        @endif

                        {{-- List Barang (Preview) --}}
                        <div class="mb-4">
                            <label class="form-label-custom">List Barang ({{ count($selectedItems) }} Unit)</label>
                            <div class="border rounded-3 overflow-hidden bg-white">
                                <div class="table-responsive" style="max-height: 150px;">
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
                                                <tr><td colspan="3" class="text-center text-muted py-3">Tidak ada item dipilih</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2 pt-2 border-top">
                            <button type="button" class="btn btn-outline-dark rounded-pill w-100 py-2" data-bs-dismiss="modal">Reset / Batal</button>
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
    Livewire.on('close-modal', () => {
        const modalEl = document.getElementById('stokModal');
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
    });
    Livewire.on('open-keluar-modal', () => {
        const modalEl = document.getElementById('keluarStokModal');
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    });
    Livewire.on('close-keluar-modal', () => {
        const modalEl = document.getElementById('keluarStokModal');
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