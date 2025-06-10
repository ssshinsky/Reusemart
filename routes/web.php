<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
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
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\RequestDonasiController;
use App\Http\Controllers\DonasiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KeranjangController;
use App\Http\Controllers\ItemKeranjangController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\TransaksiPenitipanController;
use App\Http\Controllers\TransaksiPembelianController;
use App\Http\Controllers\TransaksiMerchandiseController;
use App\Http\Controllers\DiskusiProdukController;
use App\Models\Barang;

Route::get('/', function () {
    $barangTerbatas = Barang::with('gambar')->take(12)->get();
    return view('welcome', [
        'role' => session('role'),
        'user' => session('user'),
        'barangTerbatas' => $barangTerbatas
    ]);
})->name('welcome');

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/login', function () {
    return redirect('/');
})->name('login');

Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout.submit');
Route::post('/admin/logout', function () {
    session()->flush();
    return redirect('/login');
})->name('admin.logout');

// Public Routes
Route::post('/pembeli', [PembeliController::class, 'store'])->name('pembeli.store');
Route::post('/organisasi', [OrganisasiController::class, 'store'])->name('organisasi.store');
Route::get('/produk/allProduct', [BarangController::class, 'allProduct'])->name('produk.allproduct');
Route::get('/barang/{id}', [BarangController::class, 'show'])->name('umum.show');
Route::post('/diskusi/store', [DiskusiProdukController::class, 'store'])->name('diskusi.store')->middleware('auth:pembeli');

// Cart Routes
Route::post('/keranjang/tambah/{id}', [ItemKeranjangController::class, 'tambah'])->name('cart.add');
Route::delete('/keranjang/hapus/{id}', [ItemKeranjangController::class, 'hapus'])->name('cart.remove');
Route::post('/keranjang/toggle/{id}', [ItemKeranjangController::class, 'toggleSelect'])->name('cart.toggle');
Route::post('/keranjang/checkout', [ItemKeranjangController::class, 'checkout'])->name('cart.checkout');

// Reset Password Routes
Route::get('/reset-password', [ResetPasswordController::class, 'showEmailForm'])->name('password.reset');
Route::post('/password/send-code', [ResetPasswordController::class, 'sendCode'])->name('password.sendCode');
Route::post('/password/verify-code', [ResetPasswordController::class, 'verifyCode'])->name('password.verifyCode');
Route::post('/password/update', [ResetPasswordController::class, 'updatePassword'])->name('password.update');

// Penitip Routes
Route::prefix('penitip')->middleware('auth:penitip')->group(function () {
    Route::get('/profile', [PenitipController::class, 'profile'])->name('penitip.profile');
    Route::get('/{id}/edit', [PenitipController::class, 'editProfile'])->name('penitip.edit');
    Route::put('/{id}/update', [PenitipController::class, 'updateProfile'])->name('penitip.update');
    Route::get('/reward', [PenitipController::class, 'rewards'])->name('penitip.rewards');
    Route::get('/product', [PenitipController::class, 'product'])->name('penitip.product');
    Route::get('/myproduct', [PenitipController::class, 'myproduct'])->name('penitip.myproduct');
    Route::get('/transaction', [PenitipController::class, 'transaction'])->name('penitip.transaction');
    Route::get('/transaction/filter/{type}', [PenitipController::class, 'filterTransaction'])->name('penitip.transaction.filter');
    Route::get('/transaksi/hasil', [PenitipController::class, 'showSearchResult'])->name('penitip.detail');
    Route::get('/reset-password', [ResetPasswordController::class, 'showResetForm'])->name('penitip.password');
});

