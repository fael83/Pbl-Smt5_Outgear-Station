<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

    use App\Http\Controllers\Admin\InventarisController;
    Route::get('/adminAlat', [InventarisController::class, 'index'])->name('admin.alat.index');

    Route::get('/admin', function () {
        return view('admin.dashboard');
    });
    
    Route::get('/katalog', function () {
        return view('customer.katalog');
    });

    Route::get('/admin/pelanggan', function () {
    return view('admin.pelanggan');
    });

    Route::get('/admin/pemesanan', function () {
        return view('admin.pemesanan');
    });

    Route::get('/admin/peralatan', function () {
        return view('admin.peralatan');
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
