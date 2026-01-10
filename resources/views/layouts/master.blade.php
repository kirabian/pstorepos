<!DOCTYPE html>
<html lang="en" data-bs-theme="{{ Auth::user()->theme_mode === 'dark' ? 'dark' : 'light' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'CORE | Premium Admin Dashboard' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

    @php
        // LOGIKA ACCENT COLOR DARI DATABASE
        $colors = [
            'teal'   => '0, 173, 181',   // #00ADB5 (Palette Utama)
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
        /* --- 1. DEFINISI WARNA (THEME ENGINE) --- */
        :root {
            /* Warna Statis (Tetap) */
            --ps-teal: #00ADB5;
            --ps-dark-bg: #222831;
            --ps-dark-surface: #393E46;
            --ps-light-text: #EEEEEE;
            
            /* Accent Dinamis */
            --ps-accent-rgb: {{ $accentRGB }};
            --ps-accent: rgb(var(--ps-accent-rgb));

            /* --- LIGHT MODE DEFAULTS --- */
            --bg-body: #EEEEEE;           /* Background Halaman Terang */
            --bg-card: #FFFFFF;           /* Warna Card Terang */
            --text-main: #222831;         /* Teks Utama Gelap */
            --text-muted: #6c757d;        /* Teks Muted */
            --border-color: #dee2e6;
            --navbar-bg: rgba(255, 255, 255, 0.95);
            --sidebar-bg: #222831;        /* Sidebar tetap gelap biar elegan */
            --sidebar-text: #EEEEEE;
        }

        /* --- DARK MODE OVERRIDES --- */
        [data-bs-theme="dark"] {
            --bg-body: #222831;           /* Background Gelap (Palette) */
            --bg-card: #393E46;           /* Card agak terang dikit (Palette) */
            --text-main: #EEEEEE;         /* Teks jadi Putih (Palette) */
            --text-muted: #ADB5BD;        
            --border-color: #4a505a;
            --navbar-bg: rgba(34, 40, 49, 0.95);
            --sidebar-bg: #1a1d24;        /* Sidebar lebih gelap lagi */
        }

        /* --- 2. GLOBAL UTILITY CLASSES (PAKAI INI DI MENU LAIN) --- */
        
        body {
            background-color: var(--bg-body);
            color: var(--text-main);
            transition: background-color 0.3s, color 0.3s;
        }

        /* Pengganti .bg-white dan .text-dark */
        .bg-surface-theme {
            background-color: var(--bg-card) !important;
            color: var(--text-main) !important;
            transition: background-color 0.3s, color 0.3s;
        }

        .text-theme-main {
            color: var(--text-main) !important;
        }

        .text-theme-muted {
            color: var(--text-muted) !important;
        }

        .border-theme {
            border-color: var(--border-color) !important;
        }

        /* Khusus Icon Circle agar adaptif */
        .icon-circle-theme {
            background-color: var(--bg-body); 
            color: var(--text-main);
        }
        [data-bs-theme="dark"] .icon-circle-theme {
            background-color: #222831; 
            color: var(--ps-accent);
        }

        /* --- 3. LAYOUT SPECIFIC STYLES --- */
        #content {
            background-color: var(--bg-body);
            transition: all 0.3s;
        }

        #main-navbar {
            background: var(--navbar-bg);
            border-bottom: 1px solid var(--border-color);
            backdrop-filter: blur(10px);
        }

        #main-navbar.scrolled {
            background: var(--navbar-bg) !important;
            border: 1px solid var(--ps-accent);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--ps-accent); border-radius: 10px; }
    </style>

    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div id="wrapper">
        @auth
            @include('layouts.partials.sidebar')
            @livewire('user-status-handler')
        @endauth

        <div id="content" class="d-flex flex-column min-vh-100">
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        // Update Chart Warna saat ganti tema
        document.addEventListener('themeChanged', function() {
            location.reload(); // Reload simple untuk refresh chart color
        });
    </script>
</body>
</html>