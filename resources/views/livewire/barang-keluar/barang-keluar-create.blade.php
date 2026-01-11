<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div class="d-flex align-items-center">
            <a href="{{ route('barang-keluar.index') }}" class="btn btn-light rounded-circle shadow-sm me-3">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h4 class="fw-bold text-black mb-0">Input Barang Keluar (Gudang)</h4>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-lg rounded-4">
                <div class="card-body p-5">
                    
                    @if (session()->has('success'))
                        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
                            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form wire:submit.prevent="prosesKeluar">
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase text-secondary">Scan IMEI Barang</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-danger bg-opacity-10 text-danger border-end-0">
                                    <i class="fas fa-barcode"></i>
                                </span>
                                <input type="text" class="form-control border-start-0 bg-light" 
                                       wire:model.live.debounce.500ms="imei" 
                                       placeholder="Scan IMEI barang yang akan keluar..." autofocus>
                            </div>
                            @error('imei') <small class="text-danger fw-bold mt-1">{{ $message }}</small> @enderror
                        </div>

                        @if($barangDitemukan)
                            <div class="alert alert-info border-0 bg-info bg-opacity-10 d-flex align-items-center mb-4 rounded-3">
                                <i class="fas fa-mobile-alt fs-2 text-info me-3"></i>
                                <div>
                                    <h6 class="fw-bold text-dark mb-0">{{ $barangDitemukan->nama_barang }}</h6>
                                    <small class="text-muted">Kondisi: {{ $barangDitemukan->kondisi }} | Masuk: {{ $barangDitemukan->created_at->format('d M Y') }}</small>
                                </div>
                            </div>
                        @elseif($imei && !$barangDitemukan)
                             <div class="alert alert-warning border-0 bg-warning bg-opacity-10 mb-4 rounded-3">
                                <small class="fw-bold text-warning"><i class="fas fa-exclamation-triangle me-1"></i> Barang tidak ditemukan di gudang ini.</small>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase text-secondary">Alasan Keluar</label>
                            <select class="form-select form-select-lg" wire:model="kategori">
                                @foreach($opsiKategori as $opt)
                                    <option value="{{ $opt }}">{{ $opt }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase text-secondary">Detail / Tujuan</label>
                            <textarea class="form-control" rows="2" wire:model="keterangan" placeholder="Contoh: Dikirim ke Cabang Condet via JNE..."></textarea>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-danger btn-lg rounded-3 fw-bold" 
                                    @if(!$barangDitemukan) disabled @endif>
                                <i class="fas fa-sign-out-alt me-2"></i> Proses Barang Keluar
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>