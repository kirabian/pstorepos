<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-1">Tambah Produk Baru</h5>
                    <p class="text-muted small mb-4">Pilih jenis produk yang akan diinput.</p>

                    {{-- Mode Selector --}}
                    <div class="d-flex justify-content-center mb-4">
                        <div class="btn-group bg-light rounded-pill p-1">
                            <button wire:click="$set('mode', 'imei')" class="btn rounded-pill px-4 {{ $mode == 'imei' ? 'btn-dark' : 'text-muted' }}">
                                <i class="fas fa-mobile-alt me-2"></i> HP / IMEI
                            </button>
                            <button wire:click="$set('mode', 'non-imei')" class="btn rounded-pill px-4 {{ $mode == 'non-imei' ? 'btn-dark' : 'text-muted' }}">
                                <i class="fas fa-headphones me-2"></i> Non-IMEI
                            </button>
                            <button wire:click="$set('mode', 'jasa')" class="btn rounded-pill px-4 {{ $mode == 'jasa' ? 'btn-dark' : 'text-muted' }}">
                                <i class="fas fa-tools me-2"></i> Jasa
                            </button>
                        </div>
                    </div>

                    <form wire:submit.prevent="save">
                        <div class="row g-3">
                            
                            {{-- Brand & Tipe --}}
                            @if($mode != 'jasa')
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-danger">Merek / Brand *</label>
                                <select wire:model.live="brand_id" class="form-select rounded-3">
                                    <option value="">Pilih Merek</option>
                                    @foreach($brands as $b) <option value="{{$b->id}}">{{$b->name}}</option> @endforeach
                                </select>
                            </div>
                            @endif

                            <div class="{{ $mode == 'jasa' ? 'col-md-12' : 'col-md-6' }}">
                                <label class="form-label small fw-bold text-danger">Tipe / Nama Produk *</label>
                                <input type="text" wire:model="product_name" list="types" class="form-control rounded-3" placeholder="Contoh: iPhone 15 Pro Max">
                                <datalist id="types">
                                    @foreach($existing_types as $t) <option value="{{$t}}"> @endforeach
                                </datalist>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label small fw-bold text-danger">Kategori *</label>
                                <select wire:model="category" class="form-select rounded-3 bg-light" disabled>
                                    <option>Handphone</option>
                                    <option>Aksesoris</option>
                                    <option>Jasa</option>
                                </select>
                            </div>

                            {{-- Spesifikasi Khusus IMEI --}}
                            @if($mode == 'imei')
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-danger">RAM *</label>
                                    <select wire:model="ram" class="form-select rounded-3">
                                        <option value="">Pilih RAM</option>
                                        <option>4GB</option><option>6GB</option><option>8GB</option><option>12GB</option><option>16GB</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-danger">Internal / Storage *</label>
                                    <select wire:model="storage" class="form-select rounded-3">
                                        <option value="">Pilih Storage</option>
                                        <option>64GB</option><option>128GB</option><option>256GB</option><option>512GB</option><option>1TB</option>
                                    </select>
                                </div>
                            @endif

                            {{-- Warna & Kondisi --}}
                            @if($mode != 'jasa')
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-danger">Warna *</label>
                                <input type="text" wire:model="color" class="form-control rounded-3" placeholder="Contoh: Deep Purple">
                            </div>
                            @endif

                            @if($mode == 'imei')
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-danger">Kondisi *</label>
                                <select wire:model="condition" class="form-select rounded-3">
                                    <option>Baru (New)</option>
                                    <option>Second (Bekas)</option>
                                    <option>BNOB</option>
                                </select>
                            </div>
                            @endif

                            <div class="col-12"><hr class="my-2 border-light"></div>

                            {{-- Harga --}}
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-danger">Harga Modal (Rp) *</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">Rp</span>
                                    <input type="number" wire:model="cost_price" class="form-control border-start-0 ps-0" placeholder="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-danger">Harga Jual (SRP) *</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">Rp</span>
                                    <input type="number" wire:model="srp_price" class="form-control border-start-0 ps-0" placeholder="0">
                                </div>
                            </div>

                            {{-- Input IMEI / Stok --}}
                            <div class="col-12 mt-4">
                                @if($mode == 'imei')
                                    <label class="form-label small fw-bold">Input IMEI (Untuk Stok Awal) <i class="fas fa-question-circle text-muted"></i></label>
                                    <textarea wire:model="imei_input" class="form-control rounded-3" rows="5" placeholder="Scan atau ketik IMEI di sini...&#10;8694000...&#10;8694001..."></textarea>
                                    <div class="form-text small">Pisahkan dengan baris baru (Enter). Jumlah baris otomatis menjadi stok.</div>
                                @elseif($mode == 'non-imei')
                                    <label class="form-label small fw-bold text-danger">Stok Awal *</label>
                                    <input type="number" wire:model="manual_stock" class="form-control rounded-3" placeholder="0">
                                @endif
                            </div>

                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                            <a href="{{ route('product.index') }}" class="btn btn-light rounded-pill px-4">Batal</a>
                            <button type="submit" class="btn btn-dark rounded-pill px-4">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>