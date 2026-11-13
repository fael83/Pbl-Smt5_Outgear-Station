<?php

use App\Enums\Role;
use App\Models\User;

it('forbids petugas from opening the admin dashboard', function () {
    $petugas = User::make([
        'name' => 'Petugas Gudang',
        'email' => 'petugas@example.test',
        'password' => 'password',
        'role' => Role::Petugas,
    ]);

    $this->actingAs($petugas)
        ->get(route('admin.dashboard'))
        ->assertForbidden();
});
