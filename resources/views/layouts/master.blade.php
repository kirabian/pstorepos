<!DOCTYPE html>
<html lang="en" data-bs-theme="{{ Auth::user()->theme_mode === 'dark' ? 'dark' : 'light' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'CORE | Premium Admin Dashboard' }}</title>

    {{-- FONTS --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    {{-- CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

    @php
        // LOGIKA ACCENT COLOR DARI DATABASE
        $colors = [
            'teal'   => '0, 173, 181',   // #00ADB5
            'purple' => '139, 92, 246',
            'blue'   => '59, 130, 246',
            'green'  => '16, 185, 129',
            'yellow' => '245, 158, 11',
            'red'    => '239, 68, 68',
        ];
        $userColorKey = Auth::user()->theme_color ?? 'teal';
        $accentRGB = $colors[$userColorKey] ?? $colors['teal'];
    @endphp

    <style>
        /* --- 1. THEME ENGINE (Definisi Warna) --- */
        :root {
            /* Warna Statis Palette (Tidak Berubah) */
            --ps-dark-bg: #222831;
            --ps-dark-card: #393E46;
            --ps-light-text: #EEEEEE;
            
            /* Accent Dinamis (Berubah sesuai Pilihan User) */
            --ps-accent-rgb: {{ $accentRGB }};
            --ps-accent: rgb(var(--ps-accent-rgb));

            /* --- DEFAULT (LIGHT MODE) --- */
            --bg-body: #EEEEEE;           /* Background Utama */
            --bg-surface: #FFFFFF;        /* Warna Navbar/Card */
            --text-main: #222831;         /* Teks Utama (Hitam) */
            --text-muted: #6c757d;        /* Teks Muted */
            --border-color: #dee2e6;      /* Garis Batas */
            --navbar-bg: rgba(255, 255, 255, 0.95);
            --sidebar-bg: #222831;        /* Sidebar selalu gelap */
            --sidebar-text: #EEEEEE;
        }

        /* --- DARK MODE OVERRIDES --- */
        [data-bs-theme="dark"] {
            --bg-body: #222831;           /* Background Utama Gelap */
            --bg-surface: #393E46;        /* Navbar/Card Abu Gelap */
            --text-main: #EEEEEE;         /* Teks Utama Putih */
            --text-muted: #ADB5BD;        /* Teks Muted Terang */
            --border-color: #4a505a;      /* Garis Batas Gelap */
            --navbar-bg: rgba(57, 62, 70, 0.95);
            --sidebar-bg: #1a1d24;        /* Sidebar lebih gelap lagi */
        }

        /* --- 2. GLOBAL STYLES --- */
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            margin: 0;
            overflow-x: hidden; /* Mencegah scroll samping */
            transition: background-color 0.3s, color 0.3s;
        }

        /* FLEX LAYOUT (PENTING AGAR TIDAK BERANTAKAN) */
        #wrapper {
            display: flex;
            width: 100%;
            min-height: 100vh;
            position: relative;
        }

        #content {
            width: 100%;
            flex-grow: 1; /* Isi sisa ruang di sebelah sidebar */
            display: flex;
            flex-direction: column;
            background-color: var(--bg-body);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            min-width: 0; /* Mencegah overflow flex item */
            position: relative;
        }

        /* --- 3. UTILITY CLASSES (Kelas 'Bunglon') --- */
        
        /* Gunakan ini pengganti bg-white */
        .bg-surface-theme {
            background-color: var(--bg-surface) !important;
            color: var(--text-main) !important;
            transition: background-color 0.3s, color 0.3s;
        }

        /* Gunakan ini pengganti text-dark */
        .text-theme-main {
            color: var(--text-main) !important;
        }

        .text-theme-muted {
            color: var(--text-muted) !important;
        }

        .border-theme {
            border-color: var(--border-color) !important;
        }

        /* Scrollbar Keren */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--ps-accent); border-radius: 10px; }
    </style>

    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    {{-- OVERLAY MOBILE --}}
    <div id="sidebar-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.5); z-index: 1045; opacity: 0; transition: opacity 0.3s;"></div>

    <div id="wrapper">
        @auth
            {{-- SIDEBAR --}}
            @include('layouts.partials.sidebar')
            
            {{-- USER STATUS TRACKER (Opsional) --}}
            @livewire('user-status-handler')
        @endauth

        <div id="content">
            @auth 
                {{-- NAVBAR --}}
                @include('layouts.partials.navbar') 
            @endauth
            
            {{-- MAIN CONTENT SLOT --}}
            <main class="{{ Auth::check() ? 'p-3 p-md-5' : '' }} flex-grow-1 animate__animated animate__fadeIn">
                <div class="{{ Auth::check() ? 'container-fluid' : '' }}">
                    {{ $slot }}
                </div>
            </main>
            
            @auth 
                {{-- FOOTER --}}
                @include('layouts.partials.footer') 
            @endauth
        </div>
    </div>

    @livewireScripts
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Sidebar Toggle Logic
            const toggleBtn = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');

            if (toggleBtn && sidebar) {
                toggleBtn.addEventListener('click', (e) => {
                    e.preventDefault(); e.stopPropagation();
                    if (window.innerWidth >= 992) {
                        sidebar.classList.toggle('minimized');
                    } else {
                        sidebar.classList.toggle('show-mobile');
                        if (overlay) {
                            overlay.style.display = 'block';
                            setTimeout(() => overlay.style.opacity = '1', 10);
                        }
                    }
                });
            }

            // Close Overlay Logic
            if (overlay) {
                overlay.addEventListener('click', () => {
                    if (sidebar) sidebar.classList.remove('show-mobile');
                    overlay.style.opacity = '0';
                    setTimeout(() => overlay.style.display = 'none', 300);
                });
            }

            // Resize Reset
            window.addEventListener('resize', () => {
                if (window.innerWidth >= 992) {
                    if (sidebar) sidebar.classList.remove('show-mobile');
                    if (overlay) overlay.style.display = 'none';
                }
            });

            // 2. Navbar Scroll Logic
            const navbar = document.getElementById('main-navbar');
            if(navbar) {
                const checkScroll = () => {
                    if (window.scrollY > 20) navbar.classList.add('scrolled');
                    else navbar.classList.remove('scrolled');
                };
                window.addEventListener('scroll', checkScroll);
                checkScroll();
            }

            // 3. Listener Theme Change (untuk refresh chart jika perlu)
            document.addEventListener('themeChanged', function() {
                location.reload(); 
            });
        });
    </script>
</body>
</html>