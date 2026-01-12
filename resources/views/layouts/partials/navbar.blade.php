<nav id="main-navbar" class="navbar navbar-expand-lg transition-all">
    <div class="container-fluid px-0">
        <div class="d-flex align-items-center w-100">
            <button
                class="btn bg-white border shadow-sm me-3 rounded-circle d-flex align-items-center justify-content-center"
                id="sidebarToggle" aria-label="Toggle Sidebar"
                style="width: 40px; height: 40px; min-width: 40px; z-index: 1055;">
                <i class="fas fa-bars-staggered text-dark"></i>
            </button>

            <div class="d-flex align-items-center me-auto">
                <img src="{{ asset('images/logo-pstore.png') }}" alt="PSTORE" width="100" height="25"
                    style="height: 25px; width: auto;" fetchpriority="high">
            </div>

            <div class="ms-auto d-flex align-items-center">
                @auth
                    <div class="dropdown border-start ps-3 ms-2">
                        <a href="#"
                            class="d-flex align-items-center text-decoration-none dropdown-toggle hide-caret p-1 rounded-pill border bg-white shadow-sm"
                            data-bs-toggle="dropdown" aria-label="Profile">
                            <div class="text-end me-3 d-none d-lg-block ms-2">
                                <p class="mb-0 fw-bold text-black" style="font-size: 0.8rem; line-height: 1.2;">
                                    {{ Auth::user()->nama_lengkap }}</p>
                                <p class="mb-0 text-muted text-uppercase fw-bold" style="font-size: 0.6rem;">
                                    {{ str_replace('_', ' ', Auth::user()->role) }}</p>
                            </div>
                            {{-- UPDATE: Pake avatar_url dari Model & Tambah ID --}}
                            <img src="{{ Auth::user()->avatar_url }}"
                                id="navbar-avatar"
                                class="rounded-circle border object-fit-cover" width="32" height="32" alt="Avatar">
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-xl border-0 mt-3 p-2 rounded-4">


                            <li>
                                <a href="{{ route('profile') }}"
                                    class="dropdown-item rounded-3 py-2 fw-bold text-dark mb-1">
                                    <i class="fas fa-user-circle me-2 text-secondary"></i> My Profile
                                </a>
                            </li>

                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}"> @csrf
                                    <button type="submit"
                                        class="dropdown-item rounded-3 py-2 text-danger fw-bold shadow-none border-0 bg-transparent w-100 text-start">
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

<style>
    /* Base Style Navbar */
    #main-navbar {
        position: sticky;
        /* Tetap sticky agar ikut flow content */
        top: 0;
        z-index: 1040;
        width: 100%;
        background: rgba(255, 255, 255, 0.95);
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        padding: 0.75rem 1.5rem;
        /* Transisi Properti agar animasi smooth */
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        backdrop-filter: blur(10px);
    }

    /* Style Saat Scrolled (Efek Island / Melayang) */
    #main-navbar.scrolled {
        /* Ubah posisi top agar turun sedikit */
        top: 20px;

        /* Kecilkan width agar terlihat "lepas" dari pinggir */
        width: 95%;

        /* Margin auto untuk menengahkan navbar di dalam container induknya */
        margin-left: auto;
        margin-right: auto;

        /* styling visual */
        border-radius: 50px;
        background: rgba(255, 255, 255, 0.85) !important;
        border: 1px solid rgba(255, 255, 255, 0.5);
        box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.1);
        padding: 0.5rem 1.5rem;
    }

    /* Responsif Mobile */
    @media (max-width: 991.98px) {
        #main-navbar {
            padding: 0.5rem 1rem;
        }

        #main-navbar.scrolled {
            width: 92%;
            /* Sedikit lebih lebar di HP */
            top: 15px;
        }
    }
    
    .object-fit-cover { object-fit: cover; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const navbar = document.getElementById('main-navbar');

        // Fungsi untuk mengecek posisi scroll
        function checkScroll() {
            if (window.scrollY > 20) { // Jika discroll lebih dari 20px
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        }

        // Jalankan saat dicroll
        window.addEventListener('scroll', checkScroll);

        // Jalankan saat load (barangkali user refresh saat posisi di bawah)
        checkScroll();
    });
</script>