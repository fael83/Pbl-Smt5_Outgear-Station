<?php

namespace Database\Factories;

use App\Models\DetailPemesanan;
use App\Models\DetailPengembalian;
use App\Models\Pengembalian;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DetailPengembalian>
 */
class DetailPengembalianFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'pengembalian_id' => Pengembalian::factory(),
            'detail_pemesanan_id' => DetailPemesanan::factory(),
            'jumlah_baik' => 1,
            'jumlah_rusak' => 0,
            'jumlah_hilang' => 0,
        ];
    }

    /**
     * Sebagian unit dikembalikan dalam kondisi rusak.
     */
    public function denganKerusakan(int $jumlah = 1): static
    {
        return $this->state(fn (array $attributes) => [
            'jumlah_baik' => max(0, ($attributes['jumlah_baik'] ?? 0) - $jumlah),
            'jumlah_rusak' => $jumlah,
        ]);
    }

    /**
     * Sebagian unit hilang.
     */
    public function denganKehilangan(int $jumlah = 1): static
    {
        return $this->state(fn (array $attributes) => [
            'jumlah_baik' => max(0, ($attributes['jumlah_baik'] ?? 0) - $jumlah),
            'jumlah_hilang' => $jumlah,
        ]);
    }
}
