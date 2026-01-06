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
    public $previewData = [];

    public function updatedFileImport()
    {
        $this->validate([
            'file_import' => 'required|mimes:csv,xls,xlsx|max:10240',
        ]);

        try {
            $path = $this->file_import->getRealPath();
            $spreadsheet = IOFactory::load($path);
            $data = $spreadsheet->getActiveSheet()->toArray();

            $this->previewData = [];
            foreach ($data as $index => $row) {
                if ($index === 0) continue; // Skip header
                
                if (!empty($row[0]) && !empty($row[1])) {
                    $brandId = trim($row[0]);
                    $namaTipe = trim($row[1]); 
                    
                    $brand = Brand::find($brandId);
                    $brandName = $brand ? $brand->name : 'ID TIDAK DITEMUKAN';

                    $this->previewData[] = [
                        'brand_id'     => $brandId,
                        'brand_name'   => $brandName,
                        'product_name' => $namaTipe,
                        'is_valid'     => $brand ? true : false
                    ];
                }
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal membaca file: ' . $e->getMessage());
        }
    }

    public function cancelImport()
    {
        $this->reset(['file_import', 'previewData']);
    }

    public function processImport()
    {
        if (empty($this->previewData)) return;

        DB::beginTransaction();
        try {
            $defaultCat = Category::firstOrCreate(['name' => 'Handphone']);

            foreach ($this->previewData as $item) {
                if (!$item['is_valid']) continue;

                $product = Product::firstOrCreate([
                    'brand_id'    => $item['brand_id'],
                    'name'        => $item['product_name'],
                    'category_id' => $defaultCat->id
                ]);

                ProductVariant::firstOrCreate([
                    'product_id'     => $product->id,
                    'attribute_name' => 'Original',
                ], [
                    'stock'      => 0,
                    'cost_price' => 0,
                    'srp_price'  => 0
                ]);
            }

            DB::commit();
            session()->flash('success', count($this->previewData) . ' Data Berhasil Disimpan.');
            $this->reset(['file_import', 'previewData']);
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Terjadi kesalahan database: ' . $e->getMessage());
        }
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