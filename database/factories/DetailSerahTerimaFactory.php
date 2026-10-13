<?php

namespace Database\Factories;

use App\Models\DetailPemesanan;
use App\Models\DetailSerahTerima;
use App\Models\SerahTerima;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DetailSerahTerima>
 */
class DetailSerahTerimaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'serah_terima_id' => SerahTerima::factory(),
            'detail_pemesanan_id' => DetailPemesanan::factory(),
            'jumlah_disiapkan' => 1,
            'kondisi_awal' => 'baik',
        ];
    }

    /**
     * Alat diserahkan dalam kondisi kurang baik.
     */
    public function kurangBaik(): static
    {
        return $this->state(fn (array $attributes) => ['kondisi_awal' => 'kurang_baik']);
    }
}
