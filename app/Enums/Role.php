<?php

namespace App\Enums;

/**
 * Peran pengguna (ERD: PENGGUNA.peran).
 */
enum Role: string
{
    case Customer = 'customer';
    case Admin = 'admin';
    case Petugas = 'petugas';
    case Owner = 'owner';

    /**
     * Label bahasa Indonesia untuk ditampilkan di UI.
     */
    public function label(): string
    {
        return match ($this) {
            self::Customer => 'Pelanggan',
            self::Admin => 'Admin',
            self::Petugas => 'Petugas',
            self::Owner => 'Owner',
        };
    }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Peran yang boleh mengelola master data & aturan denda.
     *
     * @return array<int, string>
     */
    public static function manajemen(): array
    {
        return [self::Admin->value, self::Owner->value];
    }

    /**
     * Peran yang menjalankan operasional harian (serah terima, pengembalian, denda).
     *
     * @return array<int, string>
     */
    public static function operasional(): array
    {
        return [self::Admin->value, self::Petugas->value];
    }
}
