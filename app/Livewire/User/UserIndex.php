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
    
    // Logic Baru: Penempatan Kerja untuk Inventory Staff
    public $placement_type = ''; // Values: 'distributor' atau 'cabang'

    // Khusus Audit: Multi Cabang Selection
    public $selected_branches = []; 

    protected $updatesQueryString = ['search'];

    #[On('echo:pstore-channel,inventory.updated')]
    public function refreshTable() { }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    // Reset input penempatan jika role berubah
    public function updatedRole($value)
    {
        if ($value !== 'gudang') {
            $this->placement_type = '';
        }
        
        // Reset ID jika role berubah
        if ($value === 'distributor') {
            $this->cabang_id = null;
        } elseif (in_array($value, ['adminproduk', 'analis', 'leader', 'sales', 'security'])) {
            $this->distributor_id = null;
        }
    }

    public function updatedPlacementType()
    {
        // Reset pilihan dropdown saat ganti radio button
        $this->distributor_id = null;
        $this->cabang_id = null;
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

        // 2. LOGIKA PROTEKSI DATA AUDIT
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

        // 3. LOGIKA DROPDOWN CABANG
        if ($currentUser->role === 'superadmin') {
            $cabangs = Cabang::orderBy('nama_cabang', 'asc')->get();
        } else {
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
        $this->placement_type = ''; // Reset placement
        $this->is_active = true; 
        $this->selected_branches = [];
        $this->userId = null;
        $this->isEdit = false;
        $this->resetErrorBag();
    }

    // === CREATE / UPDATE (STORE) ===
    public function store()
    {
        $currentUser = Auth::user();

        // 1. Rules Dasar
        $rules = [
            'nama_lengkap' => 'required',
            'tanggal_lahir' => 'required|date',
            'role'         => 'required',
            'idlogin'      => ['required', Rule::unique('users')->ignore($this->userId)],
            'email'        => ['required', 'email', Rule::unique('users')->ignore($this->userId)],
        ];

        // 2. Logic Validasi Berdasarkan Role & Placement
        if ($this->role === 'distributor') {
            // Role Distributor Murni (Owner)
            $rules['distributor_id'] = 'required';
        }
        elseif ($this->role === 'gudang') {
            // Role Inventory Staff (Bisa di Distributor / Cabang)
            $rules['placement_type'] = 'required|in:distributor,cabang';
            
            if ($this->placement_type === 'distributor') {
                $rules['distributor_id'] = 'required';
            } else {
                $rules['cabang_id'] = 'required';
            }
        }
        elseif (in_array($this->role, ['adminproduk', 'analis', 'leader', 'sales', 'security'])) {
            // Role Cabang Lainnya
            $rules['cabang_id'] = 'required';
        }
        elseif ($this->role === 'audit') {
            if ($currentUser->role === 'superadmin') {
                $rules['selected_branches'] = 'required|array|min:1';
            }
        }

        if (!$this->userId) {
            $rules['password'] = 'required|min:6';
        } else {
            $rules['password'] = 'nullable|min:6';
        }

        $this->validate($rules);

        // 3. SECURITY CHECK & PROTEKSI AUDIT
        if ($currentUser->role === 'audit') {
            if ($this->role === 'superadmin') {
                abort(403, 'Anda tidak memiliki akses membuat Superadmin.');
            }

            if ($this->cabang_id) {
                if (!in_array($this->cabang_id, $currentUser->access_cabang_ids ?? [])) {
                    $this->addError('cabang_id', 'Anda tidak berhak menambahkan user di cabang ini.');
                    return;
                }
            }

            if ($this->isEdit && $this->role === 'audit') {
                $existingUser = User::find($this->userId);
                if ($existingUser) {
                    $this->selected_branches = $existingUser->branches->pluck('id')->map(fn($id) => (string)$id)->toArray();
                }
            }
        }

        // 4. Siapkan Data
        $data = [
            'nama_lengkap' => $this->nama_lengkap,
            'idlogin'      => $this->idlogin,
            'email'        => $this->email,
            'tanggal_lahir'=> $this->tanggal_lahir,
            'role'         => $this->role,
            'is_active'    => $this->is_active,
        ];

        // Logic Save ID berdasarkan Placement
        if ($this->role === 'distributor') {
            $data['distributor_id'] = $this->distributor_id;
            $data['cabang_id'] = null;
        } 
        elseif ($this->role === 'gudang') {
            if ($this->placement_type === 'distributor') {
                $data['distributor_id'] = $this->distributor_id;
                $data['cabang_id'] = null;
            } else {
                $data['cabang_id'] = $this->cabang_id;
                $data['distributor_id'] = null;
            }
        }
        elseif (in_array($this->role, ['superadmin', 'audit'])) {
            $data['distributor_id'] = null;
            $data['cabang_id'] = null;
        }
        else {
            // Role operasional cabang biasa
            $data['cabang_id'] = $this->cabang_id;
            $data['distributor_id'] = null;
        }

        if (!empty($this->password)) {
            $data['password'] = Hash::make($this->password);
        }

        // 5. Eksekusi Simpan
        $user = User::updateOrCreate(['id' => $this->userId], $data);

        // 6. Sync Multi Cabang Audit
        if ($this->role === 'audit') {
            if ($currentUser->role === 'superadmin' || !$this->isEdit) {
                $user->branches()->sync($this->selected_branches);
            }
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

        if ($currentUser->role === 'audit') {
            if ($user->role === 'superadmin') {
                $this->dispatch('swal', ['title'=>'Akses Ditolak', 'text'=>'Anda tidak bisa mengedit Superadmin.', 'icon'=>'error']);
                return;
            }
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
        $this->is_active = (bool) $user->is_active;
        
        // Load IDs
        $this->distributor_id = $user->distributor_id;
        $this->cabang_id = $user->cabang_id;

        // Tentukan Placement Type saat Edit
        $this->placement_type = '';
        if ($user->role === 'gudang') {
            if ($user->distributor_id) {
                $this->placement_type = 'distributor';
            } elseif ($user->cabang_id) {
                $this->placement_type = 'cabang';
            }
        }

        $this->selected_branches = $user->branches->pluck('id')
            ->map(fn($id) => (string) $id) 
            ->toArray();

        $this->isEdit = true;
        $this->resetErrorBag();
    }

    // === DELETE ===
    public function delete($id)
    {
        if ($id === auth()->id()) return;
        
        $user = User::findOrFail($id);
        $currentUser = Auth::user();
        
        if ($currentUser->role === 'audit') {
            if ($user->role === 'superadmin') return;
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
        
        if ($currentUser->role === 'audit') {
            if ($user->role === 'superadmin') return;
            if ($user->cabang_id && !in_array($user->cabang_id, $currentUser->access_cabang_ids ?? [])) {
                $this->dispatch('swal', ['title'=>'Gagal', 'text'=>'User diluar akses cabang Anda.', 'icon'=>'error']);
                return;
            }
        }

        $user->is_active = !$user->is_active;
        $user->save();
    }
}