<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FcmTokenController;
use App\Http\Controllers\NotificationController;

Route::post('/login', [AuthController::class, 'loginapi']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logoutapi']);

Route::middleware('auth:sanctum')->post('/save-fcm-token', [FcmTokenController::class, 'saveToken']);
Route::middleware('auth:sanctum')->post('/send-notification', [NotificationController::class, 'sendNotification']);

Route::post('/pegawai/register', [PegawaiController::class, 'register']);
Route::post('/pegawai/login', [PegawaiController::class, 'login']);
Route::get('/barang/{id}', [BarangController::class, 'show'])->name('barang.show');
// Route::post('/diskusi/store', [DiskusiProdukController::class, 'store'])->name('diskusi.store')->middleware('auth:pembeli');

Route::get('/barang', [BarangController::class, 'apiIndex']);
Route::get('/barang/{id}', [BarangController::class, 'apiShow']);
Route::get('/kategori', [BarangController::class, 'getKategoriApi']);

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

Route::prefix('kurir')->middleware(['auth:pegawai', 'pegawai.role:5'])->group(function () {
    Route::get('/deliveries', [KurirController::class, 'getDeliveries']);
    Route::post('/deliveries/update', [KurirController::class, 'updateDeliveryStatus']);
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