<?php

namespace App\Livewire\Product;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductImei;

class ProductIndex extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search = '';

    public function render()
    {
        $products = Product::with(['brand', 'variants.imeis'])
            ->where('name', 'like', '%' . $this->search . '%')
            ->orWhereHas('brand', function($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        return view('livewire.product.product-index', [
            'products' => $products
        ]);
    }

    public function deleteVariant($variantId)
    {
        ProductVariant::find($variantId)?->delete();
        session()->flash('success', 'Varian berhasil dihapus');
    }
}