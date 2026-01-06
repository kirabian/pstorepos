<nav class="navbar navbar-expand-lg sticky-top border-bottom" 
     style="background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); z-index: 1040; height: 70px;">
    
    <div class="container-fluid px-3 px-md-4">
        
        <button class="btn btn-light border-0 shadow-sm rounded-circle d-flex align-items-center justify-content-center me-3 sidebar-toggler" 
                style="width: 38px; height: 38px;">
            <i class="fas fa-bars text-dark"></i>
        </button>

        <div class="d-flex align-items-center">
            <span class="d-lg-none fw-bold fs-5 text-dark">PSTORE</span>
            <h5 class="d-none d-lg-block mb-0 fw-bold text-dark ms-2">{{ $title ?? 'Dashboard' }}</h5>
        </div>

        <div class="ms-auto d-flex align-items-center gap-2">
            
            @auth
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle hide-caret p-1 pe-lg-3 rounded-pill bg-white border shadow-sm" 
                   data-bs-toggle="dropdown" aria-expanded="false">
                    
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->nama_lengkap) }}&background=000&color=fff&bold=true" 
                         class="rounded-circle" width="34" height="34" alt="Avatar">
                    
                    <div class="ms-2 d-none d-lg-block text-start" style="line-height: 1.1;">
                        <p class="mb-0 fw-bold text-dark" style="font-size: 0.8rem;">{{ Auth::user()->nama_lengkap }}</p>
                        <p class="mb-0 text-muted text-uppercase" style="font-size: 0.6rem;">{{ Auth::user()->role }}</p>
                    </div>
                </a>

                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2 p-2 rounded-4 animate__animated animate__fadeInUp animate__faster">
                    <li>
                        <form method="POST" action="{{ route('logout') }}"> @csrf
                            <button class="dropdown-item rounded-3 text-danger fw-bold py-2">
                                <i class="fas fa-sign-out-alt me-2"></i> Log Out
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
            @endauth
        </div>
    </div>
</nav>

<style>
    .hide-caret::after { display: none; }
    /* Tombol Toggle Effect */
    .sidebar-toggler:active { transform: scale(0.95); }
</style>