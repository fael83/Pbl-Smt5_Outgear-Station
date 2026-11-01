<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\Alat;
use App\Models\DetailPemesanan;
use App\Models\Pembayaran;
use App\Models\Pemesanan;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    /**
     * Memproses checkout pemesanan sewa peralatan dari keranjang belanja katalog.
     *
     * Data yang dibuat:
     * - Pemesanan (status: menunggu_pembayaran)
     * - DetailPemesanan (item-item alat yang disewa)
     * - Pembayaran (status_transaksi: pending)
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['required', 'integer', 'exists:alat,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_selesai' => ['nullable', 'date'],
            'nama_penyewa' => ['nullable', 'string', 'max:255'],
            'no_telepon' => ['nullable', 'string', 'max:30'],
        ]);

        $tanggalMulai = ! empty($validated['tanggal_mulai'])
            ? Carbon::parse($validated['tanggal_mulai'])->startOfDay()
            : now()->startOfDay();

        $tanggalSelesai = ! empty($validated['tanggal_selesai'])
            ? Carbon::parse($validated['tanggal_selesai'])->startOfDay()
            : $tanggalMulai->copy()->addDays(2)->startOfDay();

        // Pastikan tanggal selesai tidak lebih awal dari tanggal mulai
        if ($tanggalSelesai->lt($tanggalMulai)) {
            $tanggalSelesai = $tanggalMulai->copy()->addDays(2)->startOfDay();
        }

        $lamaSewa = max(1, (int) $tanggalMulai->diffInDays($tanggalSelesai) + 1);

        // Cari atau tentukan user pelanggan
        $user = auth()->user();
        if ($user === null) {
            if (! empty($validated['nama_penyewa'])) {
                $user = User::firstOrCreate(
                    ['no_telepon' => $validated['no_telepon'] ?: '081' . rand(10000000, 99999999)],
                    [
                        'name' => $validated['nama_penyewa'],
                        'email' => 'customer_' . Str::random(6) . '@outgear.test',
                        'password' => bcrypt('password123'),
                        'role' => Role::Customer,
                        'status_akun' => 'aktif',
                    ]
                );
            } else {
                $user = User::where('role', Role::Customer)->first() ?? User::first();
            }
        }

        $userId = $user?->id ?? 1;

        $pemesanan = DB::transaction(function () use ($validated, $userId, $tanggalMulai, $tanggalSelesai, $lamaSewa): Pemesanan {
            // Hitung nomor urut untuk format kode pemesanan
            $urutan = Pemesanan::query()->count() + 1;
            $kodePemesanan = 'ORD-' . date('Ymd') . '-' . str_pad((string) $urutan, 4, '0', STR_PAD_LEFT);

            while (Pemesanan::query()->where('kode_pemesanan', $kodePemesanan)->exists()) {
                $urutan++;
                $kodePemesanan = 'ORD-' . date('Ymd') . '-' . str_pad((string) $urutan, 4, '0', STR_PAD_LEFT);
            }

            $pemesanan = Pemesanan::create([
                'kode_pemesanan' => $kodePemesanan,
                'user_id' => $userId,
                'tanggal_mulai' => $tanggalMulai->toDateString(),
                'tanggal_selesai' => $tanggalSelesai->toDateString(),
                'total_biaya' => 0,
                'jumlah_dp' => 0,
                'status' => 'menunggu_pembayaran',
            ]);

            $totalBiaya = 0;

            foreach ($validated['items'] as $item) {
                $alat = Alat::find($item['id']);
                if ($alat === null) {
                    continue;
                }

                $qty = (int) $item['qty'];
                $subtotal = $qty * (float) $alat->tarif_per_hari * $lamaSewa;
                $totalBiaya += $subtotal;

                DetailPemesanan::create([
                    'pemesanan_id' => $pemesanan->id,
                    'alat_id' => $alat->id,
                    'jumlah' => $qty,
                    'tarif_per_hari' => $alat->tarif_per_hari,
                    'subtotal' => $subtotal,
                ]);

                // Kurangi stok tersedia dan tambahkan stok disewa jika cukup
                if ($alat->stok_tersedia >= $qty) {
                    $alat->decrement('stok_tersedia', $qty);
                    $alat->increment('stok_disewa', $qty);
                }
            }

            $jumlahDp = round($totalBiaya * 0.5);

            $pemesanan->update([
                'total_biaya' => $totalBiaya,
                'jumlah_dp' => $jumlahDp,
            ]);

            // Buat record pembayaran DP awal dengan status pending
            Pembayaran::create([
                'pemesanan_id' => $pemesanan->id,
                'transaction_id' => 'MID-' . strtoupper(Str::random(10)),
                'jenis_pembayaran' => 'dp',
                'metode_pembayaran' => 'qris',
                'jumlah' => $jumlahDp,
                'status_transaksi' => 'pending',
                'waktu_bayar' => null,
            ]);

            // Hapus cache statistik & badge admin supaya data di /admin/pemesanan langsung update
            Cache::forget('pemesanan.statistik');
            Cache::forget('sidebar:badge');

            return $pemesanan;
        });

        return response()->json([
            'success' => true,
            'message' => 'Pemesanan berhasil dibuat!',
            'kode_pemesanan' => $pemesanan->kode_pemesanan,
            'total_biaya' => $pemesanan->total_biaya,
            'lama_sewa' => $lamaSewa,
            'redirect_admin' => route('admin.pemesanan'),
            'redirect_booking' => route('booking.saya'),
        ]);
    }
}

