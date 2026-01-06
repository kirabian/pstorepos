<?php

namespace App\Livewire\Product;

use App\Models\Brand;
use App\Models\Category;
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

    public $products = [];

    public function mount()
    {
        $this->loadProducts();
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
                if (count($row) >= 4 && ! empty($row[0]) && ! empty($row[1]) && ! empty($row[3])) {
                    $brandUuid = trim($row[0]);     // ID_MERIK (kolom 0)
                    $namaMerk = trim($row[1]);      // NAMA_MERIK (kolom 1)
                    $namaTipe = trim($row[3]);      // NAMA_TYPE (kolom 3)

                    // Clean up data
                    $namaMerk = $this->cleanText($namaMerk);
                    $namaTipe = $this->cleanText($namaTipe);

                    // **FIX: Case-insensitive UUID matching**
                    $brand = $this->findBrand($brandUuid, $namaMerk);

                    // **FIX: Jika brand tidak ditemukan, akan dibuat otomatis di processImport**
                    $brandName = $brand ? $brand->name : $namaMerk;
                    $brandId = $brand ? $brand->id : null;

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
                        'brand_id' => $brandId,
                        'brand_name' => $namaMerk,
                        'brand_system_name' => $brand ? $brand->name : null,
                        'product_name' => $namaTipe,
                        'is_valid' => true, // **FIX: Selalu valid, akan dibuat otomatis**
                        'is_duplicate' => $isDuplicate,
                        'existing_product' => $existingProductName,
                        'ram_storage' => isset($row[4]) ? trim($row[4]) : '',
                    ];
                }
            }

            // **FIX: Tampilkan preview meski tidak ada brand di sistem**
            $totalData = count($this->previewData);
            $duplicateCount = count(array_filter($this->previewData, fn ($item) => $item['is_duplicate']));

            if ($totalData > 0) {
                session()->flash('info', "✅ {$totalData} data berhasil dibaca. ".
                    ($duplicateCount > 0 ? "{$duplicateCount} data duplikat." : 'Semua data siap diimport.'));
            }

        } catch (\Exception $e) {
            session()->flash('error', '❌ Gagal membaca file: '.$e->getMessage());
        }
    }

    // **FIX: Helper untuk mencari brand dengan berbagai cara**
    private function findBrand($uuid, $namaMerk)
    {
        // 1. Cari dengan UUID exact match
        $brand = Brand::where('uuid', $uuid)->first();
        if ($brand) {
            return $brand;
        }

        // 2. Cari dengan UUID case-insensitive
        $brand = Brand::where(DB::raw('LOWER(uuid)'), strtolower($uuid))->first();
        if ($brand) {
            return $brand;
        }

        // 3. Cari dengan nama brand
        if (! empty($namaMerk)) {
            $brand = Brand::where('name', 'like', '%'.$namaMerk.'%')
                ->orWhere(DB::raw('LOWER(name)'), 'like', '%'.strtolower($namaMerk).'%')
                ->first();
            if ($brand) {
                return $brand;
            }
        }

        return null;
    }

    // **FIX: Helper untuk clean text**
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

    // **FIX: Process Import dengan auto-create brand**
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
            if (! $category) {
                $category = Category::create([
                    'name' => 'Handphone',
                ]);
                $categoryId = $category->id;
            } else {
                $categoryId = $category->id;
            }

            $importedCount = 0;
            $skippedCount = 0;
            $createdBrands = 0;

            foreach ($this->previewData as $item) {
                // Skip jika duplikat
                if ($item['is_duplicate']) {
                    $skippedCount++;

                    continue;
                }

                // **FIX: Cari atau CREATE brand dengan cara yang benar**
                $brand = Brand::where('uuid', $item['brand_uuid'])->first();

                if (! $brand) {
                    // Coba case-insensitive
                    $brand = Brand::where(DB::raw('LOWER(uuid)'), strtolower($item['brand_uuid']))->first();

                    // Jika masih tidak ketemu, CREATE baru TANPA SET ID
                    if (! $brand) {
                        // Format UUID
                        $uuid = $this->formatUuid($item['brand_uuid']);

                        // **FIX: Create brand dengan UUID yang benar**
                        $brand = Brand::create([
                            'uuid' => $uuid,
                            'name' => $item['brand_name'],
                        ]);
                        $createdBrands++;
                    }
                }

                // Skip jika masih tidak dapat brand
                if (! $brand) {
                    $skippedCount++;

                    continue;
                }

                // **FIX: Insert Produk baru**
                $product = Product::create([
                    'brand_id' => $brand->id, // PASTIKAN ini integer ID dari brand
                    'name' => $item['product_name'],
                    'category_id' => $categoryId,
                    'description' => ! empty($item['ram_storage']) ? 'Spesifikasi: '.$item['ram_storage'] : null,
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
            session()->flash('error', '❌ Gagal Simpan: '.$e->getMessage());
            \Illuminate\Support\Facades\Log::error('Import Error: '.$e->getMessage());
            \Illuminate\Support\Facades\Log::error('Stack Trace: '.$e->getTraceAsString());
        }
    }

    // **FIX: Helper untuk format UUID yang benar**
    private function formatUuid($uuid)
    {
        // Bersihkan UUID
        $uuid = trim($uuid);
        $uuid = strtoupper($uuid);
        $uuid = str_replace(['"', "'", ' ', '™'], '', $uuid);

        // Validasi format UUID v4
        if (preg_match('/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i', $uuid)) {
            return $uuid;
        }

        // Jika tidak valid, generate UUID v4 baru
        return \Illuminate\Support\Str::uuid()->toString();
    }

    public function deleteProduct($id)
    {
        $product = Product::find($id);

        if ($product) {
            $productName = $product->name;
            $product->delete();
            $this->loadProducts();
            session()->flash('success', "✅ Produk '{$productName}' berhasil dihapus.");
        } else {
            session()->flash('error', '❌ Produk tidak ditemukan.');
        }
    }

    public function render()
    {
        return view('livewire.product.product-index', [
            'products' => $this->products,
        ]);
    }
}
