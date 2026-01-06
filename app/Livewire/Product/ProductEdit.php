<?php

namespace App\Livewire\Product;

use Livewire\Component;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;

class ProductEdit extends Component
{
    public $product_id;
    public $variant_id; // Jika ingin edit spesifik varian
    
    // Data Form
    public $name;
    public $brand_id;
    public $category_id;
    
    // Data Varian (Kita ambil varian pertama atau yang diedit)
    public $attribute_name; // Nama varian full
    public $stock;
    public $cost_price;
    public $srp_price;

    public function mount($id)
    {
        $product = Product::with('variants')->findOrFail($id);
        
        $this->product_id = $product->id;
        $this->name = $product->name;
        $this->brand_id = $product->brand_id;
        $this->category_id = $product->category_id;

        // Ambil varian pertama untuk diedit (Sederhana)
        // Jika produk punya banyak varian, idealnya ada list varian di bawah form edit
        $variant = $product->variants->first();
        
        if($variant) {
            $this->variant_id = $variant->id;
            $this->attribute_name = $variant->attribute_name;
            $this->stock = $variant->stock;
            $this->cost_price = $variant->cost_price;
            $this->srp_price = $variant->srp_price;
        }
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|min:3',
            'category_id' => 'required',
            'cost_price' => 'required|numeric',
            'srp_price' => 'required|numeric',
            'stock' => 'numeric',
        ]);

        DB::beginTransaction();
        try {
            $product = Product::findOrFail($this->product_id);
            $product->update([
                'name' => $this->name,
                'brand_id' => $this->brand_id, // Bisa null jika jasa
                'category_id' => $this->category_id,
            ]);

            if ($this->variant_id) {
                $variant = ProductVariant::findOrFail($this->variant_id);
                $variant->update([
                    'attribute_name' => $this->attribute_name, // User bisa ubah nama varian manual
                    'stock' => $this->stock, // Edit stok manual (opname sederhana)
                    'cost_price' => $this->cost_price,
                    'srp_price' => $this->srp_price,
                ]);
            }

            DB::commit();
            session()->flash('success', 'Produk berhasil diperbarui.');
            return redirect()->route('product.index');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.product.product-edit', [
            'categories' => Category::orderBy('name', 'asc')->get(),
            'brands' => Brand::orderBy('name', 'asc')->get(),
        ]);
    }
}