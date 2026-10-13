<?php

namespace App\Policies;

use App\Models\Alat;
use App\Models\User;

class AlatPolicy
{
    /**
     * Semua peran boleh melihat katalog alat.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Alat $alat): bool
    {
        return true;
    }

    /**
     * Data alat & stok hanya dikelola admin.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Alat $alat): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Alat $alat): bool
    {
        return $user->isAdmin();
    }
}
