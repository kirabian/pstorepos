<div class="animate__animated animate__fadeIn">

    {{-- HEADER SECTION --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5 gap-4">
        <div>
            <h1 class="display-6 fw-black text-dark mb-1 tracking-tight">
                Financial Analyst Dashboard
            </h1>
            <p class="text-secondary mb-0">
                Welcome back, <span class="fw-bold text-dark">{{ Auth::user()->nama_lengkap }}</span>. 
                Data reporting for {{ now()->format('F Y') }}.
            </p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-dark rounded-pill px-4 fw-bold">
                <i class="fas fa-download me-2"></i> Export Report
            </button>
            <button class="btn btn-dark rounded-pill px-4 fw-bold">
                <i class="fas fa-sync me-2"></i> Realtime Sync
            </button>
        </div>
    </div>

    {{-- TOP KPI CARDS --}}
    <div class="row g-4 mb-4">
        {{-- Total Omset --}}
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-5 h-100 bg-dark text-white overflow-hidden position-relative">
                <div class="card-body p-4 position-relative z-1">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div class="bg-white bg-opacity-25 p-2 rounded-3">
                            <i class="fas fa-wallet fa-lg text-white"></i>
                        </div>
                        <span class="badge bg-success text-white border border-white border-opacity-25 rounded-pill">
                            <i class="fas fa-arrow-up me-1"></i> +8.5%
                        </span>
                    </div>
                    <p class="text-white-50 text-uppercase small fw-bold mb-1 tracking-wide">Total Omset (All Branches)</p>
                    <h2 class="fw-black mb-0 tracking-tight">Rp {{ number_format($total_omset, 0, ',', '.') }}</h2>
                </div>
                {{-- Decorative Blob --}}
                <div class="position-absolute top-0 end-0 mt-n4 me-n4 opacity-25">
                     <i class="fas fa-chart-line" style="font-size: 8rem; color: rgba(255,255,255,0.1);"></i>
                </div>
            </div>
        </div>

        {{-- Total Profit --}}
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-5 h-100 bg-success bg-opacity-10">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div class="bg-success text-white p-2 rounded-3 shadow-sm">
                            <i class="fas fa-coins fa-lg"></i>
                        </div>
                    </div>
                    <p class="text-success text-uppercase small fw-bold mb-1 tracking-wide">Net Profit</p>
                    <h2 class="fw-black text-success-emphasis mb-0 tracking-tight">Rp {{ number_format($total_profit, 0, ',', '.') }}</h2>
                    <small class="text-muted fw-bold">Avg Margin: {{ number_format($avg_margin, 1) }}%</small>
                </div>
            </div>
        </div>

        {{-- Performance Index --}}
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-5 h-100 bg-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-3">
                            <i class="fas fa-chart-pie fa-lg"></i>
                        </div>
                    </div>
                    <p class="text-secondary text-uppercase small fw-bold mb-1 tracking-wide">Cabang Tertinggi</p>
                    @php $topBranch = $cabangs_performance->first(); @endphp
                    <h4 class="fw-black text-dark mb-0 tracking-tight text-truncate">{{ $topBranch['nama_cabang'] ?? '-' }}</h4>
                    <small class="text-primary fw-bold">Rp {{ number_format($topBranch['omset'] ?? 0, 0, ',', '.') }}</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-5">
        {{-- SECTION KIRI: TABEL PERFORMA CABANG --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-5 h-100 overflow-hidden">
                <div class="card-header bg-white border-bottom border-light-subtle p-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-dark mb-0">Branch Performance</h5>
                    <div class="dropdown">
                        <button class="btn btn-light btn-sm rounded-pill px-3 fw-bold dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            Filter: Omset Tertinggi
                        </button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3 small fw-bold text-uppercase text-secondary">Cabang</th>
                                <th class="py-3 small fw-bold text-uppercase text-secondary text-end">Omset</th>
                                <th class="py-3 small fw-bold text-uppercase text-secondary text-end">Profit</th>
                                <th class="py-3 small fw-bold text-uppercase text-secondary text-center">Margin</th>
                                <th class="pe-4 py-3 small fw-bold text-uppercase text-secondary text-end">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cabangs_performance as $c)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center fw-bold text-secondary" style="width: 40px; height: 40px;">
                                            {{ substr($c['nama_cabang'], 0, 1) }}
                                        </div>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-0">{{ $c['nama_cabang'] }}</h6>
                                            <small class="text-muted">{{ $c['lokasi'] }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end fw-bold text-dark">
                                    Rp {{ number_format($c['omset'], 0, ',', '.') }}
                                </td>
                                <td class="text-end fw-bold text-success">
                                    Rp {{ number_format($c['profit'], 0, ',', '.') }}
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $c['margin'] > 20 ? 'success' : 'warning' }}-subtle text-{{ $c['margin'] > 20 ? 'success' : 'warning' }} rounded-pill border border-{{ $c['margin'] > 20 ? 'success' : 'warning' }}-subtle">
                                        {{ number_format($c['margin'], 1) }}%
                                    </span>
                                </td>
                                <td class="pe-4 text-end">
                                    @if($c['margin'] > 25)
                                        <i class="fas fa-check-circle text-success" title="Excellent"></i>
                                    @elseif($c['margin'] > 15)
                                        <i class="fas fa-minus-circle text-warning" title="Average"></i>
                                    @else
                                        <i class="fas fa-exclamation-circle text-danger" title="Low Performance"></i>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- SECTION KANAN: SIMULATOR LIVEWIRE --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-5 h-100 bg-primary bg-gradient text-white overflow-hidden">
                <div class="card-body p-4 d-flex flex-column">
                    <div class="mb-4">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="fas fa-calculator"></i>
                            <h5 class="fw-bold mb-0">Profit Simulator</h5>
                        </div>
                        <p class="text-white-50 small">Simulasikan kenaikan target omset dan efisiensi biaya untuk melihat proyeksi keuntungan.</p>
                    </div>

                    {{-- Form Simulator --}}
                    <div class="mb-4">
                        <label class="small fw-bold text-uppercase text-white-50 mb-2">Target Growth (%)</label>
                        <div class="range-wrap mb-3">
                            <input type="range" wire:model.live="sim_target_growth" class="form-range custom-range" min="0" max="100" step="5">
                            <div class="d-flex justify-content-between fw-bold small">
                                <span>0%</span>
                                <span class="text-white bg-white bg-opacity-25 px-2 rounded">{{ $sim_target_growth }}%</span>
                                <span>100%</span>
                            </div>
                        </div>

                        <label class="small fw-bold text-uppercase text-white-50 mb-2">Cost Efficiency (%)</label>
                        <div class="range-wrap">
                            <input type="range" wire:model.live="sim_efficiency" class="form-range custom-range" min="0" max="50" step="1">
                            <div class="d-flex justify-content-between fw-bold small">
                                <span>0%</span>
                                <span class="text-white bg-white bg-opacity-25 px-2 rounded">{{ $sim_efficiency }}%</span>
                                <span>50%</span>
                            </div>
                        </div>
                    </div>

                    <hr class="border-white opacity-25 my-2">

                    {{-- Hasil Simulasi --}}
                    <div class="mt-auto">
                        <div class="row g-2">
                            <div class="col-12">
                                <div class="bg-black bg-opacity-25 p-3 rounded-4 border border-white border-opacity-10">
                                    <small class="text-white-50 d-block text-uppercase fw-bold extra-small">Projected Omset</small>
                                    <h4 class="fw-black mb-1">Rp {{ number_format($sim_projected_omset, 0, ',', '.') }}</h4>
                                    <small class="text-success fw-bold">
                                        <i class="fas fa-arrow-up"></i> +Rp {{ number_format($sim_growth_val, 0, ',', '.') }}
                                    </small>
                                </div>
                            </div>
                            <div class="col-12 mt-2">
                                <div class="bg-white text-primary p-3 rounded-4 shadow-sm">
                                    <small class="text-uppercase fw-bold extra-small opacity-75">Projected Net Profit</small>
                                    <h3 class="fw-black mb-0">Rp {{ number_format($sim_projected_profit, 0, ',', '.') }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 text-center">
                            <button class="btn btn-outline-light btn-sm rounded-pill w-100 fw-bold border-opacity-25">
                                <i class="fas fa-save me-1"></i> Save Projection
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Styling Khusus Halaman Analis --}}
    <style>
        .custom-range::-webkit-slider-thumb {
            background: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
        }
        .text-success-emphasis {
            color: #055160; /* Darker green for text on light green bg */
        }
        .hover-lift:hover { transform: translateY(-5px); transition: all 0.3s ease; }
    </style>
</div>