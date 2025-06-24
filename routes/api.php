<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\PenitipController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\KurirController;
use App\Http\Controllers\KeranjangController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PembeliController;
use App\Http\Controllers\TransaksiPembelianController;
use App\Http\Controllers\FcmTokenController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\HunterController;
use App\Http\Controllers\MerchandiseController;

Route::post('/login', [AuthController::class, 'loginapi']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logoutapi']);

Route::middleware('auth:sanctum')->post('/save-fcm-token', [FcmTokenController::class, 'saveToken']);
Route::middleware('auth:sanctum')->post('/send-notification', [NotificationController::class, 'sendNotification']);


// Route tanpa autentikasi
Route::get('/penitip/{id}', [PenitipController::class, 'getPenitipById']);
Route::get('/penitip/{id}/history', [PenitipController::class, 'getConsignmentHistoryById']);
Route::get('/penitip/profile', [PenitipController::class, 'getProfile']);
Route::get('/penitip/history', [PenitipController::class, 'getConsignmentHistory']);

// Route pembeli tanpa autentikasi
Route::get('/pembeli/{id}', [PembeliController::class, 'getPembeliById']);
Route::get('/pembeli/{id}/history', [TransaksiPembelianController::class, 'getPurchaseHistoryById']);
Route::get('/pembeli/profile', [PembeliController::class, 'getProfile']);
Route::get('/pembeli/history', [TransaksiPembelianController::class, 'getPurchaseHistory']);
    
Route::post('/pegawai/register', [PegawaiController::class, 'register']);
Route::post('/pegawai/login', [PegawaiController::class, 'login']);
Route::get('/barang/{id}', [BarangController::class, 'show'])->name('barang.show');
// Route::post('/diskusi/store', [DiskusiProdukController::class, 'store'])->name('diskusi.store')->middleware('auth:pembeli');

// Route::get('/top-seller', [PenitipController::class, 'getTopSeller']);
Route::get('/barang', [BarangController::class, 'apiIndex']);
Route::get('/barang/{id}', [BarangController::class, 'apiShow']);
Route::get('/kategori', [BarangController::class, 'getKategoriApi']);
Route::get('/top-seller', [TransaksiPembelianController::class, 'indextopapi'])->name('api.top-seller');

Route::middleware('auth:api')->group(function () {
    // Logout Pegawai
    Route::post('/pegawai/logout', [PegawaiController::class, 'logout']);
    // Read data pegawai
    Route::get('/pegawai', [PegawaiController::class, 'index']);
    Route::get('/pegawai/{id}', [PegawaiController::class, 'show']);
    // Create pegawai
    Route::post('/pegawai/create', [PegawaiController::class, 'store']);
    // Update pegawai
    Route::post('/pegawai/update/{id}', [PegawaiController::class, 'update']);
    // Delete pegawai
    Route::delete('/pegawai/delete/{id}', [PegawaiController::class, 'destroy']);

    // Role routes
    Route::get('/role', [RoleController::class, 'index']);
    Route::get('/role/{id}', [RoleController::class, 'show']);
    Route::post('/role/create', [RoleController::class, 'store']);
    Route::post('/role/update/{id}', [RoleController::class, 'update']);
    Route::delete('/role/delete/{id}', [RoleController::class, 'destroy']);
    
});
// Route::prefix('kurir')->middleware(['auth:sanctum', 'api_pegawai.role:5'])->group(function () {

Route::prefix('kurir')->group(function () {
    Route::get('/profile', [AuthController::class, 'profileKurir']);
    Route::get('/kelola-transaksi/kurir/{idPegawai}', [KurirController::class, 'getDeliveries']);
    Route::get('/active-deliveries/{idPegawai}', [KurirController::class, 'getActiveDeliveries']);
    Route::put('/transaksi-pembelian/{idPembelian}/status/transaksi', [KurirController::class, 'updateStatusTransaksi']);
});

Route::middleware('auth:api')->group(function () {
    Route::post('/send-notification', [NotificationController::class, 'sendNotification']);
    Route::post('/transaksi-pembelian', [TransaksiPembelianController::class, 'store']);
    Route::post('/transaksi-pembelian/{id}/verify', [TransaksiPembelianController::class, 'verify']);
    Route::post('/delivery-schedule', [TransaksiPembelianController::class, 'createDeliverySchedule']);
    Route::post('/pickup-schedule', [TransaksiPembelianController::class, 'createPickupSchedule']);
    Route::post('/update-delivery-status/{id}', [TransaksiPembelianController::class, 'updateDeliveryStatus']);
    Route::get('/keranjang/{id}/barang', [KeranjangController::class, 'getBarangByKeranjang']);
});


// Rute Merchandise (untuk Pembeli)
Route::get('/merchandise', [MerchandiseController::class, 'indexApi']);
Route::get('/merchandise/{id}', [MerchandiseController::class, 'showApi']);
Route::post('/merchandise/claim', [MerchandiseController::class, 'claimMerchandise']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logoutapi']); // Logout API

    Route::post('/save-fcm-token', [FcmTokenController::class, 'saveToken']);
    Route::post('/send-notification', [NotificationController::class, 'sendNotification']);

    // Rute Hunter
    Route::get('/hunter/profile-and-commission', [HunterController::class, 'getHunterProfileAndTotalCommission']);
    Route::get('/hunter/commission-history', [HunterController::class, 'getCommissionHistory']);
    Route::get('/hunter/commission-detail/{commissionId}', [HunterController::class, 'getCommissionDetail']);

    // Rute Merchandise (untuk Pembeli)
    // Route::get('/merchandise', [MerchandiseController::class, 'indexApi']);
    // Route::get('/merchandise/{id}', [MerchandiseController::class, 'showApi']);
    // Route::post('/merchandise/claim', [MerchandiseController::class, 'claimMerchandise']);

    Route::post('/pegawai/logout', [PegawaiController::class, 'logout']); 
    Route::get('/pegawai', [PegawaiController::class, 'index']); 
    Route::get('/pegawai/{id}', [PegawaiController::class, 'show']); 
    Route::post('/pegawai/create', [PegawaiController::class, 'store']); 
    Route::post('/pegawai/update/{id}', [PegawaiController::class, 'update']); 
    Route::delete('/pegawai/delete/{id}', [PegawaiController::class, 'destroy']); 

    // Role routes
    Route::get('/role', [RoleController::class, 'index']);
    Route::get('/role/{id}', [RoleController::class, 'show']);
    Route::post('/role/create', [RoleController::class, 'store']);
    Route::post('/role/update/{id}', [RoleController::class, 'update']);
    Route::delete('/role/delete/{id}', [RoleController::class, 'destroy']);

    Route::post('/transaksi-pembelian', [TransaksiPembelianController::class, 'store']);
    Route::post('/transaksi-pembelian/{id}/verify', [TransaksiPembelianController::class, 'verify']);
    Route::post('/delivery-schedule', [TransaksiPembelianController::class, 'createDeliverySchedule']);
    Route::post('/pickup-schedule', [TransaksiPembelianController::class, 'createPickupSchedule']);
    Route::post('/update-delivery-status/{id}', [TransaksiPembelianController::class, 'updateDeliveryStatus']);
    Route::get('/keranjang/{id}/barang', [KeranjangController::class, 'getBarangByKeranjang']);
});