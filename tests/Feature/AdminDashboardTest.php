<?php

use App\Models\User;

beforeEach(function () {
    // Dashboard admin berada di balik middleware auth + role.
    $this->actingAs(User::factory()->admin()->create());
});

it('renders the admin dashboard and its statistics', function () {
    $this->withoutVite();

    $response = $this->get('/admin');

    // Asersi lama memakai teks yang tidak lagi ada di view (judul kartu
    // sekarang "Booking Aktif" dan "Perlu Verifikasi").
    $response->assertOk()
        ->assertSee('Dashboard Operasional')
        ->assertSee('Booking Aktif')
        ->assertSee('Perlu Verifikasi')
        ->assertSee('Verifikasi Pembayaran');
});
