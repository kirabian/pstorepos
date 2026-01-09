<nav id="sidebar" class="bg-black text-white d-flex flex-column shadow-lg"
    style="min-width: 280px; max-width: 280px; min-height: 100vh; transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); z-index: 1050;">

    {{-- 1. HEADER / LOGO --}}
    <div class="p-4 pt-5 flex-grow-1 overflow-hidden overflow-y-auto custom-scrollbar">
        <div class="d-flex align-items-center mb-4 sidebar-logo-container px-2">
            <img src="{{ asset('images/logo-pstore.png') }}" alt="PSTORE" width="140" height="35"
                class="sidebar-logo-img invert-logo" style="height: 35px; width: auto;">
        </div>

        <ul class="list-unstyled components mb-5">
            
            {{-- A. DASHBOARD (Standalone) --}}
            <li class="mb-2">
                <a href="/"
                    class="nav-link p-3 rounded-4 d-flex align-items-center justify-content-between {{ request()->is('/') ? 'active-menu shadow' : 'text-secondary hover-light' }}">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-th-large fs-5 flex-shrink-0 icon-width"></i>
                        <span class="ms-3 sidebar-text fw-medium">Overview</span>
                    </div>
                </a>
            </li>

            {{-- B. GROUP: USER MANAGEMENT (Superadmin & Audit) --}}
            @if (in_array(Auth::user()->role, ['superadmin', 'audit']))
            <li class="mb-2">
                <a href="#userSubmenu" data-bs-toggle="collapse" aria-expanded="{{ request()->routeIs('user.*') || request()->routeIs('distributor.*') ? 'true' : 'false' }}" 
                   class="nav-link p-3 rounded-4 d-flex align-items-center justify-content-between dropdown-toggle-custom {{ (request()->routeIs('user.*') || request()->routeIs('distributor.*')) ? 'text-white bg-white bg-opacity-10' : 'text-secondary hover-light' }}">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-users-cog fs-5 flex-shrink-0 icon-width"></i>
                        <span class="ms-3 sidebar-text fw-medium">Team Access</span>
                    </div>
                    <i class="fas fa-chevron-right small transition-icon sidebar-text"></i>
                </a>
                
                <ul class="collapse list-unstyled ps-3 mt-1 {{ (request()->routeIs('user.*') || request()->routeIs('distributor.*')) ? 'show' : '' }}" id="userSubmenu">
                    <li>
                        <a href="{{ route('user.index') }}" class="nav-link p-2 rounded-3 d-flex align-items-center mt-1 {{ request()->routeIs('user.*') ? 'text-white fw-bold' : 'text-muted hover-white' }}">
                            <span class="sidebar-text small ms-4">Manage Users</span>
                        </a>
                    </li>
                    @if(Auth::user()->role === 'superadmin')
                    <li>
                        <a href="{{ route('distributor.index') }}" class="nav-link p-2 rounded-3 d-flex align-items-center mt-1 {{ request()->routeIs('distributor.*') ? 'text-white fw-bold' : 'text-muted hover-white' }}">
                            <span class="sidebar-text small ms-4">Distributors</span>
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif

            {{-- C. GROUP: MASTER DATA (Superadmin Only) --}}
            @if (Auth::user()->role === 'superadmin')
            <li class="mb-2">
                <a href="#masterSubmenu" data-bs-toggle="collapse" aria-expanded="{{ request()->routeIs('cabang.*') || request()->routeIs('gudang.*') || request()->routeIs('merk.*') || request()->routeIs('tipe.*') || request()->routeIs('online-shop.*') ? 'true' : 'false' }}" 
                   class="nav-link p-3 rounded-4 d-flex align-items-center justify-content-between dropdown-toggle-custom {{ (request()->routeIs('cabang.*') || request()->routeIs('gudang.*') || request()->routeIs('merk.*') || request()->routeIs('tipe.*') || request()->routeIs('online-shop.*')) ? 'text-white bg-white bg-opacity-10' : 'text-secondary hover-light' }}">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-database fs-5 flex-shrink-0 icon-width"></i>
                        <span class="ms-3 sidebar-text fw-medium">Master Data</span>
                    </div>
                    <i class="fas fa-chevron-right small transition-icon sidebar-text"></i>
                </a>

                <ul class="collapse list-unstyled ps-3 mt-1 {{ (request()->routeIs('cabang.*') || request()->routeIs('gudang.*') || request()->routeIs('merk.*') || request()->routeIs('tipe.*') || request()->routeIs('online-shop.*')) ? 'show' : '' }}" id="masterSubmenu">
                    <li>
                        <a href="{{ route('cabang.index') }}" class="nav-link p-2 rounded-3 d-flex align-items-center mt-1 {{ request()->routeIs('cabang.*') ? 'text-white fw-bold' : 'text-muted hover-white' }}">
                            <span class="sidebar-text small ms-4">Cabang (Branches)</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('gudang.index') }}" class="nav-link p-2 rounded-3 d-flex align-items-center mt-1 {{ request()->routeIs('gudang.*') ? 'text-white fw-bold' : 'text-muted hover-white' }}">
                            <span class="sidebar-text small ms-4">Gudang (Warehouses)</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('online-shop.index') }}" class="nav-link p-2 rounded-3 d-flex align-items-center mt-1 {{ request()->routeIs('online-shop.*') ? 'text-white fw-bold' : 'text-muted hover-white' }}">
                            <span class="sidebar-text small ms-4">Online Shops</span>
                        </a>
                    </li>
                    <li class="my-1 border-top border-secondary opacity-25 sidebar-text"></li>
                    <li>
                        <a href="{{ route('merk.index') }}" class="nav-link p-2 rounded-3 d-flex align-items-center mt-1 {{ request()->routeIs('merk.*') ? 'text-white fw-bold' : 'text-muted hover-white' }}">
                            <span class="sidebar-text small ms-4">Merk HP</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('tipe.index') }}" class="nav-link p-2 rounded-3 d-flex align-items-center mt-1 {{ request()->routeIs('tipe.*') ? 'text-white fw-bold' : 'text-muted hover-white' }}">
                            <span class="sidebar-text small ms-4">Tipe & Produk</span>
                        </a>
                    </li>
                </ul>
            </li>
            @endif

            {{-- D. GROUP: INVENTORY SYSTEM (Operational Roles) --}}
            <li class="mb-2">
                <a href="#inventorySubmenu" data-bs-toggle="collapse" aria-expanded="{{ request()->routeIs('stok.*') || request()->routeIs('barang-masuk.*') || request()->routeIs('barang-keluar.*') || request()->routeIs('stock-opname.*') ? 'true' : 'false' }}" 
                   class="nav-link p-3 rounded-4 d-flex align-items-center justify-content-between dropdown-toggle-custom {{ (request()->routeIs('stok.*') || request()->routeIs('barang-masuk.*') || request()->routeIs('barang-keluar.*') || request()->routeIs('stock-opname.*')) ? 'text-white bg-white bg-opacity-10' : 'text-secondary hover-light' }}">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-boxes fs-5 flex-shrink-0 icon-width"></i>
                        <span class="ms-3 sidebar-text fw-medium">Inventory</span>
                    </div>
                    <i class="fas fa-chevron-right small transition-icon sidebar-text"></i>
                </a>

                <ul class="collapse list-unstyled ps-3 mt-1 {{ (request()->routeIs('stok.*') || request()->routeIs('barang-masuk.*') || request()->routeIs('barang-keluar.*') || request()->routeIs('stock-opname.*')) ? 'show' : '' }}" id="inventorySubmenu">
                    
                    {{-- Stok Realtime --}}
                    <li>
                        <a href="{{ route('stok.index') }}" class="nav-link p-2 rounded-3 d-flex align-items-center mt-1 {{ request()->routeIs('stok.*') ? 'text-white fw-bold' : 'text-muted hover-white' }}">
                            <span class="sidebar-text small ms-4">Stok Realtime</span>
                        </a>
                    </li>

                    {{-- Transaksi Barang --}}
                    <li>
                        <a href="{{ route('barang-masuk.index') }}" class="nav-link p-2 rounded-3 d-flex align-items-center mt-1 {{ request()->routeIs('barang-masuk.*') ? 'text-white fw-bold' : 'text-muted hover-white' }}">
                            <span class="sidebar-text small ms-4">Barang Masuk (In)</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('barang-keluar.index') }}" class="nav-link p-2 rounded-3 d-flex align-items-center mt-1 {{ request()->routeIs('barang-keluar.*') ? 'text-white fw-bold' : 'text-muted hover-white' }}">
                            <span class="sidebar-text small ms-4">Barang Keluar (Out)</span>
                        </a>
                    </li>

                    {{-- Khusus Gudang --}}
                    @if(Auth::user()->role === 'gudang')
                    <li class="my-1 border-top border-secondary opacity-25 sidebar-text"></li>
                    <li>
                        <a href="{{ route('stock-opname.index') }}" class="nav-link p-2 rounded-3 d-flex align-items-center mt-1 {{ request()->routeIs('stock-opname.*') ? 'text-white fw-bold' : 'text-muted hover-white' }}">
                            <span class="sidebar-text small ms-4">Stock Opname</span>
                        </a>
                    </li>
                    @endif
                </ul>
            </li>

            {{-- E. TOOLS (Lacak IMEI) --}}
            <li class="mb-2">
                <a href="{{ route('lacak.imei') }}"
                    class="nav-link p-3 rounded-4 d-flex align-items-center justify-content-between {{ request()->routeIs('lacak.imei') ? 'active-menu shadow' : 'text-secondary hover-light' }}">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-search-location fs-5 flex-shrink-0 icon-width"></i>
                        <span class="ms-3 sidebar-text fw-medium">Lacak IMEI</span>
                    </div>
                </a>
            </li>

            {{-- SECTION DIVIDER --}}
            <li class="mt-4 mb-2 small text-uppercase text-muted fw-bold px-3 sidebar-text tracking-wider" style="font-size: 0.65rem;">
                System
            </li>

            <li>
                <a href="#" class="nav-link p-3 rounded-4 d-flex align-items-center text-secondary hover-light">
                    <i class="fas fa-sliders-h fs-5 flex-shrink-0 icon-width"></i>
                    <span class="ms-3 sidebar-text">Settings</span>
                </a>
            </li>
        </ul>
    </div>

    {{-- 2. USER FOOTER --}}
    <div class="p-3 mb-3 mt-auto">
        <div class="bg-white bg-opacity-10 rounded-4 p-3 border border-white border-opacity-10 user-card overflow-hidden">
            <div class="d-flex align-items-center mb-3 user-info-wrapper">
                <div class="position-relative flex-shrink-0">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->nama_lengkap) }}&background=fff&color=000&bold=true"
                        class="rounded-circle border border-2 border-white shadow-sm" width="45" height="45"
                        alt="Avatar">
                    <span class="position-absolute bottom-0 end-0 bg-success border border-white rounded-circle"
                        style="width: 12px; height: 12px;"></span>
                </div>
                <div class="ms-3 overflow-hidden sidebar-text">
                    <p class="mb-0 fw-bold text-truncate text-white" style="font-size: 0.9rem;">
                        {{ Auth::user()->nama_lengkap }}
                    </p>
                    <p class="mb-0 text-secondary text-truncate text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">
                        {{ str_replace('_', ' ', Auth::user()->role) }}
                    </p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" id="logout-form"> @csrf
                <button type="submit"
                    class="btn btn-outline-light w-100 btn-sm rounded-3 py-2 fw-bold d-flex align-items-center justify-content-center border-opacity-25 shadow-none hover-danger logout-btn transition-all">
                    <i class="fas fa-sign-out-alt"></i> <span class="ms-2 sidebar-text">Log Out</span>
                </button>
            </form>
        </div>
    </div>
