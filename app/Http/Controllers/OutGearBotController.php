<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OutGearBotController extends Controller
{
    // true  = pesan di luar kata kunci tetap dibalas pesan bantuan
    // false = pesan di luar kata kunci DIABAIKAN (bot diam)
    private const BALAS_JIKA_BUKAN_KUNCI = false;

    public function handleWebhook(Request $request)
    {
        try {
            Log::info('Webhook Fonnte diterima:', $request->all());

            $sender  = $request->input('sender') ?? $request->input('from');
            $device  = $request->input('device');
            $message = strtolower(trim($request->input('message') ?? ''));

            // Abaikan kalau tidak ada pengirim/pesan, atau pesan dari nomor bot sendiri
            if (!$sender || $message === '' || $sender === $device) {
                return response()->json(['status' => 'success']);
            }

            $pesanBantuan = "Halo! Selamat datang di OutGear Station. 🏕️\n\nKetik salah satu kata kunci berikut:\n- menu (Melihat katalog alat)\n- syarat (Syarat & ketentuan sewa)\n- lokasi (Alamat basecamp)";

            $reply = null;

            if (in_array($message, ['menu', 'katalog'])) {
                $reply = "Katalog OutGear Station:\n1. Tenda Badai (Ultralight)\n2. Carrier Ergonomis (60L+)\n3. Alat Masak Gunung\n\nSilakan kunjungi web kami untuk pemesanan langsung!";
            } elseif (in_array($message, ['syarat', 'ketentuan'])) {
                $reply = "Syarat Sewa Alat OutGear Station:\n1. Wajib meninggalkan identitas asli (KTP/SIM/KTM).\n2. Melakukan pembayaran melalui sistem.\n3. Menjaga kondisi alat selama masa penyewaan.";
            } elseif (in_array($message, ['lokasi', 'alamat'])) {
                $reply = "Basecamp OutGear Station:\nMalang, East Java, Indonesia.";
            } elseif (in_array($message, ['halo', 'hai', 'hi', 'bantuan', 'help'])) {
                $reply = $pesanBantuan;
            } else {
                // Bukan kata kunci
                if (self::BALAS_JIKA_BUKAN_KUNCI) {
                    $reply = $pesanBantuan;
                } else {
                    Log::info('Bot: pesan bukan kata kunci, diabaikan', ['message' => $message]);
                }
            }

            // Tidak ada balasan -> selesai
            if ($reply === null) {
                return response()->json(['status' => 'success']);
            }

            $token = config('services.fonnte.token') ?: env('FONNTE_TOKEN');

            if ($token) {
                $response = Http::withHeaders([
                    'Authorization' => $token,
                ])->post('https://api.fonnte.com/send', [
                    'target'  => $sender,
                    'message' => $reply,
                ]);

                Log::info('Bot: respons Fonnte', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
            } else {
                Log::warning('Bot: balasan TIDAK dikirim (token kosong)');
            }

            return response()->json(['status' => 'success']);

        } catch (\Throwable $e) {
            Log::error('Error pada OutGearBotController: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}