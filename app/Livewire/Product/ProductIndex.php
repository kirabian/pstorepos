<?php

namespace App\Livewire\Product;

use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ProductIndex extends Component
{
    use WithFileUploads;

    public $file_import;
    public $previewData = [];
    public $products = [];
    public $brands = [];
    public $categories = [];

    public function mount()
    {
        $this->loadProducts();
        $this->brands = Brand::all();
        $this->categories = Category::all();
    }

    public function loadProducts()
    {
        $this->products = Product::with(['brand', 'category', 'variants'])
            ->latest()
            ->get();
    }

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

                // Pastikan row memiliki cukup kolom dan data tidak kosong
                if (count($row) >= 4 && !empty($row[0]) && !empty($row[1]) && !empty($row[3])) {
                    $brandUuid = trim($row[0]);     // ID_MERIK (kolom 0)
                    $namaMerk = trim($row[1]);      // NAMA_MERIK (kolom 1) - untuk ditampilkan
                    $namaTipe = trim($row[3]);      // NAMA_TYPE (kolom 3) - nama produk
                    
                    // Hapus karakter khusus seperti ™, ", dll jika ada
                    $namaMerk = preg_replace('/[™"]/', '', $namaMerk);
                    $namaTipe = preg_replace('/[™"]/', '', $namaTipe);
                    $namaMerk = trim($namaMerk);
                    $namaTipe = trim($namaTipe);

                    // CARI BRAND BERDASARKAN UUID
                    $brand = Brand::where('uuid', $brandUuid)->first();
                    
                    // Jika tidak ditemukan dengan UUID, coba cari dengan nama (case insensitive)
                    if (!$brand && !empty($namaMerk)) {
                        $brand = Brand::where('name', 'like', '%' . $namaMerk . '%')
                                    ->orWhere(DB::raw('LOWER(name)'), 'like', '%' . strtolower($namaMerk) . '%')
                                    ->first();
                    }

                    // Cek apakah produk sudah ada (untuk validasi duplikat)
                    $isDuplicate = false;
                    $existingProductName = null;
                    if ($brand) {
                        $existingProduct = Product::where('brand_id', $brand->id)
                            ->where('name', $namaTipe)
                            ->first();
                        
                        if ($existingProduct) {
                            $isDuplicate = true;
                            $existingProductName = $existingProduct->name;
                        }
                    }

                    $this->previewData[] = [
                        'brand_uuid' => $brandUuid,
                        'brand_id' => $brand ? $brand->id : null,
                        'brand_name' => $namaMerk, // Tampilkan NAMA MERK dari Excel
                        'brand_system_name' => $brand ? $brand->name : null, // Nama dari sistem (jika ada)
                        'product_name' => $namaTipe,
                        'is_valid' => $brand ? true : false,
                        'is_duplicate' => $isDuplicate,
                        'existing_product' => $existingProductName,
                        'ram_storage' => isset($row[4]) ? trim($row[4]) : '', // RAM STORAGE (opsional)
                    ];
                }
            }
            
            // Hitung statistik
            $totalData = count($this->previewData);
            $validCount = count(array_filter($this->previewData, fn($item) => $item['is_valid']));
            $duplicateCount = count(array_filter($this->previewData, fn($item) => $item['is_duplicate']));
            
            if ($validCount === 0 && $totalData > 0) {
                session()->flash('warning', '⚠️ Tidak ada brand yang valid ditemukan. Pastikan data brand sudah sesuai dengan sistem.');
            } elseif ($duplicateCount > 0) {
                session()->flash('info', 'ℹ️ Ditemukan ' . $duplicateCount . ' data duplikat yang akan dilewati.');
            }
            
        } catch (\Exception $e) {
            session()->flash('error', '❌ Gagal membaca file: ' . $e->getMessage());
        }
    }

    public function cancelImport()
    {
        $this->reset(['file_import', 'previewData']);
        session()->flash('info', 'Import dibatalkan.');
    }

    public function processImport()
    {
        if (empty($this->previewData)) {
            session()->flash('error', '❌ Tidak ada data untuk diimport.');
            return;
        }

        DB::beginTransaction();
        try {
            // 1. Cari atau buat kategori Handphone
            $category = Category::where('name', 'Handphone')->first();
            if (!$category) {
                $category = Category::create([
                    'name' => 'Handphone',
                ]);
                $categoryId = $category->id;
            } else {
                $categoryId = $category->id;
            }

            $importedCount = 0;
            $skippedCount = 0;
            
            foreach ($this->previewData as $item) {
                if (!$item['is_valid']) {
                    $skippedCount++;
                    continue;
                }
                
                if ($item['is_duplicate']) {
                    $skippedCount++;
                    continue;
                }

                // Gunakan integer brand_id
                $brandId = $item['brand_id'];

                // 2. Insert Produk baru menggunakan Model (bukan DB::table)
                $product = Product::create([
                    'brand_id' => $brandId,
                    'name' => $item['product_name'],
                    'category_id' => $categoryId,
                    'description' => isset($item['ram_storage']) ? 'Spesifikasi: ' . $item['ram_storage'] : null,
                ]);

                // 3. Insert Varian default
                $product->variants()->create([
                    'attribute_name' => 'Original',
                    'stock' => 0,
                    'cost_price' => 0,
                    'srp_price' => 0,
                ]);

                $importedCount++;
            }

            DB::commit();
            
            // Refresh data produk
            $this->loadProducts();
            
            $message = "✅ Import berhasil! ";
            $message .= "{$importedCount} data baru ditambahkan. ";
            if ($skippedCount > 0) {
                $message .= "{$skippedCount} data dilewati (invalid/duplikat).";
            }
            
            session()->flash('success', $message);
            $this->reset(['file_import', 'previewData']);
            
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', '❌ Gagal Simpan: ' . $e->getMessage());
        }
    }

    public function deleteProduct($id)
    {
        $product = Product::find($id);
        
        if ($product) {
            $productName = $product->name;
            $product->delete();
            $this->loadProducts(); // Refresh data
            session()->flash('success', "✅ Produk '{$productName}' berhasil dihapus.");
        } else {
            session()->flash('error', '❌ Produk tidak ditemukan.');
        }
    }

    public function render()
    {
        return view('livewire.product.product-index', [
            'products' => $this->products,
            'brands' => $this->brands,
            'categories' => $this->categories,
        ]);
    }
}