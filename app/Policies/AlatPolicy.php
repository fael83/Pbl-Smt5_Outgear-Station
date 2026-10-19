<?php

namespace App\Policies;

use App\Models\Alat;
use App\Models\User;

class AlatPolicy
{
    /**
     * Semua peran boleh melihat katalog alat. Tamu (belum login) juga boleh
     * melihat karena halaman admin dibuka tanpa login pada mode demo.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Alat $alat): bool
    {
        return true;
    }

    /**
     * Data alat & stok hanya dikelola admin. Tamu diperlakukan seperti admin
     * agar panel demo tetap bisa dipakai tanpa login.
     */
    public function create(?User $user): bool
    {
        return $user === null || $user->isAdmin();
    }

    public function update(?User $user, Alat $alat): bool
    {
        return $user === null || $user->isAdmin();
    }

    public function delete(?User $user, Alat $alat): bool
    {
        return $user === null || $user->isAdmin();
    }
}
