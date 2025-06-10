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
use App\Http\Controllers\DiskusiProdukController;
use App\Models\Barang;

Route::post('/login-api-debug', [App\Http\Controllers\AuthController::class, 'loginapi'])
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]); // Kita tetap kecualikan CSRF untuk berjaga-jaga

// Route::post('/api/login', [App\Http\Controllers\AuthController::class, 'loginapi'])->name('api.login.temp');
// Route::post('/api/login', [App\Http\Controllers\AuthController::class, 'loginapi'])
//     ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]) // Ini penting
//     ->name('api.login.temp');

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
    Route::get('/myProduct', [PenitipController::class, 'myProduct'])->name('penitip.myproduct');
    Route::get('/transaction', [PenitipController::class, 'transaction'])->name('penitip.transaction');
    Route::get('/transaction/filter/{type}', [PenitipController::class, 'filterTransaction'])->name('penitip.transaction.filter');
    Route::get('/transaksi/hasil', [PenitipController::class, 'showSearchResult'])->name('penitip.detail');
    Route::get('/reset-password', [ResetPasswordController::class, 'showResetForm'])->name('penitip.password');
    Route::get('/myProduct/search', [PenitipController::class, 'searchProducts'])->name('penitip.products.search');
    Route::post('/perpanjang/{id}', [PenitipController::class, 'perpanjang'])->name('penitip.perpanjang');
    Route::patch('/barang/{id}/confirm-pickup', [PenitipController::class, 'confirmPickup']);
    Route::get('/api/barang/{id}/check-pickup-info', [PenitipController::class, 'getPickupDeadline']);
});

// =================== PEMBELI ROUTES ===================
Route::prefix('pembeli')->middleware('auth:pembeli')->name('pembeli.')->group(function () {
    // 🔐 Profil dan Reward
    Route::get('/profile', [PembeliController::class, 'profile'])->name('profile');
    Route::get('/profile/{id}/edit', [PembeliController::class, 'editProfile'])->name('profile.edit');
    Route::put('/profile/{id}/update', [PembeliController::class, 'updateProfile'])->name('update');
    Route::get('/reward', [PembeliController::class, 'reward'])->name('reward');

    // 🧾 Riwayat Transaksi Pembelian
    Route::get('/riwayat', [TransaksiPembelianController::class, 'riwayat'])->name('riwayat');
    Route::get('/riwayat/{id}', [TransaksiPembelianController::class, 'detail'])->name('riwayat.detail');

    // 💳 Transaksi & Pembayaran
    Route::get('/purchase', [PembeliController::class, 'purchase'])->name('purchase');
    Route::get('/process-payment', [ItemKeranjangController::class, 'processPayment'])->name('processPayment');
    Route::post('/bayar', [TransaksiPembelianController::class, 'bayar'])->name('bayar');
    Route::get('/batal-checkout/{id}', [TransaksiPembelianController::class, 'batalkanOtomatis'])->name('batalCheckout');

    // 🔒 Reset Password
    Route::get('/reset-password', [ResetPasswordController::class, 'showResetForm'])->name('password');

    // 📦 Alamat Pengiriman
    Route::get('/alamat', [AlamatController::class, 'alamatPembeli'])->name('alamat');
    Route::post('/alamat', [AlamatController::class, 'store'])->name('alamat.store');
    Route::put('/alamat/{id}', [AlamatController::class, 'update'])->name('alamat.update');
    Route::delete('/alamat/{id}', [AlamatController::class, 'destroy'])->name('alamat.destroy');
    Route::post('/alamat/{id}/set-default', [AlamatController::class, 'setDefault'])->name('alamat.set_default');

    // 🛒 Keranjang
    Route::get('/keranjang', [KeranjangController::class, 'index'])->name('cart');

    // (Optional) 📢 Diskusi Produk
    // Route::post('/diskusi/store', [DiskusiProdukController::class, 'store'])->name('diskusi.store');
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
    Route::post('/allocate-items', [OwnerController::class, 'storeAllocation'])->name('owner.store.allocation');
    Route::get('/update-donation', [OwnerController::class, 'updateDonation'])->name('owner.update.donation');
    Route::post('/update-donation', [OwnerController::class, 'updateDonasiStore'])->name('owner.update.donasi.store');
    Route::get('/rewards', [OwnerController::class, 'rewards'])->name('owner.rewards');
    Route::get('/donasi', [OwnerController::class, 'getDonasi'])->name('owner.get.donasi');
    Route::get('/requests-by-organisasi', [OwnerController::class, 'getRequestsByOrganisasi'])->name('owner.requests.by_organisasi');
    Route::delete('/request/{id}', [OwnerController::class, 'deleteRequest'])->name('owner.delete.request');
    //Laporan
    Route::get('/reports/sales-by-category', [OwnerController::class, 'penjualanPerKategori'])->name('owner.reports.sales_by_category');
    Route::get('/reports/sales-by-category/download', [OwnerController::class, 'downloadPenjualanPerKategori'])->name('owner.reports.download_sales_by_category');
    Route::get('/reports/expired-items', [OwnerController::class, 'expiredItems'])->name('owner.reports.expired_items');
    Route::get('/reports/expired-items/download', [OwnerController::class, 'downloadExpiredItems'])->name('owner.reports.download_expired_items'); 
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

    Route::get('/transaksi-pengiriman', [TransaksiPenitipanController::class, 'pengirimanDanPengambilanList'])->name('transaksi.pengiriman');
    Route::patch('/perbarui-status-transaksi', [TransaksiPenitipanController::class, 'perbaruiStatusOtomatis'])->name('gudang.updateStatusTransaksi');
    Route::get('/transaksi/detail/{id}', [TransaksiPenitipanController::class, 'showDetail'])->name('transaksi.detail');
    Route::get('/transaksi-pengambilan', [TransaksiPenitipanController::class, 'transaksiPengambilan'])->name('transaksi.pengambilan');
    Route::patch('/mark-as-returned/{id}', [TransaksiPenitipanController::class, 'markAsReturned'])->name('markAsReturned');
    Route::get('/transaksi/schedule/{id}', [TransaksiPenitipanController::class, 'jadwalkan'])->name('transaksi.schedule');  
    Route::post('/transaksi/jadwal/{id}', [TransaksiPenitipanController::class, 'jadwalkanPengiriman'])->name('transaksi.jadwalkanPengiriman');  
    Route::post('/transaksi/jadwalkan/{id}', [TransaksiPenitipanController::class, 'jadwalkanPengiriman'])->name('transaksi.jadwalkanPengiriman');
    Route::post('/transaksi/confirm-pickup/{id}', [TransaksiPenitipanController::class, 'confirmPickup'])->name('transaksi.confirmPickup');
    Route::get('/transaksi/print-invoice/{id}', [TransaksiPenitipanController::class, 'printInvoice'])->name('transaksi.printInvoice');
});

// Kurir Routes
Route::prefix('kurir')->middleware(['auth:pegawai', 'pegawai.role:5'])->group(function () {
    Route::get('/dashboard', fn() => view('kurir.dashboard'))->name('kurir.dashboard');
});