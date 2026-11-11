<?php

namespace App\Console\Commands;

use App\Services\PembayaranService;
use Illuminate\Console\Command;

class BatalkanPemesananKedaluwarsa extends Command
{
    protected $signature = 'pemesanan:batalkan-kedaluwarsa';

    protected $description = 'Batalkan booking yang DP-nya tidak dibayar sampai batas waktu dan kembalikan stok';

    public function handle(PembayaranService $layanan): int
    {
        $this->info('Booking dibatalkan: '.$layanan->batalkanKedaluwarsa());

        return self::SUCCESS;
    }
}
