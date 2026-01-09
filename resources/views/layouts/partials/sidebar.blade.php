<nav id="sidebar" class="sidebar-modern d-flex flex-column" 
    style="min-width: 280px; max-width: 280px; min-height: 100vh; z-index: 1050;">

    {{-- 1. HEADER / LOGO AREA --}}
    <div class="sidebar-header p-4 d-flex align-items-center justify-content-center">
        <div class="logo-container position-relative">
            {{-- Efek Glow di belakang logo --}}
            <div class="logo-glow"></div>
            <img src="{{ asset('images/logo-pstore.png') }}" alt="PSTORE" width="150" height="40"
                class="position-relative z-1 invert-logo" style="height: 40px; width: auto; object-fit: contain;">
        </div>
    </div>

    {{-- 2. SCROLLABLE CONTENT --}}
    <div class="flex-grow-1 overflow-hidden overflow-y-auto custom-scrollbar px-3 pb-4">
        
        <ul class="list-unstyled components">
            
            {{-- SECTION: MAIN --}}
            <li class="menu-label mt-2 mb-2">MAIN MENU</li>

            {{-- A. DASHBOARD --}}
            <li class="mb-1">
                <a href="/"
                    class="nav-link d-flex align-items-center justify-content-between {{ request()->is('/') ? 'active' : '' }}">
                    <div class="d-flex align-items-center">
                        <span class="icon-box"><i class="fas fa-grid-2"></i></span> {{-- Menggunakan Grid Icon --}}
                        <span class="ms-3 fw-semibold">Dashboard</span>
                    </div>
                    @if(request()->is('/')) 
                        <div class="active-indicator"></div> 
                    @endif
                </a>
            </li>

            {{-- B. GROUP: TEAM & ACCESS (Superadmin & Audit) --}}
            @if (in_array(Auth::user()->role, ['superadmin', 'audit']))
            <li class="mb-1">
                <a href="#userSubmenu" data-bs-toggle="collapse" 
                   aria-expanded="{{ request()->routeIs('user.*') || request()->routeIs('distributor.*') ? 'true' : 'false' }}" 
                   class="nav-link d-flex align-items-center justify-content-between dropdown-toggle-custom {{ (request()->routeIs('user.*') || request()->routeIs('distributor.*')) ? 'active-parent' : '' }}">
                    <div class="d-flex align-items-center">
                        <span class="icon-box"><i class="fas fa-users"></i></span>
                        <span class="ms-3 fw-semibold">Team Access</span>
                    </div>
                    <i class="fas fa-chevron-right arrow-icon"></i>
                </a>
                
                <ul class="collapse list-unstyled submenu-container {{ (request()->routeIs('user.*') || request()->routeIs('distributor.*')) ? 'show' : '' }}" id="userSubmenu">
                    <li>
                        <a href="{{ route('user.index') }}" class="nav-link-sub {{ request()->routeIs('user.*') ? 'active-sub' : '' }}">
                            <span class="sub-dot"></span> Manage Users
                        </a>
                    </li>
                    @if(Auth::user()->role === 'superadmin')
                    <li>
                        <a href="{{ route('distributor.index') }}" class="nav-link-sub {{ request()->routeIs('distributor.*') ? 'active-sub' : '' }}">
                            <span class="sub-dot"></span> Distributors
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif

            {{-- C. GROUP: MASTER DATA (Superadmin Only) --}}
            @if (Auth::user()->role === 'superadmin')
            <li class="menu-label mt-4 mb-2">DATABASE</li>
            
            <li class="mb-1">
                <a href="#masterSubmenu" data-bs-toggle="collapse" 
                   aria-expanded="{{ request()->routeIs('cabang.*') || request()->routeIs('gudang.*') || request()->routeIs('merk.*') || request()->routeIs('tipe.*') || request()->routeIs('online-shop.*') ? 'true' : 'false' }}" 
                   class="nav-link d-flex align-items-center justify-content-between dropdown-toggle-custom {{ (request()->routeIs('cabang.*') || request()->routeIs('gudang.*') || request()->routeIs('merk.*') || request()->routeIs('tipe.*') || request()->routeIs('online-shop.*')) ? 'active-parent' : '' }}">
                    <div class="d-flex align-items-center">
                        <span class="icon-box"><i class="fas fa-layer-group"></i></span>
                        <span class="ms-3 fw-semibold">Master Data</span>
                    </div>
                    <i class="fas fa-chevron-right arrow-icon"></i>
                </a>

                <ul class="collapse list-unstyled submenu-container {{ (request()->routeIs('cabang.*') || request()->routeIs('gudang.*') || request()->routeIs('merk.*') || request()->routeIs('tipe.*') || request()->routeIs('online-shop.*')) ? 'show' : '' }}" id="masterSubmenu">
                    <li>
                        <a href="{{ route('cabang.index') }}" class="nav-link-sub {{ request()->routeIs('cabang.*') ? 'active-sub' : '' }}">
                            <span class="sub-dot"></span> Cabang
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('gudang.index') }}" class="nav-link-sub {{ request()->routeIs('gudang.*') ? 'active-sub' : '' }}">
                            <span class="sub-dot"></span> Gudang
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('online-shop.index') }}" class="nav-link-sub {{ request()->routeIs('online-shop.*') ? 'active-sub' : '' }}">
                            <span class="sub-dot"></span> Online Shops
                        </a>
                    </li>
                    <li class="divider-sub"></li>
                    <li>
                        <a href="{{ route('merk.index') }}" class="nav-link-sub {{ request()->routeIs('merk.*') ? 'active-sub' : '' }}">
                            <span class="sub-dot"></span> Merk HP
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('tipe.index') }}" class="nav-link-sub {{ request()->routeIs('tipe.*') ? 'active-sub' : '' }}">
                            <span class="sub-dot"></span> Tipe & Produk
                        </a>
                    </li>
                </ul>
            </li>
            @endif

            {{-- D. GROUP: INVENTORY SYSTEM (Operational Roles) --}}
            <li class="menu-label mt-4 mb-2">OPERATIONAL</li>

            <li class="mb-1">
                <a href="#inventorySubmenu" data-bs-toggle="collapse" 
                   aria-expanded="{{ request()->routeIs('stok.*') || request()->routeIs('barang-masuk.*') || request()->routeIs('barang-keluar.*') || request()->routeIs('stock-opname.*') ? 'true' : 'false' }}" 
                   class="nav-link d-flex align-items-center justify-content-between dropdown-toggle-custom {{ (request()->routeIs('stok.*') || request()->routeIs('barang-masuk.*') || request()->routeIs('barang-keluar.*') || request()->routeIs('stock-opname.*')) ? 'active-parent' : '' }}">
                    <div class="d-flex align-items-center">
                        <span class="icon-box"><i class="fas fa-boxes-stacked"></i></span>
                        <span class="ms-3 fw-semibold">Inventory</span>
                    </div>
                    <i class="fas fa-chevron-right arrow-icon"></i>
                </a>

                <ul class="collapse list-unstyled submenu-container {{ (request()->routeIs('stok.*') || request()->routeIs('barang-masuk.*') || request()->routeIs('barang-keluar.*') || request()->routeIs('stock-opname.*')) ? 'show' : '' }}" id="inventorySubmenu">
                    
                    {{-- Stok Realtime --}}
                    <li>
                        <a href="{{ route('stok.index') }}" class="nav-link-sub {{ request()->routeIs('stok.*') ? 'active-sub' : '' }}">
                            <span class="sub-dot"></span> Stok Realtime
                        </a>
                    </li>

                    {{-- Transaksi Barang --}}
                    <li>
                        <a href="{{ route('barang-masuk.index') }}" class="nav-link-sub {{ request()->routeIs('barang-masuk.*') ? 'active-sub' : '' }}">
                            <span class="sub-dot"></span> Barang Masuk
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('barang-keluar.index') }}" class="nav-link-sub {{ request()->routeIs('barang-keluar.*') ? 'active-sub' : '' }}">
                            <span class="sub-dot"></span> Barang Keluar
                        </a>
                    </li>

                    {{-- Khusus Gudang --}}
                    @if(Auth::user()->role === 'gudang')
                    <li class="divider-sub"></li>
                    <li>
                        <a href="{{ route('stock-opname.index') }}" class="nav-link-sub {{ request()->routeIs('stock-opname.*') ? 'active-sub' : '' }}">
                            <span class="sub-dot"></span> Stock Opname
                        </a>
                    </li>
                    @endif
                </ul>
            </li>

            {{-- E. TOOLS (Lacak IMEI) --}}
            <li class="mb-1">
                <a href="{{ route('lacak.imei') }}"
                    class="nav-link d-flex align-items-center justify-content-between {{ request()->routeIs('lacak.imei') ? 'active' : '' }}">
                    <div class="d-flex align-items-center">
                        <span class="icon-box"><i class="fas fa-barcode"></i></span>
                        <span class="ms-3 fw-semibold">Lacak IMEI</span>
                    </div>
                    @if(request()->routeIs('lacak.imei')) 
                        <div class="active-indicator"></div> 
                    @endif
                </a>
            </li>

            {{-- F. SETTINGS --}}
            <li class="menu-label mt-4 mb-2">SYSTEM</li>

            <li>
                <a href="#" class="nav-link d-flex align-items-center">
                    <span class="icon-box"><i class="fas fa-cog"></i></span>
                    <span class="ms-3 fw-semibold">Settings</span>
                </a>
            </li>
        </ul>
    </div>

    {{-- 3. USER FOOTER (Modern Card) --}}
    <div class="p-3 mt-auto">
        <div class="user-card-modern p-3 rounded-4 d-flex flex-column gap-3">
            <div class="d-flex align-items-center">
                <div class="position-relative">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->nama_lengkap) }}&background=3b82f6&color=fff&bold=true"
                        class="rounded-circle border border-2 border-dark" width="42" height="42"
                        alt="Avatar">
                    <span class="status-dot"></span>
                </div>
                <div class="ms-3 overflow-hidden">
                    <p class="mb-0 fw-bold text-white text-truncate" style="font-size: 0.95rem;">
                        {{ Auth::user()->nama_lengkap }}
                    </p>
                    <p class="mb-0 text-muted text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 1px;">
                        {{ str_replace('_', ' ', Auth::user()->role) }}
                    </p>
                </div>
            </div>
            
            <form method="POST" action="{{ route('logout') }}" id="logout-form" class="m-0"> @csrf
                <button type="submit" class="btn btn-logout w-100 rounded-pill py-2 fw-bold d-flex align-items-center justify-content-center">
                    <i class="fas fa-power-off me-2"></i> Log Out
                </button>
            </form>
        </div>
    </div>
