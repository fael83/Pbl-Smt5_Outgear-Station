<?php

use App\Models\Pemesanan;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    // Dashboard admin berada di balik middleware auth + role.
    $this->admin = User::factory()->admin()->create();

    $this->actingAs($this->admin);
});

it('renders the admin dashboard and its statistics', function () {
    $this->withoutVite();

    $response = $this->get('/admin');

    // Asersi lama memakai teks yang tidak lagi ada di view (judul kartu
    // sekarang "Booking Aktif" dan "Perlu Verifikasi").
    $response->assertOk()
        ->assertSee('Dashboard Operasional')
        ->assertSee('Booking Aktif')
        ->assertSee('Perlu Verifikasi')
        ->assertSee('Verifikasi Pembayaran')
        ->assertDontSee('data pukul')
        // Profil di pojok kanan atas menampilkan nama dan email pengguna.
        ->assertSee($this->admin->name)
        ->assertSee($this->admin->email)
        // Tombol muat ulang memakai AJAX, bukan memuat ulang halaman.
        ->assertSee('@click.prevent="muatUlang()"', false)
        ->assertSee('x-ref="isi"', false)
        ->assertSee('href="'.route('admin.profile').'"', false);

    $this->get(route('admin.profile'))
        ->assertOk()
        ->assertSee('Profil Administrator');
});

it('menyiapkan data booking dari database untuk pencarian dan filter tabel', function () {
    $this->withoutVite();

    // Dashboard menyimpan hasil query di cache; pastikan tidak memakai data lama.
    Cache::flush();

    Pemesanan::factory()->create([
        'kode_pemesanan' => 'ORD-DASH-CARI',
        'status' => 'menunggu_pembayaran',
    ]);

    $response = $this->get('/admin');

    // Pencarian & filter berjalan di sisi klien memakai data asli dari database,
    // bukan data contoh yang ditulis langsung di view.
    $response->assertOk()
        ->assertSee('x-model="search"', false)
        ->assertSee('x-for="item in filteredRows"', false)
        ->assertSee('bookings:', false)
        ->assertSee('ORD-DASH-CARI')
        ->assertDontSee('Rian Pratama')
        ->assertDontSee('DP 50% Masuk');

    // Opsi dropdown filter memakai status asli pemesanan.
    foreach (['menunggu_pembayaran', 'dikonfirmasi', 'berlangsung', 'selesai', 'dibatalkan'] as $status) {
        $response->assertSee("selectedFilter = '{$status}'", false);
    }

    // Pesan kosong dibedakan antara belum ada data dan hasil filter kosong.
    $response->assertSee('Belum ada pemesanan.')
        ->assertSee('Tidak ada pemesanan yang cocok dengan filter.');
});

it('mengirim isi dashboard terbaru lewat AJAX untuk tombol muat ulang', function () {
    $this->withoutVite();

    // Halaman penuh lebih dulu mengisi cache dashboard.
    $this->get('/admin')->assertOk();

    Pemesanan::factory()->create([
        'kode_pemesanan' => 'ORD-DASH-SEGAR',
        'status' => 'dikonfirmasi',
    ]);

    // Endpoint AJAX membuang cache lebih dulu, jadi HTML yang dikirim sudah
    // memuat pemesanan terbaru tanpa perlu memuat ulang seluruh halaman.
    $this->getJson(route('admin.dashboard.data'))
        ->assertOk()
        ->assertJsonStructure(['html', 'perlu_verifikasi'])
        ->assertJsonPath('html', fn (string $html): bool => str_contains($html, 'Booking Aktif')
            && str_contains($html, 'ORD-DASH-SEGAR'));
});
