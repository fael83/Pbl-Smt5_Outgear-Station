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
        ->assertSee('aria-label="Tampilkan semua peralatan"', false)
        ->assertSee('@click="kategoriAktif = \'semua\'; barangDiperluas = false"', false)
        ->assertSee('data-kategori="'.$kategori->id.'"', false)
        ->assertSee('aria-label="Cari barang di katalog"', false)
        ->assertSee('x-model.trim="kataKunci"', false)
        ->assertSee('cocokDenganPencarian('.$alat->id.')', false)
        ->assertDontSee('Tampilkan lebih banyak')
        ->assertDontSee('tenda-camp-6.jpg')
        ->assertSee('masukkanDariDetail()', false)
        ->assertSee('submitCheckout()', false);
});

it('allows customers to expand and collapse the product list', function () {
    $kategori = Kategori::create(['nama' => 'Tenda']);

    foreach (range(1, 15) as $index) {
        Alat::create([
            'kategori_id' => $kategori->id,
            'nama' => 'Alat '.$index,
            'tarif_per_hari' => 25000,
            'stok_total' => 1,
            'stok_tersedia' => 1,
        ]);
    }

    $this->get('/katalog')
        ->assertOk()
        ->assertSee('Tampilkan lebih banyak')
        ->assertSee('Tampilkan lebih sedikit', false)
        ->assertSee('@click="barangDiperluas = !barangDiperluas"', false)
        ->assertSee('barangDiperluas || kataKunci.trim() !== \'\' || 12 < 12', false)
        ->assertSee('cocokDenganPencarian('.$kategori->alat()->first()->id.')', false);
});
