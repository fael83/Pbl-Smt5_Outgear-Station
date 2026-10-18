<?php

use App\Models\Alat;
use App\Models\DetailPemesanan;
use App\Models\Kategori;
use App\Models\Pemesanan;
use App\Models\User;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

/**
 * Buat satu alat dengan stok yang mudah diprediksi.
 *
 * @param  array<string, mixed>  $atribut
 */
function buatAlat(array $atribut = []): Alat
{
    $kategori = $atribut['kategori'] ?? Kategori::factory()->create(['nama' => 'Kategori '.fake()->unique()->word()]);
    unset($atribut['kategori']);

    $stokTotal = (int) ($atribut['stok_total'] ?? 5);
    $stokDisewa = (int) ($atribut['stok_disewa'] ?? 0);
    $stokRusak = (int) ($atribut['stok_rusak'] ?? 0);

    return Alat::factory()->create(array_merge([
        'kategori_id' => $kategori->id,
        'stok_total' => $stokTotal,
        'stok_disewa' => $stokDisewa,
        'stok_rusak' => $stokRusak,
        'stok_tersedia' => max(0, $stokTotal - $stokDisewa - $stokRusak),
    ], $atribut));
}

it('menampilkan data peralatan langsung dari database', function () {
    $alat = buatAlat(['nama' => 'Tenda Dome Kapasitas 4 Orang']);

    $this->get(route('admin.peralatan.index'))
        ->assertOk()
        ->assertSee('Data Peralatan')
        ->assertSee('Tenda Dome Kapasitas 4 Orang')
        ->assertSee('#AL-'.str_pad((string) $alat->id, 3, '0', STR_PAD_LEFT));
});

it('mencari alat berdasarkan nama', function () {
    buatAlat(['nama' => 'Tenda Dome Ultralight']);
    buatAlat(['nama' => 'Sleeping Bag Polar']);

    $this->get(route('admin.peralatan.index', ['search' => 'dome']))
        ->assertOk()
        ->assertSee('Tenda Dome Ultralight')
        ->assertDontSee('Sleeping Bag Polar');
});

it('mencari alat berdasarkan id yang ditampilkan (#AL-002)', function () {
    buatAlat(['nama' => 'Alat Pertama']);
    $kedua = buatAlat(['nama' => 'Alat Kedua']);

    $this->get(route('admin.peralatan.index', ['search' => '#AL-'.str_pad((string) $kedua->id, 3, '0', STR_PAD_LEFT)]))
        ->assertOk()
        ->assertSee('Alat Kedua')
        ->assertDontSee('Alat Pertama');
});

it('memfilter alat berdasarkan kategori', function () {
    $tenda = Kategori::factory()->create(['nama' => 'Tenda']);
    $tas = Kategori::factory()->create(['nama' => 'Carrier & Tas']);

    buatAlat(['nama' => 'Tenda Rei 4P', 'kategori' => $tenda]);
    buatAlat(['nama' => 'Carrier Eiger 60L', 'kategori' => $tas]);

    $this->get(route('admin.peralatan.index', ['kategori' => $tenda->id]))
        ->assertOk()
        ->assertSee('Tenda Rei 4P')
        ->assertDontSee('Carrier Eiger 60L');
});

it('memfilter alat yang stoknya habis', function () {
    buatAlat(['nama' => 'Tenda Stok Habis', 'stok_total' => 2, 'stok_disewa' => 2]);
    buatAlat(['nama' => 'Tenda Stok Aman', 'stok_total' => 4, 'stok_disewa' => 1]);

    $this->get(route('admin.peralatan.index', ['kondisi' => 'habis']))
        ->assertOk()
        ->assertSee('Tenda Stok Habis')
        ->assertDontSee('Tenda Stok Aman');
});

it('memfilter alat yang memiliki unit rusak', function () {
    buatAlat(['nama' => 'Kompor Rusak', 'stok_total' => 3, 'stok_rusak' => 1]);
    buatAlat(['nama' => 'Kompor Mulus', 'stok_total' => 3]);

    $this->get(route('admin.peralatan.index', ['kondisi' => 'rusak']))
        ->assertOk()
        ->assertSee('Kompor Rusak')
        ->assertDontSee('Kompor Mulus');
});

