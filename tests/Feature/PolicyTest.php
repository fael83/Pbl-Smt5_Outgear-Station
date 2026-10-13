<?php

use App\Models\Alat;
use App\Models\AturanDenda;
use App\Models\Denda;
use App\Models\DetailPemesanan;
use App\Models\Kategori;
use App\Models\Pembayaran;
use App\Models\Pemesanan;
use App\Models\Pengembalian;
use App\Models\SerahTerima;
use App\Models\User;

it('lets only admin manage master data', function () {
    $admin = User::factory()->admin()->create();
    $petugas = User::factory()->petugas()->create();
    $owner = User::factory()->owner()->create();
    $pelanggan = User::factory()->customer()->create();

    $kategori = Kategori::factory()->create();
    $alat = Alat::factory()->for($kategori)->create();

    expect($admin->can('create', Kategori::class))->toBeTrue()
        ->and($admin->can('update', $kategori))->toBeTrue()
        ->and($admin->can('delete', $kategori))->toBeTrue()
        ->and($admin->can('create', Alat::class))->toBeTrue()
        ->and($admin->can('update', $alat))->toBeTrue()
        ->and($petugas->can('create', Kategori::class))->toBeFalse()
        ->and($petugas->can('update', $alat))->toBeFalse()
        ->and($owner->can('create', Alat::class))->toBeFalse()
        ->and($pelanggan->can('create', Kategori::class))->toBeFalse()
        ->and($pelanggan->can('delete', $alat))->toBeFalse();

    // Semua peran boleh melihat katalog & kategori.
    expect($pelanggan->can('viewAny', Alat::class))->toBeTrue()
        ->and($petugas->can('view', $alat))->toBeTrue()
        ->and($owner->can('viewAny', Kategori::class))->toBeTrue();
});

it('lets admin and owner manage aturan denda', function () {
    $admin = User::factory()->admin()->create();
    $owner = User::factory()->owner()->create();
    $petugas = User::factory()->petugas()->create();

    $aturan = AturanDenda::factory()->kehilangan()->create(['user_id' => $admin->id]);

    expect($admin->can('create', AturanDenda::class))->toBeTrue()
        ->and($owner->can('create', AturanDenda::class))->toBeTrue()
        ->and($owner->can('update', $aturan))->toBeTrue()
        ->and($owner->can('delete', $aturan))->toBeFalse()
        ->and($admin->can('delete', $aturan))->toBeTrue()
        ->and($petugas->can('create', AturanDenda::class))->toBeFalse()
        ->and($petugas->can('view', $aturan))->toBeTrue();
});

it('scopes pemesanan access to the owning pelanggan', function () {
    $pelanggan = User::factory()->customer()->create();
    $pelangganLain = User::factory()->customer()->create();
    $petugas = User::factory()->petugas()->create();

    $pemesanan = Pemesanan::factory()->create(['user_id' => $pelanggan->id]);
    $pemesananDikonfirmasi = Pemesanan::factory()->dikonfirmasi()->create(['user_id' => $pelanggan->id]);

    expect($pelanggan->can('view', $pemesanan))->toBeTrue()
        ->and($pelanggan->can('update', $pemesanan))->toBeTrue()
        ->and($pelanggan->can('batalkan', $pemesanan))->toBeTrue()
        ->and($pelanggan->can('update', $pemesananDikonfirmasi))->toBeFalse()
        ->and($pelanggan->can('batalkan', $pemesananDikonfirmasi))->toBeFalse()
        ->and($pelangganLain->can('view', $pemesanan))->toBeFalse()
        ->and($pelanggan->can('create', Pemesanan::class))->toBeTrue()
        ->and($petugas->can('create', Pemesanan::class))->toBeFalse()
        ->and($petugas->can('view', $pemesanan))->toBeTrue()
        ->and($petugas->can('konfirmasi', $pemesanan))->toBeTrue()
        ->and($petugas->can('konfirmasi', $pemesananDikonfirmasi))->toBeFalse()
        ->and($pelanggan->can('konfirmasi', $pemesanan))->toBeFalse()
        ->and($pelanggan->can('delete', $pemesanan))->toBeFalse();
});

