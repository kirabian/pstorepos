<div>
    <div class="row justify-content-center p-4 animate__animated animate__zoomIn">
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-extra-lg rounded-5 p-4 p-md-5 bg-white">
                <div class="mb-5 border-start border-5 border-dark ps-3">
                    <h3 class="fw-900 text-dark mb-0 tracking-tighter display-6">Gudang Baru</h3>
                    <p class="text-secondary small fw-bold text-uppercase mt-1">Registrasi Pusat Logistik PSTORE</p>
                </div>

                <form wire:submit.prevent="store">
                    <div class="row g-4">
                        <div class="col-md-5">
                            <label class="small fw-900 text-dark mb-2 text-uppercase">Kode Gudang</label>
                            <input type="text" wire:model="kode_gudang" class="form-control border-0 bg-light-subtle py-3 px-4 rounded-4 shadow-none fw-bold focus-ring-dark" placeholder="GDG-PST-01">
                            @error('kode_gudang') <small class="text-danger fw-bold mt-2 d-block">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-7">
                            <label class="small fw-900 text-dark mb-2 text-uppercase">Nama Gudang</label>
                            <input type="text" wire:model="nama_gudang" class="form-control border-0 bg-light-subtle py-3 px-4 rounded-4 shadow-none fw-bold focus-ring-dark" placeholder="Gudang Utama Jakarta">
                            @error('nama_gudang') <small class="text-danger fw-bold mt-2 d-block">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-12">
                            <label class="small fw-900 text-dark mb-2 text-uppercase">Alamat Lengkap</label>
                            <textarea wire:model="alamat_gudang" rows="3" class="form-control border-0 bg-light-subtle py-3 px-4 rounded-4 shadow-none fw-bold focus-ring-dark" placeholder="Masukkan alamat lengkap gudang..."></textarea>
                            @error('alamat_gudang') <small class="text-danger fw-bold mt-2 d-block">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <div class="d-grid gap-3 mt-5">
                        <button type="submit" wire:loading.attr="disabled" class="btn btn-dark py-3 rounded-4 fw-900 shadow-lg hover-scale transition-all">
                            <span wire:loading.remove>SIMPAN DATA GUDANG</span>
                            <span wire:loading class="spinner-border spinner-border-sm"></span>
                        </button>
                        <a href="{{ route('gudang.index') }}" class="btn btn-link text-decoration-none text-muted fw-bold small text-center text-uppercase tracking-widest">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>