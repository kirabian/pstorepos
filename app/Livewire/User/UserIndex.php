<?php

namespace App\Livewire\User;

use App\Models\User;
use App\Models\Cabang;
use App\Models\Distributor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;

#[Layout('layouts.master')]
class UserIndex extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $userId;
    public $isEdit = false;

    // Form Fields
    public $nama_lengkap, $idlogin, $email, $password, $tanggal_lahir, $role, $distributor_id, $cabang_id, $is_active = true;
    
    // Khusus Audit: Multi Cabang Selection
    public $selected_branches = []; 

    protected $updatesQueryString = ['search'];

    #[On('echo:pstore-channel,inventory.updated')]
    public function refreshTable() { }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    // === READ (RENDER TABLE) ===
    public function render()
    {
        $currentUser = Auth::user();
        
        $query = User::with(['distributor', 'cabang', 'branches']);

        // 1. Filter Search
        if ($this->search) {
            $query->where(function($q) {
                $q->where('nama_lengkap', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('role', 'like', '%' . $this->search . '%')
                  ->orWhere('idlogin', 'like', '%' . $this->search . '%');
            });
        }

        // 2. LOGIKA PROTEKSI DATA AUDIT (Hanya lihat user di cabangnya)
        if ($currentUser->role === 'audit') {
            // Ambil array ID cabang yang dipegang audit
            $myBranchIds = $currentUser->access_cabang_ids ?? []; 
            
            $query->where(function($q) use ($myBranchIds) {
                // User biasa yang ada di cabang audit
                $q->whereIn('cabang_id', $myBranchIds)
                  // ATAU User audit lain yang memegang cabang yang sama (opsional, tergantung rule)
                  ->orWhereHas('branches', function($sq) use ($myBranchIds) {
                      $sq->whereIn('cabangs.id', $myBranchIds);
                  });
            });
            
            // Audit tidak boleh lihat Superadmin
            $query->where('role', '!=', 'superadmin');
        }

        $users = $query->latest()->paginate(10);

        // 3. LOGIKA DROPDOWN CABANG
        if ($currentUser->role === 'superadmin') {
            // Superadmin lihat semua cabang
            $cabangs = Cabang::orderBy('nama_cabang', 'asc')->get();
        } else {
            // Audit hanya lihat cabang miliknya di Dropdown
            $cabangs = Cabang::whereIn('id', $currentUser->access_cabang_ids ?? [])
                        ->orderBy('nama_cabang', 'asc')
                        ->get();
        }

        $distributors = Distributor::orderBy('nama_distributor', 'asc')->get();

        return view('livewire.user.user-index', [
            'users' => $users,
            'cabangs' => $cabangs,
            'distributors' => $distributors
        ]);
    }

    // === RESET FORM ===
    public function resetInputFields()
    {
        $this->nama_lengkap = '';
        $this->idlogin = '';
        $this->email = '';
        $this->password = '';
        $this->tanggal_lahir = '';
        $this->role = '';
        $this->distributor_id = '';
        $this->cabang_id = '';
        $this->is_active = true; 
        $this->selected_branches = [];
        $this->userId = null;
        $this->isEdit = false;
        $this->resetErrorBag();
    }

    // === CREATE / UPDATE (STORE) ===
    public function store()
    {
        // 1. Definisikan Rules Dasar
        $rules = [
            'nama_lengkap' => 'required',
            'tanggal_lahir' => 'required|date',
            'role'         => 'required',
            'idlogin'      => ['required', Rule::unique('users')->ignore($this->userId)],
            'email'        => ['required', 'email', Rule::unique('users')->ignore($this->userId)],
        ];

        // 2. Rules Tambahan Sesuai Role Inputan
        if ($this->role === 'distributor') {
            $rules['distributor_id'] = 'required';
        }
        
        // Wajib pilih cabang untuk role operasional
        if (in_array($this->role, ['adminproduk', 'analis', 'leader', 'sales', 'gudang', 'security'])) {
            $rules['cabang_id'] = 'required';
        }

        // Jika user yang dibuat adalah audit, wajib pilih area coverage
        if ($this->role === 'audit') {
            $rules['selected_branches'] = 'required|array|min:1';
        }

        // Password Wajib saat Create
        if (!$this->userId) {
            $rules['password'] = 'required|min:6';
        } else {
            $rules['password'] = 'nullable|min:6';
        }

        $this->validate($rules);

        $currentUser = Auth::user();

        // 3. SECURITY CHECK: Audit Management Rule
        if ($currentUser->role === 'audit') {
            // A. Audit tidak boleh bikin superadmin
            if ($this->role === 'superadmin') {
                abort(403, 'Anda tidak memiliki akses membuat Superadmin.');
            }

            // B. Jika Audit membuat user operasional, Cabang ID harus milik dia
            if ($this->cabang_id) {
                if (!in_array($this->cabang_id, $currentUser->access_cabang_ids ?? [])) {
                    $this->addError('cabang_id', 'Anda tidak berhak menambahkan user di cabang ini.');
                    return;
                }
            }

            // C. Jika Audit membuat Audit lain, cabangnya harus irisan (opsional)
            // Disini diasumsikan Audit mengelola staff, bukan sesama Audit.
        }

        // 4. Siapkan Data
        $data = [
            'nama_lengkap' => $this->nama_lengkap,
            'idlogin'      => $this->idlogin,
            'email'        => $this->email,
            'tanggal_lahir'=> $this->tanggal_lahir,
            'role'         => $this->role,
            'is_active'    => $this->is_active,
            'distributor_id' => ($this->role === 'distributor') ? $this->distributor_id : null,
        ];

        if (!empty($this->password)) {
            $data['password'] = Hash::make($this->password);
        }

        // Set Cabang ID (Kecuali role global)
        if (!in_array($this->role, ['superadmin', 'audit', 'distributor'])) {
            $data['cabang_id'] = $this->cabang_id;
        } else {
            $data['cabang_id'] = null;
        }

        // 5. Eksekusi Simpan
        $user = User::updateOrCreate(['id' => $this->userId], $data);

        // 6. Sync Multi Cabang (Jika user yang dibuat adalah Audit)
        if ($this->role === 'audit') {
            $user->branches()->sync($this->selected_branches);
        } else {
            $user->branches()->detach();
        }

        // 7. Selesai
        $this->dispatch('close-modal');
        $this->dispatch('swal', [
            'title' => 'Berhasil!',
            'text' => 'Data user telah disimpan.',
            'icon' => 'success'
        ]);
        
        $this->resetInputFields();
    }

    // === EDIT ===
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $currentUser = Auth::user();

        // PROTEKSI AUDIT SAAT EDIT
        if ($currentUser->role === 'audit') {
            // 1. Tidak boleh edit superadmin
            if ($user->role === 'superadmin') {
                $this->dispatch('swal', ['title'=>'Akses Ditolak', 'text'=>'Anda tidak bisa mengedit Superadmin.', 'icon'=>'error']);
                return;
            }

            // 2. Tidak boleh edit user di luar cabang aksesnya
            // Cek jika user punya cabang_id (Staff biasa)
            if ($user->cabang_id && !in_array($user->cabang_id, $currentUser->access_cabang_ids ?? [])) {
                $this->dispatch('swal', ['title'=>'Akses Ditolak', 'text'=>'User ini diluar wilayah otorisasi Anda.', 'icon'=>'error']);
                return;
            }
        }

        $this->userId = $user->id;
        $this->nama_lengkap = $user->nama_lengkap;
        $this->idlogin = $user->idlogin;
        $this->email = $user->email;
        $this->tanggal_lahir = $user->tanggal_lahir;
        $this->role = $user->role;
        $this->distributor_id = $user->distributor_id;
        $this->cabang_id = $user->cabang_id;
        $this->is_active = (bool) $user->is_active;
        
        $this->selected_branches = $user->branches->pluck('id')->toArray();

        $this->isEdit = true;
        $this->resetErrorBag();
    }

    // === DELETE ===
    public function delete($id)
    {
        if ($id === auth()->id()) return;
        
        $user = User::findOrFail($id);
        $currentUser = Auth::user();
        
        // PROTEKSI AUDIT SAAT DELETE
        if ($currentUser->role === 'audit') {
            if ($user->role === 'superadmin') return;
            
            // Cek otorisasi cabang
            if ($user->cabang_id && !in_array($user->cabang_id, $currentUser->access_cabang_ids ?? [])) {
                $this->dispatch('swal', ['title'=>'Gagal', 'text'=>'User diluar akses cabang Anda.', 'icon'=>'error']);
                return;
            }
        }

        $user->delete();
        $this->dispatch('swal', ['title'=>'Terhapus!', 'text'=>'User berhasil dihapus.', 'icon'=>'success']);
    }
    
    // === TOGGLE STATUS ===
    public function toggleStatus($id)
    {
        if ($id === auth()->id()) return; 

        $user = User::findOrFail($id);
        $currentUser = Auth::user();
        
        // PROTEKSI AUDIT SAAT TOGGLE
        if ($currentUser->role === 'audit') {
            if ($user->role === 'superadmin') return;

            // Cek otorisasi cabang
            if ($user->cabang_id && !in_array($user->cabang_id, $currentUser->access_cabang_ids ?? [])) {
                $this->dispatch('swal', ['title'=>'Gagal', 'text'=>'User diluar akses cabang Anda.', 'icon'=>'error']);
                return;
            }
        }

        $user->is_active = !$user->is_active;
        $user->save();
    }
}