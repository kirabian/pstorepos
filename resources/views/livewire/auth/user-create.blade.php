<div> 
    <div class="row justify-content-center p-4 animate__animated animate__fadeInUp">
        <div class="col-12 col-lg-7">
            <div class="card border-0 shadow-extra-lg rounded-5 p-4 p-md-5 bg-white">
                
                <div class="mb-5 border-start border-5 border-dark ps-3">
                    <h3 class="fw-900 text-dark mb-0 tracking-tighter">Registrasi User</h3>
                    <p class="text-secondary small fw-bold text-uppercase">Otorisasi Anggota Baru PSTORE</p>
                </div>
                
                <form wire:submit.prevent="store">
                    <div class="row g-4">
                        <div class="col-md-12">
                            <label class="small fw-900 text-dark mb-2 text-uppercase letter-spacing-1">Hak Akses (Role)</label>
                            <select wire:model.live="role" class="form-select border-0 bg-light-subtle py-3 px-4 rounded-4 shadow-none fw-600">
                                <option value="">-- Pilih Role --</option>
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
                            @error('role') <small class="text-danger fw-bold mt-2 d-block">{{ $message }}</small> @enderror
                        </div>

                        @if($role && !in_array($role, ['superadmin', 'audit', 'distributor']))
                        <div class="col-md-12 animate__animated animate__fadeIn">
                            <label class="small fw-900 text-dark mb-2 text-uppercase letter-spacing-1">Penempatan Cabang</label>
                            <select wire:model="cabang_id" class="form-select border-0 bg-light-subtle py-3 px-4 rounded-4 shadow-none fw-600">
                                <option value="">-- Pilih Cabang --</option>
                                @foreach($cabangs as $cabang)
                                    <option value="{{ $cabang->id }}">{{ $cabang->nama_cabang }}</option>
                                @endforeach
                            </select>
                            @error('cabang_id') <small class="text-danger fw-bold mt-2 d-block">{{ $message }}</small> @enderror
                        </div>
                        @endif

                        @if($role === 'audit')
                        <div class="col-12 animate__animated animate__fadeIn">
                            <label class="small fw-900 text-dark mb-2 text-uppercase letter-spacing-1">Akses Multi Cabang (Audit)</label>
                            <div class="p-4 bg-light-subtle rounded-4 border">
                                <div class="row">
                                    @foreach($cabangs as $cabang)
                                    <div class="col-md-4 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="{{ $cabang->id }}" wire:model="selected_branches" id="cabang_{{ $cabang->id }}">
                                            <label class="form-check-label fw-bold small" for="cabang_{{ $cabang->id }}">
                                                {{ $cabang->nama_cabang }}
                                            </label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @error('selected_branches') <small class="text-danger fw-bold mt-2 d-block">{{ $message }}</small> @enderror
                        </div>
                        @endif

                        @if($role === 'distributor')
                        <div class="col-12 animate__animated animate__fadeInDown">
                            <label class="small fw-900 text-primary mb-2 text-uppercase letter-spacing-1">Hubungkan ke Mitra</label>
                            <select wire:model="distributor_id" class="form-select border-2 border-primary bg-white py-3 px-4 rounded-4 shadow-none fw-600">
                                <option value="">-- Pilih Distributor --</option>
                                @foreach($distributors as $dist)
                                    <option value="{{ $dist->id }}">{{ $dist->nama_distributor }}</option>
                                @endforeach
                            </select>
                            @error('distributor_id') <small class="text-danger fw-bold mt-2 d-block">{{ $message }}</small> @enderror
                        </div>
                        @endif

                        <div class="col-12">
                            <label class="small fw-900 text-dark mb-2 text-uppercase letter-spacing-1">Nama Lengkap</label>
                            <input type="text" wire:model="nama_lengkap" class="form-control border-0 bg-light-subtle py-3 px-4 rounded-4 shadow-none" placeholder="Masukkan nama sesuai identitas">
                        </div>

                        <div class="col-md-6">
                            <label class="small fw-900 text-dark mb-2 text-uppercase letter-spacing-1">ID Login</label>
                            <input type="text" wire:model="idlogin" class="form-control border-0 bg-light-subtle py-3 px-4 rounded-4 shadow-none">
                        </div>

                        <div class="col-md-6">
                            <label class="small fw-900 text-dark mb-2 text-uppercase letter-spacing-1">Email</label>
                            <input type="email" wire:model="email" class="form-control border-0 bg-light-subtle py-3 px-4 rounded-4 shadow-none">
                        </div>

                        <div class="col-12">
                            <label class="small fw-900 text-dark mb-2 text-uppercase letter-spacing-1">Kata Sandi</label>
                            <input type="password" wire:model="password" class="form-control border-0 bg-light-subtle py-3 px-4 rounded-4 shadow-none" placeholder="••••••••">
                        </div>
                    </div>

                    <div class="d-grid gap-3 mt-5">
                        <button type="submit" class="btn btn-dark py-3 rounded-4 fw-900 shadow-lg hover-scale transition-all">DAFTARKAN SEKARANG</button>
                        <a href="{{ route('user.index') }}" class="btn btn-link text-decoration-none text-muted fw-bold small text-center">BATAL</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>