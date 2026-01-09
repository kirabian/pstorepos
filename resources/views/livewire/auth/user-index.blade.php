<div wire:poll.10s class="min-vh-100 bg-light-subtle mobile-spacer">
    
    {{-- SweetAlert Script --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="p-4 p-lg-5 animate__animated animate__fadeIn">
        
        {{-- HEADER SECTION --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5 gap-4">
            <div>
                <h1 class="display-6 fw-black text-dark mb-1 tracking-tight">
                    Team & Access
                </h1>
                <p class="text-secondary fw-medium mb-0 d-flex align-items-center gap-2">
                    <span class="badge bg-dark text-white rounded-pill px-3 py-2">
                        {{ $users->total() }} Users
                    </span>
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
                            <div class="d-flex align-items-center gap-1">
                                <span class="bg-success rounded-circle" style="width: 8px; height: 8px;"></span> Online
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <span class="bg-secondary rounded-circle opacity-50" style="width: 8px; height: 8px;"></span> Offline
                            </div>
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
                            {{-- KOLOM PROFILE --}}
                            <td class="ps-5 py-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="position-relative">
                                        <div class="avatar-circle-xl bg-gradient-dark text-white fw-bold shadow-sm d-flex align-items-center justify-content-center" 
                                             style="background: linear-gradient(135deg, #1a1a1a 0%, #333 100%);">
                                            {{ strtoupper(substr($user->nama_lengkap, 0, 1)) }}
                                        </div>
                                        @if($user->isOnline())
                                            <span class="position-absolute bottom-0 end-0 p-1 bg-success border border-2 border-white rounded-circle"></span>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="d-flex align-items-center gap-2 mb-0">
                                            <h6 class="fw-bold text-dark mb-0">{{ $user->nama_lengkap }}</h6>
                                            @if($user->id === auth()->id())
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill extra-small px-2">YOU</span>
                                            @endif
                                        </div>
                                        
                                        <div class="text-muted small lh-sm mt-1">
                                            <span class="fw-medium text-dark opacity-75">ID: {{ $user->idlogin }}</span>
                                            <span class="text-light-gray mx-1">•</span>
                                            <span class="text-secondary">{{ $user->email }}</span>
                                        </div>

                                        {{-- FITUR LAST SEEN --}}
                                        <div class="mt-1">
                                            @if($user->isOnline())
                                                <span class="text-success extra-small fw-bold tracking-wide">
                                                    <i class="fas fa-wifi me-1"></i> ONLINE SEKARANG
                                                </span>
                                            @else
                                                <span class="text-muted extra-small fw-bold fst-italic opacity-75">
                                                    <i class="far fa-clock me-1"></i> Terakhir dilihat: {{ $user->last_seen_formatted }}
                                                </span>
                                            @endif
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
                                @if($user->role === 'superadmin')
                                    <div class="d-flex align-items-center gap-2 text-primary fw-bold small">
                                        <div class="bg-primary bg-opacity-10 p-1 rounded-circle"><i class="fas fa-globe"></i></div>
                                        Global Access
                                    </div>
                                @elseif($user->role === 'audit')
                                    <div class="d-flex flex-column gap-1">
                                        <span class="d-flex align-items-center gap-2 text-dark fw-bold small">
                                            <div class="bg-dark bg-opacity-10 p-1 rounded-circle"><i class="fas fa-layer-group"></i></div>
                                            Multi-Branch
                                        </span>
                                        <div class="d-flex flex-wrap gap-1 mt-1" style="max-width: 200px;">
                                            @forelse($user->branches->take(3) as $b)
                                                <span class="badge bg-light text-secondary border extra-small">{{ $b->nama_cabang }}</span>
                                            @empty
                                                <span class="text-muted extra-small fst-italic">No Branch Assigned</span>
                                            @endforelse
                                            @if($user->branches->count() > 3)
                                                <span class="badge bg-light text-secondary border extra-small">+{{ $user->branches->count() - 3 }}</span>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <div class="d-flex align-items-center gap-2 text-dark fw-bold small">
                                        <div class="bg-warning bg-opacity-10 text-warning p-1 rounded-circle"><i class="fas fa-map-marker-alt"></i></div>
                                        {{ $user->cabang->nama_cabang ?? 'Headquarters' }}
                                    </div>
                                @endif
                            </td>

                            <td class="text-center">
                                @if($user->id !== auth()->id() && $user->role !== 'superadmin')
                                    <div class="form-check form-switch d-flex justify-content-center align-items-center">
                                        <input class="form-check-input cursor-pointer shadow-none" type="checkbox" role="switch" 
                                            wire:click="toggleStatus({{ $user->id }})" 
                                            {{ $user->is_active ? 'checked' : '' }} 
                                            style="width: 2.5em; height: 1.25em;">
                                    </div>
                                    <span class="d-block mt-1 extra-small fw-bold {{ $user->is_active ? 'text-success' : 'text-danger' }}">
                                        {{ $user->is_active ? 'ACTIVE' : 'INACTIVE' }}
                                    </span>
                                @else
                                    <div class="d-flex justify-content-center">
                                        <span class="badge bg-light text-muted border px-3 py-2 rounded-pill extra-small fw-bold">
                                            <i class="fas fa-lock me-1"></i> PROTECTED
                                        </span>
                                    </div>
                                @endif
                            </td>

                            <td class="pe-5 text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    <button wire:click="edit({{ $user->id }})" data-bs-toggle="modal" data-bs-target="#userModal" 
                                        class="btn btn-icon btn-light rounded-circle shadow-sm hover-primary transition-all" title="Edit Access">
                                        <i class="fas fa-pen fa-xs"></i>
                                    </button>
                                    @if($user->id !== auth()->id())
                                        <button wire:confirm="Hapus user ini?" wire:click="delete({{ $user->id }})" 
                                            class="btn btn-icon btn-light rounded-circle shadow-sm hover-danger transition-all" title="Remove User">
                                            <i class="fas fa-trash fa-xs"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-5 text-center">
                                <div class="d-flex flex-column align-items-center py-4 opacity-50">
                                    <div class="bg-light p-4 rounded-circle mb-3">
                                        <i class="fas fa-users-slash fa-2x text-muted"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1">No Team Members Found</h6>
                                    <p class="text-secondary small mb-0">Try adjusting your search filters</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
            <div class="p-4 border-top border-light-subtle bg-white">
                {{ $users->links() }}
            </div>
            @endif
        </div>
    </div>

    {{-- PREMIUM MODAL --}}
    @teleport('body')
    <div wire:ignore.self class="modal fade" id="userModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-2xl rounded-5 overflow-hidden">
                
                {{-- Modal Header --}}
                <div class="modal-header bg-white border-0 p-5 pb-0 align-items-start">
                    <div>
                        <span class="badge bg-dark text-white rounded-pill px-3 py-2 extra-small fw-bold mb-3 tracking-wide">
                            {{ $isEdit ? 'UPDATE PROFILE' : 'NEW REGISTRATION' }}
                        </span>
                        <h2 class="fw-black text-dark mb-1 tracking-tight">{{ $isEdit ? 'Edit Team Member' : 'Add New Member' }}</h2>
                        <p class="text-secondary mb-0 fw-medium small">Manage access rights and personal information.</p>
                    </div>
                    <button type="button" class="btn-close bg-light rounded-circle p-3 shadow-sm opacity-100" data-bs-dismiss="modal" wire:click="resetInputFields"></button>
                </div>

                <div class="modal-body p-5 pt-4">
                    <form wire:submit.prevent="store">
                        
                        {{-- Toggle Status --}}
                        <div class="d-flex justify-content-end mb-4 pt-2">
                            <div class="form-check form-switch d-flex align-items-center gap-3 ps-0">
                                <label class="form-check-label fw-bold text-secondary small text-uppercase mb-0" for="activeSwitch">Account Status</label>
                                <input class="form-check-input ms-0 shadow-none cursor-pointer" type="checkbox" role="switch" id="activeSwitch" wire:model.live="is_active" style="width: 3em; height: 1.5em;">
                            </div>
                        </div>

                        <div class="row g-4">
                            {{-- PERSONAL INFO --}}
                            <div class="col-12">
                                <h6 class="text-uppercase text-muted extra-small fw-bold tracking-widest mb-3">Personal Details</h6>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control bg-light border-0 fw-bold text-dark rounded-4" id="nameInput" placeholder="Full Name" wire:model="nama_lengkap">
                                    <label for="nameInput" class="text-secondary fw-semibold">Full Name</label>
                                </div>
                                @error('nama_lengkap') <span class="text-danger extra-small fw-bold ms-2">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="date" class="form-control bg-light border-0 fw-bold text-dark rounded-4" id="dobInput" wire:model="tanggal_lahir">
                                    <label for="dobInput" class="text-secondary fw-semibold">Date of Birth</label>
                                </div>
                                @error('tanggal_lahir') <span class="text-danger extra-small fw-bold ms-2">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="email" class="form-control bg-light border-0 fw-bold text-dark rounded-4" id="emailInput" placeholder="name@example.com" wire:model="email">
                                    <label for="emailInput" class="text-secondary fw-semibold">Email Address</label>
                                </div>
                                @error('email') <span class="text-danger extra-small fw-bold ms-2">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control bg-light border-0 fw-bold text-dark rounded-4" id="phoneInput" placeholder="0812..." wire:model="distributor_id"> 
                                    <label for="phoneInput" class="text-secondary fw-semibold">Phone (Optional)</label>
                                </div>
                            </div>

                            {{-- ACCESS CONTROL --}}
                            <div class="col-12 mt-4">
                                <h6 class="text-uppercase text-muted extra-small fw-bold tracking-widest mb-3">Access Control</h6>
                            </div>

                            <div class="col-12">
                                <div class="bg-dark p-1 rounded-4">
                                    <div class="form-floating">
                                        <select class="form-select border-0 bg-white text-dark fw-bold rounded-4" id="roleSelect" wire:model.live="role">
                                            <option value="">Select Role</option>
                                            @if(Auth::user()->role === 'superadmin')
                                                <option value="superadmin">SUPERADMIN (Full Access)</option>
                                                <option value="audit">AUDIT (Multi-Branch Access)</option>
                                            @endif
                                            {{-- Role Operasional --}}
                                            <option value="adminproduk">ADMIN PRODUK</option>
                                            <option value="analis">ANALIST DATA</option>
                                            <option value="leader">TEAM LEADER</option>
                                            <option value="sales">SALES / CASHIER</option>
                                            <option value="gudang">INVENTORY STAFF</option>
                                            <option value="security">SECURITY</option>
                                        </select>
                                        <label for="roleSelect" class="text-dark fw-bold">User Role</label>
                                    </div>
                                </div>
                                @error('role') <span class="text-danger extra-small fw-bold ms-2">{{ $message }}</span> @enderror
                            </div>

                            {{-- DYNAMIC LOCATION (CABANG SINGLE) --}}
                            @if($role && !in_array($role, ['superadmin', 'audit', 'distributor']))
                                <div class="col-12 animate__animated animate__fadeIn">
                                    <div class="form-floating">
                                        <select class="form-select bg-warning-subtle border-0 text-dark fw-bold rounded-4" id="branchSelect" wire:model="cabang_id">
                                            <option value="">Select Branch Location</option>
                                            {{-- DROPDOWN INI SUDAH DFILTER DARI BACKEND --}}
                                            @foreach($cabangs as $c)
                                                <option value="{{ $c->id }}">{{ $c->nama_cabang }}</option>
                                            @endforeach
                                        </select>
                                        <label for="branchSelect" class="text-dark fw-bold">Assigned Branch</label>
                                    </div>
                                    @error('cabang_id') <span class="text-danger extra-small fw-bold ms-2">{{ $message }}</span> @enderror
                                </div>
                            @endif

                            {{-- MULTI CABANG (AUDIT ONLY) --}}
                            @if($role === 'audit')
                                <div class="col-12 animate__animated animate__fadeIn">
                                    
                                    <div x-data="{ 
                                        open: false, 
                                        selected: @entangle('selected_branches').live,
                                        toggle(id) {
                                            if (this.selected.includes(String(id))) {
                                                this.selected = this.selected.filter(item => item !== String(id));
                                            } else {
                                                this.selected.push(String(id));
                                            }
                                        }
                                    }">
                                        
                                        {{-- Trigger --}}
                                        <div @click="open = !open" 
                                             class="form-control bg-light border-0 rounded-4 py-3 px-4 d-flex justify-content-between align-items-center cursor-pointer shadow-sm hover-bg-light transition-all">
                                            <div>
                                                <span class="text-secondary fw-semibold small text-uppercase d-block mb-1" style="font-size: 0.7rem;">Audit Coverage</span>
                                                <div class="d-flex align-items-center gap-2">
                                                    <i class="fas fa-building text-dark opacity-50"></i>
                                                    <span class="fw-bold text-dark" x-text="selected.length > 0 ? selected.length + ' Branches Selected' : 'Select Target Branches...'"></span>
                                                </div>
                                            </div>
                                            <div class="bg-white rounded-circle p-2 shadow-sm">
                                                <i class="fas fa-chevron-down text-dark transition-all" :class="{'rotate-180': open}"></i>
                                            </div>
                                        </div>

                                        {{-- Dropdown --}}
                                        <div x-show="open" x-transition.opacity.duration.300ms
                                             class="mt-3 bg-white rounded-4 border border-light-subtle shadow-sm overflow-hidden" 
                                             style="display: none;">
                                            
                                            <div class="p-3 bg-light border-bottom border-light-subtle d-flex justify-content-between align-items-center">
                                                <span class="text-muted small fw-bold text-uppercase tracking-wide">Available Branches</span>
                                                <span class="badge bg-dark text-white rounded-pill px-2 extra-small">Multiple Select</span>
                                            </div>

                                            <div class="custom-scrollbar" style="max-height: 250px; overflow-y: auto;">
                                                <div class="p-2">
                                                    {{-- INI JUGA MENGGUNAKAN $cabangs YANG SUDAH DFILTER --}}
                                                    @foreach($cabangs as $c)
                                                        <div @click="toggle('{{ $c->id }}')" 
                                                             class="d-flex align-items-center justify-content-between p-3 rounded-3 cursor-pointer mb-1 transition-all"
                                                             :class="selected.includes('{{ $c->id }}') ? 'bg-dark text-white shadow-sm transform-scale' : 'bg-white text-dark hover-bg-light'">
                                                            
                                                            <div class="d-flex align-items-center gap-3">
                                                                <div class="rounded-circle p-1 d-flex align-items-center justify-content-center" 
                                                                     :class="selected.includes('{{ $c->id }}') ? 'bg-white bg-opacity-25' : 'bg-light'">
                                                                    <i class="fas fa-store fa-sm"></i>
                                                                </div>
                                                                <span class="fw-bold small">{{ $c->nama_cabang }}</span>
                                                            </div>
                                                            
                                                            <div x-show="selected.includes('{{ $c->id }}')">
                                                                <i class="fas fa-check-circle text-success"></i>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Badges --}}
                                        <div class="d-flex flex-wrap gap-2 mt-3" x-show="selected.length > 0" x-transition>
                                            <template x-for="id in selected" :key="id">
                                                <div class="badge bg-white text-dark border shadow-sm rounded-pill px-3 py-2 fw-bold extra-small d-flex align-items-center gap-2 animate__animated animate__fadeIn">
                                                    <span class="bg-success rounded-circle" style="width: 6px; height: 6px;"></span>
                                                    <span x-text="document.querySelector(`[@click*='${id}'] span.fw-bold`)?.innerText || 'Branch ' + id"></span>
                                                    <i class="fas fa-times ms-1 cursor-pointer text-muted hover-text-danger" @click.stop="toggle(id)"></i>
                                                </div>
                                            </template>
                                        </div>

                                    </div>
                                    @error('selected_branches') <span class="text-danger extra-small fw-bold ms-2 mt-2 d-block">{{ $message }}</span> @enderror
                                </div>
                            @endif

                            @if($role === 'distributor')
                                <div class="col-12 animate__animated animate__fadeIn">
                                    <div class="form-floating">
                                        <select class="form-select bg-primary-subtle border-0 text-dark fw-bold rounded-4" wire:model="distributor_id">
                                            <option value="">Select Partner</option>
                                            @foreach($distributors as $dist)
                                                <option value="{{ $dist->id }}">{{ $dist->nama_distributor }}</option>
                                            @endforeach
                                        </select>
                                        <label class="text-primary fw-bold">Partner Affiliation</label>
                                    </div>
                                    @error('distributor_id') <span class="text-danger extra-small fw-bold ms-2">{{ $message }}</span> @enderror
                                </div>
                            @endif

                            {{-- LOGIN CREDENTIALS --}}
                            <div class="col-12 mt-4">
                                <h6 class="text-uppercase text-muted extra-small fw-bold tracking-widest mb-3">Login Credentials</h6>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control bg-light border-0 fw-bold text-dark rounded-4" id="idInput" placeholder="Username" wire:model="idlogin">
                                    <label for="idInput" class="text-secondary fw-semibold">Login ID (Username)</label>
                                </div>
                                @error('idlogin') <span class="text-danger extra-small fw-bold ms-2">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="password" class="form-control bg-light border-0 fw-bold text-dark rounded-4" id="passInput" placeholder="******" wire:model="password">
                                    <label for="passInput" class="text-secondary fw-semibold">Password {{ $isEdit ? '(Leave blank to keep)' : '' }}</label>
                                </div>
                                @error('password') <span class="text-danger extra-small fw-bold ms-2">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="d-grid gap-3 mt-5 pt-3 border-top">
                            <button type="submit" class="btn btn-dark py-3 rounded-4 fw-black shadow-lg hover-scale transition-all text-uppercase tracking-wide">
                                <i class="fas fa-check-circle me-2"></i> {{ $isEdit ? 'Save Changes' : 'Create Account' }}
                            </button>
                            <button type="button" wire:click="resetInputFields" data-bs-dismiss="modal" class="btn btn-white text-muted fw-bold small text-uppercase hover-opacity">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endteleport

    <style>
        /* Styles Tetap Sama */
        @media (max-width: 991px) {
            .mobile-spacer { padding-top: 80px !important; }
        }
        .fw-black { font-weight: 900; }
        .tracking-tight { letter-spacing: -0.025em; }
        .tracking-wide { letter-spacing: 0.025em; }
        .tracking-widest { letter-spacing: 0.1em; }
        .extra-small { font-size: 0.65rem; }
        .avatar-circle-xl { width: 50px; height: 50px; border-radius: 50%; font-size: 1.25rem; border: 3px solid #fff; }
        .btn-icon { width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border: 1px solid #f0f0f0; }
        .hover-lift:hover { transform: translateY(-2px); }
        .hover-primary:hover { background-color: #0d6efd; color: white; border-color: #0d6efd; }
        .hover-danger:hover { background-color: #dc3545; color: white; border-color: #dc3545; }
        .hover-opacity:hover { opacity: 0.7; }
        .hover-scale:hover { transform: scale(1.02); }
        .hover-text-danger:hover { color: #dc3545 !important; }
        .shadow-xl { box-shadow: 0 20px 40px -10px rgba(0,0,0,0.05); }
        .shadow-2xl { box-shadow: 0 30px 60px -12px rgba(0,0,0,0.15); }
        .shadow-danger { box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3); }
        .form-control:focus, .form-select:focus { box-shadow: none; background-color: #fff !important; ring: 2px solid #000; }
        .form-floating > label { font-size: 0.85rem; font-weight: 600; }
        .rotate-180 { transform: rotate(180deg); }
        .hover-bg-light:hover { background-color: #f8f9fa; }
        .transform-scale { transform: scale(0.98); }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #ccc; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #999; }
        .transition-all { transition: all 0.3s ease; }
        .cursor-pointer { cursor: pointer; }
        @media (max-width: 768px) {
            .display-6 { font-size: 1.75rem; }
            .modal-body { padding: 1.5rem !important; }
        }
    </style>
</div>

@script
<script>
    Livewire.on('close-modal', () => {
        const modal = bootstrap.Modal.getInstance(document.getElementById('userModal'));
        if(modal) modal.hide();
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
    });
    
    // Handler SweetAlert2
    Livewire.on('swal', (data) => {
        const payload = data[0]; 
        Swal.fire({
            title: payload.title,
            text: payload.text,
            icon: payload.icon,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    });
</script>
@endscript