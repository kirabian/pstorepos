<?php
namespace App\Livewire\Product;

use Livewire\Component;
use Livewire\WithFileUploads; // Tambahkan ini
use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;

class ProductIndex extends Component
{
    use WithFileUploads;

    public $file_csv;

    public function importCsv()
    {
        $this->validate([
            'file_csv' => 'required|mimes:csv,txt|max:2048',
        ]);

        $path = $this->file_csv->getRealPath();
        $data = array_map('str_getcsv', file($path));
        
        // Ambil header jika ada, atau mulai dari baris pertama
        // Asumsi CSV: Kolom 0 = Merek, Kolom 1 = Tipe/Model
        
        DB::beginTransaction();
        try {
            // Default Category (Misal: Handphone)
            $defaultCat = Category::firstOrCreate(['name' => 'Handphone']);

            foreach ($data as $index => $row) {
                if ($index === 0) continue; // Lewati header

                $brandName = strtoupper(trim($row[0]));
                $productName = trim($row[1]);

                if (!empty($brandName) && !empty($productName)) {
                    $brand = Brand::firstOrCreate(['name' => $brandName]);

                    $product = Product::firstOrCreate([
                        'name' => $productName,
                        'brand_id' => $brand->id,
                        'category_id' => $defaultCat->id
                    ]);

                    // Buat varian kosong/default agar muncul di stok
                    ProductVariant::firstOrCreate([
                        'product_id' => $product->id,
                        'attribute_name' => 'Default',
                        'stock' => 0,
                        'cost_price' => 0,
                        'srp_price' => 0
                    ]);
                }
            }
            DB::commit();
            session()->flash('success', 'Data berhasil diimport!');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal import: ' . $e->getMessage());
        }

        $this->reset('file_csv');
    }

    public function render()
    {
        return view('livewire.product.product-index', [
            'products' => Product::with(['brand', 'category', 'variants'])->latest()->get()
        ]);
    }
}