<div wire:poll.10s> 
    <div class="p-4 animate__animated animate__fadeIn">
        
        {{-- HEADER --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-5 gap-4">
            <div class="header-left ps-3 border-start border-5 border-dark">
                <h1 class="fw-900 text-dark mb-0 tracking-tighter display-5">Manajemen User</h1>
                <p class="text-muted small fw-bold text-uppercase mb-0 mt-1" style="letter-spacing: 4px; opacity: 0.7;">Otoritas & Akses Pengguna</p>
            </div>
            <div class="header-right">
                <button wire:click="resetInputFields" data-bs-toggle="modal" data-bs-target="#userModal" 
                   class="btn btn-dark rounded-pill px-5 py-3 fw-900 d-flex align-items-center shadow-premium hover-scale transition-all">
                    <i class="fas fa-plus-circle me-2 fs-5"></i> 
                    <span class="small tracking-widest text-uppercase">Tambah Pengguna</span>
                </button>
            </div>
        </div>

        {{-- TABLE CARD --}}
        <div class="glass-card rounded-5 shadow-extra-lg bg-white overflow-hidden border border-light-subtle">
            <div class="p-4 bg-white d-flex flex-column flex-md-row justify-content-between align-items-center gap-4 border-bottom border-light">
                <div class="search-box-modern w-100 w-md-50 position-relative">
                    <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-4 text-muted"></i>
                    <input type="text" wire:model.live.debounce.300ms="search" 
                        class="form-control border-0 bg-light-subtle py-3 ps-5 rounded-pill shadow-none fw-600 transition-all focus-white border-focus-dark" 
                        placeholder="Cari nama, email, atau role...">
                </div>
                <div class="text-end me-4 d-none d-lg-block">
                    <p class="mb-0 extra-small fw-900 text-dark text-uppercase opacity-40">Total Akun</p>
                    <p class="mb-0 h5 fw-black text-dark">{{ $users->total() }}</p>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 border-0">
                    <thead class="bg-light-subtle">
                        <tr>
                            <th class="ps-5 py-4 border-0 extra-small fw-900 text-dark text-uppercase tracking-2">Pengguna</th>
                            <th class="py-4 border-0 extra-small fw-900 text-dark text-uppercase tracking-2">Role</th>
                            <th class="py-4 border-0 extra-small fw-900 text-dark text-uppercase tracking-2">Akses Cabang</th>
                            <th class="py-4 border-0 extra-small fw-900 text-dark text-uppercase tracking-2 text-center">Status</th>
                            <th class="text-end pe-5 py-4 border-0 extra-small fw-900 text-dark text-uppercase tracking-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="border-0">
                        @forelse($users as $user)
                        <tr class="table-row-premium transition-all">
                            {{-- KOLOM PENGGUNA --}}
                            <td class="ps-5">
                                <div class="d-flex align-items-center">
                                    <div class="position-relative">
                                        <div class="avatar-circle-lg bg-dark text-white fw-900 shadow-sm me-3">
                                            {{ strtoupper(substr($user->nama_lengkap, 0, 1)) }}
                                        </div>
                                        @if($user->isOnline())
                                            <span class="position-absolute top-0 start-0 translate-middle p-1 bg-success border border-2 border-white rounded-circle animate__animated animate__pulse animate__infinite" 
                                                  style="width: 12px; height: 12px; margin-top: 5px; margin-left: 5px;"></span>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="fw-900 text-dark mb-0 fs-6 d-flex align-items-center gap-2">
                                            {{ $user->nama_lengkap }}
                                            @if($user->id === auth()->id())
                                                <span class="badge bg-secondary-subtle text-secondary extra-small py-1 px-2 border-0 fw-bold">ME</span>
                                            @endif
                                        </div>
                                        <div class="d-flex flex-column">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="extra-small text-muted fw-bold">{{ $user->email }}</span>
                                                <span class="v-line h-10px w-1px bg-dark opacity-20"></span>
                                                <span class="extra-small text-dark fw-black opacity-70">ID: {{ $user->idlogin }}</span>
                                            </div>
                                            <div class="extra-small mt-1">
                                                @if($user->isOnline())
                                                    <span class="text-success fw-bold text-uppercase tracking-1" style="font-size: 0.55rem;">
                                                        <i class="fas fa-circle me-1" style="font-size: 0.4rem;"></i> Online
                                                    </span>
                                                @else
                                                    <span class="text-muted opacity-50 italic fw-bold" style="font-size: 0.55rem;">
                                                        <i class="far fa-clock me-1"></i> {{ $user->last_seen_formatted }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- KOLOM ROLE --}}
                            <td>
                                <span class="badge {{ $user->role == 'superadmin' ? 'bg-danger-subtle text-danger border border-danger-subtle' : 'bg-dark text-white' }} rounded-pill px-3 py-2 extra-small fw-900 text-uppercase tracking-widest">
                                    {{ str_replace('_', ' ', $user->role) }}
                                </span>
                            </td>

                            {{-- KOLOM CABANG --}}
                            <td>
                                @if($user->role === 'superadmin')
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle extra-small fw-bold px-2 py-1">
                                        <i class="fas fa-globe me-1"></i> GLOBAL ACCESS
                                    </span>
                                @elseif($user->role === 'audit')
                                    <div class="d-flex flex-wrap gap-1" style="max-width: 200px;">
                                        @forelse($user->branches as $b)
                                            <span class="badge bg-white text-dark border extra-small fw-bold shadow-sm">{{ $b->nama_cabang }}</span>
                                        @empty
                                            <span class="text-danger extra-small fw-bold fst-italic">Tidak ada cabang</span>
                                        @endforelse
                                    </div>
                                @else
                                    @if($user->cabang)
                                        <span class="fw-bold text-dark extra-small d-flex align-items-center">
                                            <i class="fas fa-map-marker-alt me-1 text-secondary opacity-50"></i> 
                                            {{ $user->cabang->nama_cabang }}
                                        </span>
                                    @else
                                        <span class="text-muted extra-small fw-bold opacity-30 italic">pusat / internal</span>
                                    @endif
                                @endif
                            </td>
                            
                            {{-- KOLOM STATUS (SWITCH) --}}
                            <td class="text-center">
                                @if($user->id !== auth()->id() && $user->role !== 'superadmin')
                                    <div class="form-check form-switch d-flex justify-content-center mb-1">
                                        <input class="form-check-input shadow-none border-secondary" type="checkbox" wire:click="toggleStatus({{ $user->id }})" {{ $user->is_active ? 'checked' : '' }} style="cursor: pointer; transform: scale(1.2);">
                                    </div>
                                    <span class="extra-small fw-bold {{ $user->is_active ? 'text-success' : 'text-danger' }}">
                                        {{ $user->is_active ? 'AKTIF' : 'NON-AKTIF' }}
                                    </span>
                                @else
                                    <span class="badge bg-light text-muted border extra-small fw-bold"><i class="fas fa-lock me-1"></i> LOCKED</span>
                                @endif
                            </td>

                            {{-- KOLOM AKSI --}}
                            <td class="text-end pe-5">
                                <div class="d-flex justify-content-end gap-2">
                                    <button wire:click="edit({{ $user->id }})" data-bs-toggle="modal" data-bs-target="#userModal" class="btn btn-sm btn-light border rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                        <i class="fas fa-pen text-primary extra-small"></i>
                                    </button>
                                    @if($user->id !== auth()->id())
                                    <button wire:confirm="Hapus user ini?" wire:click="delete({{ $user->id }})" class="btn btn-sm btn-light border rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                        <i class="fas fa-trash text-danger extra-small"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="py-5 opacity-50">
                                    <i class="fas fa-users-slash display-4 mb-3 text-secondary"></i>
                                    <p class="text-muted fw-bold text-uppercase tracking-widest small mb-0">Tidak Ada Data User</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="p-4 border-top border-light">
                {{ $users->links() }}
            </div>
        </div>
    </div>

    {{-- MODAL FORM (PREMIUM DESIGN) --}}
    @teleport('body')
    <div wire:ignore.self class="modal fade" id="userModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-5 border-0 shadow-extra-lg overflow-hidden">
                <div class="modal-header border-0 px-5 pt-5 pb-0 bg-white">
                    <div>
                        <h3 class="fw-900 text-dark mb-1">{{ $isEdit ? 'Edit User' : 'User Baru' }}</h3>
                        <p class="text-secondary small fw-bold text-uppercase tracking-wide mb-0">
                            {{ $isEdit ? 'Perbarui data akses pengguna' : 'Tambahkan anggota tim baru' }}
                        </p>
                    </div>
                    <button type="button" class="btn-close bg-light p-2 rounded-circle" data-bs-dismiss="modal" wire:click="resetInputFields"></button>
                </div>
                
                <div class="modal-body p-5">
                    <form wire:submit.prevent="store">
                        
                        {{-- STATUS ACTIVE SWITCH --}}
                        <div class="d-flex justify-content-end mb-4">
                            <div class="form-check form-switch bg-light px-4 py-2 rounded-pill border d-flex align-items-center gap-3">
                                <label class="form-check-label fw-bold text-dark small mb-0 cursor-pointer text-uppercase" for="statusSwitch">
                                    Status Akun: <span class="{{ $is_active ? 'text-success' : 'text-danger' }}">{{ $is_active ? 'AKTIF' : 'NON-AKTIF' }}</span>
                                </label>
                                <input class="form-check-input ms-0 mt-0 shadow-none border-secondary" type="checkbox" wire:model.live="is_active" id="statusSwitch" style="transform: scale(1.3);">
                            </div>
                        </div>

                        {{-- SECTION 1: INFORMASI DASAR --}}
                        <h6 class="fw-bold text-secondary text-uppercase mb-3 small tracking-widest border-bottom pb-2">Informasi Dasar</h6>
                        <div class="row g-4 mb-5">
                            <div class="col-md-6">
                                <label class="small fw-bold text-dark mb-2">Nama Lengkap</label>
                                <div class="input-group">
                                    <span class="input-group-text border-0 bg-light-subtle rounded-start-4 ps-3 text-muted"><i class="fas fa-user"></i></span>
                                    <input type="text" wire:model="nama_lengkap" class="form-control border-0 bg-light-subtle py-3 rounded-end-4 shadow-none fw-bold" placeholder="Contoh: Budi Santoso">
                                </div>
                                @error('nama_lengkap') <small class="text-danger fw-bold mt-1 d-block">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="small fw-bold text-dark mb-2">Tanggal Lahir</label>
                                <div class="input-group">
                                    <span class="input-group-text border-0 bg-light-subtle rounded-start-4 ps-3 text-muted"><i class="fas fa-calendar-alt"></i></span>
                                    <input type="date" wire:model="tanggal_lahir" class="form-control border-0 bg-light-subtle py-3 rounded-end-4 shadow-none fw-bold">
                                </div>
                                @error('tanggal_lahir') <small class="text-danger fw-bold mt-1 d-block">{{ $message }}</small> @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label class="small fw-bold text-dark mb-2">Email Resmi</label>
                                <div class="input-group">
                                    <span class="input-group-text border-0 bg-light-subtle rounded-start-4 ps-3 text-muted"><i class="fas fa-envelope"></i></span>
                                    <input type="email" wire:model="email" class="form-control border-0 bg-light-subtle py-3 rounded-end-4 shadow-none fw-bold" placeholder="user@pstore.com">
                                </div>
                                @error('email') <small class="text-danger fw-bold mt-1 d-block">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="small fw-bold text-dark mb-2">Nomor WhatsApp / HP</label>
                                <div class="input-group">
                                    <span class="input-group-text border-0 bg-light-subtle rounded-start-4 ps-3 text-muted"><i class="fab fa-whatsapp"></i></span>
                                    <input type="text" class="form-control border-0 bg-light-subtle py-3 rounded-end-4 shadow-none fw-bold" placeholder="0812...">
                                </div>
                            </div>
                        </div>

                        {{-- SECTION 2: OTORITAS & KEAMANAN --}}
                        <h6 class="fw-bold text-secondary text-uppercase mb-3 small tracking-widest border-bottom pb-2">Otoritas & Keamanan</h6>
                        <div class="row g-4">
                            <div class="col-md-12">
                                <label class="small fw-bold text-dark mb-2">Hak Akses (Role)</label>
                                <select wire:model.live="role" class="form-select border-0 bg-dark text-white py-3 px-4 rounded-4 shadow-lg fw-bold">
                                    <option value="" class="text-muted">-- PILIH ROLE --</option>
                                    @if(Auth::user()->role === 'superadmin')
                                        <option value="superadmin">SUPERADMIN (FULL ACCESS)</option>
                                        <option value="audit">AUDIT (MULTI CABANG)</option>
                                    @endif
                                    <option value="adminproduk">ADMIN PRODUK</option>
                                    <option value="analis">ANALIS DATA</option>
                                    <option value="distributor">DISTRIBUTOR MITRA</option>
                                    <option value="leader">LEADER TEAM</option>
                                    <option value="sales">SALES / KASIR</option>
                                    <option value="gudang">STAFF GUDANG</option>
                                </select>
                                @error('role') <small class="text-danger fw-bold mt-1 d-block">{{ $message }}</small> @enderror
                            </div>

                            {{-- DYNAMIC FIELD: CABANG (SINGLE) --}}
                            @if($role && !in_array($role, ['superadmin', 'audit', 'distributor']))
                                <div class="col-md-12 animate__animated animate__fadeIn">
                                    <div class="p-3 bg-warning-subtle rounded-4 border border-warning-subtle">
                                        <label class="small fw-bold text-dark mb-2">Penempatan Cabang</label>
                                        <select wire:model="cabang_id" class="form-select border-0 bg-white py-3 px-4 rounded-4 shadow-sm fw-bold text-dark">
                                            <option value="">-- Pilih Lokasi Kerja --</option>
                                            @foreach($cabangs as $c)
                                                <option value="{{ $c->id }}">{{ $c->nama_cabang }}</option>
                                            @endforeach
                                        </select>
                                        @error('cabang_id') <small class="text-danger fw-bold mt-1 d-block">{{ $message }}</small> @enderror
                                    </div>
                                </div>
                            @endif

                            {{-- DYNAMIC FIELD: MULTI CABANG (AUDIT) --}}
                            @if($role === 'audit')
                                <div class="col-md-12 animate__animated animate__fadeIn">
                                    <div class="p-4 bg-light border rounded-4">
                                        <label class="small fw-bold text-dark mb-3 d-block text-uppercase">Cakupan Wilayah Audit</label>
                                        <div class="row g-3" style="max-height: 150px; overflow-y: auto;">
                                            @foreach($cabangs as $c)
                                            <div class="col-md-6">
                                                <div class="form-check bg-white p-2 rounded-3 border">
                                                    <input class="form-check-input ms-1 border-secondary" type="checkbox" value="{{ $c->id }}" wire:model="selected_branches" id="cb_{{ $c->id }}">
                                                    <label class="form-check-label fw-bold small text-dark ms-2 cursor-pointer" for="cb_{{ $c->id }}">{{ $c->nama_cabang }}</label>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                        @error('selected_branches') <small class="text-danger fw-bold mt-1 d-block">{{ $message }}</small> @enderror
                                    </div>
                                </div>
                            @endif

                            {{-- DYNAMIC FIELD: DISTRIBUTOR --}}
                            @if($role === 'distributor')
                                <div class="col-md-12 animate__animated animate__fadeIn">
                                    <label class="small fw-bold text-primary mb-2">Afiliasi Mitra Distributor</label>
                                    <select wire:model="distributor_id" class="form-select border-2 border-primary bg-white py-3 px-4 rounded-4 shadow-sm fw-bold">
                                        <option value="">-- Pilih Mitra --</option>
                                        @foreach($distributors as $dist)
                                            <option value="{{ $dist->id }}">{{ $dist->nama_distributor }}</option>
                                        @endforeach
                                    </select>
                                    @error('distributor_id') <small class="text-danger fw-bold mt-1 d-block">{{ $message }}</small> @enderror
                                </div>
                            @endif

                            <div class="col-md-6">
                                <label class="small fw-bold text-dark mb-2">ID Login (Username)</label>
                                <div class="input-group">
                                    <span class="input-group-text border-0 bg-light-subtle rounded-start-4 ps-3 text-muted"><i class="fas fa-id-badge"></i></span>
                                    <input type="text" wire:model="idlogin" class="form-control border-0 bg-light-subtle py-3 rounded-end-4 shadow-none fw-bold">
                                </div>
                                @error('idlogin') <small class="text-danger fw-bold mt-1 d-block">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="small fw-bold text-dark mb-2">Password {{ $isEdit ? '(Opsional)' : '' }}</label>
                                <div class="input-group">
                                    <span class="input-group-text border-0 bg-light-subtle rounded-start-4 ps-3 text-muted"><i class="fas fa-lock"></i></span>
                                    <input type="password" wire:model="password" class="form-control border-0 bg-light-subtle py-3 rounded-end-4 shadow-none fw-bold" placeholder="••••••">
                                </div>
                                @error('password') <small class="text-danger fw-bold mt-1 d-block">{{ $message }}</small> @enderror
                            </div>
                        </div>

                        {{-- FOOTER ACTION --}}
                        <div class="d-grid gap-2 mt-5 pt-3 border-top">
                            <button type="submit" class="btn btn-dark py-3 rounded-4 fw-900 shadow-lg hover-scale transition-all text-uppercase tracking-wide">
                                <i class="fas fa-save me-2"></i> {{ $isEdit ? 'Simpan Perubahan' : 'Daftarkan Pengguna' }}
                            </button>
                            <button type="button" wire:click="resetInputFields" data-bs-dismiss="modal" class="btn btn-link text-muted fw-bold text-decoration-none small text-uppercase">Batal & Kembali</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endteleport

    <style>
        .fw-900 { font-weight: 900 !important; }
        .shadow-extra-lg { box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); }
        .rounded-5 { border-radius: 1.5rem !important; }
        .avatar-circle-lg { width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; border-radius: 50%; }
        .table-row-premium { transition: background-color 0.2s; }
        .table-row-premium:hover { background-color: #f8f9fa; }
        .cursor-pointer { cursor: pointer; }
        
        /* Custom Input Icon Style */
        .input-group-text { background-color: #f8f9fa; }
    </style>
</div>

@script
<script>
    Livewire.on('close-modal', () => {
        const modal = bootstrap.Modal.getInstance(document.getElementById('userModal'));
        if(modal) modal.hide();
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
    });
</script>
@endscript