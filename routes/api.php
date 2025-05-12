<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\BarangController;

Route::post('/pegawai/register', [PegawaiController::class, 'register']);
Route::post('/pegawai/login', [PegawaiController::class, 'login']);
Route::get('/barang/{id}', [BarangController::class, 'show'])->name('barang.show');
// Route::post('/diskusi/store', [DiskusiProdukController::class, 'store'])->name('diskusi.store')->middleware('auth:pembeli');

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
