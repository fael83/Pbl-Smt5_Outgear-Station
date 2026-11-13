<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WhatsAppController extends Controller
{
    /**
     * Verifikasi Webhook dari Meta (GET Request)
     */
    public function verifyWebhook(Request $request)
    {
        $mode = $request->query('hub_mode') ?? $request->query('hub.mode');
        $token = $request->query('hub_verify_token') ?? $request->query('hub.verify_token');
        $challenge = $request->query('hub_challenge') ?? $request->query('hub.challenge');

        $verifyToken = 'outgear_station_secret_token';

        if ($mode === 'subscribe' && $token === $verifyToken) {
            return response($challenge, 200)->header('Content-Type', 'text/plain');
        }

        return response()->json(['error' => 'Unauthorized'], 403);
    }

    /**
     * Menerima & Memproses Pesan Masuk dari Meta (POST Request)
     */
    public function handleWebhook(Request $request)
    {
        // Catat semua data mentah yang dikirim oleh Meta ke file laravel.log
        \Illuminate\Support\Facades\Log::info('WEBHOOK_INCOMING:', $request->all());

        $body = $request->all();

        if (isset($body['object']) && $body['object'] === 'whatsapp_business_account') {
            foreach ($body['entry'] as $entry) {
                foreach ($entry['changes'] as $change) {
                    $value = $change['value'];
                    
                    if (isset($value['messages'][0])) {
                        $messageData = $value['messages'][0];
                        $from = $messageData['from'];
                        
                        if ($messageData['type'] === 'text') {
                            $rawMessage = trim($messageData['text']['body']);
                            $this->processIncomingMessage($from, $rawMessage);
                        }
                    }
                }
            }
            return response()->json(['status' => 'EVENT_RECEIVED'], 200);
        }

        return response()->json(['status' => 'not_found'], 404);
    }

    /**
     * Logika Pemrosesan Pesan Chatbot
     */
    private function processIncomingMessage($from, $rawMessage)
    {
        $upperMessage = strtoupper($rawMessage);

        if (in_array($upperMessage, ['MENU', 'HALO', 'HI', 'P'])) {
            $this->sendMenuResponse($from);
        } elseif ($upperMessage === '1' || $upperMessage === 'KATALOG') {
            $this->sendKatalogResponse($from);
        } elseif ($upperMessage === '2' || $upperMessage === 'HARGA') {
            $this->sendHargaResponse($from);
        } elseif ($upperMessage === '3' || str_starts_with($upperMessage, 'STOK')) {
            $this->handleStokCheck($from, $rawMessage);
        } elseif ($upperMessage === '4' || $upperMessage === 'ADMIN') {
            $this->sendWhatsAppMessage($from, "Pesan Anda telah diteruskan ke Admin OutGear Station. Tim kami akan segera membalas secara manual.");
        } else {
            $this->sendWhatsAppMessage($from, "Maaf, perintah tidak dikenali.\n\nKetik *MENU* untuk melihat opsi layanan OutGear Station.");
        }
    }

    private function sendMenuResponse($target)
    {
        $text = "Selamat datang di *OutGear Station* ⛺\n";
        $text .= "Sistem Informasi & Layanan Penyewaan Alat Outdoor\n\n";
        $text .= "Silakan balas dengan angka atau kata kunci:\n";
        $text .= "1. *KATALOG* - Lihat daftar alat outdoor\n";
        $text .= "2. *HARGA* - Cek tarif sewa harian\n";
        $text .= "3. *STOK* - Cek ketersediaan alat pada tanggal tertentu\n";
        $text .= "4. *ADMIN* - Bantuan langsung tim admin\n\n";
        $text .= "_Contoh cek stok tanggal: STOK 2026-10-15_";

        $this->sendWhatsAppMessage($target, $text);
    }

    private function sendKatalogResponse($target)
    {
        $products = DB::table('products')->select('name', 'category', 'image_path')->get();

        if ($products->isEmpty()) {
            $this->sendWhatsAppMessage($target, "Belum ada data produk di katalog OutGear Station.");
            return;
        }

        $text = "*KATALOG ALAT OUTDOOR OUTGEAR STATION*\n\n";
        $baseUrl = env('SUPABASE_STORAGE_URL');

        foreach ($products as $p) {
            $imageUrl = $p->image_path ? "{$baseUrl}/{$p->image_path}" : 'Tidak ada foto';
            $text .= "• *{$p->name}* ({$p->category})\n  Foto: {$imageUrl}\n\n";
        }

        $text .= "Ketik *HARGA* untuk tarif sewa atau *STOK [YYYY-MM-DD]* untuk cek tanggal.";
        $this->sendWhatsAppMessage($target, $text);
    }

    private function sendHargaResponse($target)
    {
        $products = DB::table('products')->select('name', 'rental_price_per_day')->get();

        if ($products->isEmpty()) {
            $this->sendWhatsAppMessage($target, "Belum ada data tarif sewa produk.");
            return;
        }

        $text = "*DAFTAR TARIF SEWA OUTGEAR STATION (PER HARI)*\n\n";
        foreach ($products as $p) {
            $formattedPrice = number_format($p->rental_price_per_day, 0, ',', '.');
            $text .= "• *{$p->name}*: Rp {$formattedPrice} /hari\n";
        }

        $text .= "\nFormat cek stok: *STOK [YYYY-MM-DD]*\nContoh: *STOK 2026-10-15*";
        $this->sendWhatsAppMessage($target, $text);
    }

    private function handleStokCheck($target, $rawMessage)
    {
        $parts = explode(' ', trim($rawMessage));

        if (count($parts) === 1) {
            $text = "Untuk cek ketersediaan barang pada tanggal sewa tertentu, gunakan format:\n\n*STOK [YYYY-MM-DD]*\nContoh: *STOK 2026-10-15*";
            $this->sendWhatsAppMessage($target, $text);
            return;
        }

        $dateInput = $parts[1];

        try {
            $checkDate = Carbon::createFromFormat('Y-m-d', $dateInput)->format('Y-m-d');
        } catch (\Exception $e) {
            $this->sendWhatsAppMessage($target, "Format tanggal salah. Gunakan format YYYY-MM-DD.\nContoh: *STOK 2026-10-15*");
            return;
        }

        $products = DB::table('products')->get();
        $text = "*INFORMASI KETERSEDIAAN ALAT (Tanggal: {$checkDate})*\n\n";

        foreach ($products as $p) {
            $bookedQty = DB::table('rentals')
                ->where('product_id', $p->id)
                ->whereIn('status', ['booked', 'active'])
                ->whereDate('start_date', '<=', $checkDate)
                ->whereDate('end_date', '>=', $checkDate)
                ->sum('qty');

            $availableStock = max(0, $p->total_stock - $bookedQty);
            $statusStr = $availableStock > 0 ? "Tersedia ({$availableStock} unit)" : "Habis Tersewa ❌";

            $text .= "• *{$p->name}*: {$statusStr}\n";
        }

        $text .= "\nKetik *MENU* untuk kembali ke menu utama.";
        $this->sendWhatsAppMessage($target, $text);
    }

    private function sendWhatsAppMessage($target, $message)
    {
        $phoneNumberId = env('WA_CLOUD_API_PHONE_NUMBER_ID');
        $accessToken = env('WA_CLOUD_API_TOKEN');

        Http::withToken($accessToken)
            ->post("https://graph.facebook.com/v18.0/{$phoneNumberId}/messages", [
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => $target,
                'type' => 'text',
                'text' => [
                    'preview_url' => false,
                    'body' => $message
                ]
            ]);
    }
}