// Pembeli Routes
Route::prefix('pembeli')->middleware('auth:pembeli')->group(function () {
    Route::get('/profile', [PembeliController::class, 'profile'])->name('pembeli.profile');
    Route::get('/{id}/edit', [PembeliController::class, 'editProfile'])->name('pembeli.edit');
    Route::put('/{id}/update', [PembeliController::class, 'updateProfile'])->name('pembeli.update');
    Route::get('/history', [TransaksiPembelianController::class, 'history'])->name('pembeli.purchase');
    Route::get('/pembeli/rating/{id}', [TransaksiPembelianController::class, 'showRatingPage'])->name('pembeli.rating');
    Route::post('/pembeli/rate/{id}', [TransaksiPembelianController::class, 'rateTransaction'])->name('pembeli.rate');
    // Route::get('/riwayat', [TransaksiPembelianController::class, 'riwayat'])->name('pembeli.riwayat');
    Route::get('/transaksi150', [TransaksiPembelianController::class, 'transaksi150k'])->name('pembeli.transaksi')->middleware('auth');
    Route::get('/pembelian', [TransaksiPembelianController::class, 'index'])->name('pembeli.pembelian');
    // Route::get('/purchase', [PembeliController::class, 'purchase'])->name('pembeli.purchase');
    
    // Route::get('/history', [TransaksiPembelianController::class, 'history'])->name('pembeli.purchase');
    Route::get('/reward', [PembeliController::class, 'reward'])->name('pembeli.reward');
    Route::get('/process-payment', [ItemKeranjangController::class, 'processPayment'])->name('pembeli.processPayment');
    Route::post('/bayar', [TransaksiPembelianController::class, 'bayar'])->name('pembeli.bayar');
    Route::get('/batal-checkout/{id}', [TransaksiPembelianController::class, 'batalkanOtomatis'])->name('pembeli.batalCheckout');
    Route::post('/rate/{id}', [TransaksiPembelianController::class, 'rateTransaction'])->name('pembeli.rate');
    // Route::get('/reset-password', [ResetPasswordController::class, 'showResetForm'])->name('pembeli.password');

    Route::get('/transaksi/{id}', [TransaksiPembelianController::class, 'detail'])->name('pembeli.transaksi.detail');
    Route::get('/alamat', [AlamatController::class, 'alamatPembeli'])->name('pembeli.alamat');
    Route::post('/alamat', [AlamatController::class, 'store'])->name('pembeli.alamat.store');
    Route::put('/alamat/{id}', [AlamatController::class, 'update'])->name('pembeli.alamat.update');
    Route::delete('/alamat/{id}', [AlamatController::class, 'destroy'])->name('pembeli.alamat.destroy');
    Route::post('/alamat/{id}/set-default', [AlamatController::class, 'setDefault'])->name('pembeli.alamat.set_default');
    Route::get('/keranjang', [ItemKeranjangController::class, 'index'])->name('pembeli.cart'); // Restored to fix RouteNotFoundException
});

// Organisasi Routes
Route::prefix('organisasi')->middleware('auth:organisasi')->name('organisasi.')->group(function () {
    Route::get('/', [RequestDonasiController::class, 'index'])->name('index');
    Route::get('/request-donasi/add', [RequestDonasiController::class, 'create'])->name('request.create');
    Route::post('/request-donasi', [RequestDonasiController::class, 'store'])->name('request.store');
    Route::get('/request-donasi/{id}/edit', [RequestDonasiController::class, 'edit'])->name('request.edit');
    Route::put('/request-donasi/{id}', [RequestDonasiController::class, 'update'])->name('request.update');
    Route::get('/request-donasi/search', [RequestDonasiController::class, 'search'])->name('request.search');
    Route::delete('/request-donasi/{id}', [RequestDonasiController::class, 'destroy'])->name('request.destroy');
    Route::get('/profile', fn() => view('organisasi.profile'))->name('profile');
});

