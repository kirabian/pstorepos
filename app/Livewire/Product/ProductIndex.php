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
    public $previewData = []; // Menampung data pratinjau

    // Fungsi ini otomatis jalan saat file dipilih
    public function updatedFileImport()
    {
        $this->validate([
            'file_import' => 'required|mimes:csv,xls,xlsx|max:10240',
        ]);

        $path = $this->file_import->getRealPath();
        $spreadsheet = IOFactory::load($path);
        $data = $spreadsheet->getActiveSheet()->toArray();

        // Ambil data (skip header baris pertama)
        $this->previewData = [];
        foreach ($data as $index => $row) {
            if ($index === 0 || empty($row[0])) continue;
            $this->previewData[] = [
                'brand' => strtoupper(trim($row[0])),
                'name'  => trim($row[1]),
            ];
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
                $brand = Brand::firstOrCreate(['name' => $item['brand']]);
                
                $product = Product::firstOrCreate([
                    'name' => $item['name'],
                    'brand_id' => $brand->id,
                    'category_id' => $defaultCat->id
                ]);

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
            session()->flash('success', count($this->previewData) . ' Data berhasil dimasukkan ke sistem.');
            $this->reset(['file_import', 'previewData']);
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
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