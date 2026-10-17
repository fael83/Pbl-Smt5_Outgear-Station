<?php

namespace Database\Factories;

use App\Models\Kategori;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Kategori>
 */
class KategoriFactory extends Factory
{
    /**
     * Nama kategori perlengkapan outdoor yang realistis.
     *
     * @var array<int, string>
     */
    public const NAMA = [
        'Tenda',
        'Carrier & Tas',
        'Sleeping Bag',
        'Alat Masak',
        'Penerangan',
        'Navigasi',
        'Sepatu & Sandal',
        'Matras & Alas Tidur',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => fake()->randomElement(self::NAMA),
            'deskripsi' => fake()->sentence(8),
        ];
    }
}
