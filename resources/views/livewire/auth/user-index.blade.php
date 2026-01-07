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
                            <th class="py-4 border-0 extra-small fw-900 text-dark text-uppercase tracking-2 text-center">Status</th> {{-- KOLOM STATUS --}}
                            <th class="text-end pe-5 py-4 border-0 extra-small fw-900 text-dark text-uppercase tracking-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="border-0">
                        @forelse($users as $user)
                        <tr class="table-row-premium transition-all">
                            <td class="ps-5">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle-lg bg-dark text-white fw-900 shadow-sm me-3">
                                        {{ strtoupper(substr($user->nama_lengkap, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-900 text-dark mb-0 fs-6">{{ $user->nama_lengkap }}</div>
                                        <div class="extra-small text-muted">{{ $user->email }} | ID: {{ $user->idlogin }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-dark rounded-pill px-3 py-2 extra-small fw-900 text-uppercase tracking-widest">
                                    {{ str_replace('_', ' ', $user->role) }}
                                </span>
                            </td>
                            <td>
                                @if($user->role === 'superadmin')
                                    <span class="badge bg-primary text-white">ALL ACCESS</span>
                                @elseif($user->role === 'audit')
                                    <div class="d-flex flex-wrap gap-1">
                                        @forelse($user->branches as $b)
                                            <span class="badge bg-white text-dark border">{{ $b->nama_cabang }}</span>
                                        @empty
                                            <span class="text-danger small fw-bold">Non-Aktif</span>
                                        @endforelse
                                    </div>
                                @else
                                    <span class="fw-bold text-dark">{{ $user->cabang->nama_cabang ?? '-' }}</span>
                                @endif
                            </td>
                            
                            {{-- KOLOM STATUS (SWITCH) --}}
                            <td class="text-center">
                                @if($user->id !== auth()->id() && $user->role !== 'superadmin')
                                    <div class="form-check form-switch d-flex justify-content-center">
                                        <input class="form-check-input" type="checkbox" wire:click="toggleStatus({{ $user->id }})" {{ $user->is_active ? 'checked' : '' }} style="cursor: pointer;">
                                    </div>
                                    <span class="extra-small fw-bold {{ $user->is_active ? 'text-success' : 'text-danger' }}">
                                        {{ $user->is_active ? 'AKTIF' : 'NON-AKTIF' }}
                                    </span>
                                @else
                                    <span class="badge bg-light text-secondary border">PROTECTED</span>
                                @endif
                            </td>

                            <td class="text-end pe-5">
                                <button wire:click="edit({{ $user->id }})" data-bs-toggle="modal" data-bs-target="#userModal" class="btn btn-sm btn-light rounded-circle shadow-sm me-2"><i class="fas fa-edit text-primary"></i></button>
                                @if($user->id !== auth()->id())
                                <button wire:confirm="Hapus user ini?" wire:click="delete({{ $user->id }})" class="btn btn-sm btn-light rounded-circle shadow-sm"><i class="fas fa-trash text-danger"></i></button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center py-5 text-muted fw-bold">Tidak ada data pengguna.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4">{{ $users->links() }}</div>
        </div>
    </div>

    {{-- MODAL FORM --}}
    @teleport('body')
    <div wire:ignore.self class="modal fade" id="userModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-5 border-0 shadow-extra-lg">
                <div class="modal-header border-0 px-5 pt-5 pb-0">
                    <div>
                        <h3 class="fw-900 text-dark mb-0">{{ $isEdit ? 'Edit User' : 'Buat User Baru' }}</h3>
                        <p class="text-secondary small fw-bold text-uppercase">Kelola Akses & Otoritas</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" wire:click="resetInputFields"></button>
                </div>
                <div class="modal-body p-5">
                    <form wire:submit.prevent="store">
                        <div class="row g-4">
                            
                            {{-- SWITCH STATUS AKTIF DI MODAL --}}
                            <div class="col-12 d-flex justify-content-end">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" wire:model="is_active" id="statusSwitch">
                                    <label class="form-check-label fw-bold text-dark" for="statusSwitch">Akun Aktif?</label>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="small fw-900 text-dark mb-2">Pilih Role</label>
                                <select wire:model.live="role" class="form-select border-0 bg-light-subtle py-3 px-4 rounded-4 shadow-none fw-600">
                                    <option value="">-- Pilih Role --</option>
                                    @if(Auth::user()->role === 'superadmin')
                                        <option value="superadmin">SUPERADMIN</option>
                                        <option value="audit">AUDIT (MULTI CABANG)</option>
                                    @endif
                                    <option value="adminproduk">ADMIN PRODUK</option>
                                    <option value="analis">ANALIS</option>
                                    <option value="distributor">DISTRIBUTOR</option>
                                    <option value="leader">LEADER</option>
                                    <option value="sales">SALES / KASIR</option>
                                    <option value="gudang">GUDANG</option>
                                </select>
                                @error('role') <small class="text-danger fw-bold">{{ $message }}</small> @enderror
                            </div>

                            @if($role === 'audit')
                            <div class="col-12 animate__animated animate__fadeIn">
                                <label class="small fw-900 text-dark mb-2">Pilih Cabang (Akses Multi)</label>
                                <div class="p-3 bg-light-subtle rounded-4 border">
                                    <div class="row g-2" style="max-height: 150px; overflow-y: auto;">
                                        @foreach($cabangs as $c)
                                        <div class="col-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="{{ $c->id }}" wire:model="selected_branches" id="cb_{{ $c->id }}">
                                                <label class="form-check-label small fw-bold" for="cb_{{ $c->id }}">{{ $c->nama_cabang }}</label>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @error('selected_branches') <small class="text-danger fw-bold">{{ $message }}</small> @enderror
                            </div>
                            @endif

                            @if($role && !in_array($role, ['superadmin', 'audit', 'distributor']))
                            <div class="col-12 animate__animated animate__fadeIn">
                                <label class="small fw-900 text-dark mb-2">Penempatan Cabang</label>
                                <select wire:model="cabang_id" class="form-select border-0 bg-light-subtle py-3 px-4 rounded-4 shadow-none fw-600">
                                    <option value="">-- Pilih Cabang --</option>
                                    @foreach($cabangs as $c)
                                        <option value="{{ $c->id }}">{{ $c->nama_cabang }}</option>
                                    @endforeach
                                </select>
                                @error('cabang_id') <small class="text-danger fw-bold">{{ $message }}</small> @enderror
                            </div>
                            @endif

                            <div class="col-md-6">
                                <label class="small fw-900 text-dark mb-2">Nama Lengkap</label>
                                <input type="text" wire:model="nama_lengkap" class="form-control border-0 bg-light-subtle py-3 px-4 rounded-4 shadow-none">
                                @error('nama_lengkap') <small class="text-danger fw-bold">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="small fw-900 text-dark mb-2">ID Login</label>
                                <input type="text" wire:model="idlogin" class="form-control border-0 bg-light-subtle py-3 px-4 rounded-4 shadow-none">
                                @error('idlogin') <small class="text-danger fw-bold">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-12">
                                <label class="small fw-900 text-dark mb-2">Email</label>
                                <input type="email" wire:model="email" class="form-control border-0 bg-light-subtle py-3 px-4 rounded-4 shadow-none">
                                @error('email') <small class="text-danger fw-bold">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-12">
                                <label class="small fw-900 text-dark mb-2">Password {{ $isEdit ? '(Opsional)' : '' }}</label>
                                <input type="password" wire:model="password" class="form-control border-0 bg-light-subtle py-3 px-4 rounded-4 shadow-none" placeholder="••••••">
                                @error('password') <small class="text-danger fw-bold">{{ $message }}</small> @enderror
                            </div>
                        </div>

                        <div class="d-grid gap-2 mt-5">
                            <button type="submit" class="btn btn-dark py-3 rounded-4 fw-900 shadow-lg">SIMPAN DATA</button>
                            <button type="button" wire:click="resetInputFields" data-bs-dismiss="modal" class="btn btn-link text-muted fw-bold text-decoration-none">Batal</button>
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