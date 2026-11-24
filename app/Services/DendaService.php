<?php

namespace App\Services;

use App\Models\AturanDenda;
use App\Models\Denda;
use App\Models\Pengembalian;
use Illuminate\Support\Facades\DB;

/**
 * Menghitung denda dari data pengembalian (terlambat, rusak, hilang).
 *
 * Aturan diambil dari tabel `aturan_denda`:
 * - keterlambatan : nilai per hari x hari terlambat;
 * - kerusakan_*   : persentase dari nilai pengganti alat x jumlah rusak;
 * - kehilangan    : persentase dari nilai pengganti alat x jumlah hilang.
 *
 * Checklist petugas hanya mencatat "rusak" tanpa tingkat keparahan, jadi
 * semua barang rusak dihitung memakai aturan `kerusakan_ringan`.
 */
class DendaService
{
    /**
     * Hitung ulang denda sebuah pengembalian. Denda yang sudah lunas tidak
     * disentuh; yang belum dibayar dibuat ulang sesuai checklist terbaru.
     * Aman dipanggil berulang.
     */
    public function sinkronkan(Pengembalian $pengembalian): void
    {
        $aturan = AturanDenda::query()->get()->keyBy('jenis_denda');

        $pengembalian->loadMissing('detailPengembalian.detailPemesanan.alat');

        DB::transaction(function () use ($pengembalian, $aturan): void {
            Denda::query()
                ->where('pengembalian_id', $pengembalian->id)
                ->where('status', 'belum_dibayar')
                ->delete();

            // Denda yang sudah lunas tidak dibuat dobel.
            $lunas = Denda::query()
                ->where('pengembalian_id', $pengembalian->id)
                ->get(['detail_pengembalian_id', 'jenis_denda']);

            $sudahAda = static fn (?int $detailId, string $jenis): bool => $lunas
                ->contains(fn (Denda $d) => $d->detail_pengembalian_id === $detailId && $d->jenis_denda === $jenis);

            $dasar = [
                'pengembalian_id' => $pengembalian->id,
                'pemesanan_id' => $pengembalian->pemesanan_id,
                'status' => 'belum_dibayar',
            ];

            // Keterlambatan: satu baris per pengembalian.
            $terlambat = $aturan->get('keterlambatan');

            if ($terlambat && $pengembalian->hari_terlambat > 0 && ! $sudahAda(null, 'keterlambatan')) {
                Denda::create($dasar + [
                    'aturan_denda_id' => $terlambat->id,
                    'detail_pengembalian_id' => null,
                    'jenis_denda' => 'keterlambatan',
                    'nilai_dasar' => (float) $terlambat->nilai_denda,
                    'jumlah_denda' => round((float) $terlambat->nilai_denda * $pengembalian->hari_terlambat),
                    'nilai_tarif_terkait' => null,
                ]);
            }

            foreach ($pengembalian->detailPengembalian as $detail) {
                $alat = $detail->detailPemesanan?->alat;

                if ($alat === null) {
                    continue;
                }

                $nilaiPengganti = (float) $alat->nilai_pengganti;

                foreach ([
                    'kerusakan_ringan' => (int) $detail->jumlah_rusak,
                    'kehilangan' => (int) $detail->jumlah_hilang,
                ] as $jenis => $jumlah) {
                    $a = $aturan->get($jenis);

                    if ($a === null || $jumlah <= 0 || $sudahAda($detail->id, $jenis)) {
                        continue;
                    }

                    Denda::create($dasar + [
                        'aturan_denda_id' => $a->id,
                        'detail_pengembalian_id' => $detail->id,
                        'jenis_denda' => $jenis,
                        'nilai_dasar' => $nilaiPengganti * $jumlah,
                        'jumlah_denda' => round($nilaiPengganti * $jumlah * (float) $a->nilai_denda / 100),
                        'nilai_tarif_terkait' => (float) $alat->tarif_per_hari,
                    ]);
                }
            }
        });
    }
}
