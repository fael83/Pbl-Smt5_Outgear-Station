<?php

use App\Models\Alat;
use App\Models\Kategori;

it('allows guests to view the katalog', function () {
    $this->get('/katalog')
        ->assertOk()
        ->assertViewIs('customer.katalog');
});

it('shows Supabase photos and opens item details before checkout', function () {
    config([
        'services.supabase.url' => 'https://storage.example.test',
        'services.supabase.bucket' => 'outgear',
        'services.supabase.service_key' => 'test-key',
    ]);

    $kategori = Kategori::create(['nama' => 'Tenda']);
    $alat = Alat::create([
        'kategori_id' => $kategori->id,
        'nama' => 'Tenda Dome',
        'foto' => 'alat/tenda-dome.jpg',
        'tarif_per_hari' => 25000,
        'stok_total' => 3,
        'stok_tersedia' => 3,
    ]);

    $this->get('/katalog')
        ->assertOk()
        ->assertSee('https://storage.example.test/storage/v1/object/public/outgear/alat/tenda-dome.jpg')
        ->assertSee('bukaDetail('.$alat->id.')', false)
        ->assertSee('Sewa Barang')
        ->assertSee('data-kategori="'.$kategori->id.'"', false)
        ->assertSee('kategoriAktif === \'semua\' || kategoriAktif === '.$kategori->id, false)
        ->assertDontSee('tenda-camp-6.jpg')
        ->assertSee('masukkanDariDetail()', false)
        ->assertSee('submitCheckout()', false);
});
