<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\DetailPemesanan;
use App\Models\Pembayaran;
use App\Models\Pemesanan;
use App\Services\MidtransService;
use App\Services\PembayaranService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Checkout keranjang -> booking + tagihan DP 50% -> token Midtrans Snap.
 *
 * Route dilindungi middleware `auth` + `role:customer`, jadi tidak ada lagi
 * pembuatan akun tamu otomatis.
 */
class CheckoutController extends Controller
{
    public const PERSEN_DP = 0.5;

    public function store(Request $request, MidtransService $midtrans, PembayaranService $layanan): JsonResponse
    {
        $data = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['required', 'integer'],
            'items.*.qty' => ['required', 'integer', 'min:1', 'max:99'],
            'tanggal_mulai' => ['required', 'date', 'after_or_equal:today'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
        ], [
            'tanggal_mulai.after_or_equal' => 'Tanggal mulai sewa tidak boleh sebelum hari ini.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
        ]);

        if (! $midtrans->terkonfigurasi()) {
            return response()->json([
                'success' => false,
                'message' => 'Pembayaran online belum dikonfigurasi. Hubungi admin.',
            ], 503);
        }

        $pelanggan = $request->user();
        $mulai = Carbon::parse($data['tanggal_mulai'])->startOfDay();
        $selesai = Carbon::parse($data['tanggal_selesai'])->startOfDay();
        $lamaSewa = max(1, (int) $mulai->diffInDays($selesai) + 1);

        // Gabungkan baris duplikat (alat yang sama) menjadi satu.
        $permintaan = collect($data['items'])
            ->groupBy('id')
            ->map(fn ($baris) => (int) $baris->sum('qty'));

        try {
            [$pemesanan, $pembayaran] = DB::transaction(function () use ($permintaan, $pelanggan, $mulai, $selesai, $lamaSewa) {
                $alatList = Alat::query()
                    ->whereIn('id', $permintaan->keys())
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                // 1) Pastikan semua alat ada dan stok cukup sebelum mengubah apa pun.
                foreach ($permintaan as $alatId => $qty) {
                    $alat = $alatList[$alatId] ?? null;

                    if ($alat === null) {
                        throw ValidationException::withMessages(['items' => ['Ada alat di keranjang yang sudah tidak tersedia.']]);
                    }

                    if ($alat->stok_tersedia < $qty) {
                        throw ValidationException::withMessages([
                            'items' => ["Stok {$alat->nama} tinggal {$alat->stok_tersedia} unit."],
                        ]);
                    }
                }

                // 2) Hitung total di memori, lalu simpan dengan sesedikit mungkin query
                //    (koneksi ke Supabase ~1 detik per query).
                $waktu = now();
                $baris = [];
                $total = 0;

                foreach ($permintaan as $alatId => $qty) {
                    $alat = $alatList[$alatId];
                    $subtotal = $qty * (float) $alat->tarif_per_hari * $lamaSewa;
                    $total += $subtotal;

                    $baris[] = [
                        'alat_id' => $alat->id,
                        'jumlah' => $qty,
                        'tarif_per_hari' => $alat->tarif_per_hari,
                        'subtotal' => $subtotal,
                        'created_at' => $waktu,
                        'updated_at' => $waktu,
                    ];
                }

                $dp = round($total * self::PERSEN_DP);

                $pemesanan = Pemesanan::create([
                    'kode_pemesanan' => $this->buatKode(),
                    'user_id' => $pelanggan->id,
                    'tanggal_mulai' => $mulai->toDateString(),
                    'tanggal_selesai' => $selesai->toDateString(),
                    'total_biaya' => $total,
                    'jumlah_dp' => $dp,
                    'status' => 'menunggu_pembayaran',
                ]);

                DetailPemesanan::insert(array_map(fn (array $b) => $b + ['pemesanan_id' => $pemesanan->id], $baris));

                // Satu query untuk semua stok: tersedia dikurangi, disewa ditambah.
                $kasus = collect($permintaan)->map(fn ($qty, $id) => 'WHEN '.(int) $id.' THEN '.(int) $qty)->implode(' ');
                DB::update(
                    "UPDATE alat SET stok_tersedia = stok_tersedia - CASE id {$kasus} ELSE 0 END, "
                    ."stok_disewa = stok_disewa + CASE id {$kasus} ELSE 0 END, updated_at = ? "
                    .'WHERE id IN ('.implode(',', array_map('intval', $permintaan->keys()->all())).')',
                    [$waktu]
                );

                $pembayaran = Pembayaran::create([
                    'pemesanan_id' => $pemesanan->id,
                    'transaction_id' => $pemesanan->kode_pemesanan.'-DP',
                    'jenis_pembayaran' => 'dp',
                    'metode_pembayaran' => null,
                    'jumlah' => $dp,
                    'status_transaksi' => 'pending',
                ]);

                return [$pemesanan, $pembayaran];
            });
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => collect($e->errors())->flatten()->first(),
            ], 422);
        }

        Cache::forget('pemesanan.statistik');

        // Token diminta setelah transaksi database selesai agar koneksi tidak tertahan.
        try {
            $token = $midtrans->buatSnapToken($pembayaran, $pelanggan, 'DP 50% sewa '.$pemesanan->kode_pemesanan);
            $pembayaran->update(['midtrans' => $token]);
        } catch (\Throwable $e) {
            report($e);
            $layanan->tandaiGagal($pembayaran);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghubungi Midtrans. Booking dibatalkan, silakan coba lagi.',
            ], 502);
        }

        return response()->json([
            'success' => true,
            'message' => 'Booking dibuat. Selesaikan pembayaran DP.',
            'kode_pemesanan' => $pemesanan->kode_pemesanan,
            'total_biaya' => (float) $pemesanan->total_biaya,
            'jumlah_dp' => (float) $pemesanan->jumlah_dp,
            'lama_sewa' => $lamaSewa,
            'snap_token' => $token,
            'url_sinkron' => route('pembayaran.sinkron', $pembayaran),
            'redirect_booking' => route('booking.saya'),
        ]);
    }

    /**
     * Kode unik tanpa query ke database: ORD-20261107-K7P2Q.
     */
    private function buatKode(): string
    {
        return 'ORD-'.date('Ymd').'-'.strtoupper(Str::random(5));
    }
}
