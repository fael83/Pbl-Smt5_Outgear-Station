<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\Pengembalian;
use App\Models\User;

class PengembalianPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Staf internal melihat semua; pelanggan hanya pengembalian miliknya.
     */
    public function view(User $user, Pengembalian $pengembalian): bool
    {
        return $user->isStaff() || $pengembalian->pemesanan->user_id === $user->id;
    }

    /**
     * Pencatatan pengembalian dilakukan petugas (atau admin).
     */
    public function create(User $user): bool
    {
        return $user->hasRole(Role::Admin, Role::Petugas);
    }

    public function update(User $user, Pengembalian $pengembalian): bool
    {
        return $user->hasRole(Role::Admin, Role::Petugas);
    }

    public function delete(User $user, Pengembalian $pengembalian): bool
    {
        return $user->isAdmin();
    }
}
