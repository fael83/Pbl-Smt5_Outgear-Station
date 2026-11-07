<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Pemesanan;
use App\Services\MidtransService;
use App\Services\PembayaranService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Aksi pembayaran milik pelanggan yang sedang login: lanjutkan/ganti metode bayar,
 * sinkronkan status dari Midtrans, dan batalkan booking yang belum dibayar.
 */
class PembayaranPelangganController extends Controller
{
    /**
     * Ambil token Snap untuk membayar tagihan yang masih pending.
     *
     * Tanpa `ganti`: token lama dipakai lagi (pelanggan melanjutkan pembayaran yang sudah dipilih).
     * Dengan `ganti=1`: transaksi lama di Midtrans dibatalkan dan dibuat order baru,
     * sehingga pelanggan bisa memilih metode pembayaran lain.
     */
    public function bayar(Request $request, Pembayaran $pembayaran, MidtransService $midtrans, PembayaranService $layanan): JsonResponse
    {
        $this->pastikanMilikSendiri($request, $pembayaran);

        $pembayaran = $layanan->sinkronkan($pembayaran);
        $pemesanan = $pembayaran->pemesanan;

        if ($pembayaran->status_transaksi !== 'pending' || $pemesanan->status !== 'menunggu_pembayaran') {
            return response()->json([
                'success' => false,
                'message' => 'Tagihan ini sudah tidak dapat dibayar.',
                'status_transaksi' => $pembayaran->status_transaksi,
            ], 409);
        }

        $sisaMenit = (int) config('services.midtrans.batas_bayar_menit', 60)
            - (int) $pemesanan->created_at->diffInMinutes(now());

        if ($sisaMenit < 1) {
            return response()->json(['success' => false, 'message' => 'Batas waktu pembayaran sudah habis.'], 409);
        }

        $ganti = $request->boolean('ganti') && filled($pembayaran->metode_pembayaran);

        if ($ganti || blank($pembayaran->midtrans)) {
            $idLama = $pembayaran->transaction_id;

            try {
                if ($ganti) {
                    // Order baru dipasang lebih dulu supaya notifikasi "cancel" untuk order lama
                    // tidak ikut membatalkan booking.
                    $pembayaran->update([
                        'transaction_id' => $pemesanan->kode_pemesanan.'-DP-'.Str::lower(Str::random(4)),
                        'midtrans' => null,
                        'metode_pembayaran' => null,
                    ]);

                    // Pembatalan transaksi lama hanya "sebisanya": Midtrans kadang menolaknya
                    // (mis. QRIS yang sudah menunggu bayar). Kalau ditolak, transaksi lama
                    // dibiarkan kedaluwarsa sendiri; bila terlanjur dibayar, webhook tetap
                    // mengenalinya lewat awalan kode booking.
                    try {
                        $midtrans->batalkan((string) $idLama);
                    } catch (\Throwable $e) {
                        report($e);
                    }
                }

                $pembayaran->update([
                    'midtrans' => $midtrans->buatSnapToken($pembayaran, $request->user(), 'DP 50% sewa '.$pemesanan->kode_pemesanan, $sisaMenit),
                ]);
            } catch (\Throwable $e) {
                report($e);

                return response()->json(['success' => false, 'message' => 'Gagal menghubungi Midtrans.'], 502);
            }
        }

        return response()->json([
            'success' => true,
            'snap_token' => $pembayaran->midtrans,
            'url_sinkron' => route('pembayaran.sinkron', $pembayaran),
        ]);
    }

    /**
     * Tarik status terbaru dari Midtrans untuk tagihan ini.
     */
    public function sinkron(Request $request, Pembayaran $pembayaran, PembayaranService $layanan): JsonResponse
    {
        $this->pastikanMilikSendiri($request, $pembayaran);

        $pembayaran = $layanan->sinkronkan($pembayaran)->load('pemesanan');

        return response()->json([
            'success' => true,
            'status_transaksi' => $pembayaran->status_transaksi,
            'status_pemesanan' => $pembayaran->pemesanan->status,
        ]);
    }

    /**
     * Batalkan booking milik pelanggan yang DP-nya belum dibayar.
     */
    public function batalkanPesanan(Request $request, Pemesanan $pemesanan, PembayaranService $layanan): JsonResponse
    {
        abort_unless($pemesanan->user_id === $request->user()->id, 403);

        $alasan = $layanan->batalkanOlehPelanggan($pemesanan);

        if ($alasan !== null) {
            return response()->json(['success' => false, 'message' => $alasan], 409);
        }

        return response()->json(['success' => true, 'message' => 'Booking dibatalkan dan stok dikembalikan.']);
    }

    private function pastikanMilikSendiri(Request $request, Pembayaran $pembayaran): void
    {
        abort_unless($pembayaran->pemesanan->user_id === $request->user()->id, 403);
    }
}
