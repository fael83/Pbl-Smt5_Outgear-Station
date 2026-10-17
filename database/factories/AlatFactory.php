<?php

namespace Database\Factories;

use App\Models\Alat;
use App\Models\Kategori;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Alat>
 */
class AlatFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tarif = fake()->randomElement([15000, 20000, 25000, 35000, 50000, 75000, 100000]);
        $stokTotal = fake()->numberBetween(3, 15);

        return [
            'kategori_id' => Kategori::factory(),
            'nama' => ucwords(fake()->words(3, true)),
            'deskripsi' => fake()->sentence(10),
            'foto' => null,
            'tarif_per_hari' => $tarif,
            'nilai_pengganti' => $tarif * fake()->numberBetween(5, 15),
            'stok_total' => $stokTotal,
            'stok_tersedia' => $stokTotal,
            'stok_disewa' => 0,
            'stok_rusak' => 0,
        ];
    }

    /**
     * Sebagian stok sedang disewa sehingga stok tersedia berkurang.
     */
    public function sebagianDisewa(int $jumlah = 1): static
    {
        return $this->state(fn (array $attributes) => [
            'stok_tersedia' => max(0, ($attributes['stok_total'] ?? $jumlah) - $jumlah),
            'stok_disewa' => $jumlah,
        ]);
    }

    /**
     * Sebagian stok rusak.
     */
    public function sebagianRusak(int $jumlah = 1): static
    {
        return $this->state(fn (array $attributes) => [
            'stok_tersedia' => max(0, ($attributes['stok_total'] ?? $jumlah) - $jumlah),
            'stok_rusak' => $jumlah,
        ]);
    }
}
