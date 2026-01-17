<div wire:poll.10s class="min-vh-100 bg-light-subtle mobile-spacer">
    
    {{-- SweetAlert Script --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="p-4 p-lg-5 animate__animated animate__fadeIn">
        
        {{-- HEADER SECTION --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5 gap-4">
            <div>
                <h1 class="display-6 fw-black text-dark mb-1 tracking-tight">Team & Access</h1>
                <p class="text-secondary fw-medium mb-0 d-flex align-items-center gap-2">
                    <span class="badge bg-dark text-white rounded-pill px-3 py-2">{{ $users->total() }} Users</span>
                    <span class="text-muted small text-uppercase tracking-wider">Kelola Otoritas Pengguna</span>
                </p>
            </div>
            
            <button wire:click="resetInputFields" data-bs-toggle="modal" data-bs-target="#userModal" 
                class="btn btn-dark rounded-pill px-5 py-3 fw-bold shadow-lg hover-lift transition-all d-flex align-items-center gap-2">
                <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width: 24px; height: 24px;">
                    <i class="fas fa-plus text-white small"></i>
                </div>
                <span>New Member</span>
            </button>
        </div>

        {{-- CONTENT CARD --}}
        <div class="card border-0 shadow-xl rounded-5 overflow-hidden bg-white">
            {{-- TOOLBAR --}}
            <div class="p-4 border-bottom border-light-subtle bg-white sticky-top z-1">
                <div class="row g-3 align-items-center">
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="position-relative">
                            <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-4 text-muted opacity-50"></i>
                            <input type="text" wire:model.live.debounce.300ms="search" 
                                class="form-control border-0 bg-light py-3 ps-5 rounded-pill fw-semibold text-dark placeholder-muted focus-ring-dark" 
                                placeholder="Cari nama, email, atau role...">
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-8 text-md-end">
                        <div class="d-inline-flex align-items-center gap-3 small text-muted fw-bold">
                            <div class="d-flex align-items-center gap-1"><span class="bg-success rounded-circle" style="width: 8px; height: 8px;"></span> Online</div>
                            <div class="d-flex align-items-center gap-1"><span class="bg-secondary rounded-circle opacity-50" style="width: 8px; height: 8px;"></span> Offline</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TABLE --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-5 py-4 text-secondary text-uppercase extra-small fw-bold tracking-widest" style="min-width: 250px;">User Profile</th>
                            <th class="py-4 text-secondary text-uppercase extra-small fw-bold tracking-widest">Access Role</th>
                            <th class="py-4 text-secondary text-uppercase extra-small fw-bold tracking-widest">Location</th>
                            <th class="py-4 text-center text-secondary text-uppercase extra-small fw-bold tracking-widest">Status</th>
                            <th class="pe-5 py-4 text-end text-secondary text-uppercase extra-small fw-bold tracking-widest">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($users as $user)
                        <tr class="group-hover-bg transition-all cursor-pointer">
                            <td class="ps-5 py-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="position-relative">
                                        <div class="avatar-circle-xl bg-gradient-dark text-white fw-bold shadow-sm d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #1a1a1a 0%, #333 100%);">
                                            {{ strtoupper(substr($user->nama_lengkap, 0, 1)) }}
                                        </div>
                                        @if($user->isOnline()) <span class="position-absolute bottom-0 end-0 p-1 bg-success border border-2 border-white rounded-circle"></span> @endif
                                    </div>
                                    <div>
                                        <h6 class="fw-bold text-dark mb-0">{{ $user->nama_lengkap }} @if($user->id === auth()->id()) <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill extra-small px-2">YOU</span> @endif</h6>
                                        <div class="text-muted small lh-sm mt-1">
                                            <span class="fw-medium text-dark opacity-75">ID: {{ $user->idlogin }}</span> • <span class="text-secondary">{{ $user->email }}</span>
                                        </div>
                                        <div class="mt-1">
                                            @if($user->isOnline()) <span class="text-success extra-small fw-bold tracking-wide"><i class="fas fa-wifi me-1"></i> ONLINE SEKARANG</span>
                                            @else <span class="text-muted extra-small fw-bold fst-italic opacity-75"><i class="far fa-clock me-1"></i> Terakhir dilihat: {{ $user->last_seen_formatted }}</span> @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge {{ $user->role == 'superadmin' ? 'bg-danger text-white shadow-danger' : 'bg-white text-dark border shadow-sm' }} rounded-pill px-3 py-2 fw-bold text-uppercase extra-small tracking-wide">
                                    {{ str_replace('_', ' ', $user->role) }}
                                </span>
                            </td>
                            <td>
                                @if($user->role === 'superadmin') <div class="d-flex align-items-center gap-2 text-primary fw-bold small"><div class="bg-primary bg-opacity-10 p-1 rounded-circle"><i class="fas fa-globe"></i></div> Global Access</div>
                                @elseif($user->role === 'audit') <div class="d-flex align-items-center gap-2 text-dark fw-bold small"><div class="bg-dark bg-opacity-10 p-1 rounded-circle"><i class="fas fa-layer-group"></i></div> Multi-Branch</div>
                                @elseif($user->distributor_id) <div class="d-flex align-items-center gap-2 text-primary fw-bold small"><div class="bg-primary bg-opacity-10 p-1 rounded-circle"><i class="fas fa-truck"></i></div> {{ $user->distributor->nama_distributor }}</div>
                                @elseif($user->gudang_id) <div class="d-flex align-items-center gap-2 text-dark fw-bold small"><div class="bg-info bg-opacity-10 text-info p-1 rounded-circle"><i class="fas fa-warehouse"></i></div> {{ $user->gudang->nama_gudang }}</div>
                                @else <div class="d-flex align-items-center gap-2 text-dark fw-bold small"><div class="bg-warning bg-opacity-10 text-warning p-1 rounded-circle"><i class="fas fa-map-marker-alt"></i></div> {{ $user->cabang->nama_cabang ?? '-' }}</div>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($user->id !== auth()->id() && $user->role !== 'superadmin')
                                    <div class="form-check form-switch d-flex justify-content-center align-items-center">
                                        <input class="form-check-input cursor-pointer shadow-none" type="checkbox" role="switch" wire:click="toggleStatus({{ $user->id }})" {{ $user->is_active ? 'checked' : '' }} style="width: 2.5em; height: 1.25em;">
                                    </div>
                                    <span class="d-block mt-1 extra-small fw-bold {{ $user->is_active ? 'text-success' : 'text-danger' }}">{{ $user->is_active ? 'ACTIVE' : 'INACTIVE' }}</span>
                                @else
                                    <span class="badge bg-light text-muted border px-3 py-2 rounded-pill extra-small fw-bold"><i class="fas fa-lock me-1"></i> PROTECTED</span>
                                @endif
                            </td>
                            <td class="pe-5 text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    <button wire:click="edit({{ $user->id }})" data-bs-toggle="modal" data-bs-target="#userModal" class="btn btn-icon btn-light rounded-circle shadow-sm hover-primary transition-all"><i class="fas fa-pen fa-xs"></i></button>
                                    @if($user->id !== auth()->id()) <button wire:confirm="Hapus user ini?" wire:click="delete({{ $user->id }})" class="btn btn-icon btn-light rounded-circle shadow-sm hover-danger transition-all"><i class="fas fa-trash fa-xs"></i></button> @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="py-5 text-center"><h6 class="fw-bold text-dark mb-1">No Team Members Found</h6></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($users->hasPages()) <div class="p-4 border-top border-light-subtle bg-white">{{ $users->links() }}</div> @endif
        </div>
    </div>

    {{-- MODAL --}}
    @teleport('body')
    <div wire:ignore.self class="modal fade" id="userModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-2xl rounded-5 overflow-hidden">
                <div class="modal-header bg-white border-0 p-5 pb-0 align-items-start">
                    <div>
                        <span class="badge bg-dark text-white rounded-pill px-3 py-2 extra-small fw-bold mb-3 tracking-wide">{{ $isEdit ? 'UPDATE PROFILE' : 'NEW REGISTRATION' }}</span>
                        <h2 class="fw-black text-dark mb-1 tracking-tight">{{ $isEdit ? 'Edit Team Member' : 'Add New Member' }}</h2>
                    </div>
                    <button type="button" class="btn-close bg-light rounded-circle p-3 shadow-sm opacity-100" data-bs-dismiss="modal" wire:click="resetInputFields"></button>
                </div>

                <div class="modal-body p-5 pt-4">
                    <form wire:submit.prevent="store">
                        <div class="d-flex justify-content-end mb-4 pt-2">
                            <div class="form-check form-switch d-flex align-items-center gap-3 ps-0">
                                <label class="form-check-label fw-bold text-secondary small text-uppercase mb-0">Account Status</label>
                                <input class="form-check-input ms-0 shadow-none cursor-pointer" type="checkbox" role="switch" wire:model.live="is_active" style="width: 3em; height: 1.5em;">
                            </div>
                        </div>

                        <div class="row g-4">
                            <div class="col-12"><h6 class="text-uppercase text-muted extra-small fw-bold tracking-widest mb-3">Personal Details</h6></div>
                            <div class="col-md-6">
                                <div class="form-floating"><input type="text" class="form-control bg-light border-0 fw-bold text-dark rounded-4" placeholder="Full Name" wire:model="nama_lengkap"><label>Full Name</label></div>
                                @error('nama_lengkap') <span class="text-danger extra-small fw-bold ms-2">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating"><input type="date" class="form-control bg-light border-0 fw-bold text-dark rounded-4" wire:model="tanggal_lahir"><label>Date of Birth</label></div>
                                @error('tanggal_lahir') <span class="text-danger extra-small fw-bold ms-2">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating"><input type="email" class="form-control bg-light border-0 fw-bold text-dark rounded-4" placeholder="Email" wire:model="email"><label>Email</label></div>
                                @error('email') <span class="text-danger extra-small fw-bold ms-2">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-12 mt-4"><h6 class="text-uppercase text-muted extra-small fw-bold tracking-widest mb-3">Access Control</h6></div>
                            <div class="col-12">
                                <div class="bg-dark p-1 rounded-4">
                                    <div class="form-floating">
                                        <select class="form-select border-0 bg-white text-dark fw-bold rounded-4" wire:model.live="role" @if($isEdit && Auth::user()->role !== 'superadmin') disabled @endif>
                                            <option value="">Select Role</option>
                                            @if(Auth::user()->role === 'superadmin')
                                                <option value="superadmin">SUPERADMIN (Full Access)</option>
                                                <option value="audit">AUDIT (Multi-Branch Access)</option>
                                            @endif
                                            <option value="inventory_staff">INVENTORY STAFF / TOKO / ONLINE</option>
                                            <option value="adminproduk">ADMIN PRODUK</option>
                                            <option value="analis">ANALIST DATA</option>
                                            <option value="leader">TEAM LEADER</option>
                                            <option value="sales">SALES / CASHIER</option>
                                            <option value="security">SECURITY</option>
                                        </select>
                                        <label class="text-dark fw-bold">User Role</label>
                                    </div>
                                </div>
                                @error('role') <span class="text-danger extra-small fw-bold ms-2">{{ $message }}</span> @enderror
                            </div>

                            {{-- LOGIC INVENTORY STAFF (Sudah Termasuk Toko Offline & Online) --}}
                            @if($role === 'inventory_staff')
                                <div class="col-12 animate__animated animate__fadeIn">
                                    <div class="p-4 bg-light border rounded-4">
                                        <label class="d-block text-secondary small fw-bold text-uppercase mb-3">Penempatan / Jenis Akun</label>
                                        <div class="d-flex flex-wrap gap-3">
                                            <div class="form-check"><input class="form-check-input" type="radio" name="plc" value="distributor" wire:model.live="placement_type"><label class="form-check-label fw-bold">Distributor</label></div>
                                            <div class="form-check"><input class="form-check-input" type="radio" name="plc" value="gudang" wire:model.live="placement_type"><label class="form-check-label fw-bold">Gudang Fisik</label></div>
                                            <div class="form-check"><input class="form-check-input" type="radio" name="plc" value="toko_offline" wire:model.live="placement_type"><label class="form-check-label fw-bold">Toko Offline (Kasir)</label></div>
                                            <div class="form-check"><input class="form-check-input" type="radio" name="plc" value="toko_online" wire:model.live="placement_type"><label class="form-check-label fw-bold">Toko Online (Admin)</label></div>
                                        </div>
                                        @error('placement_type') <span class="text-danger extra-small fw-bold mt-2 d-block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            @endif

                            {{-- DYNAMIC DROPDOWNS --}}
                            @if($role === 'distributor' || ($role === 'inventory_staff' && $placement_type === 'distributor'))
                                <div class="col-12 animate__animated animate__fadeIn">
                                    <div class="form-floating"><select class="form-select bg-primary-subtle border-0 text-dark fw-bold rounded-4" wire:model="distributor_id"><option value="">Pilih Distributor</option>@foreach($distributors as $d)<option value="{{ $d->id }}">{{ $d->nama_distributor }}</option>@endforeach</select><label>Lokasi Distributor</label></div>
                                    @error('distributor_id') <span class="text-danger extra-small fw-bold ms-2">{{ $message }}</span> @enderror
                                </div>
                            @endif

                            @if($role === 'gudang' || ($role === 'inventory_staff' && $placement_type === 'gudang'))
                                <div class="col-12 animate__animated animate__fadeIn">
                                    <div class="form-floating"><select class="form-select bg-info-subtle border-0 text-dark fw-bold rounded-4" wire:model="gudang_id"><option value="">Pilih Gudang</option>@foreach($gudangs as $g)<option value="{{ $g->id }}">{{ $g->nama_gudang }}</option>@endforeach</select><label>Lokasi Gudang</label></div>
                                    @error('gudang_id') <span class="text-danger extra-small fw-bold ms-2">{{ $message }}</span> @enderror
                                </div>
                            @endif

                            @if(($role && !in_array($role, ['superadmin', 'audit', 'distributor', 'inventory_staff', 'gudang'])) || ($role === 'inventory_staff' && in_array($placement_type, ['toko_offline', 'toko_online'])))
                                <div class="col-12 animate__animated animate__fadeIn">
                                    <div class="form-floating"><select class="form-select bg-warning-subtle border-0 text-dark fw-bold rounded-4" wire:model="cabang_id"><option value="">Pilih Cabang</option>@foreach($cabangs as $c)<option value="{{ $c->id }}">{{ $c->nama_cabang }}</option>@endforeach</select><label>Lokasi Cabang</label></div>
                                    @error('cabang_id') <span class="text-danger extra-small fw-bold ms-2">{{ $message }}</span> @enderror
                                </div>
                            @endif

                            {{-- AUDIT MULTI BRANCH --}}
                            @if($role === 'audit')
                                <div class="col-12 animate__animated animate__fadeIn" x-data="{ open: false, selected: @entangle('selected_branches').live }">
                                    <div @click="open = !open" class="form-control bg-light border-0 rounded-4 py-3 px-4 d-flex justify-content-between align-items-center shadow-sm cursor-pointer">
                                        <div><span class="text-secondary fw-semibold small text-uppercase">Audit Coverage</span><div class="fw-bold text-dark" x-text="selected.length + ' Branches Selected'"></div></div>
                                        <i class="fas fa-chevron-down"></i>
                                    </div>
                                    <div x-show="open" class="mt-2 bg-white border rounded-4 p-3 shadow-sm" style="max-height:200px; overflow-y:auto;">
                                        @foreach($cabangs as $c)
                                            <div class="form-check"><input class="form-check-input" type="checkbox" value="{{ $c->id }}" wire:model.live="selected_branches"><label class="form-check-label">{{ $c->nama_cabang }}</label></div>
                                        @endforeach
                                    </div>
                                    @error('selected_branches') <span class="text-danger extra-small fw-bold ms-2">{{ $message }}</span> @enderror
                                </div>
                            @endif

                            <div class="col-12 mt-4"><h6 class="text-uppercase text-muted extra-small fw-bold tracking-widest mb-3">Login Credentials</h6></div>
                            <div class="col-md-6"><div class="form-floating"><input type="text" class="form-control bg-light border-0 fw-bold text-dark rounded-4" placeholder="Username" wire:model="idlogin"><label>Login ID</label></div>@error('idlogin') <span class="text-danger extra-small fw-bold ms-2">{{ $message }}</span> @enderror</div>
                            <div class="col-md-6"><div class="form-floating"><input type="password" class="form-control bg-light border-0 fw-bold text-dark rounded-4" placeholder="******" wire:model="password"><label>Password</label></div>@error('password') <span class="text-danger extra-small fw-bold ms-2">{{ $message }}</span> @enderror</div>
                        </div>

                        <div class="d-grid gap-3 mt-5 pt-3 border-top">
                            <button type="submit" class="btn btn-dark py-3 rounded-4 fw-black shadow-lg hover-scale transition-all text-uppercase tracking-wide"><i class="fas fa-check-circle me-2"></i> {{ $isEdit ? 'Save Changes' : 'Create Account' }}</button>
                            <button type="button" wire:click="resetInputFields" data-bs-dismiss="modal" class="btn btn-white text-muted fw-bold small text-uppercase hover-opacity">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endteleport

    <style>
        .fw-black { font-weight: 900; } .tracking-tight { letter-spacing: -0.025em; } .tracking-widest { letter-spacing: 0.1em; } .extra-small { font-size: 0.65rem; } .avatar-circle-xl { width: 50px; height: 50px; border-radius: 50%; font-size: 1.25rem; border: 3px solid #fff; } .btn-icon { width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border: 1px solid #f0f0f0; } .hover-lift:hover { transform: translateY(-2px); } .hover-primary:hover { background-color: #0d6efd; color: white; border-color: #0d6efd; } .hover-danger:hover { background-color: #dc3545; color: white; border-color: #dc3545; } .hover-scale:hover { transform: scale(1.02); } .shadow-xl { box-shadow: 0 20px 40px -10px rgba(0,0,0,0.05); } .shadow-2xl { box-shadow: 0 30px 60px -12px rgba(0,0,0,0.15); } .form-control:focus, .form-select:focus { box-shadow: none; background-color: #fff !important; ring: 2px solid #000; }
    </style>
</div>

@script
<script>
    Livewire.on('close-modal', () => { const modal = bootstrap.Modal.getInstance(document.getElementById('userModal')); if(modal) modal.hide(); document.querySelectorAll('.modal-backdrop').forEach(el => el.remove()); });
    Livewire.on('swal', (data) => { Swal.fire({ title: data[0].title, text: data[0].text, icon: data[0].icon, toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true }); });
</script>
@endscript