it('scopes the operational records to petugas and the owning pelanggan', function () {
    $petugas = User::factory()->petugas()->create();
    $pelanggan = User::factory()->customer()->create();
    $pelangganLain = User::factory()->customer()->create();

    $pemesanan = Pemesanan::factory()->berlangsung()->create(['user_id' => $pelanggan->id]);

    $serahTerima = SerahTerima::factory()->create([
        'pemesanan_id' => $pemesanan->id,
        'user_id' => $petugas->id,
    ]);
    $pengembalian = Pengembalian::factory()->create([
        'pemesanan_id' => $pemesanan->id,
        'user_id' => $petugas->id,
    ]);
    $denda = Denda::factory()->create([
        'pemesanan_id' => $pemesanan->id,
        'pengembalian_id' => $pengembalian->id,
    ]);
    $pembayaran = Pembayaran::factory()->create(['pemesanan_id' => $pemesanan->id]);

    expect($petugas->can('create', SerahTerima::class))->toBeTrue()
        ->and($petugas->can('update', $serahTerima))->toBeTrue()
        ->and($petugas->can('create', Pengembalian::class))->toBeTrue()
        ->and($petugas->can('create', Denda::class))->toBeTrue()
        ->and($petugas->can('update', $denda))->toBeTrue()
        ->and($pelanggan->can('create', SerahTerima::class))->toBeFalse()
        ->and($pelanggan->can('create', Pengembalian::class))->toBeFalse()
        ->and($pelanggan->can('create', Denda::class))->toBeFalse()
        ->and($pelanggan->can('view', $serahTerima))->toBeTrue()
        ->and($pelangganLain->can('view', $serahTerima))->toBeFalse()
        ->and($pelanggan->can('view', $pengembalian))->toBeTrue()
        ->and($pelangganLain->can('view', $pengembalian))->toBeFalse()
        ->and($pelanggan->can('view', $denda))->toBeTrue()
        ->and($pelangganLain->can('view', $denda))->toBeFalse()
        ->and($pelanggan->can('view', $pembayaran))->toBeTrue()
        ->and($pelangganLain->can('view', $pembayaran))->toBeFalse()
        ->and($pelanggan->can('create', [Pembayaran::class, $pemesanan]))->toBeTrue()
        ->and($pelangganLain->can('create', [Pembayaran::class, $pemesanan]))->toBeFalse()
        ->and($petugas->can('create', [Pembayaran::class, $pemesanan]))->toBeFalse()
        ->and($petugas->can('update', $pembayaran))->toBeFalse();
});

it('lets the pelanggan edit only their own pending detail pemesanan', function () {
    $pelanggan = User::factory()->customer()->create();
    $pelangganLain = User::factory()->customer()->create();

    $pemesanan = Pemesanan::factory()->create(['user_id' => $pelanggan->id]);
    $detail = DetailPemesanan::factory()->create(['pemesanan_id' => $pemesanan->id]);

    $pemesananDikonfirmasi = Pemesanan::factory()->dikonfirmasi()->create(['user_id' => $pelanggan->id]);
    $detailDikonfirmasi = DetailPemesanan::factory()->create(['pemesanan_id' => $pemesananDikonfirmasi->id]);

    expect($pelanggan->can('update', $detail))->toBeTrue()
        ->and($pelanggan->can('delete', $detail))->toBeTrue()
        ->and($pelanggan->can('update', $detailDikonfirmasi))->toBeFalse()
        ->and($pelangganLain->can('update', $detail))->toBeFalse()
        ->and(User::factory()->petugas()->create()->can('update', $detail))->toBeFalse();
});
