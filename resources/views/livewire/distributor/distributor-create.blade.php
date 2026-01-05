<div class="row justify-content-center animate__animated animate__zoomIn">
    <div class="col-12 col-lg-5">
        <div class="card border-0 shadow-soft-xl rounded-5 p-4 p-md-5 bg-white overflow-hidden position-relative">
            <div class="position-absolute top-0 end-0 p-4 opacity-10">
                <i class="fas fa-truck-loading fa-6x rotate-n-15"></i>
            </div>

            <div class="mb-5 position-relative">
                <h3 class="fw-black text-dark mb-2 tracking-tighter">Onboard Partner</h3>
                <p class="text-secondary small fw-medium">Expand the network, register a new distributor.</p>
                <div class="w-15 h-2px bg-dark rounded-pill mt-3"></div>
            </div>
            
            <form wire:submit.prevent="store" class="position-relative">
                <div class="mb-4">
                    <label class="small fw-black text-dark mb-2 text-uppercase letter-spacing-1">Unique Code</label>
                    <div class="input-group-modern">
                        <input type="text" wire:model="kode_distributor" 
                            class="form-control border-0 bg-light-subtle py-3 px-4 rounded-4 shadow-none font-mono fw-bold" 
                            placeholder="e.g. PST-JKT-01">
                    </div>
                    @error('kode_distributor') <small class="text-danger fw-bold mt-2 d-block animate__animated animate__shakeX">{{ $message }}</small> @enderror
                </div>

                <div class="mb-4">
                    <label class="small fw-black text-dark mb-2 text-uppercase letter-spacing-1">Distributor Entity Name</label>
                    <input type="text" wire:model="nama_distributor" 
                        class="form-control border-0 bg-light-subtle py-3 px-4 rounded-4 shadow-none" 
                        placeholder="Company or personal name">
                    @error('nama_distributor') <small class="text-danger fw-bold mt-2 d-block">{{ $message }}</small> @enderror
                </div>

                <div class="mb-5">
                    <label class="small fw-black text-dark mb-2 text-uppercase letter-spacing-1">Geographic Address</label>
                    <textarea wire:model="alamat" 
                        class="form-control border-0 bg-light-subtle py-3 px-4 rounded-4 shadow-none" 
                        rows="3" placeholder="Full operational address..."></textarea>
                </div>

                <div class="d-grid gap-3 flex-column">
                    <button type="submit" class="btn btn-dark py-3 rounded-4 fw-black shadow-lg hover-scale-sm transition-all">
                        COMMENCE REGISTRATION
                    </button>
                    <a href="{{ route('distributor.index') }}" class="btn btn-link text-decoration-none text-muted fw-bold small">
                        <i class="fas fa-arrow-left me-2"></i> ABORT AND RETURN
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>