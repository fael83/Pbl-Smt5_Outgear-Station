<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\DetailSerahTerima;
use App\Models\User;

class DetailSerahTerimaPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, DetailSerahTerima $detailSerahTerima): bool
    {
        return $user->isStaff()
            || $detailSerahTerima->serahTerima->pemesanan->user_id === $user->id;
    }

    /**
     * Pemeriksaan kondisi awal alat dilakukan petugas (atau admin).
     */
    public function create(User $user): bool
    {
        return $user->hasRole(Role::Admin, Role::Petugas);
    }

    public function update(User $user, DetailSerahTerima $detailSerahTerima): bool
    {
        return $user->hasRole(Role::Admin, Role::Petugas);
    }

    public function delete(User $user, DetailSerahTerima $detailSerahTerima): bool
    {
        return $user->isAdmin();
    }
}
