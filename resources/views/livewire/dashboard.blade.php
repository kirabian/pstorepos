<div wire:poll.15s>
    @if (session()->has('info'))
        <div class="alert alert-dark border-0 shadow-premium rounded-4 mb-4 d-flex align-items-center animate__animated animate__fadeInDown">
            <i class="fas fa-check-circle me-3 ms-2 text-white"></i>
            <div class="text-white small fw-bold">{{ session('info') }}</div>
            <button type="button" class="btn-close btn-close-white ms-auto small shadow-none" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row align-items-center mb-5">
        <div class="col-12 col-md">
            <div class="d-flex align-items-center gap-3 mb-1">
                <div class="bg-dark text-white p-2 rounded-3 shadow-sm">
                    <i class="fas fa-shield-alt fs-4"></i>
                </div>
                <h1 class="fw-900 text-dark mb-0 display-6 tracking-tighter">Superadmin Center Test</h1>
            </div>
            <p class="text-muted small fw-bold text-uppercase ms-5 ps-2" style="letter-spacing: 4px; opacity: 0.6;">PSTORE Core Intelligence System</p>
        </div>
        <div class="col-12 col-md-auto d-flex gap-3">
            <div class="glass-pill px-4 py-2 rounded-pill border border-light-subtle d-flex align-items-center shadow-sm bg-white">
                <span class="pulse-green me-2"></span>
                <span class="extra-small fw-900 text-dark">{{ $onlineUsersCount }} USERS ACTIVE</span>
            </div>
            <button wire:click="testSinyal" class="btn-premium-dark rounded-circle shadow-premium p-3">
                <i class="fas fa-broadcast-tower"></i>
            </button>
        </div>
    </div>

    <div class="row g-4 mb-5">
        @php
            $stats = [
                ['label' => 'Total Accounts', 'value' => $totalUsers, 'icon' => 'fa-users', 'color' => 'dark', 'desc' => 'Verified System'],
                ['label' => 'Active Branches', 'value' => $totalCabang, 'icon' => 'fa-store', 'color' => 'dark', 'desc' => 'National Operations'],
                ['label' => 'Warehouses', 'value' => $totalGudang, 'icon' => 'fa-warehouse', 'color' => 'dark', 'desc' => 'Logistics Central'],
                ['label' => 'Strategic Partners', 'value' => $totalDistributor, 'icon' => 'fa-truck', 'color' => 'dark', 'desc' => 'Supply Chain']
            ];
        @endphp

        @foreach($stats as $stat)
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card-premium h-100 p-4 bg-white border border-light-subtle shadow-premium position-relative overflow-hidden">
                <div class="position-relative z-index-2">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <p class="text-uppercase extra-small fw-900 text-muted mb-0">{{ $stat['label'] }}</p>
                        <div class="icon-circle bg-light text-dark">
                            <i class="fas {{ $stat['icon'] }} small"></i>
                        </div>
                    </div>
                    <h2 class="fw-black text-dark mb-1">{{ number_format($stat['value']) }}</h2>
                    <p class="extra-small fw-bold text-muted mb-0 opacity-50">{{ $stat['desc'] }}</p>
                </div>
                <img src="{{ asset('images/logo-pstore.png') }}" class="card-watermark opacity-5">
            </div>
        </div>
        @endforeach
    </div>

    <div class="row g-4 mb-5">
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-premium rounded-5 p-4 bg-white border border-light-subtle">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-900 text-dark mb-0">System Traffic Analysis</h5>
                    <span class="badge bg-light text-dark rounded-pill extra-small px-3">Live Updates</span>
                </div>
                <div style="height: 350px;">
                    <canvas id="mainChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-premium rounded-5 p-4 bg-white border border-light-subtle">
                <h5 class="fw-900 text-dark mb-4">Authority Distribution</h5>
                <div style="height: 350px;">
                    <canvas id="roleChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <style>
        .fw-900 { font-weight: 900 !important; }
        .fw-black { font-weight: 950 !important; font-size: 2.2rem; letter-spacing: -1px; }
        .extra-small { font-size: 0.65rem; letter-spacing: 2px; }
        .shadow-premium { box-shadow: 0 15px 35px rgba(0,0,0,0.03) !important; }
        .z-index-2 { z-index: 2; position: relative; }
        
        /* FIX GETER: Gunakan transition yang halus dan batasi transform */
        .card-premium {
            border-radius: 2rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
        }
        .card-premium:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 50px rgba(0,0,0,0.06) !important;
            border-color: #000 !important;
        }
        .card-premium:active {
            transform: scale(0.98); /* Efek tekan yang natural, bukan getar */
        }

        .icon-circle {
            width: 40px; height: 40px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
        }

        .btn-premium-dark {
            background: #000; color: #fff; border: none;
            transition: all 0.3s ease; width: 50px; height: 50px;
            display: flex; align-items: center; justify-content: center;
        }
        .btn-premium-dark:hover { transform: rotate(15deg) scale(1.1); background: #222; }

        .card-watermark {
            position: absolute; width: 120px; right: -20px; bottom: -20px;
            transform: rotate(-10deg); pointer-events: none;
        }

        .pulse-green {
            width: 8px; height: 8px; background-color: #000; border-radius: 50%;
            animation: pulse-black 2s infinite;
        }
        @keyframes pulse-black {
            0% { box-shadow: 0 0 0 0 rgba(0,0,0, 0.4); }
            70% { box-shadow: 0 0 0 10px rgba(0,0,0, 0); }
            100% { box-shadow: 0 0 0 0 rgba(0,0,0, 0); }
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Gunakan fungsi untuk re-init chart
        function initCharts() {
            const ctxMain = document.getElementById('mainChart');
            const ctxRole = document.getElementById('roleChart');

            if (ctxMain) {
                // Destroy existing chart to prevent overlap
                if (window.myChart1) window.myChart1.destroy();
                window.myChart1 = new Chart(ctxMain, {
                    type: 'line',
                    data: {
                        labels: ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL'],
                        datasets: [{
                            label: 'Platform Load',
                            data: [65, 59, 80, 81, 56, 85, 90],
                            borderColor: '#000',
                            borderWidth: 3,
                            pointBackgroundColor: '#000',
                            tension: 0.4,
                            fill: true,
                            backgroundColor: (context) => {
                                const ctx = context.chart.ctx;
                                const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                                gradient.addColorStop(0, 'rgba(0,0,0,0.05)');
                                gradient.addColorStop(1, 'rgba(0,0,0,0)');
                                return gradient;
                            }
                        }]
                    },
                    options: { 
                        maintainAspectRatio: false, 
                        plugins: { legend: { display: false } },
                        scales: { y: { display: false }, x: { grid: { display: false } } }
                    }
                });
            }

            if (ctxRole) {
                if (window.myChart2) window.myChart2.destroy();
                window.myChart2 = new Chart(ctxRole, {
                    type: 'doughnut',
                    data: {
                        labels: ['Superadmin', 'Branch Managers', 'Staff', 'Security'],
                        datasets: [{
                            data: [12, 19, 45, 24],
                            backgroundColor: ['#000000', '#333333', '#888888', '#eeeeee'],
                            borderWidth: 0,
                            hoverOffset: 20
                        }]
                    },
                    options: { 
                        maintainAspectRatio: false, 
                        cutout: '80%',
                        plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, font: { weight: 'bold', size: 10 } } } }
                    }
                });
            }
        }

        // Jalankan saat navigasi Livewire dan saat pertama load
        document.addEventListener('livewire:navigated', initCharts);
        document.addEventListener('DOMContentLoaded', initCharts);
        
        // Jalankan juga setiap kali Livewire update (polling)
        document.addEventListener('livewire:update', initCharts);
    </script>
</div>