// Owner Routes
Route::prefix('owner')->middleware(['auth:pegawai', 'pegawai.role:1'])->group(function () {
    Route::get('/dashboard', [OwnerController::class, 'dashboard'])->name('owner.dashboard');
    Route::get('/donation/requests', [OwnerController::class, 'donationRequests'])->name('owner.donation.requests');
    Route::get('/donation/history', [OwnerController::class, 'donationHistory'])->name('owner.donation.history');
    Route::get('/allocate-items', [OwnerController::class, 'allocateItems'])->name('owner.allocate.items');
    Route::get('/reports', [OwnerController::class, 'reports'])->name('owner.reports');
    Route::post('/allocate-items', [OwnerController::class, 'storeAllocation'])->name('owner.store.allocation');
    Route::get('/update-donation', [OwnerController::class, 'updateDonation'])->name('owner.update.donation');
    Route::post('/update-donation', [OwnerController::class, 'updateDonasiStore'])->name('owner.update.donasi.store');
    Route::get('/rewards', [OwnerController::class, 'rewards'])->name('owner.rewards');
    Route::get('/donasi', [OwnerController::class, 'getDonasi'])->name('owner.get.donasi');
    Route::get('/requests-by-organisasi', [OwnerController::class, 'getRequestsByOrganisasi'])->name('owner.requests.by_organisasi');
    Route::delete('/request/{id}', [OwnerController::class, 'deleteRequest'])->name('owner.delete.request');
    Route::get('/monthly-sales-report', [OwnerController::class, 'monthlySalesReport'])->name('owner.monthly.sales.report');
    Route::get('/download-monthly-sales-report', [OwnerController::class, 'downloadMonthlySalesReport'])->name('owner.download.monthly.sales.report');
    Route::get('/warehouse-stock-report', [OwnerController::class, 'warehouseStockReport'])->name('owner.warehouse.stock.report');
    Route::get('/download/warehouse-stock-report', [OwnerController::class, 'downloadWarehouseStockReport'])->name('owner.download.warehouse.stock.report');
    Route::get('/monthly-sales-overview', [OwnerController::class, 'monthlySalesOverview'])->name('owner.monthly.sales.overview');
    Route::get('/download/monthly-sales-overview', [OwnerController::class, 'downloadMonthlySalesOverview'])->name('owner.download.monthly.sales.overview');
});

