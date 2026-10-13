<?php

namespace Database\Factories;

use App\Models\Pemesanan;
use App\Models\Pengembalian;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pengembalian>
 */
class PengembalianFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'pemesanan_id' => Pemesanan::factory()->berlangsung(),
            'user_id' => User::factory()->petugas(),
            'waktu_pengambilan' => now()->subDays(3),
            'waktu_submit' => now(),
            'hari_terlambat' => 0,
        ];
    }

    /**
     * Pengembalian yang melewati tanggal selesai pemesanan.
     */
    public function terlambat(int $hari = 1): static
    {
        return $this->state(fn (array $attributes) => [
            'hari_terlambat' => $hari,
        ]);
    }
}
