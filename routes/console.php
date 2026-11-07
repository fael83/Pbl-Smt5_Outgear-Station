<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Batalkan booking yang DP-nya tidak dibayar sampai batas waktu, lalu kembalikan stoknya.
// Dijalankan oleh scheduler (php artisan schedule:work saat development).
Schedule::command('pemesanan:batalkan-kedaluwarsa')->everyMinute()->withoutOverlapping();
