<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Pemesanan;
use App\Services\MidtransService;
use App\Services\PembayaranService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Throwable;

/**
 * Halaman "Pembayaran" admin: satu halaman, dua tab.
 *
 * - DP        : pembayaran uang muka dari customer (Midtrans atau tunai).
 * - Pelunasan : sisa sewa + denda (terlambat/rusak/hilang) saat barang
 *               dikembalikan, dibayar lewat Midtrans atau tunai.
 *
 * Semua perubahan status lewat PembayaranService.
 */
class PembayaranController extends Controller
{
    public function index(MidtransService $midtrans): View
    {
        return view('admin.pembayaran.index', [
            'dataAwal' => $this->data(app(PembayaranService::class)),
            'dataUrl' => route('admin.pembayaran.data'),
            'snapUrl' => $midtrans->urlSnapJs(),
            'clientKey' => $midtrans->clientKey(),
            'midtransAktif' => $midtrans->terkonfigurasi(),
        ]);
    }

    public function muatUlang(PembayaranService $layanan): JsonResponse
    {
        return response()->json($this->data($layanan));
    }

    /**
     * Terima DP secara tunai (atau verifikasi DP Midtrans yang sudah masuk).
     */
    public function verifikasi(Request $request, Pembayaran $pembayaran, PembayaranService $layanan): JsonResponse
    {
        $tunai = $request->input('cara') === 'tunai';

        if ($pembayaran->status_transaksi === 'pending' && ! $tunai && filled($pembayaran->midtrans)) {
            $pembayaran = $layanan->sinkronkan($pembayaran);

            if ($pembayaran->status_transaksi === 'pending') {
                return response()->json([
                    'message' => 'Midtrans belum menerima pembayaran ini. Tunggu pelanggan membayar, atau pilih "Terima Tunai".',
                ], 422);
            }
        }

        if ($pembayaran->status_transaksi !== 'berhasil') {
            $pembayaran = $layanan->tandaiBerhasil($pembayaran, $tunai ? 'tunai' : null);
        }

        return response()->json(['message' => 'Pembayaran #'.$pembayaran->id.' berhasil dicatat.'] + $this->data($layanan));
    }

    public function tolak(Pembayaran $pembayaran, PembayaranService $layanan): JsonResponse
    {
        $layanan->tandaiGagal($pembayaran);

        return response()->json(['message' => 'Pembayaran #'.$pembayaran->id.' ditolak.'] + $this->data($layanan));
    }

