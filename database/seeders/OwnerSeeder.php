<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class OwnerSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'owner@outgear.com'],
            [
                'name' => 'Owner OutGear',
                'password' => Hash::make('Owner123'),
                'role' => Role::Owner,
                'status_akun' => 'aktif',
                'no_telepon' => '081234567890',
            ]
        );
    }
}