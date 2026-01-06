<?php

namespace App\Livewire\Product;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Str;

class ProductIndex extends Component
{
    use WithFileUploads;

    public $file_import;
    public $previewData = [];
    public $products = [];

    public function mount()
    {
        $this->loadProducts();
    }

    public function loadProducts()
    {
        try {
            $this->products = Product::with(['brand', 'category', 'variants'])
                ->latest()
                ->get();
        } catch (\Exception $e) {
            Log::error('Load Products Error: ' . $e->getMessage());
            $this->products = [];
        }
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

                // Pastikan row memiliki cukup kolom
                if (isset($row[0], $row[1], $row[3])) {
                    $brandUuid = trim($row[0] ?? '');
                    $namaMerk = trim($row[1] ?? '');
                    $namaTipe = trim($row[3] ?? '');

                    if (empty($brandUuid) || empty($namaMerk) || empty($namaTipe)) {
                        continue;
                    }

                    // Clean up data
                    $namaMerk = $this->cleanText($namaMerk);
                    $namaTipe = $this->cleanText($namaTipe);

                    // Cari brand
                    $brand = $this->findBrand($brandUuid, $namaMerk);

                    // Cek duplikat produk
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
                        'brand_name' => $namaMerk,
                        'brand_system_name' => $brand ? $brand->name : null,
                        'product_name' => $namaTipe,
                        'is_valid' => true,
                        'is_duplicate' => $isDuplicate,
                        'existing_product' => $existingProductName,
                        'ram_storage' => $row[4] ?? '',
                    ];
                }
            }

            $totalData = count($this->previewData);
            $duplicateCount = count(array_filter($this->previewData, fn ($item) => $item['is_duplicate']));

            if ($totalData > 0) {
                session()->flash('info', "✅ {$totalData} data berhasil dibaca. " .
                    ($duplicateCount > 0 ? "{$duplicateCount} data duplikat." : 'Semua data siap diimport.'));
            }

        } catch (\Exception $e) {
            Log::error('File Import Error: ' . $e->getMessage());
            session()->flash('error', '❌ Gagal membaca file: ' . $e->getMessage());
        }
    }

    private function findBrand($uuid, $namaMerk)
    {
        try {
            // Cari dengan UUID exact match
            $brand = Brand::where('uuid', $uuid)->first();
            if ($brand) {
                return $brand;
            }

            // Cari dengan UUID case-insensitive
            $brand = Brand::where(DB::raw('BINARY uuid'), $uuid)
                        ->orWhere(DB::raw('LOWER(uuid)'), strtolower($uuid))
                        ->first();
            if ($brand) {
                return $brand;
            }

            // Cari dengan nama brand
            if (!empty($namaMerk)) {
                $brand = Brand::where('name', 'like', '%' . $namaMerk . '%')
                    ->orWhere(DB::raw('LOWER(name)'), 'like', '%' . strtolower($namaMerk) . '%')
                    ->first();
                return $brand;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Find Brand Error: ' . $e->getMessage());
            return null;
        }
    }

    private function cleanText($text)
    {
        $text = preg_replace('/[™"\']/', '', $text);
        $text = trim($text);
        return $text;
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
            // Enable query logging untuk debug
            DB::enableQueryLog();

            // 1. Cari atau buat kategori Handphone
            $category = Category::where('name', 'Handphone')->first();
            if (!$category) {
                $category = Category::create([
                    'name' => 'Handphone',
                ]);
                Log::info('Kategori Handphone dibuat: ' . $category->id);
            }
            $categoryId = $category->id;

            $importedCount = 0;
            $skippedCount = 0;
            $createdBrands = 0;

            foreach ($this->previewData as $item) {
                // Skip jika duplikat
                if ($item['is_duplicate']) {
                    $skippedCount++;
                    continue;
                }

                // Cari atau CREATE brand
                $brand = $this->findOrCreateBrand($item);
                
                if (!$brand) {
                    Log::warning('Brand tidak ditemukan/dibuat untuk: ' . $item['brand_uuid']);
                    $skippedCount++;
                    continue;
                }

                $createdBrands += ($item['brand_id'] ? 0 : 1);

                // Insert Produk
                $product = Product::create([
                    'brand_id' => $brand->id,
                    'name' => $item['product_name'],
                    'category_id' => $categoryId,
                    'description' => !empty($item['ram_storage']) ? 'Spesifikasi: ' . $item['ram_storage'] : null,
                ]);

                // Insert Varian default
                $product->variants()->create([
                    'attribute_name' => 'Original',
                    'stock' => 0,
                    'cost_price' => 0,
                    'srp_price' => 0,
                ]);

                $importedCount++;
            }

            DB::commit();

            // Log queries untuk debug
            $queries = DB::getQueryLog();
            Log::info('Import queries: ' . json_encode($queries));

            // Refresh data produk
            $this->loadProducts();

            // Pesan sukses
            $message = '✅ <strong>Import Berhasil!</strong><br>';
            $message .= "📦 <strong>{$importedCount}</strong> produk baru ditambahkan<br>";

            if ($createdBrands > 0) {
                $message .= "🏷️ <strong>{$createdBrands}</strong> brand baru dibuat<br>";
            }

            if ($skippedCount > 0) {
                $message .= "⏭️ <strong>{$skippedCount}</strong> data dilewati<br>";
            }

            session()->flash('success', $message);
            $this->reset(['file_import', 'previewData']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Process Import Error: ' . $e->getMessage());
            Log::error('Stack Trace: ' . $e->getTraceAsString());
            
            session()->flash('error', '❌ Gagal Simpan: ' . $e->getMessage() . 
                ' (Lihat log untuk detail)');
        } finally {
            DB::disableQueryLog();
        }
    }

    private function findOrCreateBrand($item)
    {
        try {
            // Cari brand yang sudah ada
            $brand = Brand::where('uuid', $item['brand_uuid'])->first();
            
            if (!$brand) {
                // Coba case-insensitive
                $brand = Brand::where(DB::raw('LOWER(uuid)'), strtolower($item['brand_uuid']))->first();
                
                // Jika masih tidak ketemu, CREATE baru
                if (!$brand) {
                    // Format UUID
                    $uuid = $this->formatUuid($item['brand_uuid']);
                    
                    // Cek dulu apakah UUID sudah ada
                    $existingBrand = Brand::where('uuid', $uuid)->first();
                    if ($existingBrand) {
                        return $existingBrand;
                    }
                    
                    // Buat brand baru
                    $brand = Brand::create([
                        'uuid' => $uuid,
                        'name' => $item['brand_name']
                    ]);
                    
                    Log::info('Brand baru dibuat: ' . $brand->id . ' - ' . $brand->name . ' - ' . $brand->uuid);
                }
            }
            
            return $brand;
            
        } catch (\Exception $e) {
            Log::error('Find or Create Brand Error: ' . $e->getMessage());
            return null;
        }
    }

    private function formatUuid($uuid)
    {
        $uuid = trim($uuid);
        $uuid = strtoupper($uuid);
        $uuid = str_replace(['"', "'", ' ', '™', '`'], '', $uuid);
        
        // Validasi format UUID
        $uuidPattern = '/^[0-9A-F]{8}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{12}$/i';
        
        if (preg_match($uuidPattern, $uuid)) {
            return $uuid;
        }
        
        // Jika tidak valid, generate baru
        return Str::uuid()->toString();
    }

    public function deleteProduct($id)
    {
        try {
            $product = Product::find($id);
            
            if ($product) {
                $productName = $product->name;
                $product->delete();
                $this->loadProducts();
                session()->flash('success', "✅ Produk '{$productName}' berhasil dihapus.");
            } else {
                session()->flash('error', '❌ Produk tidak ditemukan.');
            }
        } catch (\Exception $e) {
            Log::error('Delete Product Error: ' . $e->getMessage());
            session()->flash('error', '❌ Gagal menghapus produk.');
        }
    }

    public function render()
    {
        return view('livewire.product.product-index', [
            'products' => $this->products,
        ]);
    }
}