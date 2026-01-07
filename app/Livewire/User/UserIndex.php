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
    public $nama_lengkap, $idlogin, $email, $password, $role, $distributor_id, $cabang_id;
    
    // Khusus Audit: Multi Cabang Selection
    public $selected_branches = []; 

    protected $updatesQueryString = ['search'];

    #[On('echo:pstore-channel,inventory.updated')]
    public function refreshTable() { }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $currentUser = Auth::user();
        
        // Eager Load relasi yang dibutuhkan
        $query = User::with(['distributor', 'cabang', 'accessibleBranches']);

        // 1. Filter Search
        if ($this->search) {
            $query->where(function($q) {
                $q->where('nama_lengkap', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('role', 'like', '%' . $this->search . '%')
                  ->orWhere('idlogin', 'like', '%' . $this->search . '%');
            });
        }

        // 2. Filter Berdasarkan Role Login (Proteksi Data)
        if ($currentUser->role === 'audit') {
            // Audit cuma boleh lihat user di cabang yang dia pegang
            $myBranchIds = $currentUser->access_cabang_ids;
            
            $query->where(function($q) use ($myBranchIds) {
                // User reguler di cabang tsb
                $q->whereIn('cabang_id', $myBranchIds)
                  // ATAU sesama audit yang pegang cabang tsb
                  ->orWhereHas('accessibleBranches', function($sq) use ($myBranchIds) {
                      $sq->whereIn('cabangs.id', $myBranchIds);
                  });
            });
            
            // Audit TIDAK BOLEH lihat Superadmin
            $query->where('role', '!=', 'superadmin');
        }

        $users = $query->latest()->paginate(10);

        // Data untuk Dropdown di Modal
        if ($currentUser->role === 'superadmin') {
            $cabangs = Cabang::orderBy('nama_cabang', 'asc')->get();
        } else {
            // Audit cuma bisa pilih cabang miliknya
            $cabangs = Cabang::whereIn('id', $currentUser->access_cabang_ids)->orderBy('nama_cabang', 'asc')->get();
        }

        $distributors = Distributor::orderBy('nama_distributor', 'asc')->get();

        return view('livewire.user.user-index', [
            'users' => $users,
            'cabangs' => $cabangs,
            'distributors' => $distributors
        ]);
    }

    // --- CREATE / UPDATE LOGIC ---

    public function resetInputFields()
    {
        $this->nama_lengkap = '';
        $this->idlogin = '';
        $this->email = '';
        $this->password = '';
        $this->role = '';
        $this->distributor_id = '';
        $this->cabang_id = '';
        $this->selected_branches = [];
        $this->userId = null;
        $this->isEdit = false;
        $this->resetErrorBag();
    }

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

        // Data User
        $data = [
            'nama_lengkap' => $this->nama_lengkap,
            'idlogin'      => $this->idlogin,
            'email'        => $this->email,
            'role'         => $this->role,
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
            $user->accessibleBranches()->sync($this->selected_branches);
        } else {
            // Hapus relasi jika role berubah bukan audit
            $user->accessibleBranches()->detach();
        }

        $this->dispatch('close-modal');
        session()->flash('info', 'Data user berhasil disimpan.');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        
        // Proteksi Audit edit Superadmin
        if (Auth::user()->role === 'audit' && $user->role === 'superadmin') abort(403);

        $this->userId = $user->id;
        $this->nama_lengkap = $user->nama_lengkap;
        $this->idlogin = $user->idlogin;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->distributor_id = $user->distributor_id;
        $this->cabang_id = $user->cabang_id;
        
        // Load Selected Branches
        $this->selected_branches = $user->accessibleBranches->pluck('id')->toArray();

        $this->isEdit = true;
    }

    public function delete($id)
    {
        if ($id === auth()->id()) return;
        
        $user = User::findOrFail($id);
        if (Auth::user()->role === 'audit' && $user->role === 'superadmin') return;

        $user->delete();
        session()->flash('info', 'Pengguna berhasil dihapus.');
    }
}