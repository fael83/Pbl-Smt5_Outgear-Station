<?php

use App\Models\User;

it('renders the admin fines page with working filters', function () {
    $this->withoutVite()
        ->actingAs(User::factory()->admin()->create())
        ->get(route('admin.denda.index'))
        ->assertOk()
        ->assertSee('Daftar Tagihan Denda')
        ->assertSee('href="'.route('admin.denda.index', ['status' => 'lunas']).'"', false);
});

it('renders the admin bookings page without an unsupported create form', function () {
    $this->withoutVite()
        ->actingAs(User::factory()->admin()->create())
        ->get(route('admin.pemesanan'))
        ->assertOk()
        ->assertSee('Data Pemesanan')
        ->assertSee('Daftar Pemesanan');
});
