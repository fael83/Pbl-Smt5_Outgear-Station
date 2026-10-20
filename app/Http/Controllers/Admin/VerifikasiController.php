<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

/**
 * Halaman verifikasi pembayaran pada panel admin.
 *
 * Semua data dibaca langsung dari tabel `pembayaran`, di-join lewat relasi
 * Eloquent ke `pemesanan` dan `users` (pelanggan). Tidak ada data statis/dummy.
 */
class VerifikasiController extends Controller
{
    public function index(): View
    {
        $pembayaran = Pembayaran::query()
            ->with([
                'pemesanan:id,kode_pemesanan,user_id',
                'pemesanan.pelanggan:id,name,no_telepon',
            ])
            // Yang pending ditampilkan lebih dulu supaya langsung terlihat perlu ditindak.
            ->orderByRaw("CASE WHEN status_transaksi = 'pending' THEN 0 ELSE 1 END")
            ->orderByDesc('created_at')
            ->get();

        $pembayaranList = $pembayaran->map(function (Pembayaran $item): array {
            $pemesanan = $item->pemesanan;
            $pelanggan = $pemesanan?->pelanggan;

            return [
                'id' => $item->id,
                'pemesanan_id' => $item->pemesanan_id,
                'kode_pemesanan' => $pemesanan?->kode_pemesanan ?? ('#' . $item->pemesanan_id),
                'pelanggan_nama' => $pelanggan?->name ?? 'Pelanggan tidak ditemukan',
                'pelanggan_telp' => $pelanggan?->no_telepon ?? '-',
                'transaction_id_midtrans' => $item->transaction_id ?? '-',
                'jenis_pembayaran' => $item->jenis_pembayaran,
                'metode_pembayaran' => $item->metode_pembayaran,
                'jumlah' => (float) $item->jumlah,
                'status_transaksi' => $item->status_transaksi,
                'waktu_bayar' => $item->waktu_bayar?->translatedFormat('d M Y, H:i') ?? '-',
                'url_verifikasi' => route('admin.verifikasi.verifikasi', $item),
                'url_tolak' => route('admin.verifikasi.tolak', $item),
            ];
        })->values();

        return view('admin.verifikasi', [
            'pembayaranList' => $pembayaranList,
        ]);
    }

    /**
     * Setujui pembayaran: status_transaksi -> berhasil.
     */
    public function verifikasi(Pembayaran $pembayaran): JsonResponse
    {
        $pembayaran->update([
            'status_transaksi' => 'berhasil',
            'waktu_bayar' => $pembayaran->waktu_bayar ?? now(),
        ]);

        return response()->json([
            'message' => 'Pembayaran #' . $pembayaran->id . ' berhasil diverifikasi.',
            'status_transaksi' => $pembayaran->status_transaksi,
            'waktu_bayar' => $pembayaran->waktu_bayar->translatedFormat('d M Y, H:i'),
        ]);
    }

    /**
     * Tolak pembayaran: status_transaksi -> gagal.
     */
    public function tolak(Pembayaran $pembayaran): JsonResponse
    {
        $pembayaran->update([
            'status_transaksi' => 'gagal',
        ]);

        return response()->json([
            'message' => 'Pembayaran #' . $pembayaran->id . ' ditolak.',
            'status_transaksi' => $pembayaran->status_transaksi,
        ]);
    }
}