it('mengabaikan nilai per_page di luar pilihan', function () {
    buatAlat(['nama' => 'Alat Uji Per Page']);

    $response = $this->get(route('admin.peralatan.index', ['per_page' => 999]));

    $response->assertOk();
    expect($response->viewData('daftarAlat')->perPage())->toBe(10);
});

it('menyimpan alat baru dan menghitung stok tersedia', function () {
    $kategori = Kategori::factory()->create(['nama' => 'Tenda']);

    $this->post(route('admin.peralatan.store'), [
        'nama' => 'Tenda Nordisk Telemark 2',
        'kategori_id' => $kategori->id,
        'deskripsi' => 'Tenda ultralight dua orang',
        'tarif_per_hari' => 85000,
        'nilai_pengganti' => 1500000,
        'stok_total' => 6,
        'stok_rusak' => 1,
    ])->assertRedirect(route('admin.peralatan.index'))
        ->assertSessionHas('success');

    $alat = Alat::query()->where('nama', 'Tenda Nordisk Telemark 2')->firstOrFail();

    expect($alat->kategori_id)->toBe($kategori->id)
        ->and($alat->stok_total)->toBe(6)
        ->and($alat->stok_disewa)->toBe(0)
        ->and($alat->stok_rusak)->toBe(1)
        ->and($alat->stok_tersedia)->toBe(5);
});

it('menolak stok total yang lebih kecil dari unit rusak', function () {
    $kategori = Kategori::factory()->create(['nama' => 'Tenda']);

    $this->from(route('admin.peralatan.index'))
        ->post(route('admin.peralatan.store'), [
            'nama' => 'Tenda Stok Tidak Masuk Akal',
            'kategori_id' => $kategori->id,
            'tarif_per_hari' => 50000,
            'nilai_pengganti' => 900000,
            'stok_total' => 1,
            'stok_rusak' => 3,
        ])
        ->assertSessionHasErrors('stok_total');

    $this->assertDatabaseCount('alat', 0);
});

it('menolak nama alat yang sudah dipakai', function () {
    $kategori = Kategori::factory()->create(['nama' => 'Tenda']);
    buatAlat(['nama' => 'Tenda Yang Sudah Ada', 'kategori' => $kategori]);

    $this->from(route('admin.peralatan.index'))
        ->post(route('admin.peralatan.store'), [
            'nama' => 'Tenda Yang Sudah Ada',
            'kategori_id' => $kategori->id,
            'tarif_per_hari' => 50000,
            'nilai_pengganti' => 900000,
            'stok_total' => 2,
            'stok_rusak' => 0,
        ])
        ->assertSessionHasErrors('nama');

    $this->assertDatabaseCount('alat', 1);
});

it('memperbarui alat tanpa mengubah jumlah unit yang disewa', function () {
    $kategori = Kategori::factory()->create(['nama' => 'Tenda']);
    $alat = buatAlat([
        'nama' => 'Tenda Lama',
        'kategori' => $kategori,
        'stok_total' => 10,
        'stok_disewa' => 2,
        'stok_tersedia' => 8,
    ]);

    $this->put(route('admin.peralatan.update', $alat), [
        'nama' => 'Tenda Baru',
        'kategori_id' => $kategori->id,
        'deskripsi' => 'Sudah diperbarui',
        'tarif_per_hari' => 95000,
        'nilai_pengganti' => 1750000,
        'stok_total' => 10,
        'stok_rusak' => 1,
    ])->assertRedirect(route('admin.peralatan.index'))
        ->assertSessionHas('success');

    $alat->refresh();

    expect($alat->nama)->toBe('Tenda Baru')
        ->and($alat->stok_disewa)->toBe(2)
        ->and($alat->stok_rusak)->toBe(1)
        ->and($alat->stok_tersedia)->toBe(7);
});
it('menolak update bila stok total lebih kecil dari unit disewa dan rusak', function () {
    $kategori = Kategori::factory()->create(['nama' => 'Tenda']);
    $alat = buatAlat([
        'nama' => 'Tenda Sedang Disewa',
        'kategori' => $kategori,
        'stok_total' => 5,
        'stok_disewa' => 3,
    ]);

    $this->from(route('admin.peralatan.index'))
        ->put(route('admin.peralatan.update', $alat), [
            'nama' => 'Tenda Sedang Disewa',
            'kategori_id' => $kategori->id,
            'tarif_per_hari' => 60000,
            'nilai_pengganti' => 1000000,
            'stok_total' => 3,
            'stok_rusak' => 1,
        ])
        ->assertSessionHasErrors('stok_total');

    expect($alat->refresh()->stok_total)->toBe(5);
});

it('menghapus alat yang belum pernah dipakai pada pemesanan', function () {
    $alat = buatAlat(['nama' => 'Alat Akan Dihapus']);

    $this->delete(route('admin.peralatan.destroy', $alat))
        ->assertRedirect(route('admin.peralatan.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseMissing('alat', ['id' => $alat->id]);
});

it('menolak menghapus alat yang sudah dipakai pada pemesanan', function () {
    $alat = buatAlat(['nama' => 'Alat Sudah Terpakai']);
    DetailPemesanan::factory()->untuk(Pemesanan::factory()->create(), $alat)->create();

    $this->delete(route('admin.peralatan.destroy', $alat))
        ->assertRedirect(route('admin.peralatan.index'))
        ->assertSessionHas('error');

    $this->assertDatabaseHas('alat', ['id' => $alat->id]);
});

it('mempertahankan filter aktif setelah data disimpan', function () {
    $kategori = Kategori::factory()->create(['nama' => 'Tenda']);

    $this->post(route('admin.peralatan.store'), [
        'nama' => 'Tenda Filter Tetap',
        'kategori_id' => $kategori->id,
        'tarif_per_hari' => 45000,
        'nilai_pengganti' => 750000,
        'stok_total' => 3,
        'stok_rusak' => 0,
        'search' => 'tenda',
        'kondisi' => 'tersedia',
        'per_page' => 25,
    ])->assertRedirectContains('search=tenda')
        ->assertRedirectContains('kondisi=tersedia')
        ->assertRedirectContains('per_page=25');
});

/*
|--------------------------------------------------------------------------
| Endpoint JSON (pencarian, filter, pagination, dan simpan tanpa reload)
|--------------------------------------------------------------------------
*/

it('mengirim tabel sebagai json agar pencarian tidak memuat ulang halaman', function () {
    buatAlat(['nama' => 'Tenda Dome Ultralight']);
    buatAlat(['nama' => 'Sleeping Bag Polar']);

    $response = $this->getJson(route('admin.peralatan.tabel', [
        'search' => 'dome',
        'per_page' => 25,
    ]));

    $response->assertOk()
        ->assertJsonStructure([
            'html',
            'items',
            'ringkasan' => ['total_alat', 'total_unit', 'unit_tersedia', 'unit_disewa', 'unit_rusak'],
            'url',
        ])
        ->assertJsonPath('ringkasan.total_alat', 2)
        ->assertJsonCount(1, 'items')
        ->assertJsonPath('items.0.nama', 'Tenda Dome Ultralight');

    expect($response->json('html'))->toContain('Tenda Dome Ultralight')
        ->and($response->json('html'))->not->toContain('Sleeping Bag Polar')
        ->and($response->json('url'))->toContain('search=dome')
        ->and($response->json('url'))->toContain('per_page=25');
});

it('membagi tabel per halaman pada endpoint json', function () {
    foreach (range(1, 12) as $nomor) {
        buatAlat(['nama' => 'Alat Uji '.$nomor]);
    }

    $response = $this->getJson(route('admin.peralatan.tabel', ['page' => 2]));

    $response->assertOk()
        ->assertJsonCount(2, 'items')
        ->assertJsonPath('ringkasan.total_alat', 12);

    expect($response->json('url'))->toContain('page=2')
        ->and($response->json('html'))->toContain('page=1');
});

it('membalas json berisi baris tabel saat alat baru disimpan lewat ajax', function () {
    $kategori = Kategori::factory()->create(['nama' => 'Tenda']);

    $response = $this->postJson(route('admin.peralatan.store'), [
        'nama' => 'Tenda Ajax Baru',
        'kategori_id' => $kategori->id,
        'tarif_per_hari' => 65000,
        'nilai_pengganti' => 1100000,
        'stok_total' => 4,
        'stok_rusak' => 0,
    ]);

    $response->assertOk()
        ->assertJsonPath('message', 'Alat "Tenda Ajax Baru" berhasil ditambahkan.')
        ->assertJsonPath('item.nama', 'Tenda Ajax Baru')
        ->assertJsonPath('ringkasan.total_alat', 1);

    expect($response->json('baris'))->toContain('data-alat-id="'.$response->json('id').'"');
});

it('membalas pesan validasi json saat penyimpanan lewat ajax gagal', function () {
    $kategori = Kategori::factory()->create(['nama' => 'Tenda']);

    $this->postJson(route('admin.peralatan.store'), [
        'nama' => '',
        'kategori_id' => $kategori->id,
        'tarif_per_hari' => 50000,
        'nilai_pengganti' => 900000,
        'stok_total' => 2,
        'stok_rusak' => 0,
    ])->assertStatus(422)
        ->assertJsonValidationErrors(['nama']);

    // Aturan stok hanya dicek setelah validasi dasar lolos (lihat AlatRequest).
    $this->postJson(route('admin.peralatan.store'), [
        'nama' => 'Tenda Stok Ajax',
        'kategori_id' => $kategori->id,
        'tarif_per_hari' => 50000,
        'nilai_pengganti' => 900000,
        'stok_total' => 1,
        'stok_rusak' => 3,
    ])->assertStatus(422)
        ->assertJsonValidationErrors(['stok_total']);

    $this->assertDatabaseCount('alat', 0);
});

it('membalas json berisi baris tabel terbaru saat update lewat ajax', function () {
    $kategori = Kategori::factory()->create(['nama' => 'Tenda']);
    $alat = buatAlat([
        'nama' => 'Tenda Ajax Lama',
        'kategori' => $kategori,
        'stok_total' => 5,
        'stok_disewa' => 2,
    ]);

    $response = $this->putJson(route('admin.peralatan.update', $alat), [
        'nama' => 'Tenda Ajax Terbaru',
        'kategori_id' => $kategori->id,
        'tarif_per_hari' => 70000,
        'nilai_pengganti' => 1200000,
        'stok_total' => 6,
        'stok_rusak' => 1,
    ]);

    $response->assertOk()
        ->assertJsonPath('item.nama', 'Tenda Ajax Terbaru')
        ->assertJsonPath('item.stok_total', 6)
        ->assertJsonPath('item.stok_rusak', 1)
        ->assertJsonPath('item.stok_disewa', 2);

    expect($response->json('baris'))->toContain('Tenda Ajax Terbaru');
});

it('membuka kembali modal edit dengan pesan validasi saat update gagal', function () {
    $kategori = Kategori::factory()->create(['nama' => 'Tenda']);
    $alat = buatAlat([
        'nama' => 'Tenda Validasi',
        'kategori' => $kategori,
        'stok_total' => 4,
        'stok_disewa' => 2,
    ]);

    $this->followingRedirects()
        ->from(route('admin.peralatan.index'))
        ->put(route('admin.peralatan.update', $alat), [
            'nama' => 'Tenda Validasi',
            'kategori_id' => $kategori->id,
            'tarif_per_hari' => 50000,
            'nilai_pengganti' => 900000,
            'stok_total' => 1,
            'stok_rusak' => 0,
            'form_mode' => 'edit',
            'alat_id' => $alat->id,
        ])
        ->assertOk()
        ->assertSee('Periksa kembali data berikut')
        ->assertSee('Stok total minimal 2 unit');
});

it('membalas json saat alat dihapus lewat ajax', function () {
    $alat = buatAlat(['nama' => 'Alat Hapus Ajax']);

    $this->deleteJson(route('admin.peralatan.destroy', $alat))
        ->assertOk()
        ->assertJsonPath('id', $alat->id)
        ->assertJsonPath('ringkasan.total_alat', 0);

    $this->assertDatabaseMissing('alat', ['id' => $alat->id]);
});

it('menolak hapus lewat ajax untuk alat yang sudah dipakai pemesanan', function () {
    $alat = buatAlat(['nama' => 'Alat Terpakai Ajax']);
    DetailPemesanan::factory()->untuk(Pemesanan::factory()->create(), $alat)->create();

    $this->deleteJson(route('admin.peralatan.destroy', $alat))
        ->assertStatus(422)
        ->assertJsonPath(
            'message',
            'Alat "Alat Terpakai Ajax" tidak bisa dihapus karena sudah dipakai pada pemesanan.',
        );

    $this->assertDatabaseHas('alat', ['id' => $alat->id]);
});
