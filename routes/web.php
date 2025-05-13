<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PenitipController;
use App\Http\Controllers\PembeliController;
use App\Http\Controllers\AlamatController;
use App\Http\Controllers\OrganisasiController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\MerchandiseController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KeranjangController;
use App\Models\Barang;

Route::get('/', function () {
    $barangTerbatas = Barang::with('gambar')->take(12)->get();
    return view('welcome', [
        'role' => session('role'),
        'user' => session('user'),
        'barangTerbatas' => $barangTerbatas
    ]);
})->name('welcome');

Route::get('/about', function () {return view('about');})->name('about');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', function () {session()->flush();return redirect('/');})->name('logout');
Route::post('/admin/logout', function () {
    session()->flush();
    return redirect('/login');
})->name('admin.logout');
  
Route::post('/pembeli', [PembeliController::class, 'store']);
Route::post('/organisasi', [OrganisasiController::class, 'store']);
Route::get('/keranjang', [\App\Http\Controllers\KeranjangController::class, 'index'])->name('cart');
Route::get('/produk/allProduct', [BarangController::class, 'allProduct'])->name('produk.allproduct'); 

    // =================== PENITIP ROUTES ===================
Route::prefix('penitip')->group(function () {
    Route::get('/profile', [PenitipController::class, 'profile'])->name('penitip.profile');
    Route::get('/{id}/edit', [PenitipController::class, 'editProfile'])->name('penitip.edit');
    Route::put('/{id}/update', [PenitipController::class, 'updateProfile'])->name('penitip.update');
    Route::get('/reward', [PenitipController::class, 'rewards'])->name('penitip.rewards');
    Route::get('/product', [PenitipController::class, 'product'])->name('penitip.product');
    Route::get('/myproduct', [PenitipController::class, 'myproduct'])->name('penitip.myproduct');
    Route::get('/transaction', [PenitipController::class, 'transaction'])->name('penitip.transaction');
    Route::get('/transaction/filter/{type}', [PenitipController::class, 'filterTransaction'])->name('penitip.transaction.filter');
});

Route::prefix('pembeli')->group(function () {
    Route::get('/profile', [PembeliController::class, 'profile'])->name('pembeli.profile');
    Route::get('/{id}/edit', [PembeliController::class, 'editProfile'])->name('pembeli.edit');
    Route::put('/{id}/update', [PembeliController::class, 'updateProfile'])->name('pembeli.update');
    Route::get('/purchase', [PembeliController::class, 'purchase'])->name('pembeli.purchase');
    Route::get('/reward', [PembeliController::class, 'reward'])->name('pembeli.reward');
    Route::get('/password', function () {return view('password');})->name('pembeli.password');

    Route::get('/alamat', [AlamatController::class, 'alamatPembeli'])->name('pembeli.alamat');
    Route::post('/alamat', [AlamatController::class, 'store'])->name('pembeli.alamat.store');
    Route::put('/alamat/{id}', [AlamatController::class, 'update'])->name('pembeli.alamat.update');
    Route::delete('/alamat/{id}', [AlamatController::class, 'destroy'])->name('pembeli.alamat.destroy');
});

