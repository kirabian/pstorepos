<?php

use App\Livewire\Auth\Login;
use App\Livewire\BarangKeluar\BarangKeluarIndex;
use App\Livewire\BarangKeluar\BarangKeluarCreate;
use App\Livewire\BarangMasuk\BarangMasukIndex;
use App\Livewire\BarangMasuk\BarangMasukCreate;
use App\Livewire\Cabang\CabangCreate;
use App\Livewire\Cabang\CabangEdit;
use App\Livewire\Cabang\CabangIndex;
use App\Livewire\Dashboard;
use App\Livewire\Distributor\DistributorCreate;
use App\Livewire\Distributor\DistributorEdit;
use App\Livewire\Distributor\DistributorIndex;
use App\Livewire\Gudang\GudangCreate;
use App\Livewire\Gudang\GudangEdit;
use App\Livewire\Gudang\GudangIndex;
use App\Livewire\Lacak\LacakImei;
use App\Livewire\Merk\MerkIndex;
use App\Livewire\OnlineShop\OnlineShopIndex;
use App\Livewire\Stok\StokIndex;
use App\Livewire\Tipe\TipeIndex;
use App\Livewire\User\UserCreate;
use App\Livewire\User\UserEdit;
use App\Livewire\User\UserIndex;
// IMPORT LIVEWIRE BARU KHUSUS DISTRIBUTOR
use App\Livewire\Distributor\StokCabangIndex;
use App\Livewire\Distributor\SimulasiPembagianIndex;
use App\Livewire\Distributor\OmsetCabangIndex;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Autentikasi Publik (Guest)
|--------------------------------------------------------------------------
*/
Route::get('/login', Login::class)->name('login')->middleware('guest');

Route::post('/logout', function () {
    if (Auth::check()) {
        \Illuminate\Support\Facades\Cache::forget('user-is-online-'.Auth::id());
    }

    Auth::logout();
    session()->invalidate();
    session()->regenerateToken();

    return redirect('/login');
})->name('logout');

/*
|--------------------------------------------------------------------------
| Area Terproteksi (Login Required & Akun Aktif)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'active.user'])->group(function () {

    // Dashboard (Semua Role)
    Route::get('/', Dashboard::class)->name('dashboard');

    /* |--------------------------------------------------------------------------
    | MANAJEMEN USER (SUPERADMIN & AUDIT)
    |--------------------------------------------------------------------------
    */
    Route::prefix('users')->name('user.')->middleware('user.management')->group(function () {
        Route::get('/', UserIndex::class)->name('index');
        Route::get('/create', UserCreate::class)->name('create');
        Route::get('/{id}/edit', UserEdit::class)->name('edit');
    });

    /* |--------------------------------------------------------------------------
    | AREA KHUSUS SUPERADMIN
    |--------------------------------------------------------------------------
    */
    Route::middleware('can:superadmin-only')->group(function () {
        Route::get('/online-shops', OnlineShopIndex::class)->name('online-shop.index');

        Route::prefix('distributors')->name('distributor.')->group(function () {
            Route::get('/', DistributorIndex::class)->name('index');
            Route::get('/create', DistributorCreate::class)->name('create');
            Route::get('/{id}/edit', DistributorEdit::class)->name('edit');
        });

        Route::get('/cabang', CabangIndex::class)->name('cabang.index');
        Route::get('/cabang/create', CabangCreate::class)->name('cabang.create');
        Route::get('/cabang/{id}/edit', CabangEdit::class)->name('cabang.edit');

        Route::get('/gudang', GudangIndex::class)->name('gudang.index');
        Route::get('/gudang/create', GudangCreate::class)->name('gudang.create');
        Route::get('/gudang/{id}/edit', GudangEdit::class)->name('gudang.edit');

        Route::get('/merk', MerkIndex::class)->name('merk.index');
        Route::get('/tipe', TipeIndex::class)->name('tipe.index');
    });

    /* |--------------------------------------------------------------------------
    | OPERASIONAL UMUM
    |--------------------------------------------------------------------------
    */
    Route::get('/stok', StokIndex::class)->name('stok.index');
    Route::get('/lacak-imei', LacakImei::class)->name('lacak.imei');

    Route::get('/barang-masuk', BarangMasukIndex::class)->name('barang-masuk.index');
    Route::get('/barang-masuk/create', BarangMasukCreate::class)->name('barang-masuk.create');

    Route::get('/barang-keluar', BarangKeluarIndex::class)->name('barang-keluar.index');
    Route::get('/barang-keluar/create', BarangKeluarCreate::class)->name('barang-keluar.create');

    Route::get('/stock-opname', \App\Livewire\Gudang\StockOpnameIndex::class)
        ->name('stock-opname.index')
        ->middleware('checkRole:gudang');

    /* |--------------------------------------------------------------------------
    | FITUR KHUSUS DISTRIBUTOR (INVENTORY STAFF & OWNER)
    |--------------------------------------------------------------------------
    | 1. Lihat Stok Cabang
    | 2. Simulasi Pembagian
    | 3. Lihat Omset Cabang
    */
    Route::prefix('distributor-ops')->name('distributor-ops.')->group(function () {
        Route::get('/stok-cabang', StokCabangIndex::class)->name('stok-cabang');
        Route::get('/simulasi-pembagian', SimulasiPembagianIndex::class)->name('simulasi');
        Route::get('/omset-cabang', OmsetCabangIndex::class)->name('omset-cabang');
    });
});