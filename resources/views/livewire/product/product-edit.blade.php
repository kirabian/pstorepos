<div class="container-fluid py-3 py-md-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow rounded-4">
                <div class="card-header bg-white p-4 border-bottom-0 pb-0">
                    <h5 class="mb-1 fw-bold">Edit & Update Stok</h5>
                    <p class="text-muted small">Update informasi produk dan sesuaikan stok/harga.</p>
                </div>
                
                <div class="card-body p-4 pt-2">
                    <form wire:submit.prevent="update">
                        <div class="row g-3">
                            
                            {{-- INFO PRODUK --}}
                            <div class="col-12 mb-2">
                                <h6 class="fw-bold text-primary border-bottom pb-2">1. Informasi Produk</h6>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label fw-bold small text-secondary">Merek</label>
                                <select wire:model.live="brand_id" class="form-select rounded-3">
                                    <option value="">Tanpa Brand</option>
                                    @foreach($brands as $br) <option value="{{$br->id}}">{{$br->name}}</option> @endforeach
                                </select>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label fw-bold small text-secondary">Nama Produk</label>
                                <div class="position-relative">
                                    <input type="text" wire:model="name" class="form-control rounded-3" list="product-list-edit">
                                    <datalist id="product-list-edit">
                                        @foreach($existing_types as $type) <option value="{{ $type }}"> @endforeach
                                    </datalist>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold small text-secondary">Kategori</label>
                                <select wire:model="category_id" class="form-select rounded-3 bg-light">
                                    @foreach($categories as $cat) <option value="{{$cat->id}}">{{$cat->name}}</option> @endforeach
                                </select>
                            </div>

                            {{-- STOK & HARGA --}}
                            <div class="col-12 mt-4 mb-2">
                                <h6 class="fw-bold text-primary border-bottom pb-2">2. Stok & Harga (Varian)</h6>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold small text-secondary">Nama Varian / Spek</label>
                                <input type="text" wire:model="attribute_name" class="form-control rounded-3" placeholder="Contoh: 8/128GB Black">
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label fw-bold small text-secondary">Total Stok Saat Ini</label>
                                <input type="number" wire:model="stock" class="form-control form-control-lg fw-bold rounded-3 text-center border-primary">
                                <div class="form-text small text-danger"><i class="fas fa-exclamation-circle"></i> Mengubah angka ini akan menimpa stok lama.</div>
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label fw-bold small text-secondary">Harga Modal</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">Rp</span>
                                    <input type="number" wire:model="cost_price" class="form-control border-start-0 ps-0">
                                </div>
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label fw-bold small text-secondary">Harga Jual (SRP)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">Rp</span>
                                    <input type="number" wire:model="srp_price" class="form-control border-start-0 ps-0">
                                </div>
                            </div>
                        </div>

                        <div class="mt-5 d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('product.index') }}" class="btn btn-light rounded-3 px-4 py-2">Batal</a>
                            <button type="submit" class="btn btn-primary rounded-3 px-5 py-2 shadow-sm">
                                <i class="fas fa-save me-2"></i> Update Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>