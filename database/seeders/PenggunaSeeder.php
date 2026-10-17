<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class PenggunaSeeder extends Seeder
{
    /**
     * Akun bawaan untuk setiap peran (ERD: PENGGUNA).
     * Password semua akun: "password".
     *
     * @var array<int, array{name: string, email: string, role: Role, no_telepon: string}>
     */
    public const AKUN = [
        [
            'name' => 'Admin Outgear',
            'email' => 'admin@outgear.test',
            'role' => Role::Admin,
            'no_telepon' => '081100000001',
        ],
        [
            'name' => 'Petugas Outgear',
            'email' => 'petugas@outgear.test',
            'role' => Role::Petugas,
            'no_telepon' => '081100000002',
        ],
        [
            'name' => 'Owner Outgear',
            'email' => 'owner@outgear.test',
            'role' => Role::Owner,
            'no_telepon' => '081100000003',
        ],
        [
            'name' => 'Budi Pelanggan',
            'email' => 'pelanggan@outgear.test',
            'role' => Role::Customer,
            'no_telepon' => '081100000004',
        ],
    ];

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        foreach (self::AKUN as $akun) {
            $pengguna = User::firstOrCreate(
                ['email' => $akun['email']],
                [
                    'name' => $akun['name'],
                    'password' => 'password',
                    'role' => $akun['role'],
                    'no_telepon' => $akun['no_telepon'],
                    'status_akun' => 'aktif',
                ],
            );

            if (! $pengguna->hasVerifiedEmail()) {
                $pengguna->markEmailAsVerified();
            }
        }
    }
}
