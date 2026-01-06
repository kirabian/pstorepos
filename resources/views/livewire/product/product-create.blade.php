<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white p-4 border-bottom-0 pb-0">
                    <h5 class="mb-1 fw-bold">Tambah Produk Baru</h5>
                    <p class="text-muted small">Pilih jenis produk yang akan diinput.</p>
                </div>
                
                <div class="card-body p-4 pt-2">
                    <div class="d-flex justify-content-center mb-4 bg-light p-1 rounded-pill" style="width: fit-content; margin: 0 auto;">
                        <button wire:click="$set('form_type', 'imei')" 
                                class="btn btn-sm rounded-pill px-4 {{ $form_type == 'imei' ? 'btn-dark shadow' : 'text-muted' }}"
                                style="transition: all 0.2s;">
                            <i class="fas fa-mobile-alt me-2"></i> HP / IMEI
                        </button>
                        <button wire:click="$set('form_type', 'non-imei')" 
                                class="btn btn-sm rounded-pill px-4 {{ $form_type == 'non-imei' ? 'btn-dark shadow' : 'text-muted' }}"
                                style="transition: all 0.2s;">
                            <i class="fas fa-headphones me-2"></i> Non-IMEI
                        </button>
                        <button wire:click="$set('form_type', 'jasa')" 
                                class="btn btn-sm rounded-pill px-4 {{ $form_type == 'jasa' ? 'btn-dark shadow' : 'text-muted' }}"
                                style="transition: all 0.2s;">
                            <i class="fas fa-tools me-2"></i> Jasa
                        </button>
                    </div>

                    <form wire:submit.prevent="save">
                        
                        @if($form_type == 'jasa')
                            <div class="row g-3">
                                <div class="col-12 bg-primary-subtle p-3 rounded-3 mb-2">
                                    <div class="d-flex align-items-center text-primary">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <small class="fw-bold">Mode Input Jasa / Service (Tanpa Stok)</small>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted">Kategori <span class="text-danger">*</span></label>
                                    <select wire:model="category_id" class="form-select rounded-3 bg-light">
                                        <option value="">Pilih Kategori</option>
                                        @foreach($categories as $cat)
                                            <option value="{{$cat->id}}">{{$cat->name}}</option>
                                        @endforeach
                                    </select>
                                    @error('category_id') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted">Nama Jasa / Layanan <span class="text-danger">*</span></label>
                                    <input type="text" wire:model="name" class="form-control rounded-3" placeholder="Contoh: Ganti LCD iPhone 11">
                                    @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted">Biaya Modal (Rp)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">Rp</span>
                                        <input type="number" wire:model="cost_price" class="form-control border-start-0 ps-0" placeholder="0">
                                    </div>
                                    @error('cost_price') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted">Harga Jual (SRP) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">Rp</span>
                                        <input type="number" wire:model="srp_price" class="form-control border-start-0 ps-0" placeholder="0">
                                    </div>
                                    @error('srp_price') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        
                        @else
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted">Merek / Brand <span class="text-danger">*</span></label>
                                    <select wire:model.live="brand_id" class="form-select rounded-3">
                                        <option value="">Pilih Merek</option>
                                        @foreach($brands as $br)
                                            <option value="{{$br->id}}">{{$br->name}}</option>
                                        @endforeach
                                    </select>
                                    @error('brand_id') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted">Tipe / Nama Produk <span class="text-danger">*</span></label>
                                    
                                    <div class="position-relative">
                                        <input type="text" 
                                               wire:model="name" 
                                               class="form-control rounded-3" 
                                               list="product-list" 
                                               placeholder="Pilih atau Ketik Baru..."
                                               autocomplete="off">
                                        
                                        <datalist id="product-list">
                                            @foreach($existing_types as $type)
                                                <option value="{{ $type }}">
                                            @endforeach
                                        </datalist>
                                    </div>

                                    @if($brand_id)
                                        @if(count($existing_types) > 0)
                                            <div class="form-text small text-success">
                                                <i class="fas fa-check-circle me-1"></i> {{ count($existing_types) }} tipe ditemukan untuk brand ini.
                                            </div>
                                        @else
                                            <div class="form-text small text-muted">
                                                <i class="fas fa-info-circle me-1"></i> Belum ada data tipe untuk brand ini. Silakan ketik baru.
                                            </div>
                                        @endif
                                    @endif
                                    @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold small text-muted">Kategori <span class="text-danger">*</span></label>
                                    <select wire:model="category_id" class="form-select rounded-3 bg-light">
                                        <option value="">Pilih Kategori</option>
                                        @foreach($categories as $cat)
                                            <option value="{{$cat->id}}">{{$cat->name}}</option>
                                        @endforeach
                                    </select>
                                    @error('category_id') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>

                                @if($form_type == 'imei')
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold small text-muted">RAM <span class="text-danger">*</span></label>
                                        <select wire:model="ram" class="form-select rounded-3">
                                            <option value="">Pilih RAM</option>
                                            <option value="2GB">2GB</option>
                                            <option value="3GB">3GB</option>
                                            <option value="4GB">4GB</option>
                                            <option value="6GB">6GB</option>
                                            <option value="8GB">8GB</option>
                                            <option value="12GB">12GB</option>
                                            <option value="16GB">16GB</option>
                                            <option value="24GB">24GB</option>
                                        </select>
                                        @error('ram') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold small text-muted">Internal / Storage <span class="text-danger">*</span></label>
                                        <select wire:model="storage" class="form-select rounded-3">
                                            <option value="">Pilih Storage</option>
                                            <option value="32GB">32GB</option>
                                            <option value="64GB">64GB</option>
                                            <option value="128GB">128GB</option>
                                            <option value="256GB">256GB</option>
                                            <option value="512GB">512GB</option>
                                            <option value="1TB">1TB</option>
                                        </select>
                                        @error('storage') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-muted">Warna <span class="text-danger">*</span></label>
                                        <input type="text" wire:model="color" class="form-control rounded-3" placeholder="Contoh: Deep Purple">
                                        @error('color') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-muted">Kondisi <span class="text-danger">*</span></label>
                                        <select wire:model="condition" class="form-select rounded-3">
                                            <option value="Baru">Baru (New)</option>
                                            <option value="Second">Second (Bekas)</option>
                                            <option value="BNOB">BNOB</option>
                                        </select>
                                        @error('condition') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>

                                @else
                                    <div class="col-md-8">
                                        <label class="form-label fw-bold small text-muted">Varian Warna / Tipe (Opsional)</label>
                                        <input type="text" wire:model="color" class="form-control rounded-3" placeholder="Contoh: Hitam, Putih, atau Kosongkan">
                                    </div>
                                @endif

                                <div class="col-12"><hr class="text-muted opacity-25"></div>
                                
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted">Harga Modal (Rp) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">Rp</span>
                                        <input type="number" wire:model="cost_price" class="form-control border-start-0 ps-0" placeholder="0">
                                    </div>
                                    @error('cost_price') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted">Harga Jual (SRP) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">Rp</span>
                                        <input type="number" wire:model="srp_price" class="form-control border-start-0 ps-0" placeholder="0">
                                    </div>
                                    @error('srp_price') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>

                                @if($form_type == 'imei')
                                    <div class="col-12 mt-3">
                                        <label class="form-label fw-bold small text-muted">
                                            Input IMEI (Untuk Stok Awal)
                                            <i class="fas fa-question-circle ms-1" title="Satu baris satu IMEI"></i>
                                        </label>
                                        <textarea wire:model.live="imei_list" class="form-control rounded-3" rows="4" placeholder="Scan atau ketik IMEI di sini...&#10;8694000...&#10;8694001..."></textarea>
                                        <div class="d-flex justify-content-between mt-1">
                                            <small class="text-muted">Pisahkan dengan baris baru (Enter)</small>
                                            <small class="fw-bold text-primary">Total Stok Terbaca: {{ $stock }}</small>
                                        </div>
                                    </div>
                                @else
                                    <div class="col-md-4 mt-3">
                                        <label class="form-label fw-bold small text-muted">Stok Awal <span class="text-danger">*</span></label>
                                        <input type="number" wire:model="stock" class="form-control rounded-3" placeholder="0">
                                        @error('stock') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col-md-8 mt-3">
                                        <label class="form-label fw-bold small text-muted">Catatan (Opsional)</label>
                                        <input type="text" wire:model="description" class="form-control rounded-3" placeholder="Keterangan tambahan...">
                                    </div>
                                @endif
                            </div>
                        @endif

                        <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                            <a href="{{ route('product.index') }}" class="btn btn-light rounded-3 px-4">Batal</a>
                            <button type="submit" class="btn btn-dark px-4 rounded-3 shadow-sm" style="background: black;">
                                <i class="fas fa-save me-2"></i> Simpan Produk
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>