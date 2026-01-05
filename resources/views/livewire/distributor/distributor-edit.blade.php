<div class="row justify-content-center animate__animated animate__fadeInUp">
    <div class="col-12 col-lg-5">
        <div class="card border-0 shadow-soft-xl rounded-5 p-4 p-md-5 bg-white">
            <div class="d-flex align-items-center mb-5">
                <div class="icon-box bg-dark text-white rounded-4 p-3 me-3">
                    <i class="fas fa-edit fs-4"></i>
                </div>
                <div>
                    <h4 class="fw-black text-dark mb-0 tracking-tighter">Edit Entity</h4>
                    <p class="text-secondary small fw-medium mb-0">Modifying profile for: <span class="text-dark fw-bold">{{ $nama_distributor }}</span></p>
                </div>
            </div>
            
            <form wire:submit.prevent="update">
                <div class="mb-4">
                    <label class="small fw-black text-dark mb-2 text-uppercase letter-spacing-1">System Code</label>
                    <input type="text" wire:model="kode_distributor" 
                        class="form-control border-0 bg-light-subtle py-3 px-4 rounded-4 shadow-none font-mono fw-bold" 
                        readonly style="cursor: not-allowed; opacity: 0.7;">
                    @error('kode_distributor') <small class="text-danger fw-bold mt-2 d-block">{{ $message }}</small> @enderror
                </div>

                <div class="mb-4">
                    <label class="small fw-black text-dark mb-2 text-uppercase letter-spacing-1">Updated Name</label>
                    <input type="text" wire:model="nama_distributor" 
                        class="form-control border-0 bg-light-subtle py-3 px-4 rounded-4 shadow-none focus-ring-emerald">
                    @error('nama_distributor') <small class="text-danger fw-bold mt-2 d-block">{{ $message }}</small> @enderror
                </div>

                <div class="mb-5">
                    <label class="small fw-black text-dark mb-2 text-uppercase letter-spacing-1">Updated Address</label>
                    <textarea wire:model="alamat" 
                        class="form-control border-0 bg-light-subtle py-3 px-4 rounded-4 shadow-none" 
                        rows="3"></textarea>
                </div>

                <div class="row g-3">
                    <div class="col-7">
                        <button type="submit" class="btn btn-dark w-100 py-3 rounded-4 fw-black shadow-lg hover-scale-sm transition-all">
                            <span wire:loading.remove>UPDATE PARTNER</span>
                            <span wire:loading class="spinner-border spinner-border-sm"></span>
                        </button>
                    </div>
                    <div class="col-5">
                        <a href="{{ route('distributor.index') }}" class="btn btn-light-subtle w-100 py-3 rounded-4 fw-bold border shadow-none">
                            CANCEL
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>