<?php

namespace App\Livewire\User;

use App\Models\User;
use App\Models\Cabang;
use App\Models\Distributor;
use App\Models\Gudang;
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
    public $nama_lengkap, $idlogin, $email, $password, $tanggal_lahir, $role, $is_active = true;
    
    // ID Locations
    public $distributor_id, $cabang_id, $gudang_id; 

    // Logic Penempatan Kerja untuk Inventory Staff
    public $placement_type = ''; // 'distributor' atau 'gudang'

    // Khusus Audit
    public $selected_branches = []; 

    protected $updatesQueryString = ['search'];

    #[On('echo:pstore-channel,inventory.updated')]
    public function refreshTable() { }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    // Reset Logic saat Role Berubah
    public function updatedRole($value)
    {
        // Jika bukan Inventory Staff, reset placement
        if ($value !== 'inventory_staff') {
            $this->placement_type = '';
            $this->gudang_id = null;
            $this->distributor_id = null;
        }
        
        // Reset spesifik ID jika role bukan terkait
        if ($value === 'distributor') {
            $this->cabang_id = null;
            $this->gudang_id = null;
        } elseif (in_array($value, ['adminproduk', 'analis', 'leader', 'sales', 'security'])) {
            $this->distributor_id = null;
            $this->gudang_id = null;
        }
    }

    public function updatedPlacementType()
    {
        // Reset dropdown saat radio button berubah agar data bersih
        $this->distributor_id = null;
        $this->gudang_id = null;
        $this->cabang_id = null;
    }

    public function render()
    {
        $currentUser = Auth::user();
        
        $query = User::with(['distributor', 'cabang', 'gudang', 'branches']);

        if ($this->search) {
            $query->where(function($q) {
                $q->where('nama_lengkap', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('role', 'like', '%' . $this->search . '%')
                  ->orWhere('idlogin', 'like', '%' . $this->search . '%');
            });
        }

        if ($currentUser->role === 'audit') {
            $myBranchIds = $currentUser->access_cabang_ids ?? []; 
            $query->where(function($q) use ($myBranchIds) {
                $q->whereIn('cabang_id', $myBranchIds)
                  ->orWhereHas('branches', function($sq) use ($myBranchIds) {
                      $sq->whereIn('cabangs.id', $myBranchIds);
                  });
            });
            $query->where('role', '!=', 'superadmin');
        }

        $users = $query->latest()->paginate(10);

        if ($currentUser->role === 'superadmin') {
            $cabangs = Cabang::orderBy('nama_cabang', 'asc')->get();
        } else {
            $cabangs = Cabang::whereIn('id', $currentUser->access_cabang_ids ?? [])
                             ->orderBy('nama_cabang', 'asc')->get();
        }

        $distributors = Distributor::orderBy('nama_distributor', 'asc')->get();
        $gudangs = Gudang::orderBy('nama_gudang', 'asc')->get();

        return view('livewire.user.user-index', [
            'users' => $users,
            'cabangs' => $cabangs,
            'distributors' => $distributors,
            'gudangs' => $gudangs
        ]);
    }

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
        $this->gudang_id = '';
        $this->placement_type = ''; 
        $this->is_active = true; 
        $this->selected_branches = [];
        $this->userId = null;
        $this->isEdit = false;
        $this->resetErrorBag();
    }

    public function store()
    {
        $currentUser = Auth::user();

        // 1. Validasi Dasar
        $rules = [
            'nama_lengkap' => 'required',
            'tanggal_lahir' => 'required|date',
            'role'         => 'required',
            'idlogin'      => ['required', Rule::unique('users')->ignore($this->userId)],
            'email'        => ['required', 'email', Rule::unique('users')->ignore($this->userId)],
        ];

        // 2. Logic Validasi Inventory Staff (FIXED)
        if ($this->role === 'inventory_staff') {
            // Wajib pilih jenis penempatan
            $rules['placement_type'] = 'required|in:distributor,gudang';
            
            // Validasi dinamis berdasarkan pilihan radio
            if ($this->placement_type === 'distributor') {
                $rules['distributor_id'] = 'required';
            } elseif ($this->placement_type === 'gudang') {
                $rules['gudang_id'] = 'required';
            }
        }
        // Validasi Role Lain
        elseif ($this->role === 'distributor') {
            $rules['distributor_id'] = 'required';
        }
        elseif (in_array($this->role, ['adminproduk', 'analis', 'leader', 'sales', 'security'])) {
            $rules['cabang_id'] = 'required';
        }
        elseif ($this->role === 'audit' && $currentUser->role === 'superadmin') {
            $rules['selected_branches'] = 'required|array|min:1';
        }

        if (!$this->userId) {
            $rules['password'] = 'required|min:6';
        } else {
            $rules['password'] = 'nullable|min:6';
        }

        $this->validate($rules);

        // 3. Persiapan Data
        $data = [
            'nama_lengkap' => $this->nama_lengkap,
            'idlogin'      => $this->idlogin,
            'email'        => $this->email,
            'tanggal_lahir'=> $this->tanggal_lahir,
            'role'         => $this->role,
            'is_active'    => $this->is_active,
        ];

        // Reset ID agar bersih sebelum di-set ulang
        $data['distributor_id'] = null;
        $data['cabang_id'] = null;
        $data['gudang_id'] = null;

        // 4. Logic Penyimpanan ID (FIXED)
        if ($this->role === 'inventory_staff') {
            if ($this->placement_type === 'distributor') {
                $data['distributor_id'] = $this->distributor_id;
                // Role tetap inventory_staff, tapi punya akses distributor
            } elseif ($this->placement_type === 'gudang') {
                $data['gudang_id'] = $this->gudang_id;
                // Role tetap inventory_staff, tapi punya akses gudang
            }
        }
        elseif ($this->role === 'distributor') {
            $data['distributor_id'] = $this->distributor_id;
        }
        elseif (in_array($this->role, ['adminproduk', 'analis', 'leader', 'sales', 'security'])) {
            $data['cabang_id'] = $this->cabang_id;
        }

        if (!empty($this->password)) {
            $data['password'] = Hash::make($this->password);
        }

        // 5. Eksekusi
        $user = User::updateOrCreate(['id' => $this->userId], $data);

        // Sync Audit Branches
        if ($this->role === 'audit') {
            if ($currentUser->role === 'superadmin' || !$this->isEdit) {
                $user->branches()->sync($this->selected_branches);
            }
        } else {
            $user->branches()->detach();
        }

        $this->dispatch('close-modal');
        $this->dispatch('swal', [
            'title' => 'Berhasil!',
            'text' => 'Data user tersimpan dengan role & lokasi yang benar.',
            'icon' => 'success'
        ]);
        
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $currentUser = Auth::user();

        if ($currentUser->role === 'audit') {
            if ($user->role === 'superadmin') {
                $this->dispatch('swal', ['title'=>'Akses Ditolak', 'text'=>'Restricted Access.', 'icon'=>'error']);
                return;
            }
            if ($user->cabang_id && !in_array($user->cabang_id, $currentUser->access_cabang_ids ?? [])) {
                $this->dispatch('swal', ['title'=>'Akses Ditolak', 'text'=>'Diluar Wilayah Anda.', 'icon'=>'error']);
                return;
            }
        }

        $this->userId = $user->id;
        $this->nama_lengkap = $user->nama_lengkap;
        $this->idlogin = $user->idlogin;
        $this->email = $user->email;
        $this->tanggal_lahir = $user->tanggal_lahir;
        $this->role = $user->role;
        $this->is_active = (bool) $user->is_active;
        
        $this->distributor_id = $user->distributor_id;
        $this->cabang_id = $user->cabang_id;
        $this->gudang_id = $user->gudang_id;

        // Populate Placement Type saat Edit
        $this->placement_type = '';
        if ($user->role === 'inventory_staff') {
            if ($user->distributor_id) {
                $this->placement_type = 'distributor';
            } elseif ($user->gudang_id) {
                $this->placement_type = 'gudang';
            }
        }

        $this->selected_branches = $user->branches->pluck('id')->map(fn($id)=>(string)$id)->toArray();

        $this->isEdit = true;
        $this->resetErrorBag();
    }

    public function delete($id)
    {
        if ($id === auth()->id()) return;
        $user = User::findOrFail($id);
        $user->delete();
        $this->dispatch('swal', ['title'=>'Terhapus!', 'text'=>'User berhasil dihapus.', 'icon'=>'success']);
    }
    
    public function toggleStatus($id)
    {
        if ($id === auth()->id()) return; 
        $user = User::findOrFail($id);
        $user->is_active = !$user->is_active;
        $user->save();
    }
}