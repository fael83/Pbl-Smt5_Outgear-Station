<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\AturanDenda;
use App\Models\User;
use Illuminate\Database\Seeder;

class AturanDendaSeeder extends Seeder
{
    /**
     * Aturan denda bawaan (ERD: ATURAN_DENDA) — satu baris per jenis denda.
     * Kolom jenis_denda unik, jadi seeder ini memakai updateOrCreate.
     *
     * @var array<int, array{jenis_denda: string, perhitungan_denda: string, nilai_denda: float}>
     */
    public const ATURAN = [
        [
            'jenis_denda' => 'keterlambatan',
            'perhitungan_denda' => 'per_hari',
            'nilai_denda' => 50000,
        ],
        [
            'jenis_denda' => 'kerusakan_ringan',
            'perhitungan_denda' => 'persentase',
            'nilai_denda' => 20,
        ],
        [
            'jenis_denda' => 'kerusakan_berat',
            'perhitungan_denda' => 'persentase',
            'nilai_denda' => 50,
        ],
        [
            'jenis_denda' => 'kehilangan',
            'perhitungan_denda' => 'persentase',
            'nilai_denda' => 100,
        ],
    ];

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $pengubah = User::where('role', Role::Admin->value)->first()
            ?? User::where('role', Role::Owner->value)->first()
            ?? User::factory()->admin()->create();

        foreach (self::ATURAN as $aturan) {
            AturanDenda::updateOrCreate(
                ['jenis_denda' => $aturan['jenis_denda']],
                [
                    'perhitungan_denda' => $aturan['perhitungan_denda'],
                    'nilai_denda' => $aturan['nilai_denda'],
                    'user_id' => $pengubah->id,
                ],
            );
        }
    }
}
