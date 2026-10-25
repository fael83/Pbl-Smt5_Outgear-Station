<?php

use App\Models\Pemesanan;
use App\Models\User;

beforeEach(function () {
    // Route admin hanya untuk tim internal; test ini memakai admin.
    $this->actingAs(User::factory()->admin()->create());
});

/**
 * Buat satu akun pelanggan (ERD: PENGGUNA dengan peran customer).
 *
 * @param  array<string, mixed>  $atribut
 */
function buatPelanggan(array $atribut = []): User
{
    return User::factory()->customer()->create($atribut);
}

it('menampilkan data pelanggan langsung dari database', function () {
    $pelanggan = buatPelanggan([
        'name' => 'Rizky Pratama',
        'email' => 'rizky.pratama@example.com',
        'no_telepon' => '081234567890',
    ]);

    $this->get(route('admin.pelanggan'))
        ->assertOk()
        ->assertSee('Data Pelanggan')
        ->assertSee('Rizky Pratama')
        ->assertSee('Email')
        ->assertSee('rizky.pratama@example.com')
        ->assertSee('081234567890')
        ->assertDontSee('Bergabung')
        ->assertSee('Edit')
        ->assertSee('Hapus')
        ->assertDontSee('Nonaktifkan')
        ->assertSee(route('admin.pelanggan.update', $pelanggan), false)
        ->assertSee(route('admin.pelanggan.destroy', $pelanggan), false)
        ->assertSee('#PL-'.str_pad((string) $pelanggan->id, 3, '0', STR_PAD_LEFT));
});

it('menampilkan semua akun pelanggan dalam halaman pertama', function () {
    User::factory()->customer()->count(7)->sequence(
        ['name' => 'Pelanggan Satu', 'email' => 'satu@example.test'],
        ['name' => 'Pelanggan Dua', 'email' => 'dua@example.test'],
        ['name' => 'Pelanggan Tiga', 'email' => 'tiga@example.test'],
        ['name' => 'Pelanggan Empat', 'email' => 'empat@example.test'],
        ['name' => 'Pelanggan Lima', 'email' => 'lima@example.test'],
        ['name' => 'Pelanggan Enam', 'email' => 'enam@example.test'],
        ['name' => 'Pelanggan Tujuh', 'email' => 'tujuh@example.test'],
    )->create();

    $response = $this->get(route('admin.pelanggan'))->assertOk();

    $response->assertSee('7 pelanggan');
    foreach ([
        'Satu' => 'satu',
        'Dua' => 'dua',
        'Tiga' => 'tiga',
        'Empat' => 'empat',
        'Lima' => 'lima',
        'Enam' => 'enam',
        'Tujuh' => 'tujuh',
    ] as $nama => $email) {
        $response->assertSee("Pelanggan {$nama}")
            ->assertSee("{$email}@example.test");
    }
});

it('tidak menampilkan akun staf internal di daftar pelanggan', function () {
    User::factory()->admin()->create(['name' => 'Admin Gudang']);
    User::factory()->petugas()->create(['name' => 'Petugas Loket']);
    User::factory()->owner()->create(['name' => 'Owner Toko']);
    buatPelanggan(['name' => 'Pelanggan Nyata']);

    $this->get(route('admin.pelanggan'))
        ->assertOk()
        ->assertSee('Pelanggan Nyata')
        ->assertDontSee('Admin Gudang')
        ->assertDontSee('Petugas Loket')
        ->assertDontSee('Owner Toko');
});

it('mencari pelanggan berdasarkan nama, email, dan nomor telepon', function (string $kunci) {
    buatPelanggan([
        'name' => 'Siti Aminah',
        'email' => 'siti.aminah@example.com',
        'no_telepon' => '081211112222',
    ]);
    buatPelanggan([
        'name' => 'Budi Santoso',
        'email' => 'budi.santoso@example.com',
        'no_telepon' => '081233334444',
    ]);

    $this->get(route('admin.pelanggan', ['search' => $kunci]))
        ->assertOk()
        ->assertSee('Siti Aminah')
        ->assertDontSee('Budi Santoso');
})->with(['siti', 'siti.aminah@example.com', '081211112222']);

it('mencari pelanggan berdasarkan id yang ditampilkan (#PL-002)', function () {
    buatPelanggan(['name' => 'Pelanggan Pertama']);
    $kedua = buatPelanggan(['name' => 'Pelanggan Kedua']);

    $this->get(route('admin.pelanggan', [
        'search' => '#PL-'.str_pad((string) $kedua->id, 3, '0', STR_PAD_LEFT),
    ]))
        ->assertOk()
        ->assertSee('Pelanggan Kedua')
        ->assertDontSee('Pelanggan Pertama');
});

