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
    
    // ID Lokasi (Penyimpanan Data)
    public $distributor_id, $cabang_id, $gudang_id; 

    // Logic UI: Penempatan Kerja Khusus Inventory Staff
    public $placement_type = ''; // Values: 'distributor' atau 'gudang'

    // Khusus Audit: Multi Cabang Selection
    public $selected_branches = []; 

    protected $updatesQueryString = ['search'];

    #[On('echo:pstore-channel,inventory.updated')]
    public function refreshTable() { }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    // === LOGIC SAAT ROLE BERUBAH ===
    public function updatedRole($value)
    {
        // 1. Jika Role bukan Inventory Staff, reset pilihan penempatan
        if ($value !== 'inventory_staff') {
            $this->placement_type = '';
            $this->gudang_id = null;
            $this->distributor_id = null;
        }
        
        // 2. Reset ID lokasi lain jika Role berubah drastis
        if ($value === 'distributor') {
            // Jika role = Pemilik Distributor, otomatis reset gudang & cabang
            $this->cabang_id = null;
            $this->gudang_id = null;
        } 
        elseif (in_array($value, ['adminproduk', 'analis', 'leader', 'sales', 'security'])) {
            // Jika role = Operasional Cabang, reset gudang & distributor
            $this->distributor_id = null;
            $this->gudang_id = null;
        }
    }

    // === LOGIC SAAT PLACEMENT TYPE BERUBAH (Radio Button) ===
    public function updatedPlacementType()
    {
        // Reset dropdown pilihan agar tidak tercampur
        $this->distributor_id = null;
        $this->gudang_id = null;
        $this->cabang_id = null;
    }

    // === READ (RENDER TABLE) ===
    public function render()
    {
        $currentUser = Auth::user();
        
        // Eager load semua relasi lokasi
        $query = User::with(['distributor', 'cabang', 'gudang', 'branches']);

        // Filter Search
        if ($this->search) {
            $query->where(function($q) {
                $q->where('nama_lengkap', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('role', 'like', '%' . $this->search . '%')
                  ->orWhere('idlogin', 'like', '%' . $this->search . '%');
            });
        }

        // Logic Proteksi Audit (Hanya lihat cabang sendiri)
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

        // Load Data Dropdown
        $cabangs = [];
        if ($currentUser->role === 'superadmin') {
            $cabangs = Cabang::orderBy('nama_cabang', 'asc')->get();
        } else {
            $cabangs = Cabang::whereIn('id', $currentUser->access_cabang_ids ?? [])
                             ->orderBy('nama_cabang', 'asc')
                             ->get();
        }

        $distributors = Distributor::orderBy('nama_distributor', 'asc')->get();
        $gudangs = Gudang::orderBy('nama_gudang', 'asc')->get();

        return view('livewire.auth.user-index', [
            'users' => $users,
            'cabangs' => $cabangs,
            'distributors' => $distributors,
            'gudangs' => $gudangs
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
        $this->gudang_id = '';
        
        $this->placement_type = ''; 
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

        // 1. Rules Validasi Dasar
        $rules = [
            'nama_lengkap' => 'required',
            'tanggal_lahir' => 'required|date',
            'role'         => 'required',
            'idlogin'      => ['required', Rule::unique('users')->ignore($this->userId)],
            'email'        => ['required', 'email', Rule::unique('users')->ignore($this->userId)],
        ];

        // 2. Logic Validasi Kompleks Berdasarkan Role & Penempatan
        if ($this->role === 'distributor') {
            // Role Pemilik Distributor
            $rules['distributor_id'] = 'required';
        }
        elseif ($this->role === 'inventory_staff') {
            // Role Inventory Staff (Harus pilih lokasi kerja: Distributor ATAU Gudang)
            $rules['placement_type'] = 'required|in:distributor,gudang';
            
            if ($this->placement_type === 'distributor') {
                $rules['distributor_id'] = 'required';
            } else {
                $rules['gudang_id'] = 'required';
            }
        }
        elseif ($this->role === 'gudang') {
            // Role Kepala Gudang (Opsional jika masih dipakai)
            $rules['gudang_id'] = 'required';
        }
        elseif (in_array($this->role, ['adminproduk', 'analis', 'leader', 'sales', 'security'])) {
            // Role Operasional Cabang
            $rules['cabang_id'] = 'required';
        }
        elseif ($this->role === 'audit') {
            if ($currentUser->role === 'superadmin') {
                $rules['selected_branches'] = 'required|array|min:1';
            }
        }

        // Validasi Password (Wajib jika create baru, opsional jika edit)
        if (!$this->userId) {
            $rules['password'] = 'required|min:6';
        } else {
            $rules['password'] = 'nullable|min:6';
        }

        $this->validate($rules);

        // 3. Security Check (Audit tidak boleh buat user di luar cabangnya)
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
        }

        // 4. Mapping Data ke Database
        $data = [
            'nama_lengkap' => $this->nama_lengkap,
            'idlogin'      => $this->idlogin,
            'email'        => $this->email,
            'tanggal_lahir'=> $this->tanggal_lahir,
            'role'         => $this->role,
            'is_active'    => $this->is_active,
        ];

        // Reset semua ID lokasi agar bersih (menghindari duplikasi hak akses lokasi)
        $data['distributor_id'] = null;
        $data['cabang_id'] = null;
        $data['gudang_id'] = null;

        // Logic Save ID Lokasi
        if ($this->role === 'distributor') {
            $data['distributor_id'] = $this->distributor_id;
        } 
        elseif ($this->role === 'inventory_staff') {
            // Disini logikanya: Role tetap 'inventory_staff', tapi lokasinya disimpan beda kolom
            if ($this->placement_type === 'distributor') {
                $data['distributor_id'] = $this->distributor_id;
            } else {
                $data['gudang_id'] = $this->gudang_id;
            }
        }
        elseif ($this->role === 'gudang') {
            $data['gudang_id'] = $this->gudang_id;
        }
        elseif (in_array($this->role, ['superadmin', 'audit'])) {
            // Superadmin & Audit tidak terikat satu lokasi fisik
        }
        else {
            // Role operasional cabang
            $data['cabang_id'] = $this->cabang_id;
        }

        if (!empty($this->password)) {
            $data['password'] = Hash::make($this->password);
        }

        // 5. Eksekusi Simpan ke DB
        $user = User::updateOrCreate(['id' => $this->userId], $data);

        // 6. Sync Multi Cabang (Khusus Audit)
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

    // === EDIT FORM ===
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $currentUser = Auth::user();

        // Validasi Akses Audit
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
        
        // Load IDs Lokasi
        $this->distributor_id = $user->distributor_id;
        $this->cabang_id = $user->cabang_id;
        $this->gudang_id = $user->gudang_id;

        // Tentukan Placement Type saat Edit (Khusus Inventory Staff)
        $this->placement_type = '';
        if ($user->role === 'inventory_staff') {
            if ($user->distributor_id) {
                $this->placement_type = 'distributor';
            } elseif ($user->gudang_id) {
                $this->placement_type = 'gudang';
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