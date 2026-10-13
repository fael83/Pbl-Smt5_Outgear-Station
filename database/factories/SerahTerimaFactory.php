<?php

namespace Database\Factories;

use App\Models\Pemesanan;
use App\Models\SerahTerima;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SerahTerima>
 */
class SerahTerimaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'pemesanan_id' => Pemesanan::factory()->dikonfirmasi(),
            'user_id' => User::factory()->petugas(),
            'waktu_serah_terima' => now(),
            'nama_ktp' => fake()->name(),
            'nomor_ktp' => fake()->numerify('################'),
            'foto_ktp' => null,
            'status_serah_terima' => 'diambil',
        ];
    }

    /**
     * Alat sudah dikembalikan pelanggan.
     */
    public function dikembalikan(): static
    {
        return $this->state(fn (array $attributes) => ['status_serah_terima' => 'dikembalikan']);
    }
}