it('memfilter pelanggan berdasarkan status akun', function () {
    buatPelanggan(['name' => 'Pelanggan Aktif Saja', 'status_akun' => 'aktif']);
    buatPelanggan(['name' => 'Pelanggan Nonaktif Saja', 'status_akun' => 'nonaktif']);

    $this->get(route('admin.pelanggan', ['status' => 'aktif']))
        ->assertOk()
        ->assertSee('Pelanggan Aktif Saja')
        ->assertDontSee('Pelanggan Nonaktif Saja');

    $this->get(route('admin.pelanggan', ['status' => 'nonaktif']))
        ->assertOk()
        ->assertSee('Pelanggan Nonaktif Saja')
        ->assertDontSee('Pelanggan Aktif Saja');
});

it('tetap menerapkan filter status saat kata kunci berisi angka', function () {
    buatPelanggan(['name' => 'Andi 2 Aktif', 'status_akun' => 'aktif']);
    buatPelanggan(['name' => 'Andi 2 Nonaktif', 'status_akun' => 'nonaktif']);

    // Dulu orWhereKey() berada di luar grup sehingga hasil pencarian nama/ID
    // bisa lolos dari filter status.
    $this->get(route('admin.pelanggan', ['search' => '2', 'status' => 'aktif']))
        ->assertOk()
        ->assertSee('Andi 2 Aktif')
        ->assertDontSee('Andi 2 Nonaktif');
});

it('menghitung ringkasan pelanggan dari database', function () {
    buatPelanggan(['status_akun' => 'aktif']);
    buatPelanggan(['status_akun' => 'aktif']);
    buatPelanggan(['status_akun' => 'nonaktif']);

    $this->getJson(route('admin.pelanggan.tabel'))
        ->assertOk()
        ->assertJsonPath('ringkasan.total_pelanggan', 3)
        ->assertJsonPath('ringkasan.pelanggan_aktif', 2)
        ->assertJsonPath('ringkasan.pelanggan_nonaktif', 1)
        ->assertJsonPath('ringkasan.pelanggan_baru', 3);
});

it('menampilkan jumlah booking milik pelanggan', function () {
    $pelanggan = buatPelanggan(['name' => 'Pelanggan Rajin']);

    Pemesanan::factory()->count(2)->for($pelanggan, 'pelanggan')->create();

    $response = $this->getJson(route('admin.pelanggan.tabel'))->assertOk();

    expect($response->json('items.0.jumlah_pemesanan'))->toBe(2);
    expect($response->json('html'))->toContain('2 booking');
});

it('membalas json berisi html tabel pada endpoint tabel', function () {
    buatPelanggan(['name' => 'Pelanggan Tabel']);

    $response = $this->getJson(route('admin.pelanggan.tabel'))->assertOk();

    expect($response->json('html'))
        ->toContain('Daftar Pelanggan')
        ->toContain('Pelanggan Tabel');

    expect($response->json('items.0.nama'))->toBe('Pelanggan Tabel');
});

it('membatasi jumlah baris per halaman', function () {
    User::factory()->customer()->count(12)->create();

    $response = $this->getJson(route('admin.pelanggan.tabel', ['per_page' => 10, 'page' => 2]))
        ->assertOk();

    expect($response->json('items'))->toHaveCount(2);
    expect($response->json('url'))->toContain('page=2');
});

it('menonaktifkan pelanggan lewat ajax', function () {
    $pelanggan = buatPelanggan(['name' => 'Akun Uji Nonaktif', 'status_akun' => 'aktif']);

    $response = $this->patchJson(route('admin.pelanggan.status', $pelanggan), [
        'status_akun' => 'nonaktif',
    ]);

    $response->assertOk()
        ->assertJsonPath('item.status_akun', 'nonaktif')
        ->assertJsonPath('ringkasan.pelanggan_aktif', 0)
        ->assertJsonPath('ringkasan.pelanggan_nonaktif', 1);

    expect($response->json('baris'))
        ->toContain('Akun Uji Nonaktif')
        ->toContain('Edit')
        ->toContain('Hapus')
        ->not->toContain('Nonaktifkan');

    $this->assertDatabaseHas('users', [
        'id' => $pelanggan->id,
        'status_akun' => 'nonaktif',
    ]);
});

