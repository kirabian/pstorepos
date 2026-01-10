<!DOCTYPE html>
<html lang="en" data-bs-theme="{{ Auth::user()->theme_mode === 'dark' ? 'dark' : 'light' }}">

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

    @php
        // DEFINISI WARNA ACCENT
        $colors = [
            'teal'   => '0, 173, 181',   // #00ADB5
            'purple' => '139, 92, 246',  // #8B5CF6
            'blue'   => '59, 130, 246',  // #3B82F6
            'green'  => '16, 185, 129',  // #10B981
            'yellow' => '245, 158, 11',  // #F59E0B
            'red'    => '239, 68, 68',   // #EF4444
            'pink'   => '236, 72, 153',  // #EC4899
            'orange' => '249, 115, 22',  // #F97316
        ];

        $userMode = Auth::user()->theme_mode ?? 'system';
        $userColorKey = Auth::user()->theme_color ?? 'teal';
        $accentRGB = $colors[$userColorKey] ?? $colors['teal'];
    @endphp

    <style>
        :root {
            /* --- DYNAMIC ACCENT COLOR --- */
            --ps-accent-rgb: {{ $accentRGB }};
            --ps-accent: rgb(var(--ps-accent-rgb));

            /* --- DEFAULT LIGHT THEME VARIABLES --- */
            --ps-dark: #222831;       /* Warna Kontras Utama */
            --ps-secondary: #393E46;
            --ps-light: #F9FAFB;      /* Background Light */
            --ps-text: #111827;       /* Text Light Mode */
            --ps-sidebar-bg: #222831; /* Sidebar selalu gelap biar elegan */
            --ps-sidebar-text: #EEEEEE;
            --ps-card-bg: #FFFFFF;
            --ps-body-bg: #F3F4F6;
            --ps-border: #E5E7EB;
        }

        /* --- DARK THEME OVERRIDES --- */
        /* Jika user pilih Dark ATAU System (dan OS dark) */
        @if($userMode === 'dark')
            :root {
                --ps-light: #222831;      /* Background Dark */
                --ps-text: #EEEEEE;       /* Text Dark Mode */
                --ps-sidebar-bg: #191D24; 
                --ps-card-bg: #2A303C;
                --ps-body-bg: #222831;
                --ps-border: #393E46;
            }
        @elseif($userMode === 'system')
            @media (prefers-color-scheme: dark) {
                :root {
                    --ps-light: #222831;
                    --ps-text: #EEEEEE;
                    --ps-sidebar-bg: #191D24;
                    --ps-card-bg: #2A303C;
                    --ps-body-bg: #222831;
                    --ps-border: #393E46;
                }
            }
        @endif

        /* --- GLOBAL STYLES --- */
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--ps-body-bg);
            color: var(--ps-text);
            margin: 0;
            overflow-x: hidden;
            transition: background-color 0.3s ease, color 0.3s ease;
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
            background-color: var(--ps-body-bg);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            min-width: 0;
            position: relative;
        }

        /* Navbar Styling */
        #main-navbar {
            position: sticky;
            top: 0;
            z-index: 1040;
            width: 100%;
            background: rgba(var(--ps-card-bg-rgb, 255, 255, 255), 0.95);
            border-bottom: 1px solid var(--ps-border);
            padding: 0.75rem 1.5rem;
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            backdrop-filter: blur(10px);
        }

        @if($userMode === 'dark')
            #main-navbar { background: rgba(34, 40, 49, 0.95); }
        @endif

        #main-navbar.scrolled {
            top: 20px;
            width: 95%;
            margin-left: auto;
            margin-right: auto;
            border-radius: 50px;
            background: rgba(var(--ps-card-bg-rgb, 255, 255, 255), 0.85) !important;
            @if($userMode === 'dark') background: rgba(42, 48, 60, 0.9) !important; @endif
            border: 1px solid rgba(var(--ps-accent-rgb), 0.3);
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.1);
            padding: 0.5rem 1.5rem;
        }

        @media (max-width: 991.98px) {
            #main-navbar { padding: 0.5rem 1rem; }
            #main-navbar.scrolled { width: 92%; top: 15px; }
        }

        #sidebar-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0; width: 100vw; height: 100vh;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1045;
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }
        #sidebar-overlay.show { display: block; opacity: 1; }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(var(--ps-accent-rgb), 0.3); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--ps-accent); }

        .text-accent { color: var(--ps-accent) !important; }
        .bg-accent { background-color: var(--ps-accent) !important; }
        .border-accent { border-color: var(--ps-accent) !important; }
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Navbar Logic
            const navbar = document.getElementById('main-navbar');
            if(navbar) {
                function checkScroll() {
                    if (window.scrollY > 20) { navbar.classList.add('scrolled'); } 
                    else { navbar.classList.remove('scrolled'); }
                }
                window.addEventListener('scroll', checkScroll);
                checkScroll();
            }

            // Sidebar Toggle
            const toggleBtn = document.getElementById('sidebarToggle'),
                sidebar = document.getElementById('sidebar'),
                overlay = document.getElementById('sidebar-overlay');

            if (toggleBtn && sidebar) {
                toggleBtn.addEventListener('click', (e) => {
                    e.preventDefault(); e.stopPropagation();
                    if (window.innerWidth >= 992) { sidebar.classList.toggle('minimized'); } 
                    else { sidebar.classList.toggle('show-mobile'); if (overlay) overlay.classList.toggle('show'); }
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
    </script>
</body>
</html>