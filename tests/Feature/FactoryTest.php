<?php

use App\Enums\Role;
use App\Models\Alat;
use App\Models\AturanDenda;
use App\Models\Denda;
use App\Models\DetailPemesanan;
use App\Models\DetailPengembalian;
use App\Models\DetailSerahTerima;
use App\Models\Kategori;
use App\Models\Pembayaran;
use App\Models\Pemesanan;
use App\Models\Pengembalian;
use App\Models\SerahTerima;
use App\Models\User;

it('creates a pengguna for every role', function () {
    expect(User::factory()->admin()->create()->role)->toBe(Role::Admin)
        ->and(User::factory()->petugas()->create()->role)->toBe(Role::Petugas)
        ->and(User::factory()->owner()->create()->role)->toBe(Role::Owner)
        ->and(User::factory()->customer()->create()->role)->toBe(Role::Customer)
        ->and(User::factory()->create()->isAktif())->toBeTrue()
        ->and(User::factory()->nonaktif()->create()->isAktif())->toBeFalse();
});

it('creates rows for every ERD model through factories', function () {
    $admin = User::factory()->admin()->create();
    $petugas = User::factory()->petugas()->create();
    $pelanggan = User::factory()->customer()->create();

    $kategori = Kategori::factory()->create();
    $alat = Alat::factory()->for($kategori)->create([
        'tarif_per_hari' => 50000,
        'stok_total' => 5,
        'stok_tersedia' => 5,
    ]);

    $pemesanan = Pemesanan::factory()->berlangsung()->create(['user_id' => $pelanggan->id]);

    $detailPemesanan = DetailPemesanan::factory()
        ->untuk($pemesanan, $alat, 2)
        ->create();

    $serahTerima = SerahTerima::factory()->create([
        'pemesanan_id' => $pemesanan->id,
        'user_id' => $petugas->id,
    ]);

    $detailSerahTerima = DetailSerahTerima::factory()->create([
        'serah_terima_id' => $serahTerima->id,
        'detail_pemesanan_id' => $detailPemesanan->id,
    ]);

    $pengembalian = Pengembalian::factory()->terlambat(2)->create([
        'pemesanan_id' => $pemesanan->id,
        'user_id' => $petugas->id,
    ]);

    $detailPengembalian = DetailPengembalian::factory()->denganKerusakan(1)->create([
        'pengembalian_id' => $pengembalian->id,
        'detail_pemesanan_id' => $detailPemesanan->id,
    ]);

    $aturanDenda = AturanDenda::factory()->keterlambatan(75000)->create(['user_id' => $admin->id]);

    $denda = Denda::factory()->lunas()->create([
        'pengembalian_id' => $pengembalian->id,
        'pemesanan_id' => $pemesanan->id,
        'aturan_denda_id' => $aturanDenda->id,
        'detail_pengembalian_id' => $detailPengembalian->id,
        'jenis_denda' => 'keterlambatan',
    ]);

    $pembayaran = Pembayaran::factory()->berhasil()->create(['pemesanan_id' => $pemesanan->id]);

    expect($alat->kategori->is($kategori))->toBeTrue()
        ->and($detailPemesanan->subtotal)->toBe('100000.00')
        ->and($detailPemesanan->alat->is($alat))->toBeTrue()
        ->and($serahTerima->petugas->is($petugas))->toBeTrue()
        ->and($detailSerahTerima->jumlah_disiapkan)->toBe(1)
        ->and($detailSerahTerima->kondisi_awal)->toBe('baik')
        ->and($pengembalian->hari_terlambat)->toBe(2)
        ->and($detailPengembalian->jumlah_rusak)->toBe(1)
        ->and($aturanDenda->perhitungan_denda)->toBe('per_hari')
        ->and((float) $aturanDenda->nilai_denda)->toBe(75000.0)
        ->and($denda->status)->toBe('lunas')
        ->and($pembayaran->status_transaksi)->toBe('berhasil')
        ->and($pembayaran->waktu_bayar)->toBeInstanceOf(DateTimeInterface::class);
});

it('reuses an existing aturan denda when creating many denda', function () {
    $aturan = AturanDenda::factory()->kerusakanRingan()->create();

    Denda::factory()->count(3)->create(['aturan_denda_id' => $aturan->id]);

    expect(Denda::count())->toBe(3)
        ->and(AturanDenda::count())->toBe(1);
});
