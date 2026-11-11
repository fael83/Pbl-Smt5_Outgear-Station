<?php

namespace App\Policies;

use App\Models\AturanDenda;
use App\Models\User;

class AturanDendaPolicy
{
    /**
     * Semua peran boleh melihat aturan denda yang berlaku.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, AturanDenda $aturanDenda): bool
    {
        return true;
    }

    /**
     * Aturan denda dikelola admin & owner (ERD: PENGGUNA "mengelola" ATURAN_DENDA).
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isOwner();
    }

    public function update(User $user, AturanDenda $aturanDenda): bool
    {
        return $user->isAdmin() || $user->isOwner();
    }

    /**
     * Penghapusan aturan denda hanya admin.
     */
    public function delete(User $user, AturanDenda $aturanDenda): bool
    {
        return $user->isAdmin();
    }
}
