<?php

namespace App\Services;

use App\Models\Alat;
use App\Models\Pembayaran;
use App\Models\Pemesanan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Satu-satunya tempat yang mengubah status pembayaran DP dan dampaknya ke
 * pemesanan + stok. Dipakai oleh webhook Midtrans, sinkronisasi status,
 * pembatalan otomatis, dan tombol Verifikasi/Tolak admin, sehingga hasilnya
 * selalu konsisten.
 */
class PembayaranService
{
    public function __construct(private readonly MidtransService $midtrans) {}

    /**
     * Terapkan data notifikasi/status dari Midtrans ke pembayaran.
     * Aman dipanggil berulang (idempoten).
     *
     * @param  array<string, mixed>  $data
     */
    public function terapkanDariMidtrans(Pembayaran $pembayaran, array $data): Pembayaran
    {
        if (isset($data['gross_amount'])
            && (int) round((float) $data['gross_amount']) !== (int) round((float) $pembayaran->jumlah)) {
            Log::warning('Midtrans: nominal tidak cocok', ['order_id' => $pembayaran->transaction_id]);

            return $pembayaran;
        }

        $status = (string) ($data['transaction_status'] ?? '');
        $fraud = (string) ($data['fraud_status'] ?? 'accept');
        $metode = $data['payment_type'] ?? null;

        if (in_array($status, ['settlement', 'capture'], true) && $fraud === 'accept') {
            return $this->tandaiBerhasil($pembayaran, $metode);
        }

        if (in_array($status, ['deny', 'cancel', 'expire', 'failure'], true)) {
            return $this->tandaiGagal($pembayaran);
        }

        // pending: pelanggan sudah memilih metode tapi belum membayar.
        if ($status === 'pending' && $metode && $pembayaran->status_transaksi === 'pending') {
            $pembayaran->update(['metode_pembayaran' => $metode]);
        }

        return $pembayaran;
    }

    /**
     * Pembayaran lunas: pemesanan menunggu_pembayaran -> dikonfirmasi.
     */
    public function tandaiBerhasil(Pembayaran $pembayaran, ?string $metode = null): Pembayaran
    {
        return DB::transaction(function () use ($pembayaran, $metode): Pembayaran {
            $baru = Pembayaran::query()->lockForUpdate()->findOrFail($pembayaran->id);

            if ($baru->status_transaksi === 'berhasil') {
                return $baru;
            }

            $baru->update([
                'status_transaksi' => 'berhasil',
                'metode_pembayaran' => $metode ?? $baru->metode_pembayaran,
                'waktu_bayar' => $baru->waktu_bayar ?? now(),
            ]);

            $pemesanan = $baru->pemesanan;

            if ($baru->jenis_pembayaran === 'dp' && $pemesanan) {
                if ($pemesanan->status === 'menunggu_pembayaran') {
                    $pemesanan->update(['status' => 'dikonfirmasi']);
                } elseif ($pemesanan->status === 'dibatalkan') {
                    // Uang masuk setelah booking sempat dibatalkan (kedaluwarsa): coba hidupkan lagi.
                    $this->hidupkanKembali($pemesanan);
                }
            }

            return $baru;
        });
    }

    /**
     * Pembayaran gagal/ditolak/kedaluwarsa: booking dibatalkan dan stok dikembalikan
     * selama belum ada pembayaran lain yang berhasil.
     */
    public function tandaiGagal(Pembayaran $pembayaran): Pembayaran
    {
        return DB::transaction(function () use ($pembayaran): Pembayaran {
            $baru = Pembayaran::query()->lockForUpdate()->findOrFail($pembayaran->id);

            if ($baru->status_transaksi === 'gagal') {
                return $baru;
            }

            $baru->update(['status_transaksi' => 'gagal']);

            $pemesanan = $baru->pemesanan;

            if ($baru->jenis_pembayaran === 'dp' && $pemesanan
                && in_array($pemesanan->status, ['menunggu_pembayaran', 'dikonfirmasi'], true)
                && ! $pemesanan->pembayaran()->where('status_transaksi', 'berhasil')->exists()) {
                $this->batalkan($pemesanan);
            }

            return $baru;
        });
    }

    /**
     * Batalkan booking + kembalikan stok yang dipesan.
     */
    public function batalkan(Pemesanan $pemesanan): void
    {
        if ($pemesanan->status === 'dibatalkan') {
            return;
        }

        DB::transaction(function () use ($pemesanan): void {
            foreach ($pemesanan->detailPemesanan()->get() as $detail) {
                $alat = Alat::query()->lockForUpdate()->find($detail->alat_id);

                if ($alat) {
                    $alat->update([
                        'stok_tersedia' => $alat->stok_tersedia + $detail->jumlah,
                        'stok_disewa' => max(0, $alat->stok_disewa - $detail->jumlah),
                    ]);
                }
            }

            $pemesanan->update([
                'status' => 'dibatalkan',
                'waktu_pembatalan' => now(),
            ]);
        });
    }

