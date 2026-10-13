<?php

namespace Database\Seeders;

use App\Models\Alat;
use App\Models\AturanDenda;
use App\Models\Denda;
use App\Models\DetailPemesanan;
use App\Models\DetailPengembalian;
use App\Models\DetailSerahTerima;
use App\Models\Pembayaran;
use App\Models\Pemesanan;
use App\Models\Pengembalian;
use App\Models\SerahTerima;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Data contoh: satu siklus penuh (pemesanan -> serah terima -> pengembalian
 * -> denda -> pembayaran) plus satu pemesanan yang masih menunggu pembayaran,
 * supaya setiap peran punya data untuk didemokan.
 */
class DemoTransaksiSeeder extends Seeder
{
    public const KODE_SELESAI = 'ORD-DEMO-0001';

    public const KODE_MENUNGGU = 'ORD-DEMO-0002';

    public function run(): void
    {
        if (Pemesanan::where('kode_pemesanan', self::KODE_SELESAI)->exists()) {
            return;
        }

        $pelanggan = User::where('email', 'pelanggan@outgear.test')->first()
            ?? User::factory()->customer()->create();

        $petugas = User::where('email', 'petugas@outgear.test')->first()
            ?? User::factory()->petugas()->create();

        $alat = Alat::query()->orderBy('id')->take(2)->get();

        if ($alat->count() < 2) {
            return;
        }

        $alatUtama = $alat[0];
        $alatKedua = $alat[1];

        $mulai = now()->subDays(4)->startOfDay();
        $selesai = now()->subDay()->startOfDay();
        $lamaSewa = 3;
        $jumlahUtama = 2;

        $pemesanan = Pemesanan::create([
            'kode_pemesanan' => self::KODE_SELESAI,
            'user_id' => $pelanggan->id,
            'tanggal_mulai' => $mulai->toDateString(),
            'tanggal_selesai' => $selesai->toDateString(),
            'total_biaya' => 0,
            'jumlah_dp' => 0,
            'status' => 'berlangsung',
        ]);

        $detailUtama = DetailPemesanan::create([
            'pemesanan_id' => $pemesanan->id,
            'alat_id' => $alatUtama->id,
            'jumlah' => $jumlahUtama,
            'tarif_per_hari' => $alatUtama->tarif_per_hari,
            'subtotal' => $jumlahUtama * (float) $alatUtama->tarif_per_hari * $lamaSewa,
        ]);

        $detailKedua = DetailPemesanan::create([
            'pemesanan_id' => $pemesanan->id,
            'alat_id' => $alatKedua->id,
            'jumlah' => 1,
            'tarif_per_hari' => $alatKedua->tarif_per_hari,
            'subtotal' => (float) $alatKedua->tarif_per_hari * $lamaSewa,
        ]);

        $totalBiaya = (float) $detailUtama->subtotal + (float) $detailKedua->subtotal;

        $pemesanan->update([
            'total_biaya' => $totalBiaya,
            'jumlah_dp' => round($totalBiaya * 0.5),
        ]);

        // Stok berkurang ketika alat disewa.
        $alatUtama->decrement('stok_tersedia', $jumlahUtama);
        $alatUtama->increment('stok_disewa', $jumlahUtama);
        $alatKedua->decrement('stok_tersedia', 1);
        $alatKedua->increment('stok_disewa', 1);

        $serahTerima = SerahTerima::create([
            'pemesanan_id' => $pemesanan->id,
            'user_id' => $petugas->id,
            'waktu_serah_terima' => $mulai->copy()->addHours(9),
            'nama_ktp' => $pelanggan->name,
            'nomor_ktp' => '3273010101010001',
            'status_serah_terima' => 'diambil',
        ]);

        foreach ([$detailUtama, $detailKedua] as $detail) {
            DetailSerahTerima::create([
                'serah_terima_id' => $serahTerima->id,
                'detail_pemesanan_id' => $detail->id,
                'jumlah_disiapkan' => $detail->jumlah,
                'kondisi_awal' => 'baik',
            ]);
        }

        $pengembalian = Pengembalian::create([
            'pemesanan_id' => $pemesanan->id,
            'user_id' => $petugas->id,
            'waktu_pengambilan' => $mulai->copy()->addHours(9),
            'waktu_submit' => $selesai->copy()->addHours(17),
            'hari_terlambat' => 0,
        ]);

        // Alat utama dikembalikan 1 unit rusak, sisanya baik.
        $detailPengembalianRusak = DetailPengembalian::create([
            'pengembalian_id' => $pengembalian->id,
            'detail_pemesanan_id' => $detailUtama->id,
            'jumlah_baik' => $jumlahUtama - 1,
            'jumlah_rusak' => 1,
            'jumlah_hilang' => 0,
        ]);

        DetailPengembalian::create([
            'pengembalian_id' => $pengembalian->id,
            'detail_pemesanan_id' => $detailKedua->id,
            'jumlah_baik' => 1,
            'jumlah_rusak' => 0,
            'jumlah_hilang' => 0,
        ]);

        // Stok kembali: unit rusak masuk stok_rusak, sisanya kembali tersedia.
        $alatUtama->decrement('stok_disewa', $jumlahUtama);
        $alatUtama->increment('stok_tersedia', $jumlahUtama - 1);
        $alatUtama->increment('stok_rusak', 1);
        $alatKedua->decrement('stok_disewa', 1);
        $alatKedua->increment('stok_tersedia', 1);

        $serahTerima->update(['status_serah_terima' => 'dikembalikan']);

        // Denda kerusakan ringan = persentase dari tarif alat yang rusak.
        $aturan = AturanDenda::where('jenis_denda', 'kerusakan_ringan')->first();

        if ($aturan !== null) {
            Denda::create([
                'pengembalian_id' => $pengembalian->id,
                'aturan_denda_id' => $aturan->id,
                'pemesanan_id' => $pemesanan->id,
                'detail_pengembalian_id' => $detailPengembalianRusak->id,
                'nilai_dasar' => $alatUtama->tarif_per_hari,
                'jumlah_denda' => round((float) $alatUtama->tarif_per_hari * (float) $aturan->nilai_denda / 100),
                'status' => 'belum_dibayar',
                'jenis_denda' => 'kerusakan_ringan',
                'nilai_tarif_terkait' => $alatUtama->tarif_per_hari,
            ]);
        }

        // Pembayaran DP lewat Midtrans (QRIS).
        Pembayaran::create([
            'pemesanan_id' => $pemesanan->id,
            'transaction_id' => 'MID-DEMO-0001',
            'jenis_pembayaran' => 'dp',
            'metode_pembayaran' => 'qris',
            'jumlah' => $pemesanan->jumlah_dp,
            'status_transaksi' => 'berhasil',
            'waktu_bayar' => $mulai->copy()->subDay()->addHours(10),
        ]);

        $pemesanan->update(['status' => 'selesai']);

        // Pemesanan kedua: masih menunggu pembayaran.
        $menunggu = Pemesanan::create([
            'kode_pemesanan' => self::KODE_MENUNGGU,
            'user_id' => $pelanggan->id,
            'tanggal_mulai' => now()->addDays(3)->toDateString(),
            'tanggal_selesai' => now()->addDays(5)->toDateString(),
            'total_biaya' => 0,
            'jumlah_dp' => 0,
            'status' => 'menunggu_pembayaran',
        ]);

        $subtotalMenunggu = (float) $alatKedua->tarif_per_hari * 2;

        DetailPemesanan::create([
            'pemesanan_id' => $menunggu->id,
            'alat_id' => $alatKedua->id,
            'jumlah' => 1,
            'tarif_per_hari' => $alatKedua->tarif_per_hari,
            'subtotal' => $subtotalMenunggu,
        ]);

        $menunggu->update([
            'total_biaya' => $subtotalMenunggu,
            'jumlah_dp' => round($subtotalMenunggu * 0.5),
        ]);
    }
}
