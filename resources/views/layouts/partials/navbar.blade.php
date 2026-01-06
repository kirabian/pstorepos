<nav id="main-navbar" class="navbar navbar-expand-lg transition-all">
    <div class="container-fluid px-0">
        <div class="d-flex align-items-center w-100">
            <button class="btn bg-white border shadow-sm me-3 rounded-circle d-flex align-items-center justify-content-center" 
                id="sidebarToggle" 
                aria-label="Toggle Sidebar"
                style="width: 40px; height: 40px; min-width: 40px; z-index: 1055;">
                <i class="fas fa-bars-staggered text-dark"></i>
            </button>
            
            <div class="d-flex align-items-center me-auto">
                <img src="{{ asset('images/logo-pstore.png') }}" alt="PSTORE" width="100" height="25" style="height: 25px; width: auto;" fetchpriority="high">
            </div>

            <div class="ms-auto d-flex align-items-center">
                @auth
                    <div class="dropdown border-start ps-3 ms-2">
                        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle hide-caret p-1 rounded-pill border bg-white shadow-sm" data-bs-toggle="dropdown" aria-label="Profile">
                            <div class="text-end me-3 d-none d-lg-block ms-2">
                                <p class="mb-0 fw-bold text-black" style="font-size: 0.8rem; line-height: 1.2;">{{ Auth::user()->nama_lengkap }}</p>
                                <p class="mb-0 text-muted text-uppercase fw-bold" style="font-size: 0.6rem;">{{ str_replace('_', ' ', Auth::user()->role) }}</p>
                            </div>
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->nama_lengkap) }}&background=000&color=fff&bold=true" class="rounded-circle border" width="32" height="32" alt="Avatar">
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-xl border-0 mt-3 p-2 rounded-4">
                            <li>
                                <form method="POST" action="{{ route('logout') }}"> @csrf
                                    <button type="submit" class="dropdown-item rounded-3 py-2 text-danger fw-bold shadow-none border-0 bg-transparent w-100 text-start">
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
    /* PERBAIKAN UTAMA:
       Gunakan 'sticky' agar navbar tetap di dalam flow content (sebelah kanan sidebar),
       bukan 'fixed' yang membuatnya menimpa seluruh layar.
    */
    #main-navbar {
        position: sticky;
        top: 0;
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(8px);
        border-bottom: 1px solid rgba(0,0,0,0.05);
        padding: 0.75rem 1.0rem;
        z-index: 1040;
        width: 100%;
        transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1); 
    }

    /* Style Saat Discroll (Island Effect - Web Version) */
    #main-navbar.scrolled {
        width: 95%; /* Tidak full width, memberi efek mengambang */
        top: 15px; /* Jarak dari atas */
        margin: 0 auto; /* Posisi tengah relatif terhadap content */
        
        border-radius: 50px;
        border: 1px solid rgba(255, 255, 255, 0.6);
        
        background: rgba(255, 255, 255, 0.9) !important; 
        backdrop-filter: blur(20px) saturate(180%);
        -webkit-backdrop-filter: blur(20px) saturate(180%);
        
        box-shadow: 
            0 10px 25px -5px rgba(0, 0, 0, 0.05),
            0 8px 10px -6px rgba(0, 0, 0, 0.01);
            
        padding: 0.6rem 1.5rem;
    }

    /* Responsive untuk Mobile */
    @media (max-width: 991.98px) {
        #main-navbar {
            position: fixed; /* Di HP boleh fixed karena sidebar offcanvas */
            top: 0;
            left: 0;
            right: 0;
        }
        
        #main-navbar.scrolled {
            width: 92%;
            top: 15px;
            padding: 0.5rem 1rem;
            border-radius: 25px;
        }
    }
</style>