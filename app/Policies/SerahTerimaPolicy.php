<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\SerahTerima;
use App\Models\User;

class SerahTerimaPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Staf internal melihat semua; pelanggan hanya serah terima miliknya.
     */
    public function view(User $user, SerahTerima $serahTerima): bool
    {
        return $user->isStaff() || $serahTerima->pemesanan->user_id === $user->id;
    }

    /**
     * Serah terima alat beserta verifikasi KTP dilakukan petugas (atau admin).
     */
    public function create(User $user): bool
    {
        return $user->hasRole(Role::Admin, Role::Petugas);
    }

    public function update(User $user, SerahTerima $serahTerima): bool
    {
        return $user->hasRole(Role::Admin, Role::Petugas);
    }

    public function delete(User $user, SerahTerima $serahTerima): bool
    {
        return $user->isAdmin();
    }
}
