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
use App\Http\Controllers\Owner\DashboardController as OwnerDashboardController;
use App\Http\Controllers\Owner\DendaController as OwnerDendaController;
use App\Http\Controllers\Owner\HargaController as OwnerHargaController;
use App\Http\Controllers\Owner\LaporanController as OwnerLaporanController;
use App\Http\Controllers\Owner\StaffController as OwnerStaffController;

use App\Http\Controllers\Owner\DashboardController as OwnerDashboardController;
use App\Http\Controllers\Owner\DendaController;
use App\Http\Controllers\Owner\HargaController;
use App\Http\Controllers\Owner\LaporanController as OwnerLaporanController;
use App\Http\Controllers\Owner\StaffController;
use App\Http\Controllers\Owner\ProfileController as OwnerProfileController;

use App\Http\Controllers\PanduanController;
use App\Http\Controllers\Petugas\DashboardController as PetugasDashboardController;
use App\Http\Controllers\Petugas\PersiapanController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Petugas\PengembalianController;
use App\Http\Controllers\Petugas\KerusakanController;

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

Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');



// Notifikasi pembayaran dari server Midtrans (tanpa login & tanpa CSRF; dicek lewat signature).
Route::post('/midtrans/notifikasi', MidtransWebhookController::class)->name('midtrans.notifikasi');

// Booking & pembayaran: wajib login sebagai pelanggan.
Route::middleware(['auth', 'role:customer'])->group(function () {
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/booking-saya', BookingSayaController::class)->name('booking.saya');
    Route::post('/pembayaran/{pembayaran}/bayar', [PembayaranPelangganController::class, 'bayar'])->name('pembayaran.bayar');
    Route::post('/booking-saya/{pemesanan}/batalkan', [PembayaranPelangganController::class, 'batalkanPesanan'])->name('pemesanan.batalkan');
    Route::post('/pembayaran/{pembayaran}/sinkron', [PembayaranPelangganController::class, 'sinkron'])->name('pembayaran.sinkron');
});

=======
Route::post('/wa/webhook', [OutGearBotController::class, 'handleWebhook']);

Route::get('/', function () {
    return view('welcome');
});

Route::get('/katalog', function () {
    return view('customer.katalog');
});


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

| Panel Admin (Role: Admin, Owner)

| Panel admin

|--------------------------------------------------------------------------
|
| Middleware role membatasi akses: owner dan petugas hanya boleh melihat
| data, sedangkan aksi tulis (simpan/ubah/hapus) khusus admin.
|
*/

Route::middleware(['auth', 'role:admin,owner'])

