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
    
    // LIST UNTUK DROPDOWN TYPE
    public $existing_types = [];

    public function updatedFormType()
    {
        $this->resetValidation();
        // Reset inputs
        $this->reset(['name', 'ram', 'storage', 'color', 'description']);
        
        // Auto select category based on type
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

    public function save()
    {
        // 1. Validasi Minimalis (Tanpa Stok/Harga)
        if ($this->form_type == 'jasa') {
            $this->validate([
                'name' => 'required|min:3',
                'category_id' => 'required',
            ]);
        } elseif ($this->form_type == 'non-imei') {
            $this->validate([
                'brand_id' => 'required',
                'name' => 'required|min:3',
                'category_id' => 'required',
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

            // 3. Tentukan Nama Varian Tunggal
            if ($this->form_type == 'imei') {
                $attributeName = "{$this->ram}/{$this->storage} {$this->color} ({$this->condition})";
            } elseif ($this->form_type == 'non-imei') {
                $attributeName = $this->color ? $this->color : 'Standard';
            } else {
                $attributeName = 'Jasa Layanan';
            }

            // 4. Cek apakah varian ini sudah ada di produk tersebut?
            // Agar tidak double "Original" dan varian baru
            $existingVariant = ProductVariant::where('product_id', $product->id)
                                ->where('attribute_name', $attributeName)
                                ->first();

            if (!$existingVariant) {
                // Hanya create jika belum ada
                ProductVariant::create([
                    'product_id' => $product->id,
                    'attribute_name' => $attributeName,
                    'stock' => 0, // Default 0, edit nanti
                    'cost_price' => 0,
                    'srp_price' => 0,
                ]);
            }

            // Jika produk baru dibuat dan punya varian 'Original' bawaan yang tidak diinginkan (dari observer lain jika ada),
            // kita bisa hapus varian 'Original' jika namanya beda dengan yang baru kita buat.
            // (Opsional, tergantung setup DB Anda).
            
            DB::commit();

            session()->flash('success', 'Produk berhasil dibuat. Silakan update stok & harga.');
            // Redirect ke halaman EDIT produk tersebut agar user langsung isi stok
            return redirect()->route('product.edit', $product->id);

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