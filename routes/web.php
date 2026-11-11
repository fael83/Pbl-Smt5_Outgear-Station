<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DendaController as AdminDendaController;
use App\Http\Controllers\Admin\InventarisController;
use App\Http\Controllers\Admin\LaporanController as AdminLaporanController;
use App\Http\Controllers\Admin\PelangganController as AdminPelangganController;
use App\Http\Controllers\Admin\PemesananController as AdminPemesananController;
use App\Http\Controllers\Admin\ProfilController as AdminProfilController;
use App\Http\Controllers\Admin\VerifikasiController as AdminVerifikasiController;
use App\Http\Controllers\BerandaController;
use App\Http\Controllers\BookingSayaController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KatalogController;
use App\Http\Controllers\MidtransWebhookController;
use App\Http\Controllers\PembayaranPelangganController;
use App\Http\Controllers\OutGearBotController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\PanduanController;
use App\Http\Controllers\Petugas\DashboardController as PetugasDashboardController;
use App\Http\Controllers\Petugas\KerusakanController;
use App\Http\Controllers\Petugas\PengembalianController;
use App\Http\Controllers\Petugas\PersiapanController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Webhook & Halaman Publik
|--------------------------------------------------------------------------
*/
Route::get('/', BerandaController::class)->name('beranda');
Route::get('/katalog', KatalogController::class)->name('katalog');
Route::get('/panduan-sewa', PanduanController::class)->name('panduan');
Route::get('/booking-saya', BookingSayaController::class)->name('booking.saya');

/*
|--------------------------------------------------------------------------
| Rute Pengguna Terautentikasi (Auth Customer)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // Profil Pengguna
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Panel Admin (Role: Admin, Owner, Petugas)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin,owner,petugas'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', AdminDashboardController::class)->name('dashboard');
        Route::get('/data', [AdminDashboardController::class, 'data'])->name('dashboard.data');
        Route::view('/profile', 'admin.profile')->name('profile');

        // Data Pelanggan
        Route::get('/pelanggan', [AdminPelangganController::class, 'index'])->name('pelanggan');
        Route::get('/pelanggan/tabel', [AdminPelangganController::class, 'tabel'])->name('pelanggan.tabel');
        Route::get('/pelanggan/ekspor', [AdminPelangganController::class, 'ekspor'])->name('pelanggan.ekspor');

        // Transaksi, Verifikasi & Laporan
        Route::get('/pemesanan', [AdminPemesananController::class, 'index'])->name('pemesanan');
        Route::get('/pemesanan/tabel', [AdminPemesananController::class, 'tabel'])->name('pemesanan.tabel');
        Route::get('/verifikasi', [AdminVerifikasiController::class, 'index'])->name('verifikasi.index');
        Route::get('/denda', AdminDendaController::class)->name('denda.index');
        Route::get('/laporan', AdminLaporanController::class)->name('laporan.index');
        Route::get('/laporan/export-excel', [AdminLaporanController::class, 'exportExcel'])->name('laporan.excel');
        Route::get('/laporan/cetak-pdf', [AdminLaporanController::class, 'cetakPdf'])->name('laporan.pdf');

        // Data Peralatan & Inventaris
        Route::get('/peralatan', [InventarisController::class, 'index'])->name('peralatan.index');
        Route::get('/peralatan/tabel', [InventarisController::class, 'tabel'])->name('peralatan.tabel');

        // Aksi Tulis / Perubahan Data (Khusus Admin)
        Route::middleware('role:admin')->group(function () {
            Route::patch('/pelanggan/{pelanggan}/status', [AdminPelangganController::class, 'ubahStatus'])->name('pelanggan.status');
            Route::post('/pelanggan', [AdminPelangganController::class, 'store'])->name('pelanggan.store');
            Route::put('/pelanggan/{pelanggan}', [AdminPelangganController::class, 'update'])->name('pelanggan.update');
            Route::delete('/pelanggan/{pelanggan}', [AdminPelangganController::class, 'destroy'])->name('pelanggan.destroy');

            Route::patch('/verifikasi/{pembayaran}/verifikasi', [AdminVerifikasiController::class, 'verifikasi'])->name('verifikasi.verifikasi');
            Route::patch('/verifikasi/{pembayaran}/tolak', [AdminVerifikasiController::class, 'tolak'])->name('verifikasi.tolak');

            Route::post('/peralatan', [InventarisController::class, 'store'])->name('peralatan.store');
            Route::put('/peralatan/{alat}', [InventarisController::class, 'update'])->name('peralatan.update');
            Route::delete('/peralatan/{alat}', [InventarisController::class, 'destroy'])->name('peralatan.destroy');
        });
    });

/*
|--------------------------------------------------------------------------
| Panel Khusus Owner
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:owner'])
    ->prefix('owner')
    ->name('owner.')
    ->group(function () {
        Route::get('/dashboard', [PetugasDashboardController::class, 'index'])->name('dashboard');
        Route::get('/persiapan', [PersiapanController::class, 'index'])->name('persiapan');
        Route::get('/persiapan/tabel', [PersiapanController::class, 'tabel'])->name('persiapan.tabel');
        Route::post('/persiapan', [PersiapanController::class, 'store'])->name('persiapan.store');
        Route::put('/persiapan/{id}', [PersiapanController::class, 'update'])->name('persiapan.update');
        Route::delete('/persiapan/{id}', [PersiapanController::class, 'destroy'])->name('persiapan.destroy');
        Route::get('/pengembalian', [PengembalianController::class, 'index'])->name('pengembalian');
        Route::post('/pengembalian', [PengembalianController::class, 'store'])->name('pengembalian.store');
        Route::post('/pengembalian/{id}/checklist', [PengembalianController::class, 'updateChecklist'])->name('pengembalian.checklist');
        Route::delete('/pengembalian/{id}', [PengembalianController::class, 'destroy'])->name('pengembalian.destroy');
        Route::get('/kerusakan', [KerusakanController::class, 'index'])->name('kerusakan');
    });

require __DIR__.'/auth.php';