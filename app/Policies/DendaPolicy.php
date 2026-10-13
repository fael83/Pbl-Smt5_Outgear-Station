<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\Denda;
use App\Models\User;

class DendaPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Staf internal melihat semua; pelanggan hanya denda miliknya.
     */
    public function view(User $user, Denda $denda): bool
    {
        return $user->isStaff() || $denda->pemesanan->user_id === $user->id;
    }

    /**
     * Denda dihitung & dicatat petugas (atau admin).
     */
    public function create(User $user): bool
    {
        return $user->hasRole(Role::Admin, Role::Petugas);
    }

    /**
     * Menandai denda lunas / koreksi nilai denda.
     */
    public function update(User $user, Denda $denda): bool
    {
        return $user->hasRole(Role::Admin, Role::Petugas);
    }

    public function delete(User $user, Denda $denda): bool
    {
        return $user->isAdmin();
    }
}
