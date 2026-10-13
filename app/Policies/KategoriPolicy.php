<?php

namespace App\Policies;

use App\Models\Kategori;
use App\Models\User;

class KategoriPolicy
{
    /**
     * Semua peran boleh melihat daftar kategori.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Kategori $kategori): bool
    {
        return true;
    }

    /**
     * Master data kategori hanya dikelola admin.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Kategori $kategori): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Kategori $kategori): bool
    {
        return $user->isAdmin();
    }
}
