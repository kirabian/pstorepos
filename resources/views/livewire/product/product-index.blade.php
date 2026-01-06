<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Daftar Inventori</h4>
            <p class="text-secondary small mb-0">Mendukung import file .csv dan .xlsx (Excel)</p>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <form wire:submit.prevent="importFile" class="d-flex gap-2 bg-white p-2 rounded-3 border shadow-sm">
                <input type="file" wire:model="file_import" class="form-control form-control-sm" accept=".csv, .xlsx, .xls" style="width: 220px;">
                <button type="submit" class="btn btn-sm btn-success px-3" wire:loading.attr="disabled">
                    <span wire:loading.remove><i class="fas fa-file-excel"></i> Import File</span>
                    <span wire:loading><i class="fas fa-spinner fa-spin"></i></span>
                </button>
            </form>
            
            <a href="{{ route('product.create') }}" class="btn btn-black text-white px-4 rounded-3 shadow-sm" style="background: black;">
                <i class="fas fa-plus me-2"></i> Tambah Manual
            </a>
        </div>
    </div>

    @if (session()->has('error'))
        <div class="alert alert-danger border-0 shadow-sm mb-4 small">{{ session('error') }}</div>
    @endif

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
                        <tr style="font-size: 0.75rem;">
                            <th class="ps-4 py-3 text-uppercase text-secondary fw-bold">Nama Produk</th>
                            <th class="py-3 text-uppercase text-secondary fw-bold">Brand/Jenis</th>
                            <th class="py-3 text-uppercase text-secondary fw-bold">Detail Varian & Stok</th>
                            <th class="py-3 text-uppercase text-secondary fw-bold">Modal</th>
                            <th class="py-3 text-uppercase text-secondary fw-bold">Harga SRP</th>
                            <th class="pe-4 py-3 text-center text-uppercase text-secondary fw-bold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $p)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark">{{ $p->name }}</div>
                                    <small class="text-muted small">ID: #{{ $p->id }}</small>
                                </td>
                                <td>
                                    <span class="badge rounded-pill bg-light text-dark border px-3 mb-1">
                                        {{ $p->brand->name }}
                                    </span>
                                    <br>
                                    <small class="text-muted">{{ $p->category->name }}</small>
                                </td>
                                <td>
                                    @foreach($p->variants as $v)
                                        <div class="d-flex justify-content-between border-bottom border-light py-1">
                                            <span class="text-secondary small">{{ $v->attribute_name }}</span>
                                            <span class="fw-bold {{ $v->stock <= 3 ? 'text-danger' : 'text-success' }}">
                                                {{ $v->stock }}
                                            </span>
                                        </div>
                                    @endforeach
                                </td>
                                <td>
                                    @foreach($p->variants as $v)
                                        <div class="py-1 border-bottom border-light text-muted small">
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
                                    <button class="btn btn-sm btn-outline-danger border-0" onclick="confirm('Hapus produk?') || event.stopImmediatePropagation()" wire:click="deleteProduct({{ $p->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-secondary">Data tidak ditemukan. Silakan import file Excel atau CSV.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>