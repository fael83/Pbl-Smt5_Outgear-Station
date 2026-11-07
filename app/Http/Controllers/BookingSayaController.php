<?php

namespace App\Http\Controllers;

use App\Models\Pemesanan;
use App\Services\MidtransService;
use App\Services\PembayaranService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingSayaController extends Controller
{
    public function __invoke(Request $request, PembayaranService $layanan, MidtransService $midtrans): View
    {
        $user = $request->user();

        // Lepas booking milik pelanggan ini yang sudah lewat batas bayar.
        $layanan->batalkanKedaluwarsa($user->id);

        $daftar = Pemesanan::query()
            ->where('user_id', $user->id)
            ->with([
                'detailPemesanan:id,pemesanan_id,alat_id,jumlah',
                'detailPemesanan.alat:id,nama',
                'pembayaran' => fn ($q) => $q->where('jenis_pembayaran', 'dp')->select('id', 'pemesanan_id', 'status_transaksi', 'jumlah', 'metode_pembayaran'),
            ])
            ->latest()
            ->get();

        return view('customer.booking', [
            'daftarBooking' => $daftar,
            'snapJsUrl' => $midtrans->urlSnapJs(),
            'clientKey' => $midtrans->clientKey(),
            'batasBayarMenit' => (int) config('services.midtrans.batas_bayar_menit', 60),
        ]);
    }
}
