<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Daftar Inventori</h4>
            <p class="text-secondary small mb-0">Kelola jenis barang, brand, stok, dan harga SRP.</p>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <form wire:submit.prevent="importCsv" class="d-flex gap-2 bg-white p-2 rounded-3 border shadow-sm">
                <input type="file" wire:model="file_csv" class="form-control form-control-sm" accept=".csv" style="width: 200px;">
                <button type="submit" class="btn btn-sm btn-success" wire:loading.attr="disabled">
                    <span wire:loading.remove><i class="fas fa-file-import"></i> Import</span>
                    <span wire:loading><i class="fas fa-spinner fa-spin"></i></span>
                </button>
            </form>
            
            <a href="{{ route('product.create') }}" class="btn btn-black text-white px-4 rounded-3 shadow-sm" style="background: black;">
                <i class="fas fa-plus me-2"></i> Tambah Produk
            </a>
        </div>
    </div>

    @error('file_csv') <div class="alert alert-danger py-2 small">{{ $message }}</div> @enderror

    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 text-uppercase text-secondary small fw-bold">Produk</th>
                            <th class="py-3 text-uppercase text-secondary small fw-bold">Brand & Kategori</th>
                            <th class="py-3 text-uppercase text-secondary small fw-bold">Varian & Stok</th>
                            <th class="py-3 text-uppercase text-secondary small fw-bold">Modal Bawaan</th>
                            <th class="py-3 text-uppercase text-secondary small fw-bold">Harga SRP</th>
                            <th class="pe-4 py-3 text-center text-uppercase text-secondary small fw-bold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $p)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark">{{ $p->name }}</div>
                                    <small class="text-muted text-uppercase" style="font-size: 0.7rem;">ID: #{{ $p->id }}</small>
                                </td>
                                <td>
                                    <span class="badge rounded-pill bg-dark bg-opacity-10 text-dark border mb-1">
                                        {{ $p->brand->name }}
                                    </span>
                                    <br>
                                    <small class="text-secondary">{{ $p->category->name }}</small>
                                </td>
                                <td>
                                    @foreach($p->variants as $v)
                                        <div class="d-flex justify-content-between border-bottom border-light py-1">
                                            <span class="text-secondary small">{{ $v->attribute_name }}</span>
                                            <span class="fw-bold ms-3 {{ $v->stock <= 5 ? 'text-danger' : '' }}">
                                                {{ $v->stock }}
                                            </span>
                                        </div>
                                    @endforeach
                                </td>
                                <td>
                                    @foreach($p->variants as $v)
                                        <div class="py-1 border-bottom border-light text-secondary small">
                                            Rp {{ number_format($v->cost_price, 0, ',', '.') }}
                                        </div>
                                    @endforeach
                                </td>
                                <td>
                                    @foreach($p->variants as $v)
                                        <div class="py-1 border-bottom border-light fw-bold text-primary">
                                            Rp {{ number_format($v->srp_price, 0, ',', '.') }}
                                        </div>
                                    @endforeach
                                </td>
                                <td class="pe-4 text-center">
                                    <button class="btn btn-sm btn-light border rounded-3" onclick="confirm('Hapus produk ini?') || event.stopImmediatePropagation()" wire:click="deleteProduct({{ $p->id }})">
                                        <i class="fas fa-trash text-danger"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <p class="text-secondary">Belum ada data produk.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>