// Admin routes
Route::prefix('admin')->group(function () {
    //Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');

    // EMPLOYEES
    Route::get('/employees', [PegawaiController::class, 'indexView'])->name('admin.employees.index');
    Route::get('/employees/add', [PegawaiController::class, 'create'])->name('admin.employees.create');
    Route::get('/employees/search', [PegawaiController::class, 'search'])->name('admin.employees.search');
    Route::post('/employees', [PegawaiController::class, 'store'])->name('admin.employees.store');
    Route::get('/employees/{id}/edit', [PegawaiController::class, 'edit'])->name('admin.employees.edit');
    Route::put('/employees/{id}', [PegawaiController::class, 'update'])->name('admin.employees.update');
    Route::put('/employees/{id}/reset-password', [PegawaiController::class, 'resetPassword'])->name('admin.employees.reset-password');
    Route::put('/employees/{id}/deactivate', [PegawaiController::class, 'deactivate'])->name('admin.employees.deactivate');
    Route::put('/employees/{id}/reactivate', [PegawaiController::class, 'reactivate'])->name('admin.employees.reactivate');

    // ROLES
    Route::get('/roles', [RoleController::class, 'index'])->name('admin.roles.index');
    Route::get('/roles/add', [RoleController::class, 'create'])->name('admin.roles.create');
    Route::post('/roles', [RoleController::class, 'store'])->name('admin.roles.store');
    Route::get('/roles/{id}/edit', [RoleController::class, 'edit'])->name('admin.roles.edit');
    Route::put('/roles/{id}', [RoleController::class, 'update'])->name('admin.roles.update');
    Route::put('/roles/{id}/deactivate', [RoleController::class, 'deactivate'])->name('admin.roles.deactivate');
    Route::put('/roles/{id}/reactivate', [RoleController::class, 'reactivate'])->name('admin.roles.reactivate');
    Route::get('/roles/search', [RoleController::class, 'search'])->name('admin.roles.search');

    // ITEM OWNERS
    Route::get('/item-owners', [PenitipController::class, 'index'])->name('admin.penitip.index');
    Route::get('/item-owners/add', [PenitipController::class, 'create'])->name('admin.penitip.create');
    Route::post('/item-owners', [PenitipController::class, 'store'])->name('admin.penitip.store');
    Route::get('/item-owners/search', [PenitipController::class, 'search'])->name('admin.penitip.search');
    Route::get('/item-owners/{id}/edit', [PenitipController::class, 'edit'])->name('admin.penitip.edit');
    Route::put('/item-owners/{id}', [PenitipController::class, 'update'])->name('admin.penitip.update');
    Route::put('/item-owners/{id}/deactivate', [PenitipController::class, 'deactivate'])->name('admin.penitip.deactivate');
    Route::put('/item-owners/{id}/reactivate', [PenitipController::class, 'reactivate'])->name('admin.penitip.reactivate');

    // CUSTOMERS
    Route::get('/customers', [PembeliController::class, 'index'])->name('admin.pembeli.index');
    Route::get('/customers/add', [PembeliController::class, 'create'])->name('admin.pembeli.create');
    Route::post('/customers', [PembeliController::class, 'store'])->name('admin.pembeli.store');
    Route::get('/customers/search', [PembeliController::class, 'search'])->name('admin.pembeli.search');
    Route::get('/customers/{id}/edit', [PembeliController::class, 'edit'])->name('admin.pembeli.edit');
    Route::put('/customers/{id}', [PembeliController::class, 'update'])->name('admin.pembeli.update');
    Route::put('/customers/{id}/deactivate', [PembeliController::class, 'deactivate'])->name('admin.pembeli.deactivate');
    Route::put('/customers/{id}/reactivate', [PembeliController::class, 'reactivate'])->name('admin.pembeli.reactivate');

    // ORGANIZATIONS
    Route::get('/organizations', [OrganisasiController::class, 'index'])->name('admin.organisasi.index');
    Route::get('/organizations/add', [OrganisasiController::class, 'create'])->name('admin.organisasi.create');
    Route::post('/organizations', [OrganisasiController::class, 'store'])->name('admin.organisasi.store');
    Route::get('/organizations/search', [OrganisasiController::class, 'search'])->name('admin.organisasi.search');
    Route::get('/organizations/{id}/edit', [OrganisasiController::class, 'edit'])->name('admin.organisasi.edit');
    Route::put('/organizations/{id}', [OrganisasiController::class, 'update'])->name('admin.organisasi.update');
    Route::put('/organizations/{id}/deactivate', [OrganisasiController::class, 'deactivate'])->name('admin.organisasi.deactivate');
    Route::put('/organizations/{id}/reactivate', [OrganisasiController::class, 'reactivate'])->name('admin.organisasi.reactivate');

    // PRODUCTS
    Route::get('/products', [BarangController::class, 'index'])->name('admin.produk.index');
    Route::get('/products/add', [BarangController::class, 'create'])->name('admin.produk.create');
    Route::post('/products', [BarangController::class, 'store'])->name('admin.produk.store');
    Route::get('/products/search', [BarangController::class, 'search'])->name('admin.produk.search');
    Route::get('/products/{id}/edit', [BarangController::class, 'edit'])->name('admin.produk.edit');
    Route::put('/products/{id}', [BarangController::class, 'update'])->name('admin.produk.update');
    Route::put('/products/{id}/deactivate', [BarangController::class, 'deactivate'])->name('admin.produk.deactivate');
    Route::put('/products/{id}/reactivate', [BarangController::class, 'reactivate'])->name('admin.produk.reactivate');

    // MERCHANDISE
    Route::get('/merchandise', [MerchandiseController::class, 'index'])->name('admin.merch.index');
    Route::get('/merchandise/add', [MerchandiseController::class, 'create'])->name('admin.merchandise.create');
    Route::post('/merchandise', [MerchandiseController::class, 'store'])->name('admin.merchandise.store');
    Route::get('/merchandise/search', [MerchandiseController::class, 'search'])->name('admin.merchandise.search');
    Route::get('/merchandise/{id}/edit', [MerchandiseController::class, 'edit'])->name('admin.merchandise.edit');
    Route::put('/merchandise/{id}', [MerchandiseController::class, 'update'])->name('admin.merchandise.update');
});
