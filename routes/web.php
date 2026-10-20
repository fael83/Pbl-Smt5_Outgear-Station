<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DendaController as AdminDendaController;
use App\Http\Controllers\Admin\InventarisController;
use App\Http\Controllers\Admin\LaporanController as AdminLaporanController;
use App\Http\Controllers\Admin\PelangganController as AdminPelangganController;
use App\Http\Controllers\Admin\PemesananController as AdminPemesananController;
use App\Http\Controllers\Admin\VerifikasiController as AdminVerifikasiController;
use App\Http\Controllers\BerandaController;
use App\Http\Controllers\BookingSayaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KatalogController;
use App\Http\Controllers\OutGearBotController;
use App\Http\Controllers\PanduanController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::post('/wa/webhook', [OutGearBotController::class, 'handleWebhook']);

Route::get('/', BerandaController::class);

// Seluruh halaman publik di bawah ini dapat dibuka tanpa login. Setelah login,
// pengguna tetap diarahkan sesuai perannya (lihat DashboardController).
Route::get('/dashboard', DashboardController::class)->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Katalog, panduan, dan ringkasan booking tetap publik seperti perilaku sebelumnya.
Route::get('/katalog', KatalogController::class)->name('katalog');
Route::get('/panduan-sewa', PanduanController::class)->name('panduan');
Route::get('/booking-saya', BookingSayaController::class)->name('booking.saya');

/*
|--------------------------------------------------------------------------
| Panel admin
|--------------------------------------------------------------------------
|
| Halaman /admin/* dapat dibuka tanpa login (mode demo). Jika pengguna sudah
| login, middleware role tetap membatasi akses: owner dan petugas hanya boleh
| melihat data, sedangkan aksi tulis (simpan/ubah/hapus alat) khusus admin.
|
*/
Route::middleware(['role:admin,owner,petugas'])
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

        // Endpoint JSON untuk pencarian, filter, dan pagination tanpa reload halaman.
        Route::get('/peralatan/tabel', [InventarisController::class, 'tabel'])->name('peralatan.tabel');

        // Aksi tulis hanya untuk admin (selaras dengan AlatPolicy).
        Route::middleware('role:admin')->group(function () {
            Route::post('/peralatan', [InventarisController::class, 'store'])->name('peralatan.store');
            Route::put('/peralatan/{alat}', [InventarisController::class, 'update'])->name('peralatan.update');
            Route::delete('/peralatan/{alat}', [InventarisController::class, 'destroy'])->name('peralatan.destroy');
        });
    });

require __DIR__.'/auth.php';
