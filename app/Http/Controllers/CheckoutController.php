<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Pemesanan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:alat,id', // <-- Sesuaikan ke tabel 'alat'
            'items.*.qty' => 'required|integer|min:1',
            'tanggal_mulai' => 'required|date|after_or_equal:today',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'nama_penyewa' => 'required|string|max:255',
            'no_telepon' => 'required|string|max:20',
        ]);

        DB::beginTransaction();
        try {
            // 1. Hubungkan / Login-kan User
            if (Auth::check()) {
                $user = Auth::user();
            } else {
                // Pengunjung tanpa login (Guest Checkout) -> Buat/Cari akun berdasarkan No. Telp
                $user = User::firstOrCreate(
                    ['no_telepon' => $request->no_telepon],
                    [
                        'name' => $request->nama_penyewa,
                        'email' => 'guest_' . time() . '_' . Str::random(5) . '@outgear.station',
                        'password' => Hash::make('password123'),
                        'role' => 'customer',
                    ]
                );

                // Auto-login agar halaman 'Booking Saya' langsung membaca sesi user ini
                Auth::login($user);
            }

            // 2. Hitung Durasi & Total Biaya
            $d1 = new \DateTime($request->tanggal_mulai);
            $d2 = new \DateTime($request->tanggal_selesai);
            $diff = $d1->diff($d2)->days;
            $lamaSewa = max(1, $diff + 1);

            $totalBiaya = 0;
            $itemsToSave = [];

            foreach ($request->items as $itemData) {
                // Menggunakan Model Alat (Tabel: alat)
                $alat = Alat::where('id', $itemData['id'])->lockForUpdate()->first();

                if (!$alat || $alat->stok_tersedia < $itemData['qty']) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Stok ' . ($alat ? $alat->nama : 'peralatan') . ' tidak mencukupi!'
                    ], 422);
                }

                $subtotalPerHari = $alat->tarif_per_hari * $itemData['qty'];
                $totalBiaya += ($subtotalPerHari * $lamaSewa);

                $itemsToSave[] = [
                    'alat_id' => $alat->id, // Sesuaikan foreign key relasi detail pemesanan ke alat
                    'jumlah' => $itemData['qty'],
                    'tarif_per_hari' => $alat->tarif_per_hari,
                    'subtotal' => $subtotalPerHari * $lamaSewa,
                ];
            }

            // 3. Simpan Header Pemesanan
            $pemesanan = Pemesanan::create([
                'user_id' => $user->id,
                'kode_pemesanan' => 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(4)),
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'nama_penyewa' => $request->nama_penyewa,
                'no_telepon' => $request->no_telepon,
                'lama_sewa' => $lamaSewa,
                'total_biaya' => $totalBiaya,
                'status' => 'menunggu_pembayaran',
            ]);

            // 4. Simpan Detail Pemesanan & Potong Stok Alat
            foreach ($itemsToSave as $detail) {
                $pemesanan->detailPemesanan()->create($detail);
                Alat::where('id', $detail['alat_id'])->decrement('stok_tersedia', $detail['jumlah']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'kode_pemesanan' => $pemesanan->kode_pemesanan,
                'lama_sewa' => $pemesanan->lama_sewa,
                'total_biaya' => $pemesanan->total_biaya,
                'redirect_url' => route('booking.saya'),
                'message' => 'Pemesanan berhasil dibuat!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }
}