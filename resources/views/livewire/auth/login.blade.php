<div class="main-auth-viewport"> 
    <div class="container-fluid h-100 d-flex align-items-center justify-content-center p-0">
        <div class="row w-100 justify-content-center m-0">
            <div class="col-11 col-sm-8 col-md-6 col-lg-4 col-xl-3">
                
                <div class="card border-0 shadow-2xl bg-white animate__animated animate__fadeInUp login-card-container">
                    
                    <div class="text-center mb-5">
                        <div class="brand-display mb-4">
                            {{-- Ganti logo sesuai asset Anda --}}
                            <img src="{{ asset('images/logo-pstore.png') }}" alt="PSTORE" class="img-fluid">
                        </div>
                        
                        <h2 class="fw-800 text-dark mb-1 tracking-tighter">Identity Access</h2>
                        <p class="text-secondary small fw-medium">Secure Gateway for PSTORE System</p>
                    </div>

                    <form wire:submit.prevent="login">
                        <div class="mb-4">
                            <label class="small fw-bold text-dark opacity-75 mb-2 px-1">ID LOGIN</label>
                            <input type="text" wire:model="idlogin" 
                                   class="form-control border-0 bg-light py-3 px-4 shadow-none custom-foc-input" 
                                   placeholder="Username / ID" autofocus>
                            @error('idlogin') 
                                <span class="text-danger extra-small mt-2 d-block px-1 fw-semibold">
                                    {{ $message }}
                                </span> 
                            @enderror
                        </div>

                        <div class="mb-5">
                            <label class="small fw-bold text-dark opacity-75 mb-2 px-1">PASSWORD</label>
                            <input type="password" wire:model="password" 
                                   class="form-control border-0 bg-light py-3 px-4 shadow-none custom-foc-input" 
                                   placeholder="••••••••">
                            @error('password') 
                                <span class="text-danger extra-small mt-2 d-block px-1 fw-semibold">
                                    {{ $message }}
                                </span> 
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-black-pstore w-100 py-3 fw-bold shadow-lg border-0 d-flex align-items-center justify-content-center">
                            <span wire:loading.remove>MASUK SISTEM</span>
                            <span wire:loading class="spinner-border spinner-border-sm"></span>
                        </button>
                    </form>

                    <div class="mt-5 text-center footer-copyright">
                        <p class="mb-0">
                            Official Inventory &copy; {{ date('Y') }} <strong>PSTORE GROUP</strong>
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <style>
        .main-auth-viewport {
            position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
            background: radial-gradient(circle at top right, #f8f9fa, #e9ecef);
            z-index: 99999; overflow-y: auto;
        }
        .login-card-container {
            border-radius: 40px; padding: 50px 40px; transition: all 0.3s ease;
        }
        .brand-display img { height: 55px; width: auto; object-fit: contain; }
        .custom-foc-input {
            border-radius: 18px !important; font-size: 0.95rem; background-color: #f8f9fa !important; transition: 0.3s;
        }
        .custom-foc-input:focus {
            background-color: #ffffff !important; box-shadow: 0 0 0 5px rgba(0,0,0,0.03) !important; border: 1px solid rgba(0,0,0,0.05) !important;
        }
        .btn-black-pstore {
            background-color: #000; color: #fff; border-radius: 18px; letter-spacing: 1px; height: 58px; transition: 0.3s;
        }
        .btn-black-pstore:hover {
            background-color: #222; transform: translateY(-3px); box-shadow: 0 15px 30px rgba(0,0,0,0.15) !important;
        }
        .tracking-tighter { letter-spacing: -1.5px; }
        .fw-800 { font-weight: 800; }
        .footer-copyright { font-size: 0.75rem; opacity: 0.5; }
        .shadow-2xl { box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15) !important; }
        
        @media (max-width: 576px) {
            .login-card-container { padding: 40px 25px; border-radius: 30px; }
            .brand-display img { height: 45px; }
            h2 { font-size: 1.5rem; }
        }
    </style>
</div>