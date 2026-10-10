<?php

it('renders the admin dashboard and its statistics', function () {
    $this->withoutVite();

    $response = $this->get('/admin');

    $response->assertOk()
        ->assertSee('Dashboard Operasional')
        ->assertSee('12 Transaksi')
        ->assertSee('5 Transaksi')
        ->assertSee('WA Bot Status');
});
