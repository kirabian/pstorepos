<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Manajemen Produk</h4>
            <p class="text-muted small">Import tipe produk berdasarkan ID Merek dari Excel/CSV.</p>
        </div>
        <div class="d-flex gap-2">
            <div class="position-relative">
                <input type="file" wire:model="file_import" id="fileImport" class="d-none" accept=".csv, .xlsx, .xls">
                <label for="fileImport" class="btn btn-outline-success btn-sm px-3 rounded-3 shadow-sm" style="cursor: pointer;">
                    <i class="fas fa-file-excel me-2"></i> Import Produk
                </label>
            </div>
            <a href="{{ route('product.create') }}" class="btn btn-black btn-sm text-white px-3 rounded-3 shadow-sm" style="background: black;">
                <i class="fas fa-plus me-2"></i> Tambah Manual
            </a>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4 small">{{ session('success') }}</div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger border-0 shadow-sm mb-4 small">{{ session('error') }}</div>
    @endif

    @if(!empty($previewData))
    <div class="card border-0 shadow-lg mb-5 rounded-4 border-top border-4 border-primary">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom-0">
            <h6 class="mb-0 fw-bold"><i class="fas fa-tasks me-2 text-primary"></i> Pratinjau Import (Nama Tipe & ID Brand)</h6>
            <div>
                <button wire:click="cancelImport" class="btn btn-sm btn-light border me-1 px-3">Batal</button>
                <button wire:click="processImport" class="btn btn-sm btn-primary fw-bold px-4 shadow-sm">
                    Simpan Sekarang
                </button>
            </div>
        </div>
        <div class="card-body p-0" style="max-height: 400px; overflow-y: auto;">
            <table class="table table-sm table-hover mb-0">
                <thead class="bg-light sticky-top">
                    <tr class="small text-uppercase">
                        <th class="ps-4 py-2">ID Merek (Excel)</th>
                        <th class="py-2">Merek Sistem</th>
                        <th class="py-2">Nama Tipe (Produk)</th>
                        <th class="py-2 text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($previewData as $item)
                    <tr>
                        <td class="ps-4 py-2 small text-muted font-monospace">{{ $item['brand_id'] }}</td>
                        <td class="py-2 small fw-bold {{ !$item['is_valid'] ? 'text-danger' : 'text-dark' }}">
                            {{ $item['brand_name'] }}
                        </td>
                        <td class="py-2 small text-primary fw-bold">{{ $item['product_name'] }}</td>
                        <td class="py-2 text-center small">
                            @if($item['is_valid'])
                                <span class="badge bg-success-subtle text-success px-2 border border-success border-opacity-25">Siap Simpan</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger px-2 border border-danger border-opacity-25">ID Tidak Terdaftar</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr style="font-size: 0.75rem;">
                            <th class="ps-4 py-3 text-uppercase text-secondary fw-bold">Nama Tipe / Produk</th>
                            <th class="py-3 text-uppercase text-secondary fw-bold">Brand & Kategori</th>
                            <th class="py-3 text-uppercase text-secondary fw-bold">Varian & Stok</th>
                            <th class="py-3 text-uppercase text-secondary fw-bold">Modal</th>
                            <th class="py-3 text-uppercase text-secondary fw-bold">Harga SRP</th>
                            <th class="pe-4 py-3 text-center text-uppercase text-secondary fw-bold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $p)
                            <tr class="border-bottom">
                                <td class="ps-4">
                                    <div class="fw-bold text-dark">{{ $p->name }}</div>
                                    <small class="text-muted" style="font-size: 0.65rem;">ID SKU: #{{ $p->id }}</small>
                                </td>
                                <td>
                                    <span class="badge rounded-pill bg-light text-dark border px-2 mb-1">{{ $p->brand->name }}</span><br>
                                    <small class="text-secondary" style="font-size: 0.75rem;">{{ $p->category->name }}</small>
                                </td>
                                <td>
                                    @foreach($p->variants as $v)
                                        <div class="d-flex justify-content-between py-1 small">
                                            <span class="text-secondary">{{ $v->attribute_name }}</span>
                                            <span class="fw-bold ms-2">{{ $v->stock }}</span>
                                        </div>
                                    @endforeach
                                </td>
                                <td>
                                    @foreach($p->variants as $v)
                                        <div class="py-1 small text-muted">Rp {{ number_format($v->cost_price, 0, ',', '.') }}</div>
                                    @endforeach
                                </td>
                                <td>
                                    @foreach($p->variants as $v)
                                        <div class="py-1 small fw-bold text-primary">Rp {{ number_format($v->srp_price, 0, ',', '.') }}</div>
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
                                <td colspan="6" class="text-center py-5 text-secondary">
                                    Belum ada data produk tersedia.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>