    /**
     * Pelanggan membatalkan booking yang DP-nya belum dibayar.
     *
     * @return string|null pesan alasan penolakan, atau null bila berhasil
     */
    public function batalkanOlehPelanggan(Pemesanan $pemesanan): ?string
    {
        if ($pemesanan->status !== 'menunggu_pembayaran') {
            return 'Booking ini tidak bisa dibatalkan karena DP sudah dibayar atau booking sudah selesai/dibatalkan.';
        }

        $dp = $pemesanan->pembayaran()->where('jenis_pembayaran', 'dp')->where('status_transaksi', 'pending')->first();

        if ($dp === null) {
            $this->batalkan($pemesanan);

            return null;
        }

        // Pastikan pelanggan belum sempat membayar.
        $dp = $this->sinkronkan($dp);

        if ($dp->status_transaksi === 'berhasil') {
            return 'DP sudah terbayar, booking tidak bisa dibatalkan dari sini.';
        }

        if ($dp->status_transaksi === 'pending' && filled($dp->metode_pembayaran) && $this->midtrans->terkonfigurasi()) {
            try {
                $this->midtrans->batalkan((string) $dp->transaction_id);
            } catch (\Throwable $e) {
                Log::warning('Midtrans: gagal membatalkan transaksi', ['pesan' => $e->getMessage()]);
            }
        }

        $this->tandaiGagal($dp);
        Cache::forget('pemesanan.statistik');

        return null;
    }

    /**
     * Tarik status dari Midtrans lalu terapkan. Dipakai sebagai cadangan saat
     * webhook tidak sampai (mis. server lokal tanpa URL publik).
     */
    public function sinkronkan(Pembayaran $pembayaran): Pembayaran
    {
        if ($pembayaran->status_transaksi !== 'pending' || ! $this->midtrans->terkonfigurasi()) {
            return $pembayaran;
        }

        try {
            $data = $this->midtrans->cekStatus((string) $pembayaran->transaction_id);
        } catch (\Throwable $e) {
            Log::warning('Midtrans: gagal cek status', ['pesan' => $e->getMessage()]);

            return $pembayaran;
        }

        return $data ? $this->terapkanDariMidtrans($pembayaran, $data) : $pembayaran;
    }

    /**
     * Batalkan semua booking yang DP-nya belum dibayar melewati batas waktu.
     * Status di Midtrans dicek dulu supaya pembayaran yang baru masuk tidak ikut dibatalkan.
     *
     * @return int jumlah booking yang dibatalkan
     */
    public function batalkanKedaluwarsa(?int $userId = null): int
    {
        // +2 menit sebagai toleransi keterlambatan notifikasi.
        $batas = now()->subMinutes((int) config('services.midtrans.batas_bayar_menit', 60) + 2);

        $daftar = Pemesanan::query()
            ->where('status', 'menunggu_pembayaran')
            ->where('created_at', '<', $batas)
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->with(['pembayaran' => fn ($q) => $q->where('jenis_pembayaran', 'dp')->where('status_transaksi', 'pending')])
            ->get();

        $jumlah = 0;

        foreach ($daftar as $pemesanan) {
            $pembayaran = $pemesanan->pembayaran->first();

            if ($pembayaran) {
                $pembayaran = $this->sinkronkan($pembayaran);

                if ($pembayaran->status_transaksi === 'berhasil') {
                    continue;
                }

                $this->tandaiGagal($pembayaran);
            } else {
                $this->batalkan($pemesanan);
            }

            $jumlah++;
        }

        return $jumlah;
    }

    /**
     * Hidupkan booking yang terlanjur dibatalkan bila stoknya masih cukup.
     */
    private function hidupkanKembali(Pemesanan $pemesanan): void
    {
        $detail = $pemesanan->detailPemesanan()->get();
        $alatList = Alat::query()->lockForUpdate()->whereIn('id', $detail->pluck('alat_id'))->get()->keyBy('id');

        foreach ($detail as $d) {
            if (($alatList[$d->alat_id]->stok_tersedia ?? 0) < $d->jumlah) {
                Log::warning('Pembayaran masuk untuk booking dibatalkan tapi stok habis', ['kode' => $pemesanan->kode_pemesanan]);

                return; // tetap dibatalkan; admin perlu mengembalikan dana manual
            }
        }

        foreach ($detail as $d) {
            $alat = $alatList[$d->alat_id];
            $alat->update([
                'stok_tersedia' => $alat->stok_tersedia - $d->jumlah,
                'stok_disewa' => $alat->stok_disewa + $d->jumlah,
            ]);
        }

        $pemesanan->update(['status' => 'dikonfirmasi', 'waktu_pembatalan' => null]);
    }
}
