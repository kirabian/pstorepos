<div class="container py-4">

    {{-- Custom CSS untuk Timeline & UI --}}
    <style>
        /* Timeline Vertical Line */
        .tracking-list {
            border-left: 2px solid #e9ecef;
            position: relative;
            padding-left: 0;
            list-style: none;
        }

        .tracking-item {
            position: relative;
            padding-left: 30px;
            padding-bottom: 2.5rem;
        }

        /* Titik (Dot) Timeline */
        .tracking-item::before {
            content: '';
            position: absolute;
            left: -9px; /* Adjust agar pas ditengah garis */
            top: 4px;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background-color: #fff;
            border: 4px solid #0d6efd; /* Warna Primary Bootstrap */
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
            transition: all 0.3s ease;
        }

        .tracking-item:hover::before {
            background-color: #0d6efd;
            transform: scale(1.2);
        }

        /* Hilangkan padding bawah item terakhir */
        .tracking-item:last-child {
            padding-bottom: 0;
            border-left: 2px solid transparent; /* Hilangkan garis sisa */
        }

        /* Typography Timeline */
        .tracking-date {
            font-size: 0.8rem;
            font-weight: 600;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .tracking-title {
            font-size: 1rem;
            font-weight: 700;
            color: #212529;
        }

        /* Card Styling */
        .card-hover {
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        /* Background Gradient Header */
        .bg-gradient-header {
            background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
        }
    </style>

    {{-- Header Section --}}
    <div class="text-center mb-5">
        <h3 class="fw-bold text-dark mb-1">Lacak Status Unit</h3>
        <p class="text-muted small">Masukkan Nomor IMEI untuk melacak posisi, kondisi, dan riwayat unit.</p>
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
            {{-- Loading Indicator --}}
            <div wire:loading wire:target="lacak" class="text-center w-100 mt-2 text-primary small fw-bold fade-in">
                Sedang melacak data ke server...
            </div>
        </div>
    </div>

    {{-- Result Section --}}
    @if($stokDetail || count($riwayat) > 0)
        <div class="row g-4 animate__animated animate__fadeInUp">
            
            {{-- KOLOM KIRI: Detail Produk --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 card-hover">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                        <div class="d-flex align-items-center mb-2">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2 me-3">
                                <i class="fas fa-mobile-alt fa-lg"></i>
                            </div>
                            <h6 class="fw-bold text-uppercase m-0 ls-1">Informasi Produk</h6>
                        </div>
                        <hr class="my-2 text-muted opacity-25">
                    </div>

                    <div class="card-body px-4 pb-4">
                        @if($stokDetail)
                            {{-- Nama Produk --}}
                            <div class="mb-4">
                                <label class="small text-muted fw-bold d-block mb-1">UNIT</label>
                                <h5 class="fw-bold text-dark mb-0">{{ $stokDetail->merk->nama }}</h5>
                                <div class="text-secondary">{{ $stokDetail->tipe->nama }}</div>
                            </div>

                            <div class="row g-3 mb-4">
                                {{-- RAM/Storage --}}
                                <div class="col-6">
                                    <label class="small text-muted fw-bold d-block mb-1">VARIAN</label>
                                    <span class="badge bg-light text-dark border px-3 py-2 rounded-3 w-100 text-start">
                                        <i class="fas fa-memory me-1 text-secondary"></i> {{ $stokDetail->ram_storage }}
                                    </span>
                                </div>
                                {{-- Kondisi --}}
                                <div class="col-6">
                                    <label class="small text-muted fw-bold d-block mb-1">KONDISI</label>
                                    @if($stokDetail->kondisi == 'Baru')
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-2 rounded-3 w-100 text-start">
                                            <i class="fas fa-check-circle me-1"></i> BARU
                                        </span>
                                    @else
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning px-3 py-2 rounded-3 w-100 text-start">
                                            <i class="fas fa-box-open me-1"></i> SECOND
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Harga --}}
                            <div class="p-3 bg-light rounded-3 border border-dashed text-center">
                                <label class="small text-muted mb-1">Harga Jual (SRP)</label>
                                <h4 class="fw-bold text-primary mb-0">Rp {{ number_format($stokDetail->harga_jual, 0, ',', '.') }}</h4>
                            </div>
                        @else
                            {{-- Jika stok fisik sudah tidak ada (terjual/dihapus) tapi history ada --}}
                            <div class="alert alert-warning border-0 d-flex align-items-start rounded-3" role="alert">
                                <i class="fas fa-info-circle fs-5 me-3 mt-1"></i>
                                <div>
                                    <strong>Status: Tidak Aktif</strong>
                                    <p class="small mb-0 mt-1">
                                        Unit fisik dengan IMEI ini tidak ditemukan di gudang aktif. Kemungkinan unit sudah <b>Terjual</b> atau data dihapus. Namun, riwayat tersedia di sebelah kanan.
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: Timeline History --}}
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 h-100 card-hover">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                        <div class="d-flex align-items-center mb-2">
                            <div class="bg-info bg-opacity-10 text-info rounded-circle p-2 me-3">
                                <i class="fas fa-history fa-lg"></i>
                            </div>
                            <h6 class="fw-bold text-uppercase m-0 ls-1">Riwayat Perjalanan</h6>
                        </div>
                        <hr class="my-2 text-muted opacity-25">
                    </div>

                    <div class="card-body px-4 pb-4">
                        @if(count($riwayat) > 0)
                            <div class="mt-3">
                                <ul class="tracking-list">
                                    @foreach($riwayat as $log)
                                        <li class="tracking-item">
                                            {{-- WAKTU (MENGGUNAKAN LOGIKA TIMEZONE MODEL) --}}
                                            <div class="tracking-date">
                                                <i class="far fa-clock me-1"></i> {{ $log->waktu_lokal }}
                                            </div>
                                            
                                            {{-- STATUS --}}
                                            <div class="tracking-title mb-1">{{ $log->status }}</div>
                                            
                                            {{-- KETERANGAN --}}
                                            <p class="text-secondary small mb-2 text-break">
                                                {{ $log->keterangan }}
                                            </p>
                                            
                                            {{-- USER & CABANG --}}
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary border rounded-pill px-2 fw-normal" style="font-size: 0.7rem;">
                                                    <i class="fas fa-user-circle me-1"></i> {{ $log->user->nama_lengkap ?? 'System' }}
                                                </span>
                                                
                                                @if($log->cabang)
                                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-2 fw-normal" style="font-size: 0.7rem;">
                                                        <i class="fas fa-store me-1"></i> {{ $log->cabang->nama_cabang ?? $log->cabang->nama }}
                                                    </span>
                                                @endif
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" alt="Empty" width="80" class="opacity-50 mb-3 grayscale">
                                <h6 class="fw-bold text-muted">Belum ada riwayat</h6>
                                <p class="small text-muted">Unit ini belum memiliki catatan aktivitas apapun.</p>
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
                <div class="alert alert-danger bg-white border-danger border-start border-4 border-0 shadow-sm rounded-3 p-4">
                    <div class="d-flex">
                        <div class="me-3">
                            <i class="fas fa-times-circle text-danger fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="alert-heading fw-bold text-danger mb-1">IMEI Tidak Ditemukan</h5>
                            <p class="mb-0 text-secondary">
                                Nomor IMEI <strong>"{{ $searchImei }}"</strong> tidak terdaftar dalam sistem stok aktif maupun arsip riwayat kami. Silakan periksa kembali digit angka yang dimasukkan.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    
    {{-- Footer Copyright (Optional) --}}
    <div class="text-center mt-5 pt-3 border-top border-light">
        <small class="text-muted opacity-75">
            &copy; {{ date('Y') }} Sistem Manajemen Stok • Waktu Server: {{ now()->format('H:i') }} WIB
        </small>
    </div>

</div>