<nav id="sidebar" class="bg-black text-white d-flex flex-column shadow-lg"
    style="width: 280px; min-width: 280px; height: 100vh; position: sticky; top: 0; z-index: 1050; transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); background: linear-gradient(180deg, #000000 0%, #111111 100%);">

    <div class="sidebar-header p-4 pt-5 mb-2 d-flex align-items-center overflow-hidden">
        <div class="logo-container d-flex align-items-center w-100">
            <img src="{{ asset('images/logo-pstore.png') }}" alt="PSTORE" 
                 class="img-fluid invert-logo" 
                 style="height: 32px; width: auto; object-fit: contain;">
        </div>
    </div>

    <div class="flex-grow-1 overflow-y-auto px-3 custom-scrollbar" style="scrollbar-width: none;">
        <ul class="list-unstyled components">
            
            <li class="menu-header text-uppercase text-secondary fw-bold mb-3 mt-2 px-3" style="font-size: 0.7rem; letter-spacing: 1.5px;">Main Menu</li>

            <li class="mb-2">
                <a href="/" class="nav-link {{ request()->is('/') ? 'active' : '' }}">
                    <div class="icon-box"><i class="fas fa-th-large"></i></div>
                    <span class="link-text">Overview</span>
                </a>
            </li>

            @if (Auth::user()->role === 'superadmin')
                <li class="menu-header text-uppercase text-secondary fw-bold mb-3 mt-4 px-3" style="font-size: 0.7rem; letter-spacing: 1.5px;">Inventory Control</li>

                <li class="mb-2">
                    <a href="{{ route('stok.index') }}" class="nav-link {{ request()->routeIs('stok.*') ? 'active' : '' }}">
                        <div class="icon-box"><i class="fas fa-boxes-stacked"></i></div>
                        <span class="link-text">Stok (Inventory)</span>
                    </a>
                </li>
                
                <li class="mb-2">
                    <a href="{{ route('lacak.imei') }}" class="nav-link {{ request()->routeIs('lacak.imei') ? 'active' : '' }}">
                        <div class="icon-box"><i class="fas fa-search-location"></i></div>
                        <span class="link-text">Lacak IMEI</span>
                    </a>
                </li>

                <li class="menu-header text-uppercase text-secondary fw-bold mb-3 mt-4 px-3" style="font-size: 0.7rem; letter-spacing: 1.5px;">Master Data</li>

                <li class="mb-2">
                    <a href="{{ route('merk.index') }}" class="nav-link {{ request()->routeIs('merk.*') ? 'active' : '' }}">
                        <div class="icon-box"><i class="fas fa-tags"></i></div>
                        <span class="link-text">Merk (Brands)</span>
                    </a>
                </li>
                <li class="mb-2">
                    <a href="{{ route('tipe.index') }}" class="nav-link {{ request()->routeIs('tipe.*') ? 'active' : '' }}">
                        <div class="icon-box"><i class="fas fa-mobile-screen-button"></i></div>
                        <span class="link-text">Tipe (Models)</span>
                    </a>
                </li>
                <li class="mb-2">
                    <a href="{{ route('distributor.index') }}" class="nav-link {{ request()->routeIs('distributor.*') ? 'active' : '' }}">
                        <div class="icon-box"><i class="fas fa-truck-fast"></i></div>
                        <span class="link-text">Distributors</span>
                    </a>
                </li>
                <li class="mb-2">
                    <a href="{{ route('cabang.index') }}" class="nav-link {{ request()->routeIs('cabang.*') ? 'active' : '' }}">
                        <div class="icon-box"><i class="fas fa-store"></i></div>
                        <span class="link-text">Branches</span>
                    </a>
                </li>
                <li class="mb-2">
                    <a href="{{ route('gudang.index') }}" class="nav-link {{ request()->routeIs('gudang.*') ? 'active' : '' }}">
                        <div class="icon-box"><i class="fas fa-warehouse"></i></div>
                        <span class="link-text">Warehouses</span>
                    </a>
                </li>

                <li class="menu-header text-uppercase text-secondary fw-bold mb-3 mt-4 px-3" style="font-size: 0.7rem; letter-spacing: 1.5px;">Administration</li>

                <li class="mb-2">
                    <a href="{{ route('user.index') }}" class="nav-link {{ request()->routeIs('user.*') ? 'active' : '' }}">
                        <div class="icon-box"><i class="fas fa-users-cog"></i></div>
                        <span class="link-text">Manage Users</span>
                    </a>
                </li>
            @endif

            <li class="mb-2">
                <a href="#" class="nav-link">
                    <div class="icon-box"><i class="fas fa-sliders"></i></div>
                    <span class="link-text">Settings</span>
                </a>
            </li>
        </ul>
    </div>

    <div class="p-3 mt-2 border-top border-white border-opacity-10 bg-white bg-opacity-5">
        <div class="d-flex align-items-center user-card rounded-4 p-2">
            <div class="position-relative flex-shrink-0">
                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->nama_lengkap) }}&background=fff&color=000&bold=true"
                    class="rounded-circle border border-2 border-white" width="40" height="40" alt="Avatar">
                <span class="position-absolute bottom-0 end-0 bg-success border border-white rounded-circle p-1"></span>
            </div>
            <div class="ms-3 overflow-hidden user-info transition-all">
                <p class="mb-0 fw-bold text-white text-truncate" style="font-size: 0.9rem;">{{ explode(' ', Auth::user()->nama_lengkap)[0] }}</p>
                <form method="POST" action="{{ route('logout') }}"> @csrf
                    <button type="submit" class="btn btn-link p-0 text-secondary text-decoration-none" style="font-size: 0.75rem;">
                        <i class="fas fa-sign-out-alt me-1"></i> Log Out
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>

<style>
    .invert-logo { filter: brightness(0) invert(1); }
    
    /* Nav Link Styles */
    #sidebar .nav-link {
        display: flex;
        align-items: center;
        padding: 12px 16px;
        color: #a3a3a3;
        border-radius: 12px;
        transition: all 0.3s ease;
        margin-bottom: 4px;
        font-weight: 500;
    }

    #sidebar .nav-link:hover {
        background: rgba(255, 255, 255, 0.1);
        color: #ffffff;
        transform: translateX(5px);
    }

    #sidebar .nav-link.active {
        background: #ffffff;
        color: #000000;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        font-weight: 700;
    }

    #sidebar .icon-box {
        width: 24px;
        display: flex;
        justify-content: center;
        margin-right: 12px;
        font-size: 1.1rem;
        transition: margin 0.3s;
    }

    /* Minimized State */
    #sidebar.minimized {
        width: 90px !important;
        min-width: 90px !important;
    }

    #sidebar.minimized .link-text,
    #sidebar.minimized .menu-header,
    #sidebar.minimized .user-info,
    #sidebar.minimized .sidebar-header {
        display: none !important;
    }

    #sidebar.minimized .sidebar-header {
        justify-content: center;
        padding: 20px 0 !important;
    }
    
    #sidebar.minimized .logo-container img {
        height: 24px !important;
    }

    #sidebar.minimized .nav-link {
        justify-content: center;
        padding: 15px;
    }

    #sidebar.minimized .icon-box {
        margin-right: 0;
        font-size: 1.3rem;
    }

    #sidebar.minimized .user-card {
        justify-content: center;
    }

    /* Mobile */
    @media (max-width: 992px) {
        #sidebar {
            position: fixed;
            left: -280px;
        }
        #sidebar.show-mobile {
            left: 0;
        }
    }
</style>