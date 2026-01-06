<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="PSTORE Inventory - Premium Admin Dashboard System">
    <title>{{ $title ?? 'CORE | Premium Admin Dashboard' }}</title>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    {{-- Bootstrap & Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

    <style>
        :root {
            --core-primary: #000000;
            --core-bg: #f4f6f9; /* Light Gray Background for Content */
            --core-sidebar-width: 280px;
            --core-sidebar-collapsed-width: 90px;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--core-bg);
            color: #333;
            margin: 0;
            overflow-x: hidden;
        }

        #wrapper {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        #content {
            width: 100%;
            display: flex;
            flex-direction: column;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            min-width: 0;
        }

        /* Overlay for Mobile */
        #sidebar-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100vw; height: 100vh;
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(2px);
            z-index: 1045;
            transition: opacity 0.3s ease;
        }

        #sidebar-overlay.show { display: block; }

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* Shimmer Effect Helper */
        .shimmer {
            background: #f6f7f8;
            background-image: linear-gradient(90deg, #f6f7f8 0%, #edeef1 20%, #f6f7f8 40%, #f6f7f8 100%);
            background-size: 800px 100%;
            animation: shim 1.2s infinite linear;
        }
        @keyframes shim {
            0% { background-position: -468px 0; }
            100% { background-position: 468px 0; }
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
            
            <main class="flex-grow-1 p-3 p-md-4 animate__animated animate__fadeIn">
                <div class="{{ Auth::check() ? 'container-fluid' : '' }}">
                    {{ $slot }}
                </div>
            </main>
            
            @auth @include('layouts.partials.footer') @endauth
        </div>
    </div>

    @livewireScripts
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sidebar Toggle Logic
            const toggleBtn = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');

            if (toggleBtn && sidebar) {
                toggleBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    if (window.innerWidth > 992) {
                        sidebar.classList.toggle('minimized');
                    } else {
                        sidebar.classList.toggle('show-mobile');
                        overlay.classList.toggle('show');
                    }
                });
            }

            if (overlay) {
                overlay.addEventListener('click', () => {
                    sidebar.classList.remove('show-mobile');
                    overlay.classList.remove('show');
                });
            }

            @auth
            // Idle Timer Logic
            let idleTimer;
            let isCurrentlyOffline = false;
            const statusDelay = 10000; // 10 Detik

            function resetIdleTimer() {
                if (isCurrentlyOffline) {
                    console.log('User kembali aktif...');
                    Livewire.dispatch('setUserOnline');
                    isCurrentlyOffline = false;
                }
                clearTimeout(idleTimer);
                idleTimer = setTimeout(() => {
                    console.log('User idle...');
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

        // Flash Message Fade Out
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
        });
    </script>
</body>
</html>