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
    public $variant_id;
    
    // Data Form
    public $name;
    public $brand_id;
    public $category_id;
    
    // Data Varian
    public $attribute_name;
    public $stock;
    public $cost_price;
    public $srp_price;

    public $existing_types = [];

    public function mount($id)
    {
        $product = Product::with('variants')->findOrFail($id);
        
        $this->product_id = $product->id;
        $this->name = $product->name;
        $this->brand_id = $product->brand_id;
        $this->category_id = $product->category_id;

        // Load existing types untuk brand ini saat pertama load
        $this->loadExistingTypes($this->brand_id);

        $variant = $product->variants->first();
        
        if($variant) {
            $this->variant_id = $variant->id;
            $this->attribute_name = $variant->attribute_name;
            $this->stock = $variant->stock;
            $this->cost_price = $variant->cost_price;
            $this->srp_price = $variant->srp_price;
        }
    }

    public function updatedBrandId($value)
    {
        $this->loadExistingTypes($value);
    }

    public function loadExistingTypes($brandId)
    {
        if(!empty($brandId)) {
            $this->existing_types = Product::where('brand_id', $brandId)
                ->select('name')
                ->distinct()
                ->orderBy('name', 'asc')
                ->pluck('name')
                ->toArray();
        } else {
            $this->existing_types = [];
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
                'brand_id' => $this->brand_id,
                'category_id' => $this->category_id,
            ]);

            if ($this->variant_id) {
                $variant = ProductVariant::findOrFail($this->variant_id);
                $variant->update([
                    'attribute_name' => $this->attribute_name,
                    'stock' => $this->stock,
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