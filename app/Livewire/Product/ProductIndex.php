<?php

namespace App\Livewire\Product;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Str;

class ProductIndex extends Component
{
    use WithFileUploads;
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $file_import;
    public $previewData = [];
    public $search = '';
    
    // Properti Baru untuk Filter Brand
    public $selectedBrandId = null; 

    public function updatedSearch()
    {
        $this->resetPage();
    }

    // Method untuk set filter brand saat diklik
    public function setBrandFilter($brandId)
    {
        $this->selectedBrandId = $brandId;
        $this->resetPage(); 
    }

    public function render()
    {
        $availableBrands = Brand::whereHas('products')
            ->withCount('products')
            ->orderBy('name', 'asc')
            ->get();

        // UPDATE DISINI: Tambahkan 'variants.imeis' agar data imei ikut terload
        $query = Product::with(['brand', 'category', 'variants.imeis'])
            ->select('products.*')
            ->join('brands', 'products.brand_id', '=', 'brands.id');

        if ($this->selectedBrandId) {
            $query->where('products.brand_id', $this->selectedBrandId);
        }

        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('products.name', 'like', '%' . $this->search . '%')
                  ->orWhere('brands.name', 'like', '%' . $this->search . '%');
            });
        }

        $products = $query->orderBy('brands.name', 'asc')
                          ->orderBy('products.name', 'asc')
                          ->paginate(20);

        return view('livewire.product.product-index', [
            'products' => $products,
            'availableBrands' => $availableBrands,
        ]);
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
                if ($index === 0) continue;

                if (isset($row[0], $row[1], $row[3])) {
                    $brandUuid = trim($row[0] ?? '');
                    $namaMerk = trim($row[1] ?? '');
                    $namaTipe = trim($row[3] ?? '');

                    if (empty($brandUuid) || empty($namaMerk) || empty($namaTipe)) {
                        continue;
                    }

                    $namaMerk = $this->cleanText($namaMerk);
                    $namaTipe = $this->cleanText($namaTipe);

                    $brand = $this->findBrand($brandUuid, $namaMerk);

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

                    foreach ($this->previewData as $prevItem) {
                        if ($prevItem['brand_name'] == $namaMerk && $prevItem['product_name'] == $namaTipe) {
                            $isDuplicate = true;
                            $existingProductName = "Duplikat di dalam file Excel";
                            break;
                        }
                    }

                    $this->previewData[] = [
                        'brand_uuid' => $brandUuid,
                        'brand_id' => $brand ? $brand->id : null,
                        'brand_name' => $namaMerk,
                        'brand_system_name' => $brand ? $brand->name : null,
                        'product_name' => $namaTipe,
                        'is_duplicate' => $isDuplicate,
                        'existing_product' => $existingProductName,
                        'ram_storage' => $row[4] ?? '',
                    ];
                }
            }

            $totalData = count($this->previewData);
            $duplicateCount = count(array_filter($this->previewData, fn ($item) => $item['is_duplicate']));

            if ($totalData > 0) {
                session()->flash('info', "✅ {$totalData} data terbaca. {$duplicateCount} data duplikat (skip).");
            }

        } catch (\Exception $e) {
            Log::error('File Import Error: ' . $e->getMessage());
            session()->flash('error', '❌ Gagal membaca file: ' . $e->getMessage());
        }
    }

    private function findBrand($uuid, $namaMerk)
    {
        try {
            $brand = Brand::where('uuid', $uuid)->first();
            if ($brand) return $brand;

            if (!empty($namaMerk)) {
                $brand = Brand::where('name', 'like', '%' . $namaMerk . '%')->first();
                return $brand;
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    private function cleanText($text)
    {
        $text = preg_replace('/[™"\']/', '', $text);
        return trim($text);
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
            $category = Category::firstOrCreate(['name' => 'Handphone']);
            $categoryId = $category->id;

            $importedCount = 0;
            $skippedCount = 0;
            $createdBrands = 0;

            foreach ($this->previewData as $item) {
                if ($item['is_duplicate']) {
                    $skippedCount++;
                    continue;
                }

                $brand = $this->findOrCreateBrand($item);
                
                if (!$brand) {
                    $skippedCount++;
                    continue;
                }

                if (!$item['brand_id']) $createdBrands++;

                $product = Product::create([
                    'brand_id' => $brand->id,
                    'name' => $item['product_name'],
                    'category_id' => $categoryId,
                    'description' => !empty($item['ram_storage']) ? 'Spesifikasi: ' . $item['ram_storage'] : null,
                ]);

                $product->variants()->create([
                    'attribute_name' => 'Original',
                    'stock' => 0,
                    'cost_price' => 0,
                    'srp_price' => 0,
                ]);

                $importedCount++;
            }

            DB::commit();

            $this->reset(['file_import', 'previewData']);
            $this->resetPage();

            $message = "✅ <strong>Import Selesai!</strong><br>";
            $message .= "📥 Masuk: {$importedCount} produk<br>";
            if ($skippedCount > 0) $message .= "⏭️ Skip (Duplikat): {$skippedCount} data<br>";

            session()->flash('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Process Import Error: ' . $e->getMessage());
            session()->flash('error', '❌ Gagal Simpan: ' . $e->getMessage());
        }
    }

    private function findOrCreateBrand($item)
    {
        try {
            $uuid = $item['brand_uuid'];
            $brand = Brand::where('uuid', $uuid)->first();
            
            if (!$brand) {
                $brand = Brand::where('name', $item['brand_name'])->first();
            }

            if (!$brand) {
                $cleanUuid = $this->formatUuid($uuid);
                if (Brand::where('uuid', $cleanUuid)->exists()) {
                    $cleanUuid = Str::uuid();
                }

                $brand = Brand::create([
                    'uuid' => $cleanUuid,
                    'name' => $item['brand_name']
                ]);
            }
            
            return $brand;
        } catch (\Exception $e) {
            return null;
        }
    }

    private function formatUuid($uuid)
    {
        $uuid = trim($uuid);
        $uuidPattern = '/^[0-9A-F]{8}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{12}$/i';
        if (preg_match($uuidPattern, $uuid)) {
            return strtoupper($uuid);
        }
        return Str::uuid()->toString();
    }

    public function deleteProduct($id)
    {
        try {
            $product = Product::find($id);
            if ($product) {
                $product->delete();
                session()->flash('success', "✅ Produk berhasil dihapus.");
            }
        } catch (\Exception $e) {
            session()->flash('error', '❌ Gagal menghapus produk.');
        }
    }

    public function deleteAll()
    {
        DB::beginTransaction();
        try {
            DB::table('products')->delete(); 
            DB::commit();
            
            $this->resetPage();
            session()->flash('success', "✅ SEMUA DATA PRODUK BERHASIL DIHAPUS BERSIH.");
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Delete All Error: ' . $e->getMessage());
            try {
                $ids = Product::pluck('id');
                Product::destroy($ids);
                DB::commit();
                session()->flash('success', "✅ Semua data berhasil dihapus (Metode 2).");
            } catch (\Exception $ex) {
                session()->flash('error', '❌ Gagal menghapus semua data: ' . $ex->getMessage());
            }
        }
    }
}