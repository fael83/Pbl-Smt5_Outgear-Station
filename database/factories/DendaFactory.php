<?php

namespace Database\Factories;

use App\Models\AturanDenda;
use App\Models\Denda;
use App\Models\Pemesanan;
use App\Models\Pengembalian;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Denda>
 */
class DendaFactory extends Factory
{
    /**
     * Jenis denda yang berlaku untuk baris DENDA (tanpa kehilangan).
     *
     * @var array<int, string>
     */
    public const JENIS = [
        'keterlambatan',
        'kerusakan_ringan',
        'kerusakan_berat',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $nilaiDasar = fake()->randomElement([50000, 75000, 150000]);
        $persentase = fake()->randomElement([10, 20, 50]);

        return [
            'pengembalian_id' => Pengembalian::factory(),
            // Pakai aturan denda yang sudah ada agar tidak melanggar unique jenis_denda.
            'aturan_denda_id' => fn () => AturanDenda::query()->value('id') ?? AturanDenda::factory(),
            'pemesanan_id' => Pemesanan::factory()->selesai(),
            'detail_pengembalian_id' => null,
            'nilai_dasar' => $nilaiDasar,
            'jumlah_denda' => round($nilaiDasar * $persentase / 100),
            'status' => 'belum_dibayar',
            'jenis_denda' => fake()->randomElement(self::JENIS),
            'nilai_tarif_terkait' => null,
        ];
    }

    /**
     * Denda yang sudah dibayar pelanggan.
     */
    public function lunas(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'lunas']);
    }
}
