<?php

namespace App\Livewire\Product;

use Livewire\Component;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\ProductVariant;

class ProductCreate extends Component
{
    public $name, $category_id, $brand_id, $description;
    public $variants = [];

    public function mount()
    {
        $this->addVariant();
    }

    public function addVariant()
    {
        $this->variants[] = ['attribute' => '', 'stock' => 0, 'cost' => 0, 'srp' => 0];
    }

    public function removeVariant($index)
    {
        if (count($this->variants) > 1) {
            unset($this->variants[$index]);
            $this->variants = array_values($this->variants);
        }
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|min:3',
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

        session()->flash('success', 'Produk dan Varian berhasil disimpan.');
        return redirect()->route('product.index');
    }

    public function render()
    {
        return view('livewire.product.product-create', [
            'categories' => Category::orderBy('name', 'asc')->get(),
            'brands' => Brand::orderBy('name', 'asc')->get(),
        ]);
    }
}