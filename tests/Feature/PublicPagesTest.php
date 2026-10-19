<?php

it('menampilkan halaman katalog tanpa login', function () {
    $this->withoutVite();

    $this->get(route('katalog'))
        ->assertOk()
        ->assertSee('Katalog Sewa Alat Outdoor')
        ->assertSee('Panduan Sewa');
});

it('menampilkan halaman panduan dan booking tanpa login', function () {
    $this->withoutVite();

    $this->get(route('panduan'))
        ->assertOk()
        ->assertSee('Panduan Penyewaan Alat');

    $this->get(route('booking.saya'))
        ->assertOk()
        ->assertSee('Status Booking');
});

it('mengarahkan tamu dari /dashboard ke katalog', function () {
    $this->get(route('dashboard'))->assertRedirect('/katalog');
});
