<?php

namespace App\Livewire\Product;

use App\Models\Brand;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;
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
                if ($index === 0) {
                    continue;
                }

                if (! empty($row[0]) && ! empty($row[1])) {
                    $brandUuid = trim($row[0]);
                    $namaTipe = trim($row[1]);

                    // CARI BRAND BERDASARKAN UUID
                    $brand = Brand::where('uuid', $brandUuid)->first();

                    $brandName = $brand ? $brand->name : 'ID TIDAK DITEMUKAN';
                    $brandId = $brand ? $brand->id : null;

                    $this->previewData[] = [
                        'brand_uuid' => $brandUuid,
                        'brand_id' => $brandId,     // integer ID untuk insert
                        'brand_name' => $brandName,
                        'product_name' => $namaTipe,
                        'is_valid' => $brand ? true : false,
                    ];
                }
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal membaca file: '.$e->getMessage());
        }
    }

    public function cancelImport()
    {
        $this->reset(['file_import', 'previewData']);
    }

    public function processImport()
    {
        if (empty($this->previewData)) {
            return;
        }

        DB::beginTransaction();
        try {
            // 1. Cari atau buat kategori Handphone
            $category = DB::table('categories')->where('name', 'Handphone')->first();
            if (! $category) {
                $categoryId = DB::table('categories')->insertGetId([
                    'name' => 'Handphone',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $categoryId = $category->id;
            }

            foreach ($this->previewData as $item) {
                if (! $item['is_valid']) {
                    continue;
                }

                // GUNAKAN integer brand_id
                $brandId = $item['brand_id'];

                // 2. Cek apakah produk sudah ada
                $existingProduct = DB::table('products')
                    ->where('brand_id', $brandId)
                    ->where('name', $item['product_name'])
                    ->first();

                if (! $existingProduct) {
                    // 3. Insert Produk baru
                    $productId = DB::table('products')->insertGetId([
                        'brand_id' => $brandId,       // integer
                        'brand_uuid' => $item['brand_uuid'], // simpan juga UUID untuk referensi
                        'name' => $item['product_name'],
                        'category_id' => $categoryId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // 4. Insert Varian default
                    DB::table('product_variants')->insert([
                        'product_id' => $productId,
                        'attribute_name' => 'Original',
                        'stock' => 0,
                        'cost_price' => 0,
                        'srp_price' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            DB::commit();
            session()->flash('success', count($this->previewData).' data berhasil diproses.');
            $this->reset(['file_import', 'previewData']);
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal Simpan: '.$e->getMessage());
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
            'products' => Product::with(['brand', 'category', 'variants'])->latest()->get(),
        ]);
    }
}
