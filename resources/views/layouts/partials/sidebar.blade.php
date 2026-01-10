<nav id="sidebar" class="d-flex flex-column shadow-lg sidebar-premium"
    style="min-width: 280px; max-width: 280px; min-height: 100vh; transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); z-index: 1050;">

    <div class="p-4 pt-5 flex-grow-0">
        <div class="d-flex align-items-center mb-4 sidebar-logo-container px-2 justify-content-start">
            <img src="{{ asset('images/logo-pstore.png') }}" alt="PSTORE Navigation Logo" width="140" height="35"
                class="sidebar-logo-img invert-logo" style="height: 35px; width: auto; object-fit: contain;">
        </div>
        <div class="sidebar-divider mx-2"></div>
    </div>

    <div class="px-3 flex-grow-1 overflow-hidden overflow-y-auto custom-scrollbar">
        <ul class="list-unstyled components mb-5">
            
            {{-- DASHBOARD --}}
            <li class="mb-2">
                <a href="/"
                    class="nav-link p-3 rounded-3 d-flex align-items-center {{ request()->is('/') ? 'active' : '' }}">
                    <div class="icon-wrapper d-flex justify-content-center align-items-center">
                        <i class="fas fa-th-large fs-5"></i>
                    </div>
                    <span class="ms-3 sidebar-text text-nowrap fw-medium">Overview</span>
                </a>
            </li>

            {{-- KHUSUS SUPERADMIN: Distributor --}}
            @if (Auth::user()->role === 'superadmin')
                <li class="mb-1">
                    <div class="sidebar-header mt-3 mb-2 px-3 sidebar-text">
                        <span class="text-uppercase fw-bold text-muted" style="font-size: 0.65rem; letter-spacing: 1.5px;">Master Data</span>
                    </div>
                </li>
                <li class="mb-2">
                    <a href="{{ route('distributor.index') }}"
                        class="nav-link p-3 rounded-3 d-flex align-items-center {{ request()->routeIs('distributor.*') ? 'active' : '' }}">
                        <div class="icon-wrapper d-flex justify-content-center align-items-center">
                            <i class="fas fa-truck fs-5"></i>
                        </div>
                        <span class="ms-3 sidebar-text text-nowrap fw-medium">Distributors</span>
                    </a>
                </li>

                <li class="mb-2">
                    <a href="{{ route('online-shop.index') }}"
                        class="nav-link p-3 rounded-3 d-flex align-items-center {{ request()->routeIs('online-shop.*') ? 'active' : '' }}">
                        <div class="icon-wrapper d-flex justify-content-center align-items-center">
                            <i class="fas fa-shopping-bag fs-5"></i>
                        </div>
                        <span class="ms-3 sidebar-text text-nowrap fw-medium">Online Shops</span>
                    </a>
                </li>
            @endif

            {{-- UPDATE: Manage Users (Bisa Diakses Superadmin & Audit) --}}
            @if (in_array(Auth::user()->role, ['superadmin', 'audit']))
                <li class="mb-2">
                    <a href="{{ route('user.index') }}"
                        class="nav-link p-3 rounded-3 d-flex align-items-center {{ request()->routeIs('user.*') ? 'active' : '' }}">
                        <div class="icon-wrapper d-flex justify-content-center align-items-center">
                            <i class="fas fa-user-shield fs-5"></i>
                        </div>
                        <span class="ms-3 sidebar-text text-nowrap fw-medium">Manage Users</span>
                    </a>
                </li>
            @endif

            {{-- KHUSUS SUPERADMIN: Master Data Lainnya --}}
            @if (Auth::user()->role === 'superadmin')
                <li class="mb-2">
                    <a href="{{ route('cabang.index') }}"
                        class="nav-link p-3 rounded-3 d-flex align-items-center {{ request()->routeIs('cabang.*') ? 'active' : '' }}">
                        <div class="icon-wrapper d-flex justify-content-center align-items-center">
                            <i class="fas fa-store fs-5"></i>
                        </div>
                        <span class="ms-3 sidebar-text text-nowrap fw-medium">Branches</span>
                    </a>
                </li>

                <li class="mb-2">
                    <a href="{{ route('gudang.index') }}"
                        class="nav-link p-3 rounded-3 d-flex align-items-center {{ request()->routeIs('gudang.*') ? 'active' : '' }}">
                        <div class="icon-wrapper d-flex justify-content-center align-items-center">
                            <i class="fas fa-warehouse fs-5"></i>
                        </div>
                        <span class="ms-3 sidebar-text text-nowrap fw-medium">Warehouses</span>
                    </a>
                </li>

                <li class="mb-2">
                    <a href="{{ route('merk.index') }}"
                        class="nav-link p-3 rounded-3 d-flex align-items-center {{ request()->routeIs('merk.*') ? 'active' : '' }}">
                        <div class="icon-wrapper d-flex justify-content-center align-items-center">
                            <i class="fas fa-tags fs-5"></i>
                        </div>
                        <span class="ms-3 sidebar-text text-nowrap fw-medium">Merk (Brands)</span>
                    </a>
                </li>
                <li class="mb-2">
                    <a href="{{ route('tipe.index') }}"
                        class="nav-link p-3 rounded-3 d-flex align-items-center {{ request()->routeIs('tipe.*') ? 'active' : '' }}">
                        <div class="icon-wrapper d-flex justify-content-center align-items-center">
                            <i class="fas fa-mobile-alt fs-5"></i>
                        </div>
                        <span class="ms-3 sidebar-text text-nowrap fw-medium">Tipe (Models)</span>
                    </a>
                </li>
            @endif

            {{-- MENU KHUSUS ADMIN PRODUK --}}
            @if (Auth::user()->role === 'adminproduk')
                <li class="mb-1">
                    <div class="sidebar-header mt-3 mb-2 px-3 sidebar-text">
                        <span class="text-uppercase fw-bold text-muted" style="font-size: 0.65rem; letter-spacing: 1.5px;">Inventory</span>
                    </div>
                </li>
                <li class="mb-2">
                    <a href="{{ route('stok.index') }}"
                        class="nav-link p-3 rounded-3 d-flex align-items-center {{ request()->routeIs('stok.*') ? 'active' : '' }}">
                        <div class="icon-wrapper d-flex justify-content-center align-items-center">
                            <i class="fas fa-boxes fs-5"></i>
                        </div>
                        <span class="ms-3 sidebar-text text-nowrap fw-medium">Stok (Inventory)</span>
                    </a>
                </li>
            @endif

            {{-- OPERATIONAL MENU (Available for All) --}}
            @if(in_array(Auth::user()->role, ['superadmin', 'adminproduk', 'gudang', 'audit'])) 
             <li class="mb-1">
                <div class="sidebar-header mt-3 mb-2 px-3 sidebar-text">
                    <span class="text-uppercase fw-bold text-muted" style="font-size: 0.65rem; letter-spacing: 1.5px;">Operations</span>
                </div>
            </li>
            @endif

            <li class="mb-2">
                <a href="{{ route('lacak.imei') }}"
                    class="nav-link p-3 rounded-3 d-flex align-items-center {{ request()->routeIs('lacak.imei') ? 'active' : '' }}">
                    <div class="icon-wrapper d-flex justify-content-center align-items-center">
                        <i class="fas fa-barcode fs-5"></i>
                    </div>
                    <span class="ms-3 sidebar-text text-nowrap fw-medium">Lacak IMEI</span>
                </a>
            </li>

            {{-- MENU BARANG MASUK --}}
            <li class="mb-2">
                <a href="{{ route('barang-masuk.index') }}"
                    class="nav-link p-3 rounded-3 d-flex align-items-center {{ request()->routeIs('barang-masuk.*') ? 'active' : '' }}">
                    <div class="icon-wrapper d-flex justify-content-center align-items-center">
                        <i class="fas fa-arrow-circle-down fs-5"></i>
                    </div>
                    <span class="ms-3 sidebar-text text-nowrap fw-medium">Barang Masuk</span>
                </a>
            </li>

            {{-- MENU BARANG KELUAR --}}
            <li class="mb-2">
                <a href="{{ route('barang-keluar.index') }}"
                    class="nav-link p-3 rounded-3 d-flex align-items-center {{ request()->routeIs('barang-keluar.*') ? 'active' : '' }}">
                    <div class="icon-wrapper d-flex justify-content-center align-items-center">
                        <i class="fas fa-arrow-circle-up fs-5"></i>
                    </div>
                    <span class="ms-3 sidebar-text text-nowrap fw-medium">Barang Keluar</span>
                </a>
            </li>

            {{-- MENU KHUSUS GUDANG (INVENTORY STAFF) --}}
            @if (Auth::user()->role === 'gudang')
                <li class="mb-2">
                    <a href="{{ route('stock-opname.index') }}"
                        class="nav-link p-3 rounded-3 d-flex align-items-center {{ request()->routeIs('stock-opname.*') ? 'active' : '' }}">
                        <div class="icon-wrapper d-flex justify-content-center align-items-center">
                            <i class="fas fa-clipboard-check fs-5"></i>
                        </div>
                        <span class="ms-3 sidebar-text text-nowrap fw-medium">Stock Opname</span>
                    </a>
                </li>
            @endif

            <li class="mt-4 mb-2 small text-uppercase text-muted fw-bold px-3 sidebar-text"
                style="font-size: 0.65rem; letter-spacing: 1.5px;">Preference</li>
            <li>
                <a href="#" class="nav-link p-3 rounded-3 d-flex align-items-center text-secondary">
                    <div class="icon-wrapper d-flex justify-content-center align-items-center">
                        <i class="fas fa-sliders-h fs-5"></i>
                    </div>
                    <span class="ms-3 sidebar-text text-nowrap fw-medium">Settings</span>
                </a>
            </li>
        </ul>
    </div>

    <div class="p-3 mb-2 mt-auto">
        <div class="glass-card rounded-4 p-3 user-card position-relative overflow-hidden">
            <div class="glow-effect"></div>
            
            <div class="d-flex align-items-center mb-3 user-info-wrapper position-relative" style="z-index: 2;">
                <div class="position-relative flex-shrink-0">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->nama_lengkap) }}&background=fff&color=000&bold=true"
                        class="rounded-circle border border-2 border-white shadow-sm" width="42" height="42"
                        alt="User Avatar">
                    <span class="position-absolute bottom-0 end-0 bg-success border border-white rounded-circle"
                        style="width: 10px; height: 10px;"></span>
                </div>
                <div class="ms-3 overflow-hidden sidebar-text">
                    <p class="mb-0 fw-bold text-truncate text-white" style="font-size: 0.9rem;">
                        {{ Auth::user()->nama_lengkap }}</p>
                    <p class="mb-0 text-white-50 text-truncate text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">
                        {{ str_replace('_', ' ', Auth::user()->role) }}</p>
                </div>
            </div>
            
            <form method="POST" action="{{ route('logout') }}" id="logout-form" class="position-relative" style="z-index: 2;"> 
                @csrf
                <button type="submit"
                    class="btn btn-logout w-100 btn-sm rounded-3 py-2 fw-semibold d-flex align-items-center justify-content-center logout-btn">
                    <i class="fas fa-sign-out-alt"></i> <span class="ms-2 sidebar-text">Log Out</span>
                </button>
            </form>
        </div>
    </div>
