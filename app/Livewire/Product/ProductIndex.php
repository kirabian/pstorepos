<?php

namespace App\Livewire\Product;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ProductIndex extends Component
{
    use WithFileUploads;

    public $file_import;

    public function importFile()
    {
        $this->validate([
            'file_import' => 'required|mimes:csv,xls,xlsx|max:10240', // Max 10MB
        ]);

        $path = $this->file_import->getRealPath();
        
        try {
            // Load file menggunakan PhpSpreadsheet agar bisa baca XLSX & CSV
            $spreadsheet = IOFactory::load($path);
            $data = $spreadsheet->getActiveSheet()->toArray();

            DB::beginTransaction();
            
            // Ambil atau buat kategori default
            $defaultCat = Category::firstOrCreate(['name' => 'Handphone']);

            foreach ($data as $index => $row) {
                // Skip header (Baris 1) dan pastikan kolom Merek & Tipe tidak kosong
                if ($index === 0 || empty($row[0]) || empty($row[1])) continue; 

                $brandName = strtoupper(trim($row[0]));
                $productName = trim($row[1]);

                // 1. Cari atau Buat Brand
                $brand = Brand::firstOrCreate(['name' => $brandName]);

                // 2. Cari atau Buat Produk
                $product = Product::firstOrCreate([
                    'name' => $productName,
                    'brand_id' => $brand->id,
                    'category_id' => $defaultCat->id
                ]);

                // 3. Buat Varian Default jika belum ada
                ProductVariant::firstOrCreate([
                    'product_id' => $product->id,
                    'attribute_name' => 'Original',
                ], [
                    'stock' => 0,
                    'cost_price' => 0,
                    'srp_price' => 0
                ]);
            }

            DB::commit();
            session()->flash('success', 'Berhasil mengimport ' . (count($data) - 1) . ' data produk.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal memproses file: ' . $e->getMessage());
        }

        $this->reset('file_import');
    }

    public function deleteProduct($id)
    {
        Product::find($id)->delete();
        session()->flash('success', 'Produk berhasil dihapus.');
    }

    public function render()
    {
        return view('livewire.product.product-index', [
            'products' => Product::with(['brand', 'category', 'variants'])->latest()->get()
        ]);
    }
}