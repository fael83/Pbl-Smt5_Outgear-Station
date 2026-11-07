<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Services\PembayaranService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
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
        return view('admin.verifikasi.index', [
            'pembayaranList' => $this->daftarPembayaran(),
        ]);
    }

    /**
     * Endpoint JSON untuk tombol "Muat ulang data": antrean terbaru dari database.
     */
    public function data(): JsonResponse
    {
        return response()->json(['items' => $this->daftarPembayaran()]);
    }

    /**
     * Seluruh pembayaran (pending lebih dulu) dalam bentuk siap pakai di Alpine.
     *
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    private function daftarPembayaran(): Collection
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

        return $pembayaran->map(function (Pembayaran $item): array {
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
                'metode_label' => $item->metode_pembayaran
                    ? strtoupper(str_replace('_', ' ', $item->metode_pembayaran))
                    : 'Belum dipilih',
                'jumlah' => (float) $item->jumlah,
                'status_transaksi' => $item->status_transaksi,
                'waktu_bayar' => $item->waktu_bayar?->translatedFormat('d M Y, H:i') ?? '-',
                'url_verifikasi' => route('admin.verifikasi.verifikasi', $item),
                'url_tolak' => route('admin.verifikasi.tolak', $item),
            ];
        })->values();
    }

    /**
     * Setujui pembayaran: status_transaksi -> berhasil, pemesanan -> dikonfirmasi.
     *
     * Untuk tagihan Midtrans yang masih pending, status dicek ke Midtrans dulu.
     * Bila Midtrans belum menerima uangnya, admin tidak bisa memverifikasi
     * (kecuali parameter `paksa` dikirim, untuk kasus koreksi manual).
     */
    public function verifikasi(Request $request, Pembayaran $pembayaran, PembayaranService $layanan): JsonResponse
    {
        if ($pembayaran->status_transaksi === 'pending' && filled($pembayaran->midtrans) && ! $request->boolean('paksa')) {
            $pembayaran = $layanan->sinkronkan($pembayaran);

            if ($pembayaran->status_transaksi === 'pending') {
                return response()->json([
                    'message' => 'Midtrans belum menerima pembayaran ini. Tunggu pelanggan membayar.',
                ], 422);
            }
        }

        if ($pembayaran->status_transaksi !== 'berhasil') {
            $pembayaran = $layanan->tandaiBerhasil($pembayaran);
        }

        return response()->json([
            'message' => 'Pembayaran #'.$pembayaran->id.' berhasil diverifikasi.',
            'status_transaksi' => $pembayaran->status_transaksi,
            'waktu_bayar' => $pembayaran->waktu_bayar?->translatedFormat('d M Y, H:i'),
        ]);
    }

    /**
     * Tolak pembayaran: status_transaksi -> gagal, booking dibatalkan dan stok kembali.
     */
    public function tolak(Pembayaran $pembayaran, PembayaranService $layanan): JsonResponse
    {
        $pembayaran = $layanan->tandaiGagal($pembayaran);

        return response()->json([
            'message' => 'Pembayaran #'.$pembayaran->id.' ditolak.',
            'status_transaksi' => $pembayaran->status_transaksi,
        ]);
    }
}