</nav>

<style>
    /* --- THEME COLORS & BASICS --- */
    .sidebar-premium {
        background: linear-gradient(180deg, #09090b 0%, #18181b 100%);
        color: #a1a1aa;
        border-right: 1px solid rgba(255, 255, 255, 0.05);
    }

    .invert-logo {
        filter: brightness(0) invert(1);
        opacity: 0.9;
    }

    .sidebar-divider {
        height: 1px;
        background: linear-gradient(90deg, rgba(255,255,255,0) 0%, rgba(255,255,255,0.1) 50%, rgba(255,255,255,0) 100%);
        margin-top: 10px;
    }

    /* --- NAVIGATION ITEMS --- */
    #sidebar .nav-link {
        color: #a1a1aa; /* Text Gray */
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        border: 1px solid transparent;
        margin-bottom: 4px;
    }

    /* Icon Wrapper Fixed Width for Alignment */
    .icon-wrapper {
        width: 24px;
        height: 24px;
        flex-shrink: 0;
    }

    /* Hover State */
    #sidebar .nav-link:hover:not(.active) {
        color: #ffffff;
        background: rgba(255, 255, 255, 0.08);
        transform: translateX(5px);
    }

    /* Active State */
    #sidebar .nav-link.active {
        background-color: #ffffff;
        color: #000000;
        box-shadow: 0 4px 12px rgba(255, 255, 255, 0.15);
        font-weight: 700 !important;
        border: 1px solid rgba(255, 255, 255, 1);
    }
    
    #sidebar .nav-link.active i {
        transform: scale(1.1);
        transition: transform 0.2s;
    }

    /* --- SCROLLBAR CUSTOMIZATION --- */
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.2);
    }

    /* --- USER CARD GLASSMORPHISM --- */
    .glass-card {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
    }

    .btn-logout {
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: #fff;
        background: transparent;
        transition: all 0.2s ease;
    }

    .btn-logout:hover {
        background: #dc3545;
        border-color: #dc3545;
        color: #fff;
        box-shadow: 0 4px 10px rgba(220, 53, 69, 0.3);
    }

    /* --- RESPONSIVE LOGIC (DESKTOP) --- */
    @media (min-width: 992px) {
        #sidebar {
            position: sticky;
            top: 0;
            height: 100vh;
        }

        #sidebar.minimized {
            min-width: 85px !important;
            max-width: 85px !important;
        }

        #sidebar.minimized .sidebar-text, 
        #sidebar.minimized .sidebar-header,
        #sidebar.minimized .sidebar-divider {
            display: none !important;
        }

        #sidebar.minimized .sidebar-logo-container {
            justify-content: center !important;
            padding: 0 !important;
        }

        #sidebar.minimized .sidebar-logo-img {
            height: 24px !important;
            width: auto;
        }

        #sidebar.minimized .nav-link {
            justify-content: center !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
        }

        #sidebar.minimized .nav-link:hover {
            transform: none; /* Disable shift on minimize */
            background: rgba(255,255,255,0.1);
        }

        #sidebar.minimized .user-info-wrapper {
            justify-content: center !important;
            margin-bottom: 10px !important;
        }
        
        #sidebar.minimized .user-card {
            padding: 10px !important;
            background: transparent;
            border: none;
        }

        #sidebar.minimized .logout-btn {
            border: none !important;
            background: transparent !important;
            color: #dc3545 !important;
        }
        
        #sidebar.minimized .logout-btn:hover {
            background: rgba(220, 53, 69, 0.1) !important;
        }

        #sidebar.minimized .logout-btn span {
            display: none;
        }
    }

    /* --- RESPONSIVE LOGIC (MOBILE) --- */
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
            box-shadow: 10px 0 30px rgba(0, 0, 0, 0.5);
        }
    }
</style>