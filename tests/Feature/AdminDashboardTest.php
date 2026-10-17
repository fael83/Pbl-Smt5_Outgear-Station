<?php

it('renders the admin dashboard and its statistics', function () {
    $this->withoutVite();

    $response = $this->get('/admin');

    $response->assertOk()
        ->assertSee('Operasional OutGear Station')
        ->assertSee('TOTAL BOOKING AKTIF')
        ->assertSee('MENUNGGU VERIFIKASI')
        ->assertSee('Verifikasi Pembayaran');
});