// Admin Routes
Route::prefix('admin')->middleware(['auth:pegawai', 'pegawai.role:2'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/employees', [PegawaiController::class, 'indexView'])->name('admin.employees.index');
    Route::get('/employees/add', [PegawaiController::class, 'create'])->name('admin.employees.create');
    Route::post('/employees', [PegawaiController::class, 'store'])->name('admin.employees.store');
    Route::get('/employees/search', [PegawaiController::class, 'search'])->name('admin.employees.search');
    Route::get('/employees/{id}/edit', [PegawaiController::class, 'edit'])->name('admin.employees.edit');
    Route::put('/employees/{id}', [PegawaiController::class, 'update'])->name('admin.employees.update');
    Route::put('/employees/{id}/reset-password', [PegawaiController::class, 'resetPassword'])->name('admin.employees.reset-password');
    Route::put('/employees/{id}/deactivate', [PegawaiController::class, 'deactivate'])->name('admin.employees.deactivate');
    Route::put('/employees/{id}/reactivate', [PegawaiController::class, 'reactivate'])->name('admin.employees.reactivate');
    Route::get('/roles', [RoleController::class, 'index'])->name('admin.roles.index');
    Route::get('/roles/add', [RoleController::class, 'create'])->name('admin.roles.create');
    Route::post('/roles', [RoleController::class, 'store'])->name('admin.roles.store');
    Route::get('/roles/{id}/edit', [RoleController::class, 'edit'])->name('admin.roles.edit');
    Route::put('/roles/{id}', [RoleController::class, 'update'])->name('admin.roles.update');
    Route::put('/roles/{id}/deactivate', [RoleController::class, 'deactivate'])->name('admin.roles.deactivate');
    Route::put('/roles/{id}/reactivate', [RoleController::class, 'reactivate'])->name('admin.roles.reactivate');
    Route::get('/roles/search', [RoleController::class, 'search'])->name('admin.roles.search');
    Route::get('/item-owners', [PenitipController::class, 'index'])->name('admin.penitip.index');
    Route::get('/item-owners/add', [PenitipController::class, 'create'])->name('admin.penitip.create');
    Route::post('/item-owners', [PenitipController::class, 'store'])->name('admin.penitip.store');
    Route::get('/item-owners/search', [PenitipController::class, 'search'])->name('admin.penitip.search');
    Route::get('/item-owners/{id}/edit', [PenitipController::class, 'edit'])->name('admin.penitip.edit');
    Route::put('/item-owners/{id}', [PenitipController::class, 'update'])->name('admin.penitip.update');
    Route::put('/item-owners/{id}/deactivate', [PenitipController::class, 'deactivate'])->name('admin.penitip.deactivate');
    Route::put('/item-owners/{id}/reactivate', [PenitipController::class, 'reactivate'])->name('admin.penitip.reactivate');
    Route::get('/customers', [PembeliController::class, 'index'])->name('admin.pembeli.index');
    Route::get('/customers/add', [PembeliController::class, 'create'])->name('admin.pembeli.create');
    Route::post('/customers', [PembeliController::class, 'store'])->name('admin.pembeli.store');
    Route::get('/customers/search', [PembeliController::class, 'search'])->name('admin.pembeli.search');
    Route::get('/customers/{id}/edit', [PembeliController::class, 'edit'])->name('admin.pembeli.edit');
    Route::put('/customers/{id}', [PembeliController::class, 'update'])->name('admin.pembeli.update');
    Route::put('/customers/{id}/deactivate', [PembeliController::class, 'deactivate'])->name('admin.pembeli.deactivate');
    Route::put('/customers/{id}/reactivate', [PembeliController::class, 'reactivate'])->name('admin.pembeli.reactivate');
    Route::get('/organizations', [OrganisasiController::class, 'index'])->name('admin.organisasi.index');
    Route::get('/organizations/add', [OrganisasiController::class, 'create'])->name('admin.organisasi.create');
    Route::post('/organizations', [OrganisasiController::class, 'store'])->name('admin.organisasi.store');
    Route::get('/organizations/search', [OrganisasiController::class, 'search'])->name('admin.organisasi.search');
    Route::get('/organizations/{id}/edit', [OrganisasiController::class, 'edit'])->name('admin.organisasi.edit');
    Route::put('/organizations/{id}', [OrganisasiController::class, 'update'])->name('admin.organisasi.update');
    Route::put('/organizations/{id}/deactivate', [OrganisasiController::class, 'deactivate'])->name('admin.organisasi.deactivate');
    Route::put('/organizations/{id}/reactivate', [OrganisasiController::class, 'reactivate'])->name('admin.organisasi.reactivate');
    Route::delete('/organizations/{id}', [OrganisasiController::class, 'destroy'])->name('admin.organisasi.destroy');
    Route::get('/products', [BarangController::class, 'index'])->name('admin.produk.index');
    Route::get('/products/add', [BarangController::class, 'create'])->name('admin.produk.create');
    Route::post('/products', [BarangController::class, 'store'])->name('admin.produk.store');
    Route::get('/products/search', [BarangController::class, 'search'])->name('admin.produk.search');
    Route::get('/products/{id}/edit', [BarangController::class, 'edit'])->name('admin.produk.edit');
    Route::put('/products/{id}', [BarangController::class, 'update'])->name('admin.produk.update');
    Route::put('/products/{id}/deactivate', [BarangController::class, 'deactivate'])->name('admin.produk.deactivate');
    Route::put('/products/{id}/reactivate', [BarangController::class, 'reactivate'])->name('admin.produk.reactivate');
    Route::get('/merchandise', [MerchandiseController::class, 'index'])->name('admin.merch.index');
    Route::get('/merchandise/add', [MerchandiseController::class, 'create'])->name('admin.merchandise.create');
    Route::post('/merchandise', [MerchandiseController::class, 'store'])->name('admin.merchandise.store');
    Route::get('/merchandise/search', [MerchandiseController::class, 'search'])->name('admin.merchandise.search');
    Route::get('/merchandise/{id}/edit', [MerchandiseController::class, 'edit'])->name('admin.merchandise.edit');
    Route::put('/merchandise/{id}', [MerchandiseController::class, 'update'])->name('admin.merchandise.update');
});

