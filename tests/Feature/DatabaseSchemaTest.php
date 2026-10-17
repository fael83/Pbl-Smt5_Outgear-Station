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
use Illuminate\Support\Facades\Schema;

it('creates every table from the ERD', function (string $table) {
    expect(Schema::hasTable($table))->toBeTrue();
})->with([
    'kategori',
    'alat',
    'pemesanan',
    'detail_pemesanan',
    'pengembalian',
    'detail_pengembalian',
    'serah_terima',
    'detail_serah_terima',
    'aturan_denda',
    'denda',
    'pembayaran',
]);

it('stores the ERD pengguna attributes on the users table', function () {
    expect(Schema::hasColumns('users', [
        'name',
        'email',
        'password',
        'role',
        'no_telepon',
        'status_akun',
    ]))->toBeTrue();

    // Nilai default kolom (enum) hanya ada di database, jadi perlu di-refresh.
    $pengguna = User::factory()->create()->refresh();

    expect($pengguna->status_akun)->toBe('aktif')
        ->and($pengguna->role)->toBe(Role::Customer);
});

it('rejects a second aturan denda for the same jenis denda', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $attributes = [
        'jenis_denda' => 'kehilangan',
        'perhitungan_denda' => 'persentase',
        'nilai_denda' => 100,
        'user_id' => $admin->id,
    ];

    AturanDenda::create($attributes);
    AturanDenda::create($attributes);
})->throws(PDOException::class);

it('wires every ERD relation end to end', function () {
    $petugas = User::factory()->create(['role' => 'petugas']);
    $pelanggan = User::factory()->create(['role' => 'customer', 'no_telepon' => '081234567890']);

    $kategori = Kategori::create(['nama' => 'Tenda', 'deskripsi' => 'Perlengkapan tenda']);
    $alat = Alat::create([
        'kategori_id' => $kategori->id,
        'nama' => 'Tenda Dome 4P',
        'tarif_per_hari' => 50000,
        'nilai_pengganti' => 750000,
        'stok_total' => 10,
        'stok_tersedia' => 10,
    ]);

    $pemesanan = Pemesanan::create([
        'kode_pemesanan' => 'ORD-2026-0001',
        'user_id' => $pelanggan->id,
        'tanggal_mulai' => now()->toDateString(),
        'tanggal_selesai' => now()->addDays(2)->toDateString(),
        'total_biaya' => 150000,
        'jumlah_dp' => 50000,
    ]);

    $detailPemesanan = DetailPemesanan::create([
        'pemesanan_id' => $pemesanan->id,
        'alat_id' => $alat->id,
        'jumlah' => 2,
        'tarif_per_hari' => 50000,
        'subtotal' => 100000,
    ]);

    $serahTerima = SerahTerima::create([
        'pemesanan_id' => $pemesanan->id,
        'user_id' => $petugas->id,
        'waktu_serah_terima' => now(),
        'nama_ktp' => 'Budi Santoso',
        'nomor_ktp' => '3273010101010001',
    ]);

    $detailSerahTerima = DetailSerahTerima::create([
        'serah_terima_id' => $serahTerima->id,
        'detail_pemesanan_id' => $detailPemesanan->id,
        'jumlah_disiapkan' => 2,
    ]);

    $pengembalian = Pengembalian::create([
        'pemesanan_id' => $pemesanan->id,
        'user_id' => $petugas->id,
        'waktu_pengambilan' => now(),
        'waktu_submit' => now()->addDays(3),
        'hari_terlambat' => 1,
    ]);

    $detailPengembalian = DetailPengembalian::create([
        'pengembalian_id' => $pengembalian->id,
        'detail_pemesanan_id' => $detailPemesanan->id,
        'jumlah_baik' => 1,
        'jumlah_rusak' => 1,
    ]);

    $aturanDenda = AturanDenda::create([
        'jenis_denda' => 'kerusakan_ringan',
        'perhitungan_denda' => 'persentase',
        'nilai_denda' => 20,
        'user_id' => $petugas->id,
    ]);

    $denda = Denda::create([
        'pengembalian_id' => $pengembalian->id,
        'aturan_denda_id' => $aturanDenda->id,
        'pemesanan_id' => $pemesanan->id,
        'detail_pengembalian_id' => $detailPengembalian->id,
        'nilai_dasar' => 750000,
        'jumlah_denda' => 150000,
        'jenis_denda' => 'kerusakan_ringan',
        'nilai_tarif_terkait' => 50000,
    ]);

    $pembayaran = Pembayaran::create([
        'pemesanan_id' => $pemesanan->id,
        'transaction_id' => 'MID-2026-0001',
        'jenis_pembayaran' => 'dp',
        'metode_pembayaran' => 'qris',
        'jumlah' => 50000,
    ]);

    // Nilai default kolom (enum) hanya ada di database, jadi perlu di-refresh.
    $pemesanan->refresh();
    $serahTerima->refresh();
    $detailSerahTerima->refresh();
    $denda->refresh();
    $pembayaran->refresh();

    // Nilai default dari enum di migrasi.
    expect($pemesanan->status)->toBe('menunggu_pembayaran')
        ->and($serahTerima->status_serah_terima)->toBe('diambil')
        ->and($detailSerahTerima->kondisi_awal)->toBe('baik')
        ->and($denda->status)->toBe('belum_dibayar')
        ->and($pembayaran->status_transaksi)->toBe('pending');

    // Casting kolom.
    expect($alat->tarif_per_hari)->toBe('50000.00')
        ->and($pemesanan->total_biaya)->toBe('150000.00')
        ->and($pemesanan->tanggal_mulai)->toBeInstanceOf(DateTimeInterface::class)
        ->and($pengembalian->waktu_submit)->toBeInstanceOf(DateTimeInterface::class);

    // Relasi sesuai ERD.
    expect($kategori->alat()->count())->toBe(1)
        ->and($alat->kategori->is($kategori))->toBeTrue()
        ->and($pelanggan->pemesanan()->count())->toBe(1)
        ->and($pemesanan->pelanggan->is($pelanggan))->toBeTrue()
        ->and($pemesanan->detailPemesanan()->count())->toBe(1)
        ->and($detailPemesanan->alat->is($alat))->toBeTrue()
        ->and($pemesanan->serahTerima()->count())->toBe(1)
        ->and($serahTerima->petugas->is($petugas))->toBeTrue()
        ->and($serahTerima->detailSerahTerima()->count())->toBe(1)
        ->and($detailSerahTerima->detailPemesanan->is($detailPemesanan))->toBeTrue()
        ->and($pemesanan->pengembalian()->count())->toBe(1)
        ->and($pengembalian->petugas->is($petugas))->toBeTrue()
        ->and($pengembalian->detailPengembalian()->count())->toBe(1)
        ->and($detailPengembalian->detailPemesanan->is($detailPemesanan))->toBeTrue()
        ->and($petugas->pengembalian()->count())->toBe(1)
        ->and($petugas->serahTerima()->count())->toBe(1)
        ->and($petugas->aturanDenda()->count())->toBe(1)
        ->and($aturanDenda->pengubah->is($petugas))->toBeTrue()
        ->and($aturanDenda->denda()->count())->toBe(1)
        ->and($denda->aturanDenda->is($aturanDenda))->toBeTrue()
        ->and($denda->pengembalian->is($pengembalian))->toBeTrue()
        ->and($denda->detailPengembalian->is($detailPengembalian))->toBeTrue()
        ->and($pemesanan->denda()->count())->toBe(1)
        ->and($pemesanan->pembayaran()->count())->toBe(1)
        ->and($pembayaran->pemesanan->is($pemesanan))->toBeTrue();
});
