<?php

namespace App\Livewire\Product;

use Livewire\Component;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductVariant;
use App\Models\ProductImei;
use Illuminate\Support\Facades\DB;

class ProductCreate extends Component
{
    public $mode = 'imei'; // imei, non-imei, jasa

    // Form Fields
    public $brand_id;
    public $product_name; // "Tipe"
    public $category = 'Handphone';
    
    // Variant Specs
    public $ram;
    public $storage;
    public $color;
    public $condition = 'Baru (New)';
    
    // Pricing & Stock
    public $cost_price;
    public $srp_price;
    public $imei_input; // Textarea
    public $manual_stock = 0; // Untuk non-imei

    // Data Lists
    public $brands;
    public $existing_types = [];

    public function mount()
    {
        $this->brands = Brand::orderBy('name')->get();
    }

    public function updatedMode($value)
    {
        $this->reset(['ram', 'storage', 'color', 'imei_input', 'manual_stock']);
        if($value == 'jasa') $this->category = 'Jasa';
        else if($value == 'non-imei') $this->category = 'Aksesoris';
        else $this->category = 'Handphone';
    }

    public function updatedBrandId($value)
    {
        if($value) {
            $this->existing_types = Product::where('brand_id', $value)
                ->select('name')->distinct()->pluck('name')->toArray();
        }
    }

    public function save()
    {
        $this->validate([
            'brand_id' => 'required_unless:mode,jasa',
            'product_name' => 'required',
            'cost_price' => 'required|numeric',
            'srp_price' => 'required|numeric',
        ]);

        DB::beginTransaction();
        try {
            // 1. Create/Find Product (Tipe)
            $product = Product::firstOrCreate([
                'name' => $this->product_name,
                'brand_id' => $this->mode == 'jasa' ? null : $this->brand_id, // Jasa might not have brand
                'category' => $this->category
            ]);

            // 2. Prepare Variant Data
            $variantData = [
                'product_id' => $product->id,
                'color' => $this->color,
                'condition' => $this->condition,
                'cost_price' => $this->cost_price,
                'srp_price' => $this->srp_price,
            ];

            if($this->mode == 'imei') {
                $variantData['ram'] = $this->ram;
                $variantData['storage'] = $this->storage;
            }

            // 3. Create Variant
            // Cek jika varian sama persis sudah ada
            $variant = ProductVariant::where($variantData)->first();
            if(!$variant) {
                $variant = ProductVariant::create(array_merge($variantData, ['stock' => 0]));
            }

            // 4. Handle IMEI & Stock
            if ($this->mode == 'imei' && !empty($this->imei_input)) {
                $imeis = array_filter(explode("\n", $this->imei_input));
                $count = 0;
                
                foreach ($imeis as $imei) {
                    $cleanImei = trim($imei);
                    if(strlen($cleanImei) > 0) {
                        // Cek duplikat
                        if(!ProductImei::where('imei', $cleanImei)->exists()) {
                            ProductImei::create([
                                'product_variant_id' => $variant->id,
                                'imei' => $cleanImei,
                                'status' => 'available'
                            ]);
                            $count++;
                        }
                    }
                }
                // Update Stock Varian
                $variant->increment('stock', $count);
            
            } elseif ($this->mode == 'non-imei' && $this->manual_stock > 0) {
                $variant->increment('stock', $this->manual_stock);
            }

            DB::commit();
            session()->flash('success', 'Produk berhasil ditambahkan!');
            return redirect()->route('product.index');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.product.product-create');
    }
}