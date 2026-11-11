<?php

namespace App\Support;

use App\Models\Denda;
use App\Models\Pembayaran;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Angka lencana di sidebar admin (pembayaran menunggu verifikasi dan denda
 * belum lunas) dan angka "perlu verifikasi" di dashboard.
 *
 * Definisinya SAMA dengan halaman Verifikasi Pembayaran: pembayaran berstatus
 * `pending`. Dihitung dengan satu query lalu di-cache 1 menit; cache dibuang
 * otomatis setiap kali data pembayaran atau denda berubah (lihat booted() di
 * model Pembayaran dan Denda), jadi angkanya tidak tertinggal.
 */
final class BadgeAdmin
{
    private const KUNCI = 'sidebar:badge';

    /** Kunci cache dashboard admin (lihat Admin\DashboardController). */
    private const KUNCI_DASHBOARD = 'admin:dashboard:data';

    /**
     * @return array{pembayaran: int, denda: int}
     */
    public static function hitung(): array
    {
        return Cache::remember(self::KUNCI, now()->addMinute(), static function (): array {
            $hasil = DB::selectOne(
                "select
                    (select count(*) from pembayaran where status_transaksi = 'pending') as pembayaran,
                    (select count(*) from denda where status = 'belum_dibayar') as denda"
            );

            return [
                'pembayaran' => (int) $hasil->pembayaran,
                'denda' => (int) $hasil->denda,
            ];
        });
    }

    public static function lupakan(): void
    {
        Cache::forget(self::KUNCI);
        Cache::forget(self::KUNCI_DASHBOARD);
    }
}
