<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Daftar Inventori</h4>
            <p class="text-secondary small mb-0">Kelola jenis barang, brand, stok, dan harga SRP.</p>
        </div>
        <a href="{{ route('product.create') }}" class="btn btn-black text-white px-4 rounded-3 shadow-sm" style="background: black;">
            <i class="fas fa-plus me-2"></i> Tambah Produk
        </a>
    </div>

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
                            <th class="py-3 text-uppercase text-secondary small fw-bold">Varian (RAM/GB) & Stok</th>
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
                                    <small class="text-muted">ID: #PRD-{{ str_pad($p->id, 4, '0', STR_PAD_LEFT) }}</small>
                                </td>
                                <td>
                                    <span class="badge rounded-pill bg-dark bg-opacity-10 text-dark border mb-1">
                                        {{ $p->brand->name }}
                                    </span>
                                    <br>
                                    <small class="text-secondary"><i class="fas fa-tag me-1"></i> {{ $p->category->name }}</small>
                                </td>
                                <td>
                                    @foreach($p->variants as $v)
                                        <div class="d-flex justify-content-between border-bottom border-light py-1" style="min-width: 150px;">
                                            <span class="text-secondary">{{ $v->attribute_name }}</span>
                                            <span class="fw-bold {{ $v->stock <= 5 ? 'text-danger' : 'text-dark' }}">
                                                {{ $v->stock }} <small class="fw-normal text-muted">Unit</small>
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
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-light border rounded-3 me-1" title="Edit">
                                            <i class="fas fa-edit text-secondary"></i>
                                        </button>
                                        <button class="btn btn-sm btn-light border rounded-3" title="Hapus" onclick="confirm('Yakin ingin menghapus?') || event.stopImmediatePropagation()" wire:click="deleteProduct({{ $p->id }})">
                                            <i class="fas fa-trash text-danger"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <img src="https://cdn-icons-png.flaticon.com/512/4076/4076432.png" width="80" class="mb-3 opacity-25" alt="Empty">
                                    <p class="text-secondary">Belum ada data produk tersedia.</p>
                                    <a href="{{ route('product.create') }}" class="btn btn-sm btn-outline-dark">Tambah Sekarang</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .btn-black:hover {
        background: #333 !important;
        transform: translateY(-1px);
        transition: all 0.2s;
    }
    .table thead th {
        border-bottom: 1px solid #edf2f7;
    }
    .table tbody tr:last-child td {
        border-bottom: 0;
    }
    .fw-600 { font-weight: 600; }
</style>