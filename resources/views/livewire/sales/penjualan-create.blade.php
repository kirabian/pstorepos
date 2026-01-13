<div class="container-fluid">
    <div class="d-flex align-items-center mb-4">
        <div class="bg-dark text-white rounded-3 p-2 me-3">
            <i class="fas fa-cash-register fs-3"></i>
        </div>
        <div>
            <h4 class="fw-bold mb-0 text-dark">Input Penjualan</h4>
            <small class="text-muted">
                Cabang: <span class="fw-bold text-primary">{{ Auth::user()->cabang->nama_cabang ?? 'Tidak Ada Cabang' }}</span>
            </small>
        </div>
    </div>

    <div class="row">
        {{-- BAGIAN KIRI: LIST STOK CABANG --}}
        <div class="col-lg-5 mb-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-header bg-white border-bottom p-4">
                    <h5 class="fw-bold mb-3">1. Pilih Barang</h5>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control bg-light border-start-0" placeholder="Cari IMEI / Merk / Tipe..." wire:model.live.debounce.300ms="searchStok">
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($stoks as $stok)
                            <button type="button" 
                                    wire:click="selectStok({{ $stok->id }})"
                                    class="list-group-item list-group-item-action p-4 border-bottom d-flex justify-content-between align-items-center {{ $selectedStokId == $stok->id ? 'bg-primary bg-opacity-10 border-primary' : '' }}">
                                
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <h6 class="mb-0 fw-bold text-dark">{{ $stok->merk->nama }} {{ $stok->tipe->nama }}</h6>
                                        @if($stok->kondisi == 'Baru')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle py-1 px-2" style="font-size: 0.65rem;">NEW</span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle py-1 px-2" style="font-size: 0.65rem;">2ND</span>
                                        @endif
                                    </div>
                                    <small class="text-muted font-monospace d-block">IMEI: {{ $stok->imei }}</small>
                                    <div class="mt-2">
                                        <span class="badge bg-light text-dark border">
                                            {{ $stok->ram_storage }}
                                        </span>
                                    </div>
                                </div>

                                <div class="text-end">
                                    <h6 class="mb-1 fw-bold text-primary">Rp {{ number_format($stok->harga_jual, 0,',','.') }}</h6>
                                    <small class="text-muted">Stok: <span class="fw-bold text-dark">{{ $stok->jumlah }}</span></small>
                                </div>
                            </button>
                        @empty
                            <div class="text-center py-5 px-4">
                                <div class="mb-3">
                                    <i class="fas fa-box-open fs-1 text-muted opacity-25"></i>
                                </div>
                                <h6 class="fw-bold text-dark">Stok Tidak Ditemukan</h6>
                                <p class="text-muted small mb-0">
                                    Pastikan Admin Produk sudah menginput stok ke cabang 
                                    <span class="fw-bold">{{ Auth::user()->cabang->nama_cabang ?? 'ini' }}</span> 
                                    dan stok > 0.
                                </p>
                            </div>
                        @endforelse
                    </div>
                </div>
                {{-- Pagination Links --}}
                @if($stoks->hasPages())
                    <div class="card-footer bg-white border-top p-3">
                        {{ $stoks->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- BAGIAN KANAN: FORM DATA DIRI & BUKTI --}}
        <div class="col-lg-7 mb-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-bottom p-4">
                    <h5 class="fw-bold mb-0">2. Detail Transaksi</h5>
                </div>
                <div class="card-body p-4">
                    @if($selectedStokDetail)
                        <div class="alert alert-primary d-flex align-items-center mb-4 border-0 shadow-sm rounded-3">
                            <div class="bg-white p-2 rounded-circle text-primary me-3 shadow-sm">
                                <i class="fas fa-check fs-5"></i>
                            </div>
                            <div class="flex-grow-1">
                                <small class="d-block text-uppercase fw-bold opacity-75" style="font-size: 0.7rem;">Produk Terpilih:</small>
                                <div class="fw-bold fs-5">{{ $selectedStokDetail->merk->nama }} {{ $selectedStokDetail->tipe->nama }}</div>
                                <div class="font-monospace small">{{ $selectedStokDetail->imei }}</div>
                            </div>
                            <button wire:click="cancelSelection" class="btn btn-sm btn-light fw-bold text-danger border shadow-sm">
                                <i class="fas fa-times me-1"></i> Batal
                            </button>
                        </div>

                        <form wire:submit.prevent="storePenjualan">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-secondary">Nama Customer <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control rounded-3 p-2" wire:model="nama_customer" placeholder="Contoh: Budi Santoso">
                                    @error('nama_customer') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-secondary">Nomor WhatsApp <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-muted border-end-0 rounded-start-3"><i class="fab fa-whatsapp"></i></span>
                                        <input type="number" class="form-control border-start-0 rounded-end-3 p-2" wire:model="nomor_wa" placeholder="0812...">
                                    </div>
                                    @error('nomor_wa') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label small fw-bold text-secondary">Harga Deal (Rp) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-muted border-end-0 rounded-start-3">Rp</span>
                                        <input type="number" class="form-control border-start-0 rounded-end-3 p-2 fw-bold text-dark fs-5" wire:model="harga_deal">
                                    </div>
                                    <div class="form-text small">Harga Jual Standar: Rp {{ number_format($selectedStokDetail->harga_jual, 0,',','.') }}</div>
                                    @error('harga_deal') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label small fw-bold text-secondary">Foto Bukti / Customer <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control rounded-3" wire:model="foto_bukti">
                                    <div class="form-text small">Upload foto unit bersama customer atau bukti transfer. Max 2MB.</div>
                                    
                                    <div wire:loading wire:target="foto_bukti" class="mt-2 text-primary small">
                                        <i class="fas fa-spinner fa-spin me-1"></i> Mengupload gambar...
                                    </div>

                                    @if ($foto_bukti) 
                                        <div class="mt-3 position-relative d-inline-block">
                                            <img src="{{ $foto_bukti->temporaryUrl() }}" class="img-thumbnail rounded-3 shadow-sm" style="height: 100px; width: auto;">
                                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-success">
                                                <i class="fas fa-check"></i>
                                            </span>
                                        </div>
                                    @endif
                                    @error('foto_bukti') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label small fw-bold text-secondary">Catatan (Opsional)</label>
                                    <textarea class="form-control rounded-3 p-2" rows="3" wire:model="catatan" placeholder="Keterangan tambahan (bonus aksesoris, metode pembayaran, dll)"></textarea>
                                </div>
                            </div>

                            <div class="d-grid mt-4 pt-2">
                                <button type="submit" class="btn btn-dark rounded-pill py-3 fw-bold shadow-lg hover-scale">
                                    <span wire:loading.remove>
                                        <i class="fas fa-save me-2"></i> Simpan Transaksi Penjualan
                                    </span>
                                    <span wire:loading>
                                        <i class="fas fa-spinner fa-spin me-2"></i> Memproses Data...
                                    </span>
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="text-center py-5 my-5">
                            <div class="mb-4">
                                <div class="bg-light rounded-circle d-inline-flex p-4 text-muted">
                                    <i class="fas fa-arrow-left fa-3x opacity-50"></i>
                                </div>
                            </div>
                            <h5 class="fw-bold text-dark">Belum Ada Barang Dipilih</h5>
                            <p class="text-muted">Silakan pilih barang dari daftar stok di sebelah kiri untuk mulai mengisi data penjualan.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        .hover-scale:hover { transform: scale(1.02); transition: 0.3s; }
    </style>
</div>