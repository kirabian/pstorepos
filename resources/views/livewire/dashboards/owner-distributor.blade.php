<div class="animate__animated animate__fadeIn">
    <div class="p-5 bg-dark text-white rounded-5 shadow-lg mb-5 position-relative overflow-hidden">
        <div class="position-relative z-1">
            <h1 class="fw-black mb-1">{{ $nama_distributor }}</h1>
            <p class="opacity-75 mb-4">Owner Dashboard</p>
            
            <div class="d-flex gap-5">
                <div>
                    <small class="text-uppercase opacity-50 fw-bold">Estimasi Omset Bulan Ini</small>
                    <h2 class="fw-bold mb-0 text-warning">{{ $omset_bulan_ini }}</h2>
                </div>
                <div>
                    <small class="text-uppercase opacity-50 fw-bold">Cabang Dilayani</small>
                    <h2 class="fw-bold mb-0">{{ $cabang_terlayan }} Unit</h2>
                </div>
            </div>
        </div>
        {{-- Background Decoration --}}
        <div class="position-absolute top-0 end-0 h-100 w-50" 
             style="background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1)); pointer-events: none;">
        </div>
    </div>

    <h4 class="fw-bold text-dark mb-3">Kinerja Staff Inventory</h4>
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body">
            <p class="text-muted text-center py-5">Grafik performa pengiriman dan penerimaan barang akan tampil di sini.</p>
        </div>
    </div>
</div>