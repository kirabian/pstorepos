<div class="container py-4">

    {{-- Custom CSS untuk Timeline & UI Responsif --}}
    <style>
        /* Timeline Vertical Line */
        .tracking-list {
            border-left: 2px solid #e9ecef;
            position: relative;
            padding-left: 0;
            list-style: none;
            margin-top: 1rem;
        }

        .tracking-item {
            position: relative;
            padding-left: 25px; /* Jarak teks dari garis */
            padding-bottom: 2.5rem;
        }

        /* Titik (Dot) Timeline - Dibuat sejajar dengan judul (Top Aligned) */
        .tracking-item::before {
            content: '';
            position: absolute;
            left: -9px; 
            top: 5px; /* Posisi titik di atas, bukan di tengah */
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background-color: #fff;
            border: 4px solid #0d6efd; 
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
        }

        .tracking-item:last-child {
            padding-bottom: 0;
            border-left: 2px solid transparent; 
        }

        /* Typography Timeline */
        .tracking-date {
            font-size: 0.75rem;
            font-weight: 600;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
            line-height: 1.4; /* Jarak antar baris jika teks panjang */
        }

        .tracking-title {
            font-size: 1rem;
            font-weight: 700;
            color: #212529;
            margin-bottom: 0.25rem;
        }

        /* Tag Style Baru (Tanpa Border Bulat) */
        .meta-tag {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 4px 8px;
            border-radius: 6px; /* Kotak dengan sudut halus (sedikit rounded) */
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
        }

        .meta-tag i {
            font-size: 0.8rem;
        }

        /* Responsive Tweaks */
        @media (max-width: 576px) {
            .tracking-item {
                padding-left: 20px;
            }
            .tracking-title {
                font-size: 0.95rem;
            }
        }
    </style>

    {{-- Header Section --}}
    <div class="text-center mb-5">
        <h3 class="fw-bold text-dark mb-1">Lacak Status Unit</h3>
        <p class="text-muted small">Cek posisi dan riwayat unit berdasarkan IMEI.</p>
    </div>

    {{-- Search Box Section --}}
    <div class="row justify-content-center mb-5">
        <div class="col-md-8 col-lg-6">
            <div class="card border-0 shadow rounded-4 overflow-hidden">
                <div class="card-body p-2 bg-white">
                    <form wire:submit.prevent="lacak">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-0 ps-3">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" 
                                   class="form-control border-0 fs-5 fw-bold py-3 text-dark shadow-none" 
                                   placeholder="Contoh: 3582910..." 
                                   wire:model="searchImei"
                                   style="letter-spacing: 1px;">
                            
                            <button class="btn btn-dark rounded-4 px-4 fw-bold m-1" type="button" wire:click="lacak">
                                <span wire:loading.remove wire:target="lacak">CARI</span>
                                <span wire:loading wire:target="lacak">
                                    <i class="fas fa-circle-notch fa-spin"></i>
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div wire:loading wire:target="lacak" class="text-center w-100 mt-2 text-primary small fw-bold fade-in">
                Sedang melacak data...
            </div>
        </div>
    </div>

    {{-- Result Section --}}
    @if($stokDetail || count($riwayat) > 0)
        <div class="row g-4 animate__animated animate__fadeInUp">
            
            {{-- KOLOM KIRI: Detail Produk --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                        <div class="d-flex align-items-center mb-2">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2 me-3">
                                <i class="fas fa-mobile-alt fa-lg"></i>
                            </div>
                            <h6 class="fw-bold text-uppercase m-0 ls-1">Informasi Produk</h6>
                        </div>
                        <hr class="my-2 text-muted opacity-25">
                    </div>

                    <div class="card-body px-4 pb-4">
                        @if($stokDetail)
                            <div class="mb-4">
                                <label class="small text-muted fw-bold d-block mb-1">UNIT</label>
                                <h5 class="fw-bold text-dark mb-0">{{ $stokDetail->merk->nama }}</h5>
                                <div class="text-secondary">{{ $stokDetail->tipe->nama }}</div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-6">
                                    <label class="small text-muted fw-bold d-block mb-1">VARIAN</label>
                                    <span class="d-block bg-light text-dark fw-bold px-3 py-2 rounded-3 text-center border-0 small">
                                        {{ $stokDetail->ram_storage }}
                                    </span>
                                </div>
                                <div class="col-6">
                                    <label class="small text-muted fw-bold d-block mb-1">KONDISI</label>
                                    @if($stokDetail->kondisi == 'Baru')
                                        <span class="d-block bg-success bg-opacity-10 text-success fw-bold px-3 py-2 rounded-3 text-center border-0 small">
                                            BARU
                                        </span>
                                    @else
                                        <span class="d-block bg-warning bg-opacity-10 text-warning fw-bold px-3 py-2 rounded-3 text-center border-0 small">
                                            SECOND
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="p-3 bg-light rounded-3 text-center">
                                <label class="small text-muted mb-1">Harga Jual (SRP)</label>
                                <h4 class="fw-bold text-primary mb-0">Rp {{ number_format($stokDetail->harga_jual, 0, ',', '.') }}</h4>
                            </div>
                        @else
                            <div class="alert alert-warning border-0 rounded-3 small">
                                <i class="fas fa-info-circle me-1"></i> 
                                Unit fisik tidak ditemukan (Terjual/Hapus).
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: Timeline History --}}
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                        <div class="d-flex align-items-center mb-2">
                            <div class="bg-info bg-opacity-10 text-info rounded-3 p-2 me-3">
                                <i class="fas fa-history fa-lg"></i>
                            </div>
                            <h6 class="fw-bold text-uppercase m-0 ls-1">Riwayat Perjalanan</h6>
                        </div>
                        <hr class="my-2 text-muted opacity-25">
                    </div>

                    <div class="card-body px-4 pb-4">
                        @if(count($riwayat) > 0)
                            <ul class="tracking-list">
                                @foreach($riwayat as $log)
                                    <li class="tracking-item">
                                        {{-- TANGGAL & ZONA WAKTU (Responsif handling) --}}
                                        <div class="tracking-date">
                                            <i class="far fa-clock me-1"></i> {{ $log->waktu_lokal }}
                                        </div>
                                        
                                        {{-- JUDUL STATUS --}}
                                        <div class="tracking-title">{{ $log->status }}</div>
                                        
                                        {{-- KETERANGAN --}}
                                        <p class="text-secondary small mb-3 text-break" style="line-height: 1.5;">
                                            {{ $log->keterangan }}
                                        </p>
                                        
                                        {{-- USER & CABANG (DESIGN BARU: FLAT & CLEAN) --}}
                                        <div class="d-flex flex-wrap gap-2">
                                            {{-- Label User --}}
                                            <div class="meta-tag bg-light text-secondary">
                                                <i class="fas fa-user"></i> 
                                                <span>{{ $log->user->nama_lengkap ?? 'System' }}</span>
                                            </div>
                                            
                                            {{-- Label Cabang --}}
                                            @if($log->cabang)
                                                <div class="meta-tag bg-primary bg-opacity-10 text-primary">
                                                    <i class="fas fa-store"></i> 
                                                    <span>{{ $log->cabang->nama_cabang ?? $log->cabang->nama }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-history fa-3x text-muted opacity-25 mb-3"></i>
                                <p class="small text-muted">Belum ada riwayat aktivitas.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    {{-- Not Found State --}}
    @elseif($notFound)
        <div class="row justify-content-center animate__animated animate__headShake">
            <div class="col-md-8 col-lg-6">
                <div class="alert alert-danger border-0 shadow-sm rounded-3 p-4 d-flex align-items-center">
                    <i class="fas fa-times-circle fs-3 me-3 opacity-50"></i>
                    <div>
                        <h6 class="fw-bold mb-1">IMEI Tidak Ditemukan</h6>
                        <small>Nomor IMEI <strong>"{{ $searchImei }}"</strong> tidak terdaftar.</small>
                    </div>
                </div>
            </div>
        </div>
    @endif
    
    <div class="text-center mt-5 pt-3 border-top border-light">
        <small class="text-muted opacity-75">
            &copy; {{ date('Y') }} PStore Inventory System
        </small>
    </div>

</div>