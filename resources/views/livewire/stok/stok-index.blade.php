<div class="container-fluid">
    
    {{-- Dependencies --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .modal-backdrop { z-index: 1055 !important; }
        .modal { z-index: 1060 !important; }
        .form-check-input:checked { background-color: #212529; border-color: #212529; }
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
            <button wire:click="openKeluarStokModal" 
                    class="btn btn-dark rounded-3 px-4 py-2 shadow-sm position-relative">
                <i class="fas fa-sign-out-alt me-2"></i> Keluar / Pindah Stok
                @if(count($selectedStok) > 0)
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light">
                        {{ count($selectedStok) }}
                    </span>
                @endif
            </button>
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
                            <th class="py-3 text-secondary small fw-bold">Produk</th>
                            <th class="py-3 text-secondary small fw-bold">Lokasi Cabang</th> 
                            <th class="py-3 text-secondary small fw-bold">IMEI</th>
                            <th class="py-3 text-secondary small fw-bold text-center">Stok</th>
                            <th class="py-3 text-secondary small fw-bold text-end">Harga Jual</th>
                            <th class="py-3 px-4 text-secondary small fw-bold text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stoks as $stok)
                            <tr class="{{ in_array($stok->id, $selectedStok) ? 'table-active' : '' }}">
                                <td class="px-4 text-center">
                                    <input type="checkbox" class="form-check-input" wire:model.live="selectedStok" value="{{ $stok->id }}">
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold text-dark">{{ $stok->merk->nama }}</span>
                                        <span class="small text-muted">{{ $stok->tipe->nama }}</span>
                                        <div class="mt-1">
                                            <span class="badge bg-secondary rounded-2" style="font-size: 0.65rem;">{{ $stok->ram_storage }}</span>
                                            @if($stok->kondisi == 'Baru')
                                                <span class="badge bg-success bg-opacity-10 text-success border border-success rounded-2" style="font-size: 0.65rem;">NEW</span>
                                            @else
                                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning rounded-2" style="font-size: 0.65rem;">2ND</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($stok->cabang)
                                        <span class="badge bg-info text-dark border border-info-subtle">
                                            <i class="fas fa-map-marker-alt me-1"></i> {{ $stok->cabang->nama_cabang }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary text-white border">
                                            <i class="fas fa-warehouse me-1"></i> PUSAT / GUDANG
                                        </span>
                                    @endif
                                </td>
                                <td class="font-monospace text-primary">{{ $stok->imei }}</td>
                                <td class="text-center">
                                    @if($stok->jumlah == 0)
                                        <span class="badge bg-danger rounded-pill px-3">HABIS</span>
                                    @else
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success rounded-pill px-3">
                                            {{ $stok->jumlah }} Unit
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end fw-bold">Rp {{ number_format($stok->harga_jual, 0, ',', '.') }}</td>
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

    {{-- MODAL 1: TAMBAH/EDIT STOK --}}
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
                        {{-- FORM INPUT STOK (Tetap Sama) --}}
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label small fw-bold text-secondary">Merk <span class="text-danger">*</span></label>
                                <select class="form-select rounded-3 py-2" wire:model.live="merk_id">
                                    <option value="">-- Pilih --</option>
                                    @foreach($merks as $m) <option value="{{ $m->id }}">{{ $m->nama }}</option> @endforeach
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold text-secondary">Tipe <span class="text-danger">*</span></label>
                                <select class="form-select rounded-3 py-2" wire:model.live="tipe_id">
                                    <option value="">-- Pilih --</option>
                                    @foreach($tipeOptions as $t) <option value="{{ $t->id }}">{{ $t->nama }}</option> @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-4">
                                <label class="form-label small fw-bold text-secondary">RAM</label>
                                <select class="form-select rounded-3 py-2" wire:model="ram_storage">
                                    <option value="">--</option>
                                    @foreach($ramOptions as $ram) <option value="{{ $ram }}">{{ $ram }}</option> @endforeach
                                </select>
                            </div>
                            <div class="col-4">
                                <label class="form-label small fw-bold text-secondary">Kondisi</label>
                                <select class="form-select rounded-3 py-2" wire:model="kondisi">
                                    <option value="Baru">Baru</option>
                                    <option value="Second">Second</option>
                                </select>
                            </div>
                            <div class="col-4">
                                <label class="form-label small fw-bold text-secondary">Jumlah <span class="text-danger">*</span></label>
                                <input type="number" class="form-control rounded-3 py-2" wire:model="jumlah" min="0">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">IMEI <span class="text-danger">*</span></label>
                            <input type="text" class="form-control rounded-3 py-2" wire:model="imei" placeholder="Scan IMEI...">
                            @error('imei') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Harga Modal</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">Rp</span>
                                <input type="text" class="form-control border-start-0 rounded-end-3 py-2" 
                                    wire:model="harga_modal" 
                                    onkeyup="formatRupiah(this)" 
                                    placeholder="0">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-secondary">Harga Jual <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">Rp</span>
                                <input type="text" class="form-control border-start-0 rounded-end-3 py-2 fw-bold text-success" 
                                    wire:model="harga_jual" 
                                    onkeyup="formatRupiah(this)" 
                                    placeholder="0">
                            </div>
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

    {{-- MODAL 2: STOK KELUAR / PINDAH CABANG --}}
    @teleport('body')
    <div wire:ignore.self class="modal fade" id="keluarStokModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header border-0 px-4 pt-4 pb-2">
                    <div>
                        <h5 class="modal-title fw-bold">Proses Stok (Keluar / Pindah)</h5>
                        <p class="text-secondary small mb-0">Pilih kategori untuk memproses stok terpilih.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 pb-4">
                    <form wire:submit.prevent="storeKeluarStok">
                        
                        <div class="mb-4">
                            <label class="form-label-custom">Kategori <span class="required-star">*</span></label>
                            <select class="form-select rounded-3 py-2 fw-bold {{ $errors->has('kategoriKeluar') ? 'is-invalid' : '' }}" 
                                    wire:model.live="kategoriKeluar">
                                <option value="">Pilih Kategori...</option>
                                <option value="Pindah Cabang" class="fw-bold text-primary">Pindah Cabang</option>
                                <option disabled>----------------</option>
                                @foreach($opsiKategori as $key => $label)
                                    @if($key != 'Pindah Cabang')
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endif
                                @endforeach
                            </select>
                            @error('kategoriKeluar') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        @if($kategoriKeluar)
                            <div class="bg-light p-3 rounded-3 mb-4 border animate__animated animate__fadeIn">
                                
                                {{-- FORM KHUSUS PINDAH CABANG SESUAI GAMBAR --}}
                                @if($kategoriKeluar == 'Pindah Cabang')
                                    <div class="mb-3">
                                        <label class="form-label-custom">Cabang <span class="required-star">*</span></label>
                                        <select class="form-select rounded-3 py-2" wire:model="target_cabang_id">
                                            <option value="">Pilih Cabang</option>
                                            @foreach($cabangs as $c) <option value="{{ $c->id }}">{{ $c->nama_cabang }}</option> @endforeach
                                        </select>
                                        @error('target_cabang_id') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label-custom">Nama <span class="required-star">*</span></label>
                                        <input type="text" class="form-control rounded-3 py-2" wire:model="nama_penerima" placeholder="Masukkan nama penerima">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label-custom">Nomor Handphone <span class="required-star">*</span></label>
                                        <input type="text" class="form-control rounded-3 py-2" wire:model="nomor_handphone" placeholder="Masukkan nomor handphone penerima">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label-custom">Alamat <span class="required-star">*</span></label>
                                        <textarea class="form-control rounded-3 py-2" rows="2" wire:model="alamat" placeholder="Masukkan alamat penerima"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label-custom">Catatan <span class="required-star">*</span></label>
                                        <textarea class="form-control rounded-3 py-2" rows="2" wire:model="catatan" placeholder="Masukkan catatan"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label-custom fw-bold text-primary">Jumlah yang Dibawa <span class="required-star">*</span></label>
                                        <input type="number" class="form-control rounded-3 py-2 border-primary" wire:model="jumlah_pindah" min="1">
                                        <small class="text-muted">Masukkan jumlah unit yang akan dipindahkan per item.</small>
                                    </div>
                                @endif

                                {{-- FORM LAINNYA --}}
                                @if(in_array($kategoriKeluar, ['Admin WhatsApp', 'Shopee', 'Tokopedia', 'Giveaway']))
                                    <div class="mb-3">
                                        <label class="form-label-custom">Nama Penerima <span class="required-star">*</span></label>
                                        <input type="text" class="form-control rounded-3 py-2" wire:model="nama_penerima">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label-custom">No HP <span class="required-star">*</span></label>
                                        <input type="text" class="form-control rounded-3 py-2" wire:model="nomor_handphone">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label-custom">Alamat <span class="required-star">*</span></label>
                                        <textarea class="form-control rounded-3 py-2" rows="2" wire:model="alamat"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label-custom">Catatan <span class="required-star">*</span></label>
                                        <textarea class="form-control rounded-3 py-2" rows="2" wire:model="catatan"></textarea>
                                    </div>
                                @endif

                                @if($kategoriKeluar == 'Retur')
                                    <div class="row g-2">
                                        <div class="col-md-6 mb-2"><label class="form-label-custom">Petugas *</label><input type="text" class="form-control rounded-3" wire:model="nama_petugas"></div>
                                        <div class="col-md-6 mb-2"><label class="form-label-custom">Segel *</label><input type="text" class="form-control rounded-3" wire:model="segel"></div>
                                        <div class="col-12 mb-2"><label class="form-label-custom">Kendala *</label><input type="text" class="form-control rounded-3" wire:model="kendala_retur"></div>
                                        <div class="col-md-6 mb-2"><label class="form-label-custom">Nama Customer *</label><input type="text" class="form-control rounded-3" wire:model="nama_customer"></div>
                                    </div>
                                @endif

                                @if($kategoriKeluar == 'Kesalahan Input')
                                    <div class="mb-3">
                                        <label class="form-label-custom">Alasan Hapus <span class="required-star">*</span></label>
                                        <textarea class="form-control rounded-3 py-2" rows="2" wire:model="catatan"></textarea>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <div class="mb-4">
                            <label class="form-label-custom">List Barang Terpilih ({{ count($selectedItems) }} Item)</label>
                            <div class="border rounded-3 overflow-hidden bg-white">
                                <div class="table-responsive" style="max-height: 150px;">
                                    <table class="table table-sm table-striped mb-0 small">
                                        <thead class="bg-light sticky-top">
                                            <tr><th class="ps-3">Merk</th><th>Tipe</th><th>IMEI</th><th>Lokasi</th></tr>
                                        </thead>
                                        <tbody>
                                            @forelse($selectedItems as $item)
                                                <tr>
                                                    <td class="ps-3 fw-bold">{{ $item->merk->nama }}</td>
                                                    <td>{{ $item->tipe->nama }}</td>
                                                    <td class="font-monospace">{{ $item->imei }}</td>
                                                    <td>{{ $item->cabang->nama_cabang ?? 'Pusat' }}</td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="4" class="text-center text-muted py-3">Tidak ada item dipilih</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2 pt-2 border-top">
                            <button type="button" class="btn btn-outline-dark rounded-pill w-100 py-2" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-black rounded-pill w-100 py-2 fw-bold text-white" style="background-color: #000;">
                                @if($kategoriKeluar == 'Pindah Cabang')
                                    Simpan & Pindahkan
                                @else
                                    Simpan & Keluarkan
                                @endif
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endteleport

</div>

<script>
    function formatRupiah(element) {
        let value = element.value.replace(/[^,\d]/g, '').toString();
        let split = value.split(',');
        let sisa = split[0].length % 3;
        let rupiah = split[0].substr(0, sisa);
        let ribuan = split[0].substr(sisa).match(/\d{3}/gi);
        if (ribuan) {
            let separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }
        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        element.value = rupiah;
        element.dispatchEvent(new Event('input'));
    }

    document.addEventListener('livewire:initialized', () => {
        Livewire.on('close-modal', () => { 
            const modal = bootstrap.Modal.getInstance(document.getElementById('stokModal'));
            if(modal) modal.hide();
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        });
        Livewire.on('open-keluar-modal', () => { 
            new bootstrap.Modal(document.getElementById('keluarStokModal')).show(); 
        });
        Livewire.on('close-keluar-modal', () => { 
            const modal = bootstrap.Modal.getInstance(document.getElementById('keluarStokModal'));
            if(modal) modal.hide();
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        });
        Livewire.on('swal', (data) => { 
            Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true })
                .fire({ icon: data[0].icon, title: data[0].title, text: data[0].text });
        });
    });
</script>