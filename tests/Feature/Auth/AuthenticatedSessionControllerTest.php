<?php

use App\Enums\Role;
use App\Models\User;

it('redirects petugas to the petugas dashboard after login', function () {
    $petugas = User::create([
        'name' => 'Petugas Gudang',
        'email' => 'petugas-login@example.test',
        'password' => 'password',
        'role' => Role::Petugas,
        'status_akun' => 'aktif',
    ]);

    $this->post(route('login'), [
        'email' => $petugas->email,
        'password' => 'password',
    ])->assertRedirect(route('petugas.dashboard'));
});
