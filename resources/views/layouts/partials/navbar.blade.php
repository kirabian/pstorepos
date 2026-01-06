<nav class="navbar navbar-expand-lg sticky-top" 
    style="background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); border-bottom: 1px solid rgba(0,0,0,0.05); padding: 0.75rem 1.5rem; z-index: 1040;">
    <div class="container-fluid px-0">
        <div class="d-flex align-items-center">
            <button class="btn btn-white border-0 shadow-none me-3 rounded-circle d-flex align-items-center justify-content-center" id="sidebarToggle" aria-label="Toggle Sidebar">
                <i class="fas fa-bars-staggered text-dark"></i>
            </button>
            <div class="d-flex align-items-center me-4">
                <img src="{{ asset('images/logo-pstore.png') }}" alt="PSTORE" width="100" height="25" style="height: 25px; width: auto;" fetchpriority="high">
            </div>
        </div>

        <div class="ms-auto d-flex align-items-center">
            @auth
                <div class="dropdown border-start ps-3">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle hide-caret p-1 rounded-pill border bg-white" data-bs-toggle="dropdown" aria-label="Profile">
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
</nav>