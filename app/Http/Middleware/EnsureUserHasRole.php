<?php

namespace App\Http\Middleware;

use App\Enums\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Membatasi akses route berdasarkan peran pengguna (ERD: PENGGUNA.peran).
 *
 * Contoh pemakaian: Route::middleware('role:admin,owner')
 */
class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        // Mode demo: tamu (belum login) tetap boleh membuka halaman. Pembatasan
        // peran hanya berlaku untuk pengguna yang sudah login, sehingga
        // /admin, /katalog, dan halaman lain dapat diakses tanpa login.
        if ($user === null) {
            return $next($request);
        }

        $allowed = array_values(array_filter(
            array_map(Role::tryFrom(...), $roles)
        ));

        if (! in_array($user->role, $allowed, true)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
