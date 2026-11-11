<?php

namespace App\Support;

use App\Models\Pemesanan;
use Illuminate\Support\Facades\Cache;

/**
 * Angka lencana "Booking Saya" di navbar pelanggan: jumlah booking yang masih
 * berjalan (menunggu bayar, dikonfirmasi, atau sedang disewa).
 */
final class BadgeBooking
{
    public static function untuk(?int $userId): int
    {
        if ($userId === null) {
            return 0;
        }

        return Cache::remember(self::kunci($userId), now()->addMinute(), static fn (): int => Pemesanan::query()
            ->where('user_id', $userId)
            ->whereIn('status', ['menunggu_pembayaran', 'dikonfirmasi', 'berlangsung'])
            ->count());
    }

    public static function lupakan(int $userId): void
    {
        Cache::forget(self::kunci($userId));
    }

    private static function kunci(int $userId): string
    {
        return 'badge:booking:'.$userId;
    }
}
