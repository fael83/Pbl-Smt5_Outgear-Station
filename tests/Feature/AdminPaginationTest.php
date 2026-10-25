<?php

use App\Models\Denda;
use App\Models\Pemesanan;
use App\Models\User;

it('paginates customers and keeps the requested page size and status filter', function () {
    foreach (range(1, 11) as $nomor) {
        User::factory()->customer()->create([
            'name' => "Pelanggan Uji {$nomor}",
            'email' => "pelanggan{$nomor}@example.test",
            'status_akun' => 'aktif',
            'created_at' => now()->addSeconds($nomor),
        ]);
    }

    User::factory()->customer()->nonaktif()->create([
        'email' => 'pelanggan-nonaktif@example.test',
    ]);

    $response = $this->get(route('admin.pelanggan', [
        'status' => 'aktif',
        'per_page' => 10,
        'page' => 2,
    ]));

    $response->assertOk()
        ->assertSeeText('Menampilkan 11–11 dari 11 akun')
        ->assertSee('Pelanggan Uji 1')
        ->assertDontSee('Pelanggan Uji 11')
        ->assertSee('per_page=10', false);
});

it('paginates bookings and preserves the status filter in page links', function () {
    foreach (range(1, 11) as $nomor) {
        Pemesanan::factory()->create([
            'kode_pemesanan' => "ORD-PAGE-{$nomor}",
            'status' => 'menunggu_pembayaran',
            'created_at' => now()->addSeconds($nomor),
        ]);
    }

    Pemesanan::factory()->selesai()->create([
        'kode_pemesanan' => 'ORD-PAGE-COMPLETED',
    ]);

    $response = $this->get(route('admin.pemesanan', [
        'status' => 'menunggu_pembayaran',
        'per_page' => 10,
        'page' => 2,
    ]));

    $response->assertOk()
        ->assertSeeText('Menampilkan 11–11 dari 11 pemesanan')
        ->assertSee('ORD-PAGE-1')
        ->assertDontSee('ORD-PAGE-11')
        ->assertSee('status=menunggu_pembayaran', false);
});

it('paginates fines from the database and displays the actual range', function () {
    Denda::factory()->count(11)->create();

    $response = $this->get(route('admin.denda', [
        'per_page' => 10,
        'page' => 2,
    ]));

    $response->assertOk()
        ->assertSee('Menampilkan 11–11 dari 11 data denda')
        ->assertSee('per_page=10', false);
});

it('filters fines before paginating', function () {
    Denda::factory()->count(6)->create(['status' => 'belum_dibayar']);
    Denda::factory()->count(5)->lunas()->create();

    $response = $this->get(route('admin.denda', [
        'status' => 'lunas',
        'per_page' => 10,
    ]));

    $response->assertOk()
        ->assertSee('Menampilkan 1–5 dari 5 data denda')
        ->assertSee('Lunas');
});

it('shows contextual empty states when admin tables have no records', function () {
    $this->get(route('admin.pelanggan'))
        ->assertOk()
        ->assertSee('Belum ada data pelanggan.')
        ->assertSee('Tambahkan pelanggan pertama melalui tombol');

    $this->get(route('admin.pemesanan'))
        ->assertOk()
        ->assertSee('Belum ada data pemesanan.')
        ->assertSee('Tambahkan pemesanan pertama melalui tombol');

    $this->get(route('admin.denda'))
        ->assertOk()
        ->assertSee('Belum ada data denda.')
        ->assertSee('Data denda akan muncul setelah tercatat');
});

it('explains when search filters have no matching admin records', function () {
    $this->get(route('admin.pelanggan', ['search' => 'tidak-ada']))
        ->assertOk()
        ->assertSee('Tidak ada pelanggan yang cocok dengan filter.')
        ->assertSee('Coba ubah kata kunci pencarian atau status akun.');

    $this->get(route('admin.pemesanan', ['search' => 'tidak-ada']))
        ->assertOk()
        ->assertSee('Tidak ada pemesanan yang cocok dengan filter.')
        ->assertSee('Coba ubah kata kunci pencarian atau status pemesanan.');

    $this->get(route('admin.denda', ['search' => 'tidak-ada']))
        ->assertOk()
        ->assertSee('Tidak ada data denda yang cocok dengan filter.')
        ->assertSee('Coba ubah kata kunci pencarian atau status pembayaran.');
});

it('renders interactive pagination controls for verification results', function () {
    $response = $this->get(route('admin.verifikasi'));

    $response->assertOk()
        ->assertSee('x-for="item in paginatedList"', false)
        ->assertSee('x-model.number="perPage"', false)
        ->assertSee('Tidak ada pembayaran yang cocok dengan filter.', false)
        ->assertSee('inline-flex shadow-sm rounded-md', false)
        ->assertSee('flex gap-2 items-center justify-between sm:hidden', false)
        ->assertSee('currentPage = Math.min(totalPages, currentPage + 1)', false);
});
