<div class="container-fluid">
    <div class="d-flex align-items-center mb-4">
        <i class="fas fa-cash-register fs-3 me-2"></i>
        <div>
            <h4 class="fw-bold mb-0">Input Penjualan</h4>
            <small class="text-muted">Cabang: {{ Auth::user()->cabang->nama_cabang ?? 'Unknown' }}</small>
        </div>
    </div>

    <div class="row">
        {{-- BAGIAN KIRI: LIST STOK CABANG --}}
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-bottom p-3">
                    <h5 class="fw-bold mb-0">1. Pilih Barang</h5>
                </div>
                <div class="card-body">
                    <input type="text" class="form-control rounded-pill mb-3" placeholder="Cari IMEI / Merk..." wire:model.live.debounce.300ms="searchStok">
                    
                    <div class="list-group">
                        @forelse($stoks as $stok)
                            <button type="button" 
                                    wire:click="selectStok({{ $stok->id }})"
                                    class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ $selectedStokId == $stok->id ? 'active' : '' }}">
                                <div>
                                    <h6 class="mb-1 fw-bold">{{ $stok->merk->nama }} {{ $stok->tipe->nama }}</h6>
                                    <small class="d-block text-{{ $selectedStokId == $stok->id ? 'light' : 'muted' }}">
                                        IMEI: {{ $stok->imei }}
                                    </small>
                                    <span class="badge {{ $stok->kondisi == 'Baru' ? 'bg-success' : 'bg-warning' }} mt-1">{{ $stok->kondisi }}</span>
                                </div>
                                <div class="text-end">
                                    <h6 class="mb-1">Rp {{ number_format($stok->harga_jual, 0,',','.') }}</h6>
                                    <small>Stok: {{ $stok->jumlah }}</small>
                                </div>
                            </button>
                        @empty
                            <div class="text-center py-4 text-muted">
                                Tidak ada stok tersedia di cabang ini.
                            </div>
                        @endforelse
                    </div>
                    <div class="mt-3">
                        {{ $stoks->links() }}
                    </div>
                </div>
            </div>
        </div>

        {{-- BAGIAN KANAN: FORM DATA DIRI & BUKTI --}}
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-bottom p-3">
                    <h5 class="fw-bold mb-0">2. Data Penjualan</h5>
                </div>
                <div class="card-body">
                    @if($selectedStokDetail)
                        <div class="alert alert-info d-flex align-items-center mb-4">
                            <i class="fas fa-check-circle fs-4 me-3"></i>
                            <div>
                                <small class="d-block text-uppercase fw-bold">Barang Terpilih:</small>
                                <strong>{{ $selectedStokDetail->merk->nama }} {{ $selectedStokDetail->tipe->nama }}</strong> <br>
                                <small>IMEI: {{ $selectedStokDetail->imei }}</small>
                            </div>
                            <button wire:click="cancelSelection" class="btn btn-sm btn-outline-dark ms-auto">Ganti</button>
                        </div>

                        <form wire:submit.prevent="storePenjualan">
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-secondary">Nama Customer <span class="text-danger">*</span></label>
                                <input type="text" class="form-control rounded-3" wire:model="nama_customer">
                                @error('nama_customer') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-secondary">Nomor WhatsApp <span class="text-danger">*</span></label>
                                <input type="number" class="form-control rounded-3" wire:model="nomor_wa" placeholder="08...">
                                @error('nomor_wa') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-secondary">Harga Deal (Rp) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control rounded-3" wire:model="harga_deal">
                                @error('harga_deal') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-secondary">Foto Bukti / Customer <span class="text-danger">*</span></label>
                                <input type="file" class="form-control rounded-3" wire:model="foto_bukti">
                                <div class="form-text small">Max 2MB. Foto unit dengan customer atau nota.</div>
                                @if ($foto_bukti) 
                                    <img src="{{ $foto_bukti->temporaryUrl() }}" class="img-thumbnail mt-2 rounded-3" width="150">
                                @endif
                                @error('foto_bukti') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label small fw-bold text-secondary">Catatan (Opsional)</label>
                                <textarea class="form-control rounded-3" rows="2" wire:model="catatan"></textarea>
                            </div>

                            <button type="submit" class="btn btn-dark w-100 rounded-pill py-2 fw-bold">
                                <span wire:loading.remove>Proses Penjualan</span>
                                <span wire:loading><i class="fas fa-spinner fa-spin"></i> Memproses...</span>
                            </button>
                        </form>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-arrow-left fa-2x mb-3 opacity-25"></i>
                            <p>Silakan pilih barang di sebelah kiri terlebih dahulu.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>