<div wire:poll.5s>
    <div class="row justify-content-center p-4 animate__animated animate__fadeInUp">
        <div class="col-12 col-lg-7">
            <div class="card border-0 shadow-extra-lg rounded-5 p-4 p-md-5 bg-white">
                
                <div class="d-flex align-items-center mb-5 border-start border-5 border-dark ps-3">
                    <div>
                        <h3 class="fw-900 text-dark mb-0 tracking-tighter">Edit Otoritas Pengguna</h3>
                        <p class="text-secondary small fw-bold text-uppercase mb-0">User: <span class="text-primary">{{ $nama_lengkap }}</span></p>
                    </div>
                </div>
                
                <form wire:submit.prevent="update">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="small fw-900 text-dark mb-2 text-uppercase letter-spacing-1">Hak Akses</label>
                            <select wire:model.live="role" class="form-select border-0 bg-light-subtle py-3 px-4 rounded-4 shadow-none fw-600 focus-ring-dark">
                                <option value="superadmin">SUPERADMIN</option>
                                <option value="adminproduk">ADMIN PRODUK</option>
                                <option value="analis">ANALIS</option>
                                <option value="distributor">DISTRIBUTOR</option>
                                <option value="leader">LEADER</option>
                                <option value="sales">SALES</option>
                                <option value="gudang">GUDANG</option>
                                <option value="security">SECURITY</option>
                                <option value="audit">AUDIT (MULTI CABANG)</option>
                            </select>
                        </div>

                        @if($role && !in_array($role, ['superadmin', 'audit', 'distributor']))
                        <div class="col-md-6">
                            <label class="small fw-900 text-dark mb-2 text-uppercase letter-spacing-1">Cabang</label>
                            <select wire:model="cabang_id" class="form-select border-0 bg-light-subtle py-3 px-4 rounded-4 shadow-none fw-600">
                                <option value="">-- Pilih Cabang --</option>
                                @foreach($cabangs as $cab)
                                    <option value="{{ $cab->id }}">{{ $cab->nama_cabang }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        @if($role === 'audit')
                        <div class="col-12 animate__animated animate__fadeIn">
                            <label class="small fw-900 text-dark mb-2 text-uppercase letter-spacing-1">Akses Multi Cabang (Audit)</label>
                            <div class="p-4 bg-light-subtle rounded-4 border">
                                <div class="row">
                                    @foreach($cabangs as $cab)
                                    <div class="col-md-4 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="{{ $cab->id }}" wire:model="selected_branches" id="edit_cabang_{{ $cab->id }}">
                                            <label class="form-check-label fw-bold small" for="edit_cabang_{{ $cab->id }}">
                                                {{ $cab->nama_cabang }}
                                            </label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($role === 'distributor')
                        <div class="col-12 animate__animated animate__fadeIn">
                            <label class="small fw-900 text-primary mb-2 text-uppercase letter-spacing-1">Afiliasi Mitra</label>
                            <select wire:model="distributor_id" class="form-select border-2 border-primary py-3 px-4 rounded-4 shadow-none fw-600">
                                <option value="">-- Tanpa Mitra --</option>
                                @foreach($distributors as $dist)
                                    <option value="{{ $dist->id }}">{{ $dist->nama_distributor }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        <div class="col-12">
                            <label class="small fw-900 text-dark mb-2 text-uppercase letter-spacing-1">Nama Lengkap</label>
                            <input type="text" wire:model="nama_lengkap" class="form-control border-0 bg-light-subtle py-3 px-4 rounded-4 shadow-none focus-ring-dark">
                        </div>

                        <div class="col-12">
                            <label class="small fw-900 text-dark mb-2 text-uppercase letter-spacing-1">Reset Password</label>
                            <input type="password" wire:model="password" class="form-control border-0 bg-light-subtle py-3 px-4 rounded-4 shadow-none focus-ring-dark" placeholder="Kosongkan jika tidak ingin diubah">
                        </div>
                    </div>

                    <div class="row g-3 mt-5">
                        <div class="col-8">
                            <button type="submit" wire:loading.attr="disabled" class="btn btn-dark w-100 py-3 rounded-4 fw-900 shadow-lg hover-scale transition-all">
                                <span wire:loading.remove>SIMPAN PERUBAHAN</span>
                                <span wire:loading class="spinner-border spinner-border-sm"></span>
                            </button>
                        </div>
                        <div class="col-4">
                            <a href="{{ route('user.index') }}" class="btn btn-light-subtle w-100 py-3 rounded-4 fw-bold border shadow-none text-uppercase small">Batal</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>