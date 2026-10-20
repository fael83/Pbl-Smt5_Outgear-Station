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
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\PanduanController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Webhook & halaman publik
|--------------------------------------------------------------------------
*/
Route::post('/wa/webhook', [OutGearBotController::class, 'handleWebhook'])->name('wa.webhook');

Route::get('/', BerandaController::class)->name('beranda');

// Seluruh halaman publik di bawah ini dapat dibuka tanpa login. Setelah login,
// pengguna tetap diarahkan sesuai perannya (lihat DashboardController).
Route::get('/dashboard', DashboardController::class)->name('dashboard');

Route::get('/katalog', KatalogController::class)->name('katalog');
Route::get('/panduan-sewa', PanduanController::class)->name('panduan');
Route::get('/booking-saya', BookingSayaController::class)->name('booking.saya');

/*
|--------------------------------------------------------------------------
| Profil pengguna (wajib login)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Panel admin
|--------------------------------------------------------------------------
|
| Middleware role membatasi akses: owner dan petugas hanya boleh melihat
| data, sedangkan aksi tulis (simpan/ubah/hapus) khusus admin.
|
*/
Route::middleware(['role:admin,owner,petugas'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', AdminDashboardController::class)->name('dashboard');

        // Pelanggan (akun dengan peran customer), dibaca langsung dari tabel users.
        Route::get('/pelanggan', [AdminPelangganController::class, 'index'])->name('pelanggan');
        Route::get('/pelanggan/tabel', [AdminPelangganController::class, 'tabel'])->name('pelanggan.tabel');
        Route::get('/pelanggan/ekspor', [AdminPelangganController::class, 'ekspor'])->name('pelanggan.ekspor');

        // Pemesanan, verifikasi, denda, laporan — read-only untuk owner/petugas.
        Route::get('/pemesanan', [AdminPemesananController::class, 'index'])->name('pemesanan');
        Route::get('/verifikasi', AdminVerifikasiController::class)->name('verifikasi.index');
        Route::get('/denda', AdminDendaController::class)->name('denda.index');
        Route::get('/laporan', AdminLaporanController::class)->name('laporan.index');

        // Data Alat & Stok. Dipakai oleh menu "Data Peralatan" di sidebar admin.
        Route::get('/peralatan', [InventarisController::class, 'index'])->name('peralatan.index');
        Route::get('/peralatan/tabel', [InventarisController::class, 'tabel'])->name('peralatan.tabel');

        // Aksi tulis hanya untuk admin (selaras dengan AlatPolicy).
        Route::middleware('role:admin')->group(function () {
            Route::patch('/pelanggan/{pelanggan}/status', [AdminPelangganController::class, 'ubahStatus'])->name('pelanggan.status');

            Route::post('/peralatan', [InventarisController::class, 'store'])->name('peralatan.store');
            Route::put('/peralatan/{alat}', [InventarisController::class, 'update'])->name('peralatan.update');
            Route::delete('/peralatan/{alat}', [InventarisController::class, 'destroy'])->name('peralatan.destroy');
        });
    });


Route::middleware('role:owner')
    ->prefix('owner')
    ->name('owner.')
    ->group(function () {
        // Kelola Harga Sewa / Tarif Alat
        Route::get('/harga-sewa', [OwnerController::class, 'hargaSewa'])->name('harga-sewa.index');
        Route::put('/harga-sewa/{id}', [OwnerController::class, 'updateHargaSewa'])->name('harga-sewa.update');

        // Kelola Aturan Denda
        Route::get('/aturan-denda', [OwnerController::class, 'aturanDenda'])->name('aturan-denda.index');
        Route::put('/aturan-denda/{id}', [OwnerController::class, 'updateAturanDenda'])->name('aturan-denda.update');

        // Kelola Staff
        Route::get('/kelola-staff', [OwnerController::class, 'kelolaStaff'])->name('kelola-staff.index');
        Route::get('/kelola-staff/tambah', [OwnerController::class, 'createStaff'])->name('kelola-staff.create');
        Route::post('/kelola-staff', [OwnerController::class, 'storeStaff'])->name('kelola-staff.store');
        Route::get('/kelola-staff/{id}/edit', [OwnerController::class, 'editStaff'])->name('kelola-staff.edit');
        Route::put('/kelola-staff/{id}', [OwnerController::class, 'updateStaff'])->name('kelola-staff.update');
        Route::delete('/kelola-staff/{id}', [OwnerController::class, 'destroyStaff'])->name('kelola-staff.destroy');
    });

require __DIR__.'/auth.php';