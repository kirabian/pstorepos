<nav id="sidebar" class="bg-black text-white d-flex flex-column shadow-lg"
    style="min-width: 280px; max-width: 280px; min-height: 100vh; transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); z-index: 1050;">

    <div class="p-4 pt-5 flex-grow-1 overflow-hidden overflow-y-auto">
        <div class="d-flex align-items-center mb-5 sidebar-logo-container px-2">
            <img src="{{ asset('images/logo-pstore.png') }}" alt="PSTORE Navigation Logo" width="140" height="35"
                class="sidebar-logo-img invert-logo" style="height: 35px; width: auto;">
        </div>

        <ul class="list-unstyled components mb-5">
            <li class="mb-2">
                <a href="/"
                    class="nav-link p-3 rounded-4 d-flex align-items-center {{ request()->is('/') ? 'bg-white text-black fw-bold shadow' : 'text-secondary' }}">
                    <i class="fas fa-th-large fs-5 flex-shrink-0" style="width: 24px;"></i>
                    <span class="ms-3 sidebar-text text-nowrap">Overview</span>
                </a>
            </li>

            @if (Auth::user()->role === 'superadmin')
                <li class="mb-2">
                    <a href="{{ route('distributor.index') }}"
                        class="nav-link p-3 rounded-4 d-flex align-items-center {{ request()->routeIs('distributor.*') ? 'bg-white text-black fw-bold shadow' : 'text-secondary' }}">
                        <i class="fas fa-truck fs-5 flex-shrink-0" style="width: 24px;"></i>
                        <span class="ms-3 sidebar-text text-nowrap">Distributors</span>
                    </a>
                </li>
                <li class="mb-2">
                    <a href="{{ route('user.index') }}"
                        class="nav-link p-3 rounded-4 d-flex align-items-center {{ request()->routeIs('user.*') ? 'bg-white text-black fw-bold shadow' : 'text-secondary' }}">
                        <i class="fas fa-user-shield fs-5 flex-shrink-0" style="width: 24px;"></i>
                        <span class="ms-3 sidebar-text text-nowrap">Manage Users</span>
                    </a>
                </li>
                <li class="mb-2">
                    <a href="{{ route('cabang.index') }}"
                        class="nav-link p-3 rounded-4 d-flex align-items-center {{ request()->routeIs('cabang.*') ? 'bg-white text-black fw-bold shadow' : 'text-secondary' }}">
                        <i class="fas fa-store fs-5 flex-shrink-0" style="width: 24px;"></i>
                        <span class="ms-3 sidebar-text text-nowrap">Branches</span>
                    </a>
                </li>

                <li class="mb-2">
                    <a href="{{ route('gudang.index') }}"
                        class="nav-link p-3 rounded-4 d-flex align-items-center {{ request()->routeIs('gudang.*') ? 'bg-white text-black fw-bold shadow' : 'text-secondary' }}">
                        <i class="fas fa-warehouse fs-5 flex-shrink-0" style="width: 24px;"></i>
                        <span class="ms-3 sidebar-text text-nowrap">Warehouses</span>
                    </a>
                </li>

                <li class="mb-2">
                    <a href="{{ route('merk.index') }}"
                        class="nav-link p-3 rounded-4 d-flex align-items-center {{ request()->routeIs('merk.*') ? 'bg-white text-black fw-bold shadow' : 'text-secondary' }}">
                        <i class="fas fa-tags fs-5 flex-shrink-0" style="width: 24px;"></i>
                        <span class="ms-3 sidebar-text text-nowrap">Merk (Brands)</span>
                    </a>
                </li>
                <li class="mb-2">
                    <a href="{{ route('tipe.index') }}"
                        class="nav-link p-3 rounded-4 d-flex align-items-center {{ request()->routeIs('tipe.*') ? 'bg-white text-black fw-bold shadow' : 'text-secondary' }}">
                        <i class="fas fa-mobile-alt fs-5 flex-shrink-0" style="width: 24px;"></i>
                        <span class="ms-3 sidebar-text text-nowrap">Tipe (Models)</span>
                    </a>
                </li>
            @endif

            {{-- MENU KHUSUS ADMIN PRODUK --}}
            @if (Auth::user()->role === 'adminproduk')
                <li class="mb-2">
                    <a href="{{ route('stok.index') }}"
                        class="nav-link p-3 rounded-4 d-flex align-items-center {{ request()->routeIs('stok.*') ? 'bg-white text-black fw-bold shadow' : 'text-secondary' }}">
                        {{-- Icon diganti jadi fa-boxes --}}
                        <i class="fas fa-boxes fs-5 flex-shrink-0" style="width: 24px;"></i>
                        <span class="ms-3 sidebar-text text-nowrap">Stok (Inventory)</span>
                    </a>
                </li>
            @endif

            <li class="mb-2">
                <a href="{{ route('lacak.imei') }}"
                    class="nav-link p-3 rounded-4 d-flex align-items-center {{ request()->routeIs('lacak.imei') ? 'bg-white text-black fw-bold shadow' : 'text-secondary' }}">
                    <i class="fas fa-barcode fs-5 flex-shrink-0" style="width: 24px;"></i>
                    <span class="ms-3 sidebar-text text-nowrap">Lacak IMEI</span>
                </a>
            </li>

            {{-- MENU BARANG MASUK --}}
            <li class="mb-2">
                <a href="{{ route('barang-masuk.index') }}"
                    class="nav-link p-3 rounded-4 d-flex align-items-center {{ request()->routeIs('barang-masuk.*') ? 'bg-white text-black fw-bold shadow' : 'text-secondary' }}">
                    <i class="fas fa-arrow-circle-down fs-5 flex-shrink-0" style="width: 24px;"></i>
                    <span class="ms-3 sidebar-text text-nowrap">Barang Masuk</span>
                </a>
            </li>

            {{-- MENU BARANG KELUAR --}}
            <li class="mb-2">
                <a href="{{ route('barang-keluar.index') }}"
                    class="nav-link p-3 rounded-4 d-flex align-items-center {{ request()->routeIs('barang-keluar.*') ? 'bg-white text-black fw-bold shadow' : 'text-secondary' }}">
                    <i class="fas fa-arrow-circle-up fs-5 flex-shrink-0" style="width: 24px;"></i>
                    <span class="ms-3 sidebar-text text-nowrap">Barang Keluar</span>
                </a>
            </li>

            <li class="mt-4 mb-2 small text-uppercase text-muted fw-bold px-3 sidebar-text text-nowrap"
                style="font-size: 0.65rem; letter-spacing: 1px;">Preference</li>
            <li>
                <a href="#" class="nav-link p-3 rounded-4 d-flex align-items-center text-secondary">
                    <i class="fas fa-sliders-h fs-5 flex-shrink-0" style="width: 24px;"></i>
                    <span class="ms-3 sidebar-text text-nowrap">Settings</span>
                </a>
            </li>
        </ul>
    </div>

    <div class="p-3 mb-3 mt-auto">
        <div
            class="bg-white bg-opacity-10 rounded-4 p-3 border border-white border-opacity-10 user-card overflow-hidden">
            <div class="d-flex align-items-center mb-3 user-info-wrapper">
                <div class="position-relative flex-shrink-0">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->nama_lengkap) }}&background=fff&color=000&bold=true"
                        class="rounded-circle border border-2 border-white" width="45" height="45"
                        alt="Current User Avatar">
                    <span class="position-absolute bottom-0 end-0 bg-success border border-white rounded-circle"
                        style="width: 12px; height: 12px;"></span>
                </div>
                <div class="ms-3 overflow-hidden sidebar-text">
                    <p class="mb-0 fw-bold text-truncate text-white" style="font-size: 0.9rem;">
                        {{ Auth::user()->nama_lengkap }}</p>
                    <p class="mb-0 text-secondary text-truncate text-uppercase fw-bold" style="font-size: 0.65rem;">
                        {{ str_replace('_', ' ', Auth::user()->role) }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" id="logout-form"> @csrf
                <button type="submit"
                    class="btn btn-outline-light w-100 btn-sm rounded-3 py-2 fw-600 d-flex align-items-center justify-content-center border-opacity-25 shadow-none hover-danger logout-btn">
                    <i class="fas fa-sign-out-alt"></i> <span class="ms-2 sidebar-text">Log Out</span>
                </button>
            </form>
        </div>
    </div>
</nav>

<style>
    .invert-logo {
        filter: brightness(0) invert(1);
    }

    #sidebar .nav-link {
        transition: all 0.2s ease;
        font-size: 0.95rem;
    }

    #sidebar .nav-link:hover:not(.bg-white) {
        color: #ffffff !important;
        background: rgba(255, 255, 255, 0.05);
    }

    .hover-danger:hover {
        background-color: #dc3545 !important;
        border-color: #dc3545 !important;
        color: white !important;
    }

    /* DESKTOP: Minimized State */
    @media (min-width: 992px) {
        #sidebar {
            position: sticky;
            top: 0;
            height: 100vh;
        }

        #sidebar.minimized {
            min-width: 95px !important;
            max-width: 95px !important;
        }

        #sidebar.minimized .sidebar-text {
            display: none !important;
        }

        #sidebar.minimized .sidebar-logo-container {
            justify-content: center !important;
            margin-right: 0 !important;
            padding: 0 !important;
        }

        #sidebar.minimized .sidebar-logo-img {
            height: 25px !important;
            width: auto;
        }

        #sidebar.minimized .user-info-wrapper {
            justify-content: center !important;
        }

        #sidebar.minimized .nav-link {
            justify-content: center !important;
        }

        #sidebar.minimized .user-card {
            padding: 10px !important;
        }

        #sidebar.minimized .logout-btn {
            border: none !important;
            background: transparent !important;
        }

        #sidebar.minimized .logout-btn span {
            display: none;
        }
    }

    /* MOBILE: Off-Canvas Logic */
    @media (max-width: 991.98px) {
        #sidebar {
            position: fixed !important;
            left: 0;
            top: 0;
            bottom: 0;
            transform: translateX(-100%);
            /* Sembunyi ke kiri */
            z-index: 1050;
            width: 280px;
            /* Lebar tetap di mobile */
        }

        #sidebar.show-mobile {
            transform: translateX(0);
            /* Muncul */
            box-shadow: 5px 0 15px rgba(0, 0, 0, 0.3);
        }
    }
</style>
