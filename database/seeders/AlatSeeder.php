<?php

namespace Database\Seeders;

use App\Models\Alat;
use App\Models\Kategori;
use Illuminate\Database\Seeder;

class AlatSeeder extends Seeder
{
    /**
     * Daftar alat sewa (ERD: ALAT).
     * Format: nama => [kategori, tarif_per_hari, nilai_pengganti, stok_total, foto_url].
     *
     * @var array<string, array{0: string, 1: int, 2: int, 3: int, 4?: string}>
     */
    public const ALAT = [
        'Tenda Dome 4 Orang' => ['Tenda', 75000, 1200000, 8, 'tenda-dome-4.avif'],
        'Tenda Camp 6 Orang' => ['Tenda', 100000, 1800000, 5, 'tenda-camp-6.jpg'],
        'Flysheet 3x4' => ['Tenda', 35000, 450000, 10, 'flysheet-3x4.jpg'],
        'Carrier 60L' => ['Carrier & Tas', 50000, 900000, 6, 'carrier-60l.jpeg'],
        'Daypack 25L' => ['Carrier & Tas', 25000, 350000, 10, 'daypack-25l.jpeg'],
        'Sleeping Bag Polar' => ['Sleeping Bag', 25000, 300000, 12, 'sleeping-bag-polar.png'],
        'Sleeping Bag Mummy' => ['Sleeping Bag', 30000, 450000, 8, 'sleeping-bag-mummy.webp'],
        'Kompor Portable' => ['Alat Masak', 20000, 250000, 10, 'kompor-portable.png'],
        'Nesting Cook Set' => ['Alat Masak', 25000, 300000, 8, 'nesting-cook-set.jpeg'],
        'Lampu Tenda LED' => ['Penerangan', 15000, 150000, 15, 'lampu-tenda-led.png'],
        'Senter Headlamp' => ['Penerangan', 15000, 200000, 12, 'senter-headlamp.jpg'],
        'Kompas Silva' => ['Navigasi', 15000, 250000, 6, 'kompas-silva.jpg'],
        'Sepatu Hiking 42' => ['Sepatu & Sandal', 35000, 600000, 5, 'sepatu-hiking-42.avif'],
        'Matras Foil' => ['Matras & Alas Tidur', 10000, 75000, 20, 'matras-foil.webp'],
    ];

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $kategoriIds = Kategori::query()->pluck('id', 'nama');

        foreach (self::ALAT as $nama => $data) {
            $kategori = $data[0];
            $tarif = $data[1];
            $nilaiPengganti = $data[2];
            $stok = $data[3];
            $foto = $data[4] ?? null;

            $kategoriId = $kategoriIds[$kategori] ?? null;

            if ($kategoriId === null) {
                continue;
            }

            Alat::updateOrCreate(
                ['nama' => $nama],
                [
                    'kategori_id' => $kategoriId,
                    'foto' => $foto,
                    'deskripsi' => "Sewa {$nama} dengan kondisi terawat dan siap pakai.",
                    'tarif_per_hari' => $tarif,
                    'nilai_pengganti' => $nilaiPengganti,
                    'stok_total' => $stok,
                    'stok_tersedia' => $stok,
                    'stok_disewa' => 0,
                    'stok_rusak' => 0,
                ],
            );
        }
    }
}
