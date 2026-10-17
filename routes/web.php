<?php

use App\Http\Controllers\Admin\InventarisController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\InventarisController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return redirect('/katalog');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Data Alat & Stok. Dipakai oleh menu "Data Peralatan" di sidebar admin.
Route::get('/adminAlat', [InventarisController::class, 'index'])->name('admin.alat.index');

Route::get('/admin/peralatan', [InventarisController::class, 'index'])->name('admin.peralatan.index');
// Endpoint JSON untuk pencarian, filter, dan pagination tanpa reload halaman.
Route::get('/admin/peralatan/tabel', [InventarisController::class, 'tabel'])->name('admin.peralatan.tabel');

Route::post('/admin/peralatan', [InventarisController::class, 'store'])->name('admin.peralatan.store');
Route::put('/admin/peralatan/{alat}', [InventarisController::class, 'update'])->name('admin.peralatan.update');
Route::delete('/admin/peralatan/{alat}', [InventarisController::class, 'destroy'])->name('admin.peralatan.destroy');

Route::get('/admin', function () {
    return view('admin.dashboard');
})->name('admin.dashboard');

Route::get('/katalog', function () {
    return view('customer.katalog');
});

Route::get('/admin/pelanggan', function () {
    return view('admin.pelanggan');
});

Route::get('/admin/pemesanan', function () {
    return view('admin.pemesanan');
});

Route::get('/admin/verifikasi', function () {
    return view('admin.verifikasi');
});

Route::get('/admin/denda', function () {
    return view('admin.denda');
});

Route::get('/admin/laporan', function () {
    return view('admin.laporan');
});

require __DIR__.'/auth.php';