it('mengaktifkan kembali pelanggan tanpa javascript', function () {
    $pelanggan = buatPelanggan(['status_akun' => 'nonaktif']);

    $this->patch(route('admin.pelanggan.status', $pelanggan), ['status_akun' => 'aktif'])
        ->assertRedirect(route('admin.pelanggan'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('users', [
        'id' => $pelanggan->id,
        'status_akun' => 'aktif',
    ]);
});

it('menolak status akun yang tidak dikenal', function () {
    $pelanggan = buatPelanggan(['status_akun' => 'aktif']);

    $this->patchJson(route('admin.pelanggan.status', $pelanggan), ['status_akun' => 'dihapus'])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['status_akun']);

    $this->assertDatabaseHas('users', [
        'id' => $pelanggan->id,
        'status_akun' => 'aktif',
    ]);
});

it('menolak mengubah status akun staf internal', function () {
    $petugas = User::factory()->petugas()->create();

    $this->patchJson(route('admin.pelanggan.status', $petugas), ['status_akun' => 'nonaktif'])
        ->assertNotFound();

    $this->assertDatabaseHas('users', [
        'id' => $petugas->id,
        'status_akun' => 'aktif',
    ]);
});

it('mengubah data pelanggan tanpa mengubah status akun atau peran', function () {
    $pelanggan = buatPelanggan([
        'name' => 'Nama Lama',
        'email' => 'lama@example.test',
        'status_akun' => 'nonaktif',
    ]);

    $response = $this->put(route('admin.pelanggan.update', $pelanggan), [
        'name' => 'Nama Baru',
        'email' => 'baru@example.test',
        'no_telepon' => '081299887766',
        'role' => 'admin',
        'status_akun' => 'aktif',
    ]);

    $response->assertRedirect(route('admin.pelanggan'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('users', [
        'id' => $pelanggan->id,
        'name' => 'Nama Baru',
        'email' => 'baru@example.test',
        'no_telepon' => '081299887766',
        'role' => 'customer',
        'status_akun' => 'nonaktif',
    ]);
});

it('menolak email pelanggan yang sudah dipakai saat mengedit', function () {
    $pelanggan = buatPelanggan(['email' => 'pelanggan.lama@example.test']);
    buatPelanggan(['email' => 'pelanggan.lain@example.test']);

    $response = $this->from(route('admin.pelanggan'))
        ->put(route('admin.pelanggan.update', $pelanggan), [
            'name' => 'Nama Baru',
            'email' => 'pelanggan.lain@example.test',
            'no_telepon' => '081299887766',
        ]);

    $response->assertRedirect(route('admin.pelanggan'))
        ->assertSessionHasErrors('email');

    $this->assertDatabaseHas('users', [
        'id' => $pelanggan->id,
        'email' => 'pelanggan.lama@example.test',
    ]);
});

it('menolak mengedit akun staf dari halaman pelanggan', function () {
    $admin = User::factory()->admin()->create();
    $petugas = User::factory()->petugas()->create();

    $this->actingAs($admin)->put(route('admin.pelanggan.update', $petugas), [
        'name' => 'Nama Diubah',
        'email' => $petugas->email,
    ])->assertNotFound();

    expect($petugas->refresh()->name)->not->toBe('Nama Diubah');
});

it('menghapus pelanggan yang tidak memiliki riwayat terkait', function () {
    $pelanggan = buatPelanggan(['name' => 'Akun Tanpa Booking']);

    $this->delete(route('admin.pelanggan.destroy', $pelanggan))
        ->assertRedirect(route('admin.pelanggan'))
        ->assertSessionHas('success');

    $this->assertDatabaseMissing('users', ['id' => $pelanggan->id]);
});

it('menjaga akun pelanggan yang memiliki riwayat booking', function () {
    $pelanggan = buatPelanggan(['name' => 'Akun Dengan Booking']);
    Pemesanan::factory()->for($pelanggan, 'pelanggan')->create();

    $this->delete(route('admin.pelanggan.destroy', $pelanggan))
        ->assertRedirect(route('admin.pelanggan'))
        ->assertSessionHas('error');

    $this->assertDatabaseHas('users', ['id' => $pelanggan->id]);
});

it('mengekspor pelanggan sesuai filter sebagai csv', function () {
    buatPelanggan([
        'name' => 'Ekspor Aktif',
        'email' => 'ekspor.aktif@example.com',
        'status_akun' => 'aktif',
    ]);
    buatPelanggan(['name' => 'Ekspor Nonaktif', 'status_akun' => 'nonaktif']);

    $response = $this->get(route('admin.pelanggan.ekspor', ['status' => 'aktif']))->assertOk();

    expect($response->headers->get('content-disposition'))->toContain('pelanggan-');
    expect($response->streamedContent())
        ->toContain('Nama')
        ->toContain('Ekspor Aktif')
        ->toContain('ekspor.aktif@example.com')
        ->not->toContain('Ekspor Nonaktif');
});