Route::middleware(['role:admin,owner,petugas'])

    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', AdminDashboardController::class)->name('dashboard');
        Route::get('/data', [AdminDashboardController::class, 'data'])->name('dashboard.data');

        Route::get('/profile', [AdminProfilController::class, 'show'])->name('profile');
        Route::put('/profile', [AdminProfilController::class, 'update'])->name('profile.update');
        Route::put('/profile/sandi', [AdminProfilController::class, 'ubahSandi'])->name('profile.sandi');

        Route::view('/profile', 'admin.profile')->name('profile');
        Route::get('/denda', [AdminDendaController::class, 'index'])->name('denda.index');


        // Pelanggan (akun dengan peran customer), dibaca langsung dari tabel users.
        Route::get('/pelanggan', [AdminPelangganController::class, 'index'])->name('pelanggan');
        Route::get('/pelanggan/tabel', [AdminPelangganController::class, 'tabel'])->name('pelanggan.tabel');
        Route::get('/pelanggan/ekspor', [AdminPelangganController::class, 'ekspor'])->name('pelanggan.ekspor');

        // Pemesanan, verifikasi, denda, laporan — read-only untuk owner/petugas.
        Route::get('/pemesanan', [AdminPemesananController::class, 'index'])->name('pemesanan');
        Route::get('/pemesanan/tabel', [AdminPemesananController::class, 'tabel'])->name('pemesanan.tabel');
        Route::get('/verifikasi', [AdminVerifikasiController::class, 'index'])->name('verifikasi.index');

        Route::get('/verifikasi/data', [AdminVerifikasiController::class, 'data'])->name('verifikasi.data');
        Route::get('/denda', [AdminDendaController::class, 'index'])->name('denda.index');
        Route::get('/denda/tabel', [AdminDendaController::class, 'tabel'])->name('denda.tabel');
        Route::get('/laporan', [AdminLaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/tabel', [AdminLaporanController::class, 'tabel'])->name('laporan.tabel');

        
        Route::get('/laporan', [AdminLaporanController::class, 'index'])->name('laporan.index');

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

// /OWNERRR

Route::middleware('role:owner')
    ->prefix('owner')
    ->name('owner.')
    ->group(function () {

        Route::get('/dashboard', [OwnerDashboardController::class, 'index'])->name('dashboard');

        Route::get('/harga-sewa', [OwnerHargaController::class, 'hargaSewa'])->name('harga-sewa.index');
        Route::put('/harga-sewa/{id}', [OwnerHargaController::class, 'updateHargaSewa'])->name('harga-sewa.update');
        Route::get('/aturan-denda', [OwnerDendaController::class, 'aturanDenda'])->name('aturan-denda.index');
        Route::put('/aturan-denda/{id}', [OwnerDendaController::class, 'updateAturanDenda'])->name('aturan-denda.update');

        Route::get('/kelola-staff', [OwnerStaffController::class, 'kelolaStaff'])->name('kelola-staff.index');
        Route::get('/kelola-staff/tambah', [OwnerStaffController::class, 'createStaff'])->name('kelola-staff.create');
        Route::post('/kelola-staff', [OwnerStaffController::class, 'storeStaff'])->name('kelola-staff.store');
        Route::get('/kelola-staff/{id}/edit', [OwnerStaffController::class, 'editStaff'])->name('kelola-staff.edit');
        Route::put('/kelola-staff/{id}', [OwnerStaffController::class, 'updateStaff'])->name('kelola-staff.update');
        Route::delete('/kelola-staff/{id}', [OwnerStaffController::class, 'destroyStaff'])->name('kelola-staff.destroy');

        Route::get('/laporan', [OwnerLaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/ekspor-csv', [OwnerLaporanController::class, 'exportCsv'])->name('laporan.export-csv');
        Route::get('/laporan/ekspor-pdf', [OwnerLaporanController::class, 'exportPdf'])->name('laporan.export');



        // Dashboard Owner
        Route::get('/dashboard', [OwnerDashboardController::class, 'index'])->name('dashboard');

        // Laporan Penyewaan
        Route::get('/laporan', [OwnerLaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/export', [OwnerLaporanController::class, 'exportPdf'])->name('laporan.export');
        Route::get('/laporan/export-csv', [OwnerLaporanController::class, 'exportCsv'])->name('laporan.export-csv');
        Route::get('/laporan/denda', [OwnerLaporanController::class, 'rekapDenda'])->name('laporan.denda');

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

        //profile
        Route::get('/profil', [OwnerProfileController::class, 'index'])->name('profil.index');
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
        Route::get('/profile', [PetugasDashboardController::class, 'profile'])->name('profile');
        Route::put('/profile', [PetugasDashboardController::class, 'updateProfile'])->name('profile.update');
    });


/*
|--------------------------------------------------------------------------
| Panel Petugas Operasional
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:petugas,owner'])
    ->prefix('petugas')
    ->name('petugas.')
    ->group(function () {
        Route::get('/dashboard', [PetugasDashboardController::class, 'index'])->name('dashboard');
        Route::get('/profile', [PetugasDashboardController::class, 'profile'])->name('profile');
        Route::put('/profile', [PetugasDashboardController::class, 'updateProfile'])->name('profile.update');

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

require __DIR__.'/auth.php';

