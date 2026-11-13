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

            $pesanBantuan = "Halo! Selamat datang di OutGear Station. 🏕️\n\nKetik salah satu kata kunci berikut:\n- katalog (Melihat katalog alat di web)\n- syarat (Syarat & ketentuan sewa)\n- lokasi (Alamat basecamp)";

            $reply = null;

            // Mengambil URL dari file .env (jika belum diisi, pakai fallback URL lokal)
            $baseUrl = env('OUTGEAR_WEB_URL', url('/'));
            $katalogUrl = rtrim($baseUrl, '/') . '/katalog';

            if ($message === 'katalog') {
                $reply = "Silakan kunjungi katalog lengkap dan lakukan pemesanan alat outdoor melalui website resmi kami:\n{$katalogUrl}\n\nNikmati kemudahan sewa alat pendakian dengan cepat dan aman! ⛺✨";
            } elseif (in_array($message, ['syarat', 'ketentuan'])) {
                $reply = "Syarat & Ketentuan Umum OutGear Station:\n\n" .
                         "1. Penyewa wajib meninggalkan Identitas Asli (E-KTP) yang masih berlaku sebagai jaminan selama masa sewa.\n\n" .
                         "2. Alat yang disewa menjadi tanggung jawab penuh penyewa. Kerusakan (sobek, patah, terbakar) atau kehilangan alat akan dikenakan denda sesuai dengan harga perbaikan atau harga beli alat baru.\n\n" .
                         "3. Hitungan sewa adalah per hari (24 Jam). Keterlambatan pengembalian akan dikenakan denda sebesar harga sewa per hari untuk setiap alat yang terlambat.\n\n" .
                         "4. Uang muka (DP) yang sudah dibayarkan tidak dapat dikembalikan (hangus) apabila penyewa melakukan pembatalan sepihak pada hari-H pengambilan.\n\n" .
                         "5. Tidak perlu mencuci tenda atau alat masak setelah digunakan, cukup pastikan alat dalam keadaan kering dan bebas dari sampah.";
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
                try {
                    $response = Http::withHeaders([
                        'Authorization' => $token,
                    ])->timeout(30)->post('https://api.fonnte.com/send', [
                        'target'  => $sender,
                        'message' => $reply,
                    ]);

                    Log::info('Bot: respons Fonnte', [
                        'status' => $response->status(),
                        'body'   => $response->body(),
                    ]);
                } catch (\Exception $e) {
                    Log::error('Gagal mengirim ke Fonnte (Timeout/Network): ' . $e->getMessage());
                }
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