<div wire:poll.10s class="min-vh-100 bg-light-subtle mobile-spacer">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="p-4 p-lg-5 animate__animated animate__fadeIn">
        {{-- HEADER & TOOLBAR SAMA SEPERTI SEBELUMNYA --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5 gap-4">
            <div>
                <h1 class="display-6 fw-black text-dark mb-1 tracking-tight">Team & Access</h1>
                <p class="text-secondary fw-medium mb-0 d-flex align-items-center gap-2">
                    <span class="badge bg-dark text-white rounded-pill px-3 py-2">{{ $users->total() }} Users</span>
                    <span class="text-muted small text-uppercase tracking-wider">Kelola Otoritas Pengguna</span>
                </p>
            </div>
            <button wire:click="resetInputFields" data-bs-toggle="modal" data-bs-target="#userModal" class="btn btn-dark rounded-pill px-5 py-3 fw-bold shadow-lg hover-lift transition-all d-flex align-items-center gap-2">
                <i class="fas fa-plus text-white small"></i> <span>New Member</span>
            </button>
        </div>

        {{-- CARD & TABLE --}}
        <div class="card border-0 shadow-xl rounded-5 overflow-hidden bg-white">
            {{-- SEARCH BAR --}}
            <div class="p-4 border-bottom border-light-subtle bg-white sticky-top z-1">
                <div class="row g-3 align-items-center">
                    <div class="col-12 col-md-4">
                        <div class="position-relative">
                            <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-4 text-muted opacity-50"></i>
                            <input type="text" wire:model.live.debounce.300ms="search" class="form-control border-0 bg-light py-3 ps-5 rounded-pill fw-semibold text-dark placeholder-muted focus-ring-dark" placeholder="Cari user...">
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-5 py-4 text-secondary text-uppercase extra-small fw-bold">User Profile</th>
                            <th class="py-4 text-secondary text-uppercase extra-small fw-bold">Role</th>
                            <th class="py-4 text-secondary text-uppercase extra-small fw-bold">Location Access</th>
                            <th class="py-4 text-center text-secondary text-uppercase extra-small fw-bold">Status</th>
                            <th class="pe-5 py-4 text-end text-secondary text-uppercase extra-small fw-bold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($users as $user)
                        <tr class="group-hover-bg transition-all cursor-pointer">
                            <td class="ps-5 py-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar-circle-xl bg-gradient-dark text-white fw-bold d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; border-radius: 50%;">
                                        {{ strtoupper(substr($user->nama_lengkap, 0, 1)) }}
                                    </div>
                                    <div>
                                        <h6 class="fw-bold text-dark mb-0">{{ $user->nama_lengkap }}</h6>
                                        <div class="text-muted small">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border shadow-sm rounded-pill px-3 py-2 fw-bold text-uppercase extra-small">
                                    {{ str_replace('_', ' ', $user->role) }}
                                </span>
                            </td>
                            <td>
                                {{-- LOGIK TAMPILAN LOKASI --}}
                                @if($user->distributor_id)
                                    <div class="d-flex align-items-center gap-2 text-primary fw-bold small">
                                        <i class="fas fa-truck"></i> {{ $user->distributor->nama_distributor }}
                                    </div>
                                @elseif($user->gudang_id)
                                    <div class="d-flex align-items-center gap-2 text-info fw-bold small">
                                        <i class="fas fa-warehouse"></i> {{ $user->gudang->nama_gudang }}
                                    </div>
                                @elseif($user->cabang_id)
                                    <div class="d-flex align-items-center gap-2 text-warning fw-bold small">
                                        <i class="fas fa-map-marker-alt"></i> {{ $user->cabang->nama_cabang }}
                                    </div>
                                @elseif($user->role == 'audit')
                                    <span class="text-muted small">Multi-Branch Access</span>
                                @else
                                    <span class="text-muted small">Headquarters</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="form-check form-switch d-flex justify-content-center">
                                    <input class="form-check-input" type="checkbox" wire:click="toggleStatus({{ $user->id }})" {{ $user->is_active ? 'checked' : '' }}>
                                </div>
                            </td>
                            <td class="pe-5 text-end">
                                <button wire:click="edit({{ $user->id }})" data-bs-toggle="modal" data-bs-target="#userModal" class="btn btn-icon btn-light rounded-circle shadow-sm"><i class="fas fa-pen fa-xs"></i></button>
                                <button wire:confirm="Hapus user?" wire:click="delete({{ $user->id }})" class="btn btn-icon btn-light rounded-circle shadow-sm text-danger"><i class="fas fa-trash fa-xs"></i></button>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center py-5 text-muted">No users found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-top">{{ $users->links() }}</div>
        </div>
    </div>

    {{-- MODAL FORM --}}
    @teleport('body')
    <div wire:ignore.self class="modal fade" id="userModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-2xl rounded-5 overflow-hidden">
                <div class="modal-header bg-white border-0 p-5 pb-0">
                    <h2 class="fw-black text-dark mb-0">{{ $isEdit ? 'Edit User' : 'New User' }}</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" wire:click="resetInputFields"></button>
                </div>
                <div class="modal-body p-5 pt-4">
                    <form wire:submit.prevent="store">
                        {{-- Field Standard --}}
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control bg-light border-0 fw-bold" id="name" wire:model="nama_lengkap" placeholder="Name">
                                    <label for="name">Full Name</label>
                                </div>
                                @error('nama_lengkap') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="date" class="form-control bg-light border-0 fw-bold" id="dob" wire:model="tanggal_lahir">
                                    <label for="dob">Date of Birth</label>
                                </div>
                                @error('tanggal_lahir') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control bg-light border-0 fw-bold" id="lid" wire:model="idlogin" placeholder="ID">
                                    <label for="lid">Login ID</label>
                                </div>
                                @error('idlogin') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="email" class="form-control bg-light border-0 fw-bold" id="mail" wire:model="email" placeholder="Email">
                                    <label for="mail">Email Address</label>
                                </div>
                                @error('email') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            
                            {{-- ROLE SELECTION --}}
                            <div class="col-12 mt-4">
                                <h6 class="text-uppercase text-muted extra-small fw-bold">Access Control</h6>
                                <div class="bg-dark p-1 rounded-4">
                                    <div class="form-floating">
                                        <select class="form-select border-0 bg-white fw-bold rounded-4" id="role" wire:model.live="role">
                                            <option value="">Select Role</option>
                                            <option value="superadmin">SUPERADMIN</option>
                                            <option value="audit">AUDIT</option>
                                            <option value="inventory_staff">INVENTORY STAFF (Staff Gudang)</option>
                                            <option value="distributor">DISTRIBUTOR (Owner)</option>
                                            <option value="adminproduk">ADMIN PRODUK</option>
                                            <option value="sales">SALES</option>
                                            <option value="security">SECURITY</option>
                                        </select>
                                        <label for="role">User Role</label>
                                    </div>
                                </div>
                                @error('role') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            {{-- RADIO BUTTON KHUSUS INVENTORY STAFF --}}
                            @if($role === 'inventory_staff')
                                <div class="col-12 animate__animated animate__fadeIn">
                                    <div class="p-4 bg-light border rounded-4">
                                        <label class="d-block text-secondary small fw-bold text-uppercase mb-3">Penempatan Kerja</label>
                                        <div class="d-flex gap-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" id="plc_dist" value="distributor" wire:model.live="placement_type">
                                                <label class="form-check-label fw-bold" for="plc_dist"><i class="fas fa-truck me-1"></i> Distributor</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" id="plc_gudang" value="gudang" wire:model.live="placement_type">
                                                <label class="form-check-label fw-bold" for="plc_gudang"><i class="fas fa-warehouse me-1"></i> Gudang Fisik</label>
                                            </div>
                                        </div>
                                        @error('placement_type') <span class="text-danger small d-block mt-1">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            @endif

                            {{-- DYNAMIC DROPDOWNS --}}
                            @if($role === 'distributor' || ($role === 'inventory_staff' && $placement_type === 'distributor'))
                                <div class="col-12 animate__animated animate__fadeIn">
                                    <div class="form-floating">
                                        <select class="form-select bg-primary-subtle border-0 fw-bold rounded-4" wire:model="distributor_id">
                                            <option value="">Pilih Distributor</option>
                                            @foreach($distributors as $d) <option value="{{ $d->id }}">{{ $d->nama_distributor }}</option> @endforeach
                                        </select>
                                        <label class="text-primary fw-bold">Lokasi Distributor</label>
                                    </div>
                                    @error('distributor_id') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            @endif

                            @if($role === 'inventory_staff' && $placement_type === 'gudang')
                                <div class="col-12 animate__animated animate__fadeIn">
                                    <div class="form-floating">
                                        <select class="form-select bg-info-subtle border-0 fw-bold rounded-4" wire:model="gudang_id">
                                            <option value="">Pilih Gudang</option>
                                            @foreach($gudangs as $g) <option value="{{ $g->id }}">{{ $g->nama_gudang }}</option> @endforeach
                                        </select>
                                        <label class="text-dark fw-bold">Lokasi Gudang</label>
                                    </div>
                                    @error('gudang_id') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            @endif

                            @if(in_array($role, ['adminproduk', 'sales', 'security']))
                                <div class="col-12 animate__animated animate__fadeIn">
                                    <div class="form-floating">
                                        <select class="form-select bg-warning-subtle border-0 fw-bold rounded-4" wire:model="cabang_id">
                                            <option value="">Pilih Cabang</option>
                                            @foreach($cabangs as $c) <option value="{{ $c->id }}">{{ $c->nama_cabang }}</option> @endforeach
                                        </select>
                                        <label class="text-dark fw-bold">Lokasi Cabang</label>
                                    </div>
                                    @error('cabang_id') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            @endif

                            {{-- Password --}}
                            <div class="col-12">
                                <div class="form-floating">
                                    <input type="password" class="form-control bg-light border-0 fw-bold" id="pass" wire:model="password" placeholder="***">
                                    <label for="pass">Password</label>
                                </div>
                                @error('password') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="d-grid gap-3 mt-5 pt-3 border-top">
                            <button type="submit" class="btn btn-dark py-3 rounded-4 fw-black shadow-lg">
                                <i class="fas fa-check-circle me-2"></i> {{ $isEdit ? 'Save Changes' : 'Create Account' }}
                            </button>
                            <button type="button" wire:click="resetInputFields" data-bs-dismiss="modal" class="btn btn-white text-muted fw-bold small">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endteleport
</div>