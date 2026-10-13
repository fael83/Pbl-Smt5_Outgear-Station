<?php

namespace Database\Seeders;

use App\Models\Alat;
use App\Models\Kategori;
use Illuminate\Database\Seeder;

class AlatSeeder extends Seeder
{
    /**
     * Daftar alat sewa (ERD: ALAT).
     * Format: nama => [kategori, tarif_per_hari, nilai_pengganti, stok_total].
     *
     * @var array<string, array{0: string, 1: int, 2: int, 3: int}>
     */
    public const ALAT = [
        'Tenda Dome 4 Orang' => ['Tenda', 75000, 1200000, 8],
        'Tenda Camp 6 Orang' => ['Tenda', 100000, 1800000, 5],
        'Flysheet 3x4' => ['Tenda', 35000, 450000, 10],
        'Carrier 60L' => ['Carrier & Tas', 50000, 900000, 6],
        'Daypack 25L' => ['Carrier & Tas', 25000, 350000, 10],
        'Sleeping Bag Polar' => ['Sleeping Bag', 25000, 300000, 12],
        'Sleeping Bag Mummy' => ['Sleeping Bag', 30000, 450000, 8],
        'Kompor Portable' => ['Alat Masak', 20000, 250000, 10],
        'Nesting Cook Set' => ['Alat Masak', 25000, 300000, 8],
        'Lampu Tenda LED' => ['Penerangan', 15000, 150000, 15],
        'Senter Headlamp' => ['Penerangan', 15000, 200000, 12],
        'Kompas Silva' => ['Navigasi', 15000, 250000, 6],
        'Sepatu Hiking 42' => ['Sepatu & Sandal', 35000, 600000, 5],
        'Matras Foil' => ['Matras & Alas Tidur', 10000, 75000, 20],
    ];

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $kategoriIds = Kategori::query()->pluck('id', 'nama');

        foreach (self::ALAT as $nama => [$kategori, $tarif, $nilaiPengganti, $stok]) {
            $kategoriId = $kategoriIds[$kategori] ?? null;

            if ($kategoriId === null) {
                continue;
            }

            Alat::firstOrCreate(
                ['nama' => $nama],
                [
                    'kategori_id' => $kategoriId,
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
