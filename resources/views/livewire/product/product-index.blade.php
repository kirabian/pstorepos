<div class="container-fluid py-4">
    {{-- 
        PERBAIKAN: Tag <div class="container-fluid"> ini adalah ROOT ELEMENT tunggal.
        Semua konten, termasuk <style>, script, dan modal harus ada di DALAM div ini.
    --}}

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">📱 Manajemen Produk</h4>
            <p class="text-muted small">Import tipe produk berdasarkan data dari Excel/CSV.</p>
        </div>
        <div class="d-flex gap-2">
            @if($products->total() > 0)
            <button wire:click="deleteAll" 
                    onclick="return confirm('⚠️ PERINGATAN KERAS!\n\nApakah Anda yakin ingin MENGHAPUS SEMUA data produk?\nData yang dihapus tidak dapat dikembalikan!')"
                    class="btn btn-outline-danger btn-sm px-3 rounded-3 shadow-sm">
                <i class="fas fa-trash-alt me-2"></i> Hapus Semua Data
            </button>
            @endif

            <div class="position-relative">
                <input type="file" wire:model="file_import" id="fileImport" class="d-none" accept=".csv, .xlsx, .xls">
                <label for="fileImport" class="btn btn-outline-success btn-sm px-3 rounded-3 shadow-sm" style="cursor: pointer;">
                    <span wire:loading.remove wire:target="file_import">
                        <i class="fas fa-file-excel me-2"></i> Import Produk
                    </span>
                    <span wire:loading wire:target="file_import">
                        <i class="fas fa-spinner fa-spin me-2"></i> Memproses...
                    </span>
                </label>
            </div>
            <a href="{{ route('product.create') }}" class="btn btn-black btn-sm text-white px-3 rounded-3 shadow-sm" style="background: black;">
                <i class="fas fa-plus me-2"></i> Tambah Manual
            </a>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4 small alert-dismissible fade show">
            {!! session('success') !!}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session()->has('warning'))
        <div class="alert alert-warning border-0 shadow-sm mb-4 small d-flex align-items-center alert-dismissible fade show">
            <i class="fas fa-exclamation-triangle me-2"></i> {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session()->has('info'))
        <div class="alert alert-info border-0 shadow-sm mb-4 small d-flex align-items-center alert-dismissible fade show">
            <i class="fas fa-info-circle me-2"></i> {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger border-0 shadow-sm mb-4 small d-flex align-items-center alert-dismissible fade show">
            <i class="fas fa-times-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- AREA PREVIEW IMPORT --}}
    @if(!empty($previewData))
    <div class="card border-0 shadow-lg mb-5 rounded-4 border-top border-4 border-primary">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom-0">
            <div>
                <h6 class="mb-0 fw-bold"><i class="fas fa-tasks me-2 text-primary"></i> Pratinjau Import</h6>
                <small class="text-muted">
                    📊 Total: {{ count($previewData) }} data | 
                    ✅ Siap: {{ count($previewData) - count(array_filter($previewData, fn($item) => $item['is_duplicate'])) }} | 
                    ⚠️ Duplikat (Skip): {{ count(array_filter($previewData, fn($item) => $item['is_duplicate'])) }}
                </small>
            </div>
            <div>
                <button wire:click="cancelImport" class="btn btn-sm btn-light border me-1 px-3 rounded-3">
                    <i class="fas fa-times me-1"></i> Batal
                </button>
                <button wire:click="processImport" wire:loading.attr="disabled" class="btn btn-sm btn-primary fw-bold px-4 shadow-sm rounded-3">
                    <span wire:loading.remove wire:target="processImport">
                        <i class="fas fa-save me-1"></i> Simpan Sekarang
                    </span>
                    <span wire:loading wire:target="processImport">
                        <i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...
                    </span>
                </button>
            </div>
        </div>
        <div class="card-body p-0" style="max-height: 400px; overflow-y: auto;">
            <table class="table table-sm table-hover mb-0">
                <thead class="bg-light sticky-top">
                    <tr class="small text-uppercase">
                        <th class="ps-4 py-2">Merek (Brand)</th>
                        <th class="py-2">Nama Tipe Produk</th>
                        <th class="py-2">Spesifikasi</th>
                        <th class="py-2 text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($previewData as $index => $item)
                    <tr class="{{ $item['is_duplicate'] ? 'table-warning bg-opacity-10' : '' }}">
                        <td class="ps-4 py-2">
                            <div class="fw-bold text-dark">{{ $item['brand_name'] }}</div>
                            @if($item['brand_system_name'] && $item['brand_system_name'] !== $item['brand_name'])
                                <small class="text-success">
                                    <i class="fas fa-database me-1"></i> Sistem: {{ $item['brand_system_name'] }}
                                </small>
                            @elseif(!$item['brand_system_name'])
                                <small class="text-info">
                                    <i class="fas fa-plus-circle me-1"></i> Brand baru
                                </small>
                            @endif
                        </td>
                        <td class="py-2">
                            <div class="text-primary fw-bold">{{ $item['product_name'] }}</div>
                            @if($item['is_duplicate'])
                                <small class="text-danger fw-bold" style="font-size: 0.7rem;">
                                    <i class="fas fa-ban me-1"></i> SUDAH ADA (AKAN DI-SKIP)
                                </small>
                            @endif
                        </td>
                        <td class="py-2 small text-muted">
                            {{ $item['ram_storage'] ?: '-' }}
                        </td>
                        <td class="py-2 text-center">
                            @if(!$item['is_duplicate'])
                                <span class="badge bg-success-subtle text-success px-2 border border-success border-opacity-25 rounded-pill">
                                    <i class="fas fa-check me-1"></i> Siap
                                </span>
                            @else
                                <span class="badge bg-warning text-dark px-2 rounded-pill">
                                    <i class="fas fa-times me-1"></i> Skip
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

    {{-- AREA FILTER BRAND --}}
    @if($availableBrands->count() > 0)
    <div class="mb-4">
        <div class="d-flex align-items-center mb-2">
            <h6 class="text-uppercase text-secondary fw-bold small mb-0 me-2">
                <i class="fas fa-filter me-1"></i> Filter Brand:
            </h6>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button wire:click="setBrandFilter(null)" 
                    class="btn btn-sm rounded-pill px-3 shadow-sm border {{ $selectedBrandId === null ? 'btn-dark' : 'btn-white bg-white text-secondary' }}">
                Semua Brand
            </button>

            @foreach($availableBrands as $brand)
                <button wire:click="setBrandFilter({{ $brand->id }})" 
                        class="btn btn-sm rounded-pill px-3 shadow-sm border d-flex align-items-center gap-2 {{ $selectedBrandId === $brand->id ? 'btn-primary text-white border-primary' : 'btn-white bg-white text-dark hover-shadow' }}"
                        style="transition: all 0.2s;">
                    <span>{{ $brand->name }}</span>
                    <span class="badge {{ $selectedBrandId === $brand->id ? 'bg-white text-primary' : 'bg-light text-secondary' }} rounded-circle" style="font-size: 0.65rem;">
                        {{ $brand->products_count }}
                    </span>
                </button>
            @endforeach
        </div>
    </div>
    @endif

    {{-- AREA DAFTAR PRODUK --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white py-3 border-bottom">
            <div class="row g-2 align-items-center">
                <div class="col-md-6">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-boxes me-2"></i> Daftar Produk 
                        <span class="text-muted fw-normal">({{ $products->total() }})</span>
                        @if($selectedBrandId)
                            <span class="badge bg-primary-subtle text-primary ms-2">
                                Filter: {{ $availableBrands->find($selectedBrandId)->name }}
                                <i class="fas fa-times ms-1 cursor-pointer" wire:click="setBrandFilter(null)"></i>
                            </span>
                        @endif
                    </h6>
                </div>
                <div class="col-md-6 d-flex justify-content-end gap-2">
                    <div class="input-group input-group-sm" style="max-width: 250px;">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control border-start-0 ps-0" placeholder="Cari Tipe Produk...">
                    </div>
                    
                    <button wire:click="$refresh" class="btn btn-sm btn-light border" title="Refresh">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            @if($products->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr style="font-size: 0.75rem;">
                            <th class="ps-4 py-3 text-uppercase text-secondary fw-bold" style="width: 20%;">Merek / Brand</th>
                            <th class="py-3 text-uppercase text-secondary fw-bold" style="width: 25%;">Nama Tipe / Produk</th>
                            <th class="py-3 text-uppercase text-secondary fw-bold">Varian & Stok</th>
                            <th class="py-3 text-uppercase text-secondary fw-bold">Modal</th>
                            <th class="py-3 text-uppercase text-secondary fw-bold">Harga SRP</th>
                            <th class="pe-4 py-3 text-center text-uppercase text-secondary fw-bold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $p)
                            <tr class="border-bottom">
                                <td class="ps-4">
                                    <span class="badge rounded-pill bg-dark text-white px-3 py-2" style="font-size: 0.8rem;">
                                        {{ $p->brand->name ?? 'No Brand' }}
                                    </span>
                                    <div class="small text-muted mt-1 ps-1">
                                        <i class="fas fa-folder me-1"></i> {{ $p->category->name ?? 'Uncategorized' }}
                                    </div>
                                </td>

                                <td>
                                    <div class="fw-bold text-dark fs-6">{{ $p->name }}</div>
                                    @if($p->description)
                                        <small class="text-muted d-block mt-1" style="font-size: 0.7rem; line-height: 1.2;">
                                            <i class="fas fa-info-circle me-1"></i> {{ Str::limit($p->description, 60) }}
                                        </small>
                                    @endif
                                </td>

                                <td>
                                    @foreach($p->variants as $v)
                                        <div class="d-flex justify-content-between py-1 small border-bottom border-light">
                                            <span class="text-secondary">
                                                <i class="fas fa-cube me-1"></i> {{ $v->attribute_name }}
                                            </span>
                                            <span class="fw-bold ms-2">
                                                @if($v->stock > 0)
                                                    <span class="text-success bg-success-subtle px-2 rounded">{{ $v->stock }} Unit</span>
                                                @else
                                                    <span class="text-danger bg-danger-subtle px-2 rounded">Habis</span>
                                                @endif
                                            </span>
                                        </div>
                                    @endforeach
                                </td>

                                <td>
                                    @foreach($p->variants as $v)
                                        <div class="py-1 small text-muted border-bottom border-light">
                                            Rp {{ number_format($v->cost_price, 0, ',', '.') }}
                                        </div>
                                    @endforeach
                                </td>

                                <td>
                                    @foreach($p->variants as $v)
                                        <div class="py-1 small fw-bold text-primary border-bottom border-light">
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
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-between align-items-center px-4 py-3 bg-light border-top">
                <div class="small text-muted">
                    Menampilkan {{ $products->firstItem() }} - {{ $products->lastItem() }} dari {{ $products->total() }} data
                </div>
                <div>
                    {{ $products->links(data: ['scrollTo' => false]) }}
                </div>
            </div>

            @else
            <div class="text-center py-5 text-secondary">
                <div class="py-4">
                    <i class="fas fa-box-open fa-3x mb-3 text-muted opacity-50"></i>
                    <h6 class="mb-2">Data tidak ditemukan</h6>
                    <p class="small text-muted mb-4">
                        @if($selectedBrandId)
                            Tidak ada produk untuk brand yang dipilih.
                        @else
                            Belum ada data atau pencarian tidak cocok.
                        @endif
                    </p>
                    <div class="d-flex justify-content-center gap-2">
                        <label for="fileImport" class="btn btn-success btn-sm">
                            <i class="fas fa-file-excel me-1"></i> Import Excel
                        </label>
                        <a href="{{ route('product.create') }}" class="btn btn-dark btn-sm">
                            <i class="fas fa-plus me-1"></i> Tambah Manual
                        </a>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- 
        STYLE INI SEKARANG ADA DI DALAM ROOT ELEMENT
        Livewire tidak akan error lagi.
    --}}
    <style>
        .hover-shadow:hover {
            box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
            border-color: #dee2e6 !important;
        }
    </style>
</div>