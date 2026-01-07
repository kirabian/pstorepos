<div class="container-fluid">
    <div class="d-flex align-items-center mb-4">
        <i class="fas fa-arrow-circle-down fs-4 me-2"></i>
        <h4 class="fw-bold text-black mb-0">Barang Masuk</h4>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            
            {{-- Toolbar Filter --}}
            <div class="p-4 border-bottom bg-white">
                <div class="row g-3 align-items-center justify-content-end">
                    
                    {{-- Search Filter --}}
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 rounded-start-3 ps-3">
                                <i class="fas fa-search text-muted small"></i>
                            </span>
                            <input type="text" class="form-control border-start-0 rounded-end-3 ps-0" 
                                   placeholder="Filter dengan Kategori / IMEI" 
                                   wire:model.live.debounce.300ms="search">
                        </div>
                    </div>

                    {{-- Bulan Filter --}}
                    <div class="col-md-2">
                        <select class="form-select rounded-3" wire:model.live="bulan">
                            <option value="01">Januari</option>
                            <option value="02">Februari</option>
                            <option value="03">Maret</option>
                            <option value="04">April</option>
                            <option value="05">Mei</option>
                            <option value="06">Juni</option>
                            <option value="07">Juli</option>
                            <option value="08">Agustus</option>
                            <option value="09">September</option>
                            <option value="10">Oktober</option>
                            <option value="11">November</option>
                            <option value="12">Desember</option>
                        </select>
                    </div>

                    {{-- Tahun Filter --}}
                    <div class="col-md-2">
                        <select class="form-select rounded-3" wire:model.live="tahun">
                            @for($i = date('Y'); $i >= date('Y')-5; $i--)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>

                    {{-- Export Button --}}
                    <div class="col-md-auto">
                        <button class="btn btn-black rounded-3 px-4 fw-bold text-white" style="background-color: #000;">
                            <i class="fas fa-file-export me-2"></i> Export
                        </button>
                    </div>
                </div>
            </div>

            {{-- Table Data --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-3 px-4 text-secondary small fw-bold">No</th>
                            <th class="py-3 text-secondary small fw-bold">Tanggal Masuk</th>
                            <th class="py-3 text-secondary small fw-bold">IMEI</th>
                            <th class="py-3 text-secondary small fw-bold">Cabang / Lokasi</th>
                            <th class="py-3 text-secondary small fw-bold">Keterangan</th>
                            <th class="py-3 text-secondary small fw-bold">Kondisi</th>
                            <th class="py-3 text-secondary small fw-bold">Oleh</th>
                            <th class="py-3 px-4 text-secondary small fw-bold text-end">#</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($histories as $index => $item)
                            <tr>
                                <td class="px-4 text-muted fw-bold">{{ $histories->firstItem() + $index }}</td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="text-dark fw-bold">{{ $item->created_at->format('d F Y') }}</span>
                                        <span class="text-muted small">{{ $item->created_at->format('H:i:s') }}</span>
                                    </div>
                                </td>
                                <td class="font-monospace text-primary fw-bold">{{ $item->imei }}</td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        {{ $item->cabang->nama_cabang ?? 'Pusat' }}
                                    </span>
                                </td>
                                <td class="text-muted small">{{ Str::limit($item->keterangan, 40) }}</td>
                                <td>
                                    {{-- Karena kondisi tidak disimpan di history, kita hardcode atau ambil dari keterangan jika ada --}}
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success rounded-2">NEW</span>
                                </td>
                                <td>
                                    <span class="small fw-bold">{{ $item->user->nama_lengkap ?? 'System' }}</span>
                                </td>
                                <td class="px-4 text-end">
                                    <button class="btn btn-dark btn-sm rounded-circle shadow-sm" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="fas fa-box-open fs-1 mb-3 opacity-25"></i>
                                    <p class="mb-0">Belum ada data barang masuk.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="p-4 border-top">
                {{ $histories->links() }}
            </div>
        </div>
    </div>
</div>