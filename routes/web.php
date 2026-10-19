<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DendaController as AdminDendaController;
use App\Http\Controllers\Admin\InventarisController;
use App\Http\Controllers\Admin\LaporanController as AdminLaporanController;
use App\Http\Controllers\Admin\PelangganController as AdminPelangganController;
use App\Http\Controllers\Admin\PemesananController as AdminPemesananController;
use App\Http\Controllers\Admin\VerifikasiController as AdminVerifikasiController;
use App\Http\Controllers\BerandaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KatalogController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', BerandaController::class);

// Setelah login, pengguna diarahkan sesuai perannya (lihat DashboardController).
Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Katalog tetap publik seperti perilaku sebelumnya.
Route::get('/katalog', KatalogController::class)->name('katalog');

/*
|--------------------------------------------------------------------------
| Panel admin
|--------------------------------------------------------------------------
|
| Seluruh halaman /admin/* hanya untuk pengguna yang sudah login dan
| memiliki peran internal. Owner dan petugas hanya boleh melihat data,
| sedangkan aksi tulis (simpan/ubah/hapus alat) khusus admin.
|
*/
Route::middleware(['auth', 'role:admin,owner,petugas'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', AdminDashboardController::class)->name('dashboard');
        Route::get('/pelanggan', AdminPelangganController::class)->name('pelanggan');
        Route::get('/pemesanan', AdminPemesananController::class)->name('pemesanan');
        Route::get('/verifikasi', AdminVerifikasiController::class)->name('verifikasi');
        Route::get('/denda', AdminDendaController::class)->name('denda');
        Route::get('/laporan', AdminLaporanController::class)->name('laporan');

        // Data Alat & Stok. Dipakai oleh menu "Data Peralatan" di sidebar admin.
        Route::get('/peralatan', [InventarisController::class, 'index'])->name('peralatan.index');

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
