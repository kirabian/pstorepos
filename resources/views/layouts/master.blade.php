<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'CORE Dashboard' }}</title>

    {{-- Fonts & Icons --}}
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

    <style>
        :root {
            --core-bg: #f4f6f9;
            --sidebar-width: 280px;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--core-bg);
            margin: 0;
            overflow-x: hidden; /* Mencegah scroll horizontal di HP */
        }

        #wrapper {
            display: flex;
            width: 100%;
            min-height: 100vh;
            position: relative;
        }

        #content {
            width: 100%;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            min-width: 0; /* Fix flexbox overflow text issue */
            transition: margin-left 0.3s ease;
        }

        /* Overlay Hitam untuk Mobile */
        #mobile-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(3px);
            z-index: 1045; /* Di bawah Sidebar (1050), di atas Navbar (1040) */
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        #mobile-overlay.show {
            display: block;
            opacity: 1;
        }
    </style>

    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div id="mobile-overlay"></div>

    <div id="wrapper">
        @auth
            @include('layouts.partials.sidebar')
            @livewire('user-status-handler')
        @endauth

        <div id="content">
            @auth @include('layouts.partials.navbar') @endauth
            
            <main class="flex-grow-1 p-3 p-md-4">
                <div class="{{ Auth::check() ? 'container-fluid' : '' }}">
                    {{ $slot }}
                </div>
            </main>
            
            @auth @include('layouts.partials.footer') @endauth
        </div>
    </div>

    @livewireScripts
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobile-overlay');
            const toggleBtns = document.querySelectorAll('.sidebar-toggler'); // Class baru untuk tombol

            // Fungsi Toggle Sidebar Mobile
            function toggleSidebar() {
                if (window.innerWidth <= 991.98) {
                    sidebar.classList.toggle('show-mobile');
                    overlay.classList.toggle('show');
                    document.body.style.overflow = sidebar.classList.contains('show-mobile') ? 'hidden' : '';
                } else {
                    sidebar.classList.toggle('collapsed');
                }
            }

            // Event Listeners untuk semua tombol toggle
            toggleBtns.forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    toggleSidebar();
                });
            });

            // Tutup sidebar saat klik overlay (Mobile)
            if(overlay) {
                overlay.addEventListener('click', () => {
                    sidebar.classList.remove('show-mobile');
                    overlay.classList.remove('show');
                    document.body.style.overflow = '';
                });
            }

            // Reset saat resize window
            window.addEventListener('resize', () => {
                if (window.innerWidth > 991.98) {
                    sidebar.classList.remove('show-mobile');
                    overlay.classList.remove('show');
                    document.body.style.overflow = '';
                }
            });
        });
    </script>
</body>
</html>