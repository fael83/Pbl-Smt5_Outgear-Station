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
use App\Http\Controllers\Admin\LaporanController;

use App\Http\Controllers\Owner\HargaController;
use App\Http\Controllers\Owner\StaffController;
use App\Http\Controllers\Owner\DendaController;
use App\Http\Controllers\Owner\DashboardController as OwnerDashboardController;
use App\Http\Controllers\Owner\LaporanController as OwnerLaporanController;

use App\Http\Controllers\Petugas\DashboardController as PetugasDashboardController;

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
        Route::view('/profile', 'admin.profile')->name('profile');

        // Pelanggan (akun dengan peran customer), dibaca langsung dari tabel users.
        Route::get('/pelanggan', [AdminPelangganController::class, 'index'])->name('pelanggan');
        Route::get('/pelanggan/tabel', [AdminPelangganController::class, 'tabel'])->name('pelanggan.tabel');
        Route::get('/pelanggan/ekspor', [AdminPelangganController::class, 'ekspor'])->name('pelanggan.ekspor');

        // Pemesanan, verifikasi, denda, laporan — read-only untuk owner/petugas.
        Route::get('/pemesanan', [AdminPemesananController::class, 'index'])->name('pemesanan');
        Route::get('/verifikasi', [AdminVerifikasiController::class, 'index'])->name('verifikasi.index');
        Route::get('/denda', AdminDendaController::class)->name('denda.index');
        Route::get('/laporan', AdminLaporanController::class)->name('laporan.index');
        Route::get('/laporan/export-excel', [AdminLaporanController::class, 'exportExcel'])->name('laporan.excel');
        Route::get('/laporan/cetak-pdf', [AdminLaporanController::class, 'cetakPdf'])->name('laporan.pdf');

        // Data Alat & Stok. Dipakai oleh menu "Data Peralatan" di sidebar admin.
        Route::get('/peralatan', [InventarisController::class, 'index'])->name('peralatan.index');
        Route::get('/peralatan/tabel', [InventarisController::class, 'tabel'])->name('peralatan.tabel');

        // Aksi tulis hanya untuk admin (selaras dengan AlatPolicy).
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


///OWNERRR

Route::middleware('role:owner')
    ->prefix('owner')
    ->name('owner.')
    ->group(function () {

        // Dashboard Owner
        Route::get('/dashboard', [OwnerDashboardController::class, 'index'])->name('dashboard');

        // Laporan Penyewaan
        Route::get('/laporan', [OwnerLaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/export', [OwnerLaporanController::class, 'exportPdf'])->name('laporan.export');
        Route::get('/laporan/export-csv', [OwnerLaporanController::class, 'exportCsv'])->name('laporan.export-csv');

        // Kelola Harga Sewa / Tarif Alat
        Route::get('/harga-sewa', [HargaController::class, 'hargaSewa'])->name('harga-sewa.index');
        Route::put('/harga-sewa/{id}', [HargaController::class, 'updateHargaSewa'])->name('harga-sewa.update');

        // Kelola Aturan Denda
        Route::get('/aturan-denda', [DendaController::class, 'aturanDenda'])->name('aturan-denda.index');
        Route::put('/aturan-denda/{id}', [DendaController::class, 'updateAturanDenda'])->name('aturan-denda.update');

        // Kelola Staff
        Route::get('/kelola-staff', [StaffController::class, 'kelolaStaff'])->name('kelola-staff.index');
        Route::get('/kelola-staff/tambah', [StaffController::class, 'createStaff'])->name('kelola-staff.create');
        Route::post('/kelola-staff', [StaffController::class, 'storeStaff'])->name('kelola-staff.store');
        Route::get('/kelola-staff/{id}/edit', [StaffController::class, 'editStaff'])->name('kelola-staff.edit');
        Route::put('/kelola-staff/{id}', [StaffController::class, 'updateStaff'])->name('kelola-staff.update');
        Route::delete('/kelola-staff/{id}', [StaffController::class, 'destroyStaff'])->name('kelola-staff.destroy');
    });


/*
|--------------------------------------------------------------------------
| Panel Petugas Gudang
|--------------------------------------------------------------------------
*/

Route::middleware('role:petugas')
    ->prefix('petugas')
    ->name('petugas.')
    ->group(function () {
        Route::get('/dashboard', [
            PetugasDashboardController::class,
            'index',
        ])->name('dashboard');
    });

    

require __DIR__.'/auth.php';
