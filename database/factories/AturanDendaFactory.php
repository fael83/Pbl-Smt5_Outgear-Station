<?php

namespace Database\Factories;

use App\Models\AturanDenda;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AturanDenda>
 */
class AturanDendaFactory extends Factory
{
    /**
     * Jenis denda sesuai ERD (ATURAN_DENDA.jenis_denda).
     *
     * @var array<int, string>
     */
    public const JENIS = [
        'keterlambatan',
        'kerusakan_ringan',
        'kerusakan_berat',
        'kehilangan',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Kolom jenis_denda bersifat unik, jadi maksimal 4 baris aturan denda.
            'jenis_denda' => fake()->unique()->randomElement(self::JENIS),
            'perhitungan_denda' => 'per_hari',
            'nilai_denda' => fake()->randomElement([25000, 50000, 100000]),
            'user_id' => User::factory()->admin(),
        ];
    }

    /**
     * Denda keterlambatan (per hari).
     */
    public function keterlambatan(float $nilai = 50000): static
    {
        return $this->state(fn (array $attributes) => [
            'jenis_denda' => 'keterlambatan',
            'perhitungan_denda' => 'per_hari',
            'nilai_denda' => $nilai,
        ]);
    }

    /**
     * Denda kerusakan ringan (persentase dari tarif).
     */
    public function kerusakanRingan(float $persentase = 20): static
    {
        return $this->state(fn (array $attributes) => [
            'jenis_denda' => 'kerusakan_ringan',
            'perhitungan_denda' => 'persentase',
            'nilai_denda' => $persentase,
        ]);
    }

    /**
     * Denda kerusakan berat (persentase dari tarif).
     */
    public function kerusakanBerat(float $persentase = 50): static
    {
        return $this->state(fn (array $attributes) => [
            'jenis_denda' => 'kerusakan_berat',
            'perhitungan_denda' => 'persentase',
            'nilai_denda' => $persentase,
        ]);
    }

    /**
     * Denda kehilangan (persentase dari nilai pengganti).
     */
    public function kehilangan(float $persentase = 100): static
    {
        return $this->state(fn (array $attributes) => [
            'jenis_denda' => 'kehilangan',
            'perhitungan_denda' => 'persentase',
            'nilai_denda' => $persentase,
        ]);
    }
}
