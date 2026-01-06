<div class="container-fluid py-3 py-md-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8 col-xl-7">
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
                            
                            {{-- --- BAGIAN 1: BRAND & NAMA --- --}}
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
                                           list="product-list" placeholder="Contoh: iPhone 13 Pro" autocomplete="off">
                                    <datalist id="product-list">
                                        @foreach($existing_types as $type) <option value="{{ $type }}"> @endforeach
                                    </datalist>
                                </div>
                                @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold small text-secondary">Kategori <span class="text-danger">*</span></label>
                                <select wire:model="category_id" class="form-select rounded-3 bg-light">
                                    <option value="">Pilih Kategori</option>
                                    @foreach($categories as $cat) <option value="{{$cat->id}}">{{$cat->name}}</option> @endforeach
                                </select>
                                @error('category_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            {{-- --- BAGIAN 2: SPESIFIKASI KHUSUS IMEI --- --}}
                            @if($form_type == 'imei')
                                <div class="col-12"><hr class="my-2 border-light"></div>
                                
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
                                    <input type="text" wire:model="color" class="form-control rounded-3" placeholder="Misal: Alpine Green">
                                    @error('color') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold small text-secondary">Kondisi</label>
                                    <select wire:model="condition" class="form-select rounded-3">
                                        <option value="Baru">Baru (New)</option>
                                        <option value="Second">Second (Bekas)</option>
                                        <option value="BNOB">BNOB</option>
                                    </select>
                                    @error('condition') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>

                                {{-- FIELD IMEI & CATATAN --}}
                                <div class="col-12 mt-4">
                                    <div class="card bg-light border-0 rounded-3">
                                        <div class="card-body">
                                            <label class="form-label fw-bold small text-secondary d-flex justify-content-between">
                                                <span>Input Daftar IMEI</span>
                                                <span class="badge bg-primary rounded-pill">Total: {{ $stock }} Unit</span>
                                            </label>
                                            <textarea wire:model.live="imei_list" class="form-control font-monospace text-uppercase" rows="5" 
                                                placeholder="Scan/Ketik IMEI disini...&#10;8642390500... (Min 15 digit)&#10;8642390501..."></textarea>
                                            <div class="form-text small text-muted"><i class="fas fa-info-circle"></i> Pisahkan dengan Enter (Baris baru). Min 15 digit per IMEI.</div>
                                            @error('imei_list') <div class="text-danger small mt-1 fw-bold">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-bold small text-secondary">Catatan (Opsional)</label>
                                    <input type="text" wire:model="description" class="form-control rounded-3" placeholder="Contoh: Ex iBox, Fullset, Battery Health 90%...">
                                </div>
                            
                            {{-- --- BAGIAN 3: NON-IMEI --- --}}
                            @elseif($form_type == 'non-imei')
                                <div class="col-12">
                                    <label class="form-label fw-bold small text-secondary">Warna / Varian (Opsional)</label>
                                    <input type="text" wire:model="color" class="form-control rounded-3" placeholder="Misal: Putih">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-bold small text-secondary">Stok Awal <span class="text-danger">*</span></label>
                                    <input type="number" wire:model="stock" class="form-control rounded-3" placeholder="0">
                                    @error('stock') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-12 col-md-8">
                                    <label class="form-label fw-bold small text-secondary">Catatan (Opsional)</label>
                                    <input type="text" wire:model="description" class="form-control rounded-3" placeholder="Keterangan tambahan...">
                                </div>
                            @endif

                        </div>

                        {{-- TOMBOL SUBMIT --}}
                        <div class="mt-5 d-grid gap-2">
                            <button type="submit" class="btn btn-dark btn-lg rounded-3 shadow">
                                Lanjut ke Harga <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                            <a href="{{ route('product.index') }}" class="btn btn-light btn-lg rounded-3 text-muted">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>