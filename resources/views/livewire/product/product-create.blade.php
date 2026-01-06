<div class="container-fluid py-3 py-md-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8 col-xl-6">
            <div class="card border-0 shadow rounded-4">
                <div class="card-header bg-white p-4 border-bottom-0">
                    <h5 class="mb-1 fw-bold text-dark">Tambah Produk Baru</h5>
                    <p class="text-muted small mb-0">Isi data fisik produk. Stok & Harga diinput setelah ini.</p>
                </div>
                
                <div class="card-body p-4 pt-0">
                    <div class="d-grid d-md-flex gap-2 justify-content-center mb-4 bg-light p-2 rounded-3">
                        <button wire:click="$set('form_type', 'imei')" 
                                class="btn btn-sm rounded-3 py-2 {{ $form_type == 'imei' ? 'btn-dark shadow-sm' : 'text-muted border-0' }}">
                            <i class="fas fa-mobile-alt me-2"></i>HP / IMEI
                        </button>
                        <button wire:click="$set('form_type', 'non-imei')" 
                                class="btn btn-sm rounded-3 py-2 {{ $form_type == 'non-imei' ? 'btn-dark shadow-sm' : 'text-muted border-0' }}">
                            <i class="fas fa-headphones me-2"></i>Non-IMEI
                        </button>
                        <button wire:click="$set('form_type', 'jasa')" 
                                class="btn btn-sm rounded-3 py-2 {{ $form_type == 'jasa' ? 'btn-dark shadow-sm' : 'text-muted border-0' }}">
                            <i class="fas fa-tools me-2"></i>Jasa
                        </button>
                    </div>

                    <form wire:submit.prevent="save">
                        <div class="row g-3">
                            
                            {{-- SECTION BRAND & NAME --}}
                            @if($form_type != 'jasa')
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-bold small text-secondary">Merek / Brand <span class="text-danger">*</span></label>
                                <select wire:model.live="brand_id" class="form-select form-select-lg fs-6 rounded-3">
                                    <option value="">Pilih Merek</option>
                                    @foreach($brands as $br) <option value="{{$br->id}}">{{$br->name}}</option> @endforeach
                                </select>
                                @error('brand_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                            @endif

                            <div class="col-12 {{ $form_type != 'jasa' ? 'col-md-6' : 'col-md-12' }}">
                                <label class="form-label fw-bold small text-secondary">Nama Tipe / Produk <span class="text-danger">*</span></label>
                                <div class="position-relative">
                                    <input type="text" wire:model="name" class="form-control form-control-lg fs-6 rounded-3" 
                                           list="product-list" placeholder="Contoh: iPhone 11" autocomplete="off">
                                    <datalist id="product-list">
                                        @foreach($existing_types as $type) <option value="{{ $type }}"> @endforeach
                                    </datalist>
                                </div>
                                @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12 col-md-12">
                                <label class="form-label fw-bold small text-secondary">Kategori <span class="text-danger">*</span></label>
                                <select wire:model="category_id" class="form-select rounded-3 bg-light">
                                    <option value="">Pilih Kategori</option>
                                    @foreach($categories as $cat) <option value="{{$cat->id}}">{{$cat->name}}</option> @endforeach
                                </select>
                                @error('category_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            {{-- SECTION SPESIFIKASI --}}
                            @if($form_type == 'imei')
                                <div class="col-6 col-md-6">
                                    <label class="form-label fw-bold small text-secondary">RAM</label>
                                    <select wire:model="ram" class="form-select rounded-3">
                                        <option value="">Pilih</option>
                                        <option value="4GB">4GB</option><option value="6GB">6GB</option>
                                        <option value="8GB">8GB</option><option value="12GB">12GB</option>
                                        <option value="16GB">16GB</option>
                                    </select>
                                    @error('ram') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-6 col-md-6">
                                    <label class="form-label fw-bold small text-secondary">Storage</label>
                                    <select wire:model="storage" class="form-select rounded-3">
                                        <option value="">Pilih</option>
                                        <option value="64GB">64GB</option><option value="128GB">128GB</option>
                                        <option value="256GB">256GB</option><option value="512GB">512GB</option>
                                        <option value="1TB">1TB</option>
                                    </select>
                                    @error('storage') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold small text-secondary">Warna</label>
                                    <input type="text" wire:model="color" class="form-control rounded-3" placeholder="Misal: Black">
                                    @error('color') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold small text-secondary">Kondisi</label>
                                    <select wire:model="condition" class="form-select rounded-3">
                                        <option value="Baru">Baru (New)</option>
                                        <option value="Second">Second (Bekas)</option>
                                        <option value="BNOB">BNOB</option>
                                    </select>
                                </div>
                            
                            @elseif($form_type == 'non-imei')
                                <div class="col-12">
                                    <label class="form-label fw-bold small text-secondary">Warna / Varian (Opsional)</label>
                                    <input type="text" wire:model="color" class="form-control rounded-3" placeholder="Misal: Putih">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold small text-secondary">Catatan (Opsional)</label>
                                    <input type="text" wire:model="description" class="form-control rounded-3" placeholder="Keterangan tambahan...">
                                </div>
                            @endif

                        </div>

                        <div class="mt-5 d-grid gap-2">
                            <button type="submit" class="btn btn-dark btn-lg rounded-3 shadow">
                                Lanjut ke Stok & Harga <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                            <a href="{{ route('product.index') }}" class="btn btn-light btn-lg rounded-3 text-muted">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>