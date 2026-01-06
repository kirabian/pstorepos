<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Daftar Inventori</h4>
            <p class="text-secondary small mb-0">Gunakan file Excel atau CSV untuk menambah produk massal.</p>
        </div>
        <div class="d-flex gap-2">
            <div class="position-relative">
                <input type="file" wire:model="file_import" id="fileImport" class="d-none" accept=".csv, .xlsx, .xls">
                <label for="fileImport" class="btn btn-outline-success btn-sm px-3 rounded-3 shadow-sm" style="cursor: pointer;">
                    <i class="fas fa-file-excel me-2"></i> Import Excel/CSV
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
    <div class="card border-0 shadow-lg mb-5 rounded-4 border-top border-4 border-warning">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-list me-2 text-warning"></i> Pratinjau Data ({{ count($previewData) }} Baris)</h6>
            <div>
                <button wire:click="cancelImport" class="btn btn-sm btn-light border me-1">Batal</button>
                <button wire:click="processImport" class="btn btn-sm btn-warning fw-bold px-4 shadow-sm" wire:loading.attr="disabled">
                    <span wire:loading.remove>Simpan ke Database</span>
                    <span wire:loading><i class="fas fa-spinner fa-spin"></i> Memproses...</span>
                </button>
            </div>
        </div>
        <div class="card-body p-0" style="max-height: 400px; overflow-y: auto;">
            <table class="table table-sm table-hover mb-0">
                <thead class="bg-light sticky-top">
                    <tr class="small text-uppercase">
                        <th class="ps-4 py-2">Merek (Brand)</th>
                        <th class="py-2">Nama Produk (Tipe)</th>
                        <th class="py-2 text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($previewData as $item)
                    <tr>
                        <td class="ps-4 py-2 font-monospace small">{{ $item['brand'] }}</td>
                        <td class="py-2 small">{{ $item['name'] }}</td>
                        <td class="py-2 text-center text-success small"><i class="fas fa-check-circle"></i> Ready</td>
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
                            <th class="ps-4 py-3 text-uppercase text-secondary fw-bold">Nama Produk</th>
                            <th class="py-3 text-uppercase text-secondary fw-bold">Brand/Jenis</th>
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
                                    <small class="text-muted" style="font-size: 0.65rem;">ID: #PRD-{{ $p->id }}</small>
                                </td>
                                <td>
                                    <span class="badge rounded-pill bg-light text-dark border px-2 mb-1" style="font-size: 0.7rem;">{{ $p->brand->name }}</span><br>
                                    <small class="text-secondary" style="font-size: 0.75rem;">{{ $p->category->name }}</small>
                                </td>
                                <td>
                                    @foreach($p->variants as $v)
                                        <div class="d-flex justify-content-between py-1 small border-bottom border-light">
                                            <span class="text-secondary">{{ $v->attribute_name }}</span>
                                            <span class="fw-bold {{ $v->stock <= 3 ? 'text-danger' : 'text-dark' }}">{{ $v->stock }}</span>
                                        </div>
                                    @endforeach
                                </td>
                                <td>
                                    @foreach($p->variants as $v)
                                        <div class="py-1 small text-muted border-bottom border-light">Rp {{ number_format($v->cost_price, 0, ',', '.') }}</div>
                                    @endforeach
                                </td>
                                <td>
                                    @foreach($p->variants as $v)
                                        <div class="py-1 small fw-bold text-primary border-bottom border-light">Rp {{ number_format($v->srp_price, 0, ',', '.') }}</div>
                                    @endforeach
                                </td>
                                <td class="pe-4 text-center">
                                    <button class="btn btn-sm btn-outline-danger border-0" onclick="confirm('Hapus produk ini?') || event.stopImmediatePropagation()" wire:click="deleteProduct({{ $p->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-secondary">
                                    <i class="fas fa-box-open fs-1 mb-3 opacity-25"></i><br>
                                    Belum ada data produk. Silakan gunakan fitur import atau tambah manual.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>