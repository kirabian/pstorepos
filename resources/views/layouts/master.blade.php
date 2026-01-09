<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="PSTORE Inventory - Premium Admin Dashboard System">
    <title>{{ $title ?? 'CORE | Premium Admin Dashboard' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

    <style>
        :root {
            --core-black: #09090b;
            --core-white: #ffffff;
            /* Background Abu-abu Premium agar Navbar Putih terlihat Jelas */
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

        /* Logic Konten */
        main {
            /* Hapus margin-top besar karena kita pakai sticky, bukan fixed */
            padding-top: 1rem; 
            transition: all 0.3s ease;
        }

        /* Overlay untuk Mobile */
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

        /* Scrollbar Customization */
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

        .shimmer {
            background: #f6f7f8;
            background-image: linear-gradient(90deg, #f6f7f8 0%, #edeef1 20%, #f6f7f8 40%, #f6f7f8 100%);
            background-size: 800px 100%;
            animation: shim 1.2s infinite linear;
        }

        @keyframes shim {
            0% {
                background-position: -468px 0;
            }

            100% {
                background-position: 468px 0;
            }
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

    @livewireScripts
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- LOGIC NAVBAR SCROLL (ISLAND EFFECT) ---
            const navbar = document.getElementById('main-navbar');
            
            if(navbar) {
                if (window.scrollY > 10) {
                    navbar.classList.add('scrolled');
                }

                window.addEventListener('scroll', function() {
                    if (window.scrollY > 10) { 
                        navbar.classList.add('scrolled');
                    } else {
                        navbar.classList.remove('scrolled');
                    }
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
                        // Desktop: Minimize Sidebar
                        sidebar.classList.toggle('minimized');
                    } else {
                        // Mobile: Show Off-Canvas
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
                window.addEventListener(evt, resetIdleTimer, {
                    passive: true
                })
            );
            @endauth
        });

        document.addEventListener('livewire:init', () => {
            Livewire.on('echo:pstore-channel,inventory.updated', (event) => {
                setTimeout(() => {
                    let alertEl = document.querySelector('.alert');
                    if (alertEl) {
                        alertEl.classList.add('animate__fadeOutRight');
                        setTimeout(() => alertEl.remove(), 1000);
                    }
                }, 7000);
            });

            // Global SweetAlert Listener
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

    {{-- [PENTING] Masukkan Komponen Notifikasi Disini --}}
    @auth
        @livewire('partials.login-notification')
    @endauth

    {{-- Slot untuk script tambahan dari komponen Livewire --}}
    @stack('scripts')

</body>

</html>