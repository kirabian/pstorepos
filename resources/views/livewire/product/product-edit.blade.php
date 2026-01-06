<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white p-4 border-bottom-0 pb-0">
                    <h5 class="mb-1 fw-bold">Edit Produk</h5>
                    <p class="text-muted small">Perbarui informasi produk dan stok.</p>
                </div>
                
                <div class="card-body p-4 pt-2">
                    <form wire:submit.prevent="update">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Merek / Brand</label>
                                <select wire:model.live="brand_id" class="form-select rounded-3">
                                    <option value="">Tanpa Brand (Jasa)</option>
                                    @foreach($brands as $br)
                                        <option value="{{$br->id}}">{{$br->name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Nama Produk <span class="text-danger">*</span></label>
                                <div class="position-relative">
                                    <input type="text" 
                                           wire:model="name" 
                                           class="form-control rounded-3" 
                                           list="product-list-edit" 
                                           placeholder="Nama Produk...">
                                    
                                    <datalist id="product-list-edit">
                                        @foreach($existing_types as $type)
                                            <option value="{{ $type }}">
                                        @endforeach
                                    </datalist>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold small text-muted">Kategori <span class="text-danger">*</span></label>
                                <select wire:model="category_id" class="form-select rounded-3 bg-light">
                                    <option value="">Pilih Kategori</option>
                                    @foreach($categories as $cat)
                                        <option value="{{$cat->id}}">{{$cat->name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 mt-4"><h6 class="fw-bold border-bottom pb-2">Detail Stok & Harga</h6></div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold small text-muted">Nama Varian / Spesifikasi</label>
                                <input type="text" wire:model="attribute_name" class="form-control rounded-3" placeholder="Contoh: 8/128GB Black">
                                <div class="form-text small">Ubah nama varian jika ada kesalahan spesifikasi.</div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-muted">Sisa Stok</label>
                                <input type="number" wire:model="stock" class="form-control rounded-3">
                                <div class="form-text small text-danger">Mengubah ini berarti melakukan Stock Opname manual.</div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-muted">Harga Modal (Rp)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">Rp</span>
                                    <input type="number" wire:model="cost_price" class="form-control border-start-0 ps-0">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-muted">Harga Jual (SRP)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">Rp</span>
                                    <input type="number" wire:model="srp_price" class="form-control border-start-0 ps-0">
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                            <a href="{{ route('product.index') }}" class="btn btn-light rounded-3 px-4">Batal</a>
                            <button type="submit" class="btn btn-dark px-4 rounded-3 shadow-sm" style="background: black;">
                                <i class="fas fa-save me-2"></i> Update Produk
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>