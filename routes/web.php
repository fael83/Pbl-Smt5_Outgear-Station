<?php

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

Route::get('/adminAlat', [InventarisController::class, 'index'])->name('admin.alat.index');

Route::get('/admin', function () {
    return view('admin.dashboard');
})->name('admin.dashboard');
    
Route::get('/katalog', function () {
    return view('customer.katalog');
});

Route::get('/panduan-sewa', function () {
    return view('customer.panduan');
})->name('panduan');

Route::get('/admin/profil', function () {
    return view('admin.profile');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/booking-saya', function () {
        return view('customer.booking');
    })->name('booking.saya');
});

Route::get('/keluar-paksa', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
});

Route::get('/admin/pelanggan', function () {
    return view('admin.pelanggan');
});

Route::get('/admin/pemesanan', function () {
    return view('admin.pemesanan');
});

Route::get('/admin/peralatan', function () {
    // Halaman "Data Peralatan" pada sidebar dashboard menampilkan
    // inventaris alat yang sama dengan menu "Data Alat & Stok".
    return view('admin.inventaris.index');
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