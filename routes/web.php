<?php

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

    Route::get('/admin', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    use App\Http\Controllers\Admin\InventarisController;
    Route::get('/adminAlat', [InventarisController::class, 'index'])->name('admin.alat.index');

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
    auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
});

require __DIR__.'/auth.php';
