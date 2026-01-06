<?php

namespace App\Livewire\Product;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Livewire\Component;

class ProductIndex extends Component
{
    public $name;

    public $category_id;

    public $brand_id;

    public $description;

    public $variants = []; // Untuk handle multi-variant (RAM, Stok, Modal, SRP)

    public function mount()
    {
        $this->addVariant();
    }

    public function addVariant()
    {
        $this->variants[] = ['attribute' => '', 'stock' => 0, 'cost' => 0, 'srp' => 0];
    }

    public function saveProduct()
    {
        $this->validate([
            'name' => 'required',
            'category_id' => 'required',
            'brand_id' => 'required',
            'variants.*.attribute' => 'required',
            'variants.*.cost' => 'required|numeric',
            'variants.*.srp' => 'required|numeric',
        ]);

        $product = Product::create([
            'name' => $this->name,
            'category_id' => $this->category_id,
            'brand_id' => $this->brand_id,
            'description' => $this->description,
        ]);

        foreach ($this->variants as $v) {
            ProductVariant::create([
                'product_id' => $product->id,
                'attribute_name' => $v['attribute'],
                'stock' => $v['stock'],
                'cost_price' => $v['cost'],
                'srp_price' => $v['srp'],
            ]);
        }

        session()->flash('success', 'Produk berhasil ditambahkan!');

        return redirect()->route('product.index');
    }

    public function render()
    {
        return view('livewire.product.product-index', [
            'products' => Product::with(['category', 'brand', 'variants'])->latest()->get(),
            'categories' => Category::all(),
            'brands' => Brand::all(),
        ]);
    }
}
