<?php

use App\Models\Alat;
use App\Models\DetailPemesanan;
use App\Models\Pemesanan;
use App\Models\User;
use Illuminate\Support\Carbon;

beforeEach(function () {
    $this->withoutVite();
    $this->actingAs(User::factory()->admin()->create());
});

/** Bantu membuat pemesanan dengan atribut tertentu. */
function buatPemesanan(array $atribut = [], ?User $pelanggan = null): Pemesanan
{
    return Pemesanan::factory()->create(array_merge([
        'user_id' => ($pelanggan ?? User::factory()->customer()->create())->id,
    ], $atribut));
}

it('menampilkan data pemesanan dari database, bukan data statis', function () {
    $pelanggan = User::factory()->customer()->create([
        'name' => 'Rian Pratama',
        'no_telepon' => '081244919921',
    ]);
    $tenda = Alat::factory()->create(['nama' => 'Tenda Dome 4P']);

    $pemesanan = buatPemesanan([
        'kode_pemesanan' => 'ORD-777001',
        'tanggal_mulai' => '2026-10-25',
        'tanggal_selesai' => '2026-10-28',
        'total_biaya' => 385000,
        'status' => 'menunggu_pembayaran',
    ], $pelanggan);

    DetailPemesanan::factory()->untuk($pemesanan, $tenda, 2)->create();

    $this->get(route('admin.pemesanan'))
        ->assertOk()
        ->assertSee('#ORD-777001')
        ->assertSee('Rian Pratama')
        ->assertSee('081244919921')
        ->assertSee('Tenda Dome 4P ×2')
        ->assertSee('Rp 385.000')
        ->assertSee('Menunggu Bayar')
        ->assertSee('4 Hari Durasi') // 25-28 Okt, kedua hari dihitung
        // Data dummy lama harus sudah hilang.
        ->assertDontSee('Nadia Safitri')
        ->assertDontSee('#OG-0891')
        ->assertDontSee('ui-avatars.com');
});

it('menampilkan pesan kosong saat belum ada pemesanan', function () {
    $this->get(route('admin.pemesanan'))
        ->assertOk()
        ->assertSee('Belum ada data pemesanan.')
        ->assertSee('0 - 0');
});

it('menghitung kartu statistik dari database', function () {
    buatPemesanan(['status' => 'menunggu_pembayaran']);
    buatPemesanan(['status' => 'menunggu_pembayaran']);
    buatPemesanan(['status' => 'dikonfirmasi']);
    buatPemesanan(['status' => 'berlangsung']);
    buatPemesanan(['status' => 'berlangsung']);
    buatPemesanan(['status' => 'berlangsung']);
    buatPemesanan(['status' => 'dibatalkan']);

    // Selesai bulan ini: 2, bulan lalu: 1.
    $hariIni = Carbon::now()->toDateString();
    $bulanLalu = Carbon::now()->subMonthNoOverflow()->toDateString();
    buatPemesanan(['status' => 'selesai', 'tanggal_mulai' => $hariIni, 'tanggal_selesai' => $hariIni]);
    buatPemesanan(['status' => 'selesai', 'tanggal_mulai' => $hariIni, 'tanggal_selesai' => $hariIni]);
    buatPemesanan(['status' => 'selesai', 'tanggal_mulai' => $bulanLalu, 'tanggal_selesai' => $bulanLalu]);

    $statistik = $this->get(route('admin.pemesanan'))
        ->assertOk()
        ->viewData('statistik');

    expect($statistik['aktif'])->toBe(6)            // 2 menunggu + 1 dikonfirmasi + 3 berlangsung
        ->and($statistik['menunggu_pembayaran'])->toBe(2)
        ->and($statistik['berlangsung'])->toBe(3)
        ->and($statistik['selesai_bulan_ini'])->toBe(2)
        ->and($statistik['selisih_selesai'])->toBe(1); // 2 bulan ini - 1 bulan lalu
});

it('menyaring daftar berdasarkan status', function () {
    $berlangsung = User::factory()->customer()->create(['name' => 'Pelanggan Berjalan']);
    $selesai = User::factory()->customer()->create(['name' => 'Pelanggan Tuntas']);
    buatPemesanan(['status' => 'berlangsung'], $berlangsung);
    buatPemesanan(['status' => 'selesai'], $selesai);

    $this->get(route('admin.pemesanan', ['status' => 'berlangsung']))
        ->assertOk()
        ->assertSee('Pelanggan Berjalan')
        ->assertDontSee('Pelanggan Tuntas');
});

it('mengabaikan nilai status yang tidak valid', function () {
    buatPemesanan(['status' => 'selesai']);

    expect(
        $this->get(route('admin.pemesanan', ['status' => 'ngawur']))
            ->assertOk()
            ->viewData('pemesanan')
            ->total()
    )->toBe(1);
});

it('mencari berdasarkan kode, nama pelanggan, dan nama alat', function () {
    $budi = User::factory()->customer()->create(['name' => 'Budi Santoso']);
    $sari = User::factory()->customer()->create(['name' => 'Sari Dewi']);

    $punyaBudi = buatPemesanan(['kode_pemesanan' => 'ORD-111111'], $budi);
    $punyaSari = buatPemesanan(['kode_pemesanan' => 'ORD-222222'], $sari);

    DetailPemesanan::factory()->untuk($punyaBudi, Alat::factory()->create(['nama' => 'Carrier 60L']))->create();
    DetailPemesanan::factory()->untuk($punyaSari, Alat::factory()->create(['nama' => 'Kompor Portable']))->create();

    $this->get(route('admin.pemesanan', ['q' => 'Budi']))
        ->assertSee('Budi Santoso')->assertDontSee('Sari Dewi');

    $this->get(route('admin.pemesanan', ['q' => '222222']))
        ->assertSee('Sari Dewi')->assertDontSee('Budi Santoso');

    $this->get(route('admin.pemesanan', ['q' => 'Carrier']))
        ->assertSee('Budi Santoso')->assertDontSee('Sari Dewi');
});

it('menggabungkan filter status dan pencarian tanpa membocorkan status lain', function () {
    $budiBerjalan = User::factory()->customer()->create(['name' => 'Budi Berjalan']);
    $budiSelesai = User::factory()->customer()->create(['name' => 'Budi Selesai']);
    buatPemesanan(['status' => 'berlangsung'], $budiBerjalan);
    buatPemesanan(['status' => 'selesai'], $budiSelesai);

    $this->get(route('admin.pemesanan', ['status' => 'selesai', 'q' => 'Budi']))
        ->assertSee('Budi Selesai')
        ->assertDontSee('Budi Berjalan');
});

it('membagi daftar menjadi halaman berisi 10 pemesanan', function () {
    foreach (range(1, 12) as $nomor) {
        buatPemesanan(['kode_pemesanan' => 'ORD-9'.str_pad((string) $nomor, 5, '0', STR_PAD_LEFT)]);
    }

    $halamanSatu = $this->get(route('admin.pemesanan'))->assertOk()->viewData('pemesanan');
    expect($halamanSatu->count())->toBe(10)->and($halamanSatu->total())->toBe(12);

    $halamanDua = $this->get(route('admin.pemesanan', ['page' => 2]))->assertOk()->viewData('pemesanan');
    expect($halamanDua->count())->toBe(2);
});

it('menampilkan nama pengguna yang login di sidebar, bukan nama statis', function () {
    $this->actingAs(User::factory()->admin()->create(['name' => 'Siti Aminah']));

    $this->get(route('admin.pemesanan'))
        ->assertOk()
        ->assertSee('Siti Aminah')
        ->assertDontSee('Dimas Wicaksono');
});
