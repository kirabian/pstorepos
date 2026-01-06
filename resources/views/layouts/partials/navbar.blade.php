<nav class="navbar navbar-expand-lg sticky-top border-bottom" 
     style="background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); z-index: 1040; height: 75px;">
    
    <div class="container-fluid px-3 px-md-4">
        <div class="d-flex align-items-center">
            <button class="btn btn-light border-0 shadow-sm rounded-circle d-flex align-items-center justify-content-center me-3 hover-scale" 
                    id="sidebarToggle" 
                    style="width: 40px; height: 40px; background: #f8f9fa;">
                <i class="fas fa-bars-staggered text-dark"></i>
            </button>
            
            <div class="d-lg-none">
                <img src="{{ asset('images/logo-pstore.png') }}" alt="PSTORE" height="24">
            </div>

            <div class="d-none d-md-block ms-2">
                <h5 class="mb-0 fw-bold text-dark" style="letter-spacing: -0.5px;">
                    {{ $title ?? 'Dashboard' }}
                </h5>
            </div>
        </div>

        <div class="ms-auto d-flex align-items-center gap-3">
            
            <button class="btn btn-white position-relative rounded-circle p-2 text-secondary hover-dark">
                <i class="far fa-bell fs-5"></i>
                <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
                    <span class="visually-hidden">New alerts</span>
                </span>
            </button>

            @auth
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle hide-caret p-1 pe-3 rounded-pill bg-white border shadow-sm hover-shadow transition-all" 
                       data-bs-toggle="dropdown" aria-expanded="false">
                        
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->nama_lengkap) }}&background=000&color=fff&bold=true" 
                             class="rounded-circle" width="38" height="38" alt="Avatar">
                        
                        <div class="ms-2 d-none d-lg-block text-start" style="line-height: 1.2;">
                            <p class="mb-0 fw-bold text-dark" style="font-size: 0.85rem;">{{ Auth::user()->nama_lengkap }}</p>
                            <p class="mb-0 text-muted text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">{{ str_replace('_', ' ', Auth::user()->role) }}</p>
                        </div>
                        
                        <i class="fas fa-chevron-down ms-3 text-muted" style="font-size: 0.7rem;"></i>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-3 p-2 rounded-4 animate__animated animate__fadeInUp animate__faster" style="min-width: 200px;">
                        <li class="px-3 py-2 border-bottom mb-2">
                            <span class="d-block fw-bold text-dark">Account</span>
                            <small class="text-muted">{{ Auth::user()->email }}</small>
                        </li>
                        <li>
                            <a class="dropdown-item rounded-3 py-2 d-flex align-items-center" href="#">
                                <i class="far fa-user me-3 text-secondary"></i> Profile
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item rounded-3 py-2 d-flex align-items-center" href="#">
                                <i class="fas fa-cog me-3 text-secondary"></i> Settings
                            </a>
                        </li>
                        <li><hr class="dropdown-divider my-2"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}"> @csrf
                                <button type="submit" class="dropdown-item rounded-3 py-2 text-danger fw-bold d-flex align-items-center hover-bg-danger-light">
                                    <i class="fas fa-sign-out-alt me-3"></i> Sign Out
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
    
    .hover-scale:hover { transform: scale(1.05); transition: 0.2s; }
    .hover-shadow:hover { box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1) !important; }
    .hover-dark:hover { color: #000 !important; }
    
    .hover-bg-danger-light:hover { 
        background-color: #fef2f2 !important; 
        color: #dc2626 !important; 
    }
    
    .dropdown-item:active { background-color: #f8f9fa; color: #000; }
</style>