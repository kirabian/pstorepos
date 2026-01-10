<nav id="main-navbar" class="navbar navbar-expand-lg transition-all">
    <div class="container-fluid px-0">
        <div class="d-flex align-items-center w-100">
            {{-- Tombol Toggle --}}
            <button class="btn bg-surface-theme border-theme shadow-sm me-3 rounded-circle d-flex align-items-center justify-content-center" 
                id="sidebarToggle" 
                aria-label="Toggle Sidebar"
                style="width: 40px; height: 40px; min-width: 40px; z-index: 1055;">
                <i class="fas fa-bars-staggered text-theme-main"></i>
            </button>
            
            <div class="d-flex align-items-center me-auto">
                {{-- Logo di Navbar (bisa disembunyikan di desktop jika mau) --}}
                <img src="{{ asset('images/logo-pstore.png') }}" alt="PSTORE" width="100" height="25" style="height: 25px; width: auto;" fetchpriority="high">
            </div>

            <div class="ms-auto d-flex align-items-center">
                @auth
                    <div class="dropdown border-start border-theme ps-3 ms-2">
                        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle hide-caret p-1 rounded-pill border border-theme bg-surface-theme shadow-sm" data-bs-toggle="dropdown" aria-label="Profile">
                            <div class="text-end me-3 d-none d-lg-block ms-2">
                                <p class="mb-0 fw-bold text-theme-main" style="font-size: 0.8rem; line-height: 1.2;">{{ Auth::user()->nama_lengkap }}</p>
                                <p class="mb-0 text-uppercase fw-bold" style="font-size: 0.6rem; color: var(--ps-accent);">{{ str_replace('_', ' ', Auth::user()->role) }}</p>
                            </div>
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->nama_lengkap) }}&background=00ADB5&color=fff&bold=true" class="rounded-circle border border-theme" width="32" height="32" alt="Avatar">
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-xl border-0 mt-3 p-2 rounded-4 bg-surface-theme">
                            <li>
                                <form method="POST" action="{{ route('logout') }}"> @csrf
                                    <button type="submit" class="dropdown-item rounded-3 py-2 text-danger fw-bold shadow-none border-0 bg-transparent w-100 text-start hover-bg-light">
                                        <i class="fas fa-sign-out-alt me-2"></i> Sign Out
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</nav>

<style>
    /* Styling khusus Navbar agar mengikuti tema */
    #main-navbar {
        background: var(--navbar-bg);
        border-bottom: 1px solid var(--border-color);
        transition: all 0.4s ease;
    }
    
    #main-navbar.scrolled {
        width: 95%;
        top: 20px;
        margin: 0 auto;
        border-radius: 50px;
        background: var(--navbar-bg) !important;
        border: 1px solid var(--ps-accent);
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }

    /* Dropdown adjustment */
    .dropdown-menu.bg-surface-theme {
        background-color: var(--bg-surface);
        border: 1px solid var(--border-color);
    }
    .dropdown-item:hover {
        background-color: rgba(var(--ps-accent-rgb), 0.1);
    }
</style>