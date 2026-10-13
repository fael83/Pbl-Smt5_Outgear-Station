<?php

namespace Database\Factories;

use App\Models\Alat;
use App\Models\DetailPemesanan;
use App\Models\Pemesanan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DetailPemesanan>
 */
class DetailPemesananFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $jumlah = fake()->numberBetween(1, 3);
        $tarif = fake()->randomElement([15000, 25000, 50000, 75000]);

        return [
            'pemesanan_id' => Pemesanan::factory(),
            'alat_id' => Alat::factory(),
            'jumlah' => $jumlah,
            'tarif_per_hari' => $tarif,
            'subtotal' => $jumlah * $tarif,
        ];
    }

    /**
     * Baris pemesanan untuk pemesanan & alat tertentu (subtotal dihitung otomatis).
     */
    public function untuk(Pemesanan $pemesanan, Alat $alat, int $jumlah = 1): static
    {
        return $this->state(fn (array $attributes) => [
            'pemesanan_id' => $pemesanan->id,
            'alat_id' => $alat->id,
            'jumlah' => $jumlah,
            'tarif_per_hari' => $alat->tarif_per_hari,
            'subtotal' => $jumlah * (float) $alat->tarif_per_hari,
        ]);
    }
}
