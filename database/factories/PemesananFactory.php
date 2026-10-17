<?php

namespace Database\Factories;

use App\Models\Pemesanan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pemesanan>
 */
class PemesananFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $mulai = fake()->dateTimeBetween('-1 month', '+1 month');
        $selesai = (clone $mulai)->modify('+'.fake()->numberBetween(1, 4).' days');
        $total = fake()->numberBetween(1, 5) * 50000;

        return [
            'kode_pemesanan' => 'ORD-'.fake()->unique()->numerify('######'),
            'user_id' => User::factory()->customer(),
            'tanggal_mulai' => $mulai->format('Y-m-d'),
            'tanggal_selesai' => $selesai->format('Y-m-d'),
            'total_biaya' => $total,
            'jumlah_dp' => round($total * 0.5),
            'status' => 'menunggu_pembayaran',
            'waktu_pembatalan' => null,
        ];
    }

    /**
     * Pemesanan yang sudah dikonfirmasi admin.
     */
    public function dikonfirmasi(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'dikonfirmasi']);
    }

    /**
     * Pemesanan yang sedang berlangsung (alat sudah diambil).
     */
    public function berlangsung(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'berlangsung']);
    }

    /**
     * Pemesanan yang sudah selesai.
     */
    public function selesai(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'selesai']);
    }

    /**
     * Pemesanan yang dibatalkan pelanggan.
     */
    public function dibatalkan(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'dibatalkan',
            'waktu_pembatalan' => now(),
        ]);
    }
}
