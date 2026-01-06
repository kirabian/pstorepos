<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">📦 Manajemen Produk</h4>
            <p class="text-muted small">Kelola data produk, stok, dan harga.</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-danger btn-sm rounded-3">
                <i class="fas fa-trash me-1"></i> Hapus Semua
            </button>
            <button class="btn btn-outline-success btn-sm rounded-3">
                <i class="fas fa-file-excel me-1"></i> Import
            </button>
            <a href="{{ route('product.create') }}" class="btn btn-dark btn-sm rounded-3">
                <i class="fas fa-plus me-1"></i> Tambah Manual
            </a>
        </div>
    </div>

    {{-- Search Bar --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-2">
            <div class="input-group">
                <span class="input-group-text bg-white border-0"><i class="fas fa-search text-muted"></i></span>
                <input type="text" wire:model.live="search" class="form-control border-0" placeholder="Cari Brand atau Produk...">
            </div>
        </div>
    </div>

    {{-- Table List --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white py-3">
            <h6 class="mb-0 fw-bold">Daftar Produk ({{ $products->total() }})</h6>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="bg-light text-secondary small text-uppercase">
                    <tr>
                        <th class="ps-4" width="20%">Merek / Brand</th>
                        <th width="25%">Nama Tipe / Produk</th>
                        <th width="30%">Varian & Stok</th>
                        <th width="15%">Modal / SRP</th>
                        <th class="text-end pe-4" width="10%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $p)
                        <tr class="border-bottom">
                            <td class="ps-4 align-top py-3">
                                <span class="badge bg-dark rounded-pill mb-1">{{ $p->brand->name }}</span>
                                <div class="small text-muted"><i class="fas fa-folder me-1"></i> {{ $p->category }}</div>
                            </td>
                            <td class="align-top py-3">
                                <div class="fw-bold text-dark fs-6">{{ $p->name }}</div>
                                <div class="small text-muted">ID: #{{ $p->id }}</div>
                            </td>
                            <td class="py-3">
                                @foreach($p->variants as $v)
                                    <div class="card bg-light border-0 mb-2 rounded-3">
                                        <div class="card-body p-2 d-flex justify-content-between align-items-center">
                                            <div>
                                                <div class="fw-bold text-dark" style="font-size: 0.85rem;">
                                                    <i class="fas fa-mobile-alt me-1 text-secondary"></i> {{ $v->full_name }}
                                                </div>
                                                
                                                {{-- Dropdown IMEI --}}
                                                @if($v->imeis->count() > 0)
                                                <div class="dropdown mt-1">
                                                    <a class="text-decoration-none small text-primary bg-white border px-2 py-0 rounded-pill" href="#" role="button" data-bs-toggle="dropdown">
                                                        <i class="fas fa-barcode me-1"></i> Lihat {{ $v->imeis->count() }} IMEI
                                                    </a>
                                                    <ul class="dropdown-menu shadow-sm border-0 p-2" style="max-height: 200px; overflow-y: auto;">
                                                        @foreach($v->imeis as $imei)
                                                            <li class="dropdown-item small py-1 d-flex justify-content-between">
                                                                <span class="font-monospace">{{ $imei->imei }}</span>
                                                                <span class="badge bg-success-subtle text-success" style="font-size: 0.6em;">Ready</span>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                                @endif
                                            </div>
                                            <div class="text-end">
                                                @if($v->stock > 0)
                                                    <span class="badge bg-success-subtle text-success rounded-pill">{{ $v->stock }} Unit</span>
                                                @else
                                                    <span class="badge bg-danger-subtle text-danger rounded-pill">Habis</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </td>
                            <td class="align-top py-3">
                                @foreach($p->variants as $v)
                                    <div class="mb-3 small">
                                        <div class="text-muted">Rp {{ number_format($v->cost_price, 0, ',', '.') }}</div>
                                        <div class="fw-bold text-primary">Rp {{ number_format($v->srp_price, 0, ',', '.') }}</div>
                                    </div>
                                @endforeach
                            </td>
                            <td class="text-end pe-4 align-top py-3">
                                <button class="btn btn-sm btn-light text-danger" wire:click="deleteProduct({{ $p->id }})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-box-open fa-3x mb-3 opacity-25"></i>
                                <p>Belum ada data produk.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">
            {{ $products->links() }}
        </div>
    </div>
</div>