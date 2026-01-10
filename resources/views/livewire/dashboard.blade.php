<div wire:poll.15s>
    @if (session()->has('info'))
        <div class="alert alert-dark border-0 shadow-premium rounded-4 mb-4 d-flex align-items-center animate__animated animate__fadeInDown">
            <i class="fas fa-check-circle me-3 ms-2 text-white"></i>
            <div class="text-white small fw-bold">{{ session('info') }}</div>
            <button type="button" class="btn-close btn-close-white ms-auto small shadow-none" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- HEADER --}}
    <div class="row align-items-center mb-5">
        <div class="col-12 col-md">
            <div class="d-flex align-items-center gap-3 mb-1">
                {{-- Logo Container --}}
                <div class="bg-surface-theme p-2 rounded-3 shadow-sm border-theme border">
                    <i class="fas fa-shield-alt fs-4 text-theme-main" style="color: var(--ps-accent) !important;"></i>
                </div>
                {{-- Judul: Gunakan text-theme-main agar otomatis putih di dark mode --}}
                <h1 class="fw-900 text-theme-main mb-0 display-6 tracking-tighter">Superadmin Center Test</h1>
            </div>
            <p class="text-theme-muted small fw-bold text-uppercase ms-5 ps-2" style="letter-spacing: 4px; opacity: 0.8; color: var(--ps-accent) !important;">PSTORE Core Intelligence System</p>
        </div>
        
        <div class="col-12 col-md-auto d-flex gap-3">
            {{-- Status Pill --}}
            <div class="glass-pill px-4 py-2 rounded-pill border border-theme d-flex align-items-center shadow-sm bg-surface-theme">
                <span class="pulse-accent me-2"></span>
                <span class="extra-small fw-900 text-theme-main">{{ $onlineUsersCount }} USERS ACTIVE</span>
            </div>
            <button wire:click="testSinyal" class="btn-premium-accent rounded-circle shadow-premium p-3">
                <i class="fas fa-broadcast-tower"></i>
            </button>
        </div>
    </div>

    {{-- STATS CARDS --}}
    <div class="row g-4 mb-5">
        @php
            $stats = [
                ['label' => 'Total Accounts', 'value' => $totalUsers, 'icon' => 'fa-users', 'desc' => 'Verified System'],
                ['label' => 'Active Branches', 'value' => $totalCabang, 'icon' => 'fa-store', 'desc' => 'National Operations'],
                ['label' => 'Warehouses', 'value' => $totalGudang, 'icon' => 'fa-warehouse', 'desc' => 'Logistics Central'],
                ['label' => 'Strategic Partners', 'value' => $totalDistributor, 'icon' => 'fa-truck', 'desc' => 'Supply Chain']
            ];
        @endphp

        @foreach($stats as $stat)
        <div class="col-12 col-sm-6 col-xl-3">
            {{-- Perhatikan class bg-surface-theme --}}
            <div class="card-premium h-100 p-4 bg-surface-theme border border-theme shadow-premium position-relative overflow-hidden">
                <div class="position-relative z-index-2">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <p class="text-uppercase extra-small fw-900 text-theme-muted mb-0">{{ $stat['label'] }}</p>
                        {{-- Icon Circle Theme --}}
                        <div class="icon-circle icon-circle-theme shadow-sm">
                            <i class="fas {{ $stat['icon'] }} small"></i>
                        </div>
                    </div>
                    <h2 class="fw-black text-theme-main mb-1">{{ number_format($stat['value']) }}</h2>
                    <p class="extra-small fw-bold text-theme-muted mb-0 opacity-50">{{ $stat['desc'] }}</p>
                </div>
                <img src="{{ asset('images/logo-pstore.png') }}" class="card-watermark opacity-5">
            </div>
        </div>
        @endforeach
    </div>

    {{-- CHARTS SECTION --}}
    <div class="row g-4 mb-5">
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-premium rounded-5 p-4 bg-surface-theme border border-theme">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-900 text-theme-main mb-0">System Traffic Analysis</h5>
                    <span class="badge bg-light text-dark rounded-pill extra-small px-3">Live Updates</span>
                </div>
                <div style="height: 350px;">
                    <canvas id="mainChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-premium rounded-5 p-4 bg-surface-theme border border-theme">
                <h5 class="fw-900 text-theme-main mb-4">Authority Distribution</h5>
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
        
        .card-premium {
            border-radius: 2rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
        }
        .card-premium:hover {
            transform: translateY(-5px);
            border-color: var(--ps-accent) !important;
            box-shadow: 0 10px 40px rgba(var(--ps-accent-rgb), 0.15) !important;
        }
        
        .icon-circle {
            width: 40px; height: 40px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
        }

        .btn-premium-accent {
            background: var(--ps-accent); color: #fff; border: none;
            transition: all 0.3s ease; width: 50px; height: 50px;
            display: flex; align-items: center; justify-content: center;
        }
        .btn-premium-accent:hover { 
            transform: rotate(15deg) scale(1.1); 
            box-shadow: 0 0 20px rgba(var(--ps-accent-rgb), 0.6);
        }

        .card-watermark {
            position: absolute; width: 120px; right: -20px; bottom: -20px;
            transform: rotate(-10deg); pointer-events: none; opacity: 0.05;
        }
        /* Filter watermark jadi putih di dark mode */
        [data-bs-theme="dark"] .card-watermark { filter: invert(1); opacity: 0.1; }

        .pulse-accent {
            width: 8px; height: 8px; background-color: var(--ps-accent); border-radius: 50%;
            animation: pulse-glow 2s infinite;
        }
        @keyframes pulse-glow {
            0% { box-shadow: 0 0 0 0 rgba(var(--ps-accent-rgb), 0.4); }
            70% { box-shadow: 0 0 0 10px rgba(var(--ps-accent-rgb), 0); }
            100% { box-shadow: 0 0 0 0 rgba(var(--ps-accent-rgb), 0); }
        }
    </style>

    <script>
        // CHART CONFIGURATION YANG ADAPTIF
        function initCharts() {
            const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
            const textColor = isDark ? '#EEEEEE' : '#222831';
            const gridColor = isDark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.05)';
            const accentColor = getComputedStyle(document.documentElement).getPropertyValue('--ps-accent').trim();

            const ctxMain = document.getElementById('mainChart');
            const ctxRole = document.getElementById('roleChart');

            if (ctxMain) {
                if (window.myChart1) window.myChart1.destroy();
                window.myChart1 = new Chart(ctxMain, {
                    type: 'line',
                    data: {
                        labels: ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL'],
                        datasets: [{
                            label: 'Platform Load',
                            data: [65, 59, 80, 81, 56, 85, 90],
                            borderColor: accentColor, // Pakai Warna Custom
                            borderWidth: 3,
                            pointBackgroundColor: isDark ? '#fff' : '#000',
                            tension: 0.4,
                            fill: true,
                            backgroundColor: (context) => {
                                const ctx = context.chart.ctx;
                                const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                                gradient.addColorStop(0, accentColor); // Gradient dari accent
                                gradient.addColorStop(1, 'rgba(0,0,0,0)');
                                return gradient;
                            }
                        }]
                    },
                    options: { 
                        maintainAspectRatio: false, 
                        plugins: { legend: { display: false } },
                        scales: { 
                            y: { ticks: { color: textColor }, grid: { color: gridColor } }, 
                            x: { ticks: { color: textColor }, grid: { display: false } } 
                        }
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
                            backgroundColor: [
                                accentColor, // Warna 1: Accent
                                isDark ? '#555' : '#333',
                                isDark ? '#777' : '#888', 
                                isDark ? '#999' : '#eee'
                            ],
                            borderWidth: 0,
                            hoverOffset: 20
                        }]
                    },
                    options: { 
                        maintainAspectRatio: false, 
                        cutout: '80%',
                        plugins: { 
                            legend: { 
                                position: 'bottom', 
                                labels: { 
                                    usePointStyle: true, 
                                    font: { weight: 'bold', size: 10 },
                                    color: textColor 
                                } 
                            } 
                        }
                    }
                });
            }
        }

        document.addEventListener('livewire:navigated', initCharts);
        document.addEventListener('DOMContentLoaded', initCharts);
        document.addEventListener('livewire:update', initCharts);
    </script>
</div>