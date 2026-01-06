<?php

namespace App\Livewire\Product;

use Livewire\Component;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\ProductVariant;
use App\Models\ProductImei;
use Illuminate\Support\Facades\DB;

class ProductCreate extends Component
{
    // Mode Form: 'imei', 'non-imei', 'jasa'
    public $form_type = 'imei';

    // Data Utama
    public $brand_id;
    public $category_id;
    public $name; 
    
    // Data Detail / Varian
    public $ram;
    public $storage;
    public $color;
    public $condition = 'Baru'; 
    public $description; 
    
    // Stok & Harga
    public $stock = 0;
    public $cost_price = 0;
    public $srp_price = 0;
    
    // Khusus IMEI
    public $imei_list; 

    // Helper Dropdown
    public $existing_types = [];

    public function updatedFormType()
    {
        $this->resetValidation();
        $this->reset(['name', 'ram', 'storage', 'color', 'description', 'imei_list', 'stock', 'cost_price', 'srp_price']);
        
        // Auto select category
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
                ->select('name')->distinct()->orderBy('name', 'asc')->pluck('name')->toArray();
        } else {
            $this->existing_types = [];
        }
    }

    // FUNGSI updatedImeiList() DIHAPUS AGAR TIDAK ADA KALKULASI REALTIME YANG MENGGANGGU

    public function save()
    {
        // 1. Validasi
        $rules = [
            'name' => 'required|min:3',
            'category_id' => 'required',
            'cost_price' => 'required|numeric|min:0',
            'srp_price' => 'required|numeric|min:0',
        ];

        if ($this->form_type == 'imei') {
            $rules = array_merge($rules, [
                'brand_id' => 'required',
                'ram' => 'required',
                'storage' => 'required',
                'color' => 'required',
                'condition' => 'required',
                'imei_list' => 'required', // Wajib isi text area
            ]);
        } elseif ($this->form_type == 'non-imei') {
            $rules = array_merge($rules, [
                'brand_id' => 'required',
                'stock' => 'required|numeric|min:0',
            ]);
        }

        $this->validate($rules);

        // 2. Validasi Khusus IMEI (Panjang Karakter & Duplikat di Input)
        $validImeis = [];
        if ($this->form_type == 'imei') {
            // Pisahkan baris per baris
            $lines = explode("\n", $this->imei_list);
            $duplicates = [];
            
            foreach ($lines as $line) {
                $cleanImei = trim($line);
                if (empty($cleanImei)) continue; // Skip baris kosong

                // Cek Panjang IMEI (Minimal 15)
                if (strlen($cleanImei) < 15) {
                    $this->addError('imei_list', "IMEI '$cleanImei' tidak valid (kurang dari 15 digit).");
                    return;
                }

                // Cek apakah IMEI sudah ada di Database
                if (ProductImei::where('imei', $cleanImei)->exists()) {
                    $this->addError('imei_list', "IMEI '$cleanImei' sudah terdaftar di sistem.");
                    return;
                }

                if (in_array($cleanImei, $validImeis)) {
                    $duplicates[] = $cleanImei;
                } else {
                    $validImeis[] = $cleanImei;
                }
            }

            if (count($duplicates) > 0) {
                $this->addError('imei_list', 'Ada IMEI duplikat di inputan Anda: ' . implode(', ', $duplicates));
                return;
            }
            
            // Set Stok Akhir berdasarkan jumlah valid IMEI untuk disimpan ke DB
            $this->stock = count($validImeis);
        }

        DB::beginTransaction();
        try {
            // 3. Create Produk
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

            // 4. Create Varian
            if ($this->form_type == 'imei') {
                $attributeName = "{$this->ram}/{$this->storage} {$this->color} ({$this->condition})";
            } elseif ($this->form_type == 'non-imei') {
                $attributeName = $this->color ? $this->color : 'Standard';
            } else {
                $attributeName = 'Jasa Layanan';
            }

            $variant = ProductVariant::firstOrCreate(
                [
                    'product_id' => $product->id,
                    'attribute_name' => $attributeName,
                ],
                [
                    'stock' => 0, 
                    'cost_price' => $this->cost_price,
                    'srp_price' => $this->srp_price,
                ]
            );

            // Update harga untuk memastikan data terbaru
            $variant->update([
                'cost_price' => $this->cost_price,
                'srp_price' => $this->srp_price,
            ]);

            // 5. Jika IMEI, Simpan ke tabel product_imeis & update stok
            if ($this->form_type == 'imei') {
                foreach ($validImeis as $imei) {
                    ProductImei::create([
                        'product_variant_id' => $variant->id,
                        'imei' => $imei,
                        'status' => 'available'
                    ]);
                }
                // Tambahkan stok ke varian yang sudah ada (increment)
                $variant->increment('stock', count($validImeis));
            } else {
                // Jika non-imei/jasa, tambahkan stok manual
                $variant->increment('stock', $this->stock);
            }

            DB::commit();

            session()->flash('success', 'Produk berhasil disimpan.');
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