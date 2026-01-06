<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">📱 Manajemen Produk</h4>
            <p class="text-muted small">Import tipe produk berdasarkan data dari Excel/CSV.</p>
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
        <div class="alert alert-success border-0 shadow-sm mb-4 small d-flex align-items-center">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        </div>
    @endif
    @if (session()->has('warning'))
        <div class="alert alert-warning border-0 shadow-sm mb-4 small d-flex align-items-center">
            <i class="fas fa-exclamation-triangle me-2"></i> {{ session('warning') }}
        </div>
    @endif
    @if (session()->has('info'))
        <div class="alert alert-info border-0 shadow-sm mb-4 small d-flex align-items-center">
            <i class="fas fa-info-circle me-2"></i> {{ session('info') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger border-0 shadow-sm mb-4 small d-flex align-items-center">
            <i class="fas fa-times-circle me-2"></i> {{ session('error') }}
        </div>
    @endif

    @if(!empty($previewData))
    <div class="card border-0 shadow-lg mb-5 rounded-4 border-top border-4 border-primary">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom-0">
            <div>
                <h6 class="mb-0 fw-bold"><i class="fas fa-tasks me-2 text-primary"></i> Pratinjau Import</h6>
                <small class="text-muted">
                    Total: {{ count($previewData) }} data | 
                    Valid: {{ count(array_filter($previewData, fn($item) => $item['is_valid'])) }} | 
                    Invalid: {{ count(array_filter($previewData, fn($item) => !$item['is_valid'])) }}
                </small>
            </div>
            <div>
                <button wire:click="cancelImport" class="btn btn-sm btn-light border me-1 px-3 rounded-3">
                    <i class="fas fa-times me-1"></i> Batal
                </button>
                <button wire:click="processImport" class="btn btn-sm btn-primary fw-bold px-4 shadow-sm rounded-3">
                    <i class="fas fa-save me-1"></i> Simpan Sekarang
                </button>
            </div>
        </div>
        <div class="card-body p-0" style="max-height: 400px; overflow-y: auto;">
            <table class="table table-sm table-hover mb-0">
                <thead class="bg-light sticky-top">
                    <tr class="small text-uppercase">
                        <th class="ps-4 py-2">Merek</th>
                        <th class="py-2">Nama Tipe Produk</th>
                        <th class="py-2">Spesifikasi</th>
                        <th class="py-2 text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($previewData as $index => $item)
                    <tr class="{{ $item['is_duplicate'] ? 'table-warning' : '' }}">
                        <td class="ps-4 py-2">
                            <div class="fw-bold text-dark">{{ $item['brand_name'] }}</div>
                            @if($item['brand_system_name'] && $item['brand_system_name'] !== $item['brand_name'])
                                <small class="text-muted">Sistem: {{ $item['brand_system_name'] }}</small>
                            @endif
                        </td>
                        <td class="py-2">
                            <div class="text-primary fw-bold">{{ $item['product_name'] }}</div>
                            @if($item['is_duplicate'])
                                <small class="text-danger">
                                    <i class="fas fa-exclamation-circle"></i> Duplikat: {{ $item['existing_product'] }}
                                </small>
                            @endif
                        </td>
                        <td class="py-2 small text-muted">
                            {{ $item['ram_storage'] ?: '-' }}
                        </td>
                        <td class="py-2 text-center">
                            @if($item['is_valid'] && !$item['is_duplicate'])
                                <span class="badge bg-success-subtle text-success px-2 border border-success border-opacity-25 rounded-pill">
                                    <i class="fas fa-check me-1"></i> Valid
                                </span>
                            @elseif($item['is_duplicate'])
                                <span class="badge bg-warning-subtle text-warning px-2 border border-warning border-opacity-25 rounded-pill">
                                    <i class="fas fa-copy me-1"></i> Duplikat
                                </span>
                            @else
                                <span class="badge bg-danger-subtle text-danger px-2 border border-danger border-opacity-25 rounded-pill">
                                    <i class="fas fa-times me-1"></i> ID Salah
                                </span>
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
        <div class="card-header bg-white py-3 border-bottom">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="fas fa-boxes me-2"></i> Daftar Produk</h6>
                <div class="text-muted small">
                    Total: {{ $products->count() }} produk
                </div>
            </div>
        </div>
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
                                    @if($p->description)
                                        <small class="text-muted" style="font-size: 0.65rem;">
                                            <i class="fas fa-info-circle"></i> {{ Str::limit($p->description, 50) }}
                                        </small>
                                    @endif
                                    <small class="text-muted d-block" style="font-size: 0.65rem;">ID: #{{ $p->id }}</small>
                                </td>
                                <td>
                                    <span class="badge rounded-pill bg-light text-dark border px-2 mb-1" style="font-size: 0.7rem;">
                                        <i class="fas fa-tag me-1"></i> {{ $p->brand->name ?? 'N/A' }}
                                    </span><br>
                                    <small class="text-secondary" style="font-size: 0.75rem;">
                                        <i class="fas fa-folder me-1"></i> {{ $p->category->name ?? 'N/A' }}
                                    </small>
                                </td>
                                <td>
                                    @foreach($p->variants as $v)
                                        <div class="d-flex justify-content-between py-1 small">
                                            <span class="text-secondary">
                                                <i class="fas fa-cube me-1"></i> {{ $v->attribute_name }}
                                            </span>
                                            <span class="fw-bold ms-2">
                                                @if($v->stock > 0)
                                                    <span class="text-success">{{ $v->stock }}</span>
                                                @else
                                                    <span class="text-danger">{{ $v->stock }}</span>
                                                @endif
                                            </span>
                                        </div>
                                    @endforeach
                                </td>
                                <td>
                                    @foreach($p->variants as $v)
                                        <div class="py-1 small text-muted">
                                            Rp {{ number_format($v->cost_price, 0, ',', '.') }}
                                        </div>
                                    @endforeach
                                </td>
                                <td>
                                    @foreach($p->variants as $v)
                                        <div class="py-1 small fw-bold text-primary">
                                            Rp {{ number_format($v->srp_price, 0, ',', '.') }}
                                        </div>
                                    @endforeach
                                </td>
                                <td class="pe-4 text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="{{ route('product.edit', $p->id) }}" class="btn btn-sm btn-outline-primary border-0 rounded-3" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="btn btn-sm btn-outline-danger border-0 rounded-3" 
                                                onclick="return confirm('Hapus produk {{ $p->name }}?')" 
                                                wire:click="deleteProduct({{ $p->id }})"
                                                title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-secondary">
                                    <div class="py-4">
                                        <i class="fas fa-box-open fa-2x mb-3 text-muted"></i>
                                        <p class="mb-0">Belum ada data produk.</p>
                                        <small class="text-muted">Mulai dengan import data atau tambah manual.</small>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>