// CS Routes
Route::prefix('cs')->middleware(['auth:pegawai', 'pegawai.role:3'])->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('cs.penitip.index');
    })->name('cs.dashboard');
    Route::get('/item-owners', [PenitipController::class, 'index'])->name('cs.penitip.index');
    Route::get('/item-owners/add', [PenitipController::class, 'create'])->name('cs.penitip.create');
    Route::post('/item-owners', [PenitipController::class, 'store'])->name('cs.penitip.store');
    Route::get('/item-owners/search', [PenitipController::class, 'search'])->name('cs.penitip.search');
    Route::get('/item-owners/{id}/edit', [PenitipController::class, 'edit'])->name('cs.penitip.edit');
    Route::put('/item-owners/{id}', [PenitipController::class, 'update'])->name('cs.penitip.update');
    Route::put('/item-owners/{id}/deactivate', [PenitipController::class, 'deactivate'])->name('cs.penitip.deactivate');
    Route::put('/item-owners/{id}/reactivate', [PenitipController::class, 'reactivate'])->name('cs.penitip.reactivate');
    Route::get('/merchandise-claims', [TransaksiMerchandiseController::class, 'index'])->name('cs.merchandise-claim.index');
    Route::get('/merchandise-claims/search', [TransaksiMerchandiseController::class, 'search'])->name('cs.merchandise-claim.search');
    Route::put('/merchandise-claims/{id}', [TransaksiMerchandiseController::class, 'update'])->name('cs.merchandise-claim.update');
    Route::get('/transaksi-pembelian', [TransaksiPembelianController::class, 'show'])->name('transaksi-pembelian.index');
    Route::post('/transaksi-pembelian/{id_pembelian}/verify', [TransaksiPembelianController::class, 'verify'])
    ->name('cs.transaksi-pembelian.verify');
    Route::get('/transaksi-pembelian/search', [TransaksiPembelianController::class, 'search'])->name('cs.transaksi-pembelian.search');
});

Route::prefix('owner')->middleware(['auth:pegawai'])->group(function () {
    Route::get('/dashboard', [OwnerController::class, 'dashboard'])->name('owner.dashboard');
    Route::get('/donation/requests', [OwnerController::class, 'donationRequests'])->name('owner.donation.requests');
    Route::get('/donation/requests/download/pdf', [OwnerController::class, 'downloadPdf'])->name('owner.download.pdf');
    Route::get('/donation/history', [OwnerController::class, 'donationHistory'])->name('owner.donation.history');
    Route::get('/donation/download/pdf', [OwnerController::class, 'downloadDonationPdf'])->name('owner.download.donation.pdf');
    Route::get('/allocate-items', [OwnerController::class, 'allocateItems'])->name('owner.allocate.items');
    Route::post('/allocate-items', [OwnerController::class, 'storeAllocation'])->name('owner.store.allocation');
    Route::get('/update-donation', [OwnerController::class, 'updateDonation'])->name('owner.update.donation');
    Route::post('/update-donation', [OwnerController::class, 'updateDonasiStore'])->name('owner.update.donasi.store');
    Route::get('/rewards', [OwnerController::class, 'rewards'])->name('owner.rewards');
    Route::get('/donasi', [OwnerController::class, 'getDonasi'])->name('owner.get.donasi');
    Route::get('/requests-by-organisasi', [OwnerController::class, 'getRequestsByOrganisasi'])->name('owner.requests.by_organisasi');
    Route::delete('/request/{id}', [OwnerController::class, 'deleteRequest'])->name('owner.delete.request');
    Route::get('/consignment-report', [OwnerController::class, 'consignmentReport'])->name('owner.report');
    Route::get('/consignment-report/download/{id}', [OwnerController::class, 'downloadConsignmentReport'])
    ->name('owner.download.consignment.pdf');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Gudang Routes
Route::prefix('gudang')->middleware(['auth:pegawai', 'pegawai.role:4'])->name('gudang.')->group(function () {
    Route::get('/dashboard', [TransaksiPenitipanController::class, 'dashboard'])->name('dashboard');
    Route::get('/add-transaction', [TransaksiPenitipanController::class, 'create'])->name('add.transaction');
    Route::post('/store-transaction', [TransaksiPenitipanController::class, 'store'])->name('store.transaction');
    Route::get('/transaction-list', [TransaksiPenitipanController::class, 'transactionList'])->name('transaction.list');
    Route::get('/search-transaction', [TransaksiPenitipanController::class, 'searchTransaction'])->name('transaction.search');
    Route::get('/edit-transaction/{id}', [TransaksiPenitipanController::class, 'editTransaction'])->name('transaction.edit');
    Route::put('/update-transaction/{id}', [TransaksiPenitipanController::class, 'updateTransaction'])->name('transaction.update');
    Route::get('/print-note/{id}', [TransaksiPenitipanController::class, 'printNote'])->name('transaction.print');
    Route::get('/item-list', [BarangController::class, 'itemList'])->name('item.list');
});

Route::get('/login', function () {
    return redirect('/');
})->name('login');