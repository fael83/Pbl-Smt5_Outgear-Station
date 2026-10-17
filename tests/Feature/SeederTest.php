<?php

use App\Models\Alat;
use App\Models\AturanDenda;
use App\Models\Denda;
use App\Models\DetailPengembalian;
use App\Models\DetailSerahTerima;
use App\Models\Kategori;
use App\Models\Pembayaran;
use App\Models\Pemesanan;
use App\Models\Pengembalian;
use App\Models\SerahTerima;
use App\Models\User;
use Database\Seeders\AlatSeeder;
use Database\Seeders\AturanDendaSeeder;
use Database\Seeders\DemoTransaksiSeeder;
use Database\Seeders\KategoriSeeder;
use Database\Seeders\PenggunaSeeder;

it('seeds every table from the ERD', function () {
    $this->seed();

    expect(User::count())->toBe(count(PenggunaSeeder::AKUN))
        ->and(Kategori::count())->toBe(count(KategoriSeeder::KATEGORI))
        ->and(Alat::count())->toBe(count(AlatSeeder::ALAT))
        ->and(AturanDenda::count())->toBe(count(AturanDendaSeeder::ATURAN))
        ->and(Pemesanan::count())->toBe(2)
        ->and(SerahTerima::count())->toBe(1)
        ->and(DetailSerahTerima::count())->toBe(2)
        ->and(Pengembalian::count())->toBe(1)
        ->and(DetailPengembalian::count())->toBe(2)
        ->and(Denda::count())->toBe(1)
        ->and(Pembayaran::count())->toBe(1);
});

it('seeds one pengguna for every role with a verified email', function () {
    $this->seed();

    foreach (PenggunaSeeder::AKUN as $akun) {
        $pengguna = User::where('email', $akun['email'])->firstOrFail();

        expect($pengguna->role)->toBe($akun['role'])
            ->and($pengguna->status_akun)->toBe('aktif')
            ->and($pengguna->hasVerifiedEmail())->toBeTrue();
    }
});

it('is safe to run twice', function () {
    $this->seed();
    $this->seed();

    expect(User::count())->toBe(count(PenggunaSeeder::AKUN))
        ->and(Kategori::count())->toBe(count(KategoriSeeder::KATEGORI))
        ->and(Alat::count())->toBe(count(AlatSeeder::ALAT))
        ->and(AturanDenda::count())->toBe(count(AturanDendaSeeder::ATURAN))
        ->and(Pemesanan::count())->toBe(2);
});

it('keeps the alat stock consistent after the demo transaction', function () {
    $this->seed();

    $pemesanan = Pemesanan::where('kode_pemesanan', DemoTransaksiSeeder::KODE_SELESAI)->firstOrFail();
    $alat = $pemesanan->detailPemesanan()->orderBy('id')->firstOrFail()->alat;

    expect($pemesanan->status)->toBe('selesai')
        ->and((float) $pemesanan->total_biaya)->toBeGreaterThan(0)
        ->and($alat->stok_rusak)->toBe(1)
        ->and($alat->stok_disewa)->toBe(0)
        ->and($alat->stok_tersedia + $alat->stok_disewa + $alat->stok_rusak)->toBe($alat->stok_total)
        ->and($pemesanan->denda()->firstOrFail()->jenis_denda)->toBe('kerusakan_ringan')
        ->and($pemesanan->serahTerima()->firstOrFail()->status_serah_terima)->toBe('dikembalikan');
});

it('leaves a second pemesanan waiting for payment', function () {
    $this->seed();

    $pemesanan = Pemesanan::where('kode_pemesanan', DemoTransaksiSeeder::KODE_MENUNGGU)->firstOrFail();

    expect($pemesanan->status)->toBe('menunggu_pembayaran')
        ->and($pemesanan->pembayaran()->count())->toBe(0)
        ->and($pemesanan->detailPemesanan()->count())->toBe(1);
});
