<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div class="d-flex align-items-center">
            <a href="{{ route('barang-masuk.index') }}" class="btn btn-light rounded-circle shadow-sm me-3">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h4 class="fw-bold text-black mb-0">Input Barang Masuk (Gudang)</h4>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-body p-5">
                    
                    @if (session()->has('success'))
                        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
                            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form wire:submit.prevent="simpan">
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase text-secondary">Scan / Input IMEI</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-barcode"></i>
                                </span>
                                <input type="text" class="form-control border-start-0 bg-light" 
                                       wire:model="imei" 
                                       placeholder="Scan IMEI disini..." autofocus>
                            </div>
                            @error('imei') <small class="text-danger fw-bold mt-1">{{ $message }}</small> @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold small text-uppercase text-secondary">Nama Barang / Tipe</label>
                                <input type="text" class="form-control form-control-lg" 
                                       wire:model="nama_barang" placeholder="Contoh: iPhone 14 Pro Max">
                                @error('nama_barang') <small class="text-danger fw-bold mt-1">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold small text-uppercase text-secondary">Kondisi Fisik</label>
                                <select class="form-select form-select-lg" wire:model="kondisi">
                                    <option value="Baru">Baru (New)</option>
                                    <option value="Bekas">Bekas (Second)</option>
                                </select>
                                @error('kondisi') <small class="text-danger fw-bold mt-1">{{ $message }}</small> @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase text-secondary">Catatan / Keterangan</label>
                            <textarea class="form-control" rows="3" wire:model="keterangan" placeholder="Asal barang, kelengkapan, dll..."></textarea>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-dark btn-lg rounded-3 fw-bold">
                                <i class="fas fa-save me-2"></i> Simpan Stok Gudang
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>