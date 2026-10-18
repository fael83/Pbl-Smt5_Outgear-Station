<?php

use App\Http\Controllers\Admin\InventarisController;
use App\Http\Controllers\ProfileController;
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
})->name('katalog');

Route::get('/panduan', function () {
    return view('customer.panduan');
})->name('panduan');

Route::get('/booking-saya', function () {
    return view('customer.booking');
})->name('booking.saya');

Route::get('/admin/pelanggan', function () {
    return view('admin.pelanggan');
});

Route::get('/admin/pemesanan', function () {
    return view('admin.pemesanan');
});

Route::get('/admin/verifikasi', function () {
    $pembayaranDb = \App\Models\Pembayaran::with('pemesanan.pelanggan')->latest('id')->get();
    return view('admin.verifikasi', compact('pembayaranDb'));
});

Route::get('/admin/denda', function () {
    return view('admin.denda');
});

Route::get('/admin/laporan', function () {
    return view('admin.laporan');
});

require __DIR__.'/auth.php';