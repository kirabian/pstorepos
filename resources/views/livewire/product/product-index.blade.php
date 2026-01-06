<div class="container-fluid py-3 py-md-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold mb-0 text-dark"><i class="fas fa-box-open me-2 text-primary"></i>Manajemen Produk</h4>
            <p class="text-muted small mb-0">Kelola data produk, stok, dan harga.</p>
        </div>
        
        <div class="d-flex flex-column flex-md-row gap-2">
            @if($products->total() > 0)
            <button wire:click="deleteAll" 
                    onclick="return confirm('⚠️ BAHAYA!\n\nSemua data produk akan dihapus permanent.\nLanjutkan?')"
                    class="btn btn-outline-danger btn-sm rounded-3 w-100 w-md-auto">
                <i class="fas fa-trash-alt me-2"></i>Reset Data
            </button>
            @endif

            <div class="d-flex gap-2">
                <div class="position-relative w-100 w-md-auto">
                    <input type="file" wire:model="file_import" id="fileImport" class="d-none" accept=".csv, .xlsx, .xls">
                    <label for="fileImport" class="btn btn-success btn-sm w-100 rounded-3 shadow-sm" style="cursor: pointer;">
                        <span wire:loading.remove wire:target="file_import">
                            <i class="fas fa-file-excel me-2"></i>Import Excel
                        </span>
                        <span wire:loading wire:target="file_import">
                            <i class="fas fa-spinner fa-spin me-2"></i>Loading...
                        </span>
                    </label>
                </div>
                
                <a href="{{ route('product.create') }}" class="btn btn-dark btn-sm w-100 w-md-auto rounded-3 shadow-sm">
                    <i class="fas fa-plus me-2"></i>Tambah Baru
                </a>
            </div>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div class="alert alert-success border-0 shadow-sm mb-3 small rounded-3 fade show">
            <i class="fas fa-check-circle me-2"></i> {!! session('success') !!}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger border-0 shadow-sm mb-3 small rounded-3 fade show">
            <i class="fas fa-times-circle me-2"></i> {{ session('error') }}
        </div>
    @endif

    {{-- AREA PREVIEW IMPORT (Sama seperti sebelumnya, disederhanakan tampilannya) --}}
    @if(!empty($previewData))
    <div class="card border-0 shadow-lg mb-4 rounded-4 overflow-hidden">
        <div class="card-header bg-primary text-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="fas fa-file-import me-2"></i>Preview Import</h6>
                <button wire:click="cancelImport" class="btn btn-sm btn-light text-primary rounded-pill px-3">Batal</button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="alert alert-light mb-0 border-bottom rounded-0 small">
                <strong>Status:</strong> 
                ✅ {{ count($previewData) - count(array_filter($previewData, fn($item) => $item['is_duplicate'])) }} Baru | 
                ⚠️ {{ count(array_filter($previewData, fn($item) => $item['is_duplicate'])) }} Duplikat (Skip)
            </div>
            <div class="table-responsive" style="max-height: 300px;">
                <table class="table table-sm table-striped mb-0 small">
                    <thead class="sticky-top bg-light">
                        <tr>
                            <th class="ps-3">Brand</th>
                            <th>Produk</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($previewData as $item)
                        <tr>
                            <td class="ps-3">{{ $item['brand_name'] }}</td>
                            <td>{{ $item['product_name'] }}</td>
                            <td>
                                @if($item['is_duplicate']) <span class="badge bg-warning text-dark">Skip</span>
                                @else <span class="badge bg-success">Ready</span> @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-3 d-grid">
                <button wire:click="processImport" class="btn btn-primary shadow-sm rounded-3">
                    <i class="fas fa-save me-2"></i>Proses Import Sekarang
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- FILTER BRAND --}}
    @if($availableBrands->count() > 0)
    <div class="mb-4">
        <div class="d-flex overflow-auto pb-2 gap-2" style="scrollbar-width: none;">
            <button wire:click="setBrandFilter(null)" 
                    class="btn btn-sm rounded-pill px-3 border text-nowrap {{ $selectedBrandId === null ? 'btn-dark' : 'bg-white text-secondary' }}">
                Semua
            </button>
            @foreach($availableBrands as $brand)
                <button wire:click="setBrandFilter({{ $brand->id }})" 
                        class="btn btn-sm rounded-pill px-3 border text-nowrap d-flex align-items-center gap-2 {{ $selectedBrandId === $brand->id ? 'btn-primary' : 'bg-white text-secondary' }}">
                    {{ $brand->name }}
                    <span class="badge bg-secondary bg-opacity-25 text-dark rounded-circle" style="font-size: 0.6rem;">{{ $brand->products_count }}</span>
                </button>
            @endforeach
        </div>
    </div>
    @endif

    {{-- DAFTAR PRODUK --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white py-3 border-bottom">
            <div class="row g-2 align-items-center">
                <div class="col-12 col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control border-start-0 ps-0 bg-light" placeholder="Cari tipe produk...">
                    </div>
                </div>
                <div class="col-12 col-md-6 text-md-end">
                    <span class="text-muted small">Total: <strong>{{ $products->total() }}</strong> Produk</span>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            @if($products->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary small text-uppercase">
                        <tr>
                            <th class="ps-4 py-3">Produk Info</th>
                            <th class="py-3">Varian & Stok</th>
                            <th class="py-3 text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $p)
                            <tr class="border-bottom">
                                <td class="ps-4 py-3" style="min-width: 200px;">
                                    <div class="d-flex flex-column">
                                        <span class="badge bg-dark w-auto align-self-start mb-1" style="font-size: 0.7rem;">{{ $p->brand->name ?? 'Jasa' }}</span>
                                        <span class="fw-bold text-dark">{{ $p->name }}</span>
                                        <small class="text-muted">{{ $p->category->name ?? '-' }}</small>
                                    </div>
                                </td>
                                <td style="min-width: 250px;">
                                    @foreach($p->variants as $v)
                                        <div class="d-flex justify-content-between align-items-center py-1 small border-bottom border-light last-no-border">
                                            <div class="text-secondary">
                                                <i class="fas fa-mobile-alt me-1 opacity-50"></i> {{ $v->attribute_name }}
                                            </div>
                                            <div class="text-end">
                                                @if($v->stock > 0)
                                                    <span class="badge bg-success-subtle text-success border border-success border-opacity-25">{{ $v->stock }} Unit</span>
                                                @else
                                                    <span class="badge bg-danger-subtle text-danger border border-danger border-opacity-25">Habis</span>
                                                @endif
                                                <div class="text-primary fw-bold mt-1" style="font-size: 0.75rem;">
                                                    Rp {{ number_format($v->srp_price, 0, ',', '.') }}
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group">
                                        <a href="{{ route('product.edit', $p->id) }}" class="btn btn-sm btn-light border text-primary" title="Edit Stok & Harga">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button wire:click="deleteProduct({{ $p->id }})" 
                                                onclick="return confirm('Yakin hapus {{ $p->name }}?')"
                                                class="btn btn-sm btn-light border text-danger" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-search fa-3x text-light mb-3"></i>
                    <p class="text-muted">Data tidak ditemukan.</p>
                </div>
            @endif
        </div>
        
        @if($products->hasPages())
        <div class="card-footer bg-white border-top py-3">
            {{ $products->links(data: ['scrollTo' => false]) }}
        </div>
        @endif
    </div>
    
    <style>
        .last-no-border:last-child { border-bottom: none !important; }
        /* Mobile adjustment for pagination if needed */
        @media (max-width: 576px) {
            .pagination { justify-content: center; font-size: 0.8rem; }
        }
    </style>
</div>