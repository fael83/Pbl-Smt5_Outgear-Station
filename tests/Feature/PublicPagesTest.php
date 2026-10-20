<?php

use App\Models\Alat;
use App\Models\Kategori;

it('menampilkan halaman katalog tanpa login', function () {
    $this->withoutVite();

    $this->get(route('katalog'))
        ->assertOk()
        ->assertSee('Katalog Sewa Alat Outdoor')
        ->assertSee('Panduan Sewa');
});

it('menampilkan seluruh inventaris termasuk alat yang stoknya habis', function () {
    $this->withoutVite();

    $kategori = Kategori::factory()->create(['nama' => 'Alat Penerangan']);
    Alat::factory()->create([
        'kategori_id' => $kategori->id,
        'nama' => 'Lampu Tenda Tersedia',
        'deskripsi' => 'Lampu untuk area kemah',
        'tarif_per_hari' => 15000,
        'stok_total' => 6,
        'stok_tersedia' => 6,
        'stok_disewa' => 0,
        'stok_rusak' => 0,
    ]);
    Alat::factory()->create([
        'kategori_id' => $kategori->id,
        'nama' => 'Lampu Tenda Stok Habis',
        'stok_total' => 2,
        'stok_tersedia' => 0,
        'stok_disewa' => 2,
    ]);

    $this->get(route('katalog'))
        ->assertOk()
        ->assertSee('Lampu Tenda Tersedia')
        ->assertSee('Lampu Tenda Stok Habis')
        ->assertSee('Tersedia (6 unit)')
        ->assertSee('Rp 15.000')
        ->assertSee('Stok Habis')
        ->assertDontSee('Lampu untuk area kemah')
        ->assertDontSee('Nilai pengganti')
        ->assertDontSee('DISEWA')
        ->assertDontSee('#AL-')
        ->assertDontSee('Naturehike Cloud Up 2');
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
