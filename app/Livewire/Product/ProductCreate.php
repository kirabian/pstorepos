<?php

namespace App\Livewire\Product;

use Livewire\Component;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;

class ProductCreate extends Component
{
    // Mode Form: 'imei', 'non-imei', 'jasa'
    public $form_type = 'imei';

    // Data Utama
    public $brand_id;
    public $category_id;
    public $name; // Tipe / Nama Produk
    
    // Data Detail / Varian
    public $ram;
    public $storage;
    public $color;
    public $condition = 'Baru'; // Baru / Second
    public $description; // Catatan
    
    // Data Stok & Harga
    public $cost_price = 0; // Modal
    public $srp_price = 0;  // Harga Jual
    public $stock = 0;
    
    // Khusus IMEI
    public $imei_list; 

    // LIST UNTUK DROPDOWN TYPE
    public $existing_types = [];

    public function updatedFormType()
    {
        $this->resetValidation();
        // Set default category
        if($this->form_type == 'jasa') {
            $cat = Category::where('name', 'like', '%Jasa%')->first();
            $this->category_id = $cat ? $cat->id : null;
        } elseif ($this->form_type == 'imei') {
            $cat = Category::where('name', 'like', '%Handphone%')->first();
            $this->category_id = $cat ? $cat->id : null;
        } else {
            $cat = Category::where('name', 'like', '%Aksesoris%')->first();
            $this->category_id = $cat ? $cat->id : null;
        }
    }

    // SAAT BRAND DIPILIH, AMBIL DAFTAR TIPE PRODUK YANG SUDAH ADA
    public function updatedBrandId($value)
    {
        if(!empty($value)) {
            $this->existing_types = Product::where('brand_id', $value)
                ->select('name')
                ->distinct()
                ->orderBy('name', 'asc')
                ->pluck('name')
                ->toArray();
        } else {
            $this->existing_types = [];
        }
    }

    public function updatedImeiList()
    {
        if ($this->form_type == 'imei' && !empty($this->imei_list)) {
            $lines = array_filter(explode("\n", $this->imei_list));
            $this->stock = count($lines);
        } else {
            if ($this->form_type == 'imei') {
                $this->stock = 0;
            }
        }
    }

    public function save()
    {
        // 1. Validasi
        if ($this->form_type == 'jasa') {
            $this->validate([
                'name' => 'required|min:3',
                'category_id' => 'required',
                'cost_price' => 'required|numeric',
                'srp_price' => 'required|numeric',
            ]);
        } elseif ($this->form_type == 'non-imei') {
            $this->validate([
                'brand_id' => 'required',
                'name' => 'required|min:3',
                'category_id' => 'required',
                'cost_price' => 'required|numeric',
                'srp_price' => 'required|numeric',
                'stock' => 'required|numeric|min:0',
            ]);
        } else {
            // Tipe IMEI
            $this->validate([
                'brand_id' => 'required',
                'name' => 'required|min:3',
                'category_id' => 'required',
                'ram' => 'required',
                'storage' => 'required',
                'color' => 'required',
                'condition' => 'required',
                'cost_price' => 'required|numeric',
                'srp_price' => 'required|numeric',
            ]);
        }

        DB::beginTransaction();
        try {
            // 2. Buat / Cari Produk Master
            $product = Product::firstOrCreate(
                [
                    'name' => $this->name,
                    'brand_id' => ($this->form_type == 'jasa') ? null : $this->brand_id,
                ],
                [
                    'category_id' => $this->category_id,
                    'description' => ($this->form_type == 'imei') 
                                     ? "Spesifikasi: {$this->ram}/{$this->storage}" 
                                     : $this->description,
                ]
            );

            // 3. Buat Nama Varian
            if ($this->form_type == 'imei') {
                $attributeName = "{$this->ram}/{$this->storage} {$this->color} ({$this->condition})";
            } elseif ($this->form_type == 'non-imei') {
                $attributeName = $this->color ? $this->color : 'Standard';
            } else {
                $attributeName = 'Jasa Layanan';
            }

            // 4. Simpan Varian
            ProductVariant::create([
                'product_id' => $product->id,
                'attribute_name' => $attributeName,
                'stock' => $this->stock,
                'cost_price' => $this->cost_price,
                'srp_price' => $this->srp_price,
            ]);

            DB::commit();

            session()->flash('success', 'Produk berhasil ditambahkan.');
            return redirect()->route('product.index');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.product.product-create', [
            'categories' => Category::orderBy('name', 'asc')->get(),
            'brands' => Brand::orderBy('name', 'asc')->get(),
        ]);
    }
}