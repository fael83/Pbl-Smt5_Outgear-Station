<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\DetailPengembalian;
use App\Models\User;

class DetailPengembalianPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, DetailPengembalian $detailPengembalian): bool
    {
        return $user->isStaff()
            || $detailPengembalian->pengembalian->pemesanan->user_id === $user->id;
    }

    /**
     * Pemeriksaan kondisi alat saat kembali dilakukan petugas (atau admin).
     */
    public function create(User $user): bool
    {
        return $user->hasRole(Role::Admin, Role::Petugas);
    }

    public function update(User $user, DetailPengembalian $detailPengembalian): bool
    {
        return $user->hasRole(Role::Admin, Role::Petugas);
    }

    public function delete(User $user, DetailPengembalian $detailPengembalian): bool
    {
        return $user->isAdmin();
    }
}
