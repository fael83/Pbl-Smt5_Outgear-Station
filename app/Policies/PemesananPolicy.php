<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\Pemesanan;
use App\Models\User;

class PemesananPolicy
{
    /**
     * Semua peran boleh membuka daftar pemesanan (isi daftarnya disaring per peran).
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Staf internal melihat semua; pelanggan hanya pemesanan miliknya.
     */
    public function view(User $user, Pemesanan $pemesanan): bool
    {
        return $user->isStaff() || $this->milikPelanggan($user, $pemesanan);
    }

    /**
     * Pemesanan dibuat oleh pelanggan.
     */
    public function create(User $user): bool
    {
        return $user->isCustomer();
    }

    /**
     * Pelanggan hanya bisa mengubah pemesanan yang belum dibayar.
     */
    public function update(User $user, Pemesanan $pemesanan): bool
    {
        return $user->isAdmin()
            || ($this->milikPelanggan($user, $pemesanan) && $this->masihBisaDiubah($pemesanan));
    }

    public function delete(User $user, Pemesanan $pemesanan): bool
    {
        return $user->isAdmin();
    }

    /**
     * Pembatalan pemesanan oleh pelanggan (ERD: pemesanan.waktu_pembatalan).
     */
    public function batalkan(User $user, Pemesanan $pemesanan): bool
    {
        return $user->isAdmin()
            || ($this->milikPelanggan($user, $pemesanan) && $this->masihBisaDiubah($pemesanan));
    }

    /**
     * Konfirmasi pemesanan setelah DP diterima (admin & petugas).
     */
    public function konfirmasi(User $user, Pemesanan $pemesanan): bool
    {
        return $user->hasRole(Role::Admin, Role::Petugas)
            && $pemesanan->status === 'menunggu_pembayaran';
    }

    protected function milikPelanggan(User $user, Pemesanan $pemesanan): bool
    {
        return $pemesanan->user_id === $user->id;
    }

    protected function masihBisaDiubah(Pemesanan $pemesanan): bool
    {
        return $pemesanan->status === 'menunggu_pembayaran';
    }
}
