<?php

namespace App\Support;

/**
 * Pemetaan nama kategori alat menjadi emoji supaya tabel tetap enak dibaca
 * tanpa perlu file gambar.
 *
 * Dipakai bersama oleh partial tabel dan partial baris (respons AJAX), jadi
 * logikanya sengaja ditaruh di satu tempat agar tidak ada duplikasi.
 */
final class IkonKategori
{
    public static function untuk(?string $namaKategori): string
    {
        $namaKategori = (string) $namaKategori;

        return match (true) {
            str_contains($namaKategori, 'Tenda') => '⛺',
            str_contains($namaKategori, 'Carrier'), str_contains($namaKategori, 'Tas') => '🎒',
            str_contains($namaKategori, 'Sleeping') => '🛌',
            str_contains($namaKategori, 'Masak') => '🍳',
            str_contains($namaKategori, 'Penerangan') => '🔦',
            str_contains($namaKategori, 'Navigasi') => '🧭',
            str_contains($namaKategori, 'Sepatu'), str_contains($namaKategori, 'Sandal') => '🥾',
            str_contains($namaKategori, 'Matras') => '🛏️',
            default => '📦',
        };
    }
}
