<div class="container py-5 animate__animated animate__fadeIn">
    
    <div class="row justify-content-center">
        
        {{-- SECTION KIRI: KARTU FOTO & RINGKASAN --}}
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm rounded-4 text-center p-4 h-100">
                <div class="card-body">
                    <div class="position-relative d-inline-block mb-4">
                        {{-- Logic Preview Gambar --}}
                        @if ($photo) 
                            {{-- Jika user baru pilih file (Preview) --}}
                            <img src="{{ $photo->temporaryUrl() }}" class="rounded-circle shadow-sm object-fit-cover" style="width: 150px; height: 150px; border: 4px solid #fff; outline: 1px solid #dee2e6;">
                        @else
                            {{-- Foto Existing / Avatar Default --}}
                            <img src="{{ $existingPhoto }}" class="rounded-circle shadow-sm object-fit-cover" style="width: 150px; height: 150px; border: 4px solid #fff; outline: 1px solid #dee2e6;">
                        @endif

                        {{-- Tombol Kamera Overlay --}}
                        <label for="upload-photo" class="position-absolute bottom-0 end-0 bg-dark text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px; cursor: pointer; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
                            <i class="fas fa-camera"></i>
                        </label>
                        <input type="file" id="upload-photo" wire:model="photo" class="d-none" accept="image/*">
                    </div>

                    {{-- Loading State saat upload --}}
                    <div wire:loading wire:target="photo" class="text-primary fw-bold small mb-2">
                        Uploading... <i class="fas fa-spinner fa-spin"></i>
                    </div>

                    {{-- Error Message untuk Validasi Foto --}}
                    @error('photo') <span class="d-block text-danger small fw-bold mb-2">{{ $message }}</span> @enderror

                    <h4 class="fw-bold text-dark mb-1">{{ Auth::user()->nama_lengkap }}</h4>
                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2 text-uppercase fw-bold" style="font-size: 0.7rem;">
                        {{ str_replace('_', ' ', Auth::user()->role) }}
                    </span>

                    <hr class="my-4 border-light-subtle">

                    <div class="d-flex justify-content-between px-3 mb-2">
                        <span class="text-secondary small fw-bold">Status</span>
                        <span class="text-success small fw-bold"><i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i> Active</span>
                    </div>
                    <div class="d-flex justify-content-between px-3">
                        <span class="text-secondary small fw-bold">Bergabung</span>
                        <span class="text-dark small fw-bold">{{ Auth::user()->created_at->format('d M Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- SECTION KANAN: FORM BIODATA --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white p-4 border-bottom border-light-subtle">
                    <h5 class="fw-bold mb-0 text-dark"><i class="fas fa-user-edit me-2"></i> Edit Biodata</h5>
                </div>
                <div class="card-body p-4">
                    
                    @if (session()->has('message'))
                        <div class="alert alert-success border-0 bg-success bg-opacity-10 text-success fw-bold rounded-3 mb-4 alert-dismissible fade show">
                            <i class="fas fa-check-circle me-2"></i> {{ session('message') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form wire:submit.prevent="updateProfile">
                        
                        <div class="row g-4">
                            {{-- ID Login (Readonly) --}}
                            <div class="col-md-6">
                                <label class="form-label small text-secondary fw-bold text-uppercase">ID Login</label>
                                <input type="text" class="form-control bg-light text-muted fw-bold border-0" value="{{ $idlogin }}" readonly>
                                <small class="text-muted" style="font-size: 0.7rem;">*ID Login tidak dapat diubah.</small>
                            </div>

                            {{-- Role (Readonly) --}}
                            <div class="col-md-6">
                                <label class="form-label small text-secondary fw-bold text-uppercase">Role Jabatan</label>
                                <input type="text" class="form-control bg-light text-muted fw-bold border-0 text-uppercase" value="{{ str_replace('_', ' ', $role) }}" readonly>
                            </div>

                            {{-- Nama Lengkap --}}
                            <div class="col-md-6">
                                <label class="form-label small text-secondary fw-bold text-uppercase">Nama Lengkap</label>
                                <input type="text" wire:model="nama_lengkap" class="form-control @error('nama_lengkap') is-invalid @enderror fw-bold">
                                @error('nama_lengkap') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Email --}}
                            <div class="col-md-6">
                                <label class="form-label small text-secondary fw-bold text-uppercase">Alamat Email</label>
                                <input type="email" wire:model="email" class="form-control @error('email') is-invalid @enderror fw-bold">
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-5">
                            <button type="submit" class="btn btn-dark rounded-pill px-5 py-2 fw-bold shadow-sm hover-scale">
                                <span wire:loading.remove wire:target="updateProfile">Simpan Perubahan</span>
                                <span wire:loading wire:target="updateProfile"><i class="fas fa-spinner fa-spin me-2"></i> Menyimpan...</span>
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>

    </div>

    {{-- Style Tambahan --}}
    <style>
        .hover-scale { transition: transform 0.2s; }
        .hover-scale:hover { transform: scale(1.02); }
        .object-fit-cover { object-fit: cover; }
    </style>
</div>