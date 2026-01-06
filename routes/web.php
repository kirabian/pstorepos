<?php

use App\Livewire\Auth\Login;
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
use App\Livewire\Merk\MerkIndex;
use App\Livewire\Tipe\TipeIndex;
use App\Livewire\User\UserCreate;
use App\Livewire\User\UserEdit;
use App\Livewire\User\UserIndex;
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
        // Hapus cache agar lampu hijau mati seketika
        \Illuminate\Support\Facades\Cache::forget('user-is-online-'.Auth::id());
    }

    Auth::logout();
    session()->invalidate();
    session()->regenerateToken();

    return redirect('/login');
})->name('logout');

/*
|--------------------------------------------------------------------------
| Area Terproteksi (Login Required)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // Semua Role bisa akses Dashboard
    Route::get('/', Dashboard::class)->name('dashboard');

    /* |--------------------------------------------------------------------------
    | AREA SUPERADMIN & ADMIN PRODUK
    |--------------------------------------------------------------------------
    */

    /* |--------------------------------------------------------------------------
    | AREA KHUSUS SUPERADMIN
    |--------------------------------------------------------------------------
    | Middleware 'can:superadmin-only' memastikan user selain superadmin
    | akan dilempar ke halaman 403 custom yang baru kita buat.
    */
    Route::middleware('can:superadmin-only')->group(function () {

        // Manajemen Distributor
        Route::prefix('distributors')->name('distributor.')->group(function () {
            Route::get('/', DistributorIndex::class)->name('index');
            Route::get('/create', DistributorCreate::class)->name('create');
            Route::get('/{id}/edit', DistributorEdit::class)->name('edit');
        });

        // Manajemen User
        Route::prefix('users')->name('user.')->group(function () {
            Route::get('/', UserIndex::class)->name('index');
            Route::get('/create', UserCreate::class)->name('create');
            Route::get('/{id}/edit', UserEdit::class)->name('edit');
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
});
