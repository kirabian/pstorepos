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
                                {{-- Gunakan warna Accent untuk Role --}}
                                <p class="mb-0 text-uppercase fw-bold" style="font-size: 0.6rem; color: var(--ps-accent);">{{ str_replace('_', ' ', Auth::user()->role) }}</p>
                            </div>
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->nama_lengkap) }}&background=00ADB5&color=fff&bold=true" class="rounded-circle border" width="32" height="32" alt="Avatar">
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