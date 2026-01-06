<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white p-3">
                    <h5 class="mb-0 fw-bold">Tambah Produk & Varian Stok</h5>
                </div>
                <div class="card-body p-4">
                    <form wire:submit.prevent="save">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-600">Nama Produk</label>
                                <input type="text" wire:model="name" class="form-control" placeholder="Contoh: iPhone 15 Pro">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-600">Kategori (Jenis)</label>
                                <select wire:model="category_id" class="form-select">
                                    <option value="">Pilih Kategori</option>
                                    @foreach($categories as $cat) <option value="{{$cat->id}}">{{$cat->name}}</option> @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-600">Brand</label>
                                <select wire:model="brand_id" class="form-select">
                                    <option value="">Pilih Brand</option>
                                    @foreach($brands as $br) <option value="{{$br->id}}">{{$br->name}}</option> @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mt-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 1px;">Detail Varian (RAM/GB, Stok & Harga)</h6>
                                <button type="button" wire:click="addVariant" class="btn btn-sm btn-primary">+ Tambah Baris</button>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="bg-light">
                                        <tr style="font-size: 0.85rem;">
                                            <th>Spek (RAM/GB/Warna)</th>
                                            <th width="150">Stok Awal</th>
                                            <th width="200">Modal Bawaan</th>
                                            <th width="200">Harga SRP</th>
                                            <th width="50"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($variants as $index => $variant)
                                        <tr>
                                            <td><input type="text" wire:model="variants.{{$index}}.attribute" class="form-control form-control-sm" placeholder="8/256GB Black"></td>
                                            <td><input type="number" wire:model="variants.{{$index}}.stock" class="form-control form-control-sm"></td>
                                            <td><input type="number" wire:model="variants.{{$index}}.cost" class="form-control form-control-sm"></td>
                                            <td><input type="number" wire:model="variants.{{$index}}.srp" class="form-control form-control-sm"></td>
                                            <td>
                                                @if(count($variants) > 1)
                                                <button type="button" wire:click="removeVariant({{ $index }})" class="btn btn-sm btn-outline-danger border-0"><i class="fas fa-trash"></i></button>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top d-flex justify-content-end">
                            <a href="{{ route('product.index') }}" class="btn btn-light me-2">Batal</a>
                            <button type="submit" class="btn btn-dark px-4 shadow">Simpan Produk</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>