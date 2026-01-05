<div class="container-fluid p-4">
    <div class="row">
        <div class="col-md-5">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <h5 class="fw-bold mb-4 text-dark">Register New Account</h5>
                
                <form wire:submit.prevent="storeUser">
                    <div class="mb-3">
                        <label class="small fw-bold text-muted mb-1">ROLE</label>
                        <select wire:model.live="role" class="form-select bg-light border-0 py-2 rounded-3">
                            <option value="">-- Select Role --</option>
                            <option value="superadmin">SUPERADMIN</option>
                            <option value="distributor">DISTRIBUTOR</option>
                            <option value="gudang">GUDANG</option>
                        </select>
                        @error('role') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    @if($role === 'distributor')
                    <div class="mb-3 animate__animated animate__fadeIn">
                        <label class="small fw-bold text-primary mb-1">ASSIGN TO DISTRIBUTOR</label>
                        <select wire:model="distributor_id" class="form-select border-primary py-2 rounded-3">
                            <option value="">-- Pilih Distributor --</option>
                            @foreach($list_distributor as $dist)
                                <option value="{{ $dist->id }}">{{ $dist->nama_distributor }}</option>
                            @endforeach
                        </select>
                        @error('distributor_id') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    @endif

                    <div class="mb-3">
                        <label class="small fw-bold text-muted mb-1">NAMA LENGKAP</label>
                        <input type="text" wire:model="nama_lengkap" class="form-control bg-light border-0 py-2 rounded-3">
                    </div>

                    <div class="mb-3">
                        <label class="small fw-bold text-muted mb-1">ID LOGIN / USERNAME</label>
                        <input type="text" wire:model="idlogin" class="form-control bg-light border-0 py-2 rounded-3">
                    </div>

                    <div class="mb-3">
                        <label class="small fw-bold text-muted mb-1">EMAIL</label>
                        <input type="email" wire:model="email" class="form-control bg-light border-0 py-2 rounded-3">
                    </div>

                    <div class="mb-4">
                        <label class="small fw-bold text-muted mb-1">PASSWORD</label>
                        <input type="password" wire:model="password" class="form-control bg-light border-0 py-2 rounded-3">
                    </div>

                    <button type="submit" class="btn btn-dark w-100 rounded-3 py-2 fw-bold">
                        <span wire:loading.remove>CREATE ACCOUNT</span>
                        <span wire:loading class="spinner-border spinner-border-sm"></span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>