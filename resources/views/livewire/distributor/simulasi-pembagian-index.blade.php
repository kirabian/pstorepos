<div class="container-fluid">
    <div class="d-flex align-items-center mb-4">
        <i class="fas fa-calculator fs-4 me-2 text-warning"></i>
        <h4 class="fw-bold text-black mb-0">Simulasi Pembagian Barang</h4>
    </div>

    <div class="row">
        {{-- Panel Kiri: Input Master --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Sumber Barang</h5>
                    
                    <div class="mb-3">
                        <label class="form-label small text-muted fw-bold">TOTAL STOK BARU (UNIT)</label>
                        <input type="number" class="form-control form-control-lg fw-bold text-primary" 
                               wire:model.live="totalBarang" placeholder="0">
                    </div>

                    <div class="d-grid gap-2 mb-3">
                        <button class="btn btn-outline-primary" wire:click="bagiRata">
                            <i class="fas fa-balance-scale me-1"></i> Bagi Rata Semua Cabang
                        </button>
                        <button class="btn btn-outline-danger" wire:click="resetSimulasi">
                            <i class="fas fa-undo me-1"></i> Reset
                        </button>
                    </div>

                    <hr>

                    <div class="text-center">
                        <small class="text-uppercase text-muted fw-bold">Sisa Belum Dialokasikan</small>
                        <h1 class="display-4 fw-black {{ $sisaBarang < 0 ? 'text-danger' : 'text-success' }}">
                            {{ $sisaBarang }}
                        </h1>
                        @if($sisaBarang < 0)
                            <small class="text-danger fw-bold">Alokasi melebihi stok tersedia!</small>
                        @endif
                    </div>
                </div>
            </div>
            
            <button class="btn btn-dark w-100 py-3 rounded-4 fw-bold shadow-lg" wire:click="simpanRencana" 
                    {{ $totalBarang == 0 ? 'disabled' : '' }}>
                <i class="fas fa-save me-2"></i> Simpan Rencana Distribusi
            </button>
            
            @if (session()->has('success'))
                <div class="alert alert-success mt-3 rounded-3">
                    {{ session('success') }}
                </div>
            @endif
        </div>

        {{-- Panel Kanan: List Cabang --}}
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom p-4">
                    <h6 class="fw-bold mb-0">Alokasi Per Cabang</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light sticky-top">
                                <tr>
                                    <th class="ps-4">Nama Cabang</th>
                                    <th>Lokasi</th>
                                    <th style="width: 150px;" class="text-end pe-4">Jumlah (Unit)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cabangs as $cabang)
                                    <tr>
                                        <td class="ps-4 fw-bold">{{ $cabang->nama_cabang }}</td>
                                        <td class="text-muted small">{{ $cabang->lokasi }}</td>
                                        <td class="pe-4">
                                            <input type="number" 
                                                   class="form-control text-end fw-bold {{ $alokasi[$cabang->id] > 0 ? 'border-primary text-primary bg-primary bg-opacity-10' : '' }}" 
                                                   wire:model.live="alokasi.{{ $cabang->id }}">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>