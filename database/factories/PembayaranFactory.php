<?php

namespace Database\Factories;

use App\Models\Pembayaran;
use App\Models\Pemesanan;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Pembayaran>
 */
class PembayaranFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'pemesanan_id' => Pemesanan::factory(),
            'transaction_id' => 'MID-'.Str::uuid(),
            'midtrans' => null,
            'jenis_pembayaran' => fake()->randomElement(['dp', 'pelunasan', 'denda']),
            'metode_pembayaran' => fake()->randomElement(['qris', 'card']),
            'jumlah' => fake()->randomElement([50000, 100000, 150000, 250000]),
            'status_transaksi' => 'pending',
            'waktu_bayar' => null,
        ];
    }

    /**
     * Transaksi Midtrans yang berhasil.
     */
    public function berhasil(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_transaksi' => 'berhasil',
            'waktu_bayar' => now(),
            'midtrans' => fake()->uuid(),
        ]);
    }

    /**
     * Transaksi Midtrans yang gagal.
     */
    public function gagal(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_transaksi' => 'gagal',
            'waktu_bayar' => now(),
        ]);
    }
}