</nav>

{{-- 3. CSS STYLING --}}
<style>
    /* Base Logo Inversion */
    .invert-logo { filter: brightness(0) invert(1); }
    
    /* Icon Width Consistency */
    .icon-width { width: 24px; text-align: center; }

    /* Transitions */
    .transition-all { transition: all 0.3s ease; }
    .transition-icon { transition: transform 0.3s ease; }

    /* Active State for Standalone Items */
    .active-menu {
        background-color: #ffffff !important;
        color: #000000 !important;
        font-weight: 700 !important;
    }

    /* Hover State */
    .hover-light:hover {
        background: rgba(255, 255, 255, 0.08);
        color: #ffffff !important;
    }
    
    .hover-white:hover {
        color: #ffffff !important;
        transform: translateX(5px);
        transition: transform 0.2s;
    }

    /* Dropdown Toggle Logic */
    .dropdown-toggle-custom[aria-expanded="true"] .fa-chevron-right {
        transform: rotate(90deg);
    }
    .dropdown-toggle-custom[aria-expanded="true"] {
        background: rgba(255, 255, 255, 0.05);
        color: white !important;
    }

    /* Logout Button */
    .hover-danger:hover {
        background-color: #dc3545 !important;
        border-color: #dc3545 !important;
        color: white !important;
    }

    /* Custom Scrollbar for Sidebar Body */
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: rgba(255,255,255,0.05); }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.4); }

    /* DESKTOP: Minimized State */
    @media (min-width: 992px) {
        #sidebar {
            position: sticky;
            top: 0;
            height: 100vh;
        }
        #sidebar.minimized {
            min-width: 90px !important;
            max-width: 90px !important;
        }
        #sidebar.minimized .sidebar-text, 
        #sidebar.minimized .fa-chevron-right { display: none !important; }
        
        #sidebar.minimized .sidebar-logo-container { padding: 0 !important; justify-content: center; }
        #sidebar.minimized .sidebar-logo-img { height: 25px !important; width: auto; }
        
        #sidebar.minimized .nav-link { justify-content: center; padding-left: 0; padding-right: 0; }
        #sidebar.minimized .user-card { padding: 10px !important; text-align: center; }
        #sidebar.minimized .user-info-wrapper { justify-content: center; margin-bottom: 10px; }
        #sidebar.minimized .logout-btn span { display: none; }
        
        /* Hide submenus in minimized mode to prevent ugly overflow */
        #sidebar.minimized .collapse.show { display: none !important; }
    }

    /* MOBILE: Off-Canvas Logic */
    @media (max-width: 991.98px) {
        #sidebar {
            position: fixed !important;
            left: 0; top: 0; bottom: 0;
            transform: translateX(-100%);
            z-index: 1050;
            width: 280px;
        }
        #sidebar.show-mobile {
            transform: translateX(0);
            box-shadow: 5px 0 15px rgba(0, 0, 0, 0.3);
        }
    }
</style>