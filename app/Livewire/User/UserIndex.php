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
        $this->role = '';
        $this->distributor_id = '';
        $this->cabang_id = '';
        $this->is_active = true; // Default Aktif
        $this->selected_branches = [];
        $this->userId = null;
        $this->isEdit = false;
        $this->resetErrorBag();
    }

    // === CREATE / UPDATE ===
    public function store()
    {
        // Validasi
        $rules = [
            'nama_lengkap' => 'required',
            'idlogin'      => ['required', Rule::unique('users')->ignore($this->userId)],
            'email'        => ['required', 'email', Rule::unique('users')->ignore($this->userId)],
            'role'         => 'required',
            'distributor_id' => 'required_if:role,distributor',
            'cabang_id'      => 'required_if:role,adminproduk,analis,leader,sales,gudang,security',
            'selected_branches' => 'required_if:role,audit|array|min:1',
        ];

        if (!$this->userId) {
            $rules['password'] = 'required|min:6';
        }

        $this->validate($rules);

        // Proteksi Backend: Jika Audit mencoba create Superadmin atau role aneh -> Block
        if (Auth::user()->role === 'audit' && $this->role === 'superadmin') {
            abort(403, 'Anda tidak memiliki akses membuat Superadmin.');
        }

        $data = [
            'nama_lengkap' => $this->nama_lengkap,
            'idlogin'      => $this->idlogin,
            'email'        => $this->email,
            'role'         => $this->role,
            'is_active'    => $this->is_active, // Simpan status aktif/nonaktif
            'distributor_id' => ($this->role === 'distributor') ? $this->distributor_id : null,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        // Logic Cabang ID (Single)
        if ($this->role !== 'superadmin' && $this->role !== 'audit' && $this->role !== 'distributor') {
            $data['cabang_id'] = $this->cabang_id;
        } else {
            $data['cabang_id'] = null;
        }

        $user = User::updateOrCreate(['id' => $this->userId], $data);

        // Sync Multi Cabang untuk Audit
        if ($this->role === 'audit') {
            $user->branches()->sync($this->selected_branches);
        } else {
            $user->branches()->detach();
        }

        $this->dispatch('close-modal');
        session()->flash('info', 'Data user berhasil disimpan.');
    }

    // === EDIT ===
    public function edit($id)
    {
        $user = User::findOrFail($id);
        
        // Proteksi: Audit tidak boleh edit Superadmin
        if (Auth::user()->role === 'audit' && $user->role === 'superadmin') {
            $this->dispatch('swal', ['title'=>'Akses Ditolak', 'text'=>'Anda tidak bisa mengedit Superadmin.', 'icon'=>'error']);
            return;
        }

        // Proteksi: Audit hanya boleh edit user di cabangnya
        if (Auth::user()->role === 'audit') {
            $myBranchIds = Auth::user()->access_cabang_ids;
            
            // Cek apakah user target ada di salah satu cabang Audit
            // Jika user target punya cabang_id (Staff):
            if ($user->cabang_id && !in_array($user->cabang_id, $myBranchIds)) {
                $this->dispatch('swal', ['title'=>'Akses Ditolak', 'text'=>'User ini beda cabang dengan Anda.', 'icon'=>'error']);
                return;
            }
            // Jika user target adalah Audit lain (Multi Cabang), cek irisannya
            if ($user->role === 'audit') {
                $targetBranches = $user->branches->pluck('id')->toArray();
                $intersect = array_intersect($myBranchIds, $targetBranches);
                if (empty($intersect)) {
                    $this->dispatch('swal', ['title'=>'Akses Ditolak', 'text'=>'Audit ini tidak satu wilayah dengan Anda.', 'icon'=>'error']);
                    return;
                }
            }
        }

        $this->userId = $user->id;
        $this->nama_lengkap = $user->nama_lengkap;
        $this->idlogin = $user->idlogin;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->distributor_id = $user->distributor_id;
        $this->cabang_id = $user->cabang_id;
        $this->is_active = $user->is_active; // Load status aktif
        
        $this->selected_branches = $user->branches->pluck('id')->toArray();

        $this->isEdit = true;
    }

    // === DELETE ===
    public function delete($id)
    {
        if ($id === auth()->id()) return;
        
        $user = User::findOrFail($id);
        
        // Proteksi Delete Audit
        if (Auth::user()->role === 'audit') {
            if ($user->role === 'superadmin') return;
            
            // Cek akses cabang lagi biar aman
            $myBranchIds = Auth::user()->access_cabang_ids;
            if ($user->cabang_id && !in_array($user->cabang_id, $myBranchIds)) return;
        }

        $user->delete();
        session()->flash('info', 'Pengguna berhasil dihapus.');
    }
    
    // === TOGGLE STATUS (Shortcuy di Tabel) ===
    public function toggleStatus($id)
    {
        if ($id === auth()->id()) return; // Gak bisa nonaktifkan diri sendiri

        $user = User::findOrFail($id);
        
        // Proteksi Audit
        if (Auth::user()->role === 'audit') {
            if ($user->role === 'superadmin') return;
            $myBranchIds = Auth::user()->access_cabang_ids;
            if ($user->cabang_id && !in_array($user->cabang_id, $myBranchIds)) return;
        }

        $user->is_active = !$user->is_active;
        $user->save();
        
        // Paksa logout jika di-nonaktifkan (Opsional, pake cache clearing)
        if (!$user->is_active) {
             // Logic logout paksa bisa ditambah disini (misal hapus session driver database)
        }
    }
}