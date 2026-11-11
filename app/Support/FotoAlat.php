<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Penyimpanan foto alat di Supabase Storage lewat REST API (tanpa paket
 * tambahan).
 *
 * Nilai kolom `alat.foto` bisa berbentuk:
 * - `alat/<uuid>.jpg`   -> objek di bucket Supabase (bentuk baru);
 * - `https://...`       -> URL penuh, dipakai apa adanya;
 * - `tenda-dome-4.avif` -> berkas lama di public/images/alat (data lama).
 */
final class FotoAlat
{
    /** Folder di dalam bucket. */
    private const FOLDER = 'alat';

    public static function terkonfigurasi(): bool
    {
        return filled(config('services.supabase.url'))
            && filled(config('services.supabase.service_key'));
    }

    /**
     * URL yang bisa dipakai di <img>; null bila alat belum punya foto.
     */
    public static function url(?string $foto): ?string
    {
        if (blank($foto)) {
            return null;
        }

        if (filter_var($foto, FILTER_VALIDATE_URL)) {
            return $foto;
        }

        // Berkas lama di folder public (tanpa garis miring).
        if (! str_contains($foto, '/')) {
            return file_exists(public_path('images/alat/'.$foto))
                ? asset('images/alat/'.$foto)
                : null;
        }

        if (! self::terkonfigurasi()) {
            return null;
        }

        return rtrim((string) config('services.supabase.url'), '/')
            .'/storage/v1/object/public/'.config('services.supabase.bucket').'/'.ltrim($foto, '/');
    }

    /**
     * Unggah berkas dan kembalikan path objek (disimpan di kolom `foto`).
     *
     * @throws RuntimeException bila Supabase belum dikonfigurasi atau menolak berkas.
     */
    public static function simpan(UploadedFile $berkas): string
    {
        $ekstensi = strtolower($berkas->guessExtension() ?: $berkas->getClientOriginalExtension() ?: 'jpg');
        $path = self::FOLDER.'/'.Str::uuid().'.'.$ekstensi;

        self::unggah($path, (string) file_get_contents($berkas->getRealPath()), $berkas->getMimeType() ?: 'application/octet-stream');

        return $path;
    }

    /**
     * Unggah isi berkas ke path tertentu (dipakai juga oleh perintah migrasi).
     */
    public static function unggah(string $path, string $isi, string $mime): void
    {
        self::pastikanTerkonfigurasi();

        $respons = self::klien()
            ->withHeaders(['x-upsert' => 'true'])
            ->withBody($isi, $mime)
            ->post(self::endpoint($path));

        if ($respons->failed()) {
            throw new RuntimeException('Supabase menolak unggahan ('.$respons->status().'): '.Str::limit($respons->body(), 200));
        }
    }

    /**
     * Hapus objek lama. Berkas lama di public/images dan URL luar tidak disentuh.
     * Kegagalan hapus tidak menggagalkan proses utama (hanya meninggalkan berkas yatim).
     */
    public static function hapus(?string $foto): void
    {
        if (blank($foto) || ! str_contains($foto, '/') || filter_var($foto, FILTER_VALIDATE_URL) || ! self::terkonfigurasi()) {
            return;
        }

        try {
            self::klien()->delete(self::endpoint($foto));
        } catch (\Throwable) {
            // Abaikan: berkas yatim tidak merusak data.
        }
    }

    private static function pastikanTerkonfigurasi(): void
    {
        if (! self::terkonfigurasi()) {
            throw new RuntimeException('Supabase Storage belum dikonfigurasi (SUPABASE_URL / SUPABASE_SERVICE_KEY di .env).');
        }
    }

    private static function endpoint(string $path): string
    {
        return rtrim((string) config('services.supabase.url'), '/')
            .'/storage/v1/object/'.config('services.supabase.bucket').'/'.ltrim($path, '/');
    }

    private static function klien(): \Illuminate\Http\Client\PendingRequest
    {
        $kunci = (string) config('services.supabase.service_key');

        $klien = Http::timeout(30)->withHeaders(['apikey' => $kunci]);

        // Kunci lama berbentuk JWT dan harus ikut dikirim sebagai Bearer;
        // kunci format baru (sb_secret_...) cukup lewat header apikey.
        return str_starts_with($kunci, 'eyJ') ? $klien->withToken($kunci) : $klien;
    }
}
