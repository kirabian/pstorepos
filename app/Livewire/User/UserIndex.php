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
    public $nama_lengkap, $idlogin, $email, $password, $role, $distributor_id, $cabang_id, $is_active = true;
    
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

        // 2. LOGIKA PROTEKSI AUDIT (HANYA LIHAT USER DI CABANGNYA)
        if ($currentUser->role === 'audit') {
            $myBranchIds = $currentUser->access_cabang_ids;
            
            $query->where(function($q) use ($myBranchIds) {
                // User reguler di cabang yang dipegang audit
                $q->whereIn('cabang_id', $myBranchIds)
                  // ATAU sesama audit yang juga pegang cabang tersebut
                  ->orWhereHas('branches', function($sq) use ($myBranchIds) {
                      $sq->whereIn('cabangs.id', $myBranchIds);
                  });
            });
            
            // Audit TIDAK BOLEH lihat/edit Superadmin
            $query->where('role', '!=', 'superadmin');
        }

        $users = $query->latest()->paginate(10);

        // Data Dropdown Modal
        if ($currentUser->role === 'superadmin') {
            $cabangs = Cabang::orderBy('nama_cabang', 'asc')->get();
        } else {
            // Audit hanya bisa pilih cabang miliknya saat create/edit user
            $cabangs = Cabang::whereIn('id', $currentUser->access_cabang_ids)->orderBy('nama_cabang', 'asc')->get();
        }

        $distributors = Distributor::orderBy('nama_distributor', 'asc')->get();

        return view('livewire.auth.user-index', [
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
        $this->role = '';
        $this->distributor_id = '';
        $this->cabang_id = '';
        $this->is_active = true; // Default Aktif
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
            'role'         => 'required',
            // Validasi Unique (abaikan ID sendiri saat edit)
            'idlogin'      => ['required', Rule::unique('users')->ignore($this->userId)],
            'email'        => ['required', 'email', Rule::unique('users')->ignore($this->userId)],
        ];

        // 2. Rules Tambahan Sesuai Role
        if ($this->role === 'distributor') {
            $rules['distributor_id'] = 'required';
        }
        
        if (in_array($this->role, ['adminproduk', 'analis', 'leader', 'sales', 'gudang', 'security'])) {
            $rules['cabang_id'] = 'required';
        }

        if ($this->role === 'audit') {
            $rules['selected_branches'] = 'required|array|min:1';
        }

        // 3. Password Wajib saat Create, Opsional saat Edit
        if (!$this->userId) {
            $rules['password'] = 'required|min:6';
        } else {
            $rules['password'] = 'nullable|min:6';
        }

        $this->validate($rules);

        // 4. Proteksi Backend: Audit gaboleh bikin Superadmin
        if (Auth::user()->role === 'audit' && $this->role === 'superadmin') {
            abort(403, 'Anda tidak memiliki akses membuat Superadmin.');
        }

        // 5. Siapkan Data
        $data = [
            'nama_lengkap' => $this->nama_lengkap,
            'idlogin'      => $this->idlogin,
            'email'        => $this->email,
            'role'         => $this->role,
            'is_active'    => $this->is_active,
            'distributor_id' => ($this->role === 'distributor') ? $this->distributor_id : null,
        ];

        // Jika password diisi, update. Jika kosong saat edit, biarkan lama.
        if (!empty($this->password)) {
            $data['password'] = Hash::make($this->password);
        }

        // Logic Cabang ID (Single)
        if (!in_array($this->role, ['superadmin', 'audit', 'distributor'])) {
            $data['cabang_id'] = $this->cabang_id;
        } else {
            $data['cabang_id'] = null;
        }

        // 6. Eksekusi Simpan ke Database
        $user = User::updateOrCreate(['id' => $this->userId], $data);

        // 7. Sync Multi Cabang untuk Audit
        if ($this->role === 'audit') {
            $user->branches()->sync($this->selected_branches);
        } else {
            // Hapus relasi jika role berubah jadi bukan audit
            $user->branches()->detach();
        }

        // 8. Tutup Modal & Refresh
        $this->dispatch('close-modal');
        session()->flash('info', 'Data user berhasil disimpan.');
        $this->resetInputFields();
    }

    // === EDIT (LOAD DATA KE FORM) ===
    public function edit($id)
    {
        $user = User::findOrFail($id);
        
        // Proteksi Audit
        if (Auth::user()->role === 'audit' && $user->role === 'superadmin') {
            $this->dispatch('swal', ['title'=>'Akses Ditolak', 'text'=>'Anda tidak bisa mengedit Superadmin.', 'icon'=>'error']);
            return;
        }

        $this->userId = $user->id;
        $this->nama_lengkap = $user->nama_lengkap;
        $this->idlogin = $user->idlogin;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->distributor_id = $user->distributor_id;
        $this->cabang_id = $user->cabang_id;
        $this->is_active = (bool) $user->is_active; // Pastikan boolean
        
        // Load cabang yang dicentang (untuk Audit)
        $this->selected_branches = $user->branches->pluck('id')->toArray();

        $this->isEdit = true;
        $this->resetErrorBag();
    }

    // === DELETE ===
    public function delete($id)
    {
        if ($id === auth()->id()) return;
        
        $user = User::findOrFail($id);
        
        if (Auth::user()->role === 'audit') {
            if ($user->role === 'superadmin') return;
            $myBranchIds = Auth::user()->access_cabang_ids;
            if ($user->cabang_id && !in_array($user->cabang_id, $myBranchIds)) return;
        }

        $user->delete();
        session()->flash('info', 'Pengguna berhasil dihapus.');
    }
    
    // === TOGGLE STATUS ===
    public function toggleStatus($id)
    {
        if ($id === auth()->id()) return; 

        $user = User::findOrFail($id);
        
        if (Auth::user()->role === 'audit' && $user->role === 'superadmin') return;

        $user->is_active = !$user->is_active;
        $user->save();
        
        if (!$user->is_active) {
             // Logic logout paksa bisa ditambah disini
        }
    }
}