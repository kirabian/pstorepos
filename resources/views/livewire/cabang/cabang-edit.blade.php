<div>
    <div class="row justify-content-center p-4 animate__animated animate__fadeIn">
        <div class="col-12 col-lg-7">
            <div class="card border-0 shadow-extra-lg rounded-5 p-4 p-md-5 bg-white">
                <div class="mb-5 border-start border-5 border-dark ps-3">
                    <h3 class="fw-900 text-dark mb-0 tracking-tighter display-6">Edit Cabang</h3>
                    <p class="text-secondary small fw-bold text-uppercase mt-1">Memperbarui: <span class="text-primary">{{ $kode_cabang }}</span></p>
                </div>

                <form wire:submit.prevent="update">
                    <div class="row g-4">
                        <div class="col-12">
                            <label class="small fw-900 text-dark mb-2 text-uppercase tracking-1">Nama Cabang</label>
                            <input type="text" wire:model="nama_cabang" class="form-control border-0 bg-light-subtle py-3 px-4 rounded-4 shadow-none fw-bold focus-ring-dark">
                            @error('nama_cabang') <small class="text-danger fw-bold mt-2 d-block">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-12">
                            <label class="small fw-900 text-dark mb-2 text-uppercase tracking-1">Zona Waktu (Timezone)</label>
                            <select wire:model="timezone" class="form-select border-0 bg-light-subtle py-3 px-4 rounded-4 shadow-none fw-bold focus-ring-dark">
                                <option value="Asia/Jakarta">WIB - Waktu Indonesia Barat</option>
                                <option value="Asia/Makassar">WITA - Waktu Indonesia Tengah</option>
                                <option value="Asia/Jayapura">WIT - Waktu Indonesia Timur</option>
                            </select>
                            @error('timezone') <small class="text-danger fw-bold mt-2 d-block">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-12">
                            <label class="small fw-900 text-dark mb-2 text-uppercase tracking-1">Alamat Lokasi</label>
                            <textarea wire:model="lokasi" rows="3" class="form-control border-0 bg-light-subtle py-3 px-4 rounded-4 shadow-none fw-bold focus-ring-dark"></textarea>
                            @error('lokasi') <small class="text-danger fw-bold mt-2 d-block">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <div class="row g-3 mt-5">
                        <div class="col-8">
                            <button type="submit" wire:loading.attr="disabled" class="btn btn-dark w-100 py-3 rounded-4 fw-900 shadow-lg hover-scale transition-all">
                                <span wire:loading.remove>UPDATE PERUBAHAN</span>
                                <span wire:loading class="spinner-border spinner-border-sm"></span>
                            </button>
                        </div>
                        <div class="col-4">
                            <a href="{{ route('cabang.index') }}" class="btn btn-light-subtle w-100 py-3 rounded-4 fw-bold border text-uppercase small">Batal</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>