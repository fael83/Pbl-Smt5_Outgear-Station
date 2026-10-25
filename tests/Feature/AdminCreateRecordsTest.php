<?php

use App\Models\Alat;
use App\Models\Kategori;
use App\Models\Pemesanan;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('creates a customer with a hashed password and customer role', function () {
    $admin = User::factory()->admin()->create();
    $namaPelanggan = 'Pelanggan <script>alert(1)</script>';

    $response = $this->actingAs($admin)->post(route('admin.pelanggan.store'), [
        'name' => $namaPelanggan,
        'email' => 'pelanggan@example.test',
        'no_telepon' => '081234567890',
        'password' => 'password-rahasia',
        'password_confirmation' => 'password-rahasia',
    ]);

    $response->assertRedirect(route('admin.pelanggan'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('users', [
        'name' => $namaPelanggan,
        'email' => 'pelanggan@example.test',
        'role' => 'customer',
        'status_akun' => 'aktif',
    ]);

    $pelanggan = User::query()->where('email', 'pelanggan@example.test')->firstOrFail();
    expect(Hash::check('password-rahasia', $pelanggan->password))->toBeTrue();

    $this->actingAs($admin)
        ->get(route('admin.pelanggan'))
        ->assertOk()
        ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
        ->assertDontSee('<script>alert(1)</script>', false)
        ->assertSee(route('admin.pelanggan.store'), false);
});

it('rejects a duplicate customer email without creating another account', function () {
    $admin = User::factory()->admin()->create();
    User::factory()->customer()->create(['email' => 'pelanggan@example.test']);

    $response = $this->followingRedirects()
        ->actingAs($admin)
        ->from(route('admin.pelanggan'))
        ->post(route('admin.pelanggan.store'), [
            'name' => 'Pelanggan Baru',
            'email' => 'pelanggan@example.test',
            'password' => 'password-rahasia',
            'password_confirmation' => 'password-rahasia',
        ]);

    $response->assertOk()
        ->assertSee('modalTerbuka: true', false)
        ->assertSee('Periksa kembali data pada kolom yang ditandai.');
    $this->assertDatabaseCount('users', 2);
});

it('creates a booking and its item details with a calculated total', function () {
    $admin = User::factory()->admin()->create();
    $pelanggan = User::factory()->customer()->create();
    $kategori = Kategori::factory()->create();
    $alat = Alat::factory()->for($kategori)->create([
        'tarif_per_hari' => 50000,
        'stok_tersedia' => 3,
    ]);
    $tanggalMulai = now()->addDays(2)->toDateString();
    $tanggalSelesai = now()->addDays(4)->toDateString();

    $response = $this->actingAs($admin)->post(route('admin.pemesanan.store'), [
        'pelanggan_id' => $pelanggan->id,
        'tanggal_mulai' => $tanggalMulai,
        'tanggal_selesai' => $tanggalSelesai,
        'jumlah_dp' => 100000,
        'items' => [$alat->id => 2],
    ]);

    $response->assertRedirect(route('admin.pemesanan'))
        ->assertSessionHas('success');

    $pemesanan = Pemesanan::query()->firstOrFail();
    expect($pemesanan->total_biaya)->toBe('300000.00');

    $this->assertDatabaseHas('pemesanan', [
        'id' => $pemesanan->id,
        'user_id' => $pelanggan->id,
        'total_biaya' => 300000,
        'jumlah_dp' => 100000,
        'status' => 'menunggu_pembayaran',
    ]);
    $this->assertDatabaseHas('detail_pemesanan', [
        'pemesanan_id' => $pemesanan->id,
        'alat_id' => $alat->id,
        'jumlah' => 2,
        'tarif_per_hari' => 50000,
        'subtotal' => 300000,
    ]);
    expect($pemesanan->tanggal_mulai->toDateString())->toBe($tanggalMulai)
        ->and($pemesanan->tanggal_selesai->toDateString())->toBe($tanggalSelesai);

    $this->actingAs($admin)
        ->get(route('admin.pemesanan'))
        ->assertOk()
        ->assertSee($pemesanan->kode_pemesanan)
        ->assertSee($alat->nama)
        ->assertSee(route('admin.pemesanan.store'), false);
});

it('rejects a booking quantity greater than available stock', function () {
    $admin = User::factory()->admin()->create();
    $pelanggan = User::factory()->customer()->create();
    $kategori = Kategori::factory()->create();
    $alat = Alat::factory()->for($kategori)->create(['stok_tersedia' => 1]);

    $response = $this->actingAs($admin)->post(route('admin.pemesanan.store'), [
        'pelanggan_id' => $pelanggan->id,
        'tanggal_mulai' => now()->addDays(2)->toDateString(),
        'tanggal_selesai' => now()->addDays(3)->toDateString(),
        'items' => [$alat->id => 2],
    ]);

    $response->assertSessionHasErrors("items.{$alat->id}");
    $this->assertDatabaseCount('pemesanan', 0);
    $this->assertDatabaseCount('detail_pemesanan', 0);
});

it('rejects a booking deposit greater than its calculated total', function () {
    $admin = User::factory()->admin()->create();
    $pelanggan = User::factory()->customer()->create();
    $kategori = Kategori::factory()->create();
    $alat = Alat::factory()->for($kategori)->create([
        'tarif_per_hari' => 50000,
        'stok_tersedia' => 2,
    ]);

    $response = $this->actingAs($admin)->post(route('admin.pemesanan.store'), [
        'pelanggan_id' => $pelanggan->id,
        'tanggal_mulai' => now()->addDays(2)->toDateString(),
        'tanggal_selesai' => now()->addDays(2)->toDateString(),
        'jumlah_dp' => 50001,
        'items' => [$alat->id => 1],
    ]);

    $response->assertSessionHasErrors([
        'jumlah_dp' => 'Jumlah DP tidak boleh melebihi total biaya pemesanan.',
    ]);
    $this->assertDatabaseCount('pemesanan', 0);
    $this->assertDatabaseCount('detail_pemesanan', 0);
});

it('does not let a petugas create customer accounts', function () {
    $petugas = User::factory()->petugas()->create();

    $response = $this->actingAs($petugas)->post(route('admin.pelanggan.store'), [
        'name' => 'Pelanggan Baru',
        'email' => 'pelanggan@example.test',
        'password' => 'password-rahasia',
        'password_confirmation' => 'password-rahasia',
    ]);

    $response->assertForbidden();
    $this->assertDatabaseCount('users', 1);
});

it('does not let a guest create customer accounts', function () {
    $this->post(route('admin.pelanggan.store'), [
        'name' => 'Pelanggan Baru',
        'email' => 'pelanggan@example.test',
        'password' => 'password-rahasia',
        'password_confirmation' => 'password-rahasia',
    ])->assertForbidden();

    $this->assertDatabaseCount('users', 0);
});
