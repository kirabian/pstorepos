<?php

namespace App\Livewire\Product;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination; // Penting untuk mencegah lag
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Str;

class ProductIndex extends Component
{
    use WithFileUploads;
    use WithPagination; // Menggunakan trait Pagination

    protected $paginationTheme = 'bootstrap'; // Tema pagination

    public $file_import;
    public $previewData = [];
    public $search = ''; // Variabel pencarian

    // Reset pagination jika search berubah
    public function updatedSearch()
    {
        $this->resetPage();
    }

    // Fungsi Render Utama (Dioptimalkan)
    public function render()
    {
        // Query Produk
        $query = Product::with(['brand', 'category', 'variants'])
            ->select('products.*') // Pastikan select table products agar id tidak ambigu
            ->join('brands', 'products.brand_id', '=', 'brands.id'); // Join untuk sorting by brand name

        // Filter Pencarian
        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('products.name', 'like', '%' . $this->search . '%')
                  ->orWhere('brands.name', 'like', '%' . $this->search . '%');
            });
        }

        // Sorting: Nama Brand (A-Z) -> Nama Produk (A-Z)
        $products = $query->orderBy('brands.name', 'asc')
                          ->orderBy('products.name', 'asc')
                          ->paginate(20); // Load 20 data per halaman (Mencegah Lag)

        return view('livewire.product.product-index', [
            'products' => $products,
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
                if ($index === 0) {
                    continue; // Skip header
                }

                // Pastikan row memiliki cukup kolom (Sesuaikan index dengan file excel Anda)
                // Asumsi: [0]=UUID, [1]=NamaMerk, [2]=..., [3]=NamaTipe
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

                    // Cari brand untuk cek duplikat
                    $brand = $this->findBrand($brandUuid, $namaMerk);

                    // Cek duplikat produk di database
                    $isDuplicate = false;
                    $existingProductName = null;
                    
                    if ($brand) {
                        // Cek apakah produk dengan nama ini sudah ada di brand ini
                        $existingProduct = Product::where('brand_id', $brand->id)
                            ->where('name', $namaTipe)
                            ->first();

                        if ($existingProduct) {
                            $isDuplicate = true;
                            $existingProductName = $existingProduct->name;
                        }
                    }

                    // Cek duplikat di dalam file import itu sendiri (biar gak double entry di 1 file)
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
                session()->flash('info', "✅ {$totalData} data terbaca. {$duplicateCount} data terdeteksi duplikat dan akan dilewati.");
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
            if ($brand) return $brand;

            // Cari dengan nama brand
            if (!empty($namaMerk)) {
                $brand = Brand::where('name', 'like', '%' . $namaMerk . '%')
                    ->first();
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
            // 1. Cari atau buat kategori Handphone
            $category = Category::firstOrCreate(['name' => 'Handphone']);
            $categoryId = $category->id;

            $importedCount = 0;
            $skippedCount = 0;
            $createdBrands = 0;

            foreach ($this->previewData as $item) {
                // SKIP jika statusnya duplikat
                if ($item['is_duplicate']) {
                    $skippedCount++;
                    continue;
                }

                // Cari atau CREATE brand
                $brand = $this->findOrCreateBrand($item);
                
                if (!$brand) {
                    $skippedCount++;
                    continue;
                }

                // Hitung jika brand baru (hanya estimasi logic sederhana)
                if (!$item['brand_id']) $createdBrands++;

                // Insert Produk Baru
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

            // Reset UI
            $this->reset(['file_import', 'previewData']);
            $this->resetPage(); // Kembali ke halaman 1

            // Pesan sukses
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
            
            // Coba cari by UUID
            $brand = Brand::where('uuid', $uuid)->first();
            
            // Jika tidak ada UUID, cari by Name
            if (!$brand) {
                $brand = Brand::where('name', $item['brand_name'])->first();
            }

            // Jika masih tidak ada, Buat Baru
            if (!$brand) {
                $cleanUuid = $this->formatUuid($uuid);
                // Cek lagi collision UUID
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

    // FUNGSI BARU: HAPUS SEMUA DATA
    public function deleteAll()
    {
        DB::beginTransaction();
        try {
            // Hapus semua produk (Cascade ke variant biasanya otomatis via DB, 
            // tapi kita bisa gunakan model delete agar event fired)
            
            // Menggunakan query builder delete untuk performa cepat jika data ribuan
            // Hati-hati: Pastikan foreign key di database diset ON DELETE CASCADE untuk variants
            // Jika tidak, harus loop delete atau delete variants dulu
            
            // Cara aman via Eloquent (mungkin lambat jika data 10rb+)
            // Product::query()->delete(); 

            // Cara Cepat (Asumsi Foreign Key Cascade sudah diset di migration)
            DB::table('products')->delete(); 
            // Atau truncate jika ingin reset ID: Product::truncate(); (Hati-hati foreign key constraint)

            DB::commit();
            
            $this->resetPage();
            session()->flash('success', "✅ SEMUA DATA PRODUK BERHASIL DIHAPUS BERSIH.");
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Delete All Error: ' . $e->getMessage());
            // Jika gagal (biasanya karena FK constraint), coba manual
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