</nav>

{{-- 4. CSS STYLING (NEW & IMPROVED) --}}
<style>
    /* --- VARIABLES --- */
    :root {
        --sidebar-bg: #0f172a; /* Dark Blue-Black */
        --sidebar-bg-secondary: #1e293b;
        --text-primary: #f8fafc;
        --text-secondary: #94a3b8;
        --accent-color: #3b82f6; /* Bright Blue */
        --accent-glow: rgba(59, 130, 246, 0.3);
        --hover-bg: rgba(255, 255, 255, 0.05);
        --active-bg: linear-gradient(90deg, rgba(59,130,246,0.15) 0%, rgba(59,130,246,0.05) 100%);
        --logout-bg: rgba(239, 68, 68, 0.15);
        --logout-text: #ef4444;
    }

    /* --- BASE LAYOUT --- */
    .sidebar-modern {
        background-color: var(--sidebar-bg);
        border-right: 1px solid rgba(255,255,255,0.05);
        font-family: 'Inter', sans-serif;
    }

    .invert-logo { filter: brightness(0) invert(1); }
    
    .logo-container {
        display: inline-block;
    }
    
    .logo-glow {
        position: absolute;
        top: 50%; left: 50%;
        transform: translate(-50%, -50%);
        width: 100px; height: 30px;
        background: var(--accent-color);
        filter: blur(40px);
        opacity: 0.4;
        z-index: 0;
    }

    /* --- MENU LABELS --- */
    .menu-label {
        color: var(--text-secondary);
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 1.5px;
        padding-left: 1rem;
        opacity: 0.7;
    }

    /* --- NAV LINKS --- */
    .nav-link {
        color: var(--text-secondary) !important;
        padding: 0.8rem 1rem;
        border-radius: 12px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        margin-bottom: 2px;
    }

    .nav-link:hover {
        background-color: var(--hover-bg);
        color: var(--text-primary) !important;
        padding-left: 1.2rem; /* Slide effect */
    }

    /* Active State */
    .nav-link.active {
        background: var(--active-bg);
        color: var(--accent-color) !important;
        font-weight: 700;
        border-left: 3px solid var(--accent-color);
        padding-left: 1.2rem;
    }

    /* Parent Menu Active */
    .nav-link.active-parent {
        color: var(--text-primary) !important;
        background-color: var(--hover-bg);
    }

    .icon-box {
        width: 24px;
        text-align: center;
        font-size: 1.1rem;
        transition: transform 0.3s;
    }

    .nav-link:hover .icon-box {
        transform: scale(1.1);
    }

    /* --- SUBMENU --- */
    .submenu-container {
        background-color: rgba(0,0,0,0.2);
        border-radius: 12px;
        margin-top: 5px;
        padding: 5px 0;
        position: relative;
    }
    
    /* Guide Line for Submenu */
    .submenu-container::before {
        content: '';
        position: absolute;
        left: 20px;
        top: 10px;
        bottom: 10px;
        width: 1px;
        background-color: rgba(255,255,255,0.1);
    }

    .nav-link-sub {
        display: flex;
        align-items: center;
        padding: 0.6rem 1rem 0.6rem 2.5rem;
        color: var(--text-secondary);
        text-decoration: none;
        font-size: 0.9rem;
        transition: 0.2s;
        position: relative;
    }

    .nav-link-sub:hover {
        color: var(--text-primary);
    }

    .nav-link-sub.active-sub {
        color: var(--accent-color);
        font-weight: 600;
    }

    .sub-dot {
        width: 5px;
        height: 5px;
        background-color: var(--text-secondary);
        border-radius: 50%;
        position: absolute;
        left: 18px; /* Align with guide line */
        transition: 0.3s;
    }

    .nav-link-sub.active-sub .sub-dot {
        background-color: var(--accent-color);
        transform: scale(1.5);
        box-shadow: 0 0 10px var(--accent-color);
    }

    .divider-sub {
        height: 1px;
        background-color: rgba(255,255,255,0.05);
        margin: 5px 20px 5px 40px;
    }

    /* --- ANIMATIONS --- */
    .arrow-icon {
        font-size: 0.7rem;
        transition: transform 0.3s ease;
    }

    .dropdown-toggle-custom[aria-expanded="true"] .arrow-icon {
        transform: rotate(90deg);
    }

    /* --- USER CARD --- */
    .user-card-modern {
        background: rgba(30, 41, 59, 0.7);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.05);
    }

    .status-dot {
        width: 10px; height: 10px;
        background-color: #22c55e;
        border: 2px solid var(--sidebar-bg);
        border-radius: 50%;
        position: absolute;
        bottom: 0; right: 0;
    }

    .btn-logout {
        background-color: var(--logout-bg);
        color: var(--logout-text);
        border: none;
        transition: 0.3s;
    }

    .btn-logout:hover {
        background-color: #ef4444;
        color: white;
    }

    /* --- SCROLLBAR --- */
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.3); }

    /* --- RESPONSIVE --- */
    @media (min-width: 992px) {
        #sidebar { position: sticky; top: 0; height: 100vh; }
        
        #sidebar.minimized {
            min-width: 80px !important;
            max-width: 80px !important;
        }
        #sidebar.minimized .menu-label,
        #sidebar.minimized .fw-semibold,
        #sidebar.minimized .arrow-icon,
        #sidebar.minimized .user-info-wrapper p,
        #sidebar.minimized .btn-logout span,
        #sidebar.minimized .logo-container img { display: none; }
        
        #sidebar.minimized .logo-container {
            width: 40px; height: 40px;
            background: url('{{ asset("images/logo-icon-only.png") }}') no-repeat center/contain; /* Opsional jika punya icon only */
        }
        
        #sidebar.minimized .nav-link { justify-content: center; padding: 1rem; }
        #sidebar.minimized .nav-link:hover { padding-left: 1rem; }
        #sidebar.minimized .active-indicator { display: none; }
        #sidebar.minimized .collapse.show { display: none !important; }
    }

    @media (max-width: 991.98px) {
        #sidebar {
            position: fixed; left: 0; top: 0; bottom: 0;
            transform: translateX(-100%);
            transition: transform 0.3s ease-in-out;
        }
        #sidebar.show-mobile { transform: translateX(0); box-shadow: 10px 0 30px rgba(0,0,0,0.5); }
    }
</style>