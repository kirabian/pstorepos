<nav id="sidebar" class="d-flex flex-column flex-shrink-0 text-white shadow-lg">
    
    <div class="sidebar-header d-flex align-items-center justify-content-center p-4 border-bottom border-white border-opacity-10">
        <div class="logo-wrapper d-flex align-items-center gap-2">
            <img src="{{ asset('images/logo-pstore.png') }}" alt="PSTORE" class="img-fluid invert-logo" style="height: 30px;">
        </div>
        <button class="btn btn-link text-white d-lg-none ms-auto sidebar-toggler">
            <i class="fas fa-times fs-4"></i>
        </button>
    </div>

    <div class="sidebar-content flex-grow-1 overflow-y-auto custom-scrollbar px-3 py-3">
        <ul class="nav nav-pills flex-column mb-auto">
            
            <li class="nav-header text-uppercase text-secondary fw-bold px-3 mt-2 mb-2" style="font-size: 0.7rem; letter-spacing: 1px;">Main</li>
            
            <li class="nav-item mb-1">
                <a href="/" class="nav-link text-white {{ request()->is('/') ? 'active' : '' }}">
                    <i class="fas fa-th-large me-3 text-center" style="width: 20px;"></i>
                    <span class="link-text">Overview</span>
                </a>
            </li>

            @if (Auth::user()->role === 'superadmin')
                <li class="nav-header text-uppercase text-secondary fw-bold px-3 mt-4 mb-2" style="font-size: 0.7rem; letter-spacing: 1px;">Inventory</li>
                
                <li class="nav-item mb-1">
                    <a href="{{ route('stok.index') }}" class="nav-link text-white {{ request()->routeIs('stok.*') ? 'active' : '' }}">
                        <i class="fas fa-boxes-stacked me-3 text-center" style="width: 20px;"></i>
                        <span class="link-text">Stok Unit</span>
                    </a>
                </li>
                <li class="nav-item mb-1">
                    <a href="{{ route('lacak.imei') }}" class="nav-link text-white {{ request()->routeIs('lacak.imei') ? 'active' : '' }}">
                        <i class="fas fa-search-location me-3 text-center" style="width: 20px;"></i>
                        <span class="link-text">Lacak IMEI</span>
                    </a>
                </li>

                <li class="nav-header text-uppercase text-secondary fw-bold px-3 mt-4 mb-2" style="font-size: 0.7rem; letter-spacing: 1px;">Master Data</li>
                
                <li class="nav-item mb-1">
                    <a href="{{ route('merk.index') }}" class="nav-link text-white {{ request()->routeIs('merk.*') ? 'active' : '' }}">
                        <i class="fas fa-tags me-3 text-center" style="width: 20px;"></i>
                        <span class="link-text">Merk (Brands)</span>
                    </a>
                </li>
                <li class="nav-item mb-1">
                    <a href="{{ route('tipe.index') }}" class="nav-link text-white {{ request()->routeIs('tipe.*') ? 'active' : '' }}">
                        <i class="fas fa-mobile-screen-button me-3 text-center" style="width: 20px;"></i>
                        <span class="link-text">Tipe (Models)</span>
                    </a>
                </li>
                <li class="nav-item mb-1">
                    <a href="{{ route('cabang.index') }}" class="nav-link text-white {{ request()->routeIs('cabang.*') ? 'active' : '' }}">
                        <i class="fas fa-store me-3 text-center" style="width: 20px;"></i>
                        <span class="link-text">Cabang (Branches)</span>
                    </a>
                </li>
                <li class="nav-item mb-1">
                    <a href="{{ route('user.index') }}" class="nav-link text-white {{ request()->routeIs('user.*') ? 'active' : '' }}">
                        <i class="fas fa-users-cog me-3 text-center" style="width: 20px;"></i>
                        <span class="link-text">Users</span>
                    </a>
                </li>
            @endif
        </ul>
    </div>

    <div class="sidebar-footer p-3 border-top border-white border-opacity-10" style="background: rgba(255,255,255,0.03);">
        <div class="d-flex align-items-center">
            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->nama_lengkap) }}&background=fff&color=000&bold=true" 
                 alt="user" width="40" height="40" class="rounded-circle border border-2 border-white">
            <div class="ms-3 overflow-hidden user-details">
                <h6 class="mb-0 text-white text-truncate" style="font-size: 0.9rem;">{{ Auth::user()->nama_lengkap }}</h6>
                <form method="POST" action="{{ route('logout') }}"> @csrf
                    <button type="submit" class="btn btn-link p-0 text-secondary text-decoration-none" style="font-size: 0.75rem;">
                        Log Out
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>

<style>
    /* 1. Base Sidebar Styles (Desktop & Mobile Shared) */
    #sidebar {
        background: linear-gradient(180deg, #000000 0%, #1a1a1a 100%);
        z-index: 1050;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        width: 280px;
    }

    .invert-logo { filter: brightness(0) invert(1); }
    
    .nav-link {
        color: #a3a3a3 !important;
        border-radius: 8px;
        transition: 0.2s;
        padding: 10px 15px;
    }
    
    .nav-link:hover {
        background: rgba(255, 255, 255, 0.1);
        color: #fff !important;
        transform: translateX(3px);
    }
    
    .nav-link.active {
        background: #fff;
        color: #000 !important;
        font-weight: 600;
        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
    }

    /* 2. DESKTOP STYLES (Min-width: 992px) */
    @media (min-width: 992px) {
        #sidebar {
            position: sticky;
            top: 0;
            height: 100vh;
        }
        
        /* Collapsed State (Optional if you add functionality later) */
        #sidebar.collapsed {
            width: 80px;
        }
        #sidebar.collapsed .link-text, 
        #sidebar.collapsed .nav-header,
        #sidebar.collapsed .user-details,
        #sidebar.collapsed .logo-wrapper {
            display: none;
        }
        #sidebar.collapsed .sidebar-header {
            justify-content: center;
        }
        #sidebar.collapsed .nav-link {
            justify-content: center;
            padding: 15px 0;
        }
        #sidebar.collapsed .nav-link i {
            margin-right: 0 !important;
            font-size: 1.2rem;
        }
    }

    /* 3. MOBILE STYLES (Max-width: 991.98px) */
    @media (max-width: 991.98px) {
        #sidebar {
            position: fixed;
            top: 0;
            left: -280px; /* Hide off-screen left */
            height: 100vh;
            width: 280px; /* Full width on tiny screens if needed, or 280px */
        }

        #sidebar.show-mobile {
            left: 0; /* Slide in */
            box-shadow: 5px 0 15px rgba(0,0,0,0.5);
        }
    }
</style>