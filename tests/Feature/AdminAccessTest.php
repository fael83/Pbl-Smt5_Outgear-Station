<?php

use App\Models\Alat;
use App\Models\Kategori;
use App\Models\User;

beforeEach(function () {
    $this->kategori = Kategori::factory()->create(['nama' => 'Tenda']);
});

/**
 * Payload valid untuk store/update alat.
 *
 * @return array<string, mixed>
 */
function dataAlatValid(Kategori $kategori, array $ubah = []): array
{
    return array_merge([
        'nama' => 'Tenda Uji Akses',
        'kategori_id' => $kategori->id,
        'deskripsi' => 'Tenda untuk pengujian otorisasi',
        'tarif_per_hari' => 50000,
        'nilai_pengganti' => 900000,
        'stok_total' => 3,
        'stok_rusak' => 0,
    ], $ubah);
}

it('mengalihkan tamu ke halaman login', function () {
    $this->get(route('admin.peralatan.index'))->assertRedirect('/login');
    $this->get(route('admin.peralatan.tabel'))->assertRedirect('/login');
    $this->get(route('admin.dashboard'))->assertRedirect('/login');
});

it('menolak pelanggan mengakses seluruh route admin', function () {
    $pelanggan = User::factory()->customer()->create();
    $alat = Alat::factory()->for($this->kategori)->create();

    $this->actingAs($pelanggan)->get(route('admin.peralatan.index'))->assertForbidden();
    $this->actingAs($pelanggan)->getJson(route('admin.peralatan.tabel'))->assertForbidden();
    $this->actingAs($pelanggan)->post(route('admin.peralatan.store'), dataAlatValid($this->kategori))->assertForbidden();
    $this->actingAs($pelanggan)->put(route('admin.peralatan.update', $alat), dataAlatValid($this->kategori))->assertForbidden();
    $this->actingAs($pelanggan)->delete(route('admin.peralatan.destroy', $alat))->assertForbidden();
});

it('mengizinkan :peran melihat data peralatan tetapi tidak menulis', function (string $peran) {
    $pengguna = User::factory()->{$peran}()->create();
    $alat = Alat::factory()->for($this->kategori)->create();

    $this->actingAs($pengguna)->get(route('admin.peralatan.index'))->assertOk();
    $this->actingAs($pengguna)->getJson(route('admin.peralatan.tabel'))->assertOk();

    $this->actingAs($pengguna)->post(route('admin.peralatan.store'), dataAlatValid($this->kategori))->assertForbidden();
    $this->actingAs($pengguna)->put(route('admin.peralatan.update', $alat), dataAlatValid($this->kategori))->assertForbidden();
    $this->actingAs($pengguna)->delete(route('admin.peralatan.destroy', $alat))->assertForbidden();

    $this->assertDatabaseCount('alat', 1);
})->with(['petugas', 'owner']);

it('mengizinkan admin melihat dan mengelola data peralatan', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->get(route('admin.peralatan.index'))->assertOk();
    $this->actingAs($admin)->getJson(route('admin.peralatan.tabel'))->assertOk();

    $this->actingAs($admin)
        ->post(route('admin.peralatan.store'), dataAlatValid($this->kategori))
        ->assertRedirect(route('admin.peralatan.index'));

    $alat = Alat::query()->where('nama', 'Tenda Uji Akses')->firstOrFail();

    $this->actingAs($admin)
        ->put(route('admin.peralatan.update', $alat), dataAlatValid($this->kategori, ['nama' => 'Tenda Uji Akses Diubah']))
        ->assertRedirect(route('admin.peralatan.index'));

    $this->actingAs($admin)
        ->delete(route('admin.peralatan.destroy', $alat))
        ->assertRedirect(route('admin.peralatan.index'));

    $this->assertDatabaseCount('alat', 0);
});

it('mengarahkan /dashboard sesuai peran pengguna', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->get('/dashboard')->assertRedirect(route('admin.dashboard'));

    $this->actingAs(User::factory()->owner()->create())
        ->get('/dashboard')->assertRedirect(route('admin.dashboard'));

    $this->actingAs(User::factory()->petugas()->create())
        ->get('/dashboard')->assertRedirect(route('admin.dashboard'));

    $this->actingAs(User::factory()->customer()->create())
        ->get('/dashboard')->assertRedirect('/katalog');
});
