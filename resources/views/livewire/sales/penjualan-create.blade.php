<div class="container-fluid">
    
    {{-- ================================================================= --}}
    {{-- HALAMAN 1: PEMILIHAN AKUN SALES (LANDING PAGE) --}}
    {{-- ================================================================= --}}
    @if(!$isSalesSelected)
        <div class="d-flex flex-column align-items-center justify-content-center py-5 animate__animated animate__fadeIn">
            <div class="text-center mb-5">
                <div class="bg-dark text-white rounded-circle d-inline-flex p-4 mb-3 shadow-lg">
                    <i class="fas fa-users fs-1"></i>
                </div>
                <h2 class="fw-bold text-dark">Siapa yang bertugas?</h2>
                <p class="text-muted fs-5">Silakan pilih akun Sales Anda untuk memulai transaksi.</p>
                <span class="badge bg-primary px-3 py-2 rounded-pill">
                    <i class="fas fa-store me-1"></i> {{ Auth::user()->cabang->nama_cabang ?? 'Unknown Branch' }}
                </span>
            </div>

            <div class="row justify-content-center w-100" style="max-width: 1000px;">
                @foreach($salesUsers as $sales)
                    <div class="col-6 col-md-4 col-lg-3 mb-4">
                        <button wire:click="chooseSales({{ $sales->id }})" 
                                class="card h-100 w-100 border-0 shadow-sm rounded-4 text-center p-4 hover-card btn btn-light position-relative overflow-hidden">
                            
                            {{-- Efek Hover Background --}}
                            <div class="hover-bg"></div>

                            <div class="position-relative z-1">
                                <div class="mb-3">
                                    @if($sales->avatar_url)
                                        <img src="{{ $sales->avatar_url }}" class="rounded-circle border border-3 border-white shadow-sm object-fit-cover" width="80" height="80">
                                    @else
                                        <div class="avatar-placeholder rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center fs-2 fw-bold shadow-sm mx-auto" style="width: 80px; height: 80px;">
                                            {{ substr($sales->nama_lengkap, 0, 1) }}
                                        </div>
                                    @endif
                                </div>
                                <h5 class="fw-bold text-dark mb-1 text-truncate">{{ $sales->nama_lengkap }}</h5>
                                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Sales Force</small>
                            </div>
                        </button>
                    </div>
                @endforeach
            </div>

            {{-- Jika User Login sendiri adalah Sales, tampilkan opsi cepat --}}
            @if(Auth::user()->role == 'sales')
                <div class="mt-4">
                    <button wire:click="chooseSales({{ Auth::id() }})" class="btn btn-dark rounded-pill px-5 py-3 fw-bold shadow">
                        <i class="fas fa-user-circle me-2"></i> Masuk Sebagai {{ Auth::user()->nama_lengkap }} (Saya)
                    </button>
                </div>
            @endif
        </div>

        <style>
            .hover-card { transition: all 0.3s ease; top: 0; }
            .hover-card:hover { transform: translateY(-10px); box-shadow: 0 1rem 3rem rgba(0,0,0,.175)!important; }
            .object-fit-cover { object-fit: cover; }
        </style>

    {{-- ================================================================= --}}
    {{-- HALAMAN 2: INPUT PENJUALAN (FORM UTAMA) --}}
    {{-- ================================================================= --}}
    @else
        
        {{-- Header Bar Informasi Sales Aktif --}}
        <div class="bg-white border-bottom shadow-sm py-2 px-4 mb-4 rounded-3 d-flex justify-content-between align-items-center animate__animated animate__fadeInDown">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <i class="fas fa-user-check"></i>
                </div>
                <div style="line-height: 1.2;">
                    <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.7rem;">Sales Aktif</small>
                    <span class="fw-bold text-dark fs-6">{{ $salesUserDetail->nama_lengkap ?? 'Sales' }}</span>
                </div>
            </div>
            <button wire:click="changeSalesAccount" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold">
                <i class="fas fa-exchange-alt me-1"></i> Ganti Akun
            </button>
        </div>

        <div class="row animate__animated animate__fadeIn">
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
                                            <h6 class="mb-0 fw-bold text-dark">{{ $stok->merk->nama ?? '-' }} {{ $stok->tipe->nama ?? '-' }}</h6>
                                            
                                            @if($stok->cabang_id == Auth::user()->cabang_id)
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle py-1 px-2" style="font-size: 0.6rem;">CABANG INI</span>
                                            @elseif(is_null($stok->cabang_id))
                                                <span class="badge bg-info-subtle text-info border border-info-subtle py-1 px-2" style="font-size: 0.6rem;">STOK PUSAT</span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle py-1 px-2" style="font-size: 0.6rem;">LAINNYA</span>
                                            @endif
                                        </div>
                                        
                                        <small class="text-muted font-monospace d-block">IMEI: {{ $stok->imei }}</small>
                                        <div class="mt-2">
                                            @if($stok->kondisi == 'Baru')
                                                <span class="badge bg-success-subtle text-success border border-success-subtle py-1 px-2" style="font-size: 0.65rem;">NEW</span>
                                            @else
                                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle py-1 px-2" style="font-size: 0.65rem;">2ND</span>
                                            @endif
                                            <span class="badge bg-light text-dark border ms-1">
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
                                        Tidak ada barang ready yang cocok.<br>
                                        (Menampilkan stok Cabang {{ Auth::user()->cabang->nama_cabang ?? 'Pusat' }} & Stok Pusat)
                                    </p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                    
                    @if($stoks->hasPages())
                        <div class="card-footer bg-white border-top p-3">
                            {{ $stoks->links() }}
                        </div>
                    @endif
                </div>
            </div>

            {{-- BAGIAN KANAN: FORM DATA DIRI --}}
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
                                    <div class="fw-bold fs-5">{{ $selectedStokDetail->merk->nama ?? '-' }} {{ $selectedStokDetail->tipe->nama ?? '-' }}</div>
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
    @endif
</div>