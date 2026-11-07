<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Services\MidtransService;
use App\Services\PembayaranService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Menerima HTTP Notification dari Midtrans (atur URL-nya di dashboard sandbox:
 * Settings > Configuration > Payment Notification URL).
 */
class MidtransWebhookController extends Controller
{
    public function __invoke(Request $request, MidtransService $midtrans, PembayaranService $layanan): JsonResponse
    {
        $data = $request->all();

        if (! $midtrans->signatureValid($data)) {
            return response()->json(['message' => 'Signature tidak valid'], 403);
        }

        $orderId = (string) ($data['order_id'] ?? '');
        $pembayaran = Pembayaran::query()->where('transaction_id', $orderId)->first();

        // Pelanggan sempat "ganti metode" (order baru dibuat), tapi order lamanya malah dibayar:
        // kenali lewat awalan kode booking, hanya untuk pembayaran yang BERHASIL.
        if ($pembayaran === null
            && in_array($data['transaction_status'] ?? '', ['settlement', 'capture'], true)
            && preg_match('/^(ORD-\d{8}-[A-Z0-9]+)-DP/', $orderId, $m)) {
            $pembayaran = Pembayaran::query()
                ->where('transaction_id', 'like', $m[1].'-DP%')
                ->where('status_transaksi', 'pending')
                ->first();
        }

        // Order tak dikenal (mis. tombol "Test notification" Midtrans): balas 200 agar tidak diulang.
        if ($pembayaran) {
            $layanan->terapkanDariMidtrans($pembayaran, $data);
        }

        return response()->json(['message' => 'OK']);
    }
}
