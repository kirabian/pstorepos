<div class="container-fluid">
    
    <style>
        /* CSS Timeline Custom */
        .timeline {
            border-left: 2px solid #e9ecef;
            padding: 0 20px 0 30px;
            list-style: none;
            position: relative;
        }
        .timeline-item {
            position: relative;
            margin-bottom: 2.5rem;
        }
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -39px;
            top: 5px;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #fff;
            border: 4px solid #212529; /* Warna hitam PStore */
        }
        .timeline-date {
            font-size: 0.85rem;
            color: #6c757d;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        .timeline-content {
            background: #fff;
            padding: 0;
        }
        .timeline-title {
            font-weight: 700;
            font-size: 1rem;
            color: #000;
        }
    </style>

    <div class="mb-4">
        <h4 class="fw-bold text-black">Lacak IMEI</h4>
        <p class="text-secondary small">Telusuri riwayat perjalanan unit berdasarkan IMEI.</p>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <form wire:submit.prevent="lacak">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 ps-3">
                        <i class="fas fa-barcode text-muted"></i>
                    </span>
                    <input type="text" 
                           class="form-control border-start-0 py-3 fw-bold fs-5" 
                           placeholder="Scan atau ketik IMEI disini..." 
                           wire:model="searchImei"
                           style="letter-spacing: 1px;">
                    
                    <button class="btn btn-dark px-4 fw-bold" type="submit">
                        <i class="fas fa-search me-2"></i> LACAK
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if($stokDetail || count($riwayat) > 0)
        <div class="row animate__animated animate__fadeInUp">
            
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-uppercase text-secondary mb-4 small ls-1">Informasi Unit</h6>
                        
                        @if($stokDetail)
                            <div class="mb-3">
                                <label class="small text-muted d-block">Merk / Tipe</label>
                                <span class="fw-bold fs-5">{{ $stokDetail->merk->nama }} {{ $stokDetail->tipe->nama }}</span>
                            </div>
                            <div class="mb-3">
                                <label class="small text-muted d-block">Varian</label>
                                <span class="badge bg-light text-dark border">{{ $stokDetail->ram_storage }}</span>
                            </div>
                            <div class="mb-3">
                                <label class="small text-muted d-block">Kondisi</label>
                                @if($stokDetail->kondisi == 'Baru')
                                    <span class="badge bg-success">NEW / BARU</span>
                                @else
                                    <span class="badge bg-warning text-dark">SECOND</span>
                                @endif
                            </div>
                            <div class="mb-3">
                                <label class="small text-muted d-block">Harga Jual (SRP)</label>
                                <span class="fw-bold text-primary">Rp {{ number_format($stokDetail->harga_jual, 0, ',', '.') }}</span>
                            </div>
                        @else
                            <div class="alert alert-warning small">
                                <i class="fas fa-exclamation-triangle me-1"></i> 
                                Data unit fisik tidak ditemukan di stok aktif (Mungkin sudah terjual habis atau data lama).
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-uppercase text-secondary mb-4 small ls-1">Riwayat Perjalanan</h6>

                        @if(count($riwayat) > 0)
                            <div class="timeline mt-4">
                                @foreach($riwayat as $log)
                                    <div class="timeline-item">
                                        <div class="timeline-date">
                                            <i class="far fa-clock me-1"></i> 
                                            {{ $log->created_at->translatedFormat('d F Y, H:i:s') }}
                                        </div>
                                        <div class="timeline-content">
                                            <div class="timeline-title">{{ $log->status }}</div>
                                            <p class="text-secondary mb-1">{{ $log->keterangan }}</p>
                                            <div class="small text-muted fst-italic">
                                                Oleh: {{ $log->user->nama_lengkap ?? 'System' }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5 text-muted">
                                <i class="fas fa-history fa-3x mb-3 opacity-25"></i>
                                <p>Belum ada riwayat tercatat untuk IMEI ini.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @elseif($notFound)
        <div class="alert alert-danger shadow-sm rounded-3 border-0 d-flex align-items-center animate__animated animate__headShake">
            <i class="fas fa-times-circle fs-4 me-3"></i>
            <div>
                <strong>IMEI Tidak Ditemukan!</strong>
                <div class="small">Pastikan nomor IMEI yang Anda masukkan benar dan sudah terdaftar di sistem.</div>
            </div>
        </div>
    @endif

</div>