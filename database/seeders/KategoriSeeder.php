<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    /**
     * Kategori perlengkapan outdoor (ERD: KATEGORI).
     *
     * @var array<string, string>
     */
    public const KATEGORI = [
        'Tenda' => 'Tenda dome, camp, dan flysheet untuk berbagai kapasitas.',
        'Carrier & Tas' => 'Carrier, daypack, dan tas gunung berbagai ukuran.',
        'Sleeping Bag' => 'Sleeping bag untuk cuaca dingin maupun tropis.',
        'Alat Masak' => 'Kompor portable, nesting, dan perlengkapan masak lapangan.',
        'Penerangan' => 'Lampu tenda, senter, dan headlamp.',
        'Navigasi' => 'Kompas dan perlengkapan navigasi lapangan.',
        'Sepatu & Sandal' => 'Sepatu hiking dan sandal gunung.',
        'Matras & Alas Tidur' => 'Matras foil, matras angin, dan alas tidur.',
    ];

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        foreach (self::KATEGORI as $nama => $deskripsi) {
            Kategori::firstOrCreate(
                ['nama' => $nama],
                ['deskripsi' => $deskripsi],
            );
        }
    }
}
