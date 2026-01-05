<div>
    <div class="row justify-content-center p-4 animate__animated animate__zoomIn">
        <div class="col-12 col-lg-7">
            <div class="card border-0 shadow-extra-lg rounded-5 p-4 p-md-5 bg-white">
                <div class="mb-5 border-start border-5 border-dark ps-3">
                    <h3 class="fw-900 text-dark mb-0 tracking-tighter display-6">Cabang Baru</h3>
                    <p class="text-secondary small fw-bold text-uppercase mt-1">Registrasi Titik Distribusi PSTORE</p>
                </div>

                <form wire:submit.prevent="store">
                    <div class="row g-4">
                        <div class="col-md-5">
                            <label class="small fw-900 text-dark mb-2 text-uppercase tracking-1">Kode Cabang</label>
                            <input type="text" wire:model="kode_cabang" class="form-control border-0 bg-light-subtle py-3 px-4 rounded-4 shadow-none fw-bold focus-ring-dark" placeholder="PST-JKT-01">
                            @error('kode_cabang') <small class="text-danger fw-bold mt-2 d-block">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-7">
                            <label class="small fw-900 text-dark mb-2 text-uppercase tracking-1">Nama Cabang</label>
                            <input type="text" wire:model="nama_cabang" class="form-control border-0 bg-light-subtle py-3 px-4 rounded-4 shadow-none fw-bold focus-ring-dark" placeholder="PSTORE Jakarta Pusat">
                            @error('nama_cabang') <small class="text-danger fw-bold mt-2 d-block">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-12">
                            <label class="small fw-900 text-dark mb-2 text-uppercase tracking-1">Zona Waktu (Timezone)</label>
                            <select wire:model="timezone" class="form-select border-0 bg-light-subtle py-3 px-4 rounded-4 shadow-none fw-bold focus-ring-dark">
                                <option value="Asia/Jakarta">WIB - Waktu Indonesia Barat (Jakarta, Sumatera, Jawa)</option>
                                <option value="Asia/Makassar">WITA - Waktu Indonesia Tengah (Bali, Kalimantan, Sulawesi)</option>
                                <option value="Asia/Jayapura">WIT - Waktu Indonesia Timur (Papua, Maluku)</option>
                            </select>
                            @error('timezone') <small class="text-danger fw-bold mt-2 d-block">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-12">
                            <label class="small fw-900 text-dark mb-2 text-uppercase tracking-1">Lokasi & Alamat Lengkap</label>
                            <textarea wire:model="lokasi" rows="3" class="form-control border-0 bg-light-subtle py-3 px-4 rounded-4 shadow-none fw-bold focus-ring-dark" placeholder="Masukkan alamat lengkap cabang..."></textarea>
                            @error('lokasi') <small class="text-danger fw-bold mt-2 d-block">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <div class="d-grid gap-3 mt-5">
                        <button type="submit" wire:loading.attr="disabled" class="btn btn-dark py-3 rounded-4 fw-900 shadow-lg hover-scale transition-all">
                            <span wire:loading.remove>SIMPAN DATA CABANG</span>
                            <span wire:loading class="spinner-border spinner-border-sm"></span>
                        </button>
                        <a href="{{ route('cabang.index') }}" class="btn btn-link text-decoration-none text-muted fw-bold small text-center text-uppercase tracking-widest">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>