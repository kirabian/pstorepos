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

    /**
     * Otomatis jalan saat file dipilih.
     * Membaca Excel: Kolom 0 (ID Merek), Kolom 1 (Nama Tipe/Produk)
     */
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
                // Skip header (Baris 1)
                if ($index === 0) continue;
                
                // Pastikan kolom A (ID Merek) dan B (Nama Tipe) ada isinya
                if (!empty($row[0]) && !empty($row[1])) {
                    $brandId = trim($row[0]);
                    $namaTipe = trim($row[1]); // Ini yang jadi Nama Produk
                    
                    // Cari Nama Brand berdasarkan ID di database
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

    /**
     * Memproses data dari Preview ke Database
     */
    public function processImport()
    {
        if (empty($this->previewData)) return;

        DB::beginTransaction();
        try {
            // Pastikan kategori default ada
            $defaultCat = Category::firstOrCreate(['name' => 'Handphone']);

            foreach ($this->previewData as $item) {
                // Jika Brand ID tidak ada di database, lewati
                if (!$item['is_valid']) continue;

                // Simpan ke tabel Products (nama_tipe masuk ke kolom name)
                $product = Product::firstOrCreate([
                    'brand_id'    => $item['brand_id'],
                    'name'        => $item['product_name'],
                    'category_id' => $defaultCat->id
                ]);

                // Buat varian default "Original" dengan stok 0
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
            session()->flash('success', count($this->previewData) . ' Data Tipe Produk Berhasil Disimpan.');
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