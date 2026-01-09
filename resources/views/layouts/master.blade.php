<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="PSTORE Inventory - Premium Admin Dashboard System">
    <title>{{ $title ?? 'CORE | Premium Admin Dashboard' }}</title>

    @auth
        <meta name="user-id" content="{{ Auth::id() }}">
        <meta name="user-role" content="{{ Auth::user()->role }}">
    @endauth

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

    <style>
        :root {
            --core-black: #09090b;
            --core-white: #ffffff;
            --core-bg: #f2f4f7;
            --core-gray-light: #f8f9fa;
            --core-gray-border: #eee;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--core-bg);
            color: var(--core-black);
            margin: 0;
            overflow-x: hidden;
            font-display: swap;
        }

        #wrapper {
            display: flex;
            width: 100%;
            min-height: 100vh;
            position: relative;
        }

        #content {
            width: 100%;
            display: flex;
            flex-direction: column;
            background-color: var(--core-bg);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            min-width: 0;
            position: relative;
        }

        main {
            padding-top: 1rem;
            transition: all 0.3s ease;
        }

        #sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1045;
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }

        #sidebar-overlay.show {
            display: block;
            opacity: 1;
        }

        ::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }

        ::-webkit-scrollbar-track {
            background: var(--core-gray-light);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--core-black);
            border-radius: 10px;
        }
    </style>

    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div id="sidebar-overlay"></div>

    <div id="wrapper">
        @auth
            @include('layouts.partials.sidebar')
            {{-- Komponen Status User (Online/Offline) --}}
            @livewire('user-status-handler') 
        @endauth

        <div id="content">
            @auth @include('layouts.partials.navbar') @endauth

            <main class="{{ Auth::check() ? 'p-3 p-md-5' : '' }} flex-grow-1 animate__animated animate__fadeIn">
                <div class="{{ Auth::check() ? 'container-fluid' : '' }}">
                    {{ $slot }}
                </div>
            </main>

            @auth @include('layouts.partials.footer') @endauth
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- Navbar Scroll Effect ---
            const navbar = document.getElementById('main-navbar');
            if (navbar) {
                if (window.scrollY > 10) navbar.classList.add('scrolled');
                window.addEventListener('scroll', function() {
                    if (window.scrollY > 10) navbar.classList.add('scrolled');
                    else navbar.classList.remove('scrolled');
                });
            }

            // --- Sidebar Toggle Logic ---
            const toggleBtn = document.getElementById('sidebarToggle'),
                sidebar = document.getElementById('sidebar'),
                overlay = document.getElementById('sidebar-overlay');

            if (toggleBtn && sidebar) {
                toggleBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    if (window.innerWidth >= 992) {
                        sidebar.classList.toggle('minimized');
                    } else {
                        sidebar.classList.toggle('show-mobile');
                        if (overlay) overlay.classList.toggle('show');
                    }
                });
            }

            if (overlay) {
                overlay.addEventListener('click', () => {
                    if (sidebar) sidebar.classList.remove('show-mobile');
                    overlay.classList.remove('show');
                });
            }

            window.addEventListener('resize', () => {
                if (window.innerWidth >= 992) {
                    if (sidebar) sidebar.classList.remove('show-mobile');
                    if (overlay) overlay.classList.remove('show');
                }
            });

            // --- User Idle/Active Detection ---
            @auth
            let idleTimer;
            let isCurrentlyOffline = false;
            const statusDelay = 10000;

            function resetIdleTimer() {
                if (isCurrentlyOffline) {
                    console.log('User kembali aktif, mengirim sinyal online...');
                    Livewire.dispatch('setUserOnline');
                    isCurrentlyOffline = false;
                }
                clearTimeout(idleTimer);
                idleTimer = setTimeout(() => {
                    console.log('Status: Diam terdeteksi, mengirim sinyal offline...');
                    Livewire.dispatch('setUserOffline');
                    isCurrentlyOffline = true;
                }, statusDelay);
            }

            resetIdleTimer();
            ['mousemove', 'mousedown', 'keypress', 'touchstart', 'scroll', 'click'].forEach(evt =>
                window.addEventListener(evt, resetIdleTimer, { passive: true })
            );
            @endauth
        });

        // --- Global Livewire Listeners ---
        document.addEventListener('livewire:init', () => {
            // Listener untuk notifikasi barang (contoh)
            Livewire.on('echo:pstore-channel,inventory.updated', (event) => {
                setTimeout(() => {
                    let alertEl = document.querySelector('.alert');
                    if (alertEl) {
                        alertEl.classList.add('animate__fadeOutRight');
                        setTimeout(() => alertEl.remove(), 1000);
                    }
                }, 7000);
            });

            // Listener Global SweetAlert
            Livewire.on('swal', (data) => {
                const payload = data[0];
                Swal.fire({
                    title: payload.title,
                    text: payload.text,
                    icon: payload.icon,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            });
        });
    </script>

    {{-- ================================================================= --}}
    {{-- LOGIKA NOTIFIKASI LOGIN REALTIME (TANPA COMPONENT LIVEWIRE LAGI) --}}
    {{-- ================================================================= --}}
    @php
        $authUser = auth()->user();
        $notifChannels = [];

        if ($authUser) {
            // Channel untuk Superadmin
            if ($authUser->role === 'superadmin') {
                $notifChannels[] = 'superadmin-notify';
            }
            // Channel untuk Audit (Berdasarkan Cabang)
            if ($authUser->role === 'audit' && isset($authUser->access_cabang_ids)) {
                foreach ($authUser->access_cabang_ids as $branchId) {
                     $notifChannels[] = 'branch-notify.' . $branchId;
                }
            }
        }
    @endphp

    @if(!empty($notifChannels))
        {{-- Elemen Audio & Data --}}
        <audio id="loginSound" src="{{ asset('images/notif.mp3') }}" preload="auto" style="display: none;"></audio>
        <div id="login-notification-data" data-channels="{{ json_encode($notifChannels) }}"></div>

        <script>
            // Fungsi inisialisasi notifikasi
            function initLoginNotificationSystem() {
                const dataEl = document.getElementById('login-notification-data');
                if (!dataEl) return;

                let activeChannels = [];
                try {
                    activeChannels = JSON.parse(dataEl.getAttribute('data-channels'));
                } catch (e) { console.error('Gagal parse channel', e); return; }

                const audioEl = document.getElementById('loginSound');

                // Pastikan Echo sudah ada
                if (typeof window.Echo === 'undefined') {
                    console.warn('Laravel Echo belum dimuat. Menunggu...');
                    return;
                }

                console.log('Mendengarkan Channel Notifikasi:', activeChannels);

                activeChannels.forEach(channelName => {
                    // Bersihkan listener lama untuk mencegah duplikasi
                    window.Echo.leave(channelName);

                    // Listen ke Private Channel
                    window.Echo.private(channelName)
                        .listen('UserLoggedIn', (e) => { // Pastikan nama event sesuai class PHP: UserLoggedIn
                            console.log('🔔 NOTIFIKASI LOGIN:', e);

                            // 1. Play Audio
                            if (audioEl) {
                                audioEl.currentTime = 0;
                                audioEl.play().catch(err => console.warn('Audio autoplay diblokir:', err));
                            }

                            // 2. Tampilkan SweetAlert
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    title: 'User Login Terdeteksi!',
                                    html: `
                                        <div class="d-flex align-items-center gap-3 text-start">
                                            <div class="bg-primary text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                                                <i class="fas fa-user"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold fs-5">${e.user_name}</div>
                                                <div class="text-muted small text-uppercase">${e.user_role}</div>
                                                <div class="text-primary small fw-bold"><i class="fas fa-map-marker-alt me-1"></i> ${e.location || 'Lokasi tidak diketahui'}</div>
                                            </div>
                                        </div>
                                    `,
                                    position: 'top-end',
                                    icon: 'info',
                                    toast: true,
                                    showConfirmButton: false,
                                    timer: 6000,
                                    timerProgressBar: true,
                                    didOpen: (toast) => {
                                        toast.addEventListener('mouseenter', Swal.stopTimer)
                                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                                    }
                                });
                            }
                        });
                });
            }

            // Jalankan saat load awal
            document.addEventListener('DOMContentLoaded', () => {
                // Beri sedikit delay agar Echo selesai inisialisasi di app.js
                setTimeout(initLoginNotificationSystem, 1000);
            });
        </script>
    @endif

    @livewireScripts
</body>

</html>