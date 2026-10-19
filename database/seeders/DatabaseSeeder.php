<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PenggunaSeeder::class,
            KategoriSeeder::class,
            AlatSeeder::class,
            AturanDendaSeeder::class,
        ]);

        // Data transaksi demo tidak boleh masuk ke database produksi.
        if (! app()->environment('production')) {
            $this->call(DemoTransaksiSeeder::class);
        }
    }
}