    /**
     * Buat pembayaran pelunasan. cara=tunai langsung lunas; cara=midtrans
     * mengembalikan snap_token untuk dibuka di halaman.
     */
    public function bayarPelunasan(Request $request, Pemesanan $pemesanan, PembayaranService $layanan, MidtransService $midtrans): JsonResponse
    {
        $cara = $request->validate(['cara' => 'required|in:tunai,midtrans'])['cara'];

        if (! in_array($pemesanan->status, ['berlangsung', 'selesai'], true) || ! $pemesanan->pengembalian()->exists()) {
            return response()->json(['message' => 'Pelunasan baru bisa dibuat setelah barang dikembalikan dan dicatat petugas.'], 422);
        }

        if ($cara === 'midtrans' && ! $midtrans->terkonfigurasi()) {
            return response()->json(['message' => 'Midtrans belum dikonfigurasi. Gunakan pembayaran tunai.'], 422);
        }

        try {
            $pembayaran = $layanan->buatPelunasan($pemesanan, $cara);
        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'message' => $cara === 'tunai'
                ? 'Pelunasan tunai berhasil dicatat.'
                : 'Tagihan Midtrans dibuat.',
            'snap_token' => $cara === 'midtrans' ? $pembayaran->midtrans : null,
            'pembayaran_id' => $pembayaran->id,
        ] + $this->data($layanan));
    }

    /**
     * Cek status Midtrans satu pembayaran (dipanggil setelah pop-up ditutup).
     */
    public function sinkron(Pembayaran $pembayaran, PembayaranService $layanan): JsonResponse
    {
        $pembayaran = $layanan->sinkronkan($pembayaran);

        return response()->json([
            'message' => match ($pembayaran->status_transaksi) {
                'berhasil' => 'Pembayaran berhasil diterima.',
                'gagal' => 'Pembayaran gagal atau kedaluwarsa.',
                default => 'Pembayaran belum diterima Midtrans.',
            },
        ] + $this->data($layanan));
    }

    /**
     * @return array{dp: Collection<int, array<string, mixed>>, pelunasan: Collection<int, array<string, mixed>>}
     */
    private function data(PembayaranService $layanan): array
    {
        return [
            'dp' => $this->daftarDp(),
            'pelunasan' => $this->daftarPelunasan($layanan),
        ];
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function daftarDp(): Collection
    {
        return Pembayaran::query()
            ->where('jenis_pembayaran', 'dp')
            ->with(['pemesanan:id,kode_pemesanan,user_id', 'pemesanan.pelanggan:id,name,no_telepon'])
            ->orderByRaw("CASE WHEN status_transaksi = 'pending' THEN 0 ELSE 1 END")
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (Pembayaran $p): array => $this->baris($p))
            ->values();
    }

    /**
     * Pemesanan yang sedang/telah menyewa, lengkap dengan rincian tagihan.
     * Yang masih punya tagihan atau sedang menunggu pembayaran tampil lebih dulu.
     *
     * @return Collection<int, array<string, mixed>>
     */
    private function daftarPelunasan(PembayaranService $layanan): Collection
    {
        return Pemesanan::query()
            ->whereIn('status', ['berlangsung', 'selesai'])
            ->with([
                'pelanggan:id,name,no_telepon',
                'pembayaran',
                'denda',
                'pengembalian:id,pemesanan_id,hari_terlambat',
            ])
            ->latest('id')
            ->limit(200)
            ->get()
            ->map(function (Pemesanan $p) use ($layanan): array {
                $tagihan = $layanan->hitungTagihan($p);
                $bayar = $p->pembayaran->where('jenis_pembayaran', '!=', 'dp');
                $menunggu = $bayar->firstWhere('status_transaksi', 'pending');
                $berhasil = $bayar->where('status_transaksi', 'berhasil')->sortByDesc('id')->first();
                $dikembalikan = $p->pengembalian->isNotEmpty();

                $status = match (true) {
                    $menunggu !== null => 'menunggu',
                    $tagihan['total'] > 0 => 'belum_bayar',
                    default => 'lunas',
                };

                return [
                    'id' => $p->id,
                    'kode_pemesanan' => $p->kode_pemesanan,
                    'pelanggan_nama' => $p->pelanggan?->name ?? 'Pelanggan tidak ditemukan',
                    'pelanggan_telp' => $p->pelanggan?->no_telepon ?? '-',
                    'status_pemesanan' => $p->status,
                    'dikembalikan' => $dikembalikan,
                    'hari_terlambat' => (int) $p->pengembalian->sum('hari_terlambat'),
                    'total_biaya' => (float) $p->total_biaya,
                    'dp' => (float) $p->jumlah_dp,
                    'sisa_sewa' => $tagihan['sisa_sewa'],
                    'denda' => $tagihan['denda'],
                    'total_tagihan' => $tagihan['total'],
                    'rincian_denda' => $p->denda->where('status', 'belum_dibayar')->map(fn ($d): array => [
                        'jenis' => ucwords(str_replace('_', ' ', $d->jenis_denda)),
                        'jumlah' => (float) $d->jumlah_denda,
                    ])->values(),
                    'status' => $status,
                    'metode' => $berhasil?->metode_pembayaran ? strtoupper(str_replace('_', ' ', $berhasil->metode_pembayaran)) : null,
                    'waktu_bayar' => $berhasil?->waktu_bayar?->translatedFormat('d M Y, H:i'),
                    'pembayaran_menunggu_id' => $menunggu?->id,
                    'snap_token' => $menunggu?->midtrans,
                    'url_bayar' => route('admin.pembayaran.pelunasan', $p),
                    'url_sinkron' => $menunggu ? route('admin.pembayaran.sinkron', $menunggu) : null,
                ];
            })
            ->sortBy(fn (array $r): int => match ($r['status']) {
                'menunggu' => 0,
                'belum_bayar' => $r['dikembalikan'] ? 1 : 2,
                default => 3,
            })
            ->values();
    }

    /**
     * @return array<string, mixed>
     */
    private function baris(Pembayaran $item): array
    {
        $pemesanan = $item->pemesanan;
        $pelanggan = $pemesanan?->pelanggan;

        return [
            'id' => $item->id,
            'pemesanan_id' => $item->pemesanan_id,
            'kode_pemesanan' => $pemesanan?->kode_pemesanan ?? ('#'.$item->pemesanan_id),
            'pelanggan_nama' => $pelanggan?->name ?? 'Pelanggan tidak ditemukan',
            'pelanggan_telp' => $pelanggan?->no_telepon ?? '-',
            'transaction_id' => $item->transaction_id ?? '-',
            'metode_label' => $item->metode_pembayaran
                ? strtoupper(str_replace('_', ' ', $item->metode_pembayaran))
                : 'Belum dipilih',
            'jumlah' => (float) $item->jumlah,
            'status_transaksi' => $item->status_transaksi,
            'waktu_bayar' => $item->waktu_bayar?->translatedFormat('d M Y, H:i') ?? '-',
            'url_verifikasi' => route('admin.pembayaran.verifikasi', $item),
            'url_tolak' => route('admin.pembayaran.tolak', $item),
        ];
    }
}
