<?php

namespace App\Services;

use App\Models\Pembayaran;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Pembungkus tipis untuk REST API Midtrans (Snap + Status), tanpa paket tambahan.
 */
class MidtransService
{
    public function terkonfigurasi(): bool
    {
        return filled(config('services.midtrans.server_key'))
            && filled(config('services.midtrans.client_key'));
    }

    public function clientKey(): ?string
    {
        return config('services.midtrans.client_key');
    }

    public function urlSnapJs(): string
    {
        return $this->produksi()
            ? 'https://app.midtrans.com/snap/snap.js'
            : 'https://app.sandbox.midtrans.com/snap/snap.js';
    }

    /**
     * Minta token Snap untuk satu pembayaran. Mengembalikan token (string).
     */
    public function buatSnapToken(Pembayaran $pembayaran, User $pelanggan, string $namaItem, ?int $durasiMenit = null): string
    {
        $this->pastikanTerkonfigurasi();

        $payload = [
            'transaction_details' => [
                'order_id' => $pembayaran->transaction_id,
                // Midtrans hanya menerima bilangan bulat untuk IDR.
                'gross_amount' => (int) round((float) $pembayaran->jumlah),
            ],
            'item_details' => [[
                'id' => (string) $pembayaran->id,
                'price' => (int) round((float) $pembayaran->jumlah),
                'quantity' => 1,
                'name' => mb_substr($namaItem, 0, 50),
            ]],
            'customer_details' => array_filter([
                'first_name' => mb_substr($pelanggan->name, 0, 50),
                'email' => $pelanggan->email,
                'phone' => $pelanggan->no_telepon,
            ]),
            'expiry' => [
                'unit' => 'minutes',
                'duration' => max(1, $durasiMenit ?? (int) config('services.midtrans.batas_bayar_menit', 60)),
            ],
        ];

        $respons = Http::withBasicAuth((string) config('services.midtrans.server_key'), '')
            ->acceptJson()
            ->asJson()
            ->timeout(20)
            ->post($this->urlSnap().'/snap/v1/transactions', $payload);

        if (! $respons->successful() || blank($respons->json('token'))) {
            throw new RuntimeException(
                'Midtrans menolak permintaan: '.($respons->json('error_messages.0') ?? $respons->status())
            );
        }

        return (string) $respons->json('token');
    }

    /**
     * Ambil status transaksi terbaru dari Midtrans. Null bila order belum dikenal
     * Midtrans (pelanggan belum membuka pop-up pembayaran).
     *
     * @return array<string, mixed>|null
     */
    public function cekStatus(string $orderId): ?array
    {
        $this->pastikanTerkonfigurasi();

        $respons = Http::withBasicAuth((string) config('services.midtrans.server_key'), '')
            ->acceptJson()
            ->timeout(20)
            ->get($this->urlApi().'/v2/'.rawurlencode($orderId).'/status');

        $data = $respons->json();

        if (! is_array($data) || (string) ($data['status_code'] ?? '') === '404') {
            return null;
        }

        return $data;
    }

    /**
     * Batalkan transaksi di Midtrans (mis. QR sudah dibuat tapi pelanggan ingin ganti metode).
     * True bila berhasil dibatalkan atau Midtrans memang belum mengenal order itu (404).
     */
    public function batalkan(string $orderId): bool
    {
        $this->pastikanTerkonfigurasi();

        $respons = Http::withBasicAuth((string) config('services.midtrans.server_key'), '')
            ->acceptJson()
            ->timeout(20)
            ->post($this->urlApi().'/v2/'.rawurlencode($orderId).'/cancel');

        $ok = in_array((string) $respons->json('status_code'), ['200', '404'], true);

        if (! $ok) {
            \Illuminate\Support\Facades\Log::warning('Midtrans: pembatalan ditolak', [
                'order_id' => $orderId,
                'respons' => $respons->json(),
            ]);
        }

        return $ok;
    }

    /**
     * Cocokkan signature_key notifikasi: SHA512(order_id + status_code + gross_amount + ServerKey).
     *
     * @param  array<string, mixed>  $data
     */
    public function signatureValid(array $data): bool
    {
        $diterima = (string) ($data['signature_key'] ?? '');

        if ($diterima === '' || ! filled(config('services.midtrans.server_key'))) {
            return false;
        }

        $seharusnya = hash('sha512',
            ($data['order_id'] ?? '').($data['status_code'] ?? '').($data['gross_amount'] ?? '')
            .config('services.midtrans.server_key')
        );

        return hash_equals($seharusnya, $diterima);
    }

    private function pastikanTerkonfigurasi(): void
    {
        if (! $this->terkonfigurasi()) {
            throw new RuntimeException('Midtrans belum dikonfigurasi (MIDTRANS_SERVER_KEY / MIDTRANS_CLIENT_KEY di .env).');
        }
    }

    private function produksi(): bool
    {
        return (bool) config('services.midtrans.is_production');
    }

    private function urlSnap(): string
    {
        return $this->produksi() ? 'https://app.midtrans.com' : 'https://app.sandbox.midtrans.com';
    }

    private function urlApi(): string
    {
        return $this->produksi() ? 'https://api.midtrans.com' : 'https://api.sandbox.midtrans.com';
    }
}
