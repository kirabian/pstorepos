<?php

namespace App\Livewire\Gudang;

use App\Models\StockOpname;
use App\Models\Stok;
use App\Models\Tipe;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;

#[Layout('layouts.master')]
class StockOpnameIndex extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    
    // Form Input
    #[Rule('required')]
    public $tipe_id;
    
    #[Rule('required|numeric|min:0')]
    public $stok_fisik;
    
    #[Rule('nullable|string')]
    public $keterangan;

    // Data Helper untuk Form
    public $currentStokSistem = 0;
    public $selisihPreview = 0;

    public function mount()
    {
        // Reset form saat load
        $this->resetInputFields();
    }

    public function resetInputFields()
    {
        $this->tipe_id = '';
        $this->stok_fisik = '';
        $this->keterangan = '';
        $this->currentStokSistem = 0;
        $this->selisihPreview = 0;
        $this->resetErrorBag();
        $this->dispatch('reset-select-product'); // Reset dropdown produk
    }

    // Listener saat produk dipilih, otomatis ambil stok sistem saat ini
    public function updatedTipeId($value)
    {
        $user = Auth::user();
        // Tentukan cabang ID (Superadmin/Audit pakai logic lain, disini asumsi user gudang punya cabang_id)
        $cabangId = $user->cabang_id; 

        if($cabangId && $value) {
            $stok = Stok::where('cabang_id', $cabangId)
                        ->where('tipe_id', $value)
                        ->first();
            
            $this->currentStokSistem = $stok ? $stok->jumlah : 0;
        } else {
            $this->currentStokSistem = 0;
        }
        $this->calculateSelisih();
    }

    public function updatedStokFisik()
    {
        $this->calculateSelisih();
    }

    public function calculateSelisih()
    {
        if(is_numeric($this->stok_fisik) && $this->stok_fisik !== '') {
            $this->selisihPreview = (int)$this->stok_fisik - $this->currentStokSistem;
        } else {
            $this->selisihPreview = 0;
        }
    }

    public function store()
    {
        $this->validate();
        $user = Auth::user();

        if(!$user->cabang_id && $user->role !== 'superadmin') {
            $this->dispatch('swal', ['title' => 'Error', 'text' => 'Anda tidak terdaftar di cabang manapun.', 'icon' => 'error']);
            return;
        }

        // Logic Cabang (Fallback ke cabang pertama jika superadmin test)
        $cabangId = $user->cabang_id ?? \App\Models\Cabang::first()->id;

        // 1. Simpan Log Stock Opname
        StockOpname::create([
            'cabang_id' => $cabangId,
            'user_id' => $user->id,
            'tipe_id' => $this->tipe_id,
            'stok_sistem' => $this->currentStokSistem,
            'stok_fisik' => $this->stok_fisik,
            'selisih' => $this->selisihPreview,
            'keterangan' => $this->keterangan,
            'tanggal_opname' => now(),
        ]);

        // 2. Update Master Stok (Penyesuaian Real)
        Stok::updateOrCreate(
            ['cabang_id' => $cabangId, 'tipe_id' => $this->tipe_id],
            ['jumlah' => $this->stok_fisik] // Override stok dengan hasil fisik
        );

        $this->dispatch('close-modal');
        $this->dispatch('swal', [
            'title' => 'Stok Disesuaikan!',
            'text' => 'Data stok opname berhasil disimpan dan stok sistem diperbarui.',
            'icon' => 'success'
        ]);

        $this->resetInputFields();
    }

    public function render()
    {
        $user = Auth::user();
        
        // Query History Opname
        $query = StockOpname::with(['tipe.merk', 'user', 'cabang'])->latest();

        // Filter: Gudang hanya lihat cabangnya sendiri
        if($user->role === 'gudang' && $user->cabang_id) {
            $query->where('cabang_id', $user->cabang_id);
        }

        // Filter Search
        if($this->search) {
            $query->whereHas('tipe', function($q){
                $q->where('nama', 'like', '%'.$this->search.'%');
            });
        }

        $opnames = $query->paginate(10);

        // Data Produk untuk Dropdown (Hanya yang ada di Merk)
        $products = Tipe::with('merk')->orderBy('nama', 'asc')->get();

        return view('livewire.gudang.stock-opname-index', [
            'opnames' => $opnames,
            'products' => $products
        ]);
    }
}