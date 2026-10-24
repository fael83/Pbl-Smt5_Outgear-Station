<?php

namespace App\Auth;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Support\Facades\Cache;

/**
 * Provider Eloquent yang menyimpan data pengguna login selama beberapa menit.
 *
 * Tanpa ini, SETIAP request (pindah halaman, refresh) menjalankan
 * `select * from users where id = ?` ke Supabase (~0,5-1,2 detik) hanya untuk
 * mengenali siapa yang login. Cache dihapus otomatis saat user diubah
 * (lihat App\Models\User::booted()).
 */
class CachedEloquentUserProvider extends EloquentUserProvider
{
    public const TTL_DETIK = 300;

    public static function kunci(int|string $id): string
    {
        return 'auth.user.'.$id;
    }

    public function retrieveById($identifier)
    {
        $atribut = Cache::remember(
            self::kunci($identifier),
            self::TTL_DETIK,
            function () use ($identifier): ?array {
                $user = parent::retrieveById($identifier);

                return $user?->getAttributes();
            },
        );

        if ($atribut === null) {
            Cache::forget(self::kunci($identifier));

            return null;
        }

        $model = $this->createModel();

        return $model->newFromBuilder($atribut);
    }
}
