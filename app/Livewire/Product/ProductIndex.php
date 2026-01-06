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
                    continue; // Skip header
                }

                // Pastikan row memiliki cukup kolom
                if (count($row) >= 4 && ! empty($row[0]) && ! empty($row[3])) {
                    $brandUuid = trim($row[0]);     // ID_MERIK (kolom 0)
                    $namaTipe = trim($row[3]);      // NAME_TYPE (kolom 3)

                    // Hapus karakter khusus seperti ™, ", dll jika ada
                    $namaTipe = preg_replace('/[™"]/', '', $namaTipe);
                    $namaTipe = trim($namaTipe);

                    // Debug: Tampilkan UUID yang dicari
                    // \Log::info("Mencari brand dengan UUID: " . $brandUuid);

                    // CARI BRAND BERDASARKAN UUID
                    $brand = Brand::where('uuid', $brandUuid)->first();

                    // Jika tidak ketemu dengan UUID, coba dengan nama brand
                    if (! $brand && isset($row[1])) {
                        $brandName = trim($row[1]); // NAME_MERIK (kolom 1)
                        $brandName = preg_replace('/[™"]/', '', $brandName);
                        $brand = Brand::where('name', 'like', '%'.$brandName.'%')->first();
                    }

                    $brandName = $brand ? $brand->name : 'ID TIDAK DITEMUKAN';
                    $brandId = $brand ? $brand->id : null;

                    $this->previewData[] = [
                        'brand_uuid' => $brandUuid,
                        'brand_id' => $brandId,
                        'brand_name' => $brandName,
                        'product_name' => $namaTipe,
                        'is_valid' => $brand ? true : false,
                    ];
                }
            }

            // Jika semua ID Salah, mungkin perlu seeder
            $validCount = count(array_filter($this->previewData, fn ($item) => $item['is_valid']));
            if ($validCount === 0 && count($this->previewData) > 0) {
                session()->flash('warning', 'Semua brand tidak ditemukan. Pastikan sudah menjalankan BrandSeeder atau data brand sudah sesuai.');
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
