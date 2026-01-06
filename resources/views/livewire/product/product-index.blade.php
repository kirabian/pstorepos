<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white fw-bold">Tambah Produk Baru</div>
                <div class="card-body">
                    <form wire:submit.prevent="saveProduct">
                        <div class="mb-3">
                            <label>Nama Produk</label>
                            <input type="text" wire:model="name" class="form-control" placeholder="iPhone 15 Pro">
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label>Jenis (Category)</label>
                                <select wire:model="category_id" class="form-select">
                                    <option value="">Pilih</option>
                                    @foreach($categories as $cat) <option value="{{$cat->id}}">{{$cat->name}}</option> @endforeach
                                </select>
                            </div>
                            <div class="col-6 mb-3">
                                <label>Brand</label>
                                <select wire:model="brand_id" class="form-select">
                                    <option value="">Pilih</option>
                                    @foreach($brands as $br) <option value="{{$br->id}}">{{$br->name}}</option> @endforeach
                                </select>
                            </div>
                        </div>

                        <hr>
                        <label class="fw-bold mb-2">Varian (RAM/Stok/Harga)</label>
                        @foreach($variants as $index => $variant)
                            <div class="p-2 border rounded mb-2 bg-light">
                                <input type="text" wire:model="variants.{{$index}}.attribute" class="form-control form-control-sm mb-1" placeholder="Spek (Contoh: 8/256GB)">
                                <div class="row g-1">
                                    <div class="col-4"><input type="number" wire:model="variants.{{$index}}.stock" class="form-control form-control-sm" placeholder="Stok"></div>
                                    <div class="col-4"><input type="number" wire:model="variants.{{$index}}.cost" class="form-control form-control-sm" placeholder="Modal"></div>
                                    <div class="col-4"><input type="number" wire:model="variants.{{$index}}.srp" class="form-control form-control-sm" placeholder="SRP"></div>
                                </div>
                            </div>
                        @endforeach
                        <button type="button" wire:click="addVariant" class="btn btn-sm btn-outline-primary w-100 mt-2">+ Tambah Varian</button>

                        <button type="submit" class="btn btn-black w-100 mt-4 text-white" style="background: black;">Simpan Produk</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Brand/Jenis</th>
                                <th>Varian & Stok</th>
                                <th>Harga SRP</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $p)
                            <tr>
                                <td><strong>{{ $p->name }}</strong></td>
                                <td><span class="badge bg-secondary">{{ $p->brand->name }}</span><br><small>{{ $p->category->name }}</small></td>
                                <td>
                                    @foreach($p->variants as $v)
                                        <div class="small border-bottom mb-1">{{ $v->attribute_name }}: <strong>{{ $v->stock }}</strong></div>
                                    @endforeach
                                </td>
                                <td>
                                    @foreach($p->variants as $v)
                                        <div class="small border-bottom mb-1">Rp {{ number_format($v->srp_price) }}</div>
                                    @endforeach
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>