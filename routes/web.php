<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PenitipController;
use App\Http\Controllers\PembeliController;
use App\Http\Controllers\OrganisasiController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\MerchandiseController;

// Redirect root ke dashboard
Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');

// Admin routes
Route::prefix('admin')->group(function () {

    // =================== DASHBOARD ===================
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // =================== EMPLOYEES ===================
    Route::get('/employees', [PegawaiController::class, 'indexView'])->name('admin.employees.index');
    Route::get('/employees/add', [PegawaiController::class, 'create'])->name('admin.employees.create');
    Route::get('/employees/search', [PegawaiController::class, 'search'])->name('admin.employees.search');
    Route::post('/employees', [PegawaiController::class, 'store'])->name('admin.employees.store');

    //Edit & Non-active Employees
    Route::get('/employees/{id}/edit', [PegawaiController::class, 'edit'])->name('admin.employees.edit');
    Route::put('/employees/{id}', [PegawaiController::class, 'update'])->name('admin.employees.update');
    Route::put('/employees/{id}/reset-password', [PegawaiController::class, 'resetPassword'])->name('admin.employees.reset-password');
    Route::put('/employees/{id}/deactivate', [PegawaiController::class, 'deactivate'])->name('admin.employees.deactivate');
    Route::put('/employees/{id}/reactivate', [PegawaiController::class, 'reactivate'])->name('admin.employees.reactivate');


    // =================== ROLES ===================
    Route::get('/roles', [RoleController::class, 'index'])->name('admin.roles.index');

    // =================== ITEM OWNERS ===================
    Route::get('/item-owners', [PenitipController::class, 'index'])->name('admin.penitip.index');

    // =================== CUSTOMERS ===================
    Route::get('/customers', [PembeliController::class, 'index'])->name('admin.pembeli.index');

    // =================== ORGANIZATIONS ===================
    Route::get('/organizations', [OrganisasiController::class, 'index'])->name('admin.organisasi.index');

    // =================== PRODUCTS ===================
    Route::get('/products', [BarangController::class, 'index'])->name('admin.produk.index');

    // =================== CATEGORIES ===================
    Route::get('/categories', [KategoriController::class, 'index'])->name('admin.kategori.index');

    // =================== MERCHANDISE ===================
    Route::get('/merchandise', [MerchandiseController::class, 'index'])->name('admin.